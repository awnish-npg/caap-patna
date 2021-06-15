<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Issue_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {

        $this->db->insert(db_prefix() . 'issue_categories', $data);
        return  $this->db->insert_id();
    }

    public function getIssueCategory($id, $field = '*')
    {

        $this->db->where('id', $id);
        $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
        if($field == '*'){
            return $rows;
        }else{
            return $rows[0][$field];
        }
    }

    public function addmilestone($data)
    {
        $this->db->insert(db_prefix() . 'issue_milestones', $data);
        return  $this->db->insert_id();
    }



    public function deletemilestone($id, $is_sadmin, $areaid)
    {
        $data['is_active'] = '0';
        $this->db->where('id', $id);
        if ($is_sadmin) {
            $this->db->where('area_id', 0);
            $this->db->where('parent_issue_id', 0);
        } else {
            $this->db->where('area_id', $areaid);
        }
        $this->db->update(db_prefix() . 'issue_milestones', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
            log_activity('milestone deactivated [ID: ' . $id . ']');
        } else {
            return false;
        }
    }
    public function addtoareaadmintable($data)
    {
        $this->db->insert(db_prefix() . 'issue_region', $data);
        return true;
    }
    public function deleteall($id, $is_sadmin, $areaid)
    {

        if ($is_sadmin) {
            $this->db->where('issue_id', $id);
            $this->db->where('area_id', 0);
            $this->db->where('parent_issue_id', 0);
        } else {
            $this->db->where('issue_id', $id);
            $this->db->where('area_id', $areaid);
        }
        $this->db->delete(db_prefix() . 'issue_milestones');
    }
    public function delete($id)
    {
        // $current = $this->get($id);

        // if (is_reference_in_table('department', db_prefix() . 'tickets', $id)) {
        //     return [
        //         'referenced' => true,
        //     ];
        // }
        $data['is_active'] = '0';
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'issue_categories', $data);

        if ($this->db->affected_rows() > 0) {
            $this->db->where('issue_id', $id);
            $this->db->update(db_prefix() . 'issue_milestones', $data);

            log_activity('issue deactivated [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
    public function checkforname($data, $is_sa, $areaid, $issuename_reg = '')
    {
        if ($is_sa) {
            $this->db->where('parent_issue_id', 0);
            $this->db->where('area_id', 0);
            $this->db->where('issue_name', $data);
            $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
            if (empty($rows)) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('area_id', $areaid);
            if(empty($issuename_reg)){
                $this->db->where("issue_name", $data);
            }else{
                $this->db->where("(issue_name = '".$data."' OR issue_name = '".$issuename_reg."')");
            }
            
            $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
            if (empty($rows)) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function change_issue_status($id, $status, $is_sadmin, $areaid)
    {
        
        if ($is_sadmin) {
            $this->db->where('area_id', 0);
            $this->db->where('parent_issue_id', 0);
            $this->db->where('id', $id);
        } else {
            if($status == 1){
                $issue_name = $this->getIssueCategory($id, 'issue_name');
                $area_prefix = $GLOBALS["current_user"]->area_prefix;
                $issue_name = str_replace($area_prefix. " - ","",$issue_name);
                $response = $this->check_if_issue_exists($id, $issue_name, $areaid);
                if(!empty($response))
                return $response;
            }
            
            $this->db->where('area_id', $areaid);
            $this->db->where('id', $id);
        }
        $this->db->update(db_prefix() . 'issue_categories', [
            'is_active' => $status,
        ]);

        log_activity('issue Status Changed [issue: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
    }

    public function check_if_issue_exists($issue_id, $issue_name, $area_id){
        
        $this->db->where('area_id', $area_id);
        $this->db->where('is_active', 1);
        $this->db->where("issue_name", $issue_name);
        $this->db->where("id != ".$issue_id);
        $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
        //print_r($rows);
        if (!empty($rows)) {
            if($rows[0]['parent_issue_id'] != 0){
                return 'CREATED_BY_SA';
            }else{
                return 'CREATED_BY_SELF';
            }
            return '';
        } else {
            return '';
        }
    }


    public function updateissue($data, $id, $is_sadmin, $areaid)
    {
        
        if ($is_sadmin) {
            // Added by Awnish - to updated Action Name for all Stated
            $this->update_issue_name_for_all_states($data, $id);

            $this->db->where('id', $id);
            $this->db->where('area_id', 0);
            $this->db->where('parent_issue_id', 0);
            
        } else {
            $this->db->where('id', $id);
            $this->db->where('area_id', $areaid);
        }
        $this->db->update(db_prefix() . 'issue_categories', $data);
        log_activity('issues Updated [Name: ' . $data['issue_name'] . ', ID: ' . $id . ']');
        return true;
    }

    function update_issue_name_for_all_states($data, $id)
    {
        $this->db->where('parent_issue_id', $id);
        $this->db->update(db_prefix() . 'issue_categories', $data);
        log_activity('issues Updated for all States [Name: ' . $data['issue_name'] . ', ID: ' . $id . ']');
        return true;
    }

    public function checkimportstatus($issueid, $staffid)
    {
        $this->db->where('issue_id', $issueid);
        $this->db->where('staff_id', $staffid);
        $rows = $this->db->get(db_prefix() . 'issue_region')->row();
        $result = json_decode(json_encode($rows), true);
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }
    public function updatemilestones($data, $id)
    {
        $staffroleid = $this->session->userdata('staff_role');
        $this->db->where('id', $id);
        if ($staffroleid == 2) {
            $this->db->where('area_id', 0);
            $this->db->where('parent_issue_id', 0);
        }
        $this->db->update(db_prefix() . 'issue_milestones', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('milestones Updated  ID: ' . $id . ']');

            return true;
        }
        return false;
    }

    public function importissue($data)
    {

        $this->db->insert(db_prefix() . 'issue_region', $data);
        return true;
    }
    public function get_area_issues_for_client($area_id)
    {
        $this->db->select('i.id, i.issue_name');
        $this->db->from('issue_categories as i');
        // $this->db->join('issue_region as ir', 'ir.issue_id = i.id AND ir.area_id =' . $area_id);
        $this->db->where('area_id', $area_id);
        $this->db->where('is_active', '1');
        $this->db->order_by('issue_name', 'asc');
        $result = $this->db->get()->result_array();
        if (count($result) > 0) return $result;
        return false;
    }
    public function get_area_issues($area_id, $active = 0)
    {
        $this->db->select('i.id, i.issue_name');
        $this->db->from('issue_categories as i');
        //$this->db->join('issue_region as ir', 'ir.issue_id = i.id1 AND ir.area_id =' . $area_id);
        if(empty($area_id)){
            $this->db->where("i.parent_issue_id = 0");
        }else{
            $this->db->where('area_id', $area_id);
        }
        if(isset($active) && $active == 1){
           $this->db->where('is_active', '1');
        }
        //
        $this->db->order_by("i.issue_name", "asc");
        $result = $this->db->get()->result_array();
        if (count($result) > 0) return $result;
        return false;
    }

    public function get_duration_issues($duration, $is_range = false)
    {
        $get_area = $this->input->post('area') ? base64_decode($this->input->post('area')) : "";
        $role_slug =  $GLOBALS['current_user']->role_slug_url;
        $filter_by_area_role = ['aa', 'ae-area', 'at', 'ata', 'ar'];
        //pre($GLOBALS['current_user']);
        $this->db->select('i.id, i.issue_name');
        $this->db->from('issue_categories as i');
        $this->db->join("issue_milestones as im", "im.issue_id = i.id", "LEFT");
        if ($is_range == true) {
            $this->db->where("im.days >= ", $duration[0]);
            $this->db->where("im.days <= ", $duration[1]);
        } else if ($duration == 30) {
            $this->db->where("im.days <=" . $duration);
        } else if ($duration == 1826) {
            $this->db->where("im.days >=" . $duration);
        }
        if(!empty($get_area)){
            $this->db->where("i.area_id =" . $get_area);
        }else if (in_array($role_slug, $filter_by_area_role) ) {
            if (!empty($GLOBALS['current_user']->area)) {
                $this->db->where("i.area_id =" . $GLOBALS['current_user']->area);
            }
        }else{
            $this->db->where("i.parent_issue_id = 0");
        }
        $this->db->where("im.is_closure", 1);
        //$this->db->group_by("i.issue_name");
        $result = $this->db->get()->result_array();
        if (!empty($result))
            return $result;
        return false;
    }

    public function get_existing_area_issues($sub_region_id, $region_id)
    {
        $this->db->select('si.issue_id');
        $this->db->from('staff_region as sr');
        $this->db->join('staff_issues as si', "si.staff_id = sr.staff_id", 'LEFT');
        $this->db->join('staff as s', "s.staffid = si.staff_id", "LEFT");
        $this->db->where(['sr.region' => $region_id, 'sr.sub_region' => $sub_region_id]);
        // $this->db->where('si.issue_id', $issue_id);
        $this->db->where('si.issue_id IS NOT NULL');
        $this->db->where("s.active = 1");
        $this->db->group_by('si.issue_id');
        $result = $this->db->get()->result_array();
        if (count($result) > 0) return $result;
        return false;
    }
    public function get_all_super_admin_categories()
    {
        $this->db->where('parent_issue_id', 0);
        $this->db->where('area_id', 0);
        $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
        return $rows;
    }
    public function get_all_super_admin_categories_milestone($id)
    {
        $this->db->where('parent_issue_id', 0);
        $this->db->where('area_id', 0);
        $this->db->where('issue_id', $id);
        $rows = $this->db->get(db_prefix() . 'issue_milestones')->result_array();
        return $rows;
    }
    public function check_whose_cat_is_it($categoryname, $is_sa)
    {
        if ($is_sa) {
            $this->db->where('parent_issue_id', 0);
            $this->db->where('area_id', 0);
            $this->db->where('issue_name', $categoryname);
            $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
            if (!empty($rows)) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->select('area_id');
            $this->db->where('area_id !=', 0);
            $this->db->where('is_active', 1);
            $this->db->where("issue_name like '% - ".trim($categoryname)."%'");
            $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
            if (!empty($rows)) {
                $resultarray = [
                    'success' => true,
                    'data' => $rows,
                ];
                return $resultarray;
            } else {
                $resultarray = [
                    'success' => false,
                    'data' => [],
                ];
                return $resultarray;
            }
        }
    }

    public function get_staff_issues($staff_id)
    {
        $this->db->select("issue_id");
        $this->db->from("staff_issues");
        $this->db->where("staff_id", $staff_id);
        $result = $this->db->get()->result_array();

        if (!empty($result)) return $result;
        return false;
    }



    public function is_createdby_superadmin($id)
    {
        $this->db->where('id', $id);
        $rows = $this->db->get(db_prefix() . 'issue_categories')->result_array();
        if (!empty($rows)) {
            if($rows[0]['parent_issue_id']>0){
                return true;
            }
        } 
        return false;

    }
}

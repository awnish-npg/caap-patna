<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_ticket()
    {
        $id = array('4', '6');
        $this->db->select('id, name')->from(db_prefix() . 'project_status')->where_not_in('id ', $id);
        $query = $this->db->get();
        return  $query->result_array();
    }


    public function get_status($condition)
    {
        $this->db->select('id, name')->from('project_status')->where($condition);
        $result = $this->db->get()->result_array();
        if (!empty($result)) return $result;
        return false;
    }

    public function get_area()
    {
        $this->db->select('areaid, name')->from(db_prefix() . 'area')->where('status', '1')->where('	is_deleted', '0');
        $this->db->order_by("name", "asc");
        $query = $this->db->get();
        return  $query->result_array();
    }

    public function get_region($area_id = '', $areaids = '', $region = '')
    {
        $this->db->select('id, region_name')->from(db_prefix() . 'region')->where('status', '1')->where('	is_deleted', '0');
        if ($area_id) {
            $this->db->where('area_id', $area_id);
        }
        if ($areaids) {
            $this->db->where_in('area_id', $areaids);
        }
        if ($region)
            $this->db->where('id', $region);
        $this->db->order_by("region_name", "asc");
        $query = $this->db->get();
        return  $query->result_array();
    }

    public function get_subregion($area_id = '', $regionds = '', $sub_region = '')
    {
        $this->db->select('id, region_name')->from(db_prefix() . 'sub_region')->where('status', '1')->where('	is_deleted', '0');
        if ($area_id) {
            $this->db->where('area_id', $area_id);
        }
        if ($regionds) {
            $this->db->where_in('region_id', $regionds);
        }
        if ($sub_region)
            $this->db->where('id', $sub_region);
        $this->db->order_by("region_name", "asc");
        $query = $this->db->get();
        return  $query->result_array();
    }

    public function get_action_taker($area_id = '')
    {
        $this->db->select('CONCAT(s.firstname, " (", s.organisation, ")") AS name, s.staffid, s.organisation');
        $this->db->from('roles r');
        $this->db->join('staff s', 's.role = r.roleid AND r.slug_url = "at" ');
        if ($area_id) {
            $this->db->where('s.area', $area_id);
        }
        $this->db->where("s.active", 1);
        $this->db->order_by("name", "asc");
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_action_reviewer($area_id = '')
    {
        $this->db->select('CONCAT(s.firstname, " (", s.organisation, ")") AS name, s.staffid, s.organisation');
        $this->db->from('roles r');
        $this->db->join('staff s', 's.role = r.roleid AND r.slug_url = "ar" ');
        if ($area_id) {
            $this->db->where('s.area', $area_id);
        }
        $this->db->where("s.active", 1);
        $this->db->order_by("name", "asc");
        $result = $this->db->get()->result_array();
        return $result;
    }


    public function get_ae_region()
    {
        $this->db->select('r.id, r.region_name');
        $this->db->from('staff s');
        $this->db->join('staff_region sr', 'sr.staff_id = s.staffid');
        $this->db->join('region r', 'r.id = sr.region');
        $this->db->where("s.staffid", $this->session->userdata('staff_user_id'));
        $this->db->group_by('r.id');
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }

    public function get_ae_subregion_bk($regionds = '')
    {
        $this->db->select('sbr.id, sbr.region_name');
        $this->db->from('staff s');
        $this->db->join('staff_region sr', 'sr.staff_id = s.staffid');
        $this->db->join('sub_region sbr', 'sbr.id = sr.sub_region');
        $this->db->where("s.staffid", $this->session->userdata('staff_user_id'));
        if ($regionds) {
            $this->db->where_in('sbr.region_id', $regionds);
        }
        $this->db->order_by("region_name", "asc");
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }

    public function get_ae_subregion($regionds = '')
    {
        $this->db->select('id,region_name')->from('sub_region');
        $this->db->where("`region_id` IN (SELECT region  FROM `staff` `s` JOIN `staff_region` `sr` ON `sr`.`staff_id` = `s`.`staffid`  WHERE `s`.`staffid` = '" . $this->session->userdata('staff_user_id') . "')");
        if ($regionds) {
            $this->db->where_in('region_id', $regionds);
        }
        $this->db->order_by("region_name", "asc");
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }

    public function get_total()
    {
        $this->db->select('id')->from(db_prefix() . 'projects');
        $query = $this->db->get();
        return  $query->num_rows();
    }


    public function get_report($tableParams)
    {

        $role = (!empty($tableParams['role'])) ? $tableParams['role'] : $GLOBALS['current_user']->role;
        $region_at = ''; $subregion_at = ''; 
        if (!empty($role) && ($role == 3 || $role == 8)) {

            $user_at  = $this->staff_model->get_userDetails($GLOBALS['current_user']->staffid);
           // $region_at = (!empty($user_at->region)) ? array($user_at->region) : '';
           // $subregion_at = (!empty($user_at->sub_region)) ?  array($user_at->sub_region) : '';
            $tableParams['region'] = '';
            $tableParams['subregion'] = '';
        
        }
        
        $area = (!empty($tableParams['area'])) ? $tableParams['area'] : $GLOBALS['current_user']->area;
        $areaid = (!empty($tableParams['areaid'])) ? $tableParams['areaid'] : '';
        $region = (!empty($tableParams['region'])) ? $tableParams['region'] : $region_at;
        $subregion = (!empty($tableParams['subregion'])) ? $tableParams['subregion'] : $subregion_at;
        $category = (!empty($tableParams['category'])) ? $tableParams['category'] : '';
        $bug = (!empty($tableParams['bug'])) ? $tableParams['bug'] : '';
        $action_taker = (!empty($tableParams['action_taker'])) ? $tableParams['action_taker'] : '';
        $action_reviewer = (!empty($tableParams['action_reviewer'])) ? $tableParams['action_reviewer'] : '';
        $report_date = (!empty($tableParams['report_date'])) ? $tableParams['report_date'] : '';
        $to_date = (!empty($tableParams['to_date'])) ? date("Y-m-d", strtotime($tableParams['to_date'])) : '';
        $from_date = (!empty($tableParams['from_date'])) ? date("Y-m-d", strtotime($tableParams['from_date']))  : '';
        $statusIds = (!empty($tableParams['statusIds'])) ? $tableParams['statusIds'] : '';
        $duration = (!empty($tableParams['duration'])) ? $tableParams['duration'] : '';


        
        $cat_id = [];
        
        if (!empty($tableParams['category'])) {
            foreach($tableParams['category'] as $c_id){
                $cat_id[] = $c_id;
            }
            $catids = $this->get_parent_category($category);
            if (count($catids) > 0) {
                foreach ($catids as $var) {
                    $cat_id[] = $var['id'];
                }
            }
        }

    //    pre($cat_id); 
        $ar_id = [];
        if (!empty($tableParams['action_reviewer'])) {
            $arids = $this->get_ar_assistant($action_reviewer);
            if (count($arids) > 0) {
                foreach ($arids as $var) {
                    $ar_id[] = $var['staff_id'];
                }
            }
        }

        $pro_id = []; 
        if (!empty($report_date) ) {
            $proIds = $this->get_project_action($report_date,$to_date,$from_date,$statusIds);
            if (!empty($proIds)) {
                foreach ($proIds as $var) {
                    $pro_id[] = $var['project_id'];
                }
            }
        }
        
      
       // echo 'pre'; print_r($cat_id); exit;
       
        $this->db->select('projects.frozen,projects.reassigned,projects.id,projects.sub_id,projects.status,projects.action_date,projects.name,projects.description,projects.landmark,projects.project_created,projects.sub_ticket_id, contacts.firstname,contacts.email,contacts.phonenumber,contacts.is_cc ,projects.is_assigned,projects.deadline, projects.date_finished,projects.rname,projects.rphonenumber,projects.remail, IF(contacts.is_cc = 1 , "Call-Center" , "Surveyor") as user_type');
        $this->db->from(db_prefix() . 'projects');

        $this->db->join('contacts', 'contacts.userid = projects.clientid', 'left');

        // for at and ata
        if (!empty($role) && ($role == 3 || $role == 8)) { 
            if(empty($tableParams['action_taker'])){
                $this->db->where('projects.id IN (select project_id from ' . db_prefix() . 'project_members where active= 1 and staff_id = ' . $GLOBALS['current_user']->staffid . ')');
            }     
        }
      


        if (!empty($role) && $role != 5) {
            $this->db->where("projects.area_id", $area);
        }



        // if (!empty($role) && $role == 4) {
        //     $this->db->where("staff_assistance.assistant_id", $GLOBALS['current_user']->staffid);
        // }

        if (!empty($areaid) && count($areaid) > 0) {
            $this->db->where_in('projects.area_id', $areaid);
        }
// pre($region);
        if (!empty($region) && count($region) > 0) {
            $this->db->where_in('projects.region_id', $region);
        }

        if (!empty($subregion) && count($subregion) > 0) {
            $this->db->where_in('projects.subregion_id', $subregion);
        }

        if (empty($statusIds) && empty($report_date)) {
            $this->db->where('projects.id  NOT IN (select id from ' . db_prefix() . 'projects where projects.status = 3 AND projects.frozen = 0)');
            $this->db->where('projects.id  NOT IN (select id from ' . db_prefix() . 'projects where  projects.frozen = 1)');
        }

        if (!empty($duration)  && empty($category) ) {
            $this->db->where('projects.issue_id', 0);
        }

        // if (!empty($category) && count($category) > 0 && $role != 5) {
        //     $this->db->where_in('projects.issue_id', $category);
        // }


        if (!empty($category) && count($category) > 0 && $role != 5) {
            $this->db->where_in('projects.issue_id', $category);
        }

        if (!empty($cat_id) && count($cat_id) > 0 && $role == 5) {
            $this->db->group_start();
            $this->db->where_in('projects.issue_id', $cat_id);
            $this->db->or_where_in('projects.issue_id', $category);
            $this->db->group_end();
        }

        if (!empty($action_taker) && count($action_taker) > 0) {
          //  $this->db->where_in('project_members.staff_id',  $action_taker);
            $this->db->where('projects.id IN (select project_id from ' . db_prefix() . 'project_members where active =1 and  staff_id IN (' . implode(', ', $action_taker) . '))');
        }

        if (!empty($ar_id) && count($ar_id) > 0) {
            //$this->db->where_in('staff_assistance.assistant_id',  $action_reviewer);
            $this->db->where('projects.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id IN (' . implode(', ', $ar_id) . '))');
        }

        if (!empty($bug)) {
            $bg = explode('-', $bug);
            if (count($bg) > 1) {
                $this->db->where("projects.sub_id", $bug);
               
            } else {
                //$this->db->where("projects.id", $bug);
                $this->db->where('(projects.id = "'.$bug.'" OR projects.parent_id = "'.$bug.'")');
            }
        }

        if (!empty($report_date) ) {
            if(!empty($pro_id)){
               $this->db->where_in('projects.id', $pro_id);
            }
            else{
                $this->db->where('projects.id', 0);
            }
        }

      

        if (!empty($statusIds) && count($statusIds) > 0  && empty($report_date)) {
            $this->db->group_start();
            $this->db->or_group_start();

            //new
            if (in_array("1", $statusIds)) {
                $this->db->or_where('(projects.status = 1 AND projects.action_date >= "' . date('Y-m-d') . '" AND projects.frozen = 0)');
            }
            //wip
            if (in_array("2", $statusIds)) {
                if( $GLOBALS['current_user']->role_slug_url == 'ata'){
                    $this->db->or_where('(projects.status IN (2,6)  AND projects.action_date >= CURDATE() AND projects.frozen = 0 ) ');
                } else{
                    $this->db->or_where('(projects.status IN (2,4,6)  AND projects.action_date >= CURDATE() AND projects.frozen = 0 ) ');
                }
                
            }
            //closed
            if (in_array("3", $statusIds)) {
                $this->db->or_where('(projects.status = 3 AND projects.frozen = 0 )');
            }
            //frozen
            if (in_array("8", $statusIds)) {
                $this->db->or_where('projects.frozen = 1 ');
            }
            //Escalated
            if (in_array("7", $statusIds)) {
                if( $GLOBALS['current_user']->role_slug_url == 'ata'){
                    $this->db->or_where('( projects.frozen = 0 AND ((projects.status IN (2,6) AND projects.action_date < CURDATE()) OR (projects.status = 1 AND projects.action_date < "' . date('Y-m-d') . '")) )');
                } else{
                    $this->db->or_where('(projects.frozen = 0 AND ((projects.status IN (2,4,6) AND projects.action_date < CURDATE()) OR (projects.status = 1 AND projects.action_date < "' . date('Y-m-d') . '")) )');
                }
            }
            //Reject
            if (in_array("5", $statusIds)) {
                $this->db->or_where('(projects.status = 5 AND projects.frozen = 0 )');
            }
            //unassigned
           // if ($role == 4 || $role == 6 || $role == 7) {
                if (in_array("9", $statusIds)) {
                    //$this->db->or_where('projects.is_assigned', 0);
                    $this->db->or_where('projects.is_assigned = 0 AND projects.status = 9 AND projects.frozen = 0');
                }
            //}

            $this->db->group_end();
            $this->db->group_end();
        }


        //$this->db->group_by('projects.id');
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();// die();
        return $result;
    }

    public function get_area_name($areaids, $sep = ', ')
    {
        $this->db->select('name')->from(db_prefix() . 'area')->where('status', '1')->where('	is_deleted', '0');
        if ($areaids) {
            $this->db->where_in('areaid', $areaids);
        }
        $query = $this->db->get();
        $areaname =  $query->result_array();
        $area_name = '';


        foreach ($areaname as $var) {
            $area_name .= implode($sep, $var);
            $area_name .= $sep;
        }
        $area_name = rtrim($area_name, $sep);
        return $area_name;
    }


    public function get_region_name($regionids, $sep = ', ')
    {
        $this->db->select('region_name')->from(db_prefix() . 'region')->where('status', '1')->where('	is_deleted', '0');
        if ($regionids) {
            $this->db->where_in('id', $regionids);
        }
        $query = $this->db->get();
        $regionname =  $query->result_array();
        $region_name = '';


        foreach ($regionname as $var) {
            $region_name .= implode($sep, $var);
            $region_name .= $sep;
        }
        $region_name = rtrim($region_name, $sep);
        return $region_name;
    }

    public function get_subregion_name($subregionids, $sep = ', ')
    {
        $this->db->select('region_name')->from(db_prefix() . 'sub_region')->where('status', '1')->where('	is_deleted', '0');
        if ($subregionids) {
            $this->db->where_in('id', $subregionids);
        }
        $query = $this->db->get();
        $subregionname =  $query->result_array();
        $subregion_name = '';


        foreach ($subregionname as $var) {
            $subregion_name .= implode($sep, $var);
            $subregion_name .= $sep;
        }
        $subregion_name = rtrim($subregion_name, $sep);
        return $subregion_name;
    }

    public function get_staff_name($staffids, $sep = ', ')
    {
        $this->db->select('firstname')->from(db_prefix() . 'staff');
        if ($staffids) {
            $this->db->where_in('staffid', $staffids);
        }
        $query = $this->db->get();
        $staffname =  $query->result_array();
        $staff_name = '';


        foreach ($staffname as $var) {
            $staff_name .= implode($sep, $var);
            $staff_name .= $sep;
        }
        $staff_name = rtrim($staff_name, $sep);
        return $staff_name;
    }

    public function get_status_name($statusids, $sep = ', ')
    {
        $this->db->select('name')->from(db_prefix() . 'project_status');
        if ($statusids) {
            $this->db->where_in('id', $statusids);
        }
        $query = $this->db->get();
        $statusname =  $query->result_array();
        $status_name = '';


        foreach ($statusname as $var) {
            $status_name .= implode($sep, $var);
            $status_name .= $sep;
        }
        $status_name = rtrim($status_name, $sep);
        return $status_name;
    }

    public function get_category_name($catids, $sep = ', ')
    {
        $this->db->select('issue_name');
        $this->db->from('issue_categories');
        if ($catids) {
            $this->db->where_in('id', $catids);
        }
        $this->db->order_by("issue_name", "ASC");
        $query = $this->db->get();
        $catname =  $query->result_array();
        $cat_name = '';


        foreach ($catname as $var) {
            $cat_name .= implode($sep, $var);
            $cat_name .= $sep;
        }
        $cat_name = rtrim($cat_name, $sep);
        return $cat_name;
    }

    public function get_duration_name($durationids, $sep = ', ')
    {
        $count = count($durationids) - 1;
        $data["durations"] = [
            ["id" => "30", "duration" => "< 1 month"],
            ["id" => "31 - 183", "duration" => "1 month - 6 months"],
            ["id" => "184 - 365", "duration" => "6 months - 1 year"],
            ["id" => "366 - 1095", "duration" => "1 year - 3 years"],
            ["id" => "1096 - 1825", "duration" => "3 years - 5 years"],
            ["id" => "1826", "duration" => "> 5 years"],
        ];
        $dur_name = '';
        
        $dur = '';
        foreach ($durationids as $var) {
           
            if($var == '30'){
                $dur .= ' less than 1 month,';
            }
            if($var == '31 - 183'){
              $dur .= ' 1 month - 6 months,';
              }
            if($var == '184 - 365'){
                $dur .= ' 6 months - 1 year,';
            }
            if($var == '366 - 1095'){
                $dur .= ' 1 year - 3 years,';
            }
            if($var == '1096 - 1825'){
                $dur .= ' 3 years - 5 years,';
            }
            if($var == '1826'){
                $dur .= ' greater than 5 years,';
            }
            $dur_name .= $dur;
            $dur_name .= $sep;
        }
        $dur_name = rtrim($dur_name, $sep);
        $a = explode(",,",$dur_name);
        $duration_name = $a[$count];
      // print_r($duration_name); exit; 
        return $duration_name;
    }

    
    public function get_current_milestone($project_id,$status=NULL)
    {
        $this->db->select("t.id as task_id, t.name as task_name, t.startdate, t.duedate, t.datefinished, t.task_days, t.is_closed, t.status, ps.name as task_status,t.reminderone_days,t.remindertwo_days,(Select IF(count(id)>1 = 1 , 'Long-term' , 'Short-term') from tasks WHERE `rel_id` = $project_id) as tag");
        $this->db->from("tasks as t");
        $this->db->join("project_status as ps", "t.status = ps.id", "LEFT");
        $this->db->where(["t.rel_id" => $project_id, "t.rel_type" => "project"]);
        if(!empty($status)){
            $this->db->where("t.status",$status);
        }else{
            $this->db->where("t.status != ",0);
        }
        $this->db->order_by("t.id", "DESC");
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;
        if (count($result) > 0) return $result;
    }

    public function save_report($params, $filename)
    {
        $params =  json_encode($params);

        $data['role'] = $GLOBALS['current_user']->role;
        $data['params'] = $params;
        $data['file'] = $filename;
        $data['created_at'] = date('Y-m-d h:i:s');

        $this->db->insert(db_prefix() . 'download_report', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function get_report_summary($tableParams,$summ="")
    {
        $role = (!empty($tableParams['role'])) ? $tableParams['role'] : $GLOBALS['current_user']->role;
        $region_at = ''; $subregion_at = ''; 
        if (!empty($role) && ($role == 3 || $role == 8)) {

            $user_at  = $this->staff_model->get_userDetails($GLOBALS['current_user']->staffid);
           // $region_at = (!empty($user_at->region)) ? array($user_at->region) : '';
           // $subregion_at = (!empty($user_at->sub_region)) ?  array($user_at->sub_region) : '';
           $tableParams['region'] = '';
           $tableParams['subregion'] = '';
          
        }
        $area = (!empty($tableParams['area'])) ? $tableParams['area'] : $GLOBALS['current_user']->area;
        $areaid = (!empty($tableParams['areaid'])) ? $tableParams['areaid'] : '';
        $region = (!empty($tableParams['region'])) ? $tableParams['region'] : $region_at;
        $subregion = (!empty($tableParams['subregion'])) ? $tableParams['subregion'] : $subregion_at;
        $category = (!empty($tableParams['category'])) ? $tableParams['category'] : '';
        $bug = (!empty($tableParams['bug'])) ? $tableParams['bug'] : '';
        $action_taker = (!empty($tableParams['action_taker'])) ? $tableParams['action_taker'] : '';
        $action_reviewer = (!empty($tableParams['action_reviewer'])) ? $tableParams['action_reviewer'] : '';
        $report_date = (!empty($tableParams['report_date'])) ? $tableParams['report_date'] : '';
        $to_date = (!empty($tableParams['to_date'])) ? date("Y-m-d", strtotime($tableParams['to_date'])) : '';
        $from_date = (!empty($tableParams['from_date'])) ? date("Y-m-d", strtotime($tableParams['from_date']))  : '';
        $statusIds = (!empty($tableParams['statusIds'])) ? $tableParams['statusIds'] : '';
        $duration = (!empty($tableParams['duration'])) ? $tableParams['duration'] : '';

        //pre($category);

        $cat_id = [];
        if (!empty($tableParams['category'])) {
            foreach($tableParams['category'] as $c_id){
                $cat_id[] = $c_id;
            }
            $catids = $this->get_parent_category($category);
            if (count($catids) > 0) {
                foreach ($catids as $var) {
                    $cat_id[] = $var['id'];
                }
            }
        }

        $ar_id = [];
        if (!empty($tableParams['action_reviewer'])) {
            $arids = $this->get_ar_assistant($action_reviewer);
            if (count($arids) > 0) {
                foreach ($arids as $var) {
                    $ar_id[] = $var['staff_id'];
                }
            }
        }

        $pro_id = []; 
        if (!empty($report_date) ) {
            $proIds = $this->get_project_action($report_date,$to_date,$from_date,$statusIds);
            if (!empty($proIds)) {
                foreach ($proIds as $var) {
                    $pro_id[] = $var['project_id'];
                }
            }
        }  
        if( $GLOBALS['current_user']->role_slug_url == 'ata'){ 
            $this->db->select('p.name as name,count(*) AS total,
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as new, 
            SUM(CASE WHEN (p.status IN (2,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as escalated,
            SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
            SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as reject,
            SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen,
            SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassign, 
            SUM(CASE WHEN (p.status IN (2,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as wip');
            $this->db->from(db_prefix() . 'projects p');
        } else{
            $this->db->select('p.name as name,count(*) AS total,
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as new, 
            SUM(CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as escalated,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as wip,
            SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
            SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as reject,
            SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassign, 
            SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen
            ');
            $this->db->from(db_prefix() . 'projects p');
        }
        
        // for at and ata
        if (!empty($role) && ($role == 3 || $role == 8)) {
            if(empty($tableParams['action_taker'])){
                $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active= 1 and staff_id = ' . $GLOBALS['current_user']->staffid . ')');
            }
        }


        if (!empty($role) && $role != 5) {
            $this->db->where("p.area_id", $area);
        }

        if (!empty($areaid) && count($areaid) > 0) {
            $this->db->where_in('p.area_id', $areaid);
        }

        if (!empty($region) && count($region) > 0) {
            $this->db->where_in('p.region_id', $region);
        }

        if (!empty($subregion) && count($subregion) > 0) {
            $this->db->where_in('p.subregion_id', $subregion);
        }

        if (empty($statusIds) && empty($report_date)) {
           // $this->db->where("( `p`.`status` NOT IN(3) )");
           $this->db->where('p.id  NOT IN (select id from ' . db_prefix() . 'projects where projects.status = 3 AND projects.frozen = 0)');
           $this->db->where('p.id  NOT IN (select id from ' . db_prefix() . 'projects where  projects.frozen = 1)');
        }

        if (!empty($duration)  && empty($category) ) {
            $this->db->where('p.issue_id', 0);
        }

        if (!empty($category) && count($category) > 0 && $role != 5) {
            $this->db->where_in('p.issue_id', $category);
        }

    
           // $this->db->where_in('p.issue_id', $cat_id);
        if (!empty($cat_id) && count($cat_id) > 0 && $role == 5) {
            $this->db->group_start();
            $this->db->where_in('p.issue_id', $cat_id);
            $this->db->or_where_in('p.issue_id', $category);
            $this->db->group_end();
        
        }

        if (!empty($action_taker) && count($action_taker) > 0) {
            //$this->db->where_in('project_members.staff_id',  $action_taker);
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active=1 and staff_id IN (' . implode(', ', $action_taker) . '))');
        }
        if (!empty($ar_id) && count($ar_id) > 0) {
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id IN (' . implode(', ', $ar_id) . '))');
        }

        // if (!empty($action_reviewer) && count($action_reviewer) > 0) {
        //     $this->db->where_in('staff_assistance.assistant_id',  $action_reviewer);
        // }

        if (!empty($bug)) {
            $bg = explode('-', $bug);
            if (count($bg) > 1) {
                $this->db->where("p.sub_id", $bug);
            } else {
                //$this->db->where("p.id", $bug);
                $this->db->where('(p.id = "'.$bug.'" OR p.parent_id = "'.$bug.'")');
            }
        }

        if (!empty($report_date) ) {
            if(!empty($pro_id)){
               $this->db->where_in('p.id', $pro_id);
            }
            else{
                $this->db->where('p.id', 0);
            }
        }

        if (!empty($report_date) && $report_date != 'custom') {
           
            // if ($report_date == 'this_month') {
            //     $this->db->where("MONTH(p.action_date)", date('m'));
            // } else if ($report_date == 'last_month') {
            //     $this->db->where("MONTH(p.action_date)", date('m', strtotime('-1 month')));
            // } else if ($report_date == 'this_year') {
            //     $this->db->where("YEAR(p.action_date)", date('Y'));
            // } else if ($report_date == 'last_year') {
            //     $this->db->where("YEAR(p.action_date)", date('Y', strtotime('-1 year')));
            // } else if ($report_date == '3') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-2 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '6') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-5 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '12') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-11 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // }
        }

        // if (!empty($report_date) && $report_date == 'custom' && !empty($to_date) && !empty($from_date)) {
        //     $this->db->where('p.action_date >=', $from_date);
        //     $this->db->where('p.action_date <=', $to_date);
        // }


        if (!empty($statusIds) && count($statusIds) > 0 && empty($report_date)) {
            $this->db->group_start();
            $this->db->or_group_start();

            //new
            if (in_array("1", $statusIds)) {
                $this->db->or_where('(p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0)');
            }
            //wip
            if (in_array("2", $statusIds)) {
                if( $GLOBALS['current_user']->role_slug_url == 'ata'){
                    $this->db->or_where('(p.status IN (2,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) ');
                } else{
                    $this->db->or_where('(p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) ');
                }
            }
            //closed
            if (in_array("3", $statusIds)) {
                $this->db->or_where('(p.status = 3 AND p.frozen = 0)');
            }
            //frozen
            if (in_array("8", $statusIds)) {
                $this->db->or_where('p.frozen = 1 ');
            }
            //Escalated
            if (in_array("7", $statusIds)) {
                if( $GLOBALS['current_user']->role_slug_url == 'ata'){
                    $this->db->or_where('(p.frozen = 0 AND ((p.status IN (2,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '")) )');
                } else{
                    $this->db->or_where('(p.frozen = 0 AND ((p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '")) )');
                }
            }
            //Reject
            if (in_array("5", $statusIds)) {
                $this->db->or_where('(p.status = 5 AND p.frozen = 0 )');
            }
            //unassigned
            //if ($role == 4 || $role == 6 || $role == 7) {
                if (in_array("9", $statusIds)) {
                   // $this->db->or_where('p.is_assigned', 0);
                   $this->db->or_where('p.is_assigned = 0 AND p.status = 9 AND p.frozen = 0');
                }
            //}

            $this->db->group_end();
            $this->db->group_end();
        }

        if($summ != ''){
            $this->db->group_by('p.name');
        }
        
        $result = $this->db->get()->result_array();
       // echo $this->db->last_query();/// die;
        return $result;
    }

    public function get_parent_category($category)
    {
        $this->db->select('id')->from(db_prefix() . 'issue_categories');
        if ($category) {
            $this->db->where_in('parent_issue_id', $category);
        }
        $query = $this->db->get();
        $catid =  $query->result_array();
        return $catid;
    }

    public function get_ar_assistant($id)
    {
        $this->db->select('staff_id')->from(db_prefix() . 'staff_assistance');
        if ($id) {
            $this->db->where_in('assistant_id', $id);
        }
        $query = $this->db->get();
        $arid =  $query->result_array();
        return $arid;
    }

    public function get_report_leader($id)
    {
        $this->db->select('staff.firstname,project_members.staff_id,staff.active as staff_status');
        $this->db->from(db_prefix() . 'project_members');
        $this->db->join('staff', 'staff.staffid = project_members.staff_id','left');
        $this->db->where('project_id', $id);
        $this->db->where('staff.role', 3);
        $this->db->where('project_members.active', 1);
        $this->db->order_by("project_members.id", "DESC");
        $query = $this->db->get();
        $leader =  $query->row();
       // echo $this->db->last_query();// die();
        return $leader;
    }

    public function get_report_support($id)
    {
        $this->db->select('staff.firstname,project_members.staff_id,staff.active as staff_status');
        $this->db->from(db_prefix() . 'project_members');
        $this->db->join('staff', 'staff.staffid = project_members.staff_id','left');
        $this->db->where('project_id', $id);
        $this->db->where('staff.role', 8);
        $this->db->where('project_members.active', 1);
        $this->db->order_by("project_members.id", "DESC");
        $query = $this->db->get();
        $leader =  $query->row();
       // echo $this->db->last_query();// die();
        return $leader;
    }


    public function get_evidence($id, $sep = ', ')
    {
        $this->db->select('file_name')->from(db_prefix() . 'project_files');
        if ($id) {
            $this->db->where('project_id', $id);
        }
        $query = $this->db->get();
        $file =  $query->result_array();
        $file_name = '';
        foreach ($file as $var) {
            $file_name .= base_url('uploads/projects/' . $id . '/') . implode($sep,  $var);
            $file_name .= $sep;
        }
        $file_name = rtrim($file_name, $sep);
        $filename = explode(",",$file_name);
        return $filename;
    }

    public function get_location($id, $sep = '- ')
    {
        $this->db->select('latitude,longitude')->from(db_prefix() . 'project_files');
        if ($id) {
            $this->db->where('project_id', $id);
        }
    
        $query = $this->db->get();
        $file =  $query->result_array();
        $file_name = '';
        foreach ($file as $var) {
          // print_r($var); exit;
          if($var['latitude'] == 0 && $var['longitude'] == 0 ){
            $file_name .= '';
            $file_name .= $sep;
          }else{
           // $file_name .= base_url('admin/tickets/viewmap/' . $id . '?lat=') . $var['latitude'] .'&lang='. $var['longitude'] .'&output=embed';
            $file_name .=  "https://maps.google.com/maps?q=" . $var['latitude'] . "," . $var['latitude']. "";
           
            
            $file_name .= $sep;
          }
           
        }
    
       $file_name = rtrim($file_name, $sep); 
     
        $filename = explode("-",$file_name);
        return $filename;
    }

    public function get_project_details($projectId)
    {
        $select = "p.id as project_id,p.`name` as project_name, p.`status` as project_status, p.project_created as logged_date, p.*, a.`name` as area_name, r.region_name, sr.region_name as sub_region_name,c.company,ps.name as status_name,
        pm.staff_id as assigned_user_id,pm.created_at as assigned_date,ta.status as task_status";
        $this->db->select($select);
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'area a ', 'a.areaid = p.area_id', 'left');
        $this->db->join(db_prefix() . 'region r ', 'r.id = p.region_id', 'left');
        $this->db->join(db_prefix() . 'sub_region sr ', 'sr.id = p.subregion_id', 'left');
        $this->db->join(db_prefix() . 'clients c ', 'c.userid = p.clientid', 'left');
        $this->db->join(db_prefix() . 'task_assigned ta ', 'ta.taskid = p.id', 'left');
        $this->db->join(db_prefix() . 'project_members pm ', 'pm.project_id = p.id', 'left');
        $this->db->join(db_prefix() . 'project_status ps ', 'ps.id = p.status', 'left');
        $this->db->where('p.id', $projectId);
        return $this->db->get()->row();
    }


    public function get_report_total($tableParams)
    {
        $role = (!empty($tableParams['role'])) ? $tableParams['role'] : $GLOBALS['current_user']->role;
        $region_at = ''; $subregion_at = ''; 
        if (!empty($role) && ($role == 3 || $role == 8)) {

            $user_at  = $this->staff_model->get_userDetails($GLOBALS['current_user']->staffid);
            $region_at = (!empty($user_at->region)) ? array($user_at->region) : '';
            $subregion_at = (!empty($user_at->sub_region)) ?  array($user_at->sub_region) : '';
           // $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id = ' . $GLOBALS['current_user']->staffid . ')');
        }
        $area = (!empty($tableParams['area'])) ? $tableParams['area'] : $GLOBALS['current_user']->area;
        $areaid = (!empty($tableParams['areaid'])) ? $tableParams['areaid'] : '';
        $region = (!empty($tableParams['region'])) ? $tableParams['region'] : $region_at;
        $subregion = (!empty($tableParams['subregion'])) ? $tableParams['subregion'] : $subregion_at;
        $category = (!empty($tableParams['category'])) ? $tableParams['category'] : '';
        $bug = (!empty($tableParams['bug'])) ? $tableParams['bug'] : '';
        $action_taker = (!empty($tableParams['action_taker'])) ? $tableParams['action_taker'] : '';
        $action_reviewer = (!empty($tableParams['action_reviewer'])) ? $tableParams['action_reviewer'] : '';
        $report_date = (!empty($tableParams['report_date'])) ? $tableParams['report_date'] : '';
        $to_date = (!empty($tableParams['to_date'])) ? date("Y-m-d", strtotime($tableParams['to_date'])) : '';
        $from_date = (!empty($tableParams['from_date'])) ? date("Y-m-d", strtotime($tableParams['from_date']))  : '';
        $statusIds = (!empty($tableParams['statusIds'])) ? $tableParams['statusIds'] : '';
        $duration = (!empty($tableParams['duration'])) ? $tableParams['duration'] : '';

        $cat_id = [];
        if (!empty($tableParams['category'])) {
            foreach($tableParams['category'] as $c_id){
                $cat_id[] = $c_id;
            }
            $catids = $this->get_parent_category($category);
            if (count($catids) > 0) {
                foreach ($catids as $var) {
                    $cat_id[] = $var['id'];
                }
            }
        }

        $ar_id = [];
        if (!empty($tableParams['action_reviewer'])) {
            $arids = $this->get_ar_assistant($action_reviewer);
            if (count($arids) > 0) {
                foreach ($arids as $var) {
                    $ar_id[] = $var['staff_id'];
                }
            }
        }


        $this->db->select('SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
        SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as reject,
        SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassign, 
        SUM(CASE WHEN ((p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0)  ) THEN 1 ELSE 0  END) as escalated,
        SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen,
        SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0) THEN 1 ELSE 0  END) as wip');
        $this->db->from(db_prefix() . 'projects p');

        // if (!empty($action_reviewer) && count($action_reviewer)) {
        //     $this->db->join('project_members', 'project_members.project_id = p.id ', 'left');
        //     $this->db->join('staff_assistance', 'staff_assistance.staff_id = project_members.staff_id', 'left');
        // }
        // $this->db->join('task_assigned', 'task_assigned.taskid = p.id','left');
        // $this->db->join('contacts', 'contacts.userid = p.clientid','left');
        // $this->db->join('issue_categories', 'issue_categories.id = p.issue_id','left');

        // if (!empty($role) && ($role == 3 || $role == 5 ||$role == 8 )) {
        //     $this->db->where("p.is_assigned", 1);
        // }

        // if (!empty($role) && ($role == 3 || $role == 8)) {
        //     //$this->db->where("project_members.staff_id", $GLOBALS['current_user']->staffid);
        //     $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id = ' . $GLOBALS['current_user']->staffid . ')');
        // }

        if (!empty($role) && $role != 5) {
            $this->db->where("p.area_id", $area);
        }

        if (!empty($areaid) && count($areaid) > 0) {
            $this->db->where_in('p.area_id', $areaid);
        }

        if (!empty($region) && count($region) > 0) {
            $this->db->where_in('p.region_id', $region);
        }

        if (!empty($subregion) && count($subregion) > 0) {
            $this->db->where_in('p.subregion_id', $subregion);
        }

        if (empty($statusIds)) {
           // $this->db->where("( `p`.`status` NOT IN(3) )");
            $this->db->where('p.id NOT IN (select id from ' . db_prefix() . 'projects where projects.status = 3 AND projects.frozen = 0)');
        }

        if (!empty($duration)  && empty($category) ) {
            $this->db->where('p.issue_id', 0);
        }

        if (!empty($category) && count($category) > 0 && $role != 5) {
            $this->db->where_in('p.issue_id', $category);
        }

        if (!empty($cat_id) && count($cat_id) > 0 && $role == 5) {
            $this->db->where_in('p.issue_id', $cat_id);
        }

        if (!empty($action_taker) && count($action_taker) > 0) {
            //$this->db->where_in('project_members.staff_id',  $action_taker);
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id IN (' . implode(', ', $action_taker) . '))');
        }

        // if (!empty($action_reviewer) && count($action_reviewer) > 0) {
        //     $this->db->where_in('staff_assistance.assistant_id',  $action_reviewer);
        // }

        if (!empty($ar_id) && count($ar_id) > 0) {
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where staff_id IN (' . implode(', ', $ar_id) . '))');
        }

        if (!empty($bug)) {
            $bg = explode('-', $bug);
            if (count($bg) > 1) {
                $this->db->where("p.sub_id", $bug);
            } else {
                //$this->db->where("p.id", $bug);
                $this->db->where('(p.id = "'.$bug.'" OR p.parent_id = "'.$bug.'")');
            }
        }

        if (!empty($report_date) && $report_date != 'custom') {

            if ($report_date == 'this_month') {
                $this->db->where("MONTH(p.action_date)", date('m'));
            } else if ($report_date == 'last_month') {
                $this->db->where("MONTH(p.action_date)", date('m', strtotime('-1 month')));
            } else if ($report_date == 'this_year') {
                $this->db->where("YEAR(p.action_date)", date('Y'));
            } else if ($report_date == 'last_year') {
                $this->db->where("YEAR(p.action_date)", date('Y', strtotime('-1 year')));
            } else if ($report_date == '3') {
                $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-2 MONTH')));
                $this->db->where('p.action_date <=', date('Y-m-t'));
            } else if ($report_date == '6') {
                $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-5 MONTH')));
                $this->db->where('p.action_date <=', date('Y-m-t'));
            } else if ($report_date == '12') {
                $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-11 MONTH')));
                $this->db->where('p.action_date <=', date('Y-m-t'));
            }
        }

        if (!empty($report_date) && $report_date == 'custom' && !empty($to_date) && !empty($from_date)) {
            $this->db->where('p.action_date >=', $from_date);
            $this->db->where('p.action_date <=', $to_date);
        }


        if (!empty($statusIds) && count($statusIds) > 0) {
            $this->db->group_start();
            $this->db->or_group_start();

            //new
            if (in_array("1", $statusIds)) {
                $this->db->or_where('(p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0)');
            }
            //wip
            if (in_array("2", $statusIds)) {
                $this->db->or_where('(p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0) ');
            }
            //closed
            if (in_array("3", $statusIds)) {
                $this->db->or_where('(p.status = 3 AND p.frozen = 0)');
            }
            //frozen
            if (in_array("8", $statusIds)) {
                $this->db->or_where('p.frozen = 1');
            }
            //Escalated
            if (in_array("7", $statusIds)) {
                $this->db->or_where('(p.frozen = 0 AND ((p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '")) ) ');
            }
            //Reject
            if (in_array("5", $statusIds)) {
                $this->db->or_where('(p.status = 5 AND p.frozen = 0)');
            }
            //unassigned
            //if ($role == 4 || $role == 6 || $role == 7) {
                if (in_array("9", $statusIds)) {
                   // $this->db->or_where('p.is_assigned', 0);
                   $this->db->or_where('p.is_assigned = 0 AND p.status = 9 AND p.frozen = 0');
                }
            //}

            $this->db->group_end();
            $this->db->group_end();
        }
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query(); 
        return $result;
    }

    function get_user_location()
    {
        $location = '';
        $role = $GLOBALS['current_user']->role;
        if(!empty($GLOBALS['current_user']->location))
        {
            $area = !empty($GLOBALS['current_user']->location['area']) ? $GLOBALS['current_user']->location['area'] : '';
            $region = !empty($GLOBALS['current_user']->location['region_name']) ? $GLOBALS['current_user']->location['region_name'] : '';
            $subregion = !empty($GLOBALS['current_user']->location['subregion_name']) ? $GLOBALS['current_user']->location['subregion_name'] : '';
            $subregion = !empty($GLOBALS['current_user']->location['subregion_name']) ? $GLOBALS['current_user']->location['subregion_name'] : '';
        }
        if($role == 5 || $role == 4 ){
            $location = '';
        }else if($role == 6){
            $location = $area;
        }else{
            $location = $area.",".$region.",".$subregion;
        }
        return $location;
    }

    function get_project_action($datefilter,$to_date = '',$from_date = '',$status)
    {

        $this->db->select('project_activity.project_id');
        $this->db->from(db_prefix() . 'project_activity');
        if (!empty($status)) {
            $this->db->where_in('project_activity.status', $status);
        }
        if (!empty($datefilter) && $datefilter != 'custom') {
            if ($datefilter == 'this_month') {
                $this->db->where("MONTH(project_activity.dateadded)", date('m'));
            } else if ($datefilter == 'last_month') {
                $this->db->where("MONTH(project_activity.dateadded)", date('m', strtotime('-1 month')));
            } else if ($datefilter == 'this_year') {
                $this->db->where("YEAR(project_activity.dateadded)", date('Y'));
            } else if ($datefilter == 'last_year') {
                $this->db->where("YEAR(project_activity.dateadded)", date('Y', strtotime('-1 year')));
            } else if ($datefilter == '3') {
                $this->db->where('DATE(project_activity.dateadded) >=', date('Y-m-01', strtotime('-2 MONTH')));
                $this->db->where('DATE(project_activity.dateadded) <=', date('Y-m-t'));
            } else if ($datefilter == '6') {
                $this->db->where('DATE(project_activity.dateadded) >=', date('Y-m-01', strtotime('-5 MONTH')));
                $this->db->where('DATE(project_activity.dateadded) <=', date('Y-m-t'));
            } else if ($datefilter == '12') {
                $this->db->where('DATE(project_activity.dateadded) >=', date('Y-m-01', strtotime('-11 MONTH')));
                $this->db->where('DATE(project_activity.dateadded) <=', date('Y-m-t'));
            }
        }
        if (!empty($datefilter) && $datefilter == 'custom' && !empty($to_date) && !empty($from_date)) {
            $this->db->where('DATE(project_activity.dateadded) >=', $from_date);
            $this->db->where('DATE(project_activity.dateadded) <=', $to_date);
        }
       // $this->db->where_in('project_activity.dateadded', 'SELECT MAX(dateadded) FROM project_activity GROUP BY project_id');
        
        $this->db->group_by('project_activity.project_id');
        $this->db->order_by("project_activity.dateadded", "DESC");
        
        $result = $this->db->get()->result_array();
        // echo $this->db->last_query(); EXIT;
        if (count($result) > 0) return $result;
        
    }

   
}

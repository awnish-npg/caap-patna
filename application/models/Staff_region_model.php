<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_region_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer ID (optional)
     * @param  boolean (optional)
     * @return mixed
     */
    public function get_staff_region($id = false)
    {

        $data = $this->app_object_cache->get('staff-region-data-' . $id);
        //echo "<pre>";print_r($data);echo "</pre>";
        if(!$data){
            if (is_numeric($id)) {
                $this->db->select("sr.*, r.region_name as region_name, sreg.region_name as subregion_name, GROUP_CONCAT(r.region_name) as region, GROUP_CONCAT(sreg.region_name) as sub_region");
                $this->db->where('staff_id', $id);
                $this->db->from('staff_region sr');
                $this->db->join('region r', 'sr.region = r.id ', 'left');
                $this->db->join('sub_region sreg', 'sr.sub_region = sreg.id ', 'left');
                $data = $this->db->get()->row();
                $this->app_object_cache->set('staff-region-data-' . $id, $data);
                return $data;
            }
        }
        
        //echo "<pre>";print_r($data);echo "</pre>";
        
        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new sub-region
     */
    public function add($data)
    {
        $data['region_name'] = trim(ucwords($data['region_name']));
        $data['area_id'] = $this->get_area($this->session->userdata('staff_user_id')); 
        $data['created_at'] = date('Y-m-d h:i:s');
        $data['created_by'] = $this->session->userdata('staff_user_id');    
        $data = hooks()->apply_filters('before_subregion_added', $data);
       
        $this->db->insert(db_prefix() . 'sub_region', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            hooks()->do_action('after_region_added', $insert_id);
            log_activity('New Sub-Region Added [' . $data['region_name'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update sub-region to database
     */
    public function update($data, $id)
    {
        $region_original = $this->get($id);
        if (!$region_original) {
            return false;
        }
        $data['region_name'] = trim(ucwords($data['region_name']));
        $data['area_id'] = $this->get_area($this->session->userdata('staff_user_id')); 
        $data['updated_at'] = date('Y-m-d h:i:s');
        $data = hooks()->apply_filters('before_subregion_updated', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sub_region', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Sub-Region Updated [Name: ' . $data['region_name'] . ', ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete subregion from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $current = $this->get($id);
        if (!$current) {
            return false;
        }

        hooks()->do_action('before_delete_area', $id);
        $data['is_deleted'] = '1';
        $this->db->where('id', $id);     
        $this->db->update(db_prefix() . 'sub_region', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Sub-Region Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    public function get_region($area_id)
    {
        $this->db->select('id, region_name')->from(db_prefix() . 'region')->where('status', 1)->where('is_deleted', 0)->where('area_id', $area_id)->where('created_by', $this->session->userdata('staff_user_id'));
        $query = $this->db->get();
        return  $query->result_array();
    }

     

      /**
     * Change sub-region status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_subregion_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sub_region', [
            'status' => $status,
        ]);

        log_activity('Sub-Region Status Changed [Sub-Region ID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
        if($this->db->affected_rows() > 0)
            return true;
        return false;
    }

    // get login area id
    public function get_area($id)
    {
        $this->db->where('staffid', $id);
        $area =  $this->db->get(db_prefix() . 'staff')->row();
        return $area->area;
    }

    public function get_subregion($data)
    {
        $this->db->where($data);
        return $this->db->get('sub_region')->row();
    }


    public function get_subregion_by_id($id)
    {
        $this->db->select('id, region_name')->from(db_prefix() . 'sub_region')->where('status', 1)->where('is_deleted', 0)->where('region_id', $id)->where('created_by', $this->session->userdata('staff_user_id'));
        $query = $this->db->get();
        return  $query->result_array();
    }



  
}

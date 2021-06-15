<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Region_model extends App_Model
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
    public function get($id = false)
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $this->db->where('is_deleted', '0');
            if (!is_admin()) {
                $this->db->where('created_by', $this->session->userdata('staff_user_id'));
            }
            return $this->db->get(db_prefix() . 'region')->row();
        }

        $region = $this->app_object_cache->get('region');

        return $region;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new region
     */
    public function add($data)
    {
        $data['region_name'] = trim(ucwords($data['region_name']));
        $data['area_id'] = $this->get_area($this->session->userdata('staff_user_id'));
        $data['created_by'] = $this->session->userdata('staff_user_id');

        $data = hooks()->apply_filters('before_region_added', $data);

        $this->db->insert(db_prefix() . 'region', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            hooks()->do_action('after_region_added', $insert_id);
            log_activity('New Region Added [' . $data['region_name'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update region to database
     */
    public function update($data, $id)
    {
        $region_original = $this->get($id);
        if (!$region_original) {
            return false;
        }

        $data['region_name'] = trim(ucwords($data['region_name']));
        $data['area_id'] = $this->get_area($this->session->userdata('staff_user_id'));

        $data = hooks()->apply_filters('before_region_updated', $data, $id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'region', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Region Updated [Name: ' . $data['region_name'] . ', ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete region from database, if used return array with key referenced
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
        $this->db->update(db_prefix() . 'region', $data);

        if ($this->db->affected_rows() > 0) {
            $this->db->where('region_id', $id);
            $this->db->update(db_prefix() . 'sub_region', $data);
            log_activity('Region Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Change region status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_region_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'region', [
            'status' => $status,
        ]);

        log_activity('Region Status Changed [AreaID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
        if ($this->db->affected_rows() > 0) {
            $this->load->model("area_model");
            $this->area_model->deactivate_sub_regions($id, $status);
            return true;
        }
        return false;
    }

    // get login area id
    public function get_area($id)
    {
        $this->db->where('staffid', $id);
        $area =  $this->db->get(db_prefix() . 'staff')->row();
        return $area->area;
    }

    public function get_region($data)
    {
        $this->db->where($data);
        $this->db->order_by('region_name');
        return $this->db->get('region')->result_array();
    }

    public function get_area_region($area_id = false)
    {
        $this->db->select('r.id as region_id, r.area_id, r.region_name, sr.id as sub_region_id, sr.region_name as sub_region_name');
        $this->db->from('region r');
        $this->db->join('sub_region sr', 'sr.region_id = r.id AND sr.status = 1 AND sr.is_deleted = 0', 'left');
        $this->db->where(['r.area_id' => $area_id, 'r.status' => 1, 'r.is_deleted' => 0]);
        // $this->db->where('r.id NOT IN (SELECT region from staff_region)');
        // $this->db->where('sr.id NOT IN (SELECT str.sub_region from staff_region str where sr.region_id = str.region)');
        $this->db->order_by("r.region_name", "ASC");
        $result =  $this->db->get()->result_array();
        return $result;
    }

    public function get_staff_region($id)
    {
        $this->db->select('region, sub_region');
        $this->db->from('staff_region');
        $this->db->where('staff_id', $id);
        $result = $this->db->get()->result_array();
        if (!empty($result))
            return $result;
        return false;
    }

    public function update_status_by_area($area_id, $status)
    {
        $this->db->where('area_id', $area_id);
        $this->db->update(db_prefix() . 'region', [
            'status' => $status,
        ]);
    }

    // public function get_area_sub_region($regions = false, $area_id)
    // {
    //     // print_r($regions);die;
    //     // print_r($area_id);
    //     $this->db->select('sr.id, sr.area_id, sr.region_id, sr.region_name');
    //     $this->db->from('sub_region sr');
    //     $this->db->where_in('sr.region_id', $regions);
    //     $this->db->where(['area_id' => $area_id, 'status' => 1, 'is_deleted' => 0]);
    //     // $this->db->where('sr.id NOT IN (SELECT str.sub_region from staff_region str where sr.region_id = str.region)');
    //     $result = $this->db->get()->result_array();
    //     return $result;
    // }
}

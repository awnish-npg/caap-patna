<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Area_model extends App_Model
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
    public function get($id = false, $is_active = false, $exclude_staff_area = false, $data = [])
    {
        if (is_numeric($id)) {
            $this->db->where('areaid', $id);
            $this->db->where('is_deleted', '0');
            return $this->db->get(db_prefix() . 'area')->row();
        }
        if ($is_active === true) {
            $this->db->where('is_deleted', '0');
            $this->db->where('status', '1');
            return $this->db->get(db_prefix() . 'area')->result_array();
        }
        if ($exclude_staff_area === true) {
            $this->db->select('areaid, name');
            $this->db->where('is_deleted', '0');
            $this->db->where('status', '1');
            if (!empty($data) && isset($data['role']))
                $this->db->where('areaid NOT IN (SELECT area from staff WHERE active = 1 AND role=' . $data['role'] . ')');
            else
                $this->db->where('areaid NOT IN (SELECT area from staff WHERE active = 1)');

            $this->db->order_by('name', 'asc');
            return $this->db->get(db_prefix() . 'area')->result_array();
        }
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new area
     */
    public function add($data, $file)
    {

        $data['name'] = trim(ucwords($data['name']));
        $data['file'] = $file;
        $data['logo'] = !empty($data['logo']) ? $data['logo'] : '';
        $data['created_at'] = date('Y-m-d h:i:s');
        $data = hooks()->apply_filters('before_area_added', $data);

        $this->db->insert(db_prefix() . 'area', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            hooks()->do_action('after_area_added', $insert_id);
            log_activity('New Area Added [' . $data['name'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update department to database
     */
    public function update($data, $file, $id)
    {
        $current = $this->get($id);
        if (!$current) {
            return false;
        }
        $data['name'] = trim(ucwords($data['name']));
        $data['file'] = $file;
        $data['logo'] = !empty($data['logo']) ? $data['logo'] : '';
        $data['updated_at'] = date('Y-m-d h:i:s');
        $data = hooks()->apply_filters('before_area_updated', $data, $id);

        $this->db->where('areaid', $id);
        $this->db->update(db_prefix() . 'area', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Area Updated [Name: ' . $data['name'] . ', ID: ' . $id . ']');

            return true;
        }
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete area from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $current = $this->get($id);
        if (!$current) {
            return false;
        }
        hooks()->do_action('before_delete_area', $id);

        $data['is_deleted'] = '1';
        $this->db->where('areaid', $id);
        $this->db->update(db_prefix() . 'area', $data);

        if ($this->db->affected_rows() > 0) {

            $this->db->where('area_id', $id);
            $this->db->update(db_prefix() . 'region', $data);

            $this->db->where('area_id', $id);
            $this->db->update(db_prefix() . 'sub_region', $data);

            log_activity('Area Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_area_status($id, $status)
    {

        $this->db->where('areaid', $id);
        $this->db->update(db_prefix() . 'area', [
            'status' => $status,
        ]);

        log_activity('Area Status Changed [AreaID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
        if ($this->db->affected_rows() > 0) {
            if ($status == 0) {
                $this->deactivate_regions($id);
            }
            return true;
        }
        return false;
    }

    public function deactivate_regions($area_id)
    {
        $this->db->where("area_id", $area_id);
        $this->db->update("region", ["status" => 0]);
        if ($this->db->affected_rows() > 0) {
            $this->db->select('id');
            $this->db->from("region");
            $this->db->where("area_id", $area_id);
            $regions = $this->db->get()->result_array();
            if (count($regions) > 0) {
                $this->deactivate_sub_regions($regions, $area_id);
            }
        }
    }

    public function deactivate_sub_regions($regions, $status)
    {
        if (is_array($regions)) {

            foreach ($regions as $region) {
                $this->db->where(["region_id" => $region["id"]]);
                $this->db->update("sub_region", ["status" => $status]);
            }
        }else {
            $this->db->where(["region_id" => $regions]);
            $this->db->update("sub_region", ["status" => $status]);
        }
    }

    public function get_area($data)
    {
        $this->db->where($data);
        return $this->db->get('area')->result_array();
    }
}

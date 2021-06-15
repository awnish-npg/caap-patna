<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Exception_model extends App_Model
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
    public function get($id = false, $is_active = false )
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $this->db->where('is_deleted', '0');
            return $this->db->get(db_prefix() . 'exception')->row();
        }
        if ($is_active === true) {
            $this->db->where('is_deleted', '0');
            $this->db->where('status', '1');
            return $this->db->get(db_prefix() . 'exception')->result_array();
        }
       
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new area
     */
    public function add($data)
    {

        $data['name'] = trim(ucwords($data['name']));
        $data['created_at'] = date('Y-m-d h:i:s');
       
        $this->db->insert(db_prefix() . 'exception', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
          
            log_activity('New Exception Added [' . $data['name'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update department to database
     */
    public function update($data, $id)
    {
        $current = $this->get($id);
        if (!$current) {
            return false;
        }
        $data['name'] = trim(ucwords($data['name']));
        $data['updated_at'] = date('Y-m-d h:i:s');
       
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'exception', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Exception Updated [Name: ' . $data['name'] . ', ID: ' . $id . ']');

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
       
        $data['is_deleted'] = '1';
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'exception', $data);

        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_exception_status($id, $status)
    {

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'exception', [
            'status' => $status,
        ]);

        log_activity('Exception Status Changed [ID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
        if($this->db->affected_rows() > 0)
            return true;
        return false;
    }


    public function get_exception($data)
    {
        $this->db->where($data);
        return $this->db->get('exception')->result_array();
    }

    public function get_exception_list()
    {
        $this->db->where('status',1);
        $this->db->order_by('order');
        return $this->db->get('exception')->result_array();
    }
}

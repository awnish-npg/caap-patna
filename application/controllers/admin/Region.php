<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Region extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('region_model');
    }

    /* List all region */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('region');
        }

        $data['title']                = _l('region');
        $this->load->view('admin/region/manage', $data);
    }

    /* Edit or add new region */
    public function region($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $if_region_exist = [];
            $area_id = $this->region_model->get_area($this->session->userdata('staff_user_id'));

            if (trim($data['region_name']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid City/ Corporation name.',
                ]);
            } else if (!$this->input->post('id')) {
                // add
                $if_region_exist = $this->region_model->get_region(['region_name' => trim($data['region_name']), 'area_id' => $area_id]);

                if ($if_region_exist) {

                    echo json_encode([
                        'success'              => false,
                        'message'              => 'City/ Corporation already in use.',
                    ]);
                } else {
                    $id = $this->region_model->add($data);
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('region'));
                    }
                    echo json_encode([
                        'success'              => $success,
                        'message'              => $message,
                    ]);
                }
            } else {
                // update
                $id = $data['id'];
                $region = $this->region_model->get_region(['id' => $data['id']]);
              
                if(!empty($region[0]['region_name'])){
                    if ($region[0]['region_name'] != trim($data['region_name'])) {
                        $if_region_exist = $this->region_model->get_region(['region_name' => trim($data['region_name']), 'area_id' => $area_id]);
                    }
                }
               

                if ($if_region_exist) {
                    echo json_encode([
                        'success'              => false,
                        'message'              => 'City/ Corporation already in use.',
                    ]);
                } else {

                    unset($data['id']);

                    $success = $this->region_model->update($data, $id);

                    $message = _l('updated_successfully', _l('region'));

                    echo json_encode([
                        'success'              => true,
                        'message'              => $message,
                    ]);
                }
            }
            die;
        }
    }

    /* Delete area from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('area'));
        }
        $response = $this->region_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('region')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('region')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('region')));
        }
        redirect(admin_url('region'));
    }


    /* Change status to area active or inactive / ajax */
    public function change_region_status()
    {
     
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $id = $data['id'];
            $status = $data['status'];
            $update_status = $this->region_model->change_region_status($id, $status);
            if ($update_status) {
                $response = [
                    'success' => true,
                    'message' => "Status has been updated successfully.",
                    'check_status' => (int)$status
                ];
                $staff_ids = $this->staff_model->get_region_staff(["region" => $id]);
                // pre($staff_ids);
                if (count($staff_ids) > 0) {
                    // Deactivate staff users
                    $this->staff_model->deactivate_staff($staff_ids, $status);

                    // insert log in project activity 
                  
                    $projectids = array();
                    $projectids = $this->projects_model->getFrozenProjects(['region_id' => $id],$status);
                    if(!empty($projectids)){
                        $this->projects_model->updateFrozenStaus($projectids,$status);
                    }

                    // Set respective project to frozen
                    $this->projects_model->set_project_frozen(["region_id" => $id], $status);
                }
            }
            echo json_encode($response);
            die;
        }
    }

    /* Get status to region active or inactive / ajax */
    public function get_status()
    {
        $data               = $this->input->post();
        $id                 = $data['id'];

        $status = $this->region_model->get_status($id);
        echo json_encode($status);
    }

    public function get_region()
    {
        if ($this->input->post()) {
            $area_id = $this->input->post('area_id');
            $region_list = $this->region_model->get_area_region($area_id);
            if (count($region_list) > 0) {
                $grouped_region_list = $region_list;
                if ($this->input->post('group_by'))
                    $grouped_region_list = group_by("region_name", $region_list);
                // print_r($grouped_region_list);
                // die;
                echo json_encode([
                    'success' => true,
                    'message' => "Successfully fetched the region list.",
                    'region_list' => $grouped_region_list
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => "No City/ Corporation found.",
                ]);
            }
        }
        die;
    }


    // public function get_sub_region()
    // {
    //     if ($this->input->post()) {
    //         $regions = $this->input->post("regions");
    //         $area_id = $this->input->post("areaId");
    //         $sub_region_list = $this->region_model->get_area_sub_region($regions, $area_id);
    //         if (count($sub_region_list) > 0) {
    //             echo json_encode([
    //                 'success' => true,
    //                 'message' => "Successfully fetched the sub-region list.",
    //                 'sub_region_list' => $sub_region_list
    //             ]);
    //         } else {
    //             echo json_encode([
    //                 'success' => false,
    //                 'message' => "No sub-region found.",
    //             ]);
    //         }
    //     }
    //     die;
    // }
}

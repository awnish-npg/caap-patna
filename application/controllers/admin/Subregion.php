<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subregion extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('subregion_model');
        $this->load->model('region_model');
    }

    /* List all subregion */
    public function index()
    {

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('subregion');
        }

        $data['title']                = _l('subregion');
        $area_id = $this->region_model->get_area($this->session->userdata('staff_user_id'));
        $data['region'] = $this->subregion_model->get_region($area_id);
        $this->load->view('admin/subregion/manage', $data);
    }

    /* Edit or add new subregion */
    public function subregion($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();

            $if_subregion_exist = [];
            $area_id = $this->region_model->get_area($this->session->userdata('staff_user_id'));
            if (empty($data['region_id'])) {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'City/ Corporation is disabled.',
                ]);
            }else if (trim($data['region_name']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid Municipal Zone name.',
                ]);
            } else if (!$this->input->post('id')) {

                $if_subregion_exist = $this->subregion_model->get_subregion(['region_name' => trim($data['region_name']), 'region_id' => $data['region_id'], 'area_id' => $area_id]);

                if ($if_subregion_exist) {

                    echo json_encode([
                        'success'              => false,
                        'message'              => 'Municipal Zone already in use.',
                    ]);
                } else {
                    $id = $this->subregion_model->add($data);
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('subregion'));
                    }
                    echo json_encode([
                        'success'              => $success,
                        'message'              => $message,
                    ]);
                }
            } else {

                $id = $data['id'];

                $subregion = $this->subregion_model->get_subregion(['id' => $data['id']]);

                if ($subregion->region_name != trim($data['region_name'])) {
                    $if_subregion_exist = $this->subregion_model->get_subregion(['region_name' => trim($data['region_name']), 'region_id' => $data['region_id'], 'area_id' => $area_id]);
                }

                if ($if_subregion_exist) {
                    echo json_encode([
                        'success'              => false,
                        'message'              => 'Municipal Zone already in use.',
                    ]);
                } else {
                    unset($data['id']);

                    $success = $this->subregion_model->update($data, $id);
                    if ($success) {
                        $message = _l('updated_successfully', _l('subregion'));
                    }
                    echo json_encode([
                        'success'              => $success,
                        'message'              => $message,
                    ]);
                }
            }
            die;
        }
    }

    /* Delete subregion from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('area'));
        }
        $response = $this->subregion_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('subregion')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('subregion')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('subregion')));
        }
        redirect(admin_url('region'));
    }

    public function get_region()
    {
        $data             = $this->input->post();

        $region = $this->subregion_model->get_region($data['area']);

        echo json_encode($region);
    }

    public function get_subregion()
    {
        $data             = $this->input->post();

        $subregion = $this->subregion_model->get_subregion_by_id($data['region_id']);
        echo json_encode($subregion);
    }

    /* Change status to sub-region active or inactive / ajax */
    // public function change_subregion_status($id, $status)
    // {
    //     if ($this->input->is_ajax_request()) {
    //         $this->subregion_model->change_subregion_status($id, $status);
    //     }
    // }

    public function change_subregion_status()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $id = $data['id'];
            $status = $data['status'];

                $update_status = $this->subregion_model->change_subregion_status($id, $status);
                if ($update_status) {
                    $response = [
                        'success' => true,
                        'message' => "Status has been updated successfully.",
                        'check_status' => (int)$status
                    ];

                    $staff_ids = $this->staff_model->get_region_staff(["sub_region" => $id]);
                    if (count($staff_ids) > 0) {
                        // Deactivate staff users
                        $this->staff_model->deactivate_staff($staff_ids, $status);
                        $projectids = array();
                        $projectids = $this->projects_model->getFrozenProjects(['subregion_id' => $id], $status);
                        if(!empty($projectids)){
                            $this->projects_model->updateFrozenStaus($projectids,$status);
                        }

                        // Set respective project to frozen
                        $this->projects_model->set_project_frozen(['subregion_id' => $id], $status);
                    }
                }else {
                    $response = [
                        'success' => false,
                        'message' => "City/ Corporation is disabled.",
                
                    ];
                }
                echo json_encode($response);
                die;
        }
    }
}

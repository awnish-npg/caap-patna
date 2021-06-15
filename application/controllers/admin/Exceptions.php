<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Exceptions extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('exception_model');

        if (!has_permission('area', '', 'view')) {
            access_denied('Exception');
        }
    }

    /* List all area */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('exception');
        }

        $data['title']                = 'Exceptions';
        $this->load->view('admin/exception/manage', $data);
    }

    /* Edit or add new area */
    public function save($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
           
        
                $if_exception_exist = [];
                if(trim($data['name']) == '')
                {
                    echo json_encode([
                        'success'              => false,
                        'message'              => 'Invalid exception name.',
                    ]);
                }
                else if(trim($data['name']) == 'Others' || trim($data['name']) == 'Other' || trim($data['name']) == 'OTHERS' || trim($data['name']) == 'OTHER')
                {
                    echo json_encode([
                        'success'              => false,
                        'message'              => 'Others exception already in use.',
                    ]);
                }
                else if (!$this->input->post('id')) {

                    $if_exception_exist = $this->exception_model->get_exception(['name' => trim($data['name'])]);

                    if (count($if_exception_exist) > 0) {

                    echo json_encode([
                        'success'              => false,
                        'message'              => 'Exception name already in use.',
                    ]);
                  
                    }else{
                        $id = $this->exception_model->add($data);
                        if ($id) {
                            $success = true;
                            $message = _l('added_successfully', _l('exception'));
                        }
                        echo json_encode([
                            'success'              => $success,
                            'message'              => $message,
                        ]);
                    }

                  
                } else {
                    $id = $data['id'];
                  
                    $exception = $this->exception_model->get_exception(['id' => $data['id']])[0];

                    if ($exception['name'] != trim($data['name'])) {
                        $if_exception_exist = $this->exception_model->get_exception(['name' => trim($data['name'])]);
                    }

                    if (count($if_exception_exist) > 0) {
                        echo json_encode([
                            'success'              => false,
                            'message'              => 'Exception name already in use.',
                        ]);
                    }else{
                        unset($data['id']);
                        $success = $this->exception_model->update($data, $id);
                        if ($success) {
                            $message = _l('updated_successfully', _l('exception'));
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

    /* Delete area from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('exceptions'));
        }
        $response = $this->exception_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('exception')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('exception')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('exception')));
        }
        redirect(admin_url('exceptions'));
    }


    /* Change status to area active or inactive / ajax */
    public function change_exception_status()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $id = $data['id'];
            $status = $data['status'];

            if(!$status){
                $update_status = $this->exception_model->change_exception_status($id, $status);
                if($update_status){
                    $response = [
                        'success' => true,
                        'message' => "Status has been updated successfully.",
                        'check_status' => 0
                    ];
                }
                echo json_encode($response); die;
            }
           
            $update_status = $this->exception_model->change_exception_status($id, $status);

            $response = [
                'success' => true,
                'message' => "Status has been updated successfully.",
                'check_status' => 1
            ];
            echo json_encode($response);
        }       
    }


}

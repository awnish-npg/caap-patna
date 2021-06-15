<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Issues_manage extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('issue_model');
        $this->load->model("area_model");
        $this->load->library('form_validation');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {

            $this->app->get_table_data('issue_manage_table');
        }
        $data['title'] = 'Action Items';
        $area = $this->area_model->get_area(["areaid" => $GLOBALS["current_user"]->area]);
        if (!empty($area)) {
            $data['area_prefix'] = $area[0]["prefix"] ? $area[0]["prefix"] : "";
        }
        $this->load->view('admin/issues/issue_manage', $data);
    }
}

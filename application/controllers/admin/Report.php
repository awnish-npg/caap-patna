<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('issue_model');
        $this->load->model('projects_model');
        $this->load->model('staff_model');
        $this->load->model('dashboard_model');
    }


    public function index()
    {

        // echo '<pre>'; print_r($GLOBALS['current_user']); exit;
        $post_data = $this->input->post();
       // pre($post_data);
        $get_area = $this->input->get('area') ? base64_decode($this->input->get('area')) : "";
        $get_status = $this->input->get('status') ? base64_decode($this->input->get('status')) : "";
        $get_city = $this->input->get('city') ? base64_decode($this->input->get('city')) : "";
        $get_zone = $this->input->get('zone') ? base64_decode($this->input->get('zone')) : "";
        $role = $GLOBALS['current_user']->role;
        $role_slug =  $GLOBALS['current_user']->role_slug_url;
        $slug_url = $this->input->get('role');
        if ($role_slug == "ap-sa") {
            access_denied('Report');
            exit;
        }

        //pre($post_data['ticket']);

        $area = $GLOBALS['current_user']->area;
        $tableView = "report_managment_ae";
        $siteTitle = ["ae-area" => "Action Taken Report - " . @$GLOBALS['current_user']->location['area'] . " State Observer", "ar" => "Action Taken Report - Reviewer", "at" => "Action Taken Report - Project Leader", "ata" => "Action Taken Report - Project Support", "aa" => "Action Taken Report - State Admin"];

        
        $areaid = (!empty($post_data['areaid'])) ? $post_data['areaid'] : '';
        $regionid = (!empty($post_data['region'])) ? $post_data['region'] : '';

        if ($role_slug == 'ae-global') {
            $data['title']         = 'Action Taken Report - National Observer';
            $data['action_taker'] = $this->report_model->get_action_taker();
            $data['action_reviewer'] = $this->report_model->get_action_reviewer();
            $data['areas'] = $this->report_model->get_area(); 
            $data['region'] = $this->report_model->get_region('',$areaid);
            $data['subregion'] = $this->report_model->get_subregion('',$regionid);
      
        } else {
            $data['title']         = $siteTitle[$role_slug];
            $data['action_taker'] = $this->report_model->get_action_taker($area);
            $data['action_reviewer'] = $this->report_model->get_action_reviewer($area);
            //if($role == 7 || $role == 3 || $role == 8){
            //    $data['region'] = $this->report_model->get_ae_region();
            //    $data['subregion'] = $this->report_model->get_ae_subregion();
            //}else{
            
            $data['region'] = $this->report_model->get_region($area);
            $data['subregion'] = $this->report_model->get_subregion($area,$regionid);
            
            //}
        }
        $data['role_slug']    = $role_slug;
        // if ($role == 4 || $role == 6 || $role == 7) {
        //     $data['ticket'] = $this->report_model->get_ar_ticket();
        // } else {
        $data['ticket'] = $this->report_model->get_ticket();
        // }


        $region = [];
        $subregion = [];
        foreach ($data['region'] as $var) {
            $region[] = $var['id'];
        }
        foreach ($data['subregion'] as $var) {
            $subregion[] = $var['id'];
        }



        // if ($this->input->is_ajax_request()) {
        //print_r($post_data); exit;
        // $tableParams = [
        //     'role' =>  $role,
        //     'area' =>  $area,
        //     'areaid' =>  (!empty($post_data['area'])) ? $post_data['area'] : '',
        //     'region' =>  (!empty($post_data['region'])) ? $post_data['region'] : $region,
        //     'subregion' =>   (!empty($post_data['subregion'])) ? $post_data['subregion'] : $subregion,
        //     'category' =>   (!empty($post_data['category'])) ? $post_data['category'] : '',
        //     'bug' =>    (!empty($post_data['bug'])) ? $post_data['bug'] : '',
        //     'action_taker' =>    (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
        //     'action_reviewer' =>    (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
        //     'report_date' =>    (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
        //     'to_date' =>    (!empty($post_data['report-to'])) ? $post_data['report-to'] : date('Y-m-d'),
        //     'from_date' =>    (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
        //     'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",

        // ];
        $cats = [];
        if (!empty($post_data['category'])) {
            if (!empty($post_data['category'][0])) {

                if (strpos($post_data['category'][0], ",") != false) {
                    $cats = explode(",", $post_data['category'][0]);
                } else {
                    $cats = $post_data['category'];
                }
            } else {
                $cats = [];
            }
        }

        $range = [];
        if (!empty($post_data['duration'])) {
            if (!empty($post_data['duration'][0])) {
                if (strpos($post_data['duration'][0], ",") != false) {
                    $range = explode(",", $post_data['duration'][0]);
                } else {
                    $range = $post_data['duration'];
                }
            } else {
                $range = [];
            }
        }
        // pre($range);

        $tableParams = [
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => (!empty($cats)) ? $cats : '',
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
            'duration' => (!empty($range)) ? $range : '',
        ];

        // pre($tableParams);

        if ($get_area != "") {
            $get_area_name = $this->report_model->get_area_name([$get_area]);
            if ($get_area_name)
                $tableParams['areaid'] = ['areaid' => $get_area, "name" => $get_area_name];
        }

        if ($get_status != "") {
            $get_project_status = $this->report_model->get_status(['id' => $get_status]);
            // pre($get_project_status);
            if ($get_project_status)
                $tableParams['statusIds'] = ['id' => $get_status, "name" => $get_project_status[0]['name']];
        }
        if ($get_city != "") {
            $get_city_data = $this->report_model->get_region($get_area, '', $get_city)[0];
            if ($get_city_data)
                $tableParams['region'] = $get_city_data;
        }

        if ($get_zone != "") {
            $get_zone_data = $this->report_model->get_subregion($get_area, $get_city, $get_zone)[0];
            if ($get_zone_data)
                $tableParams['subregion'] = $get_zone_data;
        }
        // pre($areas);

        //}
        // pre([$get_area, $get_status, $tableParams['statusIds']]);

        if ($this->input->post()) {
            $data['filter'] = json_encode($post_data);
        }else{
            $data['filter'] = 0;
        }
        $data['projects'] = $this->report_model->get_report($tableParams);
       // pre($data['projects']);
        $data['statuses'] = $this->report_model->get_report_summary($tableParams,1);
        $data['totals'] = $this->report_model->get_report_summary($tableParams);
        // $data['totals'] = $this->report_model->get_report_total($tableParams);
        $data['tableParams'] = $tableParams;
        $categories = $this->issue_model->get_area_issues($area);
        $data['categories'] = (!empty($categories)) ? $categories : [];
       
        $data["durations"] = [
            ["id" => "30", "duration" => "< 1 month"],
            ["id" => "31 - 183", "duration" => "1 month - 6 months"],
            ["id" => "184 - 365", "duration" => "6 months - 1 year"],
            ["id" => "366 - 1095", "duration" => "1 year - 3 years"],
            ["id" => "1096 - 1825", "duration" => "3 years - 5 years"],
            ["id" => "1826", "duration" => "> 5 years"],
        ];
        // $data['total'] = 4;

        // pre($data);
        $this->load->view('admin/report/index', $data);
    }

    public function create_pdf()
    {
        $post_data = $this->input->get();
        //pre($post_data['category'][0]);
        $category = '';
        $duration = '';
        if(!empty($post_data['dashboard']) && $post_data['dashboard'] == 1){
            if(!empty($post_data['category'][0])){
                $category = explode(",",$post_data['category'][0]);
            }
            if(!empty($post_data['duration'][0])){
                $duration = explode(",",$post_data['duration'][0]);
            }

        }else{
            $category = (!empty($post_data['category'])) ? $post_data['category'] : '';
            $duration = (!empty($post_data['duration'])) ? $post_data['duration'] : '';
        }
        $tableParams = [
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => $category,
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
            'duration' => $duration,
        ];
        $data['report_date'] = (!empty($post_data['report_months'])) ? $post_data['report_months'] : '';
        $data['location'] = $this->report_model->get_user_location();
        $data['html'] =  $this->get_filter_table($post_data);
        $data['statuses'] = $this->report_model->get_report_summary($tableParams, 1);
        $data['totals'] = $this->report_model->get_report_summary($tableParams);
        $this->load->library('pdf');
        $html = $this->load->view('admin/report/report_summary', $data, true);
        $filename = 'ATR-' . date('dmyhis');
        $this->pdf->createPDF($html, $filename);
        //  die;
    }

    public function get_region()
    {
        if ($this->input->post()) {
            $area = $GLOBALS['current_user']->area;
            $areaids = $this->input->post('area_id');
            $role = $this->input->post('role');
            if ($role == 5) {
                $data['region'] = $this->report_model->get_region($area = '', $areaids);
            } else {
                $data['region'] = $this->report_model->get_region($area, $areaids);
            }

            if (count($data['region']) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Successfully fetched the region list.",
                    'data' => $data['region']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => "No region found.",
                ]);
            }
        }
        die;
    }

    public function get_subregion()
    {
        if ($this->input->post()) {
            $area = $GLOBALS['current_user']->area;
            $regionds = $this->input->post('region_id');
            $role = $this->input->post('role');
            if ($role == 5) {
                $data['subregion'] = $this->report_model->get_subregion($area = '', $regionds);
            } else {
                $data['subregion'] = $this->report_model->get_subregion($area, $regionds);
            }

            if (count($data['subregion']) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Successfully fetched the sub-region list.",
                    'data' => $data['subregion']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => "No sub-region found.",
                ]);
            }
        }
        die;
    }

    public function send_email()
    {
        $role = $GLOBALS['current_user']->role;
        $area = $GLOBALS['current_user']->area;
        if ($this->input->is_ajax_request()) {

            $post_data = $this->input->get();

            //$filename = $post_data['filename'];
            //$html =  $this->get_filter($post_data);
            $html =  $this->get_filter_table($post_data);

            $url = base_url('admin/report/download_export?') . $_SERVER['QUERY_STRING'];

            $send = send_mail_template('report_download_taker', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid, $url, $html);

            // if ($role == 5) {
            //     $send = send_mail_template('report_download', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid, $url, $html['bug'], $html['area'], $html['region'], $html['subregion'], $html['category'], $html['ticket_status'], $html['duration'], $html['at'], $html['ar']);
            // } else {
            //     $send = send_mail_template('report_download_area', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid, $url, $html['bug'], '', $html['region'], $html['subregion'], $html['category'], $html['ticket_status'], $html['duration'], $html['at'], $html['ar']);
            // }

            // $export = $this->export($filename, $post_data);
            echo "1";
        }
    }

    public function show_filter()
    {

        $role = $GLOBALS['current_user']->role;
        $area = $GLOBALS['current_user']->area;
        if ($this->input->is_ajax_request()) {

            $post_data = $this->input->get();
            $tableParams = [
                'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
                'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
                'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
                'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
                'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
                'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
                'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
                'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
                'to_date' => (!empty($post_data['report_to'])) ? $post_data['report_to'] : '',
                'from_date' => (!empty($post_data['report_from'])) ? $post_data['report_from'] : '',
                'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
                'duration' => (!empty($post_data['duration'])) ? $post_data['duration'] : '',
            ];

            $filename = (!empty($post_data['filename'])) ? $post_data['filename'] : "";
            $this->report_model->save_report($tableParams, $filename);

            send_mail_template('report_download_confirmation', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid);

            echo "1";
        }
    }

    public function get_filter($post_data)
    {
        $role = $GLOBALS['current_user']->role;
        $tableParams = [
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report_to'])) ? $post_data['report_to'] : '',
            'from_date' => (!empty($post_data['report_from'])) ? $post_data['report_from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
            'duration' => (!empty($post_data['duration'])) ? $post_data['duration'] : '',
        ];

        $html = array();
        if ($role == 5) {
            if ($tableParams['areaid']) {
                $area_name = $this->report_model->get_area_name($tableParams['areaid']);
                $html['area'] = $area_name;
            } else {
                $html['area'] = "All Areas";
            }
        }

        //if( $role == 5 || $role == 7 || $role == 6){ 
        if (!empty($post_data['region'])) {
            $region_name = $this->report_model->get_region_name($post_data['region']);
            $html['region'] = $region_name;
        } else {
            $html['region'] = "All Regions";
        }
        //}

        //if( $role == 5 || $role == 7 || $role == 6 || $role == 4){ 
        if (!empty($post_data['subregion'])) {
            $subregion_name = $this->report_model->get_subregion_name($post_data['subregion']);
            $html['subregion'] = $subregion_name;
        } else {
            $html['subregion'] = "All Sub-regions";
        }
        //}


        if ($tableParams['category']) {
            $category = $this->report_model->get_category_name($tableParams['category']);
            $html['category'] =  $category;
        } else {
            $html['category'] = "All Action Items";
        }


        //if( $role == 5 || $role == 7 || $role == 4 || $role == 6){ 
        if ($tableParams['action_taker']) {
            $action_taker = $this->report_model->get_staff_name($tableParams['action_taker']);
            $html['at'] = $action_taker;
        } else {
            $html['at'] = "All Action Takers";
        }
        //}

        //if( $role == 5 || $role == 7 || $role == 6){ 
        if ($tableParams['action_reviewer']) {
            $action_reviewer = $this->report_model->get_staff_name($tableParams['action_reviewer']);
            $html['ar'] = $action_reviewer;
        } else {
            $html['ar'] = "All Action Reviewers";
        }
        //}


        if ($tableParams['statusIds']) {
            $status = $this->report_model->get_status_name($tableParams['statusIds']);
            $html['ticket_status'] = $status;
        } else {
            $html['ticket_status'] = "All Ticket Status except CLOSED ";
        }

        if ($tableParams['bug']) {
            $html['bug'] = $tableParams['bug'];
        } else {
            $html['bug'] = " All Project Ids";
        }

        if ($tableParams['report_date'] && $tableParams['report_date'] != 'custom') {


            if ($tableParams['report_date'] == 'this_month') {
                $time =  date('Y-m-01') . " - " . date('Y-m-t');
            } else if ($tableParams['report_date'] == 'last_month') {
                $time =  date('Y-m-01', strtotime("-1 MONTH")) . " - " . date('Y-m-t', strtotime("-1 MONTH"));
            } else if ($tableParams['report_date'] == 'this_year') {
                $time =  date('Y-01-01') . " - " . date('Y-12-31');
            } else if ($tableParams['report_date'] == 'last_year') {
                $time =  date('Y-01-01', strtotime("-1 YEAR")) . " - " . date('Y-12-31', strtotime("-1 YEAR"));
            } else if ($tableParams['report_date'] == '3') {
                $time =  date('Y-m-01', strtotime("-2 MONTH")) . " - " . date('Y-m-t');
            } else if ($tableParams['report_date'] == '6') {
                $time =  date('Y-m-01', strtotime("-5 MONTH")) . " - " . date('Y-m-t');
            } else if ($tableParams['report_date'] == '12') {
                $time =  date('Y-m-01', strtotime("-11 MONTH")) . " - " . date('Y-m-t');
            }

            $html['duration'] = $time;
        } else if (!empty($tableParams['report_date']) && $tableParams['report_date'] == 'custom' && !empty($tableParams['to_date']) && !empty($tableParams['from_date'])) {
            $html['duration'] =  $tableParams['from_date'] . " - " . $tableParams['to_date'];
            $html['duration'] = $time;
        } else {
            $html['duration'] = "All Time";
        }

        return $html;
    }

    public function get_filter_table($post_data)
    {
        $role = $GLOBALS['current_user']->role;
        $category = '';
        $duration = '';
        if(!empty($post_data['dashboard']) && $post_data['dashboard'] == 1){
            if(!empty($post_data['category'][0])){
                $category = explode(",",$post_data['category'][0]);
            }
            if(!empty($post_data['duration'][0])){
                $duration = explode(",",$post_data['duration'][0]);
            }

        }else{
            $category = (!empty($post_data['category'])) ? $post_data['category'] : '';
            $duration = (!empty($post_data['duration'])) ? $post_data['duration'] : '';
        }
        $tableParams = [
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => $category,
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
            'duration' => $duration,
        ];

        $html = array();
        if ($role == 5) {
            if ($tableParams['areaid']) {
                $area_name = $this->report_model->get_area_name($tableParams['areaid']);
                $html['area'] = $area_name;
            } else {
                $html['area'] = "";
            }
        }

        if (!empty($post_data['duration'])) {
            $duration_name = $this->report_model->get_duration_name($post_data['duration']);
            $html['duration_name'] = $duration_name;
        } else {
            $html['duration_name'] = "";
        }


        if (!empty($post_data['region'])) {
            $region_name = $this->report_model->get_region_name($post_data['region']);
            $html['region'] = $region_name;
        } else {
            $html['region'] = "";
        }


        if (!empty($post_data['subregion'])) {
            $subregion_name = $this->report_model->get_subregion_name($post_data['subregion']);
            $html['subregion'] = $subregion_name;
        } else {
            $html['subregion'] = "";
        }

        if ($tableParams['category']) {
            $category = $this->report_model->get_category_name($tableParams['category']);
            $html['category'] =  $category;
        } else {
            $html['category'] = "";
        }


        if ($tableParams['action_taker']) {
            $action_taker = $this->report_model->get_staff_name($tableParams['action_taker']);
            $html['at'] = $action_taker;
        } else {
            $html['at'] = "";
        }

        if ($tableParams['action_reviewer']) {
            $action_reviewer = $this->report_model->get_staff_name($tableParams['action_reviewer']);
            $html['ar'] = $action_reviewer;
        } else {
            $html['ar'] = "";
        }


        if ($tableParams['statusIds']) {
            $status = $this->report_model->get_status_name($tableParams['statusIds']);
            $html['ticket_status'] = $status;
        } else {
            $html['ticket_status'] = "";
        }

        if ($tableParams['bug']) {
            $html['bug'] = $tableParams['bug'];
        } else {
            $html['bug'] = "";
        }

        if ($tableParams['report_date'] && $tableParams['report_date'] != 'custom') {

            $time ='';
            if ($tableParams['report_date'] == 'this_month') {
                $time =  "This Month (" . date('01-m-Y') . " - " . date('t-m-Y').")";
            } else if ($tableParams['report_date'] == 'last_month') {
                $time =  "Last Month (" .date('01-m-Y', strtotime("-1 MONTH")) . " - " . date('t-m-Y', strtotime("-1 MONTH")).")";
            } else if ($tableParams['report_date'] == 'this_year') {
                $time =  "This Year (" .date('01-01-Y') . " - " . date('31-12-Y').")";
            } else if ($tableParams['report_date'] == 'last_year') {
                $time =  "Last Year (" .date('01-01-Y', strtotime("-1 YEAR")) . " - " . date('31-12-Y', strtotime("-1 YEAR")).")";
            } else if ($tableParams['report_date'] == '3') {
                $time =  "Last 3 Months (" .date('01-m-Y', strtotime("-2 MONTH")) . " - " . date('t-m-Y').")";
            } else if ($tableParams['report_date'] == '6') {
                $time =  "Last 6 Months (" .date('01-m-Y', strtotime("-5 MONTH")) . " - " . date('t-m-Y').")";
            } else if ($tableParams['report_date'] == '12') {
                $time =  "Last 12 Months (" .date('01-m-Y', strtotime("-11 MONTH")) . " - " . date('t-m-Y').")";
            }

            $html['duration'] = $time;
        } else if (!empty($tableParams['report_date']) && $tableParams['report_date'] == 'custom' && !empty($tableParams['to_date']) && !empty($tableParams['from_date'])) {
            //$html['duration'] =  $tableParams['from_date'] . " - " . $tableParams['to_date'];
            $html['duration'] =  "Custom Period (" .$tableParams['from_date'] . " - " . $tableParams['to_date'].")";
            //$html['duration'] = $time;
        } else {
            $html['duration'] = "";
        }

        if (empty($html['area']) && empty($html['region']) && empty($html['subregion']) && empty($html['at']) && empty($html['ar'])  && empty($html['category']) && empty($html['ticket_status']) && empty($html['duration_name']) && empty($html['bug']) && empty($html['duration'])) {
            $table = 'This report contains projects across <b>all values</b> as filtered.';
        } else {
            $table = 'This report contains projects across the following values as filtered:<br/><br/>';
            $table .= '<table border="1" ><thead>
            <tr><th>Field</th><th>Values</th></tr> </thead><tbody>';
            if ($role == 5 && !empty($html['area'])) {
                $table .= '<tr><td>State</td><td>' . $html['area'] . '</td></tr>';
            }
            if (!empty($html['region'])) {
                $table .= '<tr><td>Cities/ Corporation</td><td>' . $html['region'] . '</td></tr>';
            }
            if (!empty($html['subregion'])) {
                $table .= '<tr><td>Municipal Zone</td><td>' . $html['subregion'] . '</td></tr>';
            }
            if (!empty($html['at'])) {
                $table .= '<tr><td>Project Leader</td><td>' . $html['at'] . '</td></tr>';
            }
            if (!empty($html['ar'])) {
                $table .= '<tr><td>Reviewer</td><td>' . $html['ar'] . '</td></tr>';
            }
            if (!empty($html['duration_name'])) {
                $table .= '<tr><td>Duration</td><td>' . $html['duration_name'] . '</td></tr>';
            }
            if (!empty($html['category'])) {
                $table .= '<tr><td>Action Items</td><td>' . $html['category'] . '</td></tr>';
            }
            if (!empty($html['ticket_status'])) {
                $table .= '<tr><td>Project Status</td><td>' . $html['ticket_status'] . '</td></tr>';
            }
            if (!empty($html['bug'])) {
                $table .= '<tr><td>Project ID</td><td>' . $html['bug'] . '</td></tr>';
            }
            if (!empty($html['duration'])) {
                $table .= '<tr><td>Date Range</td><td>' . $html['duration'] . '</td></tr>';
            }
            $table .= ' </tbody></table>';
        }

        return $table;
    }

    public function download_export()
    {

        $role = $_SESSION['staff_role'];
        $area = $GLOBALS['current_user']->area;
        $post_data = $this->input->get();

        $category = '';
        $duration = '';
        if(!empty($post_data['dashboard']) && $post_data['dashboard'] == 1){
            if(!empty($post_data['category'][0])){
                $category = explode(",",$post_data['category'][0]);
            }
            if(!empty($post_data['duration'][0])){
                $duration = explode(",",$post_data['duration'][0]);
            }

        }else{
            $category = (!empty($post_data['category'])) ? $post_data['category'] : '';
            $duration = (!empty($post_data['duration'])) ? $post_data['duration'] : '';
        }

        $tableParams = [
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => $category,
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
            'duration' => $duration ,
        ];

        $data = $this->report_model->get_report($tableParams);

        if (count($data) > 0) {

            $filename = 'ATR-' . date('dmyhis') . ".csv";
            $content = array();
            $title = array("Project ID", "Action Items", "Status", "Assigned To", "Contact", "Due Date", "Municipal Zone", "Landmark", "City/ Corporation", "State", "Raised On", "Raised Comment", "Raised Evidence", "Raised Location", "Latest Comment", "Latest Evidence", "Latest Location", "Role", "Email ID", "Project Leader","Reviewer","Milestone", "Closed On", "Raised By", "Name", "Contact ", "Email ID");

            //header("Content-Type: application/xls");
            //header("Content-Type: application/vnd.ms-excel");

            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");

            foreach ($data as $rs) {

                $ticketDetails = $this->report_model->get_project_details($rs['id']);
                // $assignedUser = !empty($ticketDetails->assigned_user_id) ? $ticketDetails->assigned_user_id : '';
                $assignedUser = !empty(getProjectAssignedUser($rs['id'])) ?  getProjectAssignedUser($rs['id']) : '';
                $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                $tasks = $this->projects_model->get_task_details($rs['id']);
                $milestone = $this->report_model->get_current_milestone($rs['id']);
                $milestone = $milestone[0];
                $evidence = $this->report_model->get_evidence($rs['id']);
                $location = $this->report_model->get_location($rs['id']);
                //$latest = $this->report_model->get_current_milestone( $rs['id'],4);
                //$latest = $latest[0];

                if(in_array($rs['status'], [2, 4, 6])){
                    $resolvedMilestone = $this->report_model->get_current_milestone($rs['id'],4);
                    $milestone['task_id'] = !empty($resolvedMilestone[0]['task_id']) ? $resolvedMilestone[0]['task_id'] : (!empty($milestone['task_id'])?$milestone['task_id']:'');
                }

                $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
                $latestImages = array();
                $latest_image = '';
                $latest_location = '';
                $resolved_evidence = '';
                $resolved_location = '';
                if(!empty($taskId)){
                    $latestImages = $this->dashboard_model->get_evidence_image($rs['id'], $taskId);
                    if(!empty($latestImages[0]['file_name']) && in_array($rs['status'], [ 2, 4, 6, 3]) ){
                        $resolved_evidence = base_url('uploads/tasks/' . $taskId . '/') . $latestImages[0]['file_name'];

                        // $resolved_evidence =  '<a href="'. $latest_image .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View </a>';
                    
                        $resolved_location = 'NA';
                        if(!empty($latestImages[0]['latitude']) && $latestImages[0]['latitude'] != 0 && !empty($latestImages[0]['longitude']) &&  $latestImages[0]['longitude'] != 0){
                            $resolved_location = 'https://maps.google.com/maps?q=' . $latestImages[0]['latitude'] . ',' . $latestImages[0]['longitude'] . '"';

                            // $resolved_location = '<a href="'. $latest_location .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>';
                        }
                    }
                }



                $projectNotes = project_latest_notes($rs['id']);
                $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
                // if(!empty($projectNote_content)){
                // if(!empty($projectNote_content) && (in_array( $rs['status'], [ 2, 4, 6, 3,5]) || !empty($rs['sub_ticket_id']) )){
                if(!empty($projectNote_content) && $rs['frozen'] == 0 && (in_array( $rs['status'], [3,5]) || !empty($rs['sub_ticket_id']) || is_project_reopened($rs['id']))){
                    $resolved_evidence = !empty($resolved_evidence)?$resolved_evidence:'NA';
                    $resolved_location = !empty($resolved_location)?$resolved_location:'NA';
                }

                $raised_name = "";
                $raised_email = "";
                $raised_phone = "";
                $user_type = "";
                if (!empty($rs['user_type']) && $rs['user_type'] == 'Call-Center') {
                    $user_type = "Call Center Executive";
                    $raised_name = (!empty($rs['rname'])) ?  $rs['rname'] : '';
                    $raised_email = (!empty($rs['remail'])) ?  $rs['remail'] : '';
                    $raised_phone = (!empty($rs['rphonenumber'])) ?  $rs['rphonenumber'] : '';
                } else if (!empty($rs['user_type']) && $rs['user_type'] == 'Surveyor') {
                    $user_type = "Surveyor";
                    $raised_name = (!empty($rs['firstname'])) ?  $rs['firstname'] : '';
                    $raised_email = (!empty($rs['email'])) ?  $rs['email'] : '';
                    $raised_phone = (!empty($rs['phonenumber'])) ?  $rs['phonenumber'] : '';
                }

                 // project leader
                 $leader_name = '';
                 $leadername = $this->report_model->get_report_leader($rs['id']);
                 if(!empty($leadername)){
                     $leader_name = $leadername->firstname;
                 }

                 // reviewer
                 $reviewer_name = '';
                 if(!empty($leadername)){
                    $reviewername = $this->staff_model->get_reporting_person($leadername->staff_id);
                    if(!empty($reviewername)){
                        $reviewer_name = $reviewername->firstname;
                    }
                }


                $status = (!empty($ticketDetails->status_name)) ?  $ticketDetails->status_name : '';

                $status_tag = '';
                $statusname = '';

                if ($rs['status'] == 9 && $rs['is_assigned'] == 0 && $rs['frozen'] == 0) {
                    $status_tag = 'Unassigned';
                } else  if (in_array($rs['status'], [2, 4, 6])  && $rs['action_date'] >= date('Y-m-d') && $rs['frozen'] == 0) {
                    $status_tag = 'In Progress';
                } else if ((in_array($rs['status'], [2, 4, 6]) && $rs['action_date'] < date('Y-m-d') && $rs['frozen'] == 0) || ($rs['status'] == 1 && $rs['action_date'] <= date('Y-m-d') && $rs['frozen'] == 0)) {
                    $status_tag = 'Delayed';
                } else if ($rs['status'] == 5 && $rs['frozen'] == 0) {
                    $status_tag = 'Rejected';
                } else if ($rs['status'] == 1 && $rs['frozen'] == 0) {
                    $status_tag = 'New';
                } else if ($rs['status'] == 3 && $rs['frozen'] == 0) {
                    $status_tag = 'Closed';
                } else if ($rs['frozen'] == 1) {
                    $status_tag = 'Frozen';
                }


                if (!empty($status_tag)) {

                    if ($status_tag == "Delayed" && $ticketDetails->project_status == 1) {
                        $statusname = 'Unaccepted';
                    } else if ($status_tag == "Delayed" &&  ($ticketDetails->project_status == 2 ||  $ticketDetails->project_status == 6 || $ticketDetails->project_status == 4)) {
                        $statusname = 'Overdue';
                    } else if ($status_tag == 'In Progress' && $ticketDetails->project_status == '2') {
                        $statusname = '';
                    } else if ($status_tag == 'In Progress'  && $rs['reassigned'] == 1) {
                        $statusname = 'In Progress';
                    } else if ($status_tag == 'In Progress' && $ticketDetails->project_status == '4') {
                        $statusname = '';
                    } else if ($status_tag == 'In Progress' && $ticketDetails->project_status == '5') {
                        $statusname = '';
                    } else if ($status_tag == 'In Progress' && $ticketDetails->project_status == '6') {
                        $statusname = 'Reopened';
                    }
                }

                if (!empty($statusname)) {
                    $status_name = $status_tag . "-" . $statusname;
                } else {
                    $status_name = $status_tag;
                }

                //print_r($location); exit;

                $row = array();

                if (!empty($rs['id'])) $row[] = stripslashes($rs['id']);
                else  $row[] = "";
                if (!empty($rs['name'])) $row[] = stripslashes($rs['name']);
                else $row[] = "";
                if (!empty($status_name)) $row[] = $status_name;
                else $row[] = "";
                if (!empty($assignedUserDetails->firstname)) $row[] = $assignedUserDetails->firstname . " " . $assignedUserDetails->organisation;
                else $row[] = "";
                if (!empty($assignedUserDetails->phonenumber)) $row[] = $assignedUserDetails->phonenumber;
                else $row[] = "";
                if (!empty($rs['deadline'])) $row[] = stripslashes($rs['deadline']);
                else  $row[] = "";
                if (!empty($ticketDetails->sub_region_name)) $row[] = $ticketDetails->sub_region_name;
                else $row[] = "";
                if (!empty($rs['landmark'])) $row[] = stripslashes($rs['landmark']);
                else $row[] = "";
                if (!empty($ticketDetails->region_name)) $row[] = $ticketDetails->region_name;
                else $row[] = "";
                if (!empty($ticketDetails->area_name)) $row[] = $ticketDetails->area_name;
                else $row[] = "";
                if (!empty($rs['project_created'])) $row[] = stripslashes($rs['project_created']);
                else  $row[] = "";
                if (!empty($rs['description'])) $row[] = stripslashes($rs['description']);
                else $row[] = "";
                if (!empty($evidence[0])) $row[] = stripslashes($evidence[0]);
                else $row[] = "NA";
                if (!empty($location[0])) $row[] = stripslashes($location[0]);
                else $row[] = "NA";
                if (!empty($projectNote_content)) $row[] = stripslashes($projectNote_content);
                else $row[] = "";

                if (!empty($resolved_evidence)) $row[] = stripslashes($resolved_evidence);
                else $row[] = "";
                if (!empty($resolved_location)) $row[] = stripslashes($resolved_location);
                else $row[] = "";

                // if (!empty($latest_image)) $row[] = stripslashes($latest_image);
                // else $row[] = "NA";
                // if (!empty($latest_location)) $row[] = stripslashes($latest_location);
                // else $row[] = "NA";

                if (!empty($assignedUserDetails->role_name)) $row[] = $assignedUserDetails->role_name;
                else $row[] = "";
                if (!empty($assignedUserDetails->email)) $row[] = $assignedUserDetails->email;
                else $row[] = "";

                if (!empty($leader_name)) $row[] = $leader_name;
                else $row[] = "";
                if (!empty($reviewer_name)) $row[] = $reviewer_name;
                else $row[] = "";
                // if (!empty($milestone['tag'])) $row[] = $milestone['tag'];
                // else $row[] = "";
                // if (!empty($milestone['task_name'])) $row[] = $milestone['task_name'];
                // else $row[] = "NA";

                if(!empty($milestone['task_name']) && in_array($rs['status'],[2,4,6]) && $rs['frozen'] == 0){
                    // $milestone_name = $milestone['task_name'];
                    $row[] = $milestone['task_name'];
                }else if($rs['status'] == 3){
                    $row[] = 'NA';
                }else{
                    $row[] = '';
                }

                if (!empty($rs['date_finished'] && $rs['date_finished'] != '0000-00-00 00:00:00')) $row[] = stripslashes(date("d-m-Y", strtotime($rs['date_finished'])));
                else  $row[] = "";
                if (!empty($user_type)) $row[] = stripslashes($user_type);
                else $row[] = "";
                if (!empty($raised_name)) $row[] = stripslashes($raised_name);
                else $row[] = "";
                if (!empty($raised_phone)) $row[] = stripslashes($raised_phone);
                else $row[] = "";
                if (!empty($raised_email)) $row[] = stripslashes($raised_email);
                else $row[] = "";


                $content[] = $row;
            }


            $output = fopen('php://output', 'w');
            fputcsv($output, $title);
            foreach ($content as $con) {
                fputcsv($output, $con);
            }


            exit;
        } else {
            echo 'No data found';
            exit;
        }
    }


    public function export($filename, $post_data)
    {

        $path = FCPATH . 'uploads/report/';
        $role = $_SESSION['staff_role'];
        $area = $GLOBALS['current_user']->area;

        $tableParams = [
            'role' => (!empty($post_data['role'])) ? $post_data['role'] : '',
            'area' => (!empty($post_data['area'])) ? $post_data['area'] : '',
            'areaid' => (!empty($post_data['areaid'])) ? $post_data['areaid'] : '',
            'region' => (!empty($post_data['region'])) ? $post_data['region'] : '',
            'subregion' => (!empty($post_data['subregion'])) ? $post_data['subregion'] : '',
            'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
            'bug' => (!empty($post_data['bug'])) ? $post_data['bug'] : '',
            'action_taker' => (!empty($post_data['action_taker'])) ? $post_data['action_taker'] : '',
            'action_reviewer' => (!empty($post_data['action_reviewer'])) ? $post_data['action_reviewer'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report_to'])) ? $post_data['report_to'] : date('Y-m-d'),
            'from_date' => (!empty($post_data['report_from'])) ? $post_data['report_from'] : '',
            'statusIds' => (!empty($post_data['ticket'])) ? $post_data['ticket'] : "",
        ];


        $data = $this->report_model->get_report($tableParams);

        if (count($data) > 0) {


            $content = array();
            $title = array("Project ID", "Action Items", "Status", "Assigned To", "Contact", "Due Date", "Municipal Zone", "Landmark", "City", "State", "Raised On", "Comment(s)", "Evidence", "Role", "Email ID", "Type", "Milestone", "Completed On", "Raised By", "Name", "Contact ", "Email ID");

            foreach ($data as $rs) {

                $ticketDetails = $this->report_model->get_project_details($rs['id']);
                $assignedUser = !empty($ticketDetails->assigned_user_id) ? $ticketDetails->assigned_user_id : '';
                $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                $tasks = $this->projects_model->get_task_details($rs['id']);
                $milestone = $this->report_model->get_current_milestone($rs['id']);
                $milestone = $milestone[0];
                $evidence = $this->report_model->get_evidence($rs['id']);
                //print_r($evidence); exit;

                $row = array();

                if (!empty($rs['id'])) $row[] = stripslashes($rs['id']);
                else  $row[] = "";
                if (!empty($rs['issue_name'])) $row[] = stripslashes($rs['issue_name']);
                else $row[] = "";
                if (!empty($ticketDetails->status_name)) $row[] = $ticketDetails->status_name;
                else $row[] = "";
                if (!empty($assignedUserDetails->firstname)) $row[] = $assignedUserDetails->firstname . " " . $assignedUserDetails->organisation;
                else $row[] = "";
                if (!empty($assignedUserDetails->phonenumber)) $row[] = $assignedUserDetails->phonenumber;
                else $row[] = "";
                if (!empty($rs['deadline'])) $row[] = stripslashes($rs['deadline']);
                else  $row[] = "";
                if (!empty($ticketDetails->sub_region_name)) $row[] = $ticketDetails->sub_region_name;
                else $row[] = "";
                if (!empty($rs['landmark'])) $row[] = stripslashes($rs['landmark']);
                else $row[] = "";
                if (!empty($ticketDetails->region_name)) $row[] = $ticketDetails->region_name;
                else $row[] = "";
                if (!empty($ticketDetails->area_name)) $row[] = $ticketDetails->area_name;
                else $row[] = "";
                if (!empty($rs['project_created'])) $row[] = stripslashes($rs['project_created']);
                else  $row[] = "";
                if (!empty($rs['description'])) $row[] = stripslashes($rs['description']);
                else $row[] = "";
                if (!empty($evidence)) $row[] = stripslashes($evidence);
                else $row[] = "";
                if (!empty($assignedUserDetails->role_name)) $row[] = $assignedUserDetails->role_name;
                else $row[] = "";
                if (!empty($assignedUserDetails->email)) $row[] = $assignedUserDetails->email;
                else $row[] = "";
                if (!empty($milestone['tag'])) $row[] = $milestone['tag'];
                else $row[] = "";
                if (!empty($milestone['task_name'])) $row[] = $milestone['task_name'];
                else $row[] = "";
                if (!empty($rs['date_finished'])) $row[] = stripslashes($rs['date_finished']);
                else  $row[] = "";
                if (!empty($rs['user_type'])) $row[] = stripslashes($rs['user_type']);
                else $row[] = "";
                if (!empty($rs['firstname'])) $row[] = stripslashes($rs['firstname']);
                else $row[] = "";
                if (!empty($rs['phonenumber'])) $row[] = stripslashes($rs['phonenumber']);
                else $row[] = "";
                if (!empty($rs['email'])) $row[] = stripslashes($rs['email']);
                else $row[] = "";

                $content[] = $row;
            }

            $output = fopen($path . $filename, 'w');

            fputcsv($output, $title);
            foreach ($content as $con) {
                fputcsv($output, $con);
            }
            fclose($output);
            //header("Content-Type: application/xls");
            //header("Content-Type: application/vnd.ms-excel");
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=$filename");
            header("Pragma: no-cache");
            header("Expires: 0");


            readfile($path . $filename);

            $html =  $this->get_filter($post_data);

            $url = base_url() . 'uploads/report/' . $filename;

            if ($role == 5) {
                $send = send_mail_template('report_download', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid, $url, $html['bug'], $html['area'], $html['region'], $html['subregion'], $html['category'], $html['ticket_status'], $html['duration'], $html['at'], $html['ar']);
            } else {
                $send = send_mail_template('report_download_area', $GLOBALS['current_user']->email, $GLOBALS['current_user']->staffid, $url, $html['bug'], '', $html['region'], $html['subregion'], $html['category'], $html['ticket_status'], $html['duration'], $html['at'], $html['ar']);
            }
            // if($role == 6 || $role == 7){
            //     $send=send_mail_template('report_download_area',$GLOBALS['current_user']->email,$GLOBALS['current_user']->staffid,$url,$html['bug'],'',$html['region'],$html['subregion'],$html['category'],$html['ticket_status'],$html['duration'],$html['at'],$html['ar']);
            // }
            // if($role == 4){
            //     $send=send_mail_template('report_download_reviewer',$GLOBALS['current_user']->email,$GLOBALS['current_user']->staffid,$url,$html['bug'],'','',$html['subregion'],$html['category'],$html['ticket_status'],$html['duration'],$html['at'],'');
            // }
            // if($role == 3 || $role == 8){
            //     $send=send_mail_template('Report_download_taker',$GLOBALS['current_user']->email,$GLOBALS['current_user']->staffid,$url,$html['bug'],'','','',$html['category'],$html['ticket_status'],$html['duration']);
            // }

            return true;
        } else {
            echo '';
            return false;
        }


        return true;
    }


    /* List all region */
    public function region()
    {
        $role = $_SESSION['staff_role'];

        if ($this->input->is_ajax_request()) {

            $aColumns = [
                db_prefix() . 'area.name',
                db_prefix() . 'region.region_name',
                db_prefix() . 'sub_region.region_name',

            ];
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'sub_region';
            $where        = ['AND ' . db_prefix() . 'sub_region.status = 1  AND ' . db_prefix() . 'region.status = 1'];
            $join         = ['LEFT JOIN ' . db_prefix() . 'area ON ' . db_prefix() . 'area.areaid = ' . db_prefix() . 'sub_region.area_id LEFT JOIN ' . db_prefix() . 'region ON ' . db_prefix() . 'region.id = ' . db_prefix() . 'sub_region.region_id'];

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,  $join, $where, [db_prefix() . 'sub_region.id', db_prefix() . 'sub_region.area_id', db_prefix() . 'sub_region.region_id']);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title']                = 'States, Cities/Corporation and Municipal Zones list';
        $this->load->view('admin/report/region', $data);
    }

    public function demo_report()
    {
        $this->load->view('admin/report/demo');
    }
}

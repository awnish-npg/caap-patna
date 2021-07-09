
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('projects_model');
        $this->load->model('issue_model');
        $this->load->model("area_model");
        $this->load->model("report_model");
        $this->load->helper('date');
        //$this->load->model('staff_model');
    }

    /* This is admin dashboard view */
    public function index()
    {
        close_setup_menu();
        if ($this->input->get("area")) {
            $area = base64_decode($this->input->get("area"));
            $data = $this->prepare_summary_data($area);
            $this->load->view('admin/dashboard/gm_sub_dashboard', $data);
            // $this->load_sub_dashboard($area);
            return;
        }
        // $data['statuses'] = $this->projects_model->get_project_status();

        $data['dashboard'] = true;
        $data['userRole'] = $userRole = $GLOBALS['current_user']->role_slug_url;

        $data['user_dashboard_visibility'] = get_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility');

        if (!$data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = [];
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);

        if (in_array($GLOBALS['current_user']->role_slug_url, ['at', 'ata'])) {
            $data['action_items_cnt'] = $this->dashboard_model->get_action_item_counts();
            $data['upcoming_deadline_data_cnt'] = $this->dashboard_model->next_week_deadline_count();
        }
        $data['userDetails'] = $this->staff_model->get_userDetails(get_staff_user_id());

        if ($userRole == 'at') {
            $userId = $GLOBALS['current_user']->staffid;
            //Get Assistant Details
            $data['assistantDetails'] = $this->staff_model->get_staff_assistance($userId);
            //Get Rejection List
            $this->load->model('exception_model');
            $data['exceptionDetails'] = $this->exception_model->get_exception_list();
        }

        if (in_array($userRole, ['ar'])) {
            $data['action_items_cnt'] = $this->dashboard_model->total_ar_action_items();
            $data['recently_closed'] = $this->dashboard_model->recently_ar_closed_tickets();
            $data['recently_closed_cnt'] = $this->dashboard_model->total_ar_closed_tickets();
        }

        if ($userRole == 'ae-global') {
            $data = $this->prepare_summary_data();
        }

        $dashboard_widget_data = $this->dashboard_model->get_dashboard_widget_data();
        $data['escalated'] = $escalated = !empty($dashboard_widget_data->escalated) ? $dashboard_widget_data->escalated : 0;
        $data['new'] = $new = !empty($dashboard_widget_data->new) ? $dashboard_widget_data->new : 0;
        $data['ongoing'] = $ongoing = !empty($dashboard_widget_data->ongoing) ? $dashboard_widget_data->ongoing : 0;
        $data['closed'] = $closed = !empty($dashboard_widget_data->closed) ? $dashboard_widget_data->closed : 0;
        $data['rejected'] = $rejected = !empty($dashboard_widget_data->rejected) ? $dashboard_widget_data->rejected : 0;
        $data['total_activity'] = $escalated + $new + $ongoing + $closed + $rejected;



        $data = hooks()->apply_filters('before_dashboard_render', $data);
        if ($userRole == 'aa') {
            $this->load->view('admin/dashboard/checklist');
        } elseif ($userRole == 'ap-sa') {
            redirect(admin_url('area'));
        } else if ($userRole == 'ar') {
            $this->load->view('admin/dashboard/ar_dashboard', $data);
        } else if ($userRole == "ae-area") {
            $data = $this->prepare_summary_data($GLOBALS['current_user']->area);
            //pre($this->input->post());
            $this->load->view('admin/dashboard/gm_sub_dashboard', $data);
        } else if ($userRole == 'ae-global') {
           
            $data['filter_data'] = $this->get_filter_duration_name($this->input->post('report_months'));
    
            
            $this->load->view('admin/dashboard/gm_dashboard', $data);
        } else
            $this->load->view('admin/dashboard/dashboard', $data);
    }

    public function get_filter_duration_name($duration_name){
        if(isset($duration_name)){
            switch($duration_name){
                case 'custom': return 'Custom'; break;
                case 'this_month': return  'This Month'; break;
                case 'last_month': return  'Last Month'; break;
                case 'this_year': return  'This Year';   break;
                case 'last_year': return  'Last Year'; break;
                case '3': return  'Last 3 Months'; break;
                case '6': return  'Last 6 Months'; break;
                case '12': return  'Last 12 Months'; break;
                default: return 'Currently';

            }
        }else{
            return 'Currently';
        }
    }

    public function prepare_summary_data($area = null)
    {
        if ($this->input->post())
            $post_data = $this->input->post();
        else if ($this->input->get())
            $post_data = $this->input->get();

        if ($area == null) {
            $area = $GLOBALS['current_user']->area;
        }
        $data["durations"] = [
            ["id" => "30", "duration" => "< 1 month"],
            ["id" => "31 - 183", "duration" => "1 month - 6 months"],
            ["id" => "184 - 365", "duration" => "6 months - 1 year"],
            ["id" => "366 - 1095", "duration" => "1 year - 3 years"],
            ["id" => "1096 - 1825", "duration" => "3 years - 5 years"],
            ["id" => "1826", "duration" => "> 5 years"],
        ];
        $data['categories'] = $this->issue_model->get_area_issues($area);
        $tableParams = [
            'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
            'duration' => (!empty($post_data['duration'])) ? $post_data['duration'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
        ];
        if ($area != null) {
            $tableParams['area'] = $area;
            $tableParams["area_name"] = $this->area_model->get_area(["areaid" => $area]);
            $tableParams["area_name"] = !empty($tableParams["area_name"]) ? $tableParams["area_name"][0]["name"] : "";
        }
        $data['tableParams'] = $tableParams;
        $data['statuses'] = $this->dashboard_model->get_ae_global($tableParams);

        return $data;
    }

    public function load_sub_dashboard($area = null, $export = false)
    {
        $response = [
            "success" => false,
            "message" => "No Data Found!",
            "data" => []
        ];

        if ($area == null)
            $area = $this->input->post("area");
        $data = $this->prepare_summary_data($area);

        // get statuses on the basis of area
        if (!empty($data['statuses'])) {
            $this->load->model("region_model");
            $regions = $this->region_model->get_region(['area_id' => $area]);
            $region_summary = [];
            foreach ($regions as $region) {
                $data['tableParams']["region_list"] = $region["id"];
                $region_data = $this->dashboard_model->get_ae_global($data['tableParams']);
                if (!empty($region_data))
                    array_push($region_summary, $region_data[0]);
            }
            // get statuses on the basis of regions
            $data['statuses'] = $region_summary;

            $this->load->model("subregion_model");
            $sub_regions = [];
            foreach ($data['statuses'] as $key => $status) {
                $data['statuses'][$key]["sub_region_status"] = [];
                // get sub-regions on the basis of region id
                $sub_region = $this->subregion_model->get_subregion_collection(["region_id" => $status["region_id"]]);
                $data['tableParams']["region_list"] = [];
                foreach ($sub_region as $region) {
                    array_push($sub_regions, $region["id"]);
                    $data['tableParams']["sub_region_list"] = $region["id"];
                    $sub_region_data = $this->dashboard_model->get_ae_global($data['tableParams']);
                    if (!empty($sub_region_data))
                        array_push($data['statuses'][$key]["sub_region_status"], $sub_region_data[0]);
                }
            }
        }
        $filter_data = $this->get_filter_duration_name($this->input->post('report_months'));
    
            
        if (!empty($data)) {
            $response = [
                "success" => true,
                "message" => "Data Fetched Successfully",
                "data" => $data['statuses'],
                "filter_data" => $filter_data

            ];
        }
        if ($export)
            return $data;
        else {

            echo json_encode($response);
            die;
        }
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($currency));
            die();
        }
    }

    public function welcome()
    {
        $this->load->view("admin/dashboard/checklist");
    }

    public function action_items()
    {
        if ($this->input->is_ajax_request()) {
            $userRole = $GLOBALS['current_user']->role_slug_url;
            $userId = $GLOBALS['current_user']->staffid;
            $userName = $GLOBALS['current_user']->full_name;

            $action_items_cnt = 0;
            if (in_array($userRole, ['ar'])) {
                $action_items_cnt = $this->dashboard_model->total_ar_action_items();
            } else {
                $action_items_cnt = $this->dashboard_model->get_action_item_counts();
            }

            if ($action_items_cnt > 0) {
                $action_items = array();
                if (in_array($userRole, ['ar'])) {
                    $action_items = $this->dashboard_model->get_ar_action_items();
                } else {
                    $action_items = $this->dashboard_model->get_action_items();
                }

                $actionList = '';
                $roles = array('at');
                $this->load->model('projects_model');
                $this->load->model('staff_model');

                foreach ($action_items as $key => $item) {
                    $project_name = !empty($item['project_name']) ? $item['project_name'] : '';
                    $landmark = !empty($item['landmark']) ? $item['landmark'] : '';
                    $reassigned = !empty($item['reassigned']) ? $item['reassigned'] : '';
                    $description = !empty($item['description']) ? $item['description'] : '';
                    $statusId = !empty($item['status']) ? $item['status'] : '';
                    $statusColor = !empty($item['color']) ? $item['color'] : '';
                    $statusBgColor = !empty($item['bg-color']) ? $item['bg-color'] : '';
                    $status_name = !empty($item['status_name']) ? $item['status_name'] : '';
                    $deadline = !empty($item['deadline']) ? setDateFormat($item['deadline']) : '';
                    $frozen = !empty($item['frozen']) ? $item['frozen'] : '';
                    $action_date = !empty($item['action_date']) ? setDateFormat($item['action_date']) : '';
                    // $start_date = !empty($item['start_date']) ? date('Y-m-d', strtotime($item['start_date'])) : '';
                    // $project_total_days = !empty($item['deadline']) ? date('d-M-Y', strtotime($item['deadline'])) : '';
                    $projectId =  $item['project_id'];

                    $assignedUser = getProjectAssignedUser($projectId);
                    if ($userRole == 'ata') {
                        $statusId = !empty($item['status_id']) ? $item['status_id'] : '';
                    }

                    $action = '';
                    if ($statusId == 1) {
                        if (in_array($userRole, $roles) && ($assignedUser == get_staff_user_id()) && empty($frozen)) {
                            $action = '<div class="btn-container">
                                            <a href="javascript:void(0)" class="btn accept-btn accept-btn-popup" data-projectid="' . $projectId . '">Accept</a>
                                        </div>
                                        <div class="btn-container">
                                            <a href="javascript:void(0)" class="btn reject-btn reject-btn-popup" data-projectid="' . $projectId . '">'.
                                             ($GLOBALS['current_user']->role_slug_url =='at'? 'Refer':'Reject')
                                            .'</a>
                                        </div>';
                        } else {
                            $action = 'New Project';
                        }
                        if ($reassigned) {
                            $status_name = 'Re-assigned';
                        }
                        if (in_array($userRole, ['at', 'ar']) && $item['action_date'] <= date('Y-m-d')) {
                            // $status_name = 'Delayed'; //Unaccepted
                            // $statusColor = "#fc0332"; //red
                            // $statusBgColor = "#ffd6de";
                        }
                    } else if ($statusId == 2 || $statusId == 6) {
                        if ($userRole != 'ata') {
                            // $ticketDetails = $this->projects_model->get_project_details($projectId);
                            // $assignedUser = !empty($ticketDetails->assigned_user_id) ? $ticketDetails->assigned_user_id : '';
                            $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                            $name = !empty($assignedUserDetails->full_name) ? $assignedUserDetails->full_name : '';
                            $phoneNumber = !empty($assignedUserDetails->phonenumber) ? $assignedUserDetails->phonenumber : '';
                            $organisation = !empty($assignedUserDetails->organisation) ? $assignedUserDetails->organisation : '';

                            if ($assignedUser == get_staff_user_id()) {
                                $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Close Project</p>';
                            } else {
                                $action = '<div class="ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                                <p class="action-label">Call - 
                                                    <span class="ata-name">' . $name . '</span>
                                                </p>
                                                <p class="action-label">Phone No - 
                                                    <span class="ata-name">' . $phoneNumber . '</span>
                                                </p>
                                            </div>';
                            }
                        } else {
                            $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Review and Resolve</p>';
                        }
                    } else if ($statusId == 3) {
                        $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Closed</p>';
                    } else if ($statusId == 4) {
                        $status_name = 'In Progress';
                        $statusColor = "#fc0332";
                        $statusBgColor = "#ffd6de";
                        $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Review</p>';
                    } else if ($statusId == 5) {
                        if ($userRole == 'ar') {
                            $projectNotes = project_latest_notes($projectId, $statusId);
                            $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
                            $task_id = !empty($projectNotes->task_id) ? $projectNotes->task_id : '';
                            $projectNote_status = !empty($projectNotes->status) ? $projectNotes->status : '';
                            $exception = (!empty($projectNotes->exception) && $projectNotes->exception != 6) ? getExceptionName($projectNotes->exception) : $projectNote_content;
                            $action = '<div class="ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p class="action-label">Rejected</p>
                                            <p class="ata-name">' . $projectNote_content . '</p>
                                        </div>';
                        } else {
                            $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Rejected</p>';
                        }
                    } else if ($statusId == 6) {
                        //  $action = '<p class="ata-name font14 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">Close</p>';
                    }

                    if (in_array($userRole, ['ar']) && !in_array($statusId, [3, 5])) {
                        $assignedAT = $this->projects_model->get_project_at($projectId);
                        $assignedUserDetails = $this->staff_model->get_userDetails($assignedAT);
                        $name = !empty($assignedUserDetails->full_name) ? $assignedUserDetails->full_name : '';
                        $phoneNumber = !empty($assignedUserDetails->phonenumber) ? $assignedUserDetails->phonenumber : '';
                        $action = '<div class="ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p class="action-label">Call - 
                                            <span class="ata-name">' . $name . '</span>
                                        </p>
                                        <p class="action-label">Phone No - 
                                            <span class="ata-name">' . $phoneNumber . '</span>
                                        </p>
                                    </div>';
                    }

                    // $dueStatus = '';
                    // if (!in_array($statusId, [1, 3, 5])) {
                    //     // $status_name = 'WIP';
                    //     // $statusColor = "#e43b05";
                    //     // $statusBgColor = "#ffe0d6";
                    //     $dueDaysLeft = $this->projects_model->calculate_project_days_left($projectId);

                    //     $dueStatus = 'Due in '.$dueDaysLeft.' days';
                    // }

                    if (!in_array($statusId, [3, 5]) && $item['action_date'] < date('Y-m-d')) {
                        $status_name = 'Delayed';
                        $statusColor = "#fc0332"; //red
                        $statusBgColor = "#ffd6de";
                    }

                    $actionList .= '<div class="dashboard-row delayed project_' . $projectId . '">
                                        <div class="dashboard-cell w270 action-item ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p>
                                                <strong data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($project_name) . '">' . mb_strimwidth($project_name, 0, 30, '...')  . '</strong>
                                                <span>near</span>
                                                <span class="landmark" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($landmark) . '">' . mb_strimwidth($landmark, 0, 30, '...')  . '</span>
                                            </p>
                                        </div>
                                        <div class="dashboard-cell w170 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p class="deadline">' . $action_date . '</p>
                                            <span class="status status_' . $projectId . '" style="background-color:' . $statusBgColor . '; color:' . $statusColor . ';">' . $status_name . '</span>
                                        </div>
                                        <div class="dashboard-cell w270 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p data-toggle="tooltip" data-placement="top" title="' . strip_tags($description) . '">' . mb_strimwidth($description, 0, 100, '...') . '</p>
                                        </div>
                                        <div class="dashboard-cell evidence w170">
                                            <p class="evidence_img report-location" data-img_type="original" data-project_id="' . $projectId . '">
                                            <i class="fa fa-eye" aria-hidden="true"></i><span>View</span></p>
                                        </div>';

                    if (in_array($userRole, ['at', 'ar'])) {
                        $actionList .= '<div class="dashboard-cell action-cell w20P action_' . $projectId . '">' . $action . '</div>';
                    }

                    $actionList .= '</div><div class="row-saperator"></div>';
                }

                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'action_items_cnt' => $action_items_cnt
                ]);
            } else {
                $actionList = '';
                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'action_items_cnt' => $action_items_cnt
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }

    public function upcoming_deadline()
    {
        if ($this->input->is_ajax_request()) {
            $upcoming_deadline_data_cnt = $this->dashboard_model->next_week_deadline_count();
            if ($upcoming_deadline_data_cnt > 0) {
                $upcomingDeadlines = $this->dashboard_model->next_week_deadline();

                $actionList = '';
                $userRole = $GLOBALS['current_user']->role_slug_url;
                $roles = array('at');

                foreach ($upcomingDeadlines as $key => $item) {
                    $project_name = !empty($item['project_name']) ? $item['project_name'] : '';
                    $landmark = !empty($item['landmark']) ? $item['landmark'] : '';
                    $description = !empty($item['description']) ? $item['description'] : '';
                    $statusId = !empty($item['status']) ? $item['status'] : '';
                    $statusColor = !empty($item['color']) ? $item['color'] : '';
                    $statusBgColor = !empty($item['bg-color']) ? $item['bg-color'] : '';
                    $status_name = !empty($item['status_name']) ? $item['status_name'] : '';
                    // $deadline = !empty($item['deadline']) ? date('d-M-Y', strtotime($item['deadline'])) : '';
                    // $action_date = !empty($item['action_date']) ? date('d-M-Y', strtotime($item['action_date'])) : '';
                    $deadline = !empty($item['deadline']) ? setDateFormat($item['deadline']) : '';
                    $action_date = !empty($item['action_date']) ? setDateFormat($item['action_date']) : '';
                    $projectId =  $item['project_id'];

                    $action = '';
                    if ($statusId == 2) {
                        if ($userRole != 'ata') {
                            $this->load->model('projects_model');
                            $ticketDetails = $this->projects_model->get_project_details($projectId);
                            $assignedUser = getProjectAssignedUser($projectId);

                            $this->load->model('staff_model');
                            $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                            $name = !empty($assignedUserDetails->full_name) ? $assignedUserDetails->full_name : '';
                            $phoneNumber = !empty($assignedUserDetails->phonenumber) ? $assignedUserDetails->phonenumber : '';
                            $organisation = !empty($assignedUserDetails->organisation) ? $assignedUserDetails->organisation : '';

                            if ($assignedUser == get_staff_user_id()) {
                                $action = '<p class="ata-name font14">Close</p>';
                            } else {
                                $action = '<div class="">
                                                <p class="action-label">Call - 
                                                    <span class="ata-name">' . $name . '</span>
                                                </p>
                                                <p class="action-label">Phone No - 
                                                    <span class="ata-name">' . $phoneNumber . '</span>
                                                </p>
                                            </div>';
                            }
                        } else {
                            $action = '<p class="ata-name font14">Review and Resolve</p>';
                        }
                    } else if ($statusId == 3) {
                        $action = '<p class="ata-name font14">Closed</p>';
                    } else if ($statusId == 4) {
                        $action = '<p class="ata-name font14">Review</p>';
                    } else if ($statusId == 5) {
                        $action = '<p class="ata-name font14">Rejected</p>';
                    } else if ($statusId == 6) {
                        $action = '<p class="ata-name font14">Reopened</p>';
                    }

                    $dueStatus = '';
                    if (in_array($statusId, [2, 4, 6])) {
                        // $dueDaysLeft = $this->projects_model->calculate_project_days_left($projectId);
                        $dueDaysLeft = dateDiffInDays($action_date, date('Y-m-d'));
                        if ($statusId == 4) {
                            //$status_name = $status_name . ' Due in ' . $dueDaysLeft . ' days';
                        } else {
                            $status_name = 'Due in ' . $dueDaysLeft . ' day(s)';
                        }
                    }

                    if ($statusId != 1 && $item['action_date'] < date('Y-m-d')) {
                        $status_name = 'Delayed';
                        $statusColor = "#fc0332"; //red
                        $statusBgColor = "#ffd6de";
                    }

                    $actionList .= '<div class="dashboard-row delayed project_' . $projectId . '">
                                        <div class="dashboard-cell w270 action-item ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p>
                                            <strong data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($project_name) . '">' . mb_strimwidth($project_name, 0, 30, '...')  . '</strong>
                                            <span>near</span>
                                            <span class="landmark" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($landmark) . '">' . mb_strimwidth($landmark, 0, 30, '...')  . '</span>
                                        </p>
                                        </div>
                                        <div class="dashboard-cell w170 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p class="deadline">' . $action_date . '</p>
                                            <span class="status status_' . $projectId . '" style="background-color:' . $statusBgColor . '; color:' . $statusColor . ';">' . $status_name . ' ' . $dueStatus . '</span>
                                        </div>
                                        <div class="dashboard-cell w270 ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                            <p data-toggle="tooltip" data-placement="top" title="' . strip_tags($description) . '">' . mb_strimwidth($description, 0, 100, '...') . '</p>
                                        </div>
                                        <div class="dashboard-cell evidence w170">
                                            <p class="evidence_img report-location" data-img_type="original" data-project_id="' . $projectId . '">
                                            <i class="fa fa-eye" aria-hidden="true"></i><span>View</span></p>
                                        </div>';

                    if (in_array($userRole, ['at', 'ar'])) {
                        $actionList .= '<div class="dashboard-cell action-cell w20P ticket_details  action_' . $projectId . '" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">' . $action . '</div>';
                    }

                    $actionList .= '</div><div class="row-saperator"></div>';
                }

                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'upcoming_deadline_data_cnt' => $upcoming_deadline_data_cnt
                ]);
            } else {
                $actionList = '';
                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'upcoming_deadline_data_cnt' => $upcoming_deadline_data_cnt
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }

    //Dashboard widget data
    public function dashboard_widget_data()
    {
        if ($this->input->is_ajax_request()) {
            $dashboard_widget_data = $this->dashboard_model->get_dashboard_widget_data();
            $escalated = !empty($dashboard_widget_data->escalated) ? $dashboard_widget_data->escalated : 0;
            $new = !empty($dashboard_widget_data->new) ? $dashboard_widget_data->new : 0;
            $ongoing = !empty($dashboard_widget_data->ongoing) ? $dashboard_widget_data->ongoing : 0;
            $closed = !empty($dashboard_widget_data->closed) ? $dashboard_widget_data->closed : 0;
            $rejected = !empty($dashboard_widget_data->rejected) ? $dashboard_widget_data->rejected : 0;
            $total_activity = $escalated + $new + $ongoing + $closed + $rejected;
            $action_items = 0;
            $upcoming_deadline = 0;
            $recently_closed = 0;

            if (in_array($GLOBALS['current_user']->role_slug_url, ['at', 'ata'])) {
                $action_items = $this->dashboard_model->get_action_item_counts();
                $upcoming_deadline = $this->dashboard_model->next_week_deadline_count();
            } else if (in_array($GLOBALS['current_user']->role_slug_url, ['ar'])) {
                $action_items = $this->dashboard_model->total_ar_action_items();
                $recently_closed = $this->dashboard_model->total_ar_closed_tickets();
            }

            $widgetData = array(
                'escalated' => $escalated,
                'new' => $new,
                'ongoing' => $ongoing,
                'closed' => $closed,
                'rejected' => $rejected,
                'total_activity' => $total_activity,
                'action_items' => $action_items,
                'upcoming_deadline' => $upcoming_deadline,
                'recently_closed' => $recently_closed
            );
            echo json_encode($widgetData);
            die;
        }
    }

    public function evidence_image()
    {
        if ($this->input->is_ajax_request()) {
            $data['project_id'] = $projectId = !empty($_GET['projectId']) ? $_GET['projectId'] : '';

            $data['evidenceImages'] = $this->dashboard_model->get_evidence_image($projectId, '', 1); //1=>original Image

            //Resolved Milestone Images
            $this->load->model('report_model');
            $milestone = $this->report_model->get_current_milestone($projectId);
            $milestone = !empty($milestone[0]) ? $milestone[0] : '';

            $projectStatus = $this->projects_model->getProjectStatus($projectId);
            if(in_array($projectStatus, [2, 4, 6])){
                $resolvedMilestone = $this->report_model->get_current_milestone($projectId,4);
                // $milestone['task_id'] = $resolvedMilestone[0]['task_id'];
                $milestone['task_id'] = !empty($resolvedMilestone[0]['task_id']) ? $resolvedMilestone[0]['task_id'] : (!empty($milestone['task_id'])?$milestone['task_id']:'');
            }

            $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
            // $data['latestImages'] = $this->dashboard_model->get_evidence_image($projectId, $taskId, 2);//2=>Latest Image
            $data['latestImages'] = !empty($taskId) ? $this->dashboard_model->get_evidence_image($projectId, $taskId) : ''; //2=>Latest Image

            $this->load->view('admin/tickets/evidence_image', $data);
        }
    }

    public function evidence_location()
    {
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_GET['projectId']) ? $_GET['projectId'] : '';
            $fileId = !empty($_GET['fileId']) ? $_GET['fileId'] : '';
            $evidenceLocData = '';
            $noLocation = '<div class="col-lg-12 mB30">
                                <ul class="detail-list issue-detail-list mB20">
                                    <li class="mT0"><label></label><span>No Location data found.</span></li>
                                </ul>
                            </div>';

            if (!empty($projectId)) {
                $evidenceLocation = $this->dashboard_model->get_evidence_location($projectId, $fileId);

                $evidenceLocData .= '<div class="row original-images">
                                        <h4 class="modal-title">
                                            <span class="add-title">Location</span>
                                        </h4>
                                    <hr class="hr-panel-model">';
                if (!empty($evidenceLocation)) {
                    $latitude = $evidenceLocation->latitude;
                    $longitude = $evidenceLocation->longitude;
                    $address = $evidenceLocation->address;
                    if (!empty($latitude) && !empty($longitude)) {
                        $evidenceLocData .= '<div class="col-lg-12 mB30">
                                                <ul class="detail-list issue-detail-list mB20">
                                                    <li class="mT0"><label>Location: </label><span>' . $address . '</span></li>
                                                </ul>
                                            <iframe width="100%" height="500" style="border: 1px solid blue;" src="https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '&output=embed"></iframe>
                                        </div>';
                    } else {
                        $evidenceLocData .= $noLocation;
                    }
                } else {
                    $evidenceLocData .= $noLocation;
                }
                $evidenceLocData .= '</div>';
            }

            echo $evidenceLocData;
            die();
        }
    }

    public function recently_closed()
    {
        if ($this->input->is_ajax_request()) {
            $recently_closed_cnt = $this->dashboard_model->total_ar_closed_tickets();

            if ($recently_closed_cnt > 0) {
                $recentlyClosed = $this->dashboard_model->recently_ar_closed_tickets();

                $actionList = '';
                $userRole = $GLOBALS['current_user']->role_slug_url;
                $roles = array('at');

                foreach ($recentlyClosed as $key => $item) {
                    $project_name = !empty($item['project_name']) ? $item['project_name'] : '';
                    $landmark = !empty($item['landmark']) ? $item['landmark'] : '';
                    $description = !empty($item['description']) ? $item['description'] : '';
                    $statusId = !empty($item['status_id']) ? $item['status_id'] : '';
                    $statusColor = !empty($item['color']) ? $item['color'] : '';
                    $status_name = !empty($item['status_name']) ? $item['status_name'] : '';
                    $deadline = !empty($item['deadline']) ? setDateFormat($item['deadline']) : '';
                    $action_date = !empty($item['action_date']) ? setDateFormat($item['action_date']) : '';
                    $date_finished = !empty($item['date_finished']) ? setDateFormat($item['date_finished']) : '';
                    $projectId =  $item['project_id'];
                    $sub_ticket_id = !empty($item['sub_ticket_id']) ? $item['sub_ticket_id'] : '';

                    $assignedUser = getProjectAssignedUser($projectId);

                    $activeUser = true;
                    //check if assigned AT is disable or not
                    $assignedUser = $this->report_model->get_report_leader($projectId);
                    if(empty($assignedUser->staff_status)){
                        $activeUser = false;
                    }

                    // $projectNotes = project_latest_notes($projectId, 4);
                    $projectNotes = project_latest_notes($projectId);
                    $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
                    // $task_id = !empty($projectNotes->task_id) ? $projectNotes->task_id : '';
                    // $projectNote_status = !empty($projectNotes->status) ? $projectNotes->status : '';
                    // $exception = (!empty($projectNotes->exception) && $projectNotes->exception != 6) ? getExceptionName($projectNotes->exception) : $projectNote_content;

                    $actionList .= '<div class="dashboard-row delayed project_' . $projectId . '">
                                    <div class="dashboard-cell w270 action-item ticket_details" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p>
                                            <strong data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($project_name) . '">' . mb_strimwidth($project_name, 0, 30, '...')  . '</strong>
                                            <span>near</span>
                                            <span class="landmark" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . strip_tags($landmark) . '">' . mb_strimwidth($landmark, 0, 30, '...')  . '</span>
                                        </p>
                                    </div>
                                    <div class="dashboard-cell w120 ticket_details  status_' . $projectId . '" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p>' . $date_finished . '</p>
                                    </div>
                                    <div class="dashboard-cell w270 ticket_details  status_' . $projectId . '" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p data-toggle="tooltip" data-placement="top" title="' . strip_tags($description) . '">' . mb_strimwidth($description, 0, 100, '...') . '</p>
                                    </div>
                                    <div class="dashboard-cell w270 ticket_details  status_' . $projectId . '" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">
                                        <p data-toggle="tooltip" data-placement="top" title="' . strip_tags($projectNote_content) . '">' . mb_strimwidth($projectNote_content, 0, 100, '...') . '</p>
                                    </div>
                                    <div class="dashboard-cell evidence w170">
                                        <p class="evidence_img report-location" data-img_type="original" data-project_id="' . $projectId . '">
                                        <i class="fa fa-eye" aria-hidden="true"></i><span>View</span></p>
                                    </div>
                                    <div class="dashboard-cell evidence w20P ticket_details  status_' . $projectId . '" data-project_id="' . $projectId . '" data-role="' . $userRole . '" data-status="' . $statusId . '">';
                    if ($userRole == 'ar' && ($statusId == 3 && (get_staff_user_id() != $assignedUser)) && empty($sub_ticket_id) && $activeUser) {
                        $actionList .= '<a href="javascript:void(0)" class="btn reject-btn" data-projectid="' . $projectId . '">Reopen</a>';
                    } else {
                        $actionList .= '<p class="text-center">NA</p>';
                    }
                    $actionList .= '</div>
                                </div>
                                <div class="row-saperator"></div>';
                }

                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'recently_closed_cnt' => $recently_closed_cnt
                ]);
            } else {
                $actionList = '';
                echo json_encode([
                    'success' => TRUE,
                    'message' => $actionList,
                    'recently_closed_cnt' => $recently_closed_cnt
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }

    public function get_region()
    {
        if ($this->input->post()) {
            $post_data = $this->input->post();
            $tableParams = [
                'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
                'area_id' => (!empty($post_data['area_id'])) ? $post_data['area_id'] : '',
            ];
            $region_list = $this->dashboard_model->get_region($tableParams);

            if (count($region_list) > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => "Successfully fetched the region list.",
                    'region_list' => $region_list
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

    public function get_chart_data($area = null)
    {
        $data = [];
        if ($this->input->post()) {
            $area = $this->input->post('area');
            $data = $this->input->post();
        }
        // $data = $this->dashboard_model->get_chart_data($area, $data);
        $cats = [];
        if (!empty($data['category'])) {
            if (!empty($data['category'][0])) {

                if (strpos($data['category'][0], ",") != false) {
                    $cats = explode(",", $data['category'][0]);
                } else {
                    $cats = $data['category'];
                }
            } else {
                $cats = [];
            }
        }

        $range = [];
        if (!empty($data['duration'])) {
            if (!empty($data['duration'][0])) {
                if (strpos($data['duration'][0], ",") != false) {
                    $range = explode(",", $data['duration'][0]);
                } else {
                    $range = $data['duration'];
                }
            } else {
                $range = [];
            }
        }

        $tableParams = [
            'category' => (!empty($cats)) ? $cats : '',
            'report_date' => (!empty($data['report_months'])) ? $data['report_months'] : '',
            'to_date' => (!empty($data['report_to'])) ? $data['report_to'] : '',
            'from_date' => (!empty($data['report_from'])) ? $data['report_from'] : '',
            'statusIds' => [1, 2, 3, 5, 7, 8, 9],
            'duration' => (!empty($range)) ? $range : '',
        ];
        if ($area) {
            $tableParams['areaid'] = [$area];
        }


        $data = $this->report_model->get_report_summary($tableParams);
        if (!empty($data)) {
            $chart_data = [['Task', 'Project Status']];
            foreach ($data[0] as $key => $value) {
                $value = (int)$value;
                if ($key == "new") {
                    array_push($chart_data, ["New", $value]);
                } else if ($key == "escalated") {
                    array_push($chart_data, ["Delayed", $value]);
                } else if ($key == "close") {
                    array_push($chart_data, ["Closed", $value]);
                } else if ($key == "wip") {
                    array_push($chart_data, ["In Progress", $value]);
                } else if ($key == "reject") {
                    array_push($chart_data, ["Rejected", $value]);
                } else if ($key == "unassign") {
                    array_push($chart_data, ["Unassigned", $value]);
                } else if ($key == "frozen") {
                    array_push($chart_data, ["Frozen", $value]);
                }
            }
            echo json_encode([
                "success" => true,
                "message" => "chart data fetched successfully",
                "data" => $chart_data
            ]);
            die;
        }
        echo json_encode([
            "success" => false,
            "message" => "failed to fetch chart data",
            "data" => []
        ]);
        die;
    }

    public function download_export()
    {
        $data = $this->prepare_export_data();
        $data['report_date'] = (!empty($data['tableParams']['report_date'])) ? $data['tableParams']['report_date'] : '';
        $filename = (empty($data['tableParams']['area_name'])) ? 'Summary of India' : 'Summary of ' . $data['tableParams']['area_name'];
        header('Content-Type: application/xls; charset=binary');
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-disposition: attachment; filename=' . $filename . '.xls');
        header("Content-Transfer-Encoding: binary ");
        $data['filename'] = $filename;

        //$data['html'] = '';
        $html = $this->load->view('admin/dashboard/aeg_projects_pdf_excel', $data, true);
        echo $html;
        die();
        pre($html);
        $title = ["Area Name12", "New", "Escalated", "Closed", "In Progress", "Total"];
        $f = fopen('php://output', 'w');
        fputcsv($f, $title);
        foreach ($data['statuses'] as $key => $line) {
            if ($line["areaid"] != "") {
                unset($line["areaid"]);
            }
        }
    }

    public function download_ae_export()
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="ae_projects_summary.csv";');
        $area = $this->input->get("area");
        $data = $this->load_sub_dashboard($area, true);
        $title = ["Name", "New", "Escalated", "Closed", "In Progress", "Total"];
        $f = fopen('php://output', 'w');
        fputcsv($f, $title);
        $temp_arr = [];
        foreach ($data['statuses'] as $key => $line) {
            unset($line["region_id"]);
            $temp_arr = $line;
            unset($temp_arr["sub_region_status"]);
            fputcsv($f, $temp_arr);
            if (!empty($line["sub_region_status"])) {
                fputcsv($f, ["--------", "--------", "--------", "--------", "--------", "--------"]);
                foreach ($line["sub_region_status"] as $val) {
                    unset($val["sub_region_id"]);
                    fputcsv($f, $val);
                }
                fputcsv($f, ["--------", "--------", "--------", "--------", "--------", "--------"]);
            }
        }
    }

    public function prepare_export_data()
    {
        $post_data = $this->input->get();

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
        $tableParams = [
            'category' => (!empty($cats)) ? $cats : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'statusIds' => [1, 2, 3, 5, 7, 8, 9],
            'duration' => (!empty($range)) ? $range : '',
        ];

        if (!empty($post_data['area'])) {
            $tableParams['areaid'] = [$post_data['area']];
            $data = $this->prepare_summary_data($post_data['area']);
        } else {
            $data = $this->prepare_summary_data();
        }

        $data['html'] =  $this->get_filter_table($post_data);
        $summary = $this->report_model->get_report_summary($tableParams);

        $this->load->model("region_model");
        if (!empty($data['statuses'])) {

            foreach ($data['statuses'] as $key => $area) {
                $data['statuses'][$key]["region_status"] = [];

                $regions = $this->region_model->get_region(["area_id" => $area['areaid']]);

                foreach ($regions as $region) {
                    $tableParams["region_list"] = $region["id"];

                    $region_data = $this->dashboard_model->get_ae_global($tableParams);

                    if (!empty($region_data))

                        array_push($data['statuses'][$key]["region_status"], $region_data[0]);


                    if (!empty($data['statuses'][$key]['region_status'])) {
                        $this->load->model("subregion_model");
                        $sub_regions = [];
                        foreach ($data['statuses'][$key]['region_status'] as $keys => $status) {
                            $data['statuses'][$key]['region_status'][$keys]["sub_region_status"] = [];
                            // get sub-regions on the basis of region id


                            $sub_region = $this->subregion_model->get_subregion_collection(["region_id" => $status["region_id"]]);
                            $tableParams["region_list"] = [];

                            foreach ($sub_region as $region) {
                                array_push($sub_regions, $region["id"]);
                                $tableParams["sub_region_list"] = $region["id"];
                                $sub_region_data = $this->dashboard_model->get_ae_global($tableParams);

                                if (!empty($sub_region_data))
                                    array_push($data['statuses'][$key]['region_status'][$keys]["sub_region_status"], $sub_region_data[0]);
                            }
                            $tableParams["sub_region_list"] = [];
                        }
                    }
                }
            }
        }

        $data['selected_regions'] = "";
        if (!empty($data['statuses']) & !empty($data['statuses'][0]['region_status'])) {
            foreach ($data['statuses'][0]['region_status'] as $index => $selected_region) {
                if ($index == 0)
                    $data['selected_regions'] = $selected_region['region_name'];
                else
                    $data['selected_regions'] = $data['selected_regions'] . ", " . $selected_region['region_name'];
            }
        }

        $data['chart_data'] = [];
        if (!empty($summary)) {
            $data['chart_data'] = $summary[0];
        }

        return $data;
    }

    public function create_aeg_pdf()
    {
        $data = $this->prepare_export_data();
        $data['report_date'] = (!empty($data['tableParams']['report_date'])) ? $data['tableParams']['report_date'] : '';
        $filename = (empty($data['tableParams']['area_name'])) ? 'Summary of India' : 'Summary of ' . $data['tableParams']['area_name'];
        $this->load->library('pdf');
        $data['filename'] = $filename;
        $html = $this->load->view('admin/dashboard/aeg_projects_pdf', $data, true);
        $this->pdf->createPDF($html, $filename);die();
    }

    public function create_ae_pdf()
    {
        $area = $this->input->get('area');
        $data = $this->prepare_summary_data($area);
        $this->load->model('area_model');
        $area_data = $this->area_model->get_area(["areaid" => $area]);
        $data["area_name"] = !empty($area_data) ? $area_data[0]['name'] : "";

        if (!empty($data['statuses'])) {
            $this->load->model("region_model");
            $regions = $this->region_model->get_region(['area_id' => $area]);
            $region_summary = [];
            foreach ($regions as $region) {
                $tableParams["region_list"] = $region["id"];
                $region_data = $this->dashboard_model->get_ae_global($tableParams);
                if (!empty($region_data))
                    array_push($region_summary, $region_data[0]);
            }
            // get statuses on the basis of regions
            if (!empty($region_summary))
                $data['statuses'] = $region_summary;

            if (!empty($data['statuses'])) {

                $this->load->model("subregion_model");
                $sub_regions = [];
                foreach ($data['statuses'] as $key => $status) {
                    $data['statuses'][$key]["sub_region_status"] = [];
                    // get sub-regions on the basis of region id
                    $sub_region = $this->subregion_model->get_subregion_collection(["region_id" => $status["region_id"]]);
                    $tableParams["region_list"] = [];
                    foreach ($sub_region as $region) {
                        array_push($sub_regions, $region["id"]);
                        $tableParams["sub_region_list"] = $region["id"];
                        $sub_region_data = $this->dashboard_model->get_ae_global($tableParams);
                        if (!empty($sub_region_data))
                            array_push($data['statuses'][$key]["sub_region_status"], $sub_region_data[0]);
                    }
                }
            }
        }

        $summary = $this->dashboard_model->get_chart_data($area, $data);
        $data['chart_data'] = [];
        if (!empty($summary)) {
            $total = 0;
            foreach ($summary[0] as $val) {
                $total = $total + $val;
            }
            $summary[0]['total'] = $total;
            $data['chart_data'] = $summary[0];
        }
        $this->load->library('pdf');
        $html = $this->load->view('admin/dashboard/ae_projects_pdf', $data, true);
        $this->pdf->createPDF($html, 'AE - Projects Summary');die();
    }

    public function get_filter_table($post_data)
    {
        $role = $GLOBALS['current_user']->role;
        $tableParams = [

            'category' => (!empty($post_data['category'])) ? $post_data['category'] : '',
            'report_date' => (!empty($post_data['report_months'])) ? $post_data['report_months'] : '',
            'to_date' => (!empty($post_data['report-to'])) ? $post_data['report-to'] : '',
            'from_date' => (!empty($post_data['report-from'])) ? $post_data['report-from'] : '',
            'duration' => (!empty($post_data['duration'])) ? $post_data['duration'] : '',
        ];

        $html = array();


        if (!empty($post_data['duration'])) {
            $duration_name = $this->report_model->get_duration_name($post_data['duration']);
            $html['duration_name'] = $duration_name;
        } else {
            $html['duration_name'] = "";
        }

        if ($tableParams['category']) {
            $category = $this->report_model->get_category_name($tableParams['category']);
            $html['category'] =  $category;
        } else {
            $html['category'] = "";
        }


        if ($tableParams['report_date'] && $tableParams['report_date'] != 'custom') {

            $time = "";
            if ($tableParams['report_date'] == 'this_month') {
                $time =  "This Month (" . date('01-m-Y') . " - " . date('t-m-Y') . ")";
            } else if ($tableParams['report_date'] == 'last_month') {
                $time =  "Last Month (" . date('01-m-Y', strtotime("-1 MONTH")) . " - " . date('t-m-Y', strtotime("-1 MONTH")) . ")";
            } else if ($tableParams['report_date'] == 'this_year') {
                $time =  "This Year (" . date('01-01-Y') . " - " . date('31-12-Y') . ")";
            } else if ($tableParams['report_date'] == 'last_year') {
                $time =  "Last Year (" . date('01-01-Y', strtotime("-1 YEAR")) . " - " . date('31-12-Y', strtotime("-1 YEAR")) . ")";
            } else if ($tableParams['report_date'] == '3') {
                $time =  "Last 3 Months (" . date('01-m-Y', strtotime("-2 MONTH")) . " - " . date('t-m-Y') . ")";
            } else if ($tableParams['report_date'] == '6') {
                $time =  "Last 6 Months (" . date('01-m-Y', strtotime("-5 MONTH")) . " - " . date('t-m-Y') . ")";
            } else if ($tableParams['report_date'] == '12') {
                $time =  "Last 12 Months (" . date('01-m-Y', strtotime("-11 MONTH")) . " - " . date('t-m-Y') . ")";
            }


            $html['duration'] = $time;
        } else if (!empty($tableParams['report_date']) && $tableParams['report_date'] == 'custom' && !empty($tableParams['to_date']) && !empty($tableParams['from_date'])) {
            $html['duration'] =  "Custom Period (" . $tableParams['from_date'] . " - " . $tableParams['to_date'] . ")";
            // $html['duration'] = $time;
        } else {
            $html['duration'] = "";
        }

        if (empty($html['category']) && empty($html['duration_name']) && empty($html['duration'])) {
            $table = 'This report contains projects across <b>all values</b> as filtered.';
        } else {
            $table = 'This report contains projects across the following values as filtered:<br/><br/>';

            $table = '<caption>' . $table . '</caption>';
            $table .= '<table border="1"><thead>
            <tr><th>Field</th><th colspan="4">Values</th></tr> </thead><tbody>';
            if (!empty($html['duration'])) {
                $table .= '<tr><td>Date Range</td><td colspan="4">' . $html['duration'] . '</td></tr>';
            }
            if (!empty($html['duration_name'])) {
                $table .= '<tr><td >Duration</td><td colspan="4">' . $html['duration_name'] . '</td></tr>';
            }
            if (!empty($html['category'])) {
                $table .= '<tr><td>Action Items</td><td colspan="4">' . $html['category'] . '</td></tr>';
            }
            
            $table .= ' </tbody></table>';
        }

        return $table;
    }
}

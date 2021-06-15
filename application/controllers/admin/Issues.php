<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Issues extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('issue_model');
        $this->load->library('form_validation');

    }

    public function index()
    {;
        
        if ($this->input->is_ajax_request()) {
            // $data=$this->app->get_table_data('issues_2');
            $data = $this->app->get_table_data('issues');
            // print_r($data);

        }
        $data['title']                = 'Action Items';
        $this->load->view('admin/issues/manage', $data);
    }
    public function change_issue_status()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $staffid = $this->session->userdata('staff_user_id');
            $staffareaid = get_staff_area_id($staffid);
            $staffroleid = $this->session->userdata('staff_role');
            if ($staffroleid == 2) {
                $msg = $this->issue_model->change_issue_status($id, $status, true, 0);
            } else {
                $msg = $this->issue_model->change_issue_status($id, $status, false, $staffareaid);
            }
            if(!empty($msg)){
                echo json_encode([
                    'success'              => false,
                    'message'              => _l($msg),
    
                ]);
                return true;
            }
            $message = _l('Status Changed successully ', _l('issue'));
            echo json_encode([
                'success'              => true,
                'message'              => $message,

            ]);
        }
    }
    public function manage_issue()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $importstat = $this->issue_model->checkimportstatus($data['id'], $data['staff']);
            if ($importstat) {
                $importdata = [
                    'staff_id' => $data['staff'],
                    'area_id' => getstaffarea($data['staff']),
                    'issue_id' => $data['id'],
                ];
                $response = $this->issue_model->importissue($importdata);
                if ($response) {
                    $message = _l('imported successfully', _l('issue'));
                    echo json_encode([
                        'success'              => true,
                        'message'              => $message,

                    ]);
                }
            } else {
                $message = _l('already imported', _l('issue'));
                echo json_encode([
                    'success'              => false,
                    'message'              => $message,

                ]);
            }
        }
    }

    public function deletemilestone()
    {
        // $staffroleid=$this->session->userdata('staff_role');
        if (has_permission('categories_manage', '', 'edit') or has_permission('categories', '', 'edit')) {
            $id = $this->input->post('id');
            $staffid = $this->session->userdata('staff_user_id');
            $staffareaid = get_staff_area_id($staffid);
            $staffroleid = $this->session->userdata('staff_role');
            if ($staffroleid == 2) {
                $responseofdel = $this->issue_model->deletemilestone($id, true, 0);
            } else {
                $responseofdel = $this->issue_model->deletemilestone($id, false, $staffareaid);
            }
            if ($responseofdel) {
                $message = _l('milestone_removed', _l('issue'));
                echo json_encode([
                    'success'              => true,
                    'message'              => $message,

                ]);
            }
        } else {
            $message = _l('you are not permitted', _l('issue'));
            echo json_encode([
                'success'              => false,
                'message'              => $message,

            ]);
        }
    }

    public function deleteall($id)
    {
        $staffid = $this->session->userdata('staff_user_id');
        $staffareaid = get_staff_area_id($staffid);
        $staffroleid = $this->session->userdata('staff_role');
        if ($staffroleid == 2) {
            $this->issue_model->deleteall($id, true, 0);
        } else {
            $this->issue_model->deleteall($id, true, $staffareaid);
        }
    }

    public function validate_field($data)
    {
        // $this->CI->form_validation->set_message('validate_field', '%s are not unique.');
        $error = array();
        $realdata = explode(',', $data);
        for ($i = 0; $i < count($realdata); $i++) {
            if (empty($realdata[$i])) {
                array_push($error, $i);
            }
        }
        $js = implode(',', $error);
        return $js;
    }
    public function reminder1_check($val)
    {
        $reminder2 = convertint($this->input->post('defaultr2'));
        $duration = convertint($this->input->post('defaultduration'));
        $reminder1 = convertint($val);
        // if($duration<0){
        //     $this->form_validation->set_message("reminder1_check","Duration must be positive");
        //     return false;
        // }
        // if($reminder1>0 and $duration <=10){
        //     $this->form_validation->set_message("reminder1_check","reminder1 must be 0");
        //     return false;
        // }
        if ($reminder1 > 0 and $reminder1 >= $duration) {
            $this->form_validation->set_message("reminder1_check", "Reminder 1 must be less than duration");
            return false;
        }
        // if($reminder1>$reminder2 and $reminder1<$duration and !empty($this->input->post('defaultr2'))){
        //     $this->form_validation->set_message("reminder1_check","reminder1 must be less than $reminder2");
        //     return false;
        // }
        else {
            return true;
        }
    }
    public function reminder2_check($val)
    {
        $reminder1 = convertint($this->input->post('defaultr1'));
        $duration = convertint($this->input->post('defaultduration'));
        $reminder2 = convertint($val);
        // if($duration<0){
        //     $this->form_validation->set_message("reminder2_check","Duration must be positive");
        //     return false;
        // }
        // else
        if ($val == "") {
            $this->form_validation->set_message("reminder2_check", "The Reminder 2 field is required");
            return false;
        } else if ($reminder2 < 0) {
            $this->form_validation->set_message("reminder2_check", "The Reminder 2 field must contain a number greater than -1.");
            return false;
        }
        // else if($reminder2>0 and $duration <=10){
        //     $this->form_validation->set_message("reminder2_check","reminder2 must be 0");
        //     return false;
        // }
        else if ($reminder2 >= $duration && $reminder2 != 0) {
            $this->form_validation->set_message("reminder2_check", "Reminder 2 must be less than duration");
            return false;
        } else if ($reminder1 >= $reminder2 and $reminder1 > 0 and $reminder1 < $duration) {
            // if($reminder1>=$duration or $reminder1<0){
            // $this->form_validation->set_message("reminder2_check","fill Reminder 1 properly");
            // }else{
            $this->form_validation->set_message("reminder2_check", "Reminder 2 must be greater than Reminder 1");
            // }
            return false;
        } else {
            return true;
        }
    }
    public function check_issue($str)
    {
        $issuename = trim($str);
        $staffroleid = $this->session->userdata('staff_role');
        $sacat = $this->check_if_created_by_sa($issuename);
        $aacat = $this->check_if_created_by_aa($issuename);
        $role_slug = $GLOBALS['current_user']->role_slug_url;
        if ($sacat) {
            $this->form_validation->set_message('check_issue', 'This Action Item already exists');
            return false;
        } else if ($aacat['success'] == true) { 
            if ($role_slug  == 'ap-sa') {

                $this->form_validation->set_message('check_issue','already created by area admins');
                
                $stringofarea = "";
                $count = 0;
                $areaid = array();
                foreach ($aacat['data'] as $areas) {
                    $stringofarea = $stringofarea . get_area_name($areas['area_id']).", ";
                    $areaid[$count] = $areas['area_id'];
                    $count++;
                }
                $id = implode(',', $areaid);
                $this->session->set_flashdata('areas', rtrim($stringofarea,", "));
                $this->session->set_flashdata('areaids', $id);
               // $this->form_validation->set_message('check_issue', 'A similar action item already exists in '.rtrim($stringofarea,", ").'. Please contact respective State Admin(s) immediately.');
                // print_r($this->session->flashdata('areas'));
                // print_r($this->session->flashdata('areaids'));
                // die;
                return true;
            } else {
                $this->form_validation->set_message('check_issue', 'Action Item already exists');
                return false;
            }
        } else {
            return true;
        }
    }
    public function check_name($str)
    {
        if (empty($str)) {
            $this->form_validation->set_message('check_name', 'Action Item name is required');
            return false;
        }
        if (preg_match('/^[a-zA-Z\-\s]+$/', trim($str)) == false) {
            $this->form_validation->set_message('check_name', 'Action Item name must contain only alphabets with space');
            return false;
        }
        return true;
    }

    public function validate()
    {
        $staffroleid = $this->session->userdata('staff_role');
        $staffid = $this->session->userdata('staff_user_id');
        $issuename = $this->input->post('issue_name');
        $issuename_reg = $this->input->post('issue_name_reg');
        $slug = get_slug($staffroleid);
        if ($this->input->post('id')) {
            //    echo true;
            //$issuename = $this->input->post('issue_name');
            $issue_id = $this->input->post('id');
            $name = $this->input->post('name');
            if (empty(trim($issuename))) {

                $message = _l('Action Item name  is required', _l('issue'));
                echo json_encode([
                    'success'              => false,
                    'message'              => $message,

                ]);
                die();
            }
            if (strlen(trim($issuename)) > 300) {
                $message = _l('Action Item name length must be less than 300 char', _l('issue'));
                echo json_encode([
                    'success'              => false,
                    'message'              => $message,

                ]);
                die();
            }
            if (strtolower(trim($issuename)) != strtolower(trim($name))) {
               // $slug = get_slug($staffroleid);
                if ($slug == "sa" or $slug == "ap-sa") {
                    $response = $this->issue_model->checkforname(trim($issuename), true, 0);
                } else {
                    $is_createdby_superadmin = $this->issue_model->is_createdby_superadmin($issue_id);
                    if(!$is_createdby_superadmin){
                        $response = $this->issue_model->checkforname(trim($issuename), false, get_staff_area_id($staffid));
                    }else{
                        $response = true;
                    }
                    
                }
                if ($response == false) {
                    $message = _l('This Action Item already exists', _l('issue'));
                    echo json_encode([
                        'success'              => false,
                        'message'              => $message,

                    ]);
                    die();
                }
            }
            if (preg_match('/^[a-zA-Z\-\s]+$/', trim($issuename)) == false) {
                $message = _l('Action Item name must contain only alphabets with space', _l('issue'));
                echo json_encode([
                    'success'              => false,
                    'message'              => $message,

                ]);
                die();
            }
            $milestone = $this->input->post('milestones');
            $durations = $this->input->post('durations');
            $reminder1 = $this->input->post('reminder1');
            $reminder2 = $this->input->post('reminder2');

            //    for($i=0;$i<count($milestone);$i++){
            //        if(trim($milestone[$i]) and trim($durations[$i]) and trim($reminder1[$i]) and trim($reminder2[$i])){
            //         if(convertint($reminder1[$i])<0 or convertint($reminder2[$i]) <0 or convertint($durations[$i])<0 ){
            //             $message = _l('durations and reminders must be a positive value', _l('issue'));

            //             echo json_encode([
            //                 'success'              => false,
            //                 'message'              => $message,

            //             ]);
            //             die();
            //         }
            //         if(convertint($durations[$i])<=convertint($reminder1[$i]) or convertint($durations[$i])<=convertint($reminder2[$i])){
            //                $message = _l('reminders must be less than durations', _l('issue'));
            //                echo json_encode([
            //                    'success'              => false,
            //                    'message'              =>$message,

            //                ]);
            //                die();
            //            }

            //        }
            //     if(count($milestone)>1){
            //     if($i>0){
            //        if(empty(trim($milestone[$i])) or empty(trim($durations[$i])) or empty(trim($reminder1[$i])) or empty(trim($reminder2[$i]))){
            //         $message = _l('fill in all the fields', _l('issue'));
            //         echo json_encode([
            //             'success'              => false,
            //             'message'              =>$message,

            //         ]);
            //         die();
            //        }
            //     }

            //     }
            //     if(count($milestone)==1){
            //         if(empty(trim($milestone[$i])) or empty($durations[$i]) or $reminder1[$i]=="" or $reminder2[$i]==""){
            //             $message = _l('fill in all the fields', _l('issue'));
            //                 echo json_encode([
            //                 'success'              => false,
            //                 'message'              =>$message,

            //             ]);
            //             die();
            //            }
            //     }
            //    }
            echo json_encode([
                'success'              => true,

            ]);
        } else {
            $checkforvalidation = $this->input->post('x');
            // die($duration[0]);
            if ($checkforvalidation > 0) {
                $this->form_validation->set_rules('issue_name', 'Action Item name', 'required|trim|max_length[300]|callback_check_name|callback_check_issue');
            }
            // $this->form_validation->set_rules('reminder_one','reminder one','max_length[4]|trim|required|greater_than[0]|less_than['.$this->input->post('duration').']');
            // $this->form_validation->set_rules('reminder_two','reminder two','max_length[4]|required|greater_than[0]|less_than['.$this->input->post('duration').']');
            // $this->form_validation->set_rules('milestone','milestones','required|trim');
            // $this->form_validation->set_rules('duration','durations','max_length[4]|required|trim|greater_than[0]');
            if ($checkforvalidation == 0) {
                $this->form_validation->set_rules('issue_name', 'Action Item name', 'required|trim|max_length[300]|callback_check_name|callback_check_issue');
                $this->form_validation->set_rules('defaultr1', 'Reminder 1', 'max_length[4]|required|numeric|trim|greater_than[-1]|callback_reminder1_check');
                $this->form_validation->set_rules('defaultmile', 'Milestone', 'required|trim|max_length[20]');
                $this->form_validation->set_rules('defaultduration', 'Duration', 'max_length[4]|required|numeric|greater_than[0]|trim');
                $this->form_validation->set_rules('defaultr2', 'Reminder 2', 'max_length[4]|numeric|required|greater_than[-1]|trim|callback_reminder2_check');;
            }
            if ($this->form_validation->run() == FALSE) {
                // print_r(json_encode($this->form_validation->error_array()));
                echo json_encode([
                    'success'              => false,
                    'message'              => $this->form_validation->error_array(),

                ]);
            } else {
                // echo true;
                if ($slug == "sa" or $slug == "ap-sa") {
                    $response = $this->issue_model->checkforname(trim($issuename), true, 0);
                } else {
                    $response = $this->issue_model->checkforname(trim($issuename), false, get_staff_area_id($staffid), trim($issuename_reg));
                }
                if ($response == false) {
                    $message = _l('This Action Item already exists', _l('issue'));
                    echo json_encode([
                        'success'              => false,
                        'message'              => ["issue_name"=>$message],

                    ]);
                    die();
                }
                $data = $this->session->flashdata('areas');
                $id = $this->session->flashdata('areaids');
                echo json_encode([
                    'success'              => true,
                    'data'                  => (!empty($data)) ? $data : "",
                    'id'                    => (!empty($id)) ? $id : "",
                ]);
                $this->session->set_flashdata('areas', "");
                $this->session->set_flashdata('areaids', "");
            }
        }
    }

    public function newissue()
    {
        $staffid = $this->session->userdata('staff_user_id');
        $staffareaid = get_staff_area_id($staffid);
        $staffroleid = $this->session->userdata('staff_role');
        if (has_permission('categories', '', 'create') or has_permission('categories_manage', '', 'create')) {
            $data = $this->input->post();
            // pre($data);
            if (!$this->input->post('id')) {
                // print_r($this->input->post());
                $issue = $this->input->post();
                $issuetabledata = [
                    'issue_name' => trim($issue['issue_name']),
                    'is_active' => 1,
                    'staff_id' => $staffid,
                    'area_id' => ($staffroleid == 2) ? 0 : $staffareaid,
                    'parent_issue_id' => 0,

                ];
                $issueid = $this->issue_model->add($issuetabledata);
                $parentissueid = $issueid;
                // if($staffroleid==2){
                //     $areas=get_area_id();
                //     for($i=0;$i<count($areas);$i++){
                //         $issuetabledata=[
                //             'issue_name'=> trim($issue['issue_name']),
                //             'is_active'=>1,
                //             'staff_id'=>$staffid,
                //             'parent_issue_id'=>$issueid,
                //             'area_id'=>$areas[$i],
                //         ];
                //         $this->issue_model->add($issuetabledata);
                //     }
                // }

                // if($this->session->userdata('staff_role')==6){
                //         $areaadmindata=[
                //             'staff_id'=>$staffid,
                //             'issue_id'=>$issueid,
                //             'area_id'=>getstaffarea($staffid)
                //         ];
                //         $this->issue_model->addtoareaadmintable($areaadmindata);


                // }
                $disabledmilestone = [
                    "milestone_name" => trim($this->input->post('defaultmilestonename')),
                    'issue_id' => $issueid,
                    'reminder_one' => convertint($this->input->post('defaultmilestoner1')),
                    'reminder_two' => convertint($this->input->post('defaultmilestoner2')),
                    'days' => convertint($this->input->post('defaultmilestoneduration')),
                    'is_active' => 1,
                    'area_id' => ($staffroleid == 2) ? 0 : $staffareaid,
                    'parent_issue_id' => ($staffroleid == 2) ? 0 : $issueid,
                    'is_closure'=>true,


                ];
                $this->issue_model->addmilestone($disabledmilestone);
                // if($staffroleid==2){

                //     for($i=0;$i<count($areas);$i++){
                //         $disabledmilestone=[
                //             "milestone_name"=>trim($this->input->post('defaultmilestonename')),
                //             'issue_id'=>$issueid,
                //             'reminder_one'=>convertint($this->input->post('defaultmilestoner1')),
                //             'reminder_two'=>convertint($this->input->post('defaultmilestoner2')),
                //             'days'=>convertint($this->input->post('defaultmilestoneduration')),
                //             'i s_active'=>1,
                //             'parent_issue_id'=>$issueid,
                //             'area_id'=>$areas[$i],

                //         ];
                //         $this->issue_model->addmilestone($disabledmilestone);

                //     }
                // }
                if (!empty($issue['milestones'])) {
                    for ($i = 0; $i < count($issue['milestones']); $i++) {
                        if ($issue['milestones'][$i]) {
                            $milestonetabledata = [
                                'milestone_name' => trim($issue['milestones'][$i]),
                                'issue_id' => $issueid,
                                'reminder_one' => convertint($issue['reminder1'][$i]),
                                'reminder_two' => convertint($issue['reminder2'][$i]),
                                'days' => convertint($issue['durations'][$i]),
                                'is_active' => 1,
                                'area_id' => ($staffroleid == 2) ? 0 : $staffareaid,
                                'parent_issue_id' => ($staffroleid == 2) ? 0 : $issueid,

                            ];

                            $this->issue_model->addmilestone($milestonetabledata);
                        }
                    }
                }
                if ($staffroleid == 2) {
                    $areas = get_area_id();
                    $areas_already_having = explode(',', $this->input->post('catareas'));
                    $areas_already_having = []; // updated by Awnish to allow items to be created for all admin even created
                    for ($j = 0; $j < count($areas); $j++) {
                        if (!in_array($areas[$j], $areas_already_having)) {
                            $issuetabledata = [
                                'issue_name' => trim($issue['issue_name']),
                                'is_active' => 1,
                                'staff_id' => $staffid,
                                'parent_issue_id' => $parentissueid,
                                'area_id' => $areas[$j],
                            ];
                            $replicaid = $this->issue_model->add($issuetabledata);
                            $disabledmilestone = [
                                "milestone_name" => trim($this->input->post('defaultmilestonename')),
                                'issue_id' => $replicaid,
                                'reminder_one' => convertint($this->input->post('defaultmilestoner1')),
                                'reminder_two' => convertint($this->input->post('defaultmilestoner2')),
                                'days' => convertint($this->input->post('defaultmilestoneduration')),
                                'is_active' => 1,
                                'area_id' => $areas[$j],
                                'parent_issue_id' => $replicaid,
                                'is_closure'=>true,

                            ];
                            $this->issue_model->addmilestone($disabledmilestone);
                            if (!empty($issue['milestones'])) {
                                for ($i = 0; $i < count($issue['milestones']); $i++) {
                                    if ($issue['milestones'][$i]) {
                                        $milestonetabledata = [
                                            'milestone_name' => trim($issue['milestones'][$i]),
                                            'issue_id' => $replicaid,
                                            'reminder_one' => convertint($issue['reminder1'][$i]),
                                            'reminder_two' => convertint($issue['reminder2'][$i]),
                                            'days' => convertint($issue['durations'][$i]),
                                            'is_active' => 1,
                                            'area_id' => $areas[$j],
                                            'parent_issue_id' => $replicaid,

                                        ];

                                        $this->issue_model->addmilestone($milestonetabledata);
                                    }
                                }
                            }
                        }
                    }
                }

                $message = _l('created_successfully', _l('issue'));
                echo json_encode([
                    'success'              => true,
                    'message'              => $message,

                ]);
                // set_alert('success', _l('issue created', _l('issue')));
                // redirect(admin_url('issues/'));
            } else {
                $id = $this->input->post('id');
                $rem1 = $this->input->post('reminder1');
                $rem2 = $this->input->post('reminder2');

                if (count($this->input->post('durations')) > 1) {
                    $response = $this->checktotalduration($this->input->post('durations'), $rem1, $rem2);
                    if ($response == false) {
                        $message = _l('Total duration of Action Item is mismatching', _l('issue'));
                        echo json_encode([
                            'success'              => false,
                            'message'              => $message,

                        ]);
                        die;
                    }
                }
                $data = $this->input->post();
                $issuedatatoupdate = [
                    'issue_name' => trim($data['issue_name']),
                ];
                
                $is_createdby_superadmin = $this->issue_model->is_createdby_superadmin($id);
                
                if ($staffroleid == 2) {
                    $response = $this->issue_model->updateissue($issuedatatoupdate, $id, true, 0);
                    $this->issue_model->deleteall($id, true, 0);
                } else {
                    if(!$is_createdby_superadmin){
                        $response = $this->issue_model->updateissue($issuedatatoupdate, $id, false, $staffareaid);
                    }else{
                        $response = true;
                    }
                    
                    $this->issue_model->deleteall($id, false, $staffareaid);
                }

                if ($response == true) {
                    $milestones = $this->input->post('milestones');
                    $milestones_id = $this->input->post('milesid');
                    $milestones_durations = $this->input->post('durations');
                    $milestones_reminder1 = $this->input->post('reminder1');
                    $milestones_reminder2 = $this->input->post('reminder2');
                    // for($i=0;$i<count($milestones_id);$i++){
                    for ($i = 0; $i < count($milestones); $i++) {

                        // if($milestones_id[$i]){
                        //     $milestonestoupdate=[
                        //         'milestone_name'=>trim($milestones[$i]),
                        //         'days'=>convertint($milestones_durations[$i]),
                        //         'reminder_one'=>convertint($milestones_reminder1[$i]),
                        //         'reminder_two'=>convertint($milestones_reminder2[$i]),
                        //     ];

                        //     $this->issue_model->updatemilestones($milestonestoupdate,$milestones_id[$i]);
                        // // }
                        // // else{

                        //     if($milestones[$i]){
                        if($i==0){
                            $closure=true;
                        }else{
                            $closure=false;
                        }
                        $milestonetabledata = [
                            'milestone_name' => trim($milestones[$i]),
                            'issue_id' => convertint($id),
                            'days' => convertint($milestones_durations[$i]),
                            'reminder_one' => convertint($milestones_reminder1[$i]),
                            'reminder_two' => convertint($milestones_reminder2[$i]),
                            'is_active' => 1,
                            'area_id' => ($staffroleid == 2) ? 0 : $staffareaid,
                            'parent_issue_id' => ($staffroleid == 2) ? 0 : $id,
                            'is_closure'=>$closure,
                        ];
                        
                        $this->issue_model->addmilestone($milestonetabledata);
                        // }

                        // }
                        //                         }


                        // }

                        // }
                    }

                    // set_alert('success', _l('Updated issue', _l('issue')));
                    // redirect(admin_url('issues/'));
                    $message = _l('Cat_updated_successfully', _l('issue'));
                    echo json_encode([
                        'success'              => true,
                        'message'              => $message,

                    ]);
                }
                // set_alert('warning', _l('problem in Updating', _l('issue')));
                // redirect(admin_url('issues/'));


            }
        } else {
            $message = _l('you are not permitted', _l('issue'));
            echo json_encode([
                'success'              => false,
                'message'              => $message,

            ]);
            // set_alert('warning', _l('you are not permitted', _l('issue')));
            // redirect(admin_url('issues/'));

        }
    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('issues/'));
        }
        $roleid = $this->session->userdata('staff_role');
        if ($roleid == 1 or $roleid == 2) {
            $response = $this->issue_model->delete($id);
            // if (is_array($response) && isset($response['referenced'])) {
            //     set_alert('warning', _l('is_referenced', _l('department_lowercase')));
            // } else
            if ($response == true) {
                set_alert('success', _l('issue deactivated', _l('issue')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('milestone')));
            }
            redirect(admin_url('issues/'));
        } else {
            set_alert('warning', _l('you are not permited', _l('issue')));
            redirect(admin_url('issues/'));
        }
    }

    public function get_area_issues()
    {
        $response = array(
            'success' => false,
            'message' => "Something went wrong."
        );
        if ($this->input->post()) {
            $area_id = $this->input->post('area_id');
            $sub_region_id = $this->input->post('sub_region_id');
            $region_id = $this->input->post('region_id');
            //$all_issues = [];
            $all_issues = $this->issue_model->get_area_issues($area_id, 1);
            $existing_issues = $this->issue_model->get_existing_area_issues($sub_region_id, $region_id);
            //if ($existing_issues) {
                $response = [
                    'success' => true,
                    'message' => "Issues fetched successfully.",
                    'issues' => ['all_issues' => $all_issues, 'existing_issues' => $existing_issues]
                ];
            //}
        }

        echo json_encode($response);
        die;
    }

    public function get_duration_issues()
    {
        $response = array(
            "success" => false,
            "message" => "Something went wrong.",
            "issues" => []
        );
        if ($this->input->post()) {
            // pre($this->input->post());
            $durations = $this->input->post("duration");
            if (!empty($durations)) {
                $issues = [];
                foreach ($durations as $duration) {
                    if (strpos($duration, "-") !== false) {
                        $issue = $this->issue_model->get_duration_issues(explode(" - ", $duration), true);
                    } else {
                        $issue = $this->issue_model->get_duration_issues($duration, false);
                    }
                    if (!empty($issue))
                        array_push($issues, $issue);
                }
                if (!empty($issues)) {
                    $response = array(
                        "success" => true,
                        "message" => "issues fetched successfully",
                        "issues" => $issues
                    );
                }
            }
        }
        echo json_encode($response);
        die;
    }

    public function checktotalduration($duration, $rem1, $rem2)
    {
        $totalduration = convertint($duration[0]);
        $count = 0;
        for ($i = 1; $i < count($duration); $i++) {
            $count = $count + convertint($duration[$i]);
        }
        if ($totalduration == $count and $rem1[0] == 0 and $rem2[0] == 0) {
            return true;
        } else {
            return false;
        }
    }
    public function check_if_created_by_sa($catname)
    {
        $response = $this->issue_model->check_whose_cat_is_it($catname, true);
        return $response;
    }
    public function check_if_created_by_aa($catname)
    {
        $response = $this->issue_model->check_whose_cat_is_it($catname, false);
        return $response;
    }
}

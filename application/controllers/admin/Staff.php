<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends AdminController
{
    /* List all staff members */
    public function index()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        $this->load->model('issue_model');
        $tableView = ["ae-global" => "staff", "ae-area" => "staff_ae", "aa" => "staff", "ar" => "staff_ar", "at" => "staff_at", "ata" => "staff_ata"];
        $siteTitle = ["ae-global" => "National Observers", "ae-area" => "State Observer", "aa" => "State Admins", "ar" => "Reviewer", "at" => "Project Leader", "ata" => "Project Support"];

        // fetching the slug for the staff
        if (!empty($this->input->get('role'))) {
            $slug_url = $this->input->get('role');
            $role_data = $this->roles_model->get_role_from_slug($slug_url);
            $view_file = 'manage_' . $slug_url;
            $permission = 'staff_' . $slug_url;
            $tableViewData = (isset($tableView[$slug_url])) ? $tableView[$slug_url] : 'staff';
        } else {
            $view_file = 'manage';
            $permission = 'staff';
            $role_data = $this->roles_model->get_role_from_slug('aa');
            $tableViewData = 'staff';
            $slug_url = 'aa';
        }

        if (!has_permission($permission, '', 'view') && !has_permission($permission, '', 'view_own')) {
            access_denied('staff');
        }

        if ($this->input->is_ajax_request()) {
            $tableParams = [
                'role' => $role_data->roleid,
                'active' => 1,
                'role_slug' => $role_data->slug_url
            ];
            $this->app->get_table_data($tableViewData, $tableParams);
        }
        $all_issues = $this->issue_model->get_area_issues($GLOBALS['current_user']->area, 1);
        //pre($all_issues);
        $data['staff_members'] = $this->staff_model->get('', ['role' => $role_data->roleid, 'active' => 1]);
        $data['title']         = $siteTitle[$slug_url];
        $data['role']          = $role_data;
        $data['permission']    = $permission;
        $data['categories']    = $all_issues;
        $this->load->view('admin/staff/' . $view_file, $data);
    }

    public function get_action_takers()
    {
        $response = [
            "success" => false,
            "message" => "Something went wrong."
        ];
        if ($this->input->post()) {
            $reviewer_id = $this->input->post('id');
            $action_takers = $this->staff_model->get_action_reviewer_takers($reviewer_id);
            if (count($action_takers) > 0) {
                $response = [
                    "success" => true,
                    "message" => "AT data fetched successfully.",
                    "action_takers" => $action_takers
                ];
            }
        }
        echo json_encode($response);
        die;
    }

    /* Get memeber table data */
    public function get_member_table_data()
    {
        $data = $this->staff_model->get_member_table_data();
        if ($this->input->is_ajax_request()) {
            return ['success' => true, 'table_Data' => $data];
        }
    }

    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        hooks()->do_action('staff_member_edit_view_profile', $id);

        $this->load->model('departments_model');
        $this->load->model('area_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $data['password'] = $this->input->post('password', false);

            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('staff/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                handle_staff_profile_image_upload($id);
                $response = $this->staff_model->update($data, $id);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('staff/member/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member']            = $member;
            $title                     = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

            $ts_filter_data = [];
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to']   = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }

            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            $data['timesheets']  = $data['logged_time']['timesheets'];
        }
       // $this->load->model('currencies_model');
       // $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['base_currency'] = [];
        $data['roles']         = $this->roles_model->get();
        $data['user_notes']    = $this->misc_model->get_notes($id, 'staff');
        $data['departments']   = $this->departments_model->get();
        $data['area']          = $this->area_model->get(false, true);
        $data['title']         = $title;

        $this->load->view('admin/staff/member', $data);
    }

    /* Add new staff member or edit existing */
    public function create($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        //  hooks()->do_action('staff_member_edit_view_profile', $id);

        //  $this->load->model('departments_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $data['password'] = random_password();

            if (isset($data['id'])) {
                $id = $data['id'];
            }

            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $data["send_welcome_email"] = "on";
                $id = $this->staff_model->add($data);
                if (is_array($id)) {
                    if (!$id['success']) {
                        echo json_encode($id);
                        die;
                    }
                }
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    $success = true;
                    $message = _l('added_successfully', _l('National Observer'));
                // sms api
                $this->sendSms($data['phonenumber'],$id,'created');
                // sms api
                    echo json_encode([
                        'success'              => $success,
                        'message'              => $message,
                    ]);
                    die;
                    //redirect(admin_url('staff/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                handle_staff_profile_image_upload($id);
                $response = $this->staff_model->update($data, $id);

                if (is_array($response)) {
                    if (!$response['success']) {
                        echo json_encode($response);
                        die;
                    }
                }

                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                //redirect(admin_url('staff/member/' . $id));
                $message = _l('updated_successfully', _l('National Observer'));
                echo json_encode([
                    'success'              => true,
                    'message'              => $message,
                ]);
                die;
            }
        }
    }


    /* Get role permission for specific role id */
    public function role_changed($id)
    {
        if (!has_permission('staff', '', 'view')) {
            ajax_access_denied('staff');
        }

        echo json_encode($this->roles_model->get($id)->permissions);
    }

    public function save_dashboard_widgets_order()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');

        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', serialize($post_data));
    }

    public function save_dashboard_widgets_visibility()
    {
        hooks()->do_action('before_save_dashboard_widgets_visibility');

        $post_data = $this->input->post();
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', serialize($post_data['widgets']));
    }

    public function reset_dashboard()
    {
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', null);
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', null);

        redirect(admin_url());
    }

    public function save_hidden_table_columns()
    {
        hooks()->do_action('before_save_hidden_table_columns');
        $data   = $this->input->post();
        $id     = $data['id'];
        $hidden = isset($data['hidden']) ? $data['hidden'] : [];
        update_staff_meta(get_staff_user_id(), 'hidden-columns-' . $id, json_encode($hidden));
    }

    public function change_language($lang = '')
    {
        hooks()->do_action('before_staff_change_language', $lang);

        $this->db->where('staffid', get_staff_user_id());
        $this->db->update(db_prefix() . 'staff', ['default_language' => $lang]);
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url());
        }
    }

    public function timesheets()
    {
        $data['view_all'] = false;
        if (is_admin() && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM ' . db_prefix() . 'taskstimers WHERE staff_id !=' . get_staff_user_id())->result_array();
            $data['view_all']                      = true;
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff_timesheets', ['view_all' => $data['view_all']]);
        }

        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }

        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $data['title']       = '';
        $this->load->view('admin/staff/timesheets', $data);
    }

    public function delete()
    {
        if (!is_admin() && is_admin($this->input->post('id'))) {
            die('Busted, you can\'t delete administrators');
        }

        if (has_permission('staff', '', 'delete')) {
            $success = $this->staff_model->delete($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }

        redirect(admin_url('staff'));
    }

    /* When staff edit his profile */
    public function edit_profile()
    {
        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $data = $this->input->post();
            $response = $this->staff_model->update_profile($data, get_staff_user_id());
            if ($response['success']) {
                set_alert('success', _l('staff_profile_updated'));
            } else {
                //print_r($response);
                set_alert('danger', $response['message']);
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
            //redirect(admin_url('staff/edit_profile'));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $data['member']            = $member;
        $data['title']             = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('staff', '', 'create') || has_permission('staff', '', 'edit'))) {
            $staff_id = $id;
        }
        hooks()->do_action('before_remove_staff_profile_image');
        $member = $this->staff_model->get($staff_id);
        if (file_exists(get_upload_path_by_type('staff') . $staff_id)) {
            delete_dir(get_upload_path_by_type('staff') . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update(db_prefix() . 'staff', [
            'profile_image' => null,
        ]);

        if (!is_numeric($id)) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        } else {
            redirect(admin_url('staff/member/' . $staff_id));
        }
    }

    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (trim($data['oldpassword']) == trim($data['newpassword'])) {
                set_alert('danger', _l("Old password & new password can't be same."));
                redirect(admin_url('staff/edit_profile'));
            }
            $response = $this->staff_model->change_password($this->input->post(null, false), get_staff_user_id());
            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorrect'));
            } else {
                if ($response == true) {
                    $member = $this->staff_model->get(get_staff_user_id());
                    $sent = send_mail_template('staff_password_resetted', $member->email, $member->staffid, $data['newpassword']);
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }
            redirect(admin_url('staff/edit_profile'));
        }
    }

    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }

        hooks()->do_action('staff_profile_access', $id);

        $data['logged_time'] = $this->staff_model->get_logged_time_data($id);
        $data['staff_p']     = $this->staff_model->get($id);

        if (!$data['staff_p']) {
            blank_page('Staff Member Not Found', 'danger');
        }

        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments']       = $this->departments_model->get();
        $data['title']             = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $total_notifications = total_rows(db_prefix() . 'notifications', [
            'touserid' => get_staff_user_id(),
        ]);
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
        $this->load->view('admin/staff/myprofile', $data);
    }

    public function update_staff_status($id, $status)
    {
        $update_status = $this->staff_model->change_staff_status($id, $status);
        if ($update_status) {
            $response = [
                'success' => true,
                'message' => "Status has been updated successfully."
            ];
        } else {
            $response = [
                'success' => false,
                'message' => "Something went wrong!"
            ];
        }
        return $response;
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_status()
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                if (!$data['status']) {
                    $slug_arr = ['at', 'ata'];
                    $is_projects_active = false;
                    if (in_array($data['slug_url'], $slug_arr)) {
                        $is_projects_active = $this->staff_model->check_staff_projects_status($data['id']);
                        $message = 'User has active projects';
                    }
                    if (in_array($data['slug_url'], ['ar'])) {
                        $is_projects_active = $this->staff_model->get_active_ats($data['id']);
                        $message = 'User has active Project Leaders';
                        // pre($is_projects_active);
                    }
                    // $is_projects_active = false;
                    if ($is_projects_active) {
                        $response = array(
                            'success' => false,
                            'message' => $message,
                            'check_status' => 1
                        );
                    } else {
                        $response = $this->update_staff_status($data['id'], $data['status']);
                        if ($data['slug_url'] == "at")
                            $this->staff_model->remove_staff_issue($data['id']);
                        $response['check_status'] = 0;
                    }
                    echo json_encode($response);
                    die;
                }
                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];

                $if_member_email_exist = $this->staff_model->get_member(['email' => $member['email'], "active" => 1, "staffid !=" => $data['id']]);
                $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $member['phonenumber'], "active" => 1, "staffid !=" => $data['id']]);
                 //    removing phone validation
                            $if_member_phone_exist=[];
                 // 
                if ($data['slug_url'] != 'ae-area' || $data['slug_url'] != 'ae-global') {
                    $if_member_area_exist = $this->staff_model->get_member(['area' => $member['area'], 'active' => 1]);
                    if (count($if_member_area_exist) > 0) {
                        $response = array(
                            'success' => false,
                            'message' => "User already exists in this Area.",
                            'check_status' => 0
                        );
                    }
                }


                $if_disabled_region = [];
                $if_disabled_subregion = [];
                if ($data["slug_url"] == "ae-area") {
                    $check_region = $this->staff_model->check_staff_region_status($data['id'], ["r.status" => 1]);
                    if (count($check_region) > 0) {
                        $if_disabled_region = [];
                    } else {
                        $if_disabled_region = [1];
                    }
                } else {
                    $if_disabled_region = $this->staff_model->check_staff_region_status($data['id'], ["r.status" => 0]);
                    $if_disabled_subregion = $this->staff_model->check_staff_subregion_status($data['id'], ["r.status" => 0]);
                }

                if (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use.",
                        'check_status' => 0
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use.",
                        'check_status' => 0
                    );
                } elseif (count($if_disabled_region) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "The user cannot be activated if their geography is inactive.",
                        'check_status' => 0
                    );
                } elseif (count($if_disabled_subregion) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "The user cannot be activated if their geography is inactive.",
                        'check_status' => 0
                    );
                } else {
                    $response = $this->update_staff_status($data['id'], $data['status']);
                    $response['check_status'] = 1;
                }
            }
        }
        echo json_encode($response);
        die;
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');
        if ($this->input->post()) {
            $page   = $this->input->post('page');
            $offset = ($page * $this->misc_model->get_notifications_limit());
            $this->db->limit($this->misc_model->get_notifications_limit(), $offset);
            $this->db->where('touserid', get_staff_user_id());
            $this->db->order_by('date', 'desc');
            $notifications = $this->db->get(db_prefix() . 'notifications')->result_array();
            $i             = 0;
            foreach ($notifications as $notification) {
                if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
                    if ($notification['fromuserid'] != 0) {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], [
                            'staff-profile-image-small',
                            'img-circle',
                            'pull-left',
                        ]) . '</a>';
                    } else {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('clients/client/' . $notification['fromclientid']) . '">
                    <img class="client-profile-image-small img-circle pull-left" src="' . contact_profile_image_url($notification['fromclientid']) . '"></a>';
                    }
                } else {
                    $notifications[$i]['profile_image'] = '';
                    $notifications[$i]['full_name']     = '';
                }
                $additional_data = '';
                if (!empty($notification['additional_data'])) {
                    $additional_data = unserialize($notification['additional_data']);
                    $x               = 0;
                    foreach ($additional_data as $data) {
                        if (strpos($data, '<lang>') !== false) {
                            $lang = get_string_between($data, '<lang>', '</lang>');
                            $temp = _l($lang);
                            if (strpos($temp, 'project_status_') !== false) {
                                $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                $temp   = $status['name'];
                            }
                            $additional_data[$x] = $temp;
                        }
                        $x++;
                    }
                }
                $notifications[$i]['description'] = _l($notification['description'], $additional_data);
                $notifications[$i]['date']        = time_ago($notification['date']);
                $notifications[$i]['full_date']   = $notification['date'];
                $i++;
            } //$notifications as $notification
            echo json_encode($notifications);
            die;
        }
    }


    /* Save Area Admin */
    public function save()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        if ($this->input->post()) {
            $data = $this->input->post();

            /* Update staff */
            if ($data['id'] != "") {
                $form_data = array(
                    "firstname" => isset($data['name']) ? general_validate($data['name']) : "",
                    "email" => isset($data['email']) ? general_validate($data['email']) : "",
                    "organisation" => isset($data['organisation']) ? general_validate($data['organisation']) : "",
                    // "password" => random_string(),
                    "area" => isset($data['departments']) ? general_validate($data['departments']) : "",
                    "phonenumber" => isset($data['phone']) ? general_validate($data['phone']) : "",
                );
                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];
                // check if email id was changed
                if ($member['email'] != $form_data['email']) {
                    $form_data["send_welcome_email"] = "on";
                }
                $if_member_area_exist = $if_member_email_exist = $if_member_phone_exist = $response = [];
                if ($member['area'] != $form_data['area']) {
                    $if_member_area_exist = $this->staff_model->get_member(['role' => $form_data['role'], 'area' => $form_data['area'], 'active' => 1]);
                }
                if ($member['email'] != $form_data['email']) {
                    $if_member_email_exist = $this->staff_model->get_member(['email' => $form_data['email'], 'active' => 1]);
                }
                if ($member['phonenumber']  != $form_data['phonenumber']) {
                    $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $form_data['phonenumber'], 'active' => 1]);
                    //    removing phone validation
                    $if_member_phone_exist=[];
                    // 
                }

                if (count($if_member_area_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "State admin already exists in this Area."
                    );
                } elseif (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use."
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use."
                    );
                } else {
                    $result = $this->staff_model->update_staff($form_data, $data['id']);

                    $role_data = $this->roles_model->get($data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $data['id']);

                    if ($result || !$result) {
                        $response = array(
                            'success' => true,
                            'message' => "Member has been updated successfully"
                        );
                    }
                }
                echo json_encode($response);
                die;
            }

            /* Add new staff */
            $form_data = array(
                "firstname" => isset($data['name']) ? general_validate($data['name']) : "",
                "email" => isset($data['email']) ? general_validate($data['email']) : "",
                "organisation" => isset($data['organisation']) ? general_validate($data['organisation']) : "",
                // "password" => isset($data['password']) ? general_validate($data['password']) : "",
                "password" => random_password(),
                "role" => isset($data['role']) ? general_validate($data['role']) : "",
                "area" => isset($data['departments']) ? general_validate($data['departments']) : "",
                "phonenumber" => isset($data['phone']) ? general_validate($data['phone']) : "",
                "datecreated" => date('Y-m-d H:i:s'),
                "active" => '1'
            );
            $if_member_email_exist = $this->staff_model->get_member(['email' => $form_data['email'], 'active' => 1]);
            $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $form_data['phonenumber'], 'active' => 1]);
            //    removing phone validation
                $if_member_phone_exist=[];
            // 
            $if_member_area_exist = $this->staff_model->get_member(['role' => $form_data['role'], 'area' => $form_data['area'], 'active' => 1]);


            if (count($if_member_area_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "State admin already exists in this State."
                );
            } elseif (count($if_member_email_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Email is already in use."
                );
            } elseif (count($if_member_phone_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Phone number is already in use."
                );
            } else {
                $result = $this->staff_model->save($form_data);
                // update staff permission
                $role_data = $this->roles_model->get($data['role']);
                if (!empty($role_data->permissions)) {
                    $permissions = $role_data->permissions;
                }
                $this->staff_model->update_permissions($permissions, $result);

                if ($result) {
// sms api
                    $this->sendSms($data['phone'],$result,'created');
// sms api
                    $response = array(
                        'success' => true,
                        'message' => "Member has been added successfully"
                    );
                }
            }
        }
        echo json_encode($response);
        die;
    }

    /* Get staff details edit / ajax */
    public function get_staff_detail()
    {
        $data               = $this->input->post();

        $id                 = $data['staffid'];
        $data     = $this->staff_model->get($id);
        $response = array(
            'success' => true,
            'staff'   => $data,
            'message' => ""
        );
        echo json_encode($response);
    }

    /* Get at assistants details edit / ajax */
    public function get_ata()
    {
        $data               = $this->input->post();

        $id                 = $data['staffid'];
        $data     = $this->staff_model->get_ata($id);
        $response = array(
            'success' => true,
            'staff'   => $data,
            'message' => ""
        );
        echo json_encode($response);
    }

    public function prepare_staff_region_data($data, $staff_id)
    {
        $date = date('Y-m-d H:i:s');
        $region_data = array();
        foreach ($data as $key => $region) {
            array_push($region_data, ['staff_id' => $staff_id, 'region' => $region, 'created_date' => $date]);

            // if (is_array($region)) {
            //     foreach ($region as $index => $innerRegion) {
            //         array_push($region_data, ['staff_id' => $staff_id, 'region' => $key, 'sub_region' => $index, 'created_date' => $date]);
            //     }
            // } else {
            //     array_push($region_data, ['staff_id' => $staff_id, 'region' => $key, 'sub_region' => 0, 'created_date' => $date]);
            // }
        }
        return $region_data;
    }

    public function save_area_enforcer()
    {
        if (!has_permission('staff_ae-area', '', 'create')) {
            access_denied('staff_ae-area');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            // print_r($data); die;
            #Check if Region selected
            if (!isset($data['region'])) {
                $response = array(
                    'success' => false,
                    'message' => "Please select a City."
                );
                echo json_encode($response);
                die;
            }

            if ($data['id'] != "") {
                $staff_form_data = [
                    "firstname" => isset($data['firstname']) ? general_validate($data['firstname']) : "",
                    "email" => isset($data['email']) ? general_validate($data['email']) : "",
                    "organisation" => isset($data['organisation']) ? general_validate($data['organisation']) : "",
                    "phonenumber" => isset($data['phonenumber']) ? general_validate($data['phonenumber']) : "",
                ];

                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];
                // check if email id was changed
                if ($member['email'] != $staff_form_data['email']) {
                    $staff_form_data["send_welcome_email"] = "on";
                }

                $if_member_email_exist = $if_member_phone_exist = $response = [];
                if ($member['email'] != $staff_form_data['email']) {
                    $if_member_email_exist = $this->staff_model->get_member(['email' => $staff_form_data['email'], 'active' => 1]);
                }
                if ($member['phonenumber']  != $staff_form_data['phonenumber']) {
                    $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $staff_form_data['phonenumber'], 'active' => 1]);
                    //    removing phone validation
                           $if_member_phone_exist=[];
                    // 
                }

                if (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use."
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use."
                    );
                } else {
                    $result = $this->staff_model->update_staff($staff_form_data, $data['id']);

                    $role_data = $this->roles_model->get($data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $data['id']);

                    if ($result || !$result) {
                        $region_data = $this->prepare_staff_region_data($data['region'],  $data['id']);

                        $this->staff_model->delete_staff_data($data['id'], 'staff_region');
                        $this->staff_model->save_staff_region($region_data);

                        $response = array(
                            'success' => true,
                            'message' => "Member has been updated successfully"
                        );
                    }
                }
                echo json_encode($response);
                die;
            }
            #prepare staff data
            $date = date('Y-m-d H:i:s');
            $staff_form_data = [
                "firstname" => isset($data['firstname']) ? general_validate($data['firstname']) : "",
                "email" => isset($data['email']) ? general_validate($data['email']) : "",
                "organisation" => isset($data['organisation']) ? general_validate($data['organisation']) : "",
                "role" => isset($data['role']) ? general_validate($data['role']) : "",
                "area" => isset($data['area']) ? general_validate($data['area']) : "",
                "phonenumber" => isset($data['phonenumber']) ? general_validate($data['phonenumber']) : "",
                "password" => random_password(),
                "datecreated" => $date,
                "active" => '1'
            ];

            #check if phone and email already exist
            $if_member_email_exist = $this->staff_model->get_member(['email' => $data['email'], 'active' => 1]);
            $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $data['phonenumber'], 'active' => 1]);
            // removing phone validation
                $if_member_phone_exist=[];
            // 
            if (count($if_member_email_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Email is already in use."
                );
            } elseif (count($if_member_phone_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Phone number is already in use."
                );
            } else {
                #Save Staff Data
                $staff_id = $this->staff_model->save($staff_form_data);

                #Check if Staff added
                if ($staff_id) {
                    # update staff permission
                    $role_data = $this->roles_model->get($staff_form_data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $staff_id);

                    #preparing region data
                    $region_data = $this->prepare_staff_region_data($data['region'], $staff_id);

                    #Save Region Data
                    $result = $this->staff_model->save_staff_region($region_data);
                    if ($result) {
// sms api
                        $this->sendSms($data['phonenumber'],$staff_id,'created');
// sms api
                        $response = array(
                            'success' => true,
                            'message' => "Member has been added successfully"
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => "Something went wrong."
                        );
                    }
                }
            }

            echo json_encode($response);
            die;
        }
    }


    public function save_action_reviewer()
    {
        if (!has_permission('staff_ar', '', 'create')) {
            access_denied('staff_ar');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            // print_r($data); exit;
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid email.',
                ]);
                die;
            }

            if (trim($data['firstname']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid name.',
                ]);
                die;
            } elseif (trim($data['organisation']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid organisation.',
                ]);
                die;
            }
            // Update action reviewer
            if (isset($data['id']) && $data['id'] != "") {


                $form_data = array(
                    "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                    "email" => isset($data['email']) ? general_validate($data['email']) : "",
                    "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                    "area" => isset($data['area']) ? general_validate($data['area']) : "",
                    "role" => isset($data['role']) ? general_validate($data['role']) : "",
                    "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                );
                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];

                // check if email id was changed
                if ($member['email'] != $form_data['email']) {
                    $form_data["send_welcome_email"] = "on";
                }
                $if_member_email_exist = $if_member_phone_exist = $response = [];

                if ($member['email'] != $form_data['email']) {
                    $if_member_email_exist = $this->staff_model->get_member(['email' => $form_data['email'], 'active' => 1]);
                }
                if ($member['phonenumber']  != $form_data['phonenumber']) {
                    $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $form_data['phonenumber'], 'active' => 1]);
                // removing phone validation
                    $if_member_phone_exist=[];
                // 
                }

                if (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use."
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use."
                    );
                } else {

                    $result = $this->staff_model->update_staff($form_data, $data['id']);

                    $role_data = $this->roles_model->get($data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $data['id']);

                    if ($result || !$result) {
                        $response = array(
                            'success' => true,
                            'message' => "Member has been updated successfully"
                        );
                    }
                }
                echo json_encode($response);
                die;
            }

            // Add action reviewer

            $date = date('Y-m-d H:i:s');
            $staff_form_data = [
                "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                "email" => isset($data['email']) ? general_validate($data['email']) : "",
                "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                "role" => isset($data['role']) ? general_validate($data['role']) : "",
                "area" => isset($data['area']) ? general_validate($data['area']) : "",
                "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                "password" => random_password(),
                "datecreated" => $date,
                "active" => '1'
            ];

            #check if phone and email already exist
            $if_member_email_exist = $this->staff_model->get_member(['email' => $data['email'], 'active' => 1]);
            $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $data['phonenumber'], 'active' => 1]);
            // removing phone validation
                $if_member_phone_exist=[];
            //             
            if (count($if_member_email_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Email is already in use."
                );
            } elseif (count($if_member_phone_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Phone number is already in use."
                );
            } else {
                #Save Staff Data
                $staff_id = $this->staff_model->save($staff_form_data);

                #Check if Staff added
                if ($staff_id) {
                    # update staff permission
                    $role_data = $this->roles_model->get($staff_form_data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $staff_id);
// sms api
                    $this->sendSms($data['phonenumber'],$staff_id,'created');
// sms api
                    $response = array(
                        'success' => true,
                        'message' => "Member has been added successfully"
                    );
                }
            }
            echo json_encode($response);
            die;
        }
    }

    public function get_staff_region()
    {
        if ($this->input->post()) {
            $this->load->model('region_model');
            $regions =  $this->region_model->get_staff_region($this->input->post('id'));
            if ($regions) {
                $response = [
                    'success' => true,
                    'message' => "Staff Regions fetched successfully.",
                    'regions' => $regions
                ];
            } else {
                $response = array(
                    'success' => false,
                    'message' => "Something went wrong."
                );
            }
        }

        echo json_encode($response);
        die;
    }

    public function get_area_reviewers()
    {
        if ($this->input->post()) {
            $reviewers = $this->staff_model->get_area_reviewers($this->input->post('area_id'));
            if ($reviewers) {
                $response = [
                    'success' => true,
                    'message' => "Reviewers fetched successfully.",
                    'reviewers' => $reviewers
                ];
            } else {
                $response = array(
                    'success' => false,
                    'message' => "Something went wrong."
                );
            }
        }
        echo json_encode($response);
        die;
    }

    /**
     * Function to prepare and save action taker's category
     * @param Array $data       Form data
     */

    public function save_action_taker_category($data)
    {
        $form_data = [];
        $date = date('Y-m-d H:i:s');

        foreach ($data['categories'] as $value) {
            array_push($form_data, ['staff_id' => $data['staff_id'], 'issue_id' => $value, 'created_at' => $date]);
        }
        $this->staff_model->save_action_taker_category($form_data);
        return true;
    }

    public function validate_custom_input($data, $field_names)
    {
        foreach ($field_names as $key => $field_name) {

            if (!isset($data[$key]) || empty($data[$key])) {
                $response = array(
                    'success' => false,
                    'message' => ucwords($field_name) . " is required."
                );
                echo json_encode($response);
                die;
            }
        }
    }

    /* Function to Save Action Taker */
    public function save_action_taker()
    {
        if (!has_permission('staff_ae-area', '', 'create')) {
            access_denied('staff_ae-area');
        }
        if ($this->input->post()) {
            $data = $this->input->post();

            $input_fields = ['region' => "City", 'sub_region' => "Municipal Zone", 'reviewer' => 'reviewer', 'categories' => 'Action Item'];
            #validate inputs
            $this->validate_custom_input($data, $input_fields);
            $rid = $this->input->post("region");
            $srid = $this->input->post("sub_region");
            $cats = $this->input->post("categories");
            $this->load->model("issue_model");
            if ($data['id'] == "") {

                $existing_cats = $this->issue_model->get_existing_area_issues($srid, $rid);
                if ($existing_cats) {
                    foreach ($data["categories"] as $category) {
                        if (in_array($category, $existing_cats)) {
                            $response = array(
                                'success' => false,
                                'message' => "Selected category is already assigned to another action taker."
                            );
                            echo json_encode($response);
                            die;
                        }
                    }
                }
            }
            $date = date('Y-m-d H:i:s');
            if ($data['id'] != "") {
                $staff_form_data = [
                    "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                    "email" => isset($data['email']) ? general_validate($data['email']) : "",
                    "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                    "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                ];

                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];
                // check if email id was changed
                if ($member['email'] != $staff_form_data['email']) {
                    $staff_form_data["send_welcome_email"] = "on";
                }
                $if_member_email_exist = $if_member_phone_exist = $response = [];
                if ($member['email'] != $staff_form_data['email']) {
                    $if_member_email_exist = $this->staff_model->get_member(['email' => $staff_form_data['email'], 'active' => 1]);
                }
                if ($member['phonenumber']  != $staff_form_data['phonenumber']) {
                    $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $staff_form_data['phonenumber'], 'active' => 1]);
            //    removing phone validation
                    $if_member_phone_exist=[];
            // 
                }

                if (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use."
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use."
                    );
                } else {
                    $result = $this->staff_model->update_staff($staff_form_data, $data['id']);

                    $role_data = $this->roles_model->get($data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $data['id']);

                    if ($result || !$result) {


                        #Save Region and Sub-Region data
                        $region_data = [
                            'staff_id' => $data['id'],
                            'region' => $data['region'],
                            'sub_region' => $data['sub_region'],
                            'created_date' => $date
                        ];
                        $this->staff_model->delete_staff_data($data['id'], 'staff_region');
                        $this->staff_model->save_staff_region($region_data, false);

                        #Save Category Data
                        $category_data = [
                            'categories' => $data['categories'],
                            'staff_id' => $data['id']
                        ];
                        $this->staff_model->delete_staff_data($data['id'], 'staff_issues');
                        $this->save_action_taker_category($category_data);

                        #Save Reviewer Data
                        $reviewer_data = [
                            'staff_id' => $data['id'],
                            'assistant_id' => $data['reviewer'],
                            'created_at' => $date
                        ];
                        $this->staff_model->delete_staff_data($data['id'], 'staff_assistance');
                        $this->staff_model->save_action_taker_reviewer($reviewer_data);

                        $response = array(
                            'success' => true,
                            'message' => "Member has been updated successfully"
                        );
                        #umair's check
                        $this->assign_to_tickets(getstaffarea($data['id']), $data['region'], $data['sub_region'], $data['categories'], $data['id']);
                    }
                }
                echo json_encode($response);
                die;
            }
            $form_data = [
                "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                "email" => isset($data['email']) ? general_validate($data['email']) : "",
                "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                "role" => isset($data['role']) ? general_validate($data['role']) : "",
                "area" => isset($data['area']) ? general_validate($data['area']) : "",
                "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                "password" => random_password(),
                "datecreated" => $date,
                "active" => '1'
            ];

            #check if phone and email already exist
            $if_member_email_exist = $this->staff_model->get_member(['email' => $data['email'], 'active' => 1]);
            $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $data['phonenumber'], 'active' => 1]);
            //    removing phone validation
            $if_member_phone_exist=[];
            // 
            if (count($if_member_email_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Email is already in use."
                );
            } elseif (count($if_member_phone_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Phone number is already in use."
                );
            } else {
                #Save Staff Data
                $staff_id = $this->staff_model->save($form_data);

                #Check if Staff added
                if ($staff_id) {
                    # update staff permission
                    $role_data = $this->roles_model->get($form_data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $staff_id);

                    #Save Region and Sub-Region data
                    $region_data = [
                        'staff_id' => $staff_id,
                        'region' => $data['region'],
                        'sub_region' => $data['sub_region'],
                        'created_date' => $date
                    ];
                    $this->staff_model->save_staff_region($region_data, false);

                    #Save Category Data
                    $category_data = [
                        'categories' => $data['categories'],
                        'staff_id' => $staff_id
                    ];
                    $this->save_action_taker_category($category_data);

                    #Save Reviewer Data
                    $reviewer_data = [
                        'staff_id' => $staff_id,
                        'assistant_id' => $data['reviewer'],
                        'created_at' => $date
                    ];
                    $this->staff_model->save_action_taker_reviewer($reviewer_data);
                    #umair's check
                    $this->assign_to_tickets(getstaffarea($staff_id), $data['region'], $data['sub_region'], $data['categories'], $staff_id);
// sms api
                    $this->sendSms($data['phonenumber'],$staff_id,'created');
// sms api
                    $response = array(
                        'success' => true,
                        'message' => "Member has been added successfully"
                    );
                }
            }
            echo json_encode($response);
            die;
        }
    }


    public function get_action_taker()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $at = $this->staff_model->get_action_taker($data);
        }
        echo json_encode($at);
        die;
    }


    public function save_action_assistance()
    {

        if (!has_permission('staff_ata', '', 'create')) {
            access_denied('staff_ata');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $date = date('Y-m-d H:i:s');
            $input_fields = ['region' => 'City', 'sub_region' => 'Municipal Zone', 'action_taker' => 'Project Leader'];
            #validate inputs
            $this->validate_custom_input($data, $input_fields);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            if ($this->form_validation->run() == FALSE) {

                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid email.',
                ]);
                die;
            }
            if (trim($data['firstname']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid name.',
                ]);
                die;
            } elseif (trim($data['organisation']) == '') {
                echo json_encode([
                    'success'              => false,
                    'message'              => 'Invalid organisation.',
                ]);
                die;
            }
            // Update action reviewer
            if (isset($data['id']) && $data['id'] != "") {


                $form_data = array(
                    "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                    "email" => isset($data['email']) ? general_validate($data['email']) : "",
                    "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                    "area" => isset($data['area']) ? general_validate($data['area']) : "",
                    "role" => isset($data['role']) ? general_validate($data['role']) : "",
                    "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                );
                $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];
                // check if email id was changed
                if ($member['email'] != $form_data['email']) {
                    $form_data["send_welcome_email"] = "on";
                }
                $if_member_email_exist = $if_member_phone_exist = $response = [];

                if ($member['email'] != $form_data['email']) {
                    $if_member_email_exist = $this->staff_model->get_member(['email' => $form_data['email'], 'active' => 1]);
                }
                if ($member['phonenumber']  != $form_data['phonenumber']) {
                    $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $form_data['phonenumber'], 'active' => 1]);
                    //    removing phone validation
                            $if_member_phone_exist=[];
                    // 
                }

                if (count($if_member_email_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Email is already in use."
                    );
                } elseif (count($if_member_phone_exist) > 0) {
                    $response = array(
                        'success' => false,
                        'message' => "Phone number is already in use."
                    );
                } else {

                    $result = $this->staff_model->update_staff($form_data, $data['id']);

                    $role_data = $this->roles_model->get($data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $data['id']);

                    if ($result || !$result) {
                        #Save Region and Sub-Region data
                        $region_data = [
                            'staff_id' => $data['id'],
                            'region' => $data['region'],
                            'sub_region' => $data['sub_region'],
                            'created_date' => $date
                        ];
                        $this->staff_model->delete_staff_data($data['id'], 'staff_region');
                        $this->staff_model->save_staff_region($region_data, false);

                        #Save Action Taker
                        $action_taker_data = [
                            'staff_id' => $data['id'],
                            'assistant_id' => $data['action_taker'],
                            'created_at' => $date
                        ];
                        $this->staff_model->delete_staff_data($data['id'], 'staff_assistance');
                        $this->staff_model->save_action_taker_reviewer($action_taker_data);
                        $response = array(
                            'success' => true,
                            'message' => "Member has been updated successfully"
                        );
                    }
                }
                echo json_encode($response);
                die;
            }

            // Add action reviewer


            $staff_form_data = [
                "firstname" => isset($data['firstname']) ? general_validate(trim(ucwords($data['firstname']))) : "",
                "email" => isset($data['email']) ? general_validate($data['email']) : "",
                "organisation" => isset($data['organisation']) ? general_validate(trim(ucwords($data['organisation']))) : "",
                "role" => isset($data['role']) ? general_validate($data['role']) : "",
                "area" => isset($data['area']) ? general_validate($data['area']) : "",
                "phonenumber" => isset($data['phonenumber']) ? general_validate(trim($data['phonenumber'])) : "",
                "password" => random_password(),
                "datecreated" => $date,
                "active" => '1'
            ];

            #check if phone and email already exist
            $if_member_email_exist = $this->staff_model->get_member(['email' => $data['email'], 'active' => 1]);
            $if_member_phone_exist = $this->staff_model->get_member(['phonenumber' => $data['phonenumber'], 'active' => 1]);
            //    removing phone validation
            $if_member_phone_exist=[];
            // 
            if (count($if_member_email_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Email is already in use."
                );
            } elseif (count($if_member_phone_exist) > 0) {
                $response = array(
                    'success' => false,
                    'message' => "Phone number is already in use."
                );
            } else {
                #Save Staff Data
                $staff_id = $this->staff_model->save($staff_form_data);

                #Check if Staff added
                if ($staff_id) {
                    # update staff permission
                    $role_data = $this->roles_model->get($staff_form_data['role']);
                    if (!empty($role_data->permissions)) {
                        $permissions = $role_data->permissions;
                    }
                    $this->staff_model->update_permissions($permissions, $staff_id);

                    #Save action taker
                    $at_data = [
                        'staff_id' => $staff_id,
                        'assistant_id' => $data['action_taker'],
                        'created_at' => $date
                    ];
                    $this->staff_model->save_action_taker_reviewer($at_data);

                    #Save Region and Sub-Region data
                    $region_data = [
                        'staff_id' => $staff_id,
                        'region' => $data['region'],
                        'sub_region' => $data['sub_region'],
                        'created_date' => $date
                    ];
                    $this->staff_model->save_staff_region($region_data, false);
// sms api
                    $this->sendSms($data['phonenumber'],$staff_id,'created');
// sms api
                    $response = array(
                        'success' => true,
                        'message' => "Member has been added successfully"
                    );
                }
            }
            echo json_encode($response);
            die;
        }
    }

    public function get_staff_issues()
    {
        $staff_id = $this->input->post("staffId");
        $this->load->model("issue_model");
        $issues = $this->issue_model->get_staff_issues($staff_id);
        $response = [
            "success" => false,
            "message" => "No issues found!"
        ];
        if ($issues) {
            $response = [
                "success" => true,
                "message" => "Issues found successfully",
                "issues" => $issues
            ];
        }

        echo json_encode($response);
        die;
    }
    public function assign_to_tickets($area, $region, $subregion, $category, $staffid)
    {
        $this->load->model('projects_model');
        for ($i = 0; $i < count($category); $i++) {
            $this->projects_model->assign_at($area, $region, $subregion, $category[$i], $staffid);
        }
    }

    //sms api integration
    public function sendSms($phonenumber,$staff_id,$type){
        $ch  =  curl_init();
        $timeout  =  30; 
        $message="";
        if($type=='created'){
            $message=$this->load->view('sms_templates/onboard',[],true);
            $url='https://www.hawkfy.in/api/mt/SendSMS?user=MCGGGN&password=MCGGGN&senderid=MCGGGN&channel=Trans&DCS=8&flashsms=1&number='.$phonenumber.'&text='.urlencode($message).'&route=05';
            curl_setopt ($ch,CURLOPT_URL, $url);
            curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
            $response = curl_exec($ch) ;
            curl_close($ch) ;
        }

    }
    // end

}

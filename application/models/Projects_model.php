<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Projects_model extends App_Model
{
    private $project_settings;

    public function __construct()
    {
        parent::__construct();

        $project_settings = [
            'available_features',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'comment_on_tasks',
            'view_task_comments',
            'view_task_attachments',
            'view_task_checklist_items',
            'upload_on_tasks',
            'view_task_total_logged_time',
            'view_finance_overview',
            'upload_files',
            'open_discussions',
            'view_milestones',
            'view_gantt',
            'view_timesheets',
            'view_activity_log',
            'view_team_members',
            'hide_tasks_on_main_tasks_table',
            'project_overview'
        ];

        $this->project_settings = hooks()->apply_filters('project_settings', $project_settings);
    }

    public function get_project_statuses()
    {
        $projectStatus = $this->db->query('SELECT * FROM ' . db_prefix() . 'project_status')->result_array();

        $statuses = hooks()->apply_filters('before_get_project_statuses', $projectStatus);

        usort($statuses, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $statuses;
    }

    public function assign_at($area, $region, $subregion, $issue, $staffid)
    {

        $this->db->where('area_id', $area);
        $this->db->where('region_id', $region);
        $this->db->where('subregion_id', $subregion);
        $this->db->where('issue_id', $issue);
        $this->db->where('is_assigned', 0);
        $this->db->select('id');
        $this->db->select('clientid');
        $rows = $this->db->get(db_prefix() . 'projects')->result_array();
        if (!empty($rows)) {
            foreach ($rows as $pid) {
                $this->allocate($pid['id'], $staffid, $pid['clientid']);
                $this->notify_at($pid['id'], $staffid);
            }
        }
    }
    public function allocate($project_id, $staffid, $contact_id)
    {
        $this->db->where('id', $project_id);
        $data = [
            'is_assigned' => 1,
            'status' => 1,
        ];
        $this->db->update(db_prefix() . 'projects', $data);
        $this->update_milestones_of_projects($project_id);
        $this->db->insert(db_prefix() . 'project_members', [
            'project_id' => $project_id,
            'staff_id'   => $staffid,
        ]);
        $additional_data = array(
            'logged_by' => $contact_id,
            'assigned_to' => '',
            'taskId' => '',
            'status' => 1,
            'comment' => ''
        ); 
        // $additional_data = "";
        $this->log_activity($project_id, 'Project assigned to <strong>' . get_staff_full_name($staffid) . '</strong> as per action taken by State Admin <strong>' . get_staff_full_name(get_staff_user_id()) . '</strong>', $staffid, json_encode($additional_data), 1, 'ticket_assigned_after');
    }
    public function unassigned_at_entry($id, $area, $region, $subregion, $issue, $contact)
    {
        $data = [
            'project_id' => $id,
            'area_id' => $area,
            'region_id' => $region,
            'subregion_id' => $subregion,
            'issue_id' => $issue,
            'contact_id' => $contact,
            'is_assigned' => 0,
        ];
        $this->db->insert(db_prefix() . 'unassigned_tickets', $data);
    }
    public function notify_at($project_id, $staffid)
    {
        $this->db->where('id', $project_id);
        $rows = $this->db->get(db_prefix() . 'projects')->result_array();
        if (!empty($rows)) {
            foreach ($rows as $pid) {
                $area = getitemname($pid['area_id'], 'area');
                $region = getitemname($pid['region_id'], 'region');
                $subregion = getitemname($pid['subregion_id'], 'subregion');
                $category = getitemname($pid['issue_id'], 'category');
                send_mail_template(
                    'New_ticket',
                    get_staff_email($staffid, ''),
                    $staffid,
                    1,
                    $project_id,
                    $area,
                    $region,
                    $subregion,
                    $category,
                    $pid['landmark'],
                    $pid['deadline']
                );
                // sms integratiom
                // $this->sendSms($project_id,$staffid,'assigned');
                // sms integration end
            }
        }
    }
    public function sendSms($project_id,$user,$type){
        $due_date=get_project_due_date($project_id);
        $ch  =  curl_init();
        $timeout  =  30; 
        if($type=='assigned'){
            $phonenumber=get_staff_phone($user);
            $phonenumber2=get_staff_phone(get_staff_assistance($user));
            $message=$this->load->view('sms_templates/new_project',['id'=>$project_id,'duedate'=>$due_date],true);
            $url=SMS_API_URL.'&number='.$phonenumber.','.$phonenumber2.'&text='.urlencode($message).'&route=05';
            curl_setopt ($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
            $response = curl_exec($ch) ;
            curl_close($ch) ;
        }
    }
    public function update_milestones_of_projects($project_id)
    {
        $this->db->select('reminderone_days');
        $this->db->select('remindertwo_days');
        $this->db->select('task_days');
        $this->db->select('id');
        $this->db->where('rel_id', $project_id);
        $rows = $this->db->get(db_prefix() . 'tasks')->result_array();
        $project = $rows[0];
        $this->update_proj_table($project_id, $project, $rows);
        if (!empty($rows)) {
            $countforduration = 0;
            $countforcheck = 0;
            foreach ($rows as $milestone) {
                if ($countforcheck > 1) {
                    $date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . (convertint($countforduration) + 1) . ' days'));
                } else {
                    $date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
                }
                $duedate = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
                $reminder1date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
                $reminder2date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
                $task = [
                    'startdate' => date_format($date, "Y-m-d"),
                    'duedate' => date_format(date_add($duedate, date_interval_create_from_date_string('' . $milestone['task_days'] . ' days')), "Y-m-d"),
                    'reminderone_date' => date_format(date_add($reminder1date, date_interval_create_from_date_string('' . $milestone['reminderone_days'] . ' days')), "Y-m-d"),
                    'remindertwo_date' => date_format(date_add($reminder2date, date_interval_create_from_date_string('' . $milestone['remindertwo_days'] . ' days')), "Y-m-d"),
                ];
                if ($countforcheck > 0) {
                    $countforduration = $countforduration + convertint($milestone['task_days']);
                } else {
                    $date = date_create(date('y-m-d'));
                }
                $countforcheck++;
                $this->db->where('rel_id', $project_id);
                $this->db->where('id', $milestone['id']);
                $this->db->update(db_prefix() . 'tasks', $task);
            }
        }
    }
    public function update_proj_table($project_id, $data, $rows)
    {
        if (count($rows) > 1) {
            $milestonetocheck = $rows[1];
            if ($milestonetocheck['task_days'] == 1) {
                $actiondate = date_format(date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $milestonetocheck['task_days'] . ' days')), "Y-m-d");
            } else {
                $actiondate = date('Y-m-d', strtotime(date("Y-m-d") . ' + 2 days'));
            }
        } else {
            $milestonetocheck = $rows[0];
            if ($milestonetocheck['task_days'] == 1) {
                $actiondate = date_format(date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $milestonetocheck['task_days'] . ' days')), "Y-m-d");
            } else {
                $actiondate = date('Y-m-d', strtotime(date("Y-m-d") . ' + 2 days'));
            }
        }
        $updateddata = [
            'start_date' => date("Y-m-d"),
            'action_date' => $actiondate,
            'deadline' => date_format(date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $data['task_days'] . ' days')), "Y-m-d")
        ];
        $this->db->where('id', $project_id);
        $this->db->update(db_prefix() . 'projects', $updateddata);
    }
    public function logTicket($data)
    {
        // if (isset($data['notify_project_members_status_change'])) {
        //     unset($data['notify_project_members_status_change']);
        // }
        // $send_created_email = false;
        // if (isset($data['send_created_email'])) {
        //     unset($data['send_created_email']);
        //     $send_created_email = true;
        // }

        // $send_project_marked_as_finished_email_to_contacts = false;
        // if (isset($data['project_marked_as_finished_email_to_contacts'])) {
        //     unset($data['project_marked_as_finished_email_to_contacts']);
        //     $send_project_marked_as_finished_email_to_contacts = true;
        // }

        if (isset($data['settings'])) {
            $project_settings = $data['settings'];
            unset($data['settings']);
        }
        // if (isset($data['custom_fields'])) {
        //     $custom_fields = $data['custom_fields'];
        //     unset($data['custom_fields']);
        // }
        if (isset($data['progress_from_tasks'])) {
            $data['progress_from_tasks'] = 1;
        } else {
            $data['progress_from_tasks'] = 0;
        }



        $data['start_date'] = $data['start_date'];

        if (!empty($data['deadline'])) {
            $data['deadline'] = $data['deadline'];
        } else {
            unset($data['deadline']);
        }

        $data['project_created'] = date('Y-m-d');
        $data['addedfrom'] = $data['clientid'];
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        // if ($data['billing_type'] == 1) {
        //     $data['project_rate_per_hour'] = 0;
        // } elseif ($data['billing_type'] == 2) {
        //     $data['project_cost'] = 0;
        // } else {
        //     $data['project_rate_per_hour'] = 0;
        //     $data['project_cost']          = 0;
        // }

        // $data['addedfrom'] = get_staff_user_id();

        $data = hooks()->apply_filters('before_add_project', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        $this->db->insert(db_prefix() . 'projects', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (!empty($GLOBALS['current_user']->role_slug_url) && $GLOBALS['current_user']->role_slug_url == 'ar') {
                $additional_data = array(
                    'assigned_by' => get_staff_user_id(),
                    'assigned_to' => get_staff_user_id(),
                    'taskId' => '',
                    'status' => 1,
                    'comment' => '',
                    'parent_ticket_id' => !empty($data['parent_id']) ? $data['parent_id'] : ''
                );
                $this->log_activity($insert_id, 'sub_ticket_created_by_ar', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
            } else {
                $additional_data = array(
                    'logged_by' => $data["clientid"],
                    'assigned_to' => '',
                    'taskId' => '',
                    'status' => $data['status'],
                    'comment' => $data['description'],
                    'parent_ticket_id' => !empty($data['parent_id']) ? $data['parent_id'] : ''
                );
                // '{logged_by:'.$data["clientid"].', assigned_to:, taskId:, comment:'.$data['description'].', status:'.$data['status'].'}'
                if (is_callcenter($this->session->userdata('client_user_id'))) {
                    $this->log_activity($insert_id, 'ticket_created_cc', "", json_encode($additional_data));
                } else {
                    $this->log_activity($insert_id, 'ticket_created', "", json_encode($additional_data));
                }
            }

            handle_tags_save($tags, $insert_id, 'project');

            // if (isset($custom_fields)) {
            //     handle_custom_fields_post($insert_id, $custom_fields);
            // }

            if ($project_members > 0) {
                $this->db->insert(db_prefix() . 'project_members', [
                    'project_id' => $insert_id,
                    'staff_id'   => $project_members,
                ]);
                // $_pm['project_members'] = $project_members;
                // $this->add_edit_members($_pm, $insert_id);

                if (!empty($GLOBALS['current_user']->role_slug_url) && $GLOBALS['current_user']->role_slug_url == 'ar') {
                    $additional_data = array(
                        'assigned_by' => get_staff_user_id(),
                        'assigned_to' => $project_members,
                        'taskId' => '',
                        'status' => 1,
                        'comment' => '',
                        'parent_ticket_id' => ''
                    );
                    $this->log_activity($insert_id, 'ticket_assigned_to_at', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
                } else {
                    $additional_data = array(
                        'logged_by' => $data["clientid"],
                        'assigned_to' => $project_members,
                        'taskId' => '',
                        // 'status' => $data['status'],
                        'status' => 1,
                        'comment' => '',
                        'parent_ticket_id' => !empty($data['parent_id']) ? $data['parent_id'] : ''
                    );
                    // '{logged_by:'.$data["clientid"].', assigned_to:'.$project_members.', taskId:, comment:'.$data['description'].', status:'.$data['status'].'}'
                    $this->log_activity($insert_id, 'ticket_assigned_to_at', $project_members, json_encode($additional_data));
                }
            } else {
                $additional_data = array(
                    'logged_by' => $data["clientid"],
                    'assigned_to' => '',
                    'taskId' => '',
                    'status' => 9,
                    'comment' =>''
                );
                // $additional_data = "";
                $this->log_activity($insert_id, 'No Project Leader assigned to <strong>' . getcatname($data['issue_id']) . '</strong> in <strong>' . getitemname($data['subregion_id'], 'subregion') . '</strong> Raised to State Admin <strong>' . get_staff_full_name(get_area_admin_by_area($data['area_id'])) . '</strong>', get_area_admin_by_area($data['area_id']), json_encode($additional_data), 1, "unassigned");
            }

            $original_settings = $this->get_settings();
            if (isset($project_settings)) {
                $_settings = [];
                $_values   = [];
                foreach ($project_settings as $name => $val) {
                    array_push($_settings, $name);
                    $_values[$name] = $val;
                }
                foreach ($original_settings as $setting) {
                    if ($setting != 'available_features') {
                        if (in_array($setting, $_settings)) {
                            $value_setting = 1;
                        } else {
                            $value_setting = 0;
                        }
                    } else {
                        $tabs         = get_project_tabs_admin();
                        $tab_settings = [];
                        foreach ($_values[$setting] as $tab) {
                            $tab_settings[$tab] = 1;
                        }
                        foreach ($tabs as $tab) {
                            if (!isset($tab['collapse'])) {
                                if (!in_array($tab['slug'], $_values[$setting])) {
                                    $tab_settings[$tab['slug']] = 0;
                                }
                            } else {
                                foreach ($tab['children'] as $tab_dropdown) {
                                    if (!in_array($tab_dropdown['slug'], $_values[$setting])) {
                                        $tab_settings[$tab_dropdown['slug']] = 0;
                                    }
                                }
                            }
                        }
                        $value_setting = serialize($tab_settings);
                    }
                    $this->db->insert(db_prefix() . 'project_settings', [
                        'project_id' => $insert_id,
                        'name'       => $setting,
                        'value'      => $value_setting,
                    ]);
                }
            } else {
                foreach ($original_settings as $setting) {
                    $value_setting = 0;
                    $this->db->insert(db_prefix() . 'project_settings', [
                        'project_id' => $insert_id,
                        'name'       => $setting,
                        'value'      => $value_setting,
                    ]);
                }
            }

            // $this->log_activity($insert_id, 'project_activity_created', $project_members,'{logged_by:'.$data["clientid"].', assigned_to:'.$project_members.', taskId:, comment:'.$data['description'].', status:'.$data['status'].'}');

            // if ($send_created_email == true) {
            //     $this->send_project_customer_email($insert_id, 'project_created_to_customer');
            // }

            // if ($send_project_marked_as_finished_email_to_contacts == true) {
            //     $this->send_project_customer_email($insert_id, 'project_marked_as_finished_to_customer');
            // }

            hooks()->do_action('after_add_project', $insert_id);

            log_activity('New Project Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    public function get_distinct_tasks_timesheets_staff($project_id)
    {
        return $this->db->query('SELECT DISTINCT staff_id FROM ' . db_prefix() . 'taskstimers LEFT JOIN ' . db_prefix() . 'tasks ON ' . db_prefix() . 'tasks.id = ' . db_prefix() . 'taskstimers.task_id WHERE rel_type="project" AND rel_id=' . $this->db->escape_str($project_id))->result_array();
    }

    public function get_distinct_projects_members()
    {
        return $this->db->query('SELECT staff_id, firstname, lastname FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id GROUP by staff_id order by firstname ASC')->result_array();
    }

    public function get_most_used_billing_type()
    {
        return $this->db->query('SELECT billing_type, COUNT(*) AS total_usage
                FROM ' . db_prefix() . 'projects
                GROUP BY billing_type
                ORDER BY total_usage DESC
                LIMIT 1')->row();
    }

    public function timers_started_for_project($project_id, $where = [], $task_timers_where = [])
    {
        $this->db->where($where);
        $this->db->where('end_time IS NULL');
        $this->db->where(db_prefix() . 'tasks.rel_id', $project_id);
        $this->db->where(db_prefix() . 'tasks.rel_type', 'project');
        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id=' . db_prefix() . 'taskstimers.task_id');
        $total = $this->db->count_all_results(db_prefix() . 'taskstimers');

        return $total > 0 ? true : false;
    }

    public function pin_action($id)
    {
        if (total_rows(db_prefix() . 'pinned_projects', [
            'staff_id' => get_staff_user_id(),
            'project_id' => $id,
        ]) == 0) {
            $this->db->insert(db_prefix() . 'pinned_projects', [
                'staff_id'   => get_staff_user_id(),
                'project_id' => $id,
            ]);

            return true;
        }
        $this->db->where('project_id', $id);
        $this->db->where('staff_id', get_staff_user_id());
        $this->db->delete(db_prefix() . 'pinned_projects');

        return true;
    }

    public function get_currency($id)
    {
        $this->load->model('currencies_model');
        $customer_currency = $this->clients_model->get_customer_default_currency(get_client_id_by_project_id($id));
        if ($customer_currency != 0) {
            $currency = $this->currencies_model->get($customer_currency);
        } else {
            $currency = $this->currencies_model->get_base_currency();
        }

        return $currency;
    }

    public function calc_progress($id)
    {
        $this->db->select('progress_from_tasks,progress,status');
        $this->db->where('id', $id);
        $project = $this->db->get(db_prefix() . 'projects')->row();

        if ($project->status == 4) {
            return 100;
        }

        if ($project->progress_from_tasks == 1) {
            return $this->calc_progress_by_tasks($id);
        }

        return $project->progress;
    }

    public function calc_progress_by_tasks($id)
    {
        $total_project_tasks = total_rows(db_prefix() . 'tasks', [
            'rel_type' => 'project',
            'rel_id'   => $id,
        ]);
        $total_finished_tasks = total_rows(db_prefix() . 'tasks', [
            'rel_type' => 'project',
            'rel_id'   => $id,
            'status'   => 5,
        ]);
        $percent = 0;
        if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $percent = 100;
        } else {
            if ($total_project_tasks !== 0) {
                $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
            }
        }

        return $percent;
    }

    public function get_last_project_settings()
    {
        $this->db->select('id');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last_project = $this->db->get(db_prefix() . 'projects')->row();
        if ($last_project) {
            return $this->get_project_settings($last_project->id);
        }

        return [];
    }

    public function get_settings()
    {
        return $this->project_settings;
    }

    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . get_sql_select_client_company());
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'projects.clientid');
        $this->db->order_by('id', 'desc');

        return $this->db->get(db_prefix() . 'projects')->result_array();

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $project = $this->db->get(db_prefix() . 'projects')->row();
            if ($project) {
                $project->shared_vault_entries = $this->clients_model->get_vault_entries($project->clientid, ['share_in_projects' => 1]);
                $settings                      = $this->get_project_settings($id);

                // SYNC NEW TABS
                $tabs                        = get_project_tabs_admin();
                $tabs_flatten                = [];
                $settings_available_features = [];

                $available_features_index = false;
                foreach ($settings as $key => $setting) {
                    if ($setting['name'] == 'available_features') {
                        $available_features_index = $key;
                        // $available_features       = unserialize($setting['value']);
                        $available_features       = $setting['value'];

                        if (is_array($available_features)) {
                            foreach ($available_features as $name => $avf) {
                                $settings_available_features[] = $name;
                            }
                        }
                    }
                }
                foreach ($tabs as $tab) {
                    if (isset($tab['collapse'])) {
                        foreach ($tab['children'] as $d) {
                            $tabs_flatten[] = $d['slug'];
                        }
                    } else {
                        $tabs_flatten[] = $tab['slug'];
                    }
                }
                if (count($settings_available_features) != $tabs_flatten) {
                    foreach ($tabs_flatten as $tab) {
                        if (!in_array($tab, $settings_available_features)) {
                            if ($available_features_index) {
                                $current_available_features_settings = $settings[$available_features_index];
                                $tmp                                 = $current_available_features_settings['value'];
                                $tmp[$tab]                           = 1;
                                $this->db->where('id', $current_available_features_settings['id']);
                                $this->db->update(db_prefix() . 'project_settings', ['value' => serialize($tmp)]);
                            }
                        }
                    }
                }

                $project->settings = new StdClass();

                foreach ($settings as $setting) {
                    $project->settings->{$setting['name']} = $setting['value'];
                }

                $project->client_data = new StdClass();
                $project->client_data = $this->clients_model->get($project->clientid);

                $project            = hooks()->apply_filters('project_get', $project);
                $GLOBALS['project'] = $project;

                return $project;
            }

            return null;
        }

        $this->db->select('*,' . get_sql_select_client_company());
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'projects.clientid');
        $this->db->order_by('id', 'desc');

        return $this->db->get(db_prefix() . 'projects')->result_array();
    }

    public function calculate_total_by_project_hourly_rate($seconds, $hourly_rate)
    {
        $hours       = seconds_to_time_format($seconds);
        $decimal     = sec2qty($seconds);
        $total_money = 0;
        $total_money += ($decimal * $hourly_rate);

        return [
            'hours'       => $hours,
            'total_money' => $total_money,
        ];
    }

    public function calculate_total_by_task_hourly_rate($tasks)
    {
        $total_money    = 0;
        $_total_seconds = 0;

        foreach ($tasks as $task) {
            $seconds = $task['total_logged_time'];
            $_total_seconds += $seconds;
            $total_money += sec2qty($seconds) * $task['hourly_rate'];
        }

        return [
            'total_money'   => $total_money,
            'total_seconds' => $_total_seconds,
        ];
    }

    public function get_tasks($id, $where = [], $apply_restrictions = false, $count = false)
    {
        $has_permission                    = has_permission('tasks', '', 'view');
        $show_all_tasks_for_project_member = get_option('show_all_tasks_for_project_member');

        if (is_client_logged_in()) {
            $this->db->where('visible_to_client', 1);
        }

        $select = implode(', ', prefixed_table_fields_array(db_prefix() . 'tasks')) . ',' . db_prefix() . 'milestones.name as milestone_name,
        (SELECT SUM(CASE
            WHEN end_time is NULL THEN ' . time() . '-start_time
            ELSE end_time-start_time
            END) FROM ' . db_prefix() . 'taskstimers WHERE task_id=' . db_prefix() . 'tasks.id) as total_logged_time,
           ' . get_sql_select_task_assignees_ids() . ' as assignees_ids
        ';

        if (!is_client_logged_in() && is_staff_logged_in()) {
            $select .= ',(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as current_user_is_assigned';
        }
        $this->db->select($select);

        $this->db->join(db_prefix() . 'milestones', db_prefix() . 'milestones.id = ' . db_prefix() . 'tasks.milestone', 'left');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'project');
        if ($apply_restrictions == true) {
            if (!is_client_logged_in() && !$has_permission && $show_all_tasks_for_project_member == 0) {
                $this->db->where('(
                    ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . get_staff_user_id() . ')
                    OR ' . db_prefix() . 'tasks.id IN(SELECT taskid FROM ' . db_prefix() . 'task_followers WHERE staffid=' . get_staff_user_id() . ')
                    OR is_public = 1
                    OR (addedfrom =' . get_staff_user_id() . ' AND is_added_from_contact = 0)
                    )');
            }
        }

        $this->db->where($where);

        // Milestones kanban order
        // Request is admin/projects/milestones_kanban
        if ($this->uri->segment(3) == 'milestones_kanban') {
            $this->db->order_by('milestone_order', 'asc');
        } else {
            $orderByString = hooks()->apply_filters('project_tasks_array_default_order', 'FIELD(status, 5), duedate IS NULL ASC, duedate');
            $this->db->order_by($orderByString, '', false);
        }

        if ($count == false) {
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
        } else {
            $tasks = $this->db->count_all_results(db_prefix() . 'tasks');
        }

        return $tasks;
    }

    public function cancel_recurring_tasks($id)
    {
        $this->db->where('rel_type', 'project');
        $this->db->where('rel_id', $id);
        $this->db->where('recurring', 1);
        $this->db->where('(cycles != total_cycles OR cycles=0)');

        $this->db->update(db_prefix() . 'tasks', [
            'recurring_type'      => null,
            'repeat_every'        => 0,
            'cycles'              => 0,
            'recurring'           => 0,
            'custom_recurring'    => 0,
            'last_recurring_date' => null,
        ]);
    }

    public function do_milestones_kanban_query($milestone_id, $project_id, $page = 1, $where = [], $count = false)
    {
        $where['milestone'] = $milestone_id;

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * get_option('tasks_kanban_limit'));
                $this->db->limit(get_option('tasks_kanban_limit'), $position);
            } else {
                $this->db->limit(get_option('tasks_kanban_limit'));
            }
        }

        return $this->get_tasks($project_id, $where, true, $count);
    }

    public function get_files($project_id)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('project_id', $project_id);

        return $this->db->get(db_prefix() . 'project_files')->result_array();
    }

    public function get_file($id, $project_id = false)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix() . 'project_files')->row();

        if ($file && $project_id) {
            if ($file->project_id != $project_id) {
                return false;
            }
        }

        return $file;
    }

    public function update_file_data($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);
        $this->db->update(db_prefix() . 'project_files', $data);
    }

    public function change_file_visibility($id, $visible)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'visible_to_customer' => $visible,
        ]);
    }

    public function change_activity_visibility($id, $visible)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'project_activity', [
            'visible_to_customer' => $visible,
        ]);
    }

    public function remove_file($id, $logActivity = true)
    {
        hooks()->do_action('before_remove_project_file', $id);

        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix() . 'project_files')->row();
        if ($file) {
            if (empty($file->external)) {
                $path     = get_upload_path_by_type('project') . $file->project_id . '/';
                $fullPath = $path . $file->file_name;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                    $fname     = pathinfo($fullPath, PATHINFO_FILENAME);
                    $fext      = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $thumbPath = $path . $fname . '_thumb.' . $fext;

                    if (file_exists($thumbPath)) {
                        unlink($thumbPath);
                    }
                }
            }

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'project_files');
            if ($logActivity) {
                $this->log_activity($file->project_id, 'project_activity_project_file_removed', $file->file_name, $file->visible_to_customer);
            }

            // Delete discussion comments
            $this->_delete_discussion_comments($id, 'file');

            if (is_dir(get_upload_path_by_type('project') . $file->project_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('project') . $file->project_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('project') . $file->project_id);
                }
            }

            return true;
        }

        return false;
    }

    public function get_project_overview_weekly_chart_data($id, $type = 'this_week')
    {
        $billing_type = get_project_billing_type($id);
        $chart        = [];

        $has_permission_create = has_permission('projects', '', 'create');
        // If don't have permission for projects create show only bileld time
        if (!$has_permission_create) {
            $timesheets_type = 'total_logged_time_only';
        } else {
            if ($billing_type == 2 || $billing_type == 3) {
                $timesheets_type = 'billable_unbilled';
            } else {
                $timesheets_type = 'total_logged_time_only';
            }
        }

        $chart['data']             = [];
        $chart['data']['labels']   = [];
        $chart['data']['datasets'] = [];

        $chart['data']['datasets'][] = [
            'label'           => ($timesheets_type == 'billable_unbilled' ? str_replace(':', '', _l('project_overview_billable_hours')) : str_replace(':', '', _l('project_overview_logged_hours'))),
            'data'            => [],
            'backgroundColor' => [],
            'borderColor'     => [],
            'borderWidth'     => 1,
        ];

        if ($timesheets_type == 'billable_unbilled') {
            $chart['data']['datasets'][] = [
                'label'           => str_replace(':', '', _l('project_overview_unbilled_hours')),
                'data'            => [],
                'backgroundColor' => [],
                'borderColor'     => [],
                'borderWidth'     => 1,
            ];
        }

        $temp_weekdays_data = [];
        $weeks              = [];
        $where_time         = '';

        if ($type == 'this_month') {
            $beginThisMonth = date('Y-m-01');
            $endThisMonth   = date('Y-m-t 23:59:59');

            $weeks_split_start = date('Y-m-d', strtotime($beginThisMonth));
            $weeks_split_end   = date('Y-m-d', strtotime($endThisMonth));

            $where_time = 'start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth);
        } elseif ($type == 'last_month') {
            $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
            $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));

            $weeks_split_start = date('Y-m-d', strtotime($beginLastMonth));
            $weeks_split_end   = date('Y-m-d', strtotime($endLastMonth));

            $where_time = 'start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth);
        } elseif ($type == 'last_week') {
            $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
            $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
            $where_time    = 'start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek);
        } else {
            $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
            $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            $where_time    = 'start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek);
        }

        if ($type == 'this_week' || $type == 'last_week') {
            foreach (get_weekdays() as $day) {
                array_push($chart['data']['labels'], $day);
            }
            $weekDay = date('w', strtotime(date('Y-m-d H:i:s')));
            $i       = 0;
            foreach (get_weekdays_original() as $day) {
                if ($weekDay != '0') {
                    $chart['data']['labels'][$i] = date('d', strtotime($day . ' ' . str_replace('_', ' ', $type))) . ' - ' . $chart['data']['labels'][$i];
                } else {
                    if ($type == 'this_week') {
                        $strtotime = 'last ' . $day;
                        if ($day == 'Sunday') {
                            $strtotime = 'sunday this week';
                        }
                        $chart['data']['labels'][$i] = date('d', strtotime($strtotime)) . ' - ' . $chart['data']['labels'][$i];
                    } else {
                        $strtotime                   = $day . ' last week';
                        $chart['data']['labels'][$i] = date('d', strtotime($strtotime)) . ' - ' . $chart['data']['labels'][$i];
                    }
                }
                $i++;
            }
        } elseif ($type == 'this_month' || $type == 'last_month') {
            $weeks_split_start = new DateTime($weeks_split_start);
            $weeks_split_end   = new DateTime($weeks_split_end);
            $weeks             = get_weekdays_between_dates($weeks_split_start, $weeks_split_end);
            $total_weeks       = count($weeks);
            for ($i = 1; $i <= $total_weeks; $i++) {
                array_push($chart['data']['labels'], split_weeks_chart_label($weeks, $i));
            }
        }

        $loop_break = ($timesheets_type == 'billable_unbilled') ? 2 : 1;

        for ($i = 0; $i < $loop_break; $i++) {
            $temp_weekdays_data = [];
            // Store the weeks in new variable for each loop to prevent duplicating
            $tmp_weeks = $weeks;


            $color = '3, 169, 244';

            $where = 'task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type = "project" AND rel_id = "' . $this->db->escape_str($id) . '"';

            if ($timesheets_type != 'total_logged_time_only') {
                $where .= ' AND billable=1';
                if ($i == 1) {
                    $color = '252, 45, 66';
                    $where .= ' AND billed = 0';
                }
            }

            $where .= ')';
            $this->db->where($where_time);
            $this->db->where($where);
            if (!$has_permission_create) {
                $this->db->where('staff_id', get_staff_user_id());
            }
            $timesheets = $this->db->get(db_prefix() . 'taskstimers')->result_array();

            foreach ($timesheets as $t) {
                $total_logged_time = 0;
                if ($t['end_time'] == null) {
                    $total_logged_time = time() - $t['start_time'];
                } else {
                    $total_logged_time = $t['end_time'] - $t['start_time'];
                }

                if ($type == 'this_week' || $type == 'last_week') {
                    $weekday = date('N', $t['start_time']);
                    if (!isset($temp_weekdays_data[$weekday])) {
                        $temp_weekdays_data[$weekday] = 0;
                    }
                    $temp_weekdays_data[$weekday] += $total_logged_time;
                } else {
                    // months - this and last
                    $w = 1;
                    foreach ($tmp_weeks as $week) {
                        $start_time_date = strftime('%Y-%m-%d', $t['start_time']);
                        if (!isset($tmp_weeks[$w]['total'])) {
                            $tmp_weeks[$w]['total'] = 0;
                        }
                        if (in_array($start_time_date, $week)) {
                            $tmp_weeks[$w]['total'] += $total_logged_time;
                        }
                        $w++;
                    }
                }
            }

            if ($type == 'this_week' || $type == 'last_week') {
                ksort($temp_weekdays_data);
                for ($w = 1; $w <= 7; $w++) {
                    $total_logged_time = 0;
                    if (isset($temp_weekdays_data[$w])) {
                        $total_logged_time = $temp_weekdays_data[$w];
                    }
                    array_push($chart['data']['datasets'][$i]['data'], sec2qty($total_logged_time));
                    array_push($chart['data']['datasets'][$i]['backgroundColor'], 'rgba(' . $color . ',0.8)');
                    array_push($chart['data']['datasets'][$i]['borderColor'], 'rgba(' . $color . ',1)');
                }
            } else {
                // loop over $tmp_weeks because the unbilled is shown twice because we auto increment twice
                // months - this and last
                foreach ($tmp_weeks as $week) {
                    $total = 0;
                    if (isset($week['total'])) {
                        $total = $week['total'];
                    }
                    $total_logged_time = $total;
                    array_push($chart['data']['datasets'][$i]['data'], sec2qty($total_logged_time));
                    array_push($chart['data']['datasets'][$i]['backgroundColor'], 'rgba(' . $color . ',0.8)');
                    array_push($chart['data']['datasets'][$i]['borderColor'], 'rgba(' . $color . ',1)');
                }
            }
        }

        return $chart;
    }

    public function get_gantt_data($project_id, $type = 'milestones', $taskStatus = null)
    {
        $type_data = [];
        if ($type == 'milestones') {
            $type_data[] = [
                'name' => _l('milestones_uncategorized'),
                'id'   => 0,
            ];
            $_milestones = $this->get_milestones($project_id);
            foreach ($_milestones as $m) {
                $type_data[] = $m;
            }
        } elseif ($type == 'members') {
            $type_data[] = [
                'name'     => _l('task_list_not_assigned'),
                'staff_id' => 0,
            ];
            $_members = $this->get_project_members($project_id);
            foreach ($_members as $m) {
                $type_data[] = $m;
            }
        } else {
            if (!$taskStatus) {
                $statuses = $this->tasks_model->get_statuses();
                foreach ($statuses as $status) {
                    $type_data[] = $status['id'];
                }
            } else {
                $type_data[] = $taskStatus;
            }
        }

        $gantt_data     = [];
        $has_permission = has_permission('tasks', '', 'view');
        foreach ($type_data as $data) {
            if ($type == 'milestones') {
                $tasks = $this->get_tasks($project_id, 'milestone=' . $this->db->escape_str($data['id']) . ($taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $this->db->escape_str($taskStatus) : ''), true);
                $name  = $data['name'];
            } elseif ($type == 'members') {
                if ($data['staff_id'] != 0) {
                    $tasks = $this->get_tasks($project_id, db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid=' . $data['staff_id'] . ')' . ($taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $taskStatus : ''), true);
                    $name  = get_staff_full_name($data['staff_id']);
                } else {
                    $tasks = $this->get_tasks($project_id, db_prefix() . 'tasks.id NOT IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned)' . ($taskStatus ? ' AND ' . db_prefix() . 'tasks.status=' . $taskStatus : ''), true);
                    $name  = $data['name'];
                }
            } else {
                $tasks = $this->get_tasks($project_id, [
                    'status' => $data,
                ], true);

                $name = format_task_status($data, false, true);
            }

            if (count($tasks) > 0) {
                $data         = get_task_array_gantt_data($tasks[0]);
                $data['name'] = $name;

                $gantt_data[] = $data;
                unset($tasks[0]);

                foreach ($tasks as $task) {
                    $gantt_data[] = get_task_array_gantt_data($task);
                }
            }
        }

        return $gantt_data;
    }

    public function get_all_projects_gantt_data($filters = [])
    {
        $statuses   = $this->get_project_statuses();
        $gantt_data = [];

        $statusesIds = [];
        foreach ($statuses as $status) {
            if (!in_array($status['id'], $filters['status'])) {
                continue;
            }

            if (!has_permission('projects', '', 'view')) {
                $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
            }

            if ($filters['member']) {
                $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . $this->db->escape_str($filters['member']) . ')');
            }

            $this->db->where('status', $status['id']);
            $this->db->order_by('deadline IS NULL ASC, deadline', '', false);
            $projects = $this->db->get(db_prefix() . 'projects')->result_array();

            foreach ($projects as $project) {
                $tasks = $this->get_tasks($project['id'], [], true);

                $data             = [];
                $data['values']   = [];
                $values           = [];
                $data['desc']     = ' '; // right white background
                $data['name'] = $project['name']; // the heading

                $values['from'] = strftime('%Y/%m/%d', strtotime($project['start_date']));
                $values['to']       = strftime('%Y/%m/%d', strtotime($project['deadline']));
                $values['desc']     = '';
                $values['label']    = $project['name'];

                $values['dataObj'] = [
                    'project_id' => $project['id'],
                ];
                $values['customClass'] = 'ganttProject';
                $data['values'][]      = $values;
                $gantt_data[]          = $data;

                if (count($tasks) > 0) {
                    foreach ($tasks as $task) {
                        $gantt_data[] = get_task_array_gantt_data($task);
                    }
                }
            }
        }

        return $gantt_data;
    }

    public function calc_milestone_logged_time($project_id, $id)
    {
        $total = [];
        $tasks = $this->get_tasks($project_id, [
            'milestone' => $id,
        ]);

        foreach ($tasks as $task) {
            $total[] = $task['total_logged_time'];
        }

        return array_sum($total);
    }

    public function total_logged_time($id)
    {
        $q = $this->db->query('
            SELECT SUM(CASE
                WHEN end_time is NULL THEN ' . time() . '-start_time
                ELSE end_time-start_time
                END) as total_logged_time
            FROM ' . db_prefix() . 'taskstimers
            WHERE task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id=' . $this->db->escape_str($id) . ')')
            ->row();

        return $q->total_logged_time;
    }

    public function get_milestones($project_id)
    {
        $this->db->select('*, (SELECT COUNT(id) FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id=' . $this->db->escape_str($project_id) . ' and milestone=' . db_prefix() . 'milestones.id) as total_tasks, (SELECT COUNT(id) FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id=' . $this->db->escape_str($project_id) . ' and milestone=' . db_prefix() . 'milestones.id AND status=5) as total_finished_tasks');
        $this->db->where('project_id', $project_id);
        $this->db->order_by('milestone_order', 'ASC');
        $milestones = $this->db->get(db_prefix() . 'milestones')->result_array();
        $i          = 0;
        foreach ($milestones as $milestone) {
            $milestones[$i]['total_logged_time'] = $this->calc_milestone_logged_time($project_id, $milestone['id']);
            $i++;
        }


        return $milestones;
    }

    public function add_milestone($data)
    {
        $data['due_date']    = to_sql_date($data['due_date']);
        $data['datecreated'] = date('Y-m-d');
        $data['description'] = nl2br($data['description']);

        if (isset($data['description_visible_to_customer'])) {
            $data['description_visible_to_customer'] = 1;
        } else {
            $data['description_visible_to_customer'] = 0;
        }
        $this->db->insert(db_prefix() . 'milestones', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $insert_id);
            $milestone = $this->db->get(db_prefix() . 'milestones')->row();
            $project   = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_created_milestone', $milestone->name, $show_to_customer);
            log_activity('Project Milestone Created [ID:' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_milestone($data, $id)
    {
        $this->db->where('id', $id);
        $milestone           = $this->db->get(db_prefix() . 'milestones')->row();
        $data['due_date']    = to_sql_date($data['due_date']);
        $data['description'] = nl2br($data['description']);

        if (isset($data['description_visible_to_customer'])) {
            $data['description_visible_to_customer'] = 1;
        } else {
            $data['description_visible_to_customer'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'milestones', $data);
        if ($this->db->affected_rows() > 0) {
            $project = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_resolve_milestone', $milestone->name, $show_to_customer);
            log_activity('Project Milestone Updated [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function update_task_milestone($data)
    {
        $this->db->where('id', $data['task_id']);
        $this->db->update(db_prefix() . 'tasks', [
            'milestone' => $data['milestone_id'],
        ]);

        foreach ($data['order'] as $order) {
            $this->db->where('id', $order[0]);
            $this->db->update(db_prefix() . 'tasks', [
                'milestone_order' => $order[1],
            ]);
        }
    }

    public function update_milestones_order($data)
    {
        foreach ($data['order'] as $status) {
            $this->db->where('id', $status[0]);
            $this->db->update(db_prefix() . 'milestones', [
                'milestone_order' => $status[1],
            ]);
        }
    }

    public function update_milestone_color($data)
    {
        $this->db->where('id', $data['milestone_id']);
        $this->db->update(db_prefix() . 'milestones', [
            'color' => $data['color'],
        ]);
    }

    public function delete_milestone($id)
    {
        $this->db->where('id', $id);
        $milestone = $this->db->get(db_prefix() . 'milestones')->row();
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'milestones');
        if ($this->db->affected_rows() > 0) {
            $project = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_deleted_milestone', $milestone->name, $show_to_customer);
            $this->db->where('milestone', $id);
            $this->db->update(db_prefix() . 'tasks', [
                'milestone' => 0,
            ]);
            log_activity('Project Milestone Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    public function add($data)
    {
        if (isset($data['notify_project_members_status_change'])) {
            unset($data['notify_project_members_status_change']);
        }
        $send_created_email = false;
        if (isset($data['send_created_email'])) {
            unset($data['send_created_email']);
            $send_created_email = true;
        }

        $send_project_marked_as_finished_email_to_contacts = false;
        if (isset($data['project_marked_as_finished_email_to_contacts'])) {
            unset($data['project_marked_as_finished_email_to_contacts']);
            $send_project_marked_as_finished_email_to_contacts = true;
        }

        if (isset($data['settings'])) {
            $project_settings = $data['settings'];
            unset($data['settings']);
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        if (isset($data['progress_from_tasks'])) {
            $data['progress_from_tasks'] = 1;
        } else {
            $data['progress_from_tasks'] = 0;
        }



        $data['start_date'] = to_sql_date($data['start_date']);

        if (!empty($data['deadline'])) {
            $data['deadline'] = to_sql_date($data['deadline']);
        } else {
            unset($data['deadline']);
        }

        $data['project_created'] = date('Y-m-d');
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        if ($data['billing_type'] == 1) {
            $data['project_rate_per_hour'] = 0;
        } elseif ($data['billing_type'] == 2) {
            $data['project_cost'] = 0;
        } else {
            $data['project_rate_per_hour'] = 0;
            $data['project_cost']          = 0;
        }

        $data['addedfrom'] = get_staff_user_id();

        $data = hooks()->apply_filters('before_add_project', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        $this->db->insert(db_prefix() . 'projects', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            handle_tags_save($tags, $insert_id, 'project');

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            if (isset($project_members)) {
                $_pm['project_members'] = $project_members;
                $this->add_edit_members($_pm, $insert_id);
            }

            $original_settings = $this->get_settings();
            if (isset($project_settings)) {
                $_settings = [];
                $_values   = [];
                foreach ($project_settings as $name => $val) {
                    array_push($_settings, $name);
                    $_values[$name] = $val;
                }
                foreach ($original_settings as $setting) {
                    if ($setting != 'available_features') {
                        if (in_array($setting, $_settings)) {
                            $value_setting = 1;
                        } else {
                            $value_setting = 0;
                        }
                    } else {
                        $tabs         = get_project_tabs_admin();
                        $tab_settings = [];
                        foreach ($_values[$setting] as $tab) {
                            $tab_settings[$tab] = 1;
                        }
                        foreach ($tabs as $tab) {
                            if (!isset($tab['collapse'])) {
                                if (!in_array($tab['slug'], $_values[$setting])) {
                                    $tab_settings[$tab['slug']] = 0;
                                }
                            } else {
                                foreach ($tab['children'] as $tab_dropdown) {
                                    if (!in_array($tab_dropdown['slug'], $_values[$setting])) {
                                        $tab_settings[$tab_dropdown['slug']] = 0;
                                    }
                                }
                            }
                        }
                        $value_setting = serialize($tab_settings);
                    }
                    $this->db->insert(db_prefix() . 'project_settings', [
                        'project_id' => $insert_id,
                        'name'       => $setting,
                        'value'      => $value_setting,
                    ]);
                }
            } else {
                foreach ($original_settings as $setting) {
                    $value_setting = 0;
                    $this->db->insert(db_prefix() . 'project_settings', [
                        'project_id' => $insert_id,
                        'name'       => $setting,
                        'value'      => $value_setting,
                    ]);
                }
            }

            $this->log_activity($insert_id, 'project_activity_created');

            if ($send_created_email == true) {
                $this->send_project_customer_email($insert_id, 'project_created_to_customer');
            }

            if ($send_project_marked_as_finished_email_to_contacts == true) {
                $this->send_project_customer_email($insert_id, 'project_marked_as_finished_to_customer');
            }

            hooks()->do_action('after_add_project', $insert_id);

            log_activity('New Project Created [ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function update($data, $id)
    {
        $this->db->select('status');
        $this->db->where('id', $id);
        $old_status = $this->db->get(db_prefix() . 'projects')->row()->status;

        $send_created_email = false;
        if (isset($data['send_created_email'])) {
            unset($data['send_created_email']);
            $send_created_email = true;
        }

        $send_project_marked_as_finished_email_to_contacts = false;
        if (isset($data['project_marked_as_finished_email_to_contacts'])) {
            unset($data['project_marked_as_finished_email_to_contacts']);
            $send_project_marked_as_finished_email_to_contacts = true;
        }

        $original_project = $this->get($id);

        if (isset($data['notify_project_members_status_change'])) {
            $notify_project_members_status_change = true;
            unset($data['notify_project_members_status_change']);
        }
        $affectedRows = 0;
        if (!isset($data['settings'])) {
            $this->db->where('project_id', $id);
            $this->db->update(db_prefix() . 'project_settings', [
                'value' => 0,
            ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $_settings = [];
            $_values   = [];

            foreach ($data['settings'] as $name => $val) {
                array_push($_settings, $name);
                $_values[$name] = $val;
            }

            unset($data['settings']);
            $original_settings = $this->get_project_settings($id);

            foreach ($original_settings as $setting) {
                if ($setting['name'] != 'available_features') {
                    if (in_array($setting['name'], $_settings)) {
                        $value_setting = 1;
                    } else {
                        $value_setting = 0;
                    }
                } else {
                    $tabs         = get_project_tabs_admin();
                    $tab_settings = [];
                    foreach ($_values[$setting['name']] as $tab) {
                        $tab_settings[$tab] = 1;
                    }
                    foreach ($tabs as $tab) {
                        if (!isset($tab['collapse'])) {
                            if (!in_array($tab['slug'], $_values[$setting['name']])) {
                                $tab_settings[$tab['slug']] = 0;
                            }
                        } else {
                            foreach ($tab['children'] as $tab_dropdown) {
                                if (!in_array($tab_dropdown['slug'], $_values[$setting['name']])) {
                                    $tab_settings[$tab_dropdown['slug']] = 0;
                                }
                            }
                        }
                    }
                    $value_setting = serialize($tab_settings);
                }

                $this->db->where('project_id', $id);
                $this->db->where('name', $setting['name']);
                $this->db->update(db_prefix() . 'project_settings', [
                    'value' => $value_setting,
                ]);

                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if ($old_status == 4 && $data['status'] != 4) {
            $data['date_finished'] = null;
        } elseif (isset($data['date_finished'])) {
            $data['date_finished'] = to_sql_date($data['date_finished'], true);
        }

        if (isset($data['progress_from_tasks'])) {
            $data['progress_from_tasks'] = 1;
        } else {
            $data['progress_from_tasks'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (!empty($data['deadline'])) {
            $data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $data['deadline'] = null;
        }

        $data['start_date'] = to_sql_date($data['start_date']);
        if ($data['billing_type'] == 1) {
            $data['project_rate_per_hour'] = 0;
        } elseif ($data['billing_type'] == 2) {
            $data['project_cost'] = 0;
        } else {
            $data['project_rate_per_hour'] = 0;
            $data['project_cost']          = 0;
        }
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        $_pm = [];
        if (isset($project_members)) {
            $_pm['project_members'] = $project_members;
        }
        if ($this->add_edit_members($_pm, $id)) {
            $affectedRows++;
        }
        if (isset($data['mark_all_tasks_as_completed'])) {
            $mark_all_tasks_as_completed = true;
            unset($data['mark_all_tasks_as_completed']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'project')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        if (isset($data['cancel_recurring_tasks'])) {
            unset($data['cancel_recurring_tasks']);
            $this->cancel_recurring_tasks($id);
        }

        $data = hooks()->apply_filters('before_update_project', $data, $id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'projects', $data);

        if ($this->db->affected_rows() > 0) {
            if (isset($mark_all_tasks_as_completed)) {
                $this->_mark_all_project_tasks_as_completed($id);
            }
            $affectedRows++;
        }

        if ($send_created_email == true) {
            if ($this->send_project_customer_email($id, 'project_created_to_customer')) {
                $affectedRows++;
            }
        }

        if ($send_project_marked_as_finished_email_to_contacts == true) {
            if ($this->send_project_customer_email($id, 'project_marked_as_finished_to_customer')) {
                $affectedRows++;
            }
        }
        if ($affectedRows > 0) {
            $this->log_activity($id, 'project_activity_updated');
            log_activity('Project Updated [ID: ' . $id . ']');

            if ($original_project->status != $data['status']) {
                hooks()->do_action('project_status_changed', [
                    'status'     => $data['status'],
                    'project_id' => $id,
                ]);
                // Give space this log to be on top
                sleep(1);
                if ($data['status'] == 4) {
                    $this->log_activity($id, 'project_marked_as_finished');
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'projects', ['date_finished' => date('Y-m-d H:i:s')]);
                } else {
                    $this->log_activity($id, 'project_status_updated', '<b><lang>project_status_' . $data['status'] . '</lang></b>');
                }

                if (isset($notify_project_members_status_change)) {
                    $this->_notify_project_members_status_change($id, $original_project->status, $data['status']);
                }
            }
            hooks()->do_action('after_update_project', $id);

            return true;
        }

        return false;
    }

    /**
     * Simplified function to send non complicated email templates for project contacts
     * @param  mixed $id project id
     * @return boolean
     */
    public function send_project_customer_email($id, $template)
    {
        $this->db->select('clientid');
        $this->db->where('id', $id);
        $clientid = $this->db->get(db_prefix() . 'projects')->row()->clientid;

        $sent     = false;
        $contacts = $this->clients_model->get_contacts($clientid, ['active' => 1, 'project_emails' => 1]);
        foreach ($contacts as $contact) {
            if (send_mail_template($template, $id, $clientid, $contact)) {
                $sent = true;
            }
        }

        return $sent;
    }

    public function mark_as($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['project_id']);
        $old_status = $this->db->get(db_prefix() . 'projects')->row()->status;

        $this->db->where('id', $data['project_id']);
        $this->db->update(db_prefix() . 'projects', [
            'status' => $data['status_id'],
        ]);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('project_status_changed', [
                'status'     => $data['status_id'],
                'project_id' => $data['project_id'],
            ]);

            if ($data['status_id'] == 3) {
                // $this->log_activity($data['project_id'], 'project_marked_as_finished');
                $this->db->where('id', $data['project_id']);
                $this->db->update(db_prefix() . 'projects', ['date_finished' => date('Y-m-d H:i:s')]);
            } else if ($data['status_id'] == 6) {
                // $this->log_activity($data['project_id'], 'reopen_ticket', '<b><lang>project_status_' . $data['status_id'] . '</lang></b>');
                $this->db->where('id', $data['project_id']);
                $this->db->update(db_prefix() . 'projects', ['date_finished' => null]);

                //update milestone
                $this->load->model('tasks_model');
                $this->tasks_model->update_reopen_task_status($data['project_id']);
            } else {
                // $this->log_activity($data['project_id'], 'project_status_updated', '<b><lang>project_status_' . $data['status_id'] . '</lang></b>');
                if ($old_status == 3) {
                    $this->db->update(db_prefix() . 'projects', ['date_finished' => null]);
                }
            }

            // if ($data['notify_project_members_status_change'] == 1) {
            //     $this->_notify_project_members_status_change($data['project_id'], $old_status, $data['status_id']);
            // }

            // if ($data['mark_all_tasks_as_completed'] == 1) {
            //     $this->_mark_all_project_tasks_as_completed($data['project_id']);
            // }

            // if (isset($data['cancel_recurring_tasks']) && $data['cancel_recurring_tasks'] == 'true') {
            //     $this->cancel_recurring_tasks($data['project_id']);
            // }

            // if (isset($data['send_project_marked_as_finished_email_to_contacts'])
            //     && $data['send_project_marked_as_finished_email_to_contacts'] == 1) {
            //     $this->send_project_customer_email($data['project_id'], 'project_marked_as_finished_to_customer');
            // }

            return true;
        }


        return false;
    }

    private function _notify_project_members_status_change($id, $old_status, $new_status)
    {
        $members       = $this->get_project_members($id);
        $notifiedUsers = [];
        foreach ($members as $member) {
            if ($member['staff_id'] != get_staff_user_id()) {
                $notified = add_notification([
                    'fromuserid'      => get_staff_user_id(),
                    'description'     => 'not_project_status_updated',
                    'link'            => 'projects/view/' . $id,
                    'touserid'        => $member['staff_id'],
                    'additional_data' => serialize([
                        '<lang>project_status_' . $old_status . '</lang>',
                        '<lang>project_status_' . $new_status . '</lang>',
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }

    private function _mark_all_project_tasks_as_completed($id)
    {
        $this->db->where('rel_type', 'project');
        $this->db->where('rel_id', $id);
        $this->db->update(db_prefix() . 'tasks', [
            'status'       => 5,
            'datefinished' => date('Y-m-d H:i:s'),
        ]);
        $tasks = $this->get_tasks($id);
        foreach ($tasks as $task) {
            $this->db->where('task_id', $task['id']);
            $this->db->where('end_time IS NULL');
            $this->db->update(db_prefix() . 'taskstimers', [
                'end_time' => time(),
            ]);
        }
        $this->log_activity($id, 'project_activity_marked_all_tasks_as_complete');
    }

    public function add_edit_members($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
        }

        $new_project_members_to_receive_email = [];
        $this->db->select('name,clientid');
        $this->db->where('id', $id);
        $project      = $this->db->get(db_prefix() . 'projects')->row();
        $project_name = $project->name;
        $client_id    = $project->clientid;

        $project_members_in = $this->get_project_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('project_id', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete(db_prefix() . 'project_members');
                        if ($this->db->affected_rows() > 0) {
                            $this->db->where('staff_id', $project_member['staff_id']);
                            $this->db->where('project_id', $id);
                            $this->db->delete(db_prefix() . 'pinned_projects');

                            // $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('project_id', $id);
                    $this->db->delete(db_prefix() . 'project_members');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    $this->db->where('project_id', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get(db_prefix() . 'project_members')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert(db_prefix() . 'project_members', [
                            'project_id' => $id,
                            'staff_id'   => $staff_id,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification([
                                    'fromuserid'      => get_staff_user_id(),
                                    'description'     => 'not_staff_added_as_project_member',
                                    'link'            => 'projects/view/' . $id,
                                    'touserid'        => $staff_id,
                                    'additional_data' => serialize([
                                        $project_name,
                                    ]),
                                ]);
                                array_push($new_project_members_to_receive_email, $staff_id);
                                if ($notified) {
                                    array_push($notifiedUsers, $staff_id);
                                }
                            }


                            // $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                            $affectedRows++;
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        } else {
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    if (empty($staff_id)) {
                        continue;
                    }
                    $this->db->insert(db_prefix() . 'project_members', [
                        'project_id' => $id,
                        'staff_id'   => $staff_id,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification([
                                'fromuserid'      => get_staff_user_id(),
                                'description'     => 'not_staff_added_as_project_member',
                                'link'            => 'projects/view/' . $id,
                                'touserid'        => $staff_id,
                                'additional_data' => serialize([
                                    $project_name,
                                ]),
                            ]);
                            array_push($new_project_members_to_receive_email, $staff_id);
                            if ($notifiedUsers) {
                                array_push($notifiedUsers, $staff_id);
                            }
                        }
                        // $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                        $affectedRows++;
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        }

        if (count($new_project_members_to_receive_email) > 0) {
            $all_members = $this->get_project_members($id);
            foreach ($all_members as $data) {
                if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
                    send_mail_template('project_staff_added_as_member', $data, $id, $client_id);
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function is_member($project_id, $staff_id = '')
    {
        if (!is_numeric($staff_id)) {
            $staff_id = get_staff_user_id();
        }
        $member = total_rows(db_prefix() . 'project_members', [
            'staff_id'   => $staff_id,
            'project_id' => $project_id,
        ]);
        if ($member > 0) {
            return true;
        }

        return false;
    }

    public function get_projects_for_ticket($client_id)
    {
        return $this->get('', [
            'clientid' => $client_id,
        ]);
    }

    public function get_project_settings($project_id)
    {
        $this->db->where('project_id', $project_id);

        return $this->db->get(db_prefix() . 'project_settings')->result_array();
    }

    public function get_project_members($id, $role = "")
    {
        $this->db->select('email,project_id,staff_id');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id');
        if(!empty($role)){
            $this->db->select('roles.*');
            $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid=' . db_prefix() . 'staff.role');
            $this->db->where('roles.slug_url IN '.$role);
        }
        $this->db->where('project_id', $id);

        return $this->db->get(db_prefix() . 'project_members')->result_array();
    }

    public function remove_team_member($project_id, $staff_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'project_members');
        if ($this->db->affected_rows() > 0) {

            // Remove member from tasks where is assigned
            $this->db->where('staffid', $staff_id);
            $this->db->where('taskid IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $this->db->escape_str($project_id) . '")');
            $this->db->delete(db_prefix() . 'task_assigned');

            // $this->log_activity($project_id, 'project_activity_removed_team_member', get_staff_full_name($staff_id));

            return true;
        }

        return false;
    }

    public function get_timesheets($project_id, $tasks_ids = [])
    {
        if (count($tasks_ids) == 0) {
            $tasks     = $this->get_tasks($project_id);
            $tasks_ids = [];
            foreach ($tasks as $task) {
                array_push($tasks_ids, $task['id']);
            }
        }
        if (count($tasks_ids) > 0) {
            $this->db->where('task_id IN(' . implode(', ', $tasks_ids) . ')');
            $timesheets = $this->db->get(db_prefix() . 'taskstimers')->result_array();
            $i          = 0;
            foreach ($timesheets as $t) {
                $task                         = $this->tasks_model->get($t['task_id']);
                $timesheets[$i]['task_data']  = $task;
                $timesheets[$i]['staff_name'] = get_staff_full_name($t['staff_id']);
                if (!is_null($t['end_time'])) {
                    $timesheets[$i]['total_spent'] = $t['end_time'] - $t['start_time'];
                } else {
                    $timesheets[$i]['total_spent'] = time() - $t['start_time'];
                }
                $i++;
            }

            return $timesheets;
        }

        return [];
    }

    public function get_discussion($id, $project_id = '')
    {
        if ($project_id != '') {
            $this->db->where('project_id', $project_id);
        }
        $this->db->where('id', $id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
            $this->db->where('project_id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE clientid=' . get_client_user_id() . ')');
        }
        $discussion = $this->db->get(db_prefix() . 'projectdiscussions')->row();
        if ($discussion) {
            return $discussion;
        }

        return false;
    }

    public function get_discussion_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'projectdiscussioncomments')->row();
        if ($comment->contact_id != 0) {
            if (is_client_logged_in()) {
                if ($comment->contact_id == get_contact_user_id()) {
                    $comment->created_by_current_user = true;
                } else {
                    $comment->created_by_current_user = false;
                }
            } else {
                $comment->created_by_current_user = false;
            }
            $comment->profile_picture_url = contact_profile_image_url($comment->contact_id);
        } else {
            if (is_client_logged_in()) {
                $comment->created_by_current_user = false;
            } else {
                if (is_staff_logged_in()) {
                    if ($comment->staff_id == get_staff_user_id()) {
                        $comment->created_by_current_user = true;
                    } else {
                        $comment->created_by_current_user = false;
                    }
                } else {
                    $comment->created_by_current_user = false;
                }
            }
            if (is_admin($comment->staff_id)) {
                $comment->created_by_admin = true;
            } else {
                $comment->created_by_admin = false;
            }
            $comment->profile_picture_url = staff_profile_image_url($comment->staff_id);
        }
        $comment->created = (strtotime($comment->created) * 1000);
        if (!empty($comment->modified)) {
            $comment->modified = (strtotime($comment->modified) * 1000);
        }
        if (!is_null($comment->file_name)) {
            $comment->file_url = site_url('uploads/discussions/' . $comment->discussion_id . '/' . $comment->file_name);
        }

        return $comment;
    }

    public function get_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments             = $this->db->get(db_prefix() . 'projectdiscussioncomments')->result_array();
        $i                    = 0;
        $allCommentsIDS       = [];
        $allCommentsParentIDS = [];
        foreach ($comments as $comment) {
            $allCommentsIDS[] = $comment['id'];
            if (!empty($comment['parent'])) {
                $allCommentsParentIDS[] = $comment['parent'];
            }

            if ($comment['contact_id'] != 0) {
                if (is_client_logged_in()) {
                    if ($comment['contact_id'] == get_contact_user_id()) {
                        $comments[$i]['created_by_current_user'] = true;
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                } else {
                    $comments[$i]['created_by_current_user'] = false;
                }
                $comments[$i]['profile_picture_url'] = contact_profile_image_url($comment['contact_id']);
            } else {
                if (is_client_logged_in()) {
                    $comments[$i]['created_by_current_user'] = false;
                } else {
                    if (is_staff_logged_in()) {
                        if ($comment['staff_id'] == get_staff_user_id()) {
                            $comments[$i]['created_by_current_user'] = true;
                        } else {
                            $comments[$i]['created_by_current_user'] = false;
                        }
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                }
                if (is_admin($comment['staff_id'])) {
                    $comments[$i]['created_by_admin'] = true;
                } else {
                    $comments[$i]['created_by_admin'] = false;
                }
                $comments[$i]['profile_picture_url'] = staff_profile_image_url($comment['staff_id']);
            }
            if (!is_null($comment['file_name'])) {
                $comments[$i]['file_url'] = site_url('uploads/discussions/' . $id . '/' . $comment['file_name']);
            }
            $comments[$i]['created'] = (strtotime($comment['created']) * 1000);
            if (!empty($comment['modified'])) {
                $comments[$i]['modified'] = (strtotime($comment['modified']) * 1000);
            }
            $i++;
        }

        // Ticket #5471
        foreach ($allCommentsParentIDS as $parent_id) {
            if (!in_array($parent_id, $allCommentsIDS)) {
                foreach ($comments as $key => $comment) {
                    if ($comment['parent'] == $parent_id) {
                        $comments[$key]['parent'] = null;
                    }
                }
            }
        }

        return $comments;
    }

    public function get_discussions($project_id)
    {
        $this->db->where('project_id', $project_id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
        }
        $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();
        $i           = 0;
        foreach ($discussions as $discussion) {
            $discussions[$i]['total_comments'] = total_rows(db_prefix() . 'projectdiscussioncomments', [
                'discussion_id'   => $discussion['id'],
                'discussion_type' => 'regular',
            ]);
            $i++;
        }

        return $discussions;
    }

    public function add_discussion_comment($data, $discussion_id, $type)
    {
        $discussion               = $this->get_discussion($discussion_id);
        $_data['discussion_id']   = $discussion_id;
        $_data['discussion_type'] = $type;
        if (isset($data['content'])) {
            $_data['content'] = $data['content'];
        }
        if (isset($data['parent']) && $data['parent'] != null) {
            $_data['parent'] = $data['parent'];
        }
        if (is_client_logged_in()) {
            $_data['contact_id'] = get_contact_user_id();
            $_data['fullname']   = get_contact_full_name($_data['contact_id']);
            $_data['staff_id']   = 0;
        } else {
            $_data['contact_id'] = 0;
            $_data['staff_id']   = get_staff_user_id();
            $_data['fullname']   = get_staff_full_name($_data['staff_id']);
        }
        $_data            = handle_project_discussion_comment_attachments($discussion_id, $data, $_data);
        $_data['created'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'projectdiscussioncomments', $_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($type == 'regular') {
                $discussion = $this->get_discussion($discussion_id);
                $not_link   = 'projects/view/' . $discussion->project_id . '?group=project_discussions&discussion_id=' . $discussion_id;
            } else {
                $discussion                   = $this->get_file($discussion_id);
                $not_link                     = 'projects/view/' . $discussion->project_id . '?group=project_files&file_id=' . $discussion_id;
                $discussion->show_to_customer = $discussion->visible_to_customer;
            }

            $this->send_project_email_template($discussion->project_id, 'project_new_discussion_comment_to_staff', 'project_new_discussion_comment_to_customer', $discussion->show_to_customer, [
                'staff' => [
                    'discussion_id'         => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type'       => $type,
                ],
                'customers' => [
                    'customer_template'     => true,
                    'discussion_id'         => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type'       => $type,
                ],
            ]);


            $this->log_activity($discussion->project_id, 'project_activity_commented_on_discussion', $discussion->subject, $discussion->show_to_customer);

            $notification_data = [
                'description' => 'not_commented_on_project_discussion',
                'link'        => $not_link,
            ];

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $members       = $this->get_project_members($discussion->project_id);
            $notifiedUsers = [];
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);

            $this->_update_discussion_last_activity($discussion_id, $type);

            return $this->get_discussion_comment($insert_id);
        }

        return false;
    }

    public function update_discussion_comment($data)
    {
        $comment = $this->get_discussion_comment($data['id']);
        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'projectdiscussioncomments', [
            'modified' => date('Y-m-d H:i:s'),
            'content'  => $data['content'],
        ]);
        if ($this->db->affected_rows() > 0) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return $this->get_discussion_comment($data['id']);
    }

    public function delete_discussion_comment($id, $logActivity = true)
    {
        $comment = $this->get_discussion_comment($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'projectdiscussioncomments');
        if ($this->db->affected_rows() > 0) {
            $this->delete_discussion_comment_attachment($comment->file_name, $comment->discussion_id);
            if ($logActivity) {
                $additional_data = '';
                if ($comment->discussion_type == 'regular') {
                    $discussion = $this->get_discussion($comment->discussion_id);
                    $not        = 'project_activity_deleted_discussion_comment';
                    $additional_data .= $discussion->subject . '<br />' . $comment->content;
                } else {
                    $discussion = $this->get_file($comment->discussion_id);
                    $not        = 'project_activity_deleted_file_discussion_comment';
                    $additional_data .= $discussion->subject . '<br />' . $comment->content;
                }

                if (!is_null($comment->file_name)) {
                    $additional_data .= $comment->file_name;
                }

                $this->log_activity($discussion->project_id, $not, $additional_data);
            }
        }

        $this->db->where('parent', $id);
        $this->db->update(db_prefix() . 'projectdiscussioncomments', [
            'parent' => null,
        ]);

        if ($this->db->affected_rows() > 0 && $logActivity) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return true;
    }

    public function delete_discussion_comment_attachment($file_name, $discussion_id)
    {
        $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER . $discussion_id;
        if (!is_null($file_name)) {
            if (file_exists($path . '/' . $file_name)) {
                unlink($path . '/' . $file_name);
            }
        }
        if (is_dir($path)) {
            // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files($path);
            if (count($other_attachments) == 0) {
                delete_dir($path);
            }
        }
    }

    public function add_discussion($data)
    {
        if (is_client_logged_in()) {
            $data['contact_id']       = get_contact_user_id();
            $data['staff_id']         = 0;
            $data['show_to_customer'] = 1;
        } else {
            $data['staff_id']   = get_staff_user_id();
            $data['contact_id'] = 0;
            if (isset($data['show_to_customer'])) {
                $data['show_to_customer'] = 1;
            } else {
                $data['show_to_customer'] = 0;
            }
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert(db_prefix() . 'projectdiscussions', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $members           = $this->get_project_members($data['project_id']);
            $notification_data = [
                'description' => 'not_created_new_project_discussion',
                'link'        => 'projects/view/' . $data['project_id'] . '?group=project_discussions&discussion_id=' . $insert_id,
            ];

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $notifiedUsers = [];
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);
            $this->send_project_email_template($data['project_id'], 'project_discussion_created_to_staff', 'project_discussion_created_to_customer', $data['show_to_customer'], [
                'staff' => [
                    'discussion_id'   => $insert_id,
                    'discussion_type' => 'regular',
                ],
                'customers' => [
                    'customer_template' => true,
                    'discussion_id'     => $insert_id,
                    'discussion_type'   => 'regular',
                ],
            ]);
            $this->log_activity($data['project_id'], 'project_activity_created_discussion', $data['subject'], $data['show_to_customer']);

            return $insert_id;
        }

        return false;
    }

    public function edit_discussion($data, $id)
    {
        $this->db->where('id', $id);
        if (isset($data['show_to_customer'])) {
            $data['show_to_customer'] = 1;
        } else {
            $data['show_to_customer'] = 0;
        }
        $data['description'] = nl2br($data['description']);
        $this->db->update(db_prefix() . 'projectdiscussions', $data);
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($data['project_id'], 'project_activity_updated_discussion', $data['subject'], $data['show_to_customer']);

            return true;
        }

        return false;
    }

    public function delete_discussion($id, $logActivity = true)
    {
        $discussion = $this->get_discussion($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'projectdiscussions');
        if ($this->db->affected_rows() > 0) {
            if ($logActivity) {
                $this->log_activity($discussion->project_id, 'project_activity_deleted_discussion', $discussion->subject, $discussion->show_to_customer);
            }
            $this->_delete_discussion_comments($id, 'regular');

            return true;
        }

        return false;
    }

    public function copy($project_id, $data)
    {
        $project   = $this->get($project_id);
        $settings  = $this->get_project_settings($project_id);
        $_new_data = [];
        $fields    = $this->db->list_fields(db_prefix() . 'projects');
        foreach ($fields as $field) {
            if (isset($project->$field)) {
                $_new_data[$field] = $project->$field;
            }
        }

        unset($_new_data['id']);
        $_new_data['clientid'] = $data['clientid_copy_project'];
        unset($_new_data['clientid_copy_project']);

        $_new_data['start_date'] = to_sql_date($data['start_date']);

        if ($_new_data['start_date'] > date('Y-m-d')) {
            $_new_data['status'] = 1;
        } else {
            $_new_data['status'] = 2;
        }
        if ($data['deadline']) {
            $_new_data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $_new_data['deadline'] = null;
        }

        $_new_data['project_created'] = date('Y-m-d H:i:s');
        $_new_data['addedfrom']       = get_staff_user_id();

        $_new_data['date_finished'] = null;

        $this->db->insert(db_prefix() . 'projects', $_new_data);
        $id = $this->db->insert_id();
        if ($id) {
            $tags = get_tags_in($project_id, 'project');
            handle_tags_save($tags, $id, 'project');

            foreach ($settings as $setting) {
                $this->db->insert(db_prefix() . 'project_settings', [
                    'project_id' => $id,
                    'name'       => $setting['name'],
                    'value'      => $setting['value'],
                ]);
            }
            $added_tasks = [];
            $tasks       = $this->get_tasks($project_id);
            if (isset($data['tasks'])) {
                foreach ($tasks as $task) {
                    if (isset($data['task_include_followers'])) {
                        $copy_task_data['copy_task_followers'] = 'true';
                    }
                    if (isset($data['task_include_assignees'])) {
                        $copy_task_data['copy_task_assignees'] = 'true';
                    }
                    if (isset($data['tasks_include_checklist_items'])) {
                        $copy_task_data['copy_task_checklist_items'] = 'true';
                    }
                    $copy_task_data['copy_from'] = $task['id'];
                    $task_id                     = $this->tasks_model->copy($copy_task_data, [
                        'rel_id'              => $id,
                        'rel_type'            => 'project',
                        'last_recurring_date' => null,
                        'status'              => $data['copy_project_task_status'],
                    ]);
                    if ($task_id) {
                        array_push($added_tasks, $task_id);
                    }
                }
            }
            if (isset($data['milestones'])) {
                $milestones        = $this->get_milestones($project_id);
                $_added_milestones = [];
                foreach ($milestones as $milestone) {
                    $dCreated = new DateTime($milestone['datecreated']);
                    $dDuedate = new DateTime($milestone['due_date']);
                    $dDiff    = $dCreated->diff($dDuedate);
                    $due_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY'))));

                    $this->db->insert(db_prefix() . 'milestones', [
                        'name'                            => $milestone['name'],
                        'project_id'                      => $id,
                        'milestone_order'                 => $milestone['milestone_order'],
                        'description_visible_to_customer' => $milestone['description_visible_to_customer'],
                        'description'                     => $milestone['description'],
                        'due_date'                        => $due_date,
                        'datecreated'                     => date('Y-m-d'),
                        'color'                           => $milestone['color'],
                    ]);

                    $milestone_id = $this->db->insert_id();
                    if ($milestone_id) {
                        $_added_milestone_data         = [];
                        $_added_milestone_data['id']   = $milestone_id;
                        $_added_milestone_data['name'] = $milestone['name'];
                        $_added_milestones[]           = $_added_milestone_data;
                    }
                }
                if (isset($data['tasks'])) {
                    if (count($added_tasks) > 0) {
                        // Original project tasks
                        foreach ($tasks as $task) {
                            if ($task['milestone'] != 0) {
                                $this->db->where('id', $task['milestone']);
                                $milestone = $this->db->get(db_prefix() . 'milestones')->row();
                                if ($milestone) {
                                    $name = $milestone->name;
                                    foreach ($_added_milestones as $added_milestone) {
                                        if ($name == $added_milestone['name']) {
                                            $this->db->where('id IN (' . implode(', ', $added_tasks) . ')');
                                            $this->db->where('milestone', $task['milestone']);
                                            $this->db->update(db_prefix() . 'tasks', [
                                                'milestone' => $added_milestone['id'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // milestones not set
                if (count($added_tasks)) {
                    foreach ($added_tasks as $task) {
                        $this->db->where('id', $task['id']);
                        $this->db->update(db_prefix() . 'tasks', [
                            'milestone' => 0,
                        ]);
                    }
                }
            }
            if (isset($data['members'])) {
                $members  = $this->get_project_members($project_id);
                $_members = [];
                foreach ($members as $member) {
                    array_push($_members, $member['staff_id']);
                }
                $this->add_edit_members([
                    'project_members' => $_members,
                ], $id);
            }

            $custom_fields = get_custom_fields('projects');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($project_id, $field['id'], 'projects', false);
                if ($value != '') {
                    $this->db->insert(db_prefix() . 'customfieldsvalues', [
                        'relid'   => $id,
                        'fieldid' => $field['id'],
                        'fieldto' => 'projects',
                        'value'   => $value,
                    ]);
                }
            }

            $this->log_activity($id, 'project_activity_created');
            log_activity('Project Copied [ID: ' . $project_id . ', NewID: ' . $id . ']');

            return $id;
        }

        return false;
    }

    public function get_staff_notes($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get(db_prefix() . 'project_notes')->row();
        if ($notes) {
            return $notes->content;
        }

        return '';
    }

    public function save_note($data, $project_id)
    {
        // Check if the note exists for this project;
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get(db_prefix() . 'project_notes')->row();
        if ($notes) {
            $this->db->where('id', $notes->id);
            $this->db->update(db_prefix() . 'project_notes', [
                'content' => $data['content'],
            ]);
            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        }
        $this->db->insert(db_prefix() . 'project_notes', [
            'staff_id'   => get_staff_user_id(),
            'content'    => $data['content'],
            'project_id' => $project_id,
        ]);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;


        return false;
    }

    public function delete($project_id)
    {
        $project_name = get_project_name_by_id($project_id);

        $this->db->where('id', $project_id);
        $this->db->delete(db_prefix() . 'projects');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_members');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_notes');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'milestones');

            // Delete the custom field values
            $this->db->where('relid', $project_id);
            $this->db->where('fieldto', 'projects');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $project_id);
            $this->db->where('rel_type', 'project');
            $this->db->delete(db_prefix() . 'taggables');


            $this->db->where('project_id', $project_id);
            $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();
            foreach ($discussions as $discussion) {
                $discussion_comments = $this->get_discussion_comments($discussion['id'], 'regular');
                foreach ($discussion_comments as $comment) {
                    $this->delete_discussion_comment_attachment($comment['file_name'], $discussion['id']);
                }
                $this->db->where('discussion_id', $discussion['id']);
                $this->db->delete(db_prefix() . 'projectdiscussioncomments');
            }
            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'projectdiscussions');

            $files = $this->get_files($project_id);
            foreach ($files as $file) {
                $this->remove_file($file['id']);
            }

            $tasks = $this->get_tasks($project_id);
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_settings');

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'project_activity');

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'expenses', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'invoices', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'creditnotes', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'estimates', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->update(db_prefix() . 'tickets', [
                'project_id' => 0,
            ]);

            $this->db->where('project_id', $project_id);
            $this->db->delete(db_prefix() . 'pinned_projects');

            log_activity('Project Deleted [ID: ' . $project_id . ', Name: ' . $project_name . ']');

            return true;
        }

        return false;
    }

    public function get_activity($id = '', $limit = '', $only_project_members_activity = false)
    {
        if (!is_client_logged_in()) {
            $has_permission = has_permission('projects', '', 'view');
            if (!$has_permission) {
                // $this->db->where('pa.project_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members as pm WHERE pm.staff_id=' . get_staff_user_id() . ')');
            }
        }
        // $this->db->join("staff as s", 's.staffid = pa.staff_id', 'LEFT');
        if (is_client_logged_in()) {
            $this->db->where('pa.visible_to_customer', 1);
        }
        if (is_numeric($id)) {
            $this->db->where('pa.project_id', $id);
        }

        //remove delayed and frozen project from history list
        // $this->db->where_not_in('description_key',['project_delayed','project_frozen']);
        // $this->db->where_not_in('status',[7,8]);
        $this->db->where_not_in('description_key',['project_delayed']);
        $this->db->where_not_in('status',[7]);

        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }
        // $this->db->order_by('pa.dateadded', 'asc');
        $this->db->order_by('pa.id', 'asc');
        $activities = $this->db->get(db_prefix() . 'project_activity as pa')->result_array();
        $i          = 0;
        foreach ($activities as $activity) {
            $seconds          = get_string_between($activity['additional_data'], '<seconds>', '</seconds>');
            $other_lang_keys  = get_string_between($activity['additional_data'], '<lang>', '</lang>');
            $_additional_data = $activity['additional_data'];
            if ($seconds != '') {
                $_additional_data = str_replace('<seconds>' . $seconds . '</seconds>', seconds_to_time_format($seconds), $_additional_data);
            }
            if ($other_lang_keys != '') {
                $_additional_data = str_replace('<lang>' . $other_lang_keys . '</lang>', _l($other_lang_keys), $_additional_data);
            }
            if (strpos($_additional_data, 'project_status_') !== false) {
                $_additional_data = get_project_status_by_id(strafter($_additional_data, 'project_status_'));
            }
            $activities[$i]['description']     = _l($activities[$i]['description_key']);
            $activities[$i]['additional_data'] = $_additional_data;
            $activities[$i]['project_name']    = get_project_name_by_id($activity['project_id']);
            // unset($activities[$i]['description_key']);
            $i++;
        }

        return $activities;
    }

    public function log_activity($project_id, $description_key, $staffid = 0, $additional_data = '', $visible_to_customer = 1, $check = '')
    {
        if ($check != '') {
            if (!DEFINED('CRON')) {
                if (is_client_logged_in()) {
                    $data['contact_id'] = ($check == 'unassigned' or $check == 'ticket_assigned_after') ? 0 : get_contact_user_id();
                    $data['staff_id']   = $staffid;
                    $data['fullname']   = ($check == 'unassigned' or $check == 'ticket_assigned_after') ? get_staff_full_name($staffid) : get_contact_full_name(get_contact_user_id());
                } elseif (is_staff_logged_in()) {
                    $data['contact_id'] = 0;
                    $data['staff_id']   = get_staff_user_id();
                    $data['fullname']   = get_staff_full_name(get_staff_user_id());
                }
            } else {
                $data['contact_id'] = 0;
                $data['staff_id']   = 0;
                $data['fullname']   = '[CRON]';
            }
        } else {
            if (!DEFINED('CRON')) {
                if (is_client_logged_in()) {
                    $data['contact_id'] = ($description_key == 'unassigned' or $description_key == 'ticket_assigned_to_at') ? 0 : get_contact_user_id();
                    $data['staff_id']   = $staffid;
                    $data['fullname']   = ($description_key == 'unassigned' or $description_key == 'ticket_assigned_to_at') ? get_staff_full_name($staffid) : get_contact_full_name(get_contact_user_id());
                } elseif (is_staff_logged_in()) {
                    $data['contact_id'] = 0;
                    $data['staff_id']   = get_staff_user_id();
                    $data['fullname']   = get_staff_full_name(get_staff_user_id());
                }
            } else {
                $data['contact_id'] = 0;
                $data['staff_id']   = 0;
                $data['fullname']   = '[CRON]';
            }
        }
        $additionalData = isJson($additional_data)?json_decode($additional_data):'';
        // echo '<pre>'; print_r($additionalData); echo '</pre>';
        if(!empty($additionalData)){
            $data['status']     = !empty($additionalData->status)?$additionalData->status:'';
        }
        $data['description_key']     = $description_key;
        $data['additional_data']     = $additional_data;
        $data['visible_to_customer'] = $visible_to_customer;
        $data['project_id']          = $project_id;
        $data['dateadded']           = date('Y-m-d H:i:s');

        $data = hooks()->apply_filters('before_log_project_activity', $data);

        $this->db->insert(db_prefix() . 'project_activity', $data);
    }

    public function new_project_file_notification($file_id, $project_id)
    {
        $file = $this->get_file($file_id);

        $additional_data = $file->file_name;
        $this->log_activity($project_id, 'project_activity_uploaded_file', $additional_data, $file->visible_to_customer);

        $members           = $this->get_project_members($project_id);
        $notification_data = [
            'description' => 'not_project_file_uploaded',
            'link'        => 'projects/view/' . $project_id . '?group=project_files&file_id=' . $file_id,
        ];

        if (is_client_logged_in()) {
            $notification_data['fromclientid'] = get_contact_user_id();
        } else {
            $notification_data['fromuserid'] = get_staff_user_id();
        }

        $notifiedUsers = [];
        foreach ($members as $member) {
            if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                continue;
            }
            $notification_data['touserid'] = $member['staff_id'];
            if (add_notification($notification_data)) {
                array_push($notifiedUsers, $member['staff_id']);
            }
        }
        pusher_trigger_notification($notifiedUsers);

        $this->send_project_email_template(
            $project_id,
            'project_file_to_staff',
            'project_file_to_customer',
            $file->visible_to_customer,
            [
                'staff'     => ['discussion_id' => $file_id, 'discussion_type' => 'file'],
                'customers' => ['customer_template' => true, 'discussion_id' => $file_id, 'discussion_type' => 'file'],
            ]
        );
    }

    public function add_external_file($data)
    {
        $insert['dateadded']           = date('Y-m-d H:i:s');
        $insert['project_id']          = $data['project_id'];
        $insert['external']            = $data['external'];
        $insert['visible_to_customer'] = $data['visible_to_customer'];
        $insert['file_name']           = $data['files'][0]['name'];
        $insert['subject']             = $data['files'][0]['name'];
        $insert['external_link']       = $data['files'][0]['link'];

        $path_parts         = pathinfo($data['files'][0]['name']);
        $insert['filetype'] = get_mime_by_extension('.' . $path_parts['extension']);

        if (isset($data['files'][0]['thumbnailLink'])) {
            $insert['thumbnail_link'] = $data['files'][0]['thumbnailLink'];
        }

        if (isset($data['staffid'])) {
            $insert['staffid'] = $data['staffid'];
        } elseif (isset($data['contact_id'])) {
            $insert['contact_id'] = $data['contact_id'];
        }

        $this->db->insert(db_prefix() . 'project_files', $insert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->new_project_file_notification($insert_id, $data['project_id']);

            return $insert_id;
        }

        return false;
    }

    public function send_project_email_template($project_id, $staff_template, $customer_template, $action_visible_to_customer, $additional_data = [])
    {
        if (count($additional_data) == 0) {
            $additional_data['customers'] = [];
            $additional_data['staff']     = [];
        } elseif (count($additional_data) == 1) {
            if (!isset($additional_data['staff'])) {
                $additional_data['staff'] = [];
            } else {
                $additional_data['customers'] = [];
            }
        }

        $project = $this->get($project_id);
        $members = $this->get_project_members($project_id);

        foreach ($members as $member) {
            if (is_staff_logged_in() && $member['staff_id'] == get_staff_user_id()) {
                continue;
            }
            send_mail_template($staff_template, $project, $member, $additional_data['staff']);
        }
        if ($action_visible_to_customer == 1) {
            $contacts = $this->clients_model->get_contacts($project->clientid, ['active' => 1, 'project_emails' => 1]);

            foreach ($contacts as $contact) {
                if (is_client_logged_in() && $contact['id'] == get_contact_user_id()) {
                    continue;
                }
                send_mail_template($customer_template, $project, $contact, $additional_data['customers']);
            }
        }
    }

    private function _get_project_billing_data($id)
    {
        $this->db->select('billing_type,project_rate_per_hour');
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'projects')->row();
    }

    public function total_logged_time_by_billing_type($id, $conditions = [])
    {
        $project_data = $this->_get_project_billing_data($id);
        $data         = [];
        if ($project_data->billing_type == 2) {
            $seconds             = $this->total_logged_time($id);
            $data                = $this->projects_model->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        } elseif ($project_data->billing_type == 3) {
            $data = $this->_get_data_total_logged_time($id);
        }

        return $data;
    }

    public function data_billable_time($id)
    {
        return $this->_get_data_total_logged_time($id, [
            'billable' => 1,
        ]);
    }

    public function data_billed_time($id)
    {
        return $this->_get_data_total_logged_time($id, [
            'billable' => 1,
            'billed'   => 1,
        ]);
    }

    public function data_unbilled_time($id)
    {
        return $this->_get_data_total_logged_time($id, [
            'billable' => 1,
            'billed'   => 0,
        ]);
    }

    private function _delete_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments = $this->db->get(db_prefix() . 'projectdiscussioncomments')->result_array();
        foreach ($comments as $comment) {
            $this->delete_discussion_comment_attachment($comment['file_name'], $id);
        }
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $this->db->delete(db_prefix() . 'projectdiscussioncomments');
    }

    private function _get_data_total_logged_time($id, $conditions = [])
    {
        $project_data = $this->_get_project_billing_data($id);
        $tasks        = $this->get_tasks($id, $conditions);

        if ($project_data->billing_type == 3) {
            $data                = $this->calculate_total_by_task_hourly_rate($tasks);
            $data['logged_time'] = seconds_to_time_format($data['total_seconds']);
        } elseif ($project_data->billing_type == 2) {
            $seconds = 0;
            foreach ($tasks as $task) {
                $seconds += $task['total_logged_time'];
            }
            $data                = $this->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        }

        return $data;
    }

    private function _update_discussion_last_activity($id, $type)
    {
        if ($type == 'file') {
            $table = db_prefix() . 'project_files';
        } elseif ($type == 'regular') {
            $table = db_prefix() . 'projectdiscussions';
        }
        $this->db->where('id', $id);
        $this->db->update($table, [
            'last_activity' => date('Y-m-d H:i:s'),
        ]);
    }

    public function get_project_status()
    {
        $projectStatus = $this->get_project_statuses();

        $statuses = array();
        foreach ($projectStatus as $status) {
            $statuses[$status['id']] = $status;
        }
        return $statuses;
    }

    public function update_team_members($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
        }
        // echo '<pre>'; print_r($project_members);
        // $new_project_members_to_receive_email = [];
        $this->db->select('name,clientid');
        $this->db->where('id', $id);
        $project      = $this->db->get(db_prefix() . 'projects')->row();
        $project_name = $project->name;
        $client_id    = $project->clientid;

        $project_members_in = $this->get_project_members($id);
        // echo '<pre>'; print_r($project_members_in); die;
        if (sizeof($project_members_in) > 0) {
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    $this->db->where('project_id', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get(db_prefix() . 'project_members')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert(db_prefix() . 'project_members', [
                            'project_id' => $id,
                            'staff_id'   => $staff_id,
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            /* if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification([
                                    'fromuserid'      => get_staff_user_id(),
                                    'description'     => 'not_staff_added_as_project_member',
                                    'link'            => 'projects/view/' . $id,
                                    'touserid'        => $staff_id,
                                    'additional_data' => serialize([
                                        $project_name,
                                    ]),
                                ]);
                                array_push($new_project_members_to_receive_email, $staff_id);
                                if ($notified) {
                                    array_push($notifiedUsers, $staff_id);
                                }
                            } */

                            // $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                            $affectedRows++;
                        }
                    } else {
                        return true;
                    }
                }
                // pusher_trigger_notification($notifiedUsers);
            }
        } else {
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    if (empty($staff_id)) {
                        continue;
                    }
                    $this->db->insert(db_prefix() . 'project_members', [
                        'project_id' => $id,
                        'staff_id'   => $staff_id,
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification([
                                'fromuserid'      => get_staff_user_id(),
                                'description'     => 'not_staff_added_as_project_member',
                                'link'            => 'projects/view/' . $id,
                                'touserid'        => $staff_id,
                                'additional_data' => serialize([
                                    $project_name,
                                ]),
                            ]);
                            // array_push($new_project_members_to_receive_email, $staff_id);
                            // if ($notifiedUsers) {
                            //     array_push($notifiedUsers, $staff_id);
                            // }
                        }
                        // $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                        $affectedRows++;
                    }
                }
                // pusher_trigger_notification($notifiedUsers);
            }
        }

        // if (count($new_project_members_to_receive_email) > 0) {
        //     $all_members = $this->get_project_members($id);
        //     foreach ($all_members as $data) {
        //         if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
        //             send_mail_template('project_staff_added_as_member', $data, $id, $client_id);
        //         }
        //     }
        // }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function save_ticket_note($data, $project_id)
    {
        if(!empty($data['content'])){
            $this->db->insert(db_prefix() . 'project_notes', [
                'staff_id'   => get_staff_user_id(),
                'content'    => !empty($data['content'])?$data['content']:'',
                'project_id' => $project_id,
                'status' => !empty($data['status_id'])?$data['status_id']:'',
                'exception' => !empty($data['exception'])?$data['exception']:''
            ]);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                // $this->log_activity($project_id, 'project_activity_rejected', get_staff_full_name(get_staff_user_id()), 'Project Rejected and Updated status 5');
                return true;
            }
        }else{
            return true;
        }
        return false;
    }

    public function get_project_details($projectId)
    {
        $select = "p.id as project_id,p.`name` as project_name, p.`status` as project_status, p.project_created as logged_date, p.*, a.`name` as area_name, r.region_name, sr.region_name as sub_region_name,c.company,ps.name as status_name,
        pm.staff_id as assigned_user_id,pm.created_at as assigned_date,ta.status as task_status";

        // $select = "p.id as project_id,p.`name` as project_name,p.description, p.`status` as project_status, p.project_created as logged_date,p.landmark, p.frozen,p.reassigned,p.area_id, p.region_id,p.subregion_id,p.clientid, p.issue_id,p.remail,p.rphonenumber,p.rname,p.action_date,, a.`name` as area_name, r.region_name, sr.region_name as sub_region_name,c.company,ps.name as status_name,
        // pm.staff_id as assigned_user_id,pm.created_at as assigned_date,ta.status as task_status";
        //ta.staffid as assigned_user_id,ta.assigned_date,ta.status as task_status

        $this->db->select($select);
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'area a ', 'a.areaid = p.area_id', 'left');
        $this->db->join(db_prefix() . 'region r ', 'r.id = p.region_id', 'left');
        $this->db->join(db_prefix() . 'sub_region sr ', 'sr.id = p.subregion_id', 'left');
        $this->db->join(db_prefix() . 'clients c ', 'c.userid = p.clientid', 'left');
        $this->db->join(db_prefix() . 'task_assigned ta ', 'ta.taskid = p.id', 'left');
        $this->db->join(db_prefix() . 'project_members pm ', 'pm.project_id = p.id', 'left');
        $this->db->join(db_prefix() . 'project_status ps ', 'ps.id = p.status', 'left');

        $this->db->where('p.id', $projectId);
        // $this->db->where('pm.assigned', 1);
        // $this->db->where('pm.active', 1);
        return $this->db->get()->row();
    }

    public function get_task_details($project_id)
    {
        $this->db->select("t.id as task_id, t.name as task_name, t.startdate, t.duedate, t.datefinished, t.task_days, t.is_closed, t.status, ps.name as task_status,t.reminderone_days,t.remindertwo_days");
        $this->db->from("tasks as t");
        $this->db->join("project_status as ps", "t.status = ps.id", "LEFT");
        $this->db->where(["t.rel_id" => $project_id, "t.rel_type" => "project"]);
        $this->db->order_by("t.startdate", "ASC");
        $result = $this->db->get()->result_array();
        if (count($result) > 0) return $result;
        return array();
    }

    public function reopen_ticket_note($data)
    {
        $this->db->insert(db_prefix() . 'project_notes', [
            'staff_id'   => get_staff_user_id(),
            'content'    => $data['content'],
            'project_id' => $data['project_id'],
            'status' => $data['status_id'],
        ]);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            // $this->log_activity($data['project_id'], 'reopen_ticket', get_staff_full_name(get_staff_user_id()), '<b><lang>project_status_6</lang></b>');
            return true;
        }

        return false;
    }

    public function get_ar_project_details($projectId)
    {

        $select = "p.id as project_id,p.`name` as project_name,p.description, p.`status` as project_status, p.project_created as logged_date,p.landmark, a.`name` as area_name, r.region_name, sr.region_name as sub_region_name,c.company,pm.staff_id as assigned_user_id,pm.created_at as assigned_date,p.status as task_status";

        $this->db->select($select);
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'area a ', 'a.areaid = p.area_id', 'left');

        $this->db->join(db_prefix() . 'region r ', 'r.id = p.region_id', 'left');
        $this->db->join(db_prefix() . 'sub_region sr ', 'sr.id = p.subregion_id', 'left');
        $this->db->join(db_prefix() . 'clients c ', 'c.userid = p.clientid', 'left');
        $this->db->join(db_prefix() . 'project_members pm ', 'pm.project_id = p.id', 'left');

        $this->db->where('p.id', $projectId);
        $this->db->where('pm.active', 1);
        return $this->db->get()->row();
    }

    public function update_reject_count($projectId)
    {
        $this->db->select('rejected');
        $this->db->where('id', $projectId);
        $old_rejectCnt = $this->db->get(db_prefix() . 'projects')->row()->rejected;

        $newCnt = !empty($old_rejectCnt) ? $old_rejectCnt + 1 : 1;

        $this->db->where('id', $projectId);
        $this->db->update(db_prefix() . 'projects', ['rejected' => $newCnt,'action_date'=>date('Y-m-d')]);
    }

    public function updateAssignedUser($projectId, $staffId,$reAssignStatus = NULL)
    {
        //Update all assigned to un-assigned
        $this->db->where('project_id', $projectId);
        $updateData = array();
        $updateData = array(
            'assigned' => 0,
        );

        $unassignedUsers = $this->db->update(db_prefix() . 'project_members', $updateData);

        //assigned ticket to staff
        $this->db->where('project_id', $projectId);
        $this->db->where('staff_id', $staffId);
        $_exists = $this->db->get(db_prefix() . 'project_members')->row();
        if (!$_exists) {
            $this->db->insert(db_prefix() . 'project_members', [
                'project_id' => $projectId,
                'staff_id'   => $staffId,
            ]);
        } else {
            $this->db->where('project_id', $projectId);
            $this->db->where('staff_id', $staffId);
            $this->db->update(db_prefix() . 'project_members', ['assigned' => 1, 'active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $staff_name = get_staff_full_name($staffId);
        $this->load->model('staff_model');
        $userDetail = $this->staff_model->get_userDetails($staffId);
        $role = !empty($userDetail->role) ? $userDetail->role : '';
        $label = 'ticket_assigned_to_you';
        $status = !empty($reAssignStatus)?$reAssignStatus:2;
        if ($role == 3) {
            $label = 'ticket_assigned_to_at';
        } else if ($role == 4) {
            $label = 'ticket_assigned_to_ar';
            $status = 5;
        } else if ($role == 8) {
            $label = 'ticket_assigned_to_ata';
        }

        $additional_data = array(
            'assigned_by' => get_staff_user_id(),
            'assigned_to' => $staffId,
            'taskId' => '',
            'status' => $status,
            'comment' => ''
        );
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($projectId, $label, $staff_name, json_encode($additional_data));
            return true;
        } else {
            return false;
        }
    }

    public function set_project_frozen($condition, $status = 0)
    {
        // $this->db->select("project_id as id");
        // $this->db->from("project_members");
        // $this->db->where_in($staff_ids);
        // $this->db->group_by("project_id");
        // $projects = $this->db->get()->result_array();
        // pre($projects);
        // if (count($projects) > 0) {
        // foreach ($projects as $project) {
        $this->db->where($condition);
        $this->db->update("projects", ["frozen" => ($status == 0) ? 1 : 0]);
        // }
        // }
    }

    function getFrozenProjects($condition,$status)
    {
        $this->db->select('p.id as project_id');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->where($condition);
        $this->db->where('frozen', $status);
        $result = $this->db->get()->result_array();
       // echo $this->db->last_query();  exit;
      
        $project = array();
        if(!empty($result)){
            $project = array_column($result, 'project_id');
        }
        return $project;
    
    }

    function updateFrozenStaus($projectIds,$status){
        if(empty($status)){
            $this->addDelayedFrozenProjects($projectIds,'project_frozen',8);
        }else{
            $this->updateDelayedHistoryProjects($projectIds,'project_frozen',8);
            //Check if any Un-frozen ticket has Delayed Status
            if(!empty($projectIds)){
                $delayedProjects = $this->getUnFrozenProjectsStatus($projectIds);
                if(!empty($delayedProjects)){
                    $this->update_delayed_projects($delayedProjects);
                }
            }
        }
    }

    /********************************************
        Task Assigned - assigned user and status
        Project_members -> status
        Update Tasks 
     *********************************************/
    public function reopen_ticket_details($projectId, $pStatus, $tStatus)
    {
        //Find AT/ATA for projectId
        $staff_id = $this->get_project_assignee($projectId);

        //Update Task Assigned
        $statusData = array(
            'status' => 6, //$tStatus
            'staffid' => $staff_id,
            'assigned_from' => get_staff_user_id()
        );
        $this->load->model('tasks_model');
        $this->tasks_model->updateTaskStatus($projectId, $statusData);

        //Update Task Status
        // $this->tasks_model->update_reopen_task_status($projectId);

        //Update Project members status
        $this->updateAssignedUser($projectId, $staff_id);

        //Update Task_assigned - status, startdate, duedate, reminderone_date,remindertwo_date,
        //Update Project - start_date,deadline,date_finished,action_date,status
        $newProjectAndTaskDeadline = $this->updateNewProjectAndTaskDeadline($projectId, $pStatus);

        if ($newProjectAndTaskDeadline) {
            // $this->log_activity($projectId, 'reset_milestone', get_staff_full_name(get_staff_user_id()), 'Milestone Reset');
            return true;
        } else {
            return false;
        }
    }

    public function get_project_assignee($projectId)
    {
        $this->db->select('email,project_id,staff_id,role,assigned');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id');
        $this->db->where('project_id', $projectId);
        $this->db->where(db_prefix() . 'project_members.active', 1);
        $this->db->where(db_prefix() . 'staff.active', 1);
        $this->db->order_by("id", "desc");

        $results = $this->db->get(db_prefix() . 'project_members')->result_object();
        $staff_id = get_staff_user_id();
        foreach ($results as $result) {
            if ($result->role == 8) { //ata
                $staff_id = $result->staff_id;
                break;
            } else if ($result->role == 3) { //at
                $staff_id = $result->staff_id;
                break;
            }
        }

        return $staff_id;
    }

    public function get_project_at($projectId)
    {
        $this->db->select('email,project_id,staff_id,role,assigned');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'project_members.staff_id');
        $this->db->where('project_id', $projectId);
        $this->db->where(db_prefix() . 'project_members.active', 1);
        $this->db->where(db_prefix() . 'staff.active', 1);
        $this->db->order_by("id", "desc");

        $results = $this->db->get(db_prefix() . 'project_members')->result_object();
        $staff_id = get_staff_user_id();
        foreach ($results as $result) {
            if ($result->role == 3) {
                $staff_id = $result->staff_id;
                break;
            }
        }

        return $staff_id;
    }

    public function updateNewProjectAndTaskDeadline($projectId, $pStatus)
    {
        $task_details = $this->get_task_details($projectId);
        $cnt = !empty(count($task_details)) ? count($task_details) : 0;
        $nw_startDate = date('Y-m-d');
        $nw_deadline = '';
        $action_date = '';

        $this->load->model('tasks_model');

        $countforduration = 0;
        $countforcheck = 0;
        $nwTaskDetails = array();
        foreach ($task_details as $key => $task) {
            $task_id = $task['task_id'];
            $task_days = $task['task_days'];
            $reminderone_days = $task['reminderone_days'];
            $remindertwo_days = $task['remindertwo_days'];
            $reminderone_date = '';
            $remindertwo_date = '';

            if ($countforcheck > 1) {
                $date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . (convertint($countforduration) + 1) . ' days'));
            } else {
                $date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
            }
            $due_date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
            $reminder1date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));
            $reminder2date = date_add(date_create(date('y-m-d')), date_interval_create_from_date_string('' . $countforduration . ' days'));

            $reminderone_date = date_format(date_add($reminder1date, date_interval_create_from_date_string('' . $reminderone_days . ' days')), "Y-m-d");
            $remindertwo_date = date_format(date_add($reminder2date, date_interval_create_from_date_string('' . $remindertwo_days . ' days')), "Y-m-d");

            $duedate = date_format(date_add($due_date, date_interval_create_from_date_string('' . $task_days . ' days')), "Y-m-d");
            $status = 0;
            if ($key <= 1) {
                $status = 2;
            }

            $nwTaskDetails = array(
                'startdate' => date_format($date, "Y-m-d"),
                'duedate' => $duedate,
                'reminderone_date' => $reminderone_date,
                'reminderone_date' => $remindertwo_date,
                'status' => $status,
                'datefinished' => ''
            );

            //Update Task Details with new deadline
            $this->tasks_model->update_reopen_tasks($projectId, $task_id, $nwTaskDetails);

            if ($cnt == 1 && empty($key)) {
                $action_date = $duedate;
            } else if ($cnt > 1 && $key == 1) {
                $action_date = $duedate;
            }

            if (empty($key)) {
                $nw_deadline = $duedate;
            }

            if ($countforcheck > 0) {
                $countforduration = $countforduration + convertint($task_days);
            } else {
                $date = date_create(date('y-m-d'));
            }
            $countforcheck++;
        }

        $newProjectStatus = array(
            'start_date' => $nw_startDate,
            'deadline' => $nw_deadline,
            'date_finished' => NULL,
            'action_date' => $action_date,
            'status' => $pStatus
        );
        $this->db->where('id', $projectId);
        $this->db->update(db_prefix() . 'projects', $newProjectStatus);

        if ($this->db->affected_rows() > 0) {
            if($pStatus == 6){
                //Update parent ticket in case of sub-ticket of parent ticket reopend by user
                $this->reopen_parent_ticket($projectId,$nw_deadline);
            }
            return true;
        } else {
            return false;
        }
    }

    public function reopen_parent_ticket($projectId,$nw_deadline = NULL)
    {
        //check if ticket has parent ticket
        $parentId = $this->hasParentTicket($projectId);
        if (!empty($parentId)) {
            //Save log for sub-ticket close in parent ticekt
            $p_additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 11, //Reopen status of sub-ticket for parent ticket
                'comment' => ''
            );
            $subTicketId = $this->getSubTicketName($projectId);
            $label = "Project <strong>$subTicketId</strong> reopened by Project Leader";
            if($GLOBALS['current_user']->role_slug_url == 'ar'){
                $label = "Project <strong>$subTicketId</strong> reopened by Project Reviewer";
            }
            $this->log_activity($parentId, $label, get_staff_full_name(get_staff_user_id()), json_encode($p_additional_data));
            
            //Get parent project status
            $projectDetail = $this->get_project_details($parentId);
            $deadline = !empty($projectDetail->deadline)?$projectDetail->deadline:'';
            $project_status = $projectDetail->project_status;
            $latestDueDate = $this->get_subproject_latest_due_date($parentId);
            // $parentProjectStatus = $this->getProjectStatus($parentId);
            $newDueDate = !empty($latestDueDate)?$latestDueDate:$deadline;
            $pstatus = 2;
            if($project_status == 3){
                $pstatus = 6;
            }
            //Reopen parent project
            $newProjectStatus = array(
                'status' => $pstatus,
                'deadline' => $newDueDate,
                'date_finished' => NULL,
                'action_date' => $newDueDate
            );
            $updateStatus = $this->updateProjectStatus($parentId, $newProjectStatus);

            if($project_status == 3){
                //Update Task Assigned
                $taskStatusData = array(
                    'status' => 6, 
                    'assigned_from' => get_staff_user_id()
                );
                $this->load->model('tasks_model');
                $this->tasks_model->updateTaskStatus($parentId, $taskStatusData);

                // $assignedUser = $projectDetail->assigned_user_id;
                $assignedUser = getProjectAssignedUser($projectId);
                if ($updateStatus) {
                    $subId = $this->getSubTicketName($projectId);
                    $additional_data = array(
                        'assigned_by' => get_staff_user_id(),
                        'assigned_to' => $assignedUser,
                        'taskId' => '',
                        'status' => 6,
                        'comment' => 'Sub ticket reopened. Sub Ticket ID: ' . $subId
                    );
                    $this->log_activity($parentId, 'reopen_parent_ticket', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public function updateProjectStatus($projectId, $data)
    {
        $this->db->where("id", $projectId);
        $this->db->update(db_prefix() . 'projects', $data);
        // echo $this->db->last_query();
        // echo '<br>';
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function reassigned_ticket($projectId, $assignAt, $extendDeadlineData, $milestoneCnt)
    {

        $nwTaskDetails = array();
        $nw_startDate = date('Y-m-d');
        $nw_deadline = '';

        $this->load->model('tasks_model');

        foreach ($extendDeadlineData as $key => $task) {
            $task_id = $task['task_id'];
            $startDate = date('Y-m-d', strtotime($task['startDate']));
            $dueDate = date('Y-m-d', strtotime($task['dueDate']));
            // $task_days = $task['task_days'];
            // $reminderone_days = $task['reminderOne_days'];
            // $remindertwo_days = $task['reminderTwo_days'];
            $task_days = dateDiffInDays($dueDate, $startDate);
            $reminderone_days = getReminderOne($task_days);
            $remindertwo_days = getReminderTwo($task_days);
            $milestoneActionDate = '';

            $reminderone_date = date('Y-m-d', strtotime($task['startDate'] . ' + ' . $reminderone_days . ' days'));
            $remindertwo_date = date('Y-m-d', strtotime($task['startDate'] . ' + ' . $remindertwo_days . ' days'));

            $status = 0;
            if ($key < 1) {
                $status = 2;
            }

            $nwTaskDetails = array(
                'startdate' => $startDate,
                'duedate' => $dueDate,
                'reminderone_date' => $reminderone_date,
                'remindertwo_date' => $remindertwo_date,
                'reminderone_days' => $reminderone_days,
                'remindertwo_days' => $remindertwo_days,
                'task_days' => $task_days,
                'status' => $status,
                'datefinished' => NULL
            );

            //Update Task Details with new deadline
            $this->tasks_model->update_reopen_tasks($projectId, $task_id, $nwTaskDetails);

            if (empty($key)) {
                $nw_startDate = $startDate;
            }
            if (empty($key) && $task_days == 1) {
                $milestoneActionDate = $dueDate;
            }

            $nw_deadline = $dueDate;
        }

        if (empty($milestoneActionDate)) {
            $action_date = date('Y-m-d', strtotime($nw_startDate . ' + 2 days'));
        } else {
            $action_date = $milestoneActionDate;
        }

        $checkAssigneeExists = $this->checkProjectAssignee($projectId, $assignAt);
        $reassigned = ($checkAssigneeExists) ? 1 : 0;

        //Update Assigned user
        $this->updateReAssignedUser($projectId, $assignAt, $reassigned);

        $projectDueDate = $this->getProjectDueDate($projectId);

        if (!empty($projectDueDate)) {
            //Log for date change - Project due date changed from 10-Aug-20 to 20-Aug-20
            $dlabel = 'Project due date changed from <strong>' . setDateFormat($projectDueDate) . '</strong> to <strong>' . setDateFormat($nw_deadline) . '</strong>  by Reviewer ';
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 1,
                'comment' => ''
            );
            $this->projects_model->log_activity($projectId, $dlabel, get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
        }

        $newProjectStatus = array(
            'start_date' => $nw_startDate,
            'deadline' => $nw_deadline,
            'date_finished' => NULL,
            'action_date' => $action_date,
            'status' => 1,
            'reassigned' => $reassigned
        );

        $this->update_parent_ticket_duedate($projectId,$nw_deadline);

        if ($milestoneCnt > 1) {
            $this->updateProjectClosure($projectId, $nw_startDate, $nw_deadline);
        }

        //Send Email Notification
        $this->sendAssignEmailNotification($projectId, $assignAt, 'New_ticket');

        $this->db->where('id', $projectId);
        $this->db->update(db_prefix() . 'projects', $newProjectStatus);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_parent_ticket_duedate($projectId,$nw_deadline = NULL)
    {
        //check if ticket has parent ticket
        $parentId = $this->hasParentTicket($projectId);
        if (!empty($parentId)) {
            //Get parent project status
            $projectDetail = $this->get_project_details($parentId);
            $deadline = !empty($projectDetail->deadline)?$projectDetail->deadline:'';
            if (!empty($projectDetail)) {
                //Reopen parent project
                if(strtotime($nw_deadline) > strtotime($deadline)){
                    $newProjectStatus = array(
                        'deadline' => $nw_deadline,
                        'action_date' => $nw_deadline
                    );
                    $updateStatus = $this->updateProjectStatus($parentId, $newProjectStatus);
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function checkProjectAssignee($projectId, $staffId)
    {
        //assigned ticket to staff
        $this->db->where('project_id', $projectId);
        $this->db->where('staff_id', $staffId);
        $_exists = $this->db->get(db_prefix() . 'project_members')->row();
        if (!$_exists) {
            return false;
        } else {
            return true;
        }
    }

    public function updateProjectClosure($projectId, $nw_startDate, $nw_deadline)
    {
        $this->db->where('rel_id', $projectId);
        $this->db->order_by('id');
        $_exists = $this->db->get(db_prefix() . 'tasks')->row();

        if ($_exists) {
            $tId = $_exists->id;
            $nwTaskDetails = array(
                'startdate' => $nw_startDate,
                'duedate' => $nw_deadline,
                'status' => 0,
                'datefinished' => NULL
            );

            $this->db->where('id', $tId);
            $this->db->where('rel_id', $projectId);
            $this->db->update(db_prefix() . 'tasks', $nwTaskDetails);
        }
        return true;
    }

    public function subTickets($projectId, $assignAt, $extendDeadlineData, $milestoneCnt)
    {
        $childTicketIds = array();
        $childProjectFileIds = array();
        $emailNotificationData = array();
        $affected_rows = 0;
        $nw_startDate = '';
        $nw_deadline = date('Y-m-d');
        $milestoneActionDate = '';

        $projectDetail = $this->get_project_details($projectId);

        $email = (!empty($assignedSubAt)) ? get_staff_email($assignedSubAt, "") : get_staff_email("", $projectDetail->area_id);
        $area = !empty($projectDetail->area_name) ? $projectDetail->area_name : '';
        $region = !empty($projectDetail->region_name) ? $projectDetail->region_name : '';
        $subregion = !empty($projectDetail->sub_region_name) ? $projectDetail->sub_region_name : '';
        $category = !empty($projectDetail->project_name) ? $projectDetail->project_name : '';
        $landmark = !empty($projectDetail->landmark) ? $projectDetail->landmark : '';
        $clientId = !empty($projectDetail->clientid) ? $projectDetail->clientid : '';
        $description = !empty($projectDetail->description) ? $projectDetail->description : '';

        $this->load->model('tasks_model');
        foreach ($extendDeadlineData as $key => $task) {
            $task_id = $task['task_id'];
            $startDate = date('Y-m-d', strtotime($task['startDate']));
            $dueDate = date('Y-m-d', strtotime($task['dueDate']));
            // $task_days = $task['task_days'];
            $task_days = dateDiffInDays($dueDate, $startDate);
            $reminderone_days = getReminderOne($task_days); //$task['reminderOne_days'];
            $remindertwo_days = getReminderTwo($task_days); //$task['reminderTwo_days'];
            $subtask_name = $task['subtask_name'];
            $assignSubAt = $task['assignSubAt'];
            $subId = $key + 1;
            $midpart = '-00';
            if ($subId >= 10) {
                $midpart = '-0';
            }
            $sub_ticket_id = $projectId . $midpart . $subId;

            $reminderone_date = date('Y-m-d', strtotime($task['startDate'] . ' + ' . $reminderone_days . ' days'));
            $remindertwo_date = date('Y-m-d', strtotime($task['startDate'] . ' + ' . $remindertwo_days . ' days'));

            if ($task_days == 1) {
                $action_date = $dueDate;
            } else {
                $action_date = date('Y-m-d', strtotime($task['startDate'] . ' + 2 days'));
            }

            $ticketdata = [
                'name' => $category,
                'clientid' => $clientId,
                'project_members' => $assignSubAt,
                'landmark' => $landmark,
                'start_date' => $startDate,
                'deadline' => $dueDate,
                'description' => $description,
                'is_assigned' => 1,
                'status' => 1,
                'area_id' => !empty($projectDetail->area_id) ? $projectDetail->area_id : '',
                'region_id' => !empty($projectDetail->region_id) ? $projectDetail->region_id : '',
                'subregion_id' => !empty($projectDetail->subregion_id) ? $projectDetail->subregion_id : '',
                'issue_id' => !empty($projectDetail->issue_id) ? $projectDetail->issue_id : '',
                'updated_at' => date("Y-m-d"),
                'remail' => !empty($projectDetail->remail) ? $projectDetail->remail : '',
                'rphonenumber' => !empty($projectDetail->rphonenumber) ? $projectDetail->rphonenumber : '',
                'rname' => !empty($projectDetail->rname) ? $projectDetail->rname : '',
                'action_date' => $action_date,
                'parent_id' => $projectId,
                'sub_id' => $sub_ticket_id
            ];

            $id = $this->logTicket($ticketdata);
            // $id= '';
            $childTicketIds[] = array(
                'id' => $id,
                'sub_id' => $sub_ticket_id,
                'staff_id' => $assignSubAt
            );
            $childProjectFileIds[] = $id;

            if ($id) {
                $nwTaskDetails = [
                    'name' => $subtask_name,
                    'startdate' => $startDate,
                    'duedate' => $dueDate,
                    'reminderone_date' => $reminderone_date,
                    'remindertwo_date' => $remindertwo_date,
                    'addedfrom' => get_staff_user_id(),
                    'reminderone_days' => $reminderone_days,
                    'remindertwo_days' => $remindertwo_days,
                    'task_days' => $task_days,
                    'is_closed' => 0,
                    'rel_id' => $id,
                    'rel_type' => 'project',
                    'is_staff' => 1,
                    'milestone' => $task_id
                ];

                $this->load->model('tasks_model');
                $this->tasks_model->addMilestonesForTickets($nwTaskDetails);

                //Send Email Notification
                $assignedSubAt = $assignSubAt;

                if (!empty($assignedSubAt)) {
                    $emailNotificationData[] = array(
                        'email' => $email,
                        'assignedAt' => $assignedSubAt,
                        // 'id' => $id,
                        'id' => $sub_ticket_id,
                        'area' => $area,
                        'region' => $region,
                        'subregion' => $subregion,
                        'category' => $category,
                        'landmark' => $landmark,
                        'dueDate' => $dueDate
                    );
                    // $send = send_mail_template('New_ticket',$email,$assignedAt,1,$id, $area, $region, $subregion, $category, $projectDetail->landmark ,$dueDate);
                }

                //Copy evidence images file to new project
                //    $imgPath = FCPATH.'/uploads/projects/' . $project_id . '/';
                $src = FCPATH . 'uploads/projects/' . $projectId;
                $dst = FCPATH . 'uploads/projects/' . $id;
                recurse_copy_evidence($src, $dst);
            }

            if (empty($key)) {
                $nw_startDate = $startDate;
            }
            // $nw_deadline = $dueDate;
            if(strtotime($dueDate) > strtotime($nw_deadline)){
                $nw_deadline = $dueDate;
            }

            $affected_rows++;
        }

        //Update evidence images file data to new project
        $this->copyProjectFileData($projectId, $childProjectFileIds);

        //Update Parent Project with AT Assigned and add childTickets
        $childTickets = !empty($childTicketIds) ? json_encode($childTicketIds) : '';
        $newProjectStatus = array(
            'start_date' => $nw_startDate,
            'deadline' => $nw_deadline,
            'date_finished' => NULL,
            'action_date' => $nw_deadline,
            'status' => 1,
            'sub_ticket_id' => $childTickets,
        );

        $this->db->where('id', $projectId);
        $this->db->update(db_prefix() . 'projects', $newProjectStatus);

        $additional_data = array(
            'assigned_by' => get_staff_user_id(),
            'assigned_to' => '',
            'taskId' => '',
            'status' => 1,
            'comment' => '',
            'child_ids' => $childTickets
        );
        $this->log_activity($projectId, 'sub_tickets', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));

        //Set sub-ticket log for child tickets
        foreach ($childTicketIds as $key => $ticket) {
            $childId = $ticket['id'];
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => '',
                'taskId' => '',
                'status' => 1,
                'comment' => '',
                'child_ids' => $childTickets
            );
            $this->log_activity($childId, 'sub_tickets', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
        }

        $emailNotificationData[] = array(
            'email' => (!empty($assignAt)) ? get_staff_email($assignAt, "") : get_staff_email("", $projectDetail->area_id),
            'assignedAt' => $assignAt,
            'id' => $projectId,
            'area' => $area,
            'region' => $region,
            'subregion' => $subregion,
            'category' => $category,
            'landmark' => $landmark,
            'dueDate' => $nw_deadline
        );

        //Send Email Notification
        if (!empty($emailNotificationData))
            foreach ($emailNotificationData as $data) {
                $send = send_mail_template('New_ticket', $data['email'], $data['assignedAt'], 1, $data['id'], $data['area'], $data['region'], $data['subregion'], $data['category'], $data['landmark'], $data['dueDate']);
            }

        if ($affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function copyProjectFileData($projectId, $childProjectFileIds)
    {

        $this->load->model('dashboard_model');
        $projectFileData = $this->dashboard_model->get_evidence_image($projectId, '', 1);

        foreach ($childProjectFileIds as $childId) {
            foreach ($projectFileData as $data) {
                unset($data['id']);
                $data['project_id'] = $childId;
                $data['dateadded'] = date('Y-m-d H:i:s');

                $this->db->insert(db_prefix() . 'project_files', $data);
            }
        }
        return true;
    }

    public function close_parent_ticket($projectId,$comment)
    {
        //check if ticket has parent ticket
        $parentId = $this->hasParentTicket($projectId);
        if (!empty($parentId)) {
            //Save log for sub-ticket close in parent ticekt
            $p_additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 10, //Closed status of sub-ticket for parent ticket
                'comment' => $comment
            );
            $subTicketId = $this->getSubTicketName($projectId);
            $label = "Project <strong>$subTicketId</strong> closed by Project Leader";
            if($GLOBALS['current_user']->role_slug_url == 'ar'){
                $label = "Project <strong>$subTicketId</strong> closed by Project Reviewer";
            }
            $this->log_activity($parentId, $label, get_staff_full_name(get_staff_user_id()), json_encode($p_additional_data));

            $updateProjectStatus = false;
            //check if all sub-ticket of parent ticket has been resolved
            $subTicketOpened = $this->getOpenedSubTickets($parentId);
            if($subTicketOpened>0){
                $updateProjectStatus = false;
            }else{
                $updateProjectStatus = true;
            }

            // $subChilds = $this->getSubChildTickets($parentId);
            // if (!empty($subChilds)) {
            //     $childs =  isJson($subChilds)?json_decode($subChilds):'';
            //     $updateProjectStatus = false;
            //     if(!empty($childs)){
            //         $childIds = [];
            //         foreach ($childs as $key => $child) {
            //             $childProjectId = $child->id;
            //             // $childIds[] = $child->id;
            //             $childProjectStatus = $this->getProjectStatus($childProjectId);
            //             if ($childProjectStatus != 3) {
            //                 $updateProjectStatus = false;
            //             }else{
            //                 $updateProjectStatus = true;
            //             }
            //         }
            //         //check if all sub-tickets are closed
            //     }
            // var_dump($subTicketOpened); die;

            //Update Project Status if all child is closed
            if ($updateProjectStatus) {
                $updateData = array(
                                'status' => 3,
                                'action_date' => date('Y-m-d'),
                                "date_finished" => date('Y-m-d h:i:s')
                            );
                $updateStatus = $this->updateProjectStatus($parentId, $updateData);
                $this->db->where("id", $projectId);
                $this->db->update(db_prefix() . 'projects', ["date_finished" => date('Y-m-d h:i:s')]);
                if ($updateStatus) {
                    $additional_data = array(
                        'assigned_by' => get_staff_user_id(),
                        'assigned_to' => '',
                        'taskId' => '',
                        'status' => 3,
                        'comment' => ''
                    );
                    $this->log_activity($parentId, 'close_parent_ticket', get_staff_full_name(get_staff_user_id()), json_encode($additional_data));
                    return true;
                }
                return false;
            }
            //     return false;
            // }
            return false;
        }
        return false;
    }

    public function getOpenedSubTickets($parentId){
        $this->db->where('parent_id', $parentId);
        $this->db->where('status !=', 3);
        $_exists = $this->db->get(db_prefix() . 'projects')->result_array();
        // echo $this->db->last_query(); die;
        
        if (!empty($_exists)) {
            return count($_exists);
        } else {
            return 0;
        }
    }

    public function hasParentTicket($projectId)
    {
        $this->db->where('id', $projectId);
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        $parent_id = '';
        if ($_exists) {
            $parent_id = $_exists->parent_id;
        }

        return $parent_id;
    }

    public function getSubChildTickets($projectId)
    {
        $this->db->where('id', $projectId);
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        $sub_ticket_id = '';
        if ($_exists) {
            $sub_ticket_id = (!empty($_exists->sub_ticket_id)) ? $_exists->sub_ticket_id : '';
        }
        return $sub_ticket_id;
    }

    public function getProjectStatus($projectId)
    {
        $this->db->where('id', $projectId);
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        $status = '';
        if ($_exists) {
            $status = !empty($_exists->status) ? $_exists->status : '';
        }
        return $status;
    }

    public function getSubTicketName($projectId)
    {
        $this->db->where('id', $projectId);
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        $sub_id = '';
        if ($_exists) {
            $sub_id =  !empty($_exists->sub_id) ? $_exists->sub_id : '';
        }
        return $sub_id;
    }

    public function updateReAssignedUser($projectId, $staffId, $reassign = NULL)
    {
        //Update all assigned to un-assigned
        $this->db->where('project_id', $projectId);
        $updateData = array();
        // $updateData = array(
        //     'assigned' => 0,
        // );
        if(!empty($reassign)){
            $updateData = array(
                'assigned' => 0,
            );
        }else{
            $updateData = array(
                'assigned' => 0,
                'active' => 0
            );
        }
        $unassignedUsers = $this->db->update(db_prefix() . 'project_members', $updateData);

        //assigned ticket to staff
        $this->db->where('project_id', $projectId);
        $this->db->where('staff_id', $staffId);
        $_exists = $this->db->get(db_prefix() . 'project_members')->row();
        if (!$_exists) {
            $this->db->insert(db_prefix() . 'project_members', [
                'project_id' => $projectId,
                'staff_id'   => $staffId,
            ]);
        } else {
            $this->db->where('project_id', $projectId);
            $this->db->where('staff_id', $staffId);
            $this->db->update(db_prefix() . 'project_members', ['assigned' => 1, 'active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $staff_name = get_staff_full_name($staffId);
        $this->load->model('staff_model');
        $userDetail = $this->staff_model->get_userDetails($staffId);
        $role = !empty($userDetail->role) ? $userDetail->role : '';
        $label = 'ticket_assigned_to_you';
        $status = 1;
        if ($role == 3) {
            $label = 'ticket_assigned_to_at';
        } else if ($role == 4) {
            $label = 'ticket_assigned_to_ar';
            $status = 5;
        } else if ($role == 8) {
            $label = 'ticket_assigned_to_ata';
        }

        if ($reassign == 2) {
            $label = 'Parent project <strong>' . $projectId . '</strong> assigned to Project Leader';
        }

        $additional_data = array(
            'assigned_by' => get_staff_user_id(),
            'assigned_to' => $staffId,
            'taskId' => '',
            'status' => $status,
            'comment' => ''
        );
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($projectId, $label, $staff_name, json_encode($additional_data));
            return true;
        } else {
            return false;
        }
    }

    public function calculate_project_days_left($projectId)
    {
        $this->load->model('report_model');
        $milestone = $this->report_model->get_current_milestone($projectId);

        $milestone = $milestone[0];
        $start_date = (!empty($milestone['startdate'])) ? $milestone['startdate'] : '';
        $deadline = (!empty($milestone['duedate'])) ? $milestone['duedate'] : '';
        $task_days = (!empty($milestone['task_days'])) ? $milestone['task_days'] : '';
        $project_days_left = $task_days;
        if ($deadline) {
            if (human_to_unix($start_date . ' 00:00') < time() && human_to_unix($deadline . ' 00:00') > time()) {
                // $project_days_left         = round((human_to_unix($deadline . ' 00:00') - time()) / 3600 / 24);
                $project_days_left         = ceil((human_to_unix($deadline . ' 00:00') - time()) / 3600 / 24);
            } else if (human_to_unix($deadline . ' 00:00') < time()) {
                $project_days_left         = 0;
            }
        }
        return $project_days_left;
    }

    public function sendAssignEmailNotification($projectId, $staffId, $type, $comment = NULL)
    {
        $projectDetail = $this->get_project_details($projectId);

        $email = (!empty($staffId)) ? get_staff_email($staffId, "") : get_staff_email("", $projectDetail->area_id);
        $area = !empty($projectDetail->area_name) ? $projectDetail->area_name : '';
        $region = !empty($projectDetail->region_name) ? $projectDetail->region_name : '';
        $subregion = !empty($projectDetail->sub_region_name) ? $projectDetail->sub_region_name : '';
        $category = !empty($projectDetail->project_name) ? $projectDetail->project_name : '';
        $landmark = !empty($projectDetail->landmark) ? $projectDetail->landmark : '';
        $clientId = !empty($projectDetail->clientid) ? $projectDetail->clientid : '';
        $description = !empty($projectDetail->description) ? $projectDetail->description : '';
        $dueDate = !empty($projectDetail->deadline) ? date('d-M-Y', strtotime($projectDetail->deadline)) : '';

        if ($type == 'New_ticket') {
            $send = send_mail_template('New_ticket', $email, $staffId, 1, $projectId, $area, $region, $subregion, $category, $landmark, $dueDate);
        } else if ($type == 'Ticket_reopened') {
            $reOpenedBy = get_staff_full_name(get_staff_user_id());
            $org = !empty($GLOBALS['current_user']->organisation) ? $GLOBALS['current_user']->organisation : '';
            $desg = !empty($GLOBALS['current_user']->role_name) ? $GLOBALS['current_user']->role_name : '';
            $projectNotes = project_latest_notes($projectId, 4);
            $latestComment = !empty($projectNotes->content) ? $projectNotes->content : '';

            $send = send_mail_template('Ticket_reopened', $email, $staffId, 1, $projectId, $area, $region, $subregion, $category, $landmark, $dueDate, $reOpenedBy, $org, $desg, $latestComment, $comment);
        }
    }

    public function sendReopenMailToAllMember($projectId, $reopenReason = NULL)
    {
        //Send Email Notification
        $allMembers = $this->get_project_members($projectId);
        foreach ($allMembers as $key => $member) {
            $staff_id = $member['staff_id'];
            if (in_array($GLOBALS['current_user']->role_slug_url, ['ar', 'at']) && $staff_id == get_staff_user_id()) {
                continue;
            }
            $this->sendAssignEmailNotification($projectId, $staff_id, 'Ticket_reopened', $reopenReason);
        }
    }

    public function getProjectDueDate($projectId)
    {
        $this->db->where('id', $projectId);
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        $deadline = '';
        if ($_exists) {
            $deadline = !empty($_exists->deadline) ? $_exists->deadline : '';
        }
        return $deadline;
    }

    public function update_parent_action_date($projectId){
        $this->db->select('id,sub_id, deadline');
        $this->db->where('parent_id', $projectId);
        $this->db->order_by('deadline','DESC');
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        // echo $this->db->last_query(); die;
        
        if (!empty($_exists)) {
            $deadline = !empty($_exists->deadline)?$_exists->deadline:'';
            if(!empty($deadline)){
                $updateData = array(
                    'action_date' => $deadline,
                );
                $updateStatus = $this->updateProjectStatus($projectId, $updateData);
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function get_subproject_latest_due_date($projectId){
        $this->db->select('id,sub_id, deadline');
        $this->db->where('parent_id', $projectId);
        $this->db->order_by('deadline','DESC');
        $_exists = $this->db->get(db_prefix() . 'projects')->row();
        // echo $this->db->last_query(); die;
        
        if (!empty($_exists)) {
            $deadline = !empty($_exists->deadline)?$_exists->deadline:'';
            if(!empty($deadline)){
                
                return $deadline;
            }
            return false;
        } else {
            return false;
        }
    }
    
    public function getDelayedProjects(){

        $this->db->select('p.id as project_id');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->where('(p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < CURDATE() )');
        $this->db->where('p.start_date <= CURDATE()');
        $this->db->where('p.frozen = 0');
        $this->db->order_by('action_date,p.status');
        // $this->db->limit(10);
        
        $result = $this->db->get()->result_array();
        $delayedProject = array();
        if(!empty($result)){
            $delayedProject = array_column($result, 'project_id');
        }
        return $delayedProject;
    }
    
    public function getDelayedFrozenHistoryProjects($description_key,$status,$delayedProjects = NULL){

        $this->db->select('project_id');
        $this->db->from(db_prefix() . 'project_activity');
        $this->db->where('status',$status);
        $this->db->where('description_key',$description_key);
        $this->db->where('updated_at =', null);
        if(!empty($delayedProjects)){
            $this->db->where_in('project_id',$delayedProjects);
        }
        $result = $this->db->get()->result_array();
        $delayedProject = array();
        if(!empty($result)){
            $delayedProject = array_column($result, 'project_id');
        }
        return $delayedProject;
    }

    public function updateDelayedHistoryProjects($updatedProjects,$description_key,$status){

        $this->db->where_in('project_id', $updatedProjects);
        $this->db->where('status',$status);
        $this->db->where('description_key',$description_key);
        $this->db->where('updated_at =', null);
        $updateData = array(
            'updated_at' => date("Y-m-d H:i:s")
        );

        $this->db->update(db_prefix() . 'project_activity', $updateData);

        if ($this->db->affected_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function addDelayedFrozenProjects($updatedProjects,$description_key,$status){
        foreach($updatedProjects as $projectId){
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => '',
                'taskId' => '',
                'status' => $status,
                'comment' => ''
            ); 
            
            $this->log_activity($projectId, $description_key, get_staff_full_name(get_staff_user_id()),json_encode($additional_data));
        }
        return true;
    }

    public function getUnFrozenProjectsStatus($projectIds){

        $this->db->select('p.id as project_id');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->where('(p.status IN (1,2,4,6) AND p.action_date < CURDATE())');
        $this->db->where('p.start_date <= CURDATE()');
        // $this->db->where('p.frozen = 0');
        $this->db->where_in('p.id',$projectIds);
        $this->db->order_by('action_date,p.status');
        // $this->db->limit(10);
        
        $result = $this->db->get()->result_array();
        $delayedProject = array();
        if(!empty($result)){
            $delayedProject = array_column($result, 'project_id');
        }
        return $delayedProject;
    }

    public function update_delayed_projects($delayedProjects){
        //check if any delayed project updated his state
        $delayedHistoryProjects = $this->getDelayedFrozenHistoryProjects('project_delayed',7,$delayedProjects);
        
        $delayedStatusProjects = array_diff($delayedProjects,$delayedHistoryProjects);
        
        //update project_activity table with delayed projects
        if(!empty($delayedStatusProjects)){
            $this->addDelayedFrozenProjects($delayedStatusProjects,'project_delayed',7);
        }
    }
}

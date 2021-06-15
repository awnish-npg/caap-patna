<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class Clients extends ClientsController
{
    /**
     * @since  2.3.3
     */
    use ValidatesContact;

    public function __construct()
    {
        parent::__construct();

        hooks()->do_action('after_clients_area_init', $this);
    }

    public function index()
    {
        // $data['is_home'] = true;
        // $this->load->model('reports_model');
        // $data['payments_years'] = $this->reports_model->get_distinct_customer_invoices_years();

        // $data['project_statuses'] = $this->projects_model->get_project_statuses();
        // $data['title']            = get_company_name(get_client_user_id());
        // $this->data($data);
        // $this->view('home');
        // $this->layout();
        redirect(site_url('authentication'));
    }

    public function announcements()
    {
        $data['title']         = _l('announcements');
        $data['announcements'] = $this->announcements_model->get();
        $this->data($data);
        $this->view('announcements');
        $this->layout();
    }

    public function announcement($id)
    {
        $data['announcement'] = $this->announcements_model->get($id);
        $data['title']        = $data['announcement']->name;
        $this->data($data);
        $this->view('announcement');
        $this->layout();
    }

    public function calendar()
    {
        $data['title'] = _l('calendar');
        $this->view('calendar');
        $this->data($data);
        $this->layout();
    }

    public function get_calendar_data()
    {
        $this->load->model('utilities_model');
        $data = $this->utilities_model->get_calendar_data(
            $this->input->get('start'),
            $this->input->get('end'),
            get_user_id_by_contact_id(get_contact_user_id()),
            get_contact_user_id()
        );

        echo json_encode($data);
    }

    public function projects($status = '')
    {
        if (!has_contact_permission('projects')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $where = 'clientid=' . get_client_user_id();

        if (is_numeric($status)) {
            $where .= ' AND status=' . $this->db->escape_str($status);
        } else {
            $listStatusesIds = [];
            $where .= ' AND status IN (';
            foreach ($data['project_statuses'] as $projectStatus) {
                if (isset($projectStatus['filter_default']) && $projectStatus['filter_default'] == true) {
                    $listStatusesIds[] = $projectStatus['id'];
                    $where .= $this->db->escape_str($projectStatus['id']) . ',';
                }
            }
            $where = rtrim($where, ',');
            $where .= ')';
        }

        $data['list_statuses'] = is_numeric($status) ? [$status] : $listStatusesIds;
        $data['projects']      = $this->projects_model->get('', $where);
        $data['title']         = _l('clients_my_tickets');
        $this->data($data);
        $this->view('projects');
        $this->layout();
    }

    public function project($id)
    {
        if (!has_contact_permission('projects')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $project = $this->projects_model->get($id, [
            'clientid' => get_client_user_id(),
        ]);

        if (!$project) {
            show_404();
        }

        $data['project']                               = $project;
        $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);

        $data['title'] = $data['project']->name;
        if ($this->input->post('action')) {
            $action = $this->input->post('action');

            switch ($action) {
                  case 'new_task':
                  case 'edit_task':

                    $data    = $this->input->post();
                    $task_id = false;
                    if (isset($data['task_id'])) {
                        $task_id = $data['task_id'];
                        unset($data['task_id']);
                    }

                    $data['rel_type']    = 'project';
                    $data['rel_id']      = $project->id;
                    $data['description'] = nl2br($data['description']);

                    $assignees = isset($data['assignees']) ? $data['assignees'] : [];
                    if (isset($data['assignees'])) {
                        unset($data['assignees']);
                    }
                    unset($data['action']);

                    if (!$task_id) {
                        $task_id = $this->tasks_model->add($data, true);
                        if ($task_id) {
                            foreach ($assignees as $assignee) {
                                $this->tasks_model->add_task_assignees(['taskid' => $task_id, 'assignee' => $assignee], false, true);
                            }
                            $uploadedFiles = handle_task_attachments_array($task_id);
                            if ($uploadedFiles && is_array($uploadedFiles)) {
                                foreach ($uploadedFiles as $file) {
                                    $file['contact_id'] = get_contact_user_id();
                                    $this->misc_model->add_attachment_to_database($task_id, 'task', [$file]);
                                }
                            }
                            set_alert('success', _l('added_successfully', _l('task')));
                            redirect(site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $task_id));
                        }
                    } else {
                        if ($project->settings->edit_tasks == 1
                            && total_rows(db_prefix() . 'tasks', ['is_added_from_contact' => 1, 'addedfrom' => get_contact_user_id()]) > 0) {
                            $affectedRows = 0;
                            $updated      = $this->tasks_model->update($data, $task_id, true);
                            if ($updated) {
                                $affectedRows++;
                            }

                            $currentAssignees    = $this->tasks_model->get_task_assignees($task_id);
                            $currentAssigneesIds = [];
                            foreach ($currentAssignees as $assigned) {
                                array_push($currentAssigneesIds, $assigned['assigneeid']);
                            }

                            $totalAssignees = count($assignees);

                            /**
                             * In case when contact created the task and then was able to view team members
                             * Now in this case he still can view team members and can edit them
                             */
                            if ($totalAssignees == 0 && $project->settings->view_team_members == 1) {
                                $this->db->where('taskid', $task_id);
                                $this->db->delete(db_prefix() . 'task_assigned');
                            } elseif ($totalAssignees > 0 && $project->settings->view_team_members == 1) {
                                foreach ($currentAssignees as $assigned) {
                                    if (!in_array($assigned['assigneeid'], $assignees)) {
                                        if ($this->tasks_model->remove_assignee($assigned['id'], $task_id)) {
                                            $affectedRows++;
                                        }
                                    }
                                }
                                foreach ($assignees as $assignee) {
                                    if (!$this->tasks_model->is_task_assignee($assignee, $task_id)) {
                                        if ($this->tasks_model->add_task_assignees(['taskid' => $task_id, 'assignee' => $assignee], false, true)) {
                                            $affectedRows++;
                                        }
                                    }
                                }
                            }
                            if ($affectedRows > 0) {
                                set_alert('success', _l('updated_successfully', _l('task')));
                            }
                            redirect(site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $task_id));
                        }
                    }

                    redirect(site_url('clients/project/' . $project->id . '?group=project_tasks'));

                    break;
                case 'discussion_comments':
                    echo json_encode($this->projects_model->get_discussion_comments($this->input->post('discussion_id'), $this->input->post('discussion_type')));
                    die;
                case 'new_discussion_comment':
                    echo json_encode($this->projects_model->add_discussion_comment($this->input->post(), $this->input->post('discussion_id'), $this->input->post('discussion_type')));
                    die;

                    break;
                case 'update_discussion_comment':
                    echo json_encode($this->projects_model->update_discussion_comment($this->input->post(), $this->input->post('discussion_id')));
                    die;

                    break;
                case 'delete_discussion_comment':
                    echo json_encode($this->projects_model->delete_discussion_comment($this->input->post('id')));
                    die;

                    break;
                case 'new_discussion':
                    $discussion_data = $this->input->post();
                    unset($discussion_data['action']);
                    $success = $this->projects_model->add_discussion($discussion_data);
                    if ($success) {
                        set_alert('success', _l('added_successfully', _l('project_discussion')));
                    }
                    redirect(site_url('clients/project/' . $id . '?group=project_discussions'));

                    break;
                case 'upload_file':
                    handle_project_file_uploads($id);
                    die;

                    break;
                case 'project_file_dropbox': // deprecated
                case 'project_external_file':
                        $data                        = [];
                        $data['project_id']          = $id;
                        $data['files']               = $this->input->post('files');
                        $data['external']            = $this->input->post('external');
                        $data['visible_to_customer'] = 1;
                        $data['contact_id']          = get_contact_user_id();
                        $this->projects_model->add_external_file($data);
                die;

                break;
                case 'get_file':
                    $file_data['discussion_user_profile_image_url'] = contact_profile_image_url(get_contact_user_id());
                    $file_data['current_user_is_admin']             = false;
                    $file_data['file']                              = $this->projects_model->get_file($this->input->post('id'), $this->input->post('project_id'));

                    if (!$file_data['file']) {
                        header('HTTP/1.0 404 Not Found');
                        die;
                    }
                    echo get_template_part('projects/file', $file_data, true);
                    die;

                    break;
                case 'update_file_data':
                    $file_data = $this->input->post();
                    unset($file_data['action']);
                    $this->projects_model->update_file_data($file_data);

                    break;
                case 'upload_task_file':
                    $taskid = $this->input->post('task_id');
                    $files  = handle_task_attachments_array($taskid, 'file');
                    if ($files) {
                        $i   = 0;
                        $len = count($files);
                        foreach ($files as $file) {
                            $file['contact_id'] = get_contact_user_id();
                            $file['staffid']    = 0;
                            $this->tasks_model->add_attachment_to_database($taskid, [$file], false, ($i == $len - 1 ? true : false));
                            $i++;
                        }
                    }
                    die;

                    break;
                case 'add_task_external_file':
                    $taskid                = $this->input->post('task_id');
                    $file                  = $this->input->post('files');
                    $file[0]['contact_id'] = get_contact_user_id();
                    $file[0]['staffid']    = 0;
                    $this->tasks_model->add_attachment_to_database($this->input->post('task_id'), $file, $this->input->post('external'));
                    die;

                    break;
                case 'new_task_comment':
                    $comment_data            = $this->input->post();
                    $comment_data['content'] = nl2br($comment_data['content']);
                    $comment_id              = $this->tasks_model->add_task_comment($comment_data);
                    $url                     = site_url('clients/project/' . $id . '?group=project_tasks&taskid=' . $comment_data['taskid']);

                    if ($comment_id) {
                        set_alert('success', _l('task_comment_added'));
                        $url .= '#comment_' . $comment_id;
                    }

                    redirect($url);

                    break;
                default:
                    redirect(site_url('clients/project/' . $id));

                    break;
            }
        }
        if (!$this->input->get('group')) {
            $group = 'project_overview';
        } else {
            $group = $this->input->get('group');
        }
        $data['project_status'] = get_project_status_by_id($data['project']->status);
        if ($group != 'edit_task') {
            if ($group == 'project_overview') {
                $percent          = $this->projects_model->calc_progress($id);
                @$data['percent'] = $percent / 100;
                $this->load->helper('date');
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left'] = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);

                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                        $data['project_time_left_percent'] = round($data['project_time_left_percent'], 2);
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }
                $total_tasks = total_rows(db_prefix() . 'tasks', [
                    'rel_id'            => $id,
                    'rel_type'          => 'project',
                    'visible_to_client' => 1,
                ]);
                $total_tasks = hooks()->apply_filters('client_project_total_tasks', $total_tasks, $id);

                $data['tasks_not_completed'] = total_rows(db_prefix() . 'tasks', [
                'status !='         => 5,
                'rel_id'            => $id,
                'rel_type'          => 'project',
                'visible_to_client' => 1,
            ]);

                $data['tasks_not_completed'] = hooks()->apply_filters('client_project_tasks_not_completed', $data['tasks_not_completed'], $id);

                $data['tasks_completed'] = total_rows(db_prefix() . 'tasks', [
                'status'            => 5,
                'rel_id'            => $id,
                'rel_type'          => 'project',
                'visible_to_client' => 1,
            ]);
                $data['tasks_completed'] = hooks()->apply_filters('client_project_tasks_completed', $data['tasks_completed'], $id);

                $data['total_tasks']                  = $total_tasks;
                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);
                $data['tasks_not_completed_progress'] = round($data['tasks_not_completed_progress'], 2);
            } elseif ($group == 'new_task') {
                if ($project->settings->create_tasks == 0) {
                    redirect(site_url('clients/project/' . $project->id));
                }
                $data['milestones'] = $this->projects_model->get_milestones($id);
            } elseif ($group == 'project_gantt') {
                $data['gantt_data'] = $this->projects_model->get_gantt_data($id);
            } elseif ($group == 'project_discussions') {
                if ($this->input->get('discussion_id')) {
                    $data['discussion_user_profile_image_url'] = contact_profile_image_url(get_contact_user_id());
                    $data['discussion']                        = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                    $data['current_user_is_admin']             = false;
                }
                $data['discussions'] = $this->projects_model->get_discussions($id);
            } elseif ($group == 'project_files') {
                $data['files'] = $this->projects_model->get_files($id);
            } elseif ($group == 'project_tasks') {
                $data['tasks_statuses'] = $this->tasks_model->get_statuses();
                $data['project_tasks']  = $this->projects_model->get_tasks($id);
            } elseif ($group == 'project_activity') {
                $data['activity'] = $this->projects_model->get_activity($id);
            } elseif ($group == 'project_milestones') {
                $data['milestones'] = $this->projects_model->get_milestones($id);
            } elseif ($group == 'project_invoices') {
                $data['invoices'] = [];
                if (has_contact_permission('invoices')) {
                    $whereInvoices = [
                            'clientid'   => get_client_user_id(),
                            'project_id' => $id,
                        ];
                    if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                        $whereInvoices['status !='] = 6;
                    }
                    $data['invoices'] = $this->invoices_model->get('', $whereInvoices);
                }
            } elseif ($group == 'project_tickets') {
                $data['tickets'] = [];
                if (has_contact_permission('support')) {
                    $where_tickets = [
                        db_prefix() . 'tickets.userid' => get_client_user_id(),
                        'project_id'                   => $id,
                    ];

                    if (!!can_logged_in_contact_view_all_tickets()) {
                        $where_tickets[db_prefix() . 'tickets.contactid'] = get_contact_user_id();
                    }

                    $data['tickets']                 = $this->tickets_model->get('', $where_tickets);
                    $data['show_submitter_on_table'] = show_ticket_submitter_on_clients_area_table();
                }
            } elseif ($group == 'project_estimates') {
                $data['estimates'] = [];
                if (has_contact_permission('estimates')) {
                    $data['estimates'] = $this->estimates_model->get('', [
                            'clientid'   => get_client_user_id(),
                            'project_id' => $id,
                        ]);
                }
            } elseif ($group == 'project_timesheets') {
                $data['timesheets'] = $this->projects_model->get_timesheets($id);
            }

            if ($this->input->get('taskid')) {
                $data['view_task'] = $this->tasks_model->get($this->input->get('taskid'), [
                    'rel_id'   => $project->id,
                    'rel_type' => 'project',
                ]);

                $data['title'] = $data['view_task']->name;
            }
        } elseif ($group == 'edit_task') {
            $data['milestones'] = $this->projects_model->get_milestones($id);
            $data['task']       = $this->tasks_model->get($this->input->get('taskid'), [
                    'rel_id'                => $project->id,
                    'rel_type'              => 'project',
                    'addedfrom'             => get_contact_user_id(),
                    'is_added_from_contact' => 1,
                ]);
        }

        $data['group']    = $group;
        $data['currency'] = $this->projects_model->get_currency($id);
        $data['members']  = $this->projects_model->get_project_members($id);

        $this->data($data);
        $this->view('project');
        $this->layout();
    }

    public function download_all_project_files($id)
    {
        if (!has_contact_permission('projects')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $files = $this->projects_model->get_files($id);

        if (count($files) == 0) {
            set_alert('warning', _l('no_files_found'));
            redirect(site_url('clients/project/' . $id . '?group=project_files'));
        }

        $path = get_upload_path_by_type('project') . $id;
        $this->load->library('zip');

        foreach ($files as $file) {
            $this->zip->read_file($path . '/' . $file['file_name']);
        }

        $this->zip->download(slug_it(get_project_name_by_id($id)) . '-files.zip');
        $this->zip->clear_data();
    }

    public function files()
    {
        $files_where = 'visible_to_customer = 1 AND id IN (SELECT file_id FROM ' . db_prefix() . 'shared_customer_files WHERE contact_id =' . get_contact_user_id() . ')';

        $files_where = hooks()->apply_filters('customers_area_files_where', $files_where);

        $files = $this->clients_model->get_customer_files(get_client_user_id(), $files_where);

        $data['files'] = $files;
        $data['title'] = _l('customer_attachments');
        $this->data($data);
        $this->view('files');
        $this->layout();
    }

    public function upload_files()
    {
        $success = false;
        if ($this->input->post('external')) {
            $file                        = $this->input->post('files');
            $file[0]['staffid']          = 0;
            $file[0]['contact_id']       = get_contact_user_id();
            $file['visible_to_customer'] = 1;
            $success                     = $this->misc_model->add_attachment_to_database(
                get_client_user_id(),
                'customer',
                $file,
                $this->input->post('external')
            );
        } else {
            $success = handle_client_attachments_upload(get_client_user_id(), true);
        }

        if ($success) {
            $this->clients_model->send_notification_customer_profile_file_uploaded_to_responsible_staff(
                get_contact_user_id(),
                get_client_user_id()
            );
        }
    }

    public function delete_file($id, $type = '')
    {
        if (get_option('allow_contact_to_delete_files') == 1) {
            if ($type == 'general') {
                $file = $this->misc_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->clients_model->delete_attachment($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/files'));
            } elseif ($type == 'project') {
                $this->load->model('projects_model');
                $file = $this->projects_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->projects_model->remove_file($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/project/' . $file->project_id . '?group=project_files'));
            } elseif ($type == 'task') {
                $file = $this->misc_model->get_file($id);
                if ($file->contact_id == get_contact_user_id()) {
                    $this->tasks_model->remove_task_attachment($id);
                    set_alert('success', _l('deleted', _l('file')));
                }
                redirect(site_url('clients/project/' . $this->input->get('project_id') . '?group=project_tasks&taskid=' . $file->rel_id));
            }
        }
        redirect(site_url());
    }

    public function remove_task_comment($id)
    {
        echo json_encode([
            'success' => $this->tasks_model->remove_comment($id),
        ]);
    }

    public function edit_comment()
    {
        if ($this->input->post()) {
            $data            = $this->input->post();
            $data['content'] = nl2br($data['content']);
            $success         = $this->tasks_model->edit_comment($data);
            if ($success) {
                set_alert('success', _l('task_comment_updated'));
            }
            echo json_encode([
                'success' => $success,
            ]);
        }
    }

    public function tickets($status = '')
    {
        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $where = db_prefix() . 'tickets.userid=' . get_client_user_id();
        if (!can_logged_in_contact_view_all_tickets()) {
            $where .= ' AND ' . db_prefix() . 'tickets.contactid=' . get_contact_user_id();
        }

        $data['show_submitter_on_table'] = show_ticket_submitter_on_clients_area_table();

        $defaultStatuses = hooks()->apply_filters('customers_area_list_default_ticket_statuses', [1, 2, 3, 4]);
        // By default only open tickets
        if (!is_numeric($status)) {
            $where .= ' AND status IN (' . implode(', ', $defaultStatuses) . ')';
        } else {
            $where .= ' AND status=' . $this->db->escape_str($status);
        }

        $data['list_statuses'] = is_numeric($status) ? [$status] : $defaultStatuses;
        $data['bodyclass']     = 'tickets';
        $data['tickets']       = $this->tickets_model->get('', $where);
        $data['title']         = _l('clients_tickets_heading');
        $this->data($data);
        $this->view('tickets');
        $this->layout();
    }

    public function change_ticket_status()
    {
        if (has_contact_permission('support')) {
            $post_data = $this->input->post();
            if (can_change_ticket_status_in_clients_area($post_data['status_id'])) {
                $response = $this->tickets_model->change_ticket_status($post_data['ticket_id'], $post_data['status_id']);
                set_alert($response['alert'], $response['message']);
            }
        }
    }

    public function proposals()
    {
        if (!has_contact_permission('proposals')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $where = 'rel_id =' . get_client_user_id() . ' AND rel_type ="customer"';

        if (get_option('exclude_proposal_from_client_area_with_draft_status') == 1) {
            $where .= ' AND status != 6';
        }

        $client = $this->clients_model->get(get_client_user_id());

        if (!is_null($client->leadid)) {
            $where .= ' OR rel_type="lead" AND rel_id=' . $client->leadid;
        }

        $data['proposals'] = $this->proposals_model->get('', $where);
        $data['title']     = _l('proposals');
        $this->data($data);
        $this->view('proposals');
        $this->layout();
    }

    public function get_admin_area()
    {
        $this->load->model('area_model');
        $data = $this->input->post();

        if ($data['exclude_staff_area']) {
            $area_list = $this->area_model->get(false, false, true);
        } else {
            $area_list = $this->area_model->get(false, true, false);
        }
        echo json_encode(['success' => true, 'area_list' => $area_list]);
        die;
    }

    public function get_area_issues()
    {
        $this->load->model('issue_model');
        $response = array(
            'success' => false,
            'message' => "No Categories Found"
        );
        if ($this->input->post()) {
            $issues = $this->issue_model->get_area_issues_for_client($this->input->post('area_id'));
            if ($issues) {
                $response = [
                    'success' => true,
                    'message' => "Issues fetched successfully.",
                    'issues' => $issues
                ];
            }
        }

        echo json_encode($response);
        die;
    }

    public function get_region()
    {
        $this->load->model('region_model');
        if ($this->input->post()) {
            $area_id = $this->input->post('area_id');
            $region_list = $this->region_model->get_area_region($area_id);
            $grouped_region_list = $region_list;
            if (count($region_list) > 0) {
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
                    'message' => "No City found.",
                ]);
            }
        }
        die;
    }
    public function add_external_file($data)
    {
        $this->load->model('projects_model');
        // if ($this->input->post()) {
            // $data                        = [];
            // $data['project_id']          = $this->input->post('project_id');
            // $data['files']               = $this->input->post('files');
            // $data['external']            = $this->input->post('external');
            // $data['visible_to_customer'] = ($this->input->post('visible_to_customer') == 'true' ? 1 : 0);
            // $data['contact_id']             = $this->session->userdata('client_user_id');
            $this->projects_model->add_external_file($data);
        // }
    }
    public function getsubregion(){
        $res=getsubregion($this->input->post('regionid'));
        if(count($res)>0){
            echo json_encode([
                'success' => true,
                'message' => "Successfully fetched the region list.",
                'sub_region_list' => $res
            ]);
        }
        else{
            echo json_encode([
                'success' => false,
                'message' => "No Municipal Zone found.",
            ]);
        }

    }
    public function getarea(){
       $res= get_all_areas();
       if(count($res)>0){
        echo json_encode([
            'success' => true,
            'message' => "Successfully fetched the area list.",
            'areas' => $res
        ]);
    }
    else{
        echo json_encode([
            'success' => false,
            'message' => "No state found.",
        ]);
    }

    }
    public function get_surveyor_detail(){
        $id=$this->session->userdata('client_user_id');
        $res=surveyor_area_region_subregion($id);
        if(!empty($res)){
            echo json_encode([
                'area'=>$res['area_id'],
                'region'=>$res['region_id'],
                'subregion'=>$res['subregion_id']
            ]);
        }

    }
    public function file_check(){
      for($i=0;$i<count($_FILES['file']['tmp_name']);$i++){
        $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        $allowed_mime_type_file = array('application/pdf');
        if(empty($_FILES['file']['tmp_name'][$i])){
            $this->form_validation->set_message("file_check","pictures or pdf are required");
            return false;
        }
        // if(in_array($_FILES['file']['type'][$i],$allowed_mime_type_arr)){
        // if(!in_array($_FILES['file']['type'][$i],$allowed_mime_type_arr)){
        //     $this->form_validation->set_message("file_check","Please select only jpg/png files");
        //     return false;
        // }
            // if(empty(get_image_location($_FILES['file']['tmp_name'][$i]))){
                // $this->form_validation->set_message("file_check","Please select geo tagged images and pdf");
                // return false;
            // }
        // }
        if(!in_array($_FILES['file']['type'][$i],$allowed_mime_type_arr) and !in_array($_FILES['file']['type'][$i],$allowed_mime_type_file)){
                    $this->form_validation->set_message("file_check","Please select pdf or images only");
                return false;
                }
      }
    }
    public function check_geotagg_image(){
        $count=0;
        $faultyimage="";
        $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/PNG','image/JPEG','image/JPG','image/x-png');
        for($i=0;$i<count($_FILES['file']['tmp_name']);$i++){
            if(in_array($_FILES['file']['type'][$i],$allowed_mime_type_arr) and empty(get_image_location($_FILES['file']['tmp_name'][$i]))){
               $count++;
               $faultyimage=$faultyimage.$i.",";
            }
        }
        if($count>0){
            $response = [
                'success' => false,
                'message' => "Select Geotagged Images Only.",
                'faulty_image'=> $faultyimage,
            ];
         echo json_encode($response);
        }
        else{
        $response = [
            'success' => true,
        ];
        echo json_encode($response);
        }

    }
    public function open_ticket()
    {   
        // 
        // $ch  =  curl_init();
        // $timeout  =  30; 
        // 
        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        if ($this->input->post()) {
            $this->session->set_flashdata('region',$this->input->post('region'));
            $this->session->set_flashdata('categories',$this->input->post('categories'));
            $this->session->set_flashdata('subregion',$this->input->post('subregion'));

            $this->form_validation->set_rules('area', 'State', 'required');
            $this->form_validation->set_rules('region', 'City', 'required');
            $this->form_validation->set_rules('subregion', 'Municipal Zone', 'required');
            if(is_callcenter($this->session->userdata('client_user_id'))){
            $this->form_validation->set_rules('rname', 'Name', 'alpha_numeric_spaces|max_length[50]|required');
            $this->form_validation->set_rules('remail', 'Email', 'trim|required|max_length[50]|valid_email');
            $this->form_validation->set_rules('rphonenumber', 'Phone number', 'required|max_length[12]|min_length[8]');
            }
            $this->form_validation->set_rules('categories', 'Action Item', 'required');
            // $this->form_validation->set_rules('message', 'Description', 'max_length[255]|alpha_numeric_spaces');
            $this->form_validation->set_rules('landmark', 'Landmark', 'required|max_length[255]');
            // $this->form_validation->set_rules('file', 'file', 'callback_file_check');
            // $custom_fields = get_custom_fields('tickets', [
            //     'show_on_client_portal' => 1,
            //     'required'              => 1,
            // ]);
            // foreach ($custom_fields as $field) {
            //     $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            //     if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
            //         $field_name .= '[]';
            //     }
            //     $this->form_validation->set_rules($field_name, $field['name'], 'required');
            // }
            if ($this->form_validation->run() !== false) {
                // for($i=0;$i<count($_FILES['file']['name']);$i++){
                    // $imageloc=get_image_location($_FILES['file']['tmp_name'][$i]);
                    // if(empty($imageloc)){
                    //     set_alert('warning', _l('Select Geotagged Images'));
                    //     redirect(site_url('clients/open_ticket'));
                    // }

                // }
                $this->load->model('projects_model');
                $this->load->model('tasks_model');
                $data = $this->input->post();
                // $at=findAt($data['area'],$data['region'],$data['subregion'],$data['categories']);
                // if(empty($at)){
                //     set_alert('danger', _l('No Action Taker'));
                //     redirect(site_url('clients/open_ticket'));

                // }
                $milestones=get_milestone_for_ticket($data['categories']);
                $miledurations=array();
                $count=0;
                foreach($milestones as $milestone){
                    $miledurations[$count]=$milestone['days'];
                    $count++;
                }
                if(count($miledurations)>1){
                    if($miledurations[1]==1){
                        $actiondate=date('Y-m-d', strtotime(date("Y-m-d"). ' + '.$miledurations[1].'days'));
                    }else{
                        $actiondate=date('Y-m-d', strtotime(date("Y-m-d"). ' + 2 days'));   
                    }
                }else{
                    if($miledurations[0]==1){
                        $actiondate=date('Y-m-d', strtotime(date("Y-m-d"). ' + '.$miledurations[0].'days'));
                    }else{
                        $actiondate=date('Y-m-d', strtotime(date("Y-m-d"). ' + 2 days'));   
                    }
                }
                $ticketdata=[
                    'name'=>getcatname($data['categories']),
                    'clientid'=>$this->session->userdata('client_user_id'),
                    'project_members'=>findAt($data['area'],$data['region'],$data['subregion'],$data['categories']),
                    'landmark'=>$data['landmark'],
                    'start_date'=>date("Y-m-d"),
                    'deadline'=>date('Y-m-d', strtotime(date("Y-m-d"). ' + '.$miledurations[0].'days')),
                    'description'=>$data['message'],
                    'status'=>(!empty(findAt($data['area'],$data['region'],$data['subregion'],$data['categories']))?1:9),
                    'area_id'=>$data['area'],
                    'region_id'=>$data['region'],
                    'subregion_id'=>$data['subregion'],
                    'issue_id'=>$data['categories'],
                    'updated_at'=>date("Y-m-d"),
                    'remail'=> !empty($data['remail'])?$data['remail']:"",
                    'rphonenumber'=> !empty($data['rphonenumber'])?$data['rphonenumber']:"",
                    'rname'=> !empty($data['rname'])?$data['rname']:"",
                    'action_date'=>$actiondate,
                    'is_assigned'=>(!empty(findAt($data['area'],$data['region'],$data['subregion'],$data['categories']))?1:0),
                ];

                $id=$this->projects_model->logTicket($ticketdata);
                add_new_details_to_surveyor($this->session->userdata('client_user_id'),$data['area'],$data['region'],$data['subregion']);
                // print_r($id);
                if($id){
                    
                    $countforduration=0;
                    $countforcheck=0;
                    foreach($milestones as $milestone){
                        if($countforcheck>1){
                            $date=date_add(date_create(date('y-m-d')),date_interval_create_from_date_string(''.(convertint($countforduration)+1).' days'));
                        }else{
                            $date=date_add(date_create(date('y-m-d')),date_interval_create_from_date_string(''.$countforduration.' days'));
                        }
                        $duedate=date_add(date_create(date('y-m-d')),date_interval_create_from_date_string(''.$countforduration.' days'));
                        $reminder1date=date_add(date_create(date('y-m-d')),date_interval_create_from_date_string(''.$countforduration.' days'));
                        $reminder2date=date_add(date_create(date('y-m-d')),date_interval_create_from_date_string(''.$countforduration.' days'));
                        $task=[
                            'name'=>$milestone['milestone_name'],
                            'startdate'=>date_format($date,"Y-m-d"),
                            'duedate'=>date_format(date_add($duedate,date_interval_create_from_date_string(''.$milestone['days'].' days')),"Y-m-d"),
                            'reminderone_date'=>date_format(date_add($reminder1date,date_interval_create_from_date_string(''.$milestone['reminder_one'].' days')),"Y-m-d"),
                            'remindertwo_date'=>date_format(date_add($reminder2date,date_interval_create_from_date_string(''.$milestone['reminder_two'].' days')),"Y-m-d"),
                            'addedfrom'=>$this->session->userdata('client_user_id'),
                            'reminderone_days'=>$milestone['reminder_one'],
                            'remindertwo_days'=>$milestone['reminder_two'],
                            'task_days'=>$milestone['days'],
                            'is_closed'=>0,
                            'rel_id'=>$id,
                            'rel_type'=>'project',
                        ];
                        if($countforcheck>0){
                        $countforduration=$countforduration+convertint($milestone['days']);
                        }
                        else{
                            $date=date_create(date('y-m-d'));
                        }
                        $countforcheck++;
                        $this->tasks_model->addMilestonesForTickets($task);
                    }
                   $assignedat=findAt($data['area'],$data['region'],$data['subregion'],$data['categories']);
                //    if(empty($assignedat)){
                //        $this->projects_model->unassigned_at_entry($id,$data['area'],$data['region'],$data['subregion'],$data['categories'],$this->session->userdata('client_user_id'));
                //    }
                if(array_key_exists("latitude",$data) and array_key_exists("longitude",$data))
                {
                    $locationdata=[
                    'latitude'=>$data['latitude'],
                    'longitude'=>$data['longitude']
                    ];
                }
                else{
                    $locationdata=[];
                }
                    
                   (!empty($locationdata))?handle_project_file_uploads($id,$locationdata):handle_project_file_uploads($id);
                   $email=(!empty($assignedat))?get_staff_email($assignedat,""):get_staff_email("",$data['area']);
                // phone number   
                    // $phone_number_at=(!empty($assignedat)?get_staff_phone($assignedat):'');
                    $phone_number_sa=(empty($assignedat)?get_phone_number_by_email($email):'');
                // 
                   if(empty($email)){
                    $email=get_superadmin('email');
                   }
                //    phone
                //    if(!empty($phone_number_at)){
                //         $this->load->model('staff_model');
                //         $ata_id=$this->staff_model->get_ata($assignedat);
                //         $phone_number_ata=(!empty($ata_id)?get_staff_phone($ata_id->assistant_id):'');
                //         $message=$this->load->view('sms_templates/new_project',['id'=>$id,'duedate'=>date_format($duedate,"Y/m/d")],true);
                //         $url='https://www.hawkfy.in/api/mt/SendSMS?user=MCGGGN&password=MCGGGN&senderid=MCGGGN&channel=Trans&DCS=8&flashsms=1&number='.$phone_number_at.','.$phone_number_ata.'&text='.urlencode($message).'&route=05';
                //         curl_setopt ($ch,CURLOPT_URL, $url);
                //         curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
                //         curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
                //         $response = curl_exec($ch) ;
                //         curl_close($ch) ; 
                //     }
                    // if(!empty($phone_number_sa)){
                    //     $message=$this->load->view('sms_templates/unassigned_project',[],true);
                    //     $url=SMS_API_URL.'&number='.$phone_number_sa.'&text='.urlencode($message).'&route=05';
                    //     curl_setopt ($ch,CURLOPT_URL, $url);
                    //     curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
                    //     curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    //     curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
                    //     $response = curl_exec($ch) ;
                    //     curl_close($ch) ; 
                    // }
                // phone
                   $area=getitemname($data['area'],'area');
                   $region=getitemname($data['region'],'region');
                   $subregion=getitemname($data['subregion'],'subregion');
                   $category =getcatname($data['categories']);
                   if(!empty($assignedat)){
                    $send=send_mail_template('New_ticket',$email,$assignedat,1,
                    $id,$area,$region,$subregion,$category,$data['landmark'],$ticketdata['deadline']);
                   }
                   else{
                    $areaadminid=get_area_admin_by_area($data['area']);
                    if(!empty($areaadminid)){
                    $send=send_mail_template('Unassigned_Ticket',$email,$areaadminid,1,
                    $id,$area,$region,$subregion,$category,$data['landmark'],$ticketdata['deadline']);
                    }else{
                        $send=send_mail_template('Unassigned_Ticket',$email,get_superadmin('id'),1,
                        $id,$area,$region,$subregion,$category,$data['landmark'],$ticketdata['deadline']);
                    }
                   }
                    set_alert('success', _l('new_ticket_added_successfully'));
                    redirect(site_url('clients/open_ticket'));

                }
                // die;


                // $id = $this->tickets_model->add([
                //     'subject'    => $data['subject'],
                //     'department' => $data['department'],
                //     'priority'   => $data['priority'],
                //     'service'    => isset($data['service']) && is_numeric($data['service'])
                //     ? $data['service']
                //     : null,
                //     'project_id' => isset($data['project_id']) && is_numeric($data['project_id'])
                //     ? $data['project_id']
                //     : 0,
                //     'custom_fields' => isset($data['custom_fields']) && is_array($data['custom_fields'])
                //     ? $data['custom_fields']
                //     : [],
                //     'message'   => $data['message'],
                //     'contactid' => get_contact_user_id(),
                //     'userid'    => get_client_user_id(),
                // ]);

                // if ($id) {
                //     set_alert('success', _l('new_ticket_added_successfully', $id));
                //     redirect(site_url('clients/ticket/' . $id));
                // }

            }
        }
        $data             = [];
        // $data['projects'] = $this->projects_model->get_projects_for_ticket(get_client_user_id());
        $data['title']    = _l('new_ticket');
        $this->data($data);
        $this->view('open_ticket');
        $this->layout();
    }

    public function ticket($id)
    {
        if (!has_contact_permission('support')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        if (!$id) {
            redirect(site_url());
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id, get_client_user_id());
        if (!$data['ticket'] || $data['ticket']->userid != get_client_user_id()) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('message', _l('ticket_reply'), 'required');

            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                $replyid = $this->tickets_model->add_reply([
                    'message'   => $data['message'],
                    'contactid' => get_contact_user_id(),
                    'userid'    => get_client_user_id(),
                ], $id);
                if ($replyid) {
                    set_alert('success', _l('replied_to_ticket_successfully', $id));
                }
                redirect(site_url('clients/ticket/' . $id));
            }
        }

        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($id);
        $data['title']          = $data['ticket']->subject;
        $this->data($data);
        $this->view('single_ticket');
        $this->layout();
    }

    public function contracts()
    {
        if (!has_contact_permission('contracts')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $data['contracts'] = $this->contracts_model->get('', [
            'client'                => get_client_user_id(),
            'not_visible_to_client' => 0,
            'trash'                 => 0,
        ]);

        $data['contracts_by_type_chart'] = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['title']                   = _l('clients_contracts');
        $this->data($data);
        $this->view('contracts');
        $this->layout();
    }

    public function invoices($status = false)
    {
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $where = [
            'clientid' => get_client_user_id(),
        ];

        if (is_numeric($status)) {
            $where['status'] = $status;
        }

        if (isset($where['status'])) {
            if ($where['status'] == Invoices_model::STATUS_DRAFT
                && get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                unset($where['status']);
                $where['status !='] = Invoices_model::STATUS_DRAFT;
            }
        } else {
            if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                $where['status !='] = Invoices_model::STATUS_DRAFT;
            }
        }

        $data['invoices'] = $this->invoices_model->get('', $where);
        $data['title']    = _l('clients_my_invoices');
        $this->data($data);
        $this->view('invoices');
        $this->layout();
    }

    public function statement()
    {
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $data = [];
        // Default to this month
        $from = _d(date('Y-m-01'));
        $to   = _d(date('Y-m-t'));

        if ($this->input->get('from') && $this->input->get('to')) {
            $from = $this->input->get('from');
            $to   = $this->input->get('to');
        }

        $data['statement'] = $this->clients_model->get_statement(get_client_user_id(), to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to']   = $to;

        $data['period_today'] = json_encode(
                     [
                     _d(date('Y-m-d')),
                     _d(date('Y-m-d')),
                     ]
        );
        $data['period_this_week'] = json_encode(
                     [
                     _d(date('Y-m-d', strtotime('monday this week'))),
                     _d(date('Y-m-d', strtotime('sunday this week'))),
                     ]
        );
        $data['period_this_month'] = json_encode(
                     [
                     _d(date('Y-m-01')),
                     _d(date('Y-m-t')),
                     ]
        );

        $data['period_last_month'] = json_encode(
                     [
                     _d(date('Y-m-01', strtotime('-1 MONTH'))),
                     _d(date('Y-m-t', strtotime('-1 MONTH'))),
                     ]
        );

        $data['period_this_year'] = json_encode(
                     [
                     _d(date('Y-m-d', strtotime(date('Y-01-01')))),
                     _d(date('Y-m-d', strtotime(date('Y-12-31')))),
                     ]
        );
        $data['period_last_year'] = json_encode(
                     [
                     _d(date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')))),
                     _d(date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')))),
                     ]
        );

        $data['period_selected'] = json_encode([$from, $to]);

        $data['custom_period'] = ($this->input->get('custom_period') ? true : false);

        $data['title'] = _l('customer_statement');
        $this->data($data);
        $this->view('statement');
        $this->layout();
    }

    public function statement_pdf()
    {
        if (!has_contact_permission('invoices')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['statement'] = $this->clients_model->get_statement(
            get_client_user_id(),
            to_sql_date($from),
            to_sql_date($to)
        );

        try {
            $pdf = statement_pdf($data['statement']);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf_name = slug_it(_l('customer_statement') . '_' . get_option('companyname'));
        $pdf->Output($pdf_name . '.pdf', $type);
    }

    public function estimates($status = '')
    {
        if (!has_contact_permission('estimates')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $where = [
            'clientid' => get_client_user_id(),
        ];
        if (is_numeric($status)) {
            $where['status'] = $status;
        }
        if (isset($where['status'])) {
            if ($where['status'] == 1 && get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                unset($where['status']);
                $where['status !='] = 1;
            }
        } else {
            if (get_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                $where['status !='] = 1;
            }
        }
        $data['estimates'] = $this->estimates_model->get('', $where);
        $data['title']     = _l('clients_my_estimates');
        $this->data($data);
        $this->view('estimates');
        $this->layout();
    }

    public function company()
    {
        if ($this->input->post() && is_primary_contact()) {
            if (get_option('company_is_required') == 1) {
                $this->form_validation->set_rules('company', _l('clients_company'), 'required');
            }

            if (active_clients_theme() == 'perfex') {
                // Fix for custom fields checkboxes validation
                $this->form_validation->set_rules('company_form', '', 'required');
            }

            $custom_fields = get_custom_fields('customers', [
                'show_on_client_portal'  => 1,
                'required'               => 1,
                'disalow_client_to_edit' => 0,
            ]);

            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }

            if ($this->form_validation->run() !== false) {
                $data['company'] = $this->input->post('company');

                if (!is_null($this->input->post('vat'))) {
                    $data['vat'] = $this->input->post('vat');
                }

                if (!is_null($this->input->post('default_language'))) {
                    $data['default_language'] = $this->input->post('default_language');
                }

                if (!is_null($this->input->post('custom_fields'))) {
                    $data['custom_fields'] = $this->input->post('custom_fields');
                }

                $data['phonenumber'] = $this->input->post('phonenumber');
                $data['website']     = $this->input->post('website');
                $data['country']     = $this->input->post('country');
                $data['city']        = $this->input->post('city');
                $data['address']     = $this->input->post('address');
                $data['zip']         = $this->input->post('zip');
                $data['state']       = $this->input->post('state');

                if (get_option('allow_primary_contact_to_view_edit_billing_and_shipping') == 1
                    && is_primary_contact()) {

                    // Dynamically get the billing and shipping values from $_POST
                    for ($i = 0; $i < 2; $i++) {
                        $prefix = ($i == 0 ? 'billing_' : 'shipping_');
                        foreach (['street', 'city', 'state', 'zip', 'country'] as $field) {
                            $data[$prefix . $field] = $this->input->post($prefix . $field);
                        }
                    }
                }

                $success = $this->clients_model->update_company_details($data, get_client_user_id());
                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('clients/company'));
            }
        }
        $data['title'] = _l('client_company_info');
        $this->data($data);
        $this->view('company_profile');
        $this->layout();
    }

    public function profile()
    {
        if ($this->input->post('profile')) {
            $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
            $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');

            $this->form_validation->set_message('contact_email_profile_unique', _l('form_validation_is_unique'));
            $this->form_validation->set_rules('email', _l('clients_email'), 'required|valid_email|callback_contact_email_profile_unique');

            $custom_fields = get_custom_fields('contacts', [
                'show_on_client_portal'  => 1,
                'required'               => 1,
                'disalow_client_to_edit' => 0,
            ]);
            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }
            if ($this->form_validation->run() !== false) {
                handle_contact_profile_image_upload();

                $data = $this->input->post();

                $contact = $this->clients_model->get_contact(get_contact_user_id());

                if (has_contact_permission('invoices')) {
                    $data['invoice_emails']     = isset($data['invoice_emails']) ? 1 : 0;
                    $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
                } else {
                    $data['invoice_emails']     = $contact->invoice_emails;
                    $data['credit_note_emails'] = $contact->credit_note_emails;
                }

                if (has_contact_permission('estimates')) {
                    $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
                } else {
                    $data['estimate_emails'] = $contact->estimate_emails;
                }

                if (has_contact_permission('support')) {
                    $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;
                } else {
                    $data['ticket_emails'] = $contact->ticket_emails;
                }

                if (has_contact_permission('contracts')) {
                    $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
                } else {
                    $data['contract_emails'] = $contact->contract_emails;
                }

                if (has_contact_permission('projects')) {
                    $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
                    $data['task_emails']    = isset($data['task_emails']) ? 1 : 0;
                } else {
                    $data['project_emails'] = $contact->project_emails;
                    $data['task_emails']    = $contact->task_emails;
                }

                $success = $this->clients_model->update_contact([
                    'firstname'          => $this->input->post('firstname'),
                    'lastname'           => $this->input->post('lastname'),
                    'title'              => $this->input->post('title'),
                    'email'              => $this->input->post('email'),
                    'phonenumber'        => $this->input->post('phonenumber'),
                    'direction'          => $this->input->post('direction'),
                    'invoice_emails'     => $data['invoice_emails'],
                    'credit_note_emails' => $data['credit_note_emails'],
                    'estimate_emails'    => $data['estimate_emails'],
                    'ticket_emails'      => $data['ticket_emails'],
                    'contract_emails'    => $data['contract_emails'],
                    'project_emails'     => $data['project_emails'],
                    'task_emails'        => $data['task_emails'],
                    'custom_fields'      => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
                ], get_contact_user_id(), true);

                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('clients/profile'));
            }
        } elseif ($this->input->post('change_password')) {
            $this->form_validation->set_rules('oldpassword', _l('clients_edit_profile_old_password'), 'required');
            $this->form_validation->set_rules('newpassword', _l('clients_edit_profile_new_password'), 'required');
            $this->form_validation->set_rules('newpasswordr', _l('clients_edit_profile_new_password_repeat'), 'required|matches[newpassword]');
            if ($this->form_validation->run() !== false) {
                $success = $this->clients_model->change_contact_password(
                    get_contact_user_id(),
                    $this->input->post('oldpassword', false),
                    $this->input->post('newpasswordr', false)
                );

                if (is_array($success) && isset($success['old_password_not_match'])) {
                    set_alert('danger', _l('client_old_password_incorrect'));
                } elseif ($success == true) {
                    set_alert('success', _l('client_password_changed'));
                }

                redirect(site_url('clients/profile'));
            }
        }
        $data['title'] = _l('clients_profile_heading');
        $this->data($data);
        $this->view('profile');
        $this->layout();
    }

    public function remove_profile_image()
    {
        $id = get_contact_user_id();

        hooks()->do_action('before_remove_contact_profile_image', $id);

        if (file_exists(get_upload_path_by_type('contact_profile_images') . $id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'profile_image' => null,
        ]);

        if ($this->db->affected_rows() > 0) {
            redirect(site_url('clients/profile'));
        }
    }

    public function dismiss_announcement($id)
    {
        $this->misc_model->dismiss_announcement($id, false);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_credit_card()
    {
        if (!can_logged_in_contact_update_credit_card()) {
            redirect(site_url());
        }

        $this->load->library('stripe_subscriptions');
        $this->load->library('stripe_core');
        $this->load->model('subscriptions_model');

        $sessionData = [
              'payment_method_types' => ['card'],
              'mode'                 => 'setup',
              'setup_intent_data'    => [
                'metadata' => [
                  'customer_id' => $this->clients_model->get(get_client_user_id())->stripe_id,
                ],
              ],
              'success_url' => site_url('clients/success_update_card?session_id={CHECKOUT_SESSION_ID}'),
              'cancel_url'  => $cancelUrl = site_url('clients/credit_card'),
            ];

        $contact = $this->clients_model->get_contact(get_contact_user_id());

        if ($contact->email) {
            $sessionData['customer_email'] = $contact->email;
        }

        try {
            $session = $this->stripe_core->create_session($sessionData);
            redirect_to_stripe_checkout($session->id);
        } catch (Exception $e) {
            set_alert('warning', $e->getMessage());
            redirect($cancelUrl);
        }
    }

    public function success_update_card()
    {
        if (!can_logged_in_contact_update_credit_card()) {
            redirect(site_url());
        }

        $this->load->library('stripe_core');

        try {
            $session = $this->stripe_core->retrieve_session([
                'id'     => $this->input->get('session_id'),
                'expand' => ['setup_intent.payment_method'],
            ]);

            $session->setup_intent->payment_method->attach(['customer' => $session->setup_intent->metadata->customer_id]);

            $this->stripe_core->update_customer($session->setup_intent->metadata->customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $session->setup_intent->payment_method->id,
                  ],
              ]);

            set_alert('success', _l('updated_successfully', _l('credit_card')));
        } catch (Exception $e) {
            set_alert('warning', $e->getMessage());
        }

        redirect(site_url('clients/credit_card'));
    }

    public function credit_card()
    {
        if (!can_logged_in_contact_update_credit_card()) {
            redirect(site_url());
        }

        $this->load->library('stripe_core');
        $client = $this->clients_model->get(get_client_user_id());

        $data['stripe_customer'] = $this->stripe_core->get_customer($client->stripe_id);
        $data['payment_method']  = null;

        if (!empty($data['stripe_customer']->invoice_settings->default_payment_method)) {
            $data['payment_method'] = $this->stripe_core->retrieve_payment_method($data['stripe_customer']->invoice_settings->default_payment_method);
        }

        $data['bodyclass'] = 'customer-credit-card';
        $data['title']     = _l('credit_card');

        $this->data($data);
        $this->view('credit_card');
        $this->layout();
    }

    public function delete_credit_card()
    {
        if (customer_can_delete_credit_card()) {
            $client = $this->clients_model->get(get_client_user_id());

            $this->load->library('stripe_core');

            $stripeCustomer = $this->stripe_core->get_customer($client->stripe_id);

            try {
                $payment_method = $this->stripe_core->retrieve_payment_method($stripeCustomer->invoice_settings->default_payment_method);
                $payment_method->detach();

                set_alert('success', _l('credit_card_successfully_deleted'));
            } catch (Exception $e) {
                set_alert('warning', $e->getMessage());
            }
        }

        redirect(site_url('clients/credit_card'));
    }

    public function subscriptions()
    {
        if (!can_logged_in_contact_view_subscriptions()) {
            redirect(site_url());
        }

        $this->load->model('subscriptions_model');
        $data['subscriptions'] = $this->subscriptions_model->get(['clientid' => get_client_user_id()]);

        $data['show_projects'] = total_rows(db_prefix() . 'subscriptions', 'project_id != 0 AND clientid=' . get_client_user_id()) > 0 && has_contact_permission('projects');

        $data['title']     = _l('subscriptions');
        $data['bodyclass'] = 'subscriptions';
        $this->data($data);
        $this->view('subscriptions');
        $this->layout();
    }

    public function cancel_subscription($id)
    {
        if (!is_primary_contact(get_contact_user_id())
            || get_option('show_subscriptions_in_customers_area') != '1') {
            redirect(site_url());
        }

        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');
        $subscription = $this->subscriptions_model->get_by_id($id, ['clientid' => get_client_user_id()]);

        if (!$subscription) {
            show_404();
        }

        try {
            $type    = $this->input->get('type');
            $ends_at = time();
            if ($type == 'immediately') {
                $this->stripe_subscriptions->cancel($subscription->stripe_subscription_id);
            } elseif ($type == 'at_period_end') {
                $ends_at = $this->stripe_subscriptions->cancel_at_end_of_billing_period($subscription->stripe_subscription_id);
            } else {
                throw new Exception('Invalid Cancelation Type', 1);
            }

            $update = ['ends_at' => $ends_at];
            if ($type == 'immediately') {
                $update['status'] = 'canceled';
            }
            $this->subscriptions_model->update($id, $update);

            set_alert('success', _l('subscription_canceled'));
        } catch (Exception $e) {
            set_alert('danger', $e->getMessage());
        }

        redirect(site_url('clients/subscriptions'));
    }

    public function resume_subscription($id)
    {
        if (!is_primary_contact(get_contact_user_id())
            || get_option('show_subscriptions_in_customers_area') != '1') {
            redirect(site_url());
        }

        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');
        $subscription = $this->subscriptions_model->get_by_id($id, ['clientid' => get_client_user_id()]);

        if (!$subscription) {
            show_404();
        }

        try {
            $this->stripe_subscriptions->resume($subscription->stripe_subscription_id, $subscription->stripe_plan_id);
            $this->subscriptions_model->update($id, ['ends_at' => null]);
            set_alert('success', _l('subscription_resumed'));
        } catch (Exception $e) {
            set_alert('danger', $e->getMessage());
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function gdpr()
    {
        $this->load->model('gdpr_model');

        if (is_gdpr()
            && $this->input->post('removal_request')
            && get_option('gdpr_contact_enable_right_to_be_forgotten') == '1') {
            $success = $this->gdpr_model->add_removal_request([
                'description'  => nl2br($this->input->post('removal_description')),
                'request_from' => get_contact_full_name(get_contact_user_id()),
                'contact_id'   => get_contact_user_id(),
                'clientid'     => get_client_user_id(),
            ]);
            if ($success) {
                send_gdpr_email_template('gdpr_removal_request_by_customer', get_contact_user_id());
                set_alert('success', _l('data_removal_request_sent'));
            }
            redirect(site_url('clients/gdpr'));
        }

        $data['title'] = _l('gdpr');
        $this->data($data);
        $this->view('gdpr');
        $this->layout();
    }

    public function change_language($lang = '')
    {
        if (!can_logged_in_contact_change_language()) {
            redirect(site_url());
        }

        hooks()->do_action('before_customer_change_language', $lang);

        $this->db->where('userid', get_client_user_id());
        $this->db->update(db_prefix() . 'clients', ['default_language' => $lang]);

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(site_url());
        }
    }

    public function export()
    {
        if (is_gdpr()
            && get_option('gdpr_data_portability_contacts') == '0'
            || !is_gdpr()) {
            show_error('This page is currently disabled, check back later.');
        }

        $this->load->library('gdpr/gdpr_contact');
        $this->gdpr_contact->export(get_contact_user_id());
    }

    /**
     * Client home chart
     * @return mixed
     */
    public function client_home_chart()
    {
        $statuses = [
                1,
                2,
                4,
                3,
            ];
        $months          = [];
        $months_original = [];
        for ($m = 1; $m <= 12; $m++) {
            array_push($months, _l(date('F', mktime(0, 0, 0, $m, 1))));
            array_push($months_original, date('F', mktime(0, 0, 0, $m, 1)));
        }
        $chart = [
                'labels'   => $months,
                'datasets' => [],
            ];
        foreach ($statuses as $status) {
            $this->db->select('total as amount, date');
            $this->db->from(db_prefix() . 'invoices');
            $this->db->where('clientid', get_client_user_id());
            $this->db->where('status', $status);
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $this->db->where('currency', $by_currency);
            }
            if ($this->input->post('year')) {
                $this->db->where('YEAR(' . db_prefix() . 'invoices.date)', $this->input->post('year'));
            }
            $payments      = $this->db->get()->result_array();
            $data          = [];
            $data['temp']  = $months_original;
            $data['total'] = [];
            $i             = 0;
            foreach ($months_original as $month) {
                $data['temp'][$i] = [];
                foreach ($payments as $payment) {
                    $_month = date('F', strtotime($payment['date']));
                    if ($_month == $month) {
                        $data['temp'][$i][] = $payment['amount'];
                    }
                }
                $data['total'][] = array_sum($data['temp'][$i]);
                $i++;
            }

            if ($status == 1) {
                $borderColor = '#fc142b';
            } elseif ($status == 2) {
                $borderColor = '#84c529';
            } elseif ($status == 4 || $status == 3) {
                $borderColor = '#ff6f00';
            }

            $backgroundColor = 'rgba(' . implode(',', hex2rgb($borderColor)) . ',0.3)';

            array_push($chart['datasets'], [
                    'label'           => format_invoice_status($status, '', false, true),
                    'backgroundColor' => $backgroundColor,
                    'borderColor'     => $borderColor,
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => $data['total'],
                ]);
        }
        echo json_encode($chart);
    }

    public function contact_email_profile_unique($email)
    {
        return total_rows(db_prefix() . 'contacts', 'id !=' . get_contact_user_id() . ' AND email="' . get_instance()->db->escape_str($email) . '"') > 0 ? false : true;
    }
    public function evidence_image()
    {   $this->load->model('dashboard_model');
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_GET['projectId']) ? $_GET['projectId'] : '';
            $imgType = !empty($_GET['imgType']) ? $_GET['imgType'] : '';
            $img_milestone = ($imgType == 'original') ? 1 : 2;
            $evidenceData = '';
            $taskId = '';
            $milestoneName = '';
            if (!empty($projectId)) {
                if ($img_milestone == 2) {
                    $this->load->model('report_model');
                    $milestone = $this->report_model->get_current_milestone($projectId);
                    $milestone = $milestone[0];
                    $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
                    $milestoneName = (!empty($milestone['task_name'])) ? $milestone['task_name'] : '';
                }
                $evidenceImages = $this->dashboard_model->get_evidence_image($projectId, $taskId, $img_milestone);
                $evidenceImageData = array();
                $evidenceData .= '<div class="modal-header"><h4 class="modal-title">
                                                    <span class="add-title">Evidence</span>
                                                </h4></div>
                                                <hr class="hr-panel-model">';
                foreach ($evidenceImages as $key => $image) {
                    $file_id = $image['id'];
                    $file_name = $image['file_name'];
                    $milestone = $image['milestone'];
                    $latitude = $image['latitude'];
                    $longitude = $image['longitude'];
                    $filetype = $image['filetype'];


                    if ($milestone == 0 && empty($key)) {
                        // $evidenceData .= '<p class="reported-detail mL20 semibold">These files were uploaded when Project was raised.</p>
                                            // <div class="">';
                    } elseif (empty($key)) {
                        $evidenceData .= '<p class="reported-detail mL20 semibold">This is evidence uploaded by resolver.</p>
                                        <p class="reported-detail mL20 semibold">' . $milestoneName . '</p>
                                            <div class="row original-images">';
                    }

                    if (!empty($milestone)) {
                        $imgPath = 'uploads/tasks/' . $image['milestone'] . '/';
                    } else {
                        $imgPath = 'uploads/projects/' . $projectId . '/';
                    }

                    $location = '';
                    if (!empty($latitude) && !empty($longitude)) {
                        $url = base_url('/admin/tickets/viewmap/' . $projectId . '?lat=' . $latitude . '&lang=' . $longitude . '&output=embed');
                        // $location = '<a href="' . $url . '" class="mT10 text-center d-block" target="_blank">View Location</a>';
                    } else {
                        $location = '';
                    }

                    if ($filetype == 'application/pdf') {
                        $evidenceImageData['document'][] = array(
                            'imgPath' => $imgPath,
                            'milestone' => $milestone,
                            'projectId' => $projectId,
                            'file_id' => $file_id,
                            'file_name' => $file_name,
                            'location' => $location,
                            'filetype' => $filetype
                        );
                    } else {
                        $evidenceImageData['image'][] = array(
                            'imgPath' => $imgPath,
                            'milestone' => $milestone,
                            'projectId' => $projectId,
                            'file_id' => $file_id,
                            'file_name' => $file_name,
                            'location' => $location,
                            'filetype' => $filetype
                        );
                    }
                }

                $evidenceData .= '<ul class="nav nav-tabs ticket-detail-tabs">
                        <li class="active print-none"><a href="#imagePopup" data-toggle="tab">Image(s)</a></li>
                        <li class="print-none"><a href="#documentPopup" data-toggle="tab">Document(s)</a></li>
                    </ul>
                    
                    <ul class="nav nav-tabs only-print">
                        <li class="" style="font-size: 14px; font-weight: 700; margin-top:15px;"><a href="javascript:void(0)">Image(s)</a></li>
                    </ul>
                    <div class="clearfix "></div>
                    
                    <div class="tab-content faq-cat-content">
                        <div class="panel-body pL0">
                            <div class="tab-content">
                                <!-- Images -->
                                <div class="tab-pane active in fade  pr-mT0" id="imagePopup">
                                    <div class="content pr-mT0 print-p-0">';
                    if (!empty($evidenceImageData['image'])){
                        $evidenceData .= '<ul class="row lightgallery">';
                
                        foreach ($evidenceImageData['image'] as $imageData) {
                            $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                            $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                            $evidenceData .= '<li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="' . base_url($imgPath . $file_name) . '" data-src="' . base_url($imgPath . $file_name) . '">
                                                    <a href="javascript:void(0);">
                                                        <figure>
                                                            <img class="" src="' . base_url($imgPath . $file_name) . '" data-src="' . base_url($imgPath . $file_name) . '">
                                                        </figure>
                                                    </a>
                                                </li>';
                        }
                        $evidenceData .= '</ul>
                                        <ul class="row">';
                        if (!empty($evidenceImageData['image']))
                        foreach ($evidenceImageData['image'] as $imageData) {
                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                            $evidenceData .= '<li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="' . base_url($imgPath . $file_name) . '" data-src="' . base_url($imgPath . $file_name) . '">'
                                . $loc . '
                                                    </li>';
                        }
                        $evidenceData .= '</ul>';
                    } else {
                        $evidenceData .= '<p class="text-warning pL20"><i class="fa fa-warning"></i>No evidence image available</p>';
                    }
            } else {
                $evidenceData .= '<p class="text-warning pL20"><i class="fa fa-warning"></i>No evidence available</p>';
            }
            $evidenceData .= '</div>
                                </div>
                                <!-- Document -->
                                <div class="tab-pane fade" id="documentPopup">
                                    <ul class="nav nav-tabs only-print">
                                        <li class="" style="font-size: 14px; width:100%; border-bottom: 1px solid #ddd; font-weight: 700; margin-top:25px; margin-bottom: 20px"><a href="javascript:void(0)">Document(s)</a></li>
                                    </ul>
                                    <ul class="row docgallery content">';
            if (!empty($evidenceImageData['document'])) {
                foreach ($evidenceImageData['document'] as $imageData) {
                    $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                    $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                    $loc = !empty($imageData['location']) ? $imageData['location'] : '';

                    $evidenceData .= '<li class="col-xs-6 col-sm-4 col-md-3" data-responsive="' . base_url($imgPath . $file_name) . '" data-src="' . base_url($imgPath . $file_name) . '">
                                                    <a href="' . base_url($imgPath . $file_name) . '" target="_blank">
                                                        <figure><img class="print-document-icon" src="' . base_url('/assets/images/pdf-icon.png') . '"></figure>
                                                    </a>
                                                </li>';
                }
            } else {
                $evidenceData .= '<p class="text-warning pL20"><i class="fa fa-warning"></i> No document available</p>';
            }
            $evidenceData .= '</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            echo $evidenceData;
            die();
        }
    }

}

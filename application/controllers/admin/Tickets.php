<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()) {
            redirect(admin_url());
        }
        $this->load->model('tickets_model');
    }

    public function index($status = '', $userid = '')
    {
        close_setup_menu();

        if (!is_numeric($status)) {
            $status = '';
        }

        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('filters_ticket_id')) {
                $tableParams = [
                    'status' => $status,
                    'userid' => $userid,
                ];
            } else {
                // request for othes tickets when single ticket is opened
                $tableParams = [
                    'userid'              => $this->input->post('filters_userid'),
                    'where_not_ticket_id' => $this->input->post('filters_ticket_id'),
                ];
                if ($tableParams['userid'] == 0) {
                    unset($tableParams['userid']);
                    $tableParams['by_email'] = $this->input->post('filters_email');
                }
            }

            $this->app->get_table_data('tickets', $tableParams);
        }

        $data['chosen_ticket_status']              = $status;
        $data['weekly_tickets_opening_statistics'] = json_encode($this->tickets_model->get_weekly_tickets_opening_statistics());
        $data['title']                             = _l('support_tickets');
        $this->load->model('departments_model');
        $data['statuses']             = $this->tickets_model->get_ticket_status();
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['departments']          = $this->departments_model->get();
        $data['priorities']           = $this->tickets_model->get_priority();
        $data['services']             = $this->tickets_model->get_service();
        $data['ticket_assignees']     = $this->tickets_model->get_tickets_assignes_disctinct();
        $data['bodyclass']            = 'tickets-page';
        add_admin_tickets_js_assets();
        $data['default_tickets_list_statuses'] = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);
        $this->load->view('admin/tickets/list', $data);
    }

    public function add($userid = false)
    {
        if ($this->input->post()) {
            $data            = $this->input->post();
            $data['message'] = html_purify($this->input->post('message', false));
            $id              = $this->tickets_model->add($data, get_staff_user_id());
            if ($id) {
                set_alert('success', _l('new_ticket_added_successfully', $id));
                redirect(admin_url('tickets/ticket/' . $id));
            }
        }
        if ($userid !== false) {
            $data['userid'] = $userid;
            $data['client'] = $this->clients_model->get($userid);
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities']         = $this->tickets_model->get_priority();
        $data['services']           = $this->tickets_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']     = $this->staff_model->get('', $whereStaff);
        $data['articles']  = $this->knowledge_base_model->get();
        $data['bodyclass'] = 'ticket';
        $data['title']     = _l('new_ticket');

        if ($this->input->get('project_id') && $this->input->get('project_id') > 0) {
            // request from project area to create new ticket
            $data['project_id'] = $this->input->get('project_id');
            $data['userid']     = get_client_id_by_project_id($data['project_id']);
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'userid' => $data['userid']]) == 1) {
                $contact = $this->clients_model->get_contacts($data['userid']);
                if (isset($contact[0])) {
                    $data['contact'] = $contact[0];
                }
            }
        } elseif ($this->input->get('contact_id') && $this->input->get('contact_id') > 0 && $this->input->get('userid')) {
            $contact_id = $this->input->get('contact_id');
            if (total_rows(db_prefix() . 'contacts', ['active' => 1, 'id' => $contact_id]) == 1) {
                $contact = $this->clients_model->get_contact($contact_id);
                if ($contact) {
                    $data['contact'] = (array) $contact;
                }
            }
        }
        add_admin_tickets_js_assets();
        $this->load->view('admin/tickets/add', $data);
    }

    public function delete($ticketid)
    {
        if (!$ticketid) {
            redirect(admin_url('tickets'));
        }

        $response = $this->tickets_model->delete($ticketid);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_lowercase')));
        }

        if (strpos($_SERVER['HTTP_REFERER'], 'tickets/ticket') !== false) {
            redirect(admin_url('tickets'));
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_attachment($id)
    {
        if (is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')) {
            if (get_option('staff_access_only_assigned_departments') == 1 && !is_admin()) {
                $attachment = $this->tickets_model->get_ticket_attachment($id);
                $ticket     = $this->tickets_model->get_ticket_by_id($attachment->ticketid);

                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($ticket->department, $staff_departments)) {
                    set_alert('danger', _l('ticket_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }

            $this->tickets_model->delete_ticket_attachment($id);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function ticket($id)
    {
        if (!$id) {
            redirect(admin_url('tickets/add'));
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id);

        if (!$data['ticket']) {
            blank_page(_l('ticket_not_found'));
        }

        if (get_option('staff_access_only_assigned_departments') == 1) {
            if (!is_admin()) {
                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($data['ticket']->department, $staff_departments)) {
                    set_alert('danger', _l('ticket_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }
        }

        if ($this->input->post()) {
            $returnToTicketList = false;
            $data               = $this->input->post();

            if (isset($data['ticket_add_response_and_back_to_list'])) {
                $returnToTicketList = true;
                unset($data['ticket_add_response_and_back_to_list']);
            }

            $data['message'] = html_purify($this->input->post('message', false));
            $replyid         = $this->tickets_model->add_reply($data, $id, get_staff_user_id());

            if ($replyid) {
                set_alert('success', _l('replied_to_ticket_successfully', $id));
            }
            if (!$returnToTicketList) {
                redirect(admin_url('tickets/ticket/' . $id));
            } else {
                set_ticket_open(0, $id);
                redirect(admin_url('tickets'));
            }
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');

        $data['statuses']                       = $this->tickets_model->get_ticket_status();
        $data['statuses']['callback_translate'] = 'ticket_status_translate';

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities']         = $this->tickets_model->get_priority();
        $data['services']           = $this->tickets_model->get_service();
        $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']                = $this->staff_model->get('', $whereStaff);
        $data['articles']             = $this->knowledge_base_model->get();
        $data['ticket_replies']       = $this->tickets_model->get_ticket_replies($id);
        $data['bodyclass']            = 'top-tabs ticket single-ticket';
        $data['title']                = $data['ticket']->subject;
        $data['ticket']->ticket_notes = $this->misc_model->get_notes($id, 'ticket');
        add_admin_tickets_js_assets();
        $this->load->view('admin/tickets/single', $data);
    }

    public function edit_message()
    {
        if ($this->input->post()) {
            $data         = $this->input->post();
            $data['data'] = html_purify($this->input->post('data', false));

            if ($data['type'] == 'reply') {
                $this->db->where('id', $data['id']);
                $this->db->update(db_prefix() . 'ticket_replies', [
                    'message' => $data['data'],
                ]);
            } elseif ($data['type'] == 'ticket') {
                $this->db->where('ticketid', $data['id']);
                $this->db->update(db_prefix() . 'tickets', [
                    'message' => $data['data'],
                ]);
            }
            if ($this->db->affected_rows() > 0) {
                set_alert('success', _l('ticket_message_updated_successfully'));
            }
            redirect(admin_url('tickets/ticket/' . $data['main_ticket']));
        }
    }

    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        if (!$reply_id) {
            redirect(admin_url('tickets'));
        }
        $response = $this->tickets_model->delete_ticket_reply($ticket_id, $reply_id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_reply')));
        }
        redirect(admin_url('tickets/ticket/' . $ticket_id));
    }

    public function change_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tickets_model->change_ticket_status($id, $status));
        }
    }

    public function update_single_ticket_settings()
    {
        if ($this->input->post()) {
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
            $success = $this->tickets_model->update_single_ticket_settings($this->input->post());
            if ($success) {
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_settings', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $ticket = $this->tickets_model->get_ticket_by_id($this->input->post('ticketid'));
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($ticket->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('ticket_settings_updated_successfully_and_reassigned', $ticket->department_name));
                        echo json_encode([
                            'success'               => $success,
                            'department_reassigned' => true,
                        ]);
                        die();
                    }
                }
                set_alert('success', _l('ticket_settings_updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
            ]);
            die();
        }
    }

    // Priorities
    /* Get all ticket priorities */
    public function priorities()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['title']      = _l('ticket_priorities');
        $this->load->view('admin/tickets/priorities/manage', $data);
    }

    /* Add new priority od update existing*/
    public function priority()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_priority($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_priority')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_priority($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_priority')));
                }
            }
            die;
        }
    }

    /* Delete ticket priority */
    public function delete_priority($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if (!$id) {
            redirect(admin_url('tickets/priorities'));
        }
        $response = $this->tickets_model->delete_priority($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('ticket_priority_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_priority')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_priority_lowercase')));
        }
        redirect(admin_url('tickets/priorities'));
    }

    /* List all ticket predefined replies */
    public function predefined_replies()
    {
        if (!is_admin()) {
            access_denied('Predefined Replies');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name',
            ];
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'tickets_predefined_replies';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'id',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('tickets/predefined_reply/' . $aRow['id']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options            = icon_btn('tickets/predefined_reply/' . $aRow['id'], 'pencil-square-o');
                $row[]              = $options .= icon_btn('tickets/delete_predefined_reply/' . $aRow['id'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('predefined_replies');
        $this->load->view('admin/tickets/predefined_replies/manage', $data);
    }

    public function get_predefined_reply_ajax($id)
    {
        echo json_encode($this->tickets_model->get_predefined_reply($id));
    }

    public function ticket_change_data()
    {
        if ($this->input->is_ajax_request()) {
            $contact_id = $this->input->post('contact_id');
            echo json_encode([
                'contact_data'          => $this->clients_model->get_contact($contact_id),
                'customer_has_projects' => customer_has_projects(get_user_id_by_contact_id($contact_id)),
            ]);
        }
    }

    /* Add new reply or edit existing */
    public function predefined_reply($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_tickets_predefined_replies') == '0') {
            access_denied('Predefined Reply');
        }
        if ($this->input->post()) {
            $data              = $this->input->post();
            $data['message']   = html_purify($this->input->post('message', false));
            $ticketAreaRequest = isset($data['ticket_area']);

            if (isset($data['ticket_area'])) {
                unset($data['ticket_area']);
            }

            if ($id == '') {
                $id = $this->tickets_model->add_predefined_reply($data);
                if (!$ticketAreaRequest) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('predefined_reply')));
                        redirect(admin_url('tickets/predefined_reply/' . $id));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id]);
                    die;
                }
            } else {
                $success = $this->tickets_model->update_predefined_reply($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('predefined_reply')));
                }
                redirect(admin_url('tickets/predefined_reply/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('predefined_reply_lowercase'));
        } else {
            $predefined_reply         = $this->tickets_model->get_predefined_reply($id);
            $data['predefined_reply'] = $predefined_reply;
            $title                    = _l('edit', _l('predefined_reply_lowercase')) . ' ' . $predefined_reply->name;
        }
        $data['title'] = $title;
        $this->load->view('admin/tickets/predefined_replies/reply', $data);
    }

    /* Delete ticket reply from database */
    public function delete_predefined_reply($id)
    {
        if (!is_admin()) {
            access_denied('Delete Predefined Reply');
        }
        if (!$id) {
            redirect(admin_url('tickets/predefined_replies'));
        }
        $response = $this->tickets_model->delete_predefined_reply($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('predefined_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('predefined_reply_lowercase')));
        }
        redirect(admin_url('tickets/predefined_replies'));
    }

    // Ticket statuses
    /* Get all ticket statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $data['title']    = 'Ticket statuses';
        $this->load->view('admin/tickets/tickets_statuses/manage', $data);
    }

    /* Add new or edit existing status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_ticket_status($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_status')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_ticket_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_status')));
                }
            }
            die;
        }
    }

    /* Delete ticket status from database */
    public function delete_ticket_status($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if (!$id) {
            redirect(admin_url('tickets/statuses'));
        }
        $response = $this->tickets_model->delete_ticket_status($id);
        if (is_array($response) && isset($response['default'])) {
            set_alert('warning', _l('cant_delete_default', _l('ticket_status_lowercase')));
        } elseif (is_array($response) && isset($response['referenced'])) {
            set_alert('danger', _l('is_referenced', _l('ticket_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_status_lowercase')));
        }
        redirect(admin_url('tickets/statuses'));
    }

    /* List all ticket services */
    public function services()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name',
            ];
            $sIndexColumn = 'serviceid';
            $sTable       = db_prefix() . 'services';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'serviceid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" onclick="edit_service(this,' . $aRow['serviceid'] . ');return false" data-name="' . $aRow['name'] . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', [
                    'data-name' => $aRow['name'],
                    'onclick'   => 'edit_service(this,' . $aRow['serviceid'] . '); return false;',
                ]);
                $row[]              = $options .= icon_btn('tickets/delete_service/' . $aRow['serviceid'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('services');
        $this->load->view('admin/tickets/services/manage', $data);
    }

    /* Add new service od delete existing one */
    public function service($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_tickets_predefined_replies') == '0') {
            access_denied('Ticket Services');
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();
            if (!$this->input->post('id')) {
                $requestFromTicketArea = isset($post_data['ticket_area']);
                if (isset($post_data['ticket_area'])) {
                    unset($post_data['ticket_area']);
                }
                $id = $this->tickets_model->add_service($post_data);
                if (!$requestFromTicketArea) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('service')));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id, 'name' => $post_data['name']]);
                }
            } else {
                $id = $post_data['id'];
                unset($post_data['id']);
                $success = $this->tickets_model->update_service($post_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('service')));
                }
            }
            die;
        }
    }

    /* Delete ticket service from database */
    public function delete_service($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if (!$id) {
            redirect(admin_url('tickets/services'));
        }
        $response = $this->tickets_model->delete_service($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }
        redirect(admin_url('tickets/services'));
    }

    public function block_sender()
    {
        if ($this->input->post()) {
            $this->load->model('spam_filters_model');
            $sender  = $this->input->post('sender');
            $success = $this->spam_filters_model->add(['type' => 'sender', 'value' => $sender], 'tickets');
            if ($success) {
                set_alert('success', _l('sender_blocked_successfully'));
            }
        }
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_tickets');
        if ($this->input->post()) {
            $total_deleted = 0;
            $ids           = $this->input->post('ids');
            $status        = $this->input->post('status');
            $department    = $this->input->post('department');
            $service       = $this->input->post('service');
            $priority      = $this->input->post('priority');
            $tags          = $this->input->post('tags');
            $is_admin      = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($is_admin) {
                            if ($this->tickets_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix() . 'tickets', [
                                'status' => $status,
                            ]);
                        }
                        if ($department) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix() . 'tickets', [
                                'department' => $department,
                            ]);
                        }
                        if ($priority) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix() . 'tickets', [
                                'priority' => $priority,
                            ]);
                        }

                        if ($service) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix() . 'tickets', [
                                'service' => $service,
                            ]);
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'ticket');
                        }
                    }
                }
            }

            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tickets_deleted', $total_deleted));
            }
        }
    }

    public function ticket_details()
    {
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_GET['projectId']) ? $_GET['projectId'] : '';
            $userRole = $GLOBALS['current_user']->role_slug_url;
            $userId = $GLOBALS['current_user']->staffid;
            
            $data = array();
            $this->load->model('projects_model');
            $data['ticketDetails'] = $this->projects_model->get_project_details($projectId);
            // $data['assignedUser'] = $assignedUser = !empty($data['ticketDetails']->assigned_user_id) ? $data['ticketDetails']->assigned_user_id : '';
            $data['assignedUser'] = $assignedUser = getProjectAssignedUser($projectId);
            if ($userRole == 'ata'){
                $task_status = !empty($data['ticketDetails']->task_status) ? $data['ticketDetails']->task_status : '';
            }else{
                $task_status = !empty($data['ticketDetails']->project_status) ? $data['ticketDetails']->project_status : '';
            }
            
            //Update New ticket to WIP ticket for ATA
            $taskStatus = 2;
            $this->load->model('tasks_model');
            if ($userRole == 'ata' && ($task_status == 1 || $task_status == 6)) {
                $statusData = array('status' => $taskStatus);
                $this->tasks_model->updateTaskStatus($projectId, $statusData);
            }

            if ($userRole == 'at' && $task_status == 6) {      
                $statusData = array('status' => $taskStatus);          
                $this->projects_model->updateProjectStatus($projectId,$statusData);
            }

            $data['tasks'] = $this->projects_model->get_task_details($projectId);
            $data['milestoneStatus'] = !empty($data['tasks'][0]) ? $data['tasks'][0]['status'] : 0;
            $data['taskDueDate'] = !empty($data['tasks'][0]) ? setDateFormat($data['tasks'][0]['duedate']) : "";
            // pre($data['tasks']);
            $wipMilestone = 1;
            $data['lastMilestone'] = false;
            if (count($data['tasks']) > 1) {
                array_shift($data['tasks']);

                foreach ($data['tasks'] as $key => $task) {
                    if ($task['status'] == 2) {
                        $wipMilestone = $key + 1;
                        $data['wip_task'] = $task;
                        break;
                    }
                }
            } else if (count($data['tasks']) == 1 && $data["tasks"][0]["status"] != 4) {
                $data["wip_task"] = $data["tasks"][0];
            }

            if (count($data['tasks']) == $wipMilestone) {
                $data['lastMilestone'] = true;
            }

            $this->load->model('staff_model');
            $data['assignedUserDetails'] = $this->staff_model->get_userDetails($assignedUser);

            $this->load->model('dashboard_model');
            $data['evidenceImages'] = $this->dashboard_model->get_evidence_image($projectId, '',1); //1=>original Image
            $data['evidenceLocation'] = $this->dashboard_model->get_evidence_location($projectId, '');

            //Resolved Milestone Images
            $this->load->model('report_model');
            // $milestone = $this->report_model->get_current_milestone($projectId,4);
            $milestone = $this->report_model->get_current_milestone($projectId);
            $milestone = !empty($milestone[0])?$milestone[0]:array();

            $projectStatus = $this->projects_model->getProjectStatus($projectId);
            if(in_array($projectStatus, [2, 4, 6]) ){
                $resolvedMilestone = $this->report_model->get_current_milestone($projectId,4);
                $milestone['task_id'] = !empty($resolvedMilestone[0]['task_id']) ? $resolvedMilestone[0]['task_id'] : (!empty($milestone['task_id'])?$milestone['task_id']:'');
                // $milestone['task_id'] = $resolvedMilestone[0]['task_id'];
            }

            $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';            
            // $data['latestImages'] = !empty($taskId)?$this->dashboard_model->get_evidence_image($projectId, $taskId, 2):'';//2=>Latest Image
            $data['latestImages'] = !empty($taskId)?$this->dashboard_model->get_evidence_image($projectId, $taskId):'';

            if ($userRole == 'at') {
                //Get Assistant Details
                $data['assistantDetails'] = $this->staff_model->get_staff_assistance($userId);

                //Get Rejection List
                $this->load->model('exception_model');
                $data['exceptionDetails'] = $this->exception_model->get_exception_list();
            }

            //Check Child Ticket Status
            // $hasChilds = !empty($data['ticketDetails']->sub_ticket_id)?json_decode($data['ticketDetails']->sub_ticket_id):'';
            $data['hasChild'] = !empty($data['ticketDetails']->sub_ticket_id) ? 1 : 0;

            if ($userRole == 'ar') {
                $area = $GLOBALS['current_user']->area;
                $data['atLists'] = $this->staff_model->get_action_taker(['area_id'=>$area]);

                $data['activeUser'] = true;
                //check if assigned AT is disable or not
                $assignedUser = $this->report_model->get_report_leader($projectId);
                if(empty($assignedUser->staff_status)){
                    $data['activeUser'] = false;
                }
                
                //Get list of all ATs reporting to AR
                $assignedAts = $this->dashboard_model->get_ar_assistant($userId);
                $data['assignedAts'] = !empty($assignedAts) ? explode(',',$assignedAts):array();
            }
            $this->load->view('admin/tickets/ticket_detail', $data);
        }
    }

    public function ticket_detail_pdf()
    {
        ob_start();
        $projectId = !empty($_GET['projectId']) ? $_GET['projectId'] : '';
        if(!empty($projectId)){
            $userRole = $GLOBALS['current_user']->role_slug_url;
            
            $data = array();
            $this->load->model('projects_model');
            $data['ticketDetails'] = $this->projects_model->get_project_details($projectId);
            $data['assignedUser'] = $assignedUser = getProjectAssignedUser($projectId);
            if ($userRole == 'ata'){
                $task_status = !empty($data['ticketDetails']->task_status) ? $data['ticketDetails']->task_status : '';
            }else{
                $task_status = !empty($data['ticketDetails']->project_status) ? $data['ticketDetails']->project_status : '';
            }
            
            $data['tasks'] = $this->projects_model->get_task_details($projectId);
            $data['milestoneStatus'] = !empty($data['tasks'][0]) ? $data['tasks'][0]['status'] : 0;
            $data['taskDueDate'] = !empty($data['tasks'][0]) ? setDateFormat($data['tasks'][0]['duedate']) : "";
            
            $wipMilestone = 1;
            $data['lastMilestone'] = false;
            if (count($data['tasks']) > 1) {
                array_shift($data['tasks']);
                foreach ($data['tasks'] as $key => $task) {
                    if ($task['status'] == 2) {
                        $wipMilestone = $key + 1;
                        $data['wip_task'] = $task;
                        break;
                    }
                }
            } else if (count($data['tasks']) == 1 && $data["tasks"][0]["status"] != 4) {
                $data["wip_task"] = $data["tasks"][0];
            }

            if (count($data['tasks']) == $wipMilestone) {
                $data['lastMilestone'] = true;
            }

            $this->load->model('staff_model');
            $data['assignedUserDetails'] = $this->staff_model->get_userDetails($assignedUser);

            $this->load->model('dashboard_model');
            $data['evidenceImages'] = $this->dashboard_model->get_evidence_image($projectId, '',1); //1=>original Image
            // $data['evidenceLocation'] = $this->dashboard_model->get_evidence_location($projectId, '');

            //Resolved Milestone Images
            $this->load->model('report_model');
            $milestone = $this->report_model->get_current_milestone($projectId,4);
            
            $milestone = !empty($milestone[0])?$milestone[0]:'';
            $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
            $data['latestImages'] = !empty($taskId)?$this->dashboard_model->get_evidence_image($projectId, $taskId, 2):'';//2=>Latest Image

            // if ($userRole == 'at') {
            //     $userId = $GLOBALS['current_user']->staffid;

            //     //Get Assistant Details
            //     $data['assistantDetails'] = $this->staff_model->get_staff_assistance($userId);

            //     //Get Rejection List
            //     $this->load->model('exception_model');
            //     $data['exceptionDetails'] = $this->exception_model->get_exception_list();
            // }

            //Check Child Ticket Status
            $data['hasChild'] = !empty($data['ticketDetails']->sub_ticket_id) ? 1 : 0;

            // if ($userRole == 'ar') {
            //     $area = $GLOBALS['current_user']->area;
            //     $data['atLists'] = $this->staff_model->get_action_taker(['area_id'=>$area]);
            // }

            $data['activities'] = $this->projects_model->get_activity($projectId);
            // $this->load->model("dashboard_model");
            // $this->load->model("staff_model");
            $this->load->model("clients_model");
            $this->load->model("tasks_model");
            if ($data["activities"]) {
                foreach ($data["activities"] as $key => $activity) {
                    $data['activities'][$key]["evidences"] = [];
                    $data['activities'][$key]["staff_data"] = [];
                    $data["activities"][$key]["task_name"] = "";
                    if ($activity["contact_id"] != 0) {
                        if($activity["description_key"] == 'ticket_created_cc'){
                            $staff = $this->projects_model->get_project_details($projectId);
                            if (!empty($staff)) {
                                $data['activities'][$key]["staff_data"] = [
                                    "name" => $staff->rname,
                                    "email" => $staff->remail,
                                    "phone" => $staff->rphonenumber
                                ];
                            }
                        }else{
                            $staff = $this->clients_model->get_contact($activity["contact_id"]);
                            if (!empty($staff)) {
                                $data['activities'][$key]["staff_data"] = [
                                    "name" => $staff->firstname . " " . $staff->lastname,
                                    "email" => $staff->email,
                                    "organisation" => getClientOrganisation($staff->userid),
                                    "phone" => $staff->phonenumber
                                ];
                            }
                        }
                    } else if (!empty($activity["additional_data"]) && isJson($activity["additional_data"])) {
                        $additional_data = json_decode($activity["additional_data"]);
                        $task_id = !empty($additional_data->taskId) ? $additional_data->taskId : '';
                        $data['activities'][$key]["additional_data"] = $additional_data;
                        if (!empty($task_id)) {
                            $find=[
                                'task_id'=>$task_id,
                                'date'=>$data['activities'][$key]['dateadded'],
                            ];
                            $data['activities'][$key]["evidences"] = $this->dashboard_model->get_evidence_image($activity["project_id"], $find);
                            $task_data = $this->tasks_model->get($task_id);
                            $data["activities"][$key]["task_name"] = !empty($task_data) ? $task_data->name : "";
                        }

                        // prepare ticket history data
                        if (isset($additional_data->assigned_to) && !empty($additional_data->assigned_to)) {
                            $staff = $this->staff_model->get_member(["staffid" => $additional_data->assigned_to]);
                            if (!empty($staff)) {
                                $data['activities'][$key]["staff_data"] = [
                                    "name" => $staff[0]["firstname"] . " " . $staff[0]["lastname"],
                                    "email" => $staff[0]["email"],
                                    "phone" => $staff[0]["phonenumber"],
                                    "organisation" => $staff[0]["organisation"]
                                ];
                            }
                        }
                    }
                }
            }
            
            $this->load->library('pdf');
            $html = $this->load->view('admin/tickets/ticket_detail_pdf', $data, true);
           //  echo $html; die;
            // $filename = "Project Detail $projectId";
            $filename = "$projectId";
            $this->pdf->createPDF($html, $filename);die();
            $this->pdf->Output($html, $filename,'D');
        }else{
            echo 'Something went wrong. Please try again.';
            die;
        }
    }

    public function assign_ticket()
    {
        if ($this->input->is_ajax_request()) {
            $userId = $GLOBALS['current_user']->staffid;
            $staffId = !empty($_POST['staffId']) ? $_POST['staffId'] : '';
            $staffName = !empty($_POST['staffName']) ? $_POST['staffName'] : '';
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';

            if (!empty($projectId)) {
                //Update Task Assigned to User
                $this->load->model('projects_model');
                $additional_data = array(
                    'assigned_by' => $userId,
                    'assigned_to' => $userId,
                    'taskId' => '',
                    'status' => 2,
                    'comment' => ''
                );

                $this->projects_model->log_activity($projectId, 'ticket_accepted_by_at', get_staff_full_name($userId), json_encode($additional_data));

                $assignedTicket = $this->projects_model->updateAssignedUser($projectId, $staffId);

                // Assign Ticket to Team Member
                $assignTicket = $this->tickets_model->updateTicketStatus($projectId, $userId, $staffId);

                if ($assignTicket) {
                    //Send Email Notification
                    $this->projects_model->sendAssignEmailNotification($projectId, $staffId,'New_ticket');
                    // sms part
                    // $this->sendSms($projectId,$staffId,'assigned');
                    // smspart

                    echo json_encode([
                        'success' => TRUE,
                        'message' => 'Project successfully assigned to ' . $staffName,
                    ]);
                    die;
                } else {
                    echo json_encode([
                        'fail' => FALSE,
                        'message' => 'Project not assigned',
                    ]);
                    die;
                }
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project not closed',
                ]);
                die;
            }
        }
    }

    public function reject_ticket()
    {
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $exceptionId = !empty($_POST['exceptionId']) ? $_POST['exceptionId'] : '';
            $exception  = !empty($_POST['exception']) ? $_POST['exception'] : '';

            //Update Ticket Status
            $this->load->model('projects_model');
            $staffId = get_staff_user_id();
            $staff_name = get_staff_full_name($staffId);

            $additional_data = array(
                'assigned_by' => $staffId,
                'assigned_to' => $staffId,
                'taskId' => '',
                'status' => 5,
                'comment' => $exception
            );
            $this->projects_model->log_activity($projectId, 'ticket_rejected_by_at', $staff_name, json_encode($additional_data));

            $statusData = array(
                'project_id' => $projectId,
                'status_id' => 5, //Rejected,
                'exception' => $exceptionId,
                'content' => $exception,
                'status' => 5,
            );
            $updateTicket = $this->projects_model->mark_as($statusData);

            //update Reject count
            $updateRejectCount = $this->projects_model->update_reject_count($projectId);

            //Update Ticket Notes
            $updateTicketNotes = $this->projects_model->save_ticket_note($statusData, $projectId);

            //Assign Reject Ticket to AR
            $assignedToAR = $this->tickets_model->updateRejectTicketAssignment($projectId);

            if ($updateTicket) {
                echo json_encode([
                    'success' => TRUE,
                    'message' => 'Project rejected successfully',
                ]);
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project not rejected',
                ]);
            }
        }
    }

    public function close_ticket()
    {
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $closeReason = !empty($_POST['closeReason']) ? $_POST['closeReason'] : '';

            if (!empty($projectId)) {
                //Update Ticket Status

                $updateTicket = $this->review_and_close($projectId,$closeReason);

                if ($updateTicket) {
                    echo json_encode([
                        'success' => TRUE,
                        'message' => 'Project closed successfully',
                    ]);
                } else {
                    echo json_encode([
                        'fail' => FALSE,
                        'message' => 'Project not closed',
                    ]);
                }
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project not closed',
                ]);
            }
        }
    }

    public function review_and_close($projectId,$comment=NULL)
    {
        $this->load->model('projects_model');
        $taskStatus = 3;  //Close,
        $statusData = array(
            'project_id' => $projectId,
            'status_id' => $taskStatus,
        );
        $updateTicket = $this->projects_model->mark_as($statusData);

        $this->load->model('tasks_model');
        $statusData = array('status' => $taskStatus);
        $this->tasks_model->updateTaskStatus($projectId, $statusData);

        if ($updateTicket) {

            $resolveData = array(
                'project_id' => $projectId,
                'status_id' => 4, //Closed,
                'exception' => '',
                'content' => $comment,
                'status' => 4,
            );
			
            //Update Ticket Notes
            $updateTicketNotes = $this->projects_model->save_ticket_note($resolveData, $projectId);

            //update Action Date
            $updateData = array('action_date'=>date('Y-m-d'));
            $this->projects_model->updateProjectStatus($projectId,$updateData);
    
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 3,
                'comment' => $comment
            );
            $label = 'ticket_closed_by_at';
            if($GLOBALS['current_user']->role_slug_url == 'ar'){
                $label = 'ticket_closed_by_ar';
            }
            $this->projects_model->log_activity($projectId, $label, get_staff_full_name(get_staff_user_id()), json_encode($additional_data));

            //Check in case of sub-ticket case if ticket has parent and closed parent ticket if closing last milestone of parent ticket
            $closeParentTicket = $this->projects_model->close_parent_ticket($projectId,$comment);

            return true;
        } else {
            return false;
        }
    }

    public function reopen_ticket()
    {
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $reopenReason  = !empty($_POST['reopenReason']) ? $_POST['reopenReason'] : '';
            $userRole = $GLOBALS['current_user']->role_slug_url;
            $pStatus = 6; //Reopen
            $tStatus = 6;

            $this->load->model('projects_model');

            $inactiveUser = false;
            if ($userRole == 'ar') {
                //reopen parent ticket in case of sub-tickets if child ticket reopen
                // $reopenParentTicket = $this->projects_model->reopen_parent_ticket($projectId);

                //check if assigned AT is disable or not
                $this->load->model('report_model');
                $assignedUser = $this->report_model->get_report_leader($projectId);
                if(empty($assignedUser->staff_status)){
                    $inactiveUser = true;
                    
                }
                
            }else if ($userRole == 'at') {
                //check if assigned ATA is disable or not
                $this->load->model('report_model');
                $assignedUser = $this->report_model->get_report_support($projectId);
                if(empty($assignedUser->staff_status)){
                    $inactiveUser = true;
                }
            }

            if($inactiveUser){
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project cannot be reopened as this user is no longer active. Please raise another project and assign it to a different Project Leader.',
                ]);
                die;
            }else{ 
                $logLabel = 'ticket_reopen_by_ar';
                if ($userRole == 'at') {
                    $pStatus = 2;
                    $logLabel = 'ticket_reopen_by_at';
                }else{
                    $pStatus = 6;
                }

                //Update Ticket Status
                $additional_data = array(
                    'assigned_by' => get_staff_user_id(),
                    'assigned_to' => get_staff_user_id(),
                    'taskId' => '',
                    'status' => 6,
                    'comment' => $reopenReason
                ); 
                $this->projects_model->log_activity($projectId, $logLabel, get_staff_full_name(get_staff_user_id()),json_encode($additional_data));

                $statusData = array(
                    'project_id' => $projectId,
                    'status_id' => $pStatus,
                    'content' => $reopenReason,
                    'role' => $userRole
                );

                // $updateTicket = $this->projects_model->mark_as($statusData);
                $updateTicketDetails = $this->projects_model->reopen_ticket_details($projectId, $pStatus, $tStatus);

                //Update Ticket Notes
                $updateTicketNotes = $this->projects_model->reopen_ticket_note($statusData);
                
                if ($updateTicketDetails) {
                    //Send Email Notification
                    $this->projects_model->sendReopenMailToAllMember($projectId,$reopenReason);
// sms integration
                    // $this->sendSms($projectId,$assignedUser,'reopened');
// 
                    echo json_encode([
                        'success' => TRUE,
                        'message' => 'Project reopened successfully',
                    ]);
                } else {
                    echo json_encode([
                        'fail' => FALSE,
                        'message' => 'Project cannot be reopened',
                    ]);
                }
            }
        }
    }

    public function update_milestone()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // pre(["data" => $data, "file"=> $_FILES]); die;
            $last_milestone = !empty($data["last_milestone"]) ? $data["last_milestone"] : '';
            $projectId = $data["project_id"];
            if (!$data["comment"]) {
                echo json_encode([
                    'success' => FALSE,
                    'message' => 'Please fill comment box.',
                ]);
                die;
            }
            if (!$_FILES) {
                echo json_encode([
                    'success' => FALSE,
                    'message' => 'Please select at least one image or pdf file to upload.',
                ]);
                die;
            }
            if ($_FILES && sizeof($_FILES) > 4) {
                echo json_encode([
                    'success' => FALSE,
                    'message' => 'File selection exceeds the limit of 4',
                ]);
                die;
            }
            $count = 0;
            $faultyimage = "";
            $allowed_mime_type_arr = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
            for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {
                if (in_array($_FILES['file']['type'][$i], $allowed_mime_type_arr) and empty(get_image_location($_FILES['file']['tmp_name'][$i]))) {
                    $count++;
                    $faultyimage = $faultyimage . $i . ",";
                }
            }
            if ($count > 0) {
                $response = [
                    'success' => false,
                    'message' => "Please upload geotagged images only.",
                    'faulty_image' => $faultyimage,
                ];
                echo json_encode($response);
                die;
            }
            $pStatus = 4;
            $form_data = [
                "comment" => $data["comment"],
                "status" => "4",
                "staff_id" => $data["staff_id"],
                "project_id" => $projectId,
                "task_id" => $data["task_id"],
                'pStatus' => $pStatus
            ];
            $task_update_status = $this->tickets_model->update_milestone($form_data);
            $file_uploaded = "";
            if ($task_update_status) {
                if(!empty($data['location_ios'])){
                    $coordinates=explode(',',$data['location_ios']);
                    $coordinatesvalue=[
                        'latitude'=>$coordinates[0],
                        'longitude'=>$coordinates[1]
                    ];
                }else{
                    $coordinatesvalue=[];
                }
                $file_uploaded = handle_task_attachments_array($data['task_id'], "file",$coordinatesvalue);
                if (isset($file_uploaded)) {
                    $file_data = [];
                    foreach ($file_uploaded as $file) {
                        array_push($file_data, [
                            "file_name" => $file['file_name'],
                            "filetype" => $file['filetype'],
                            "dateadded" => date('Y-m-d H:i:s'),
                            "project_id" => $projectId,
                            "staffid" => $data["staff_id"],
                            "latitude" => $file['latitude'],
                            "longitude" => $file['longitude'],
                            "milestone" => $data["task_id"],
                            "task_id" => $data["task_id"],
                            "subject" => $file['subject'],
                            "thumbnail_link"=>$file['thumbnail_link'],
                        ]);
                    }

                    if ($GLOBALS['current_user']->role_slug_url == "ata" && $last_milestone) {
                        // $pStatus = 3;
                        //Update Task Assigned
                        $this->load->model("projects_model");
                        $staff_id = $this->projects_model->get_project_at($projectId);
                        if (!empty($staff_id)) {

                            $statusData = array(
                                'status' => $pStatus,
                                'staffid' => $staff_id,
                                'assigned_from' => get_staff_user_id()
                            );
                            $this->load->model('tasks_model');
                            $this->tasks_model->updateTaskStatus($projectId, $statusData);

                            //Update Project members status
                            $this->projects_model->updateAssignedUser($projectId, $staff_id);
                        }
                    }

                    $is_file_added = $this->tickets_model->add_project_files($file_data);
                    if ($is_file_added) {
                        if ($GLOBALS['current_user']->role_slug_url == "at" && $last_milestone) {
                            //Update and close ticket
                            $updateTicket = $this->review_and_close($projectId);
                            echo json_encode([
                                'success' => TRUE,
                                'message' => 'Project has been resolved and closed successfully',
                            ]);
                        } else {
                            echo json_encode([
                                'success' => TRUE,
                                'message' => 'Project has been updated successfully',
                            ]);
                        }
                        die;
                    }
                }
            }
        }
    }

    public function viewmap($project_id = 0)
    {
        $map_file['delhi'] = 'Delhi_Ward.kmz';
        $kmzPath = 'http://apag.inroad.in/uploads/map_files';
       // $kmzPath = 'uploads/map_files';
        if ($this->input->get()) {
            $data            = $this->input->get();
            $this->load->model('projects_model');
            if(!empty($project_id) && $project_id!=0){
                $project_details = $this->projects_model->get_project_details($project_id);
                //$ticketDetails = $this->projects_model->get($project_id);
            }
            $data['kmz_file'] =  "";
            $data['project_id'] = $project_id;
            if(!empty($project_details)){
                $area_name = strtolower($project_details->area_name);
                if(isset($map_file[$area_name]))
                $data['kmz_file'] = $kmzPath.'/'.$area_name.'/'.$map_file[$area_name];
            }
            
            //$data['kmz_file'] = base_url($kmzPath.'/'.$ticketDetails->area_id.'/'.$map_file[$ticketDetails->area_id]);
            //$data['kmz_file'] = 'https://uat.norton-netprophets.com/Delhi_Ward.kmz';
            $data['ticketDetails'] =  $project_details;
            $data['project_details'] =  $project_details;
            $data['title']         = "Map | ".$project_details->name. ' | '.$project_details->landmark;
           // echo "<pre>";print_r($data); echo "</pre>";
           
            $this->load->view('admin/tickets/viewmap', $data);
        }
    }

    public function extend_deadline(){
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $assignAt = !empty($_POST['assignAt']) ? $_POST['assignAt'] : '';
            $milestoneCnt = !empty($_POST['milestoneCnt']) ? $_POST['milestoneCnt'] : '';
            $extendDeadlineData = !empty($_POST['extendDeadlineData']) ? $_POST['extendDeadlineData'] : '';
            
            $this->load->model('projects_model');
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 1,
                'comment' => ''
            ); 
            $this->projects_model->log_activity($projectId, 'ticket_reassigned_by_ar', get_staff_full_name(get_staff_user_id()),json_encode($additional_data));

            //Update Ticket Status
            $updateTicketDetails = $this->projects_model->reassigned_ticket($projectId, $assignAt, $extendDeadlineData,$milestoneCnt);

            //Update Task Assigned
            $statusData = array(
                'status' => 1,
                'staffid' => $assignAt,
                'assigned_from' => get_staff_user_id()
            );
            $this->load->model('tasks_model');
            $this->tasks_model->updateTaskStatus($projectId, $statusData);

            if ($updateTicketDetails) {
                echo json_encode([
                    'success' => TRUE,
                    // 'message' => 'Project has been re-assigned and deadline extended successfully',
                    'message' => 'Project has been edited successfully',
                ]);
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project not updated.',
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }

    public function subtickets(){
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $assignAt = !empty($_POST['assignAt']) ? $_POST['assignAt'] : '';
            $milestoneCnt = !empty($_POST['milestoneCnt']) ? $_POST['milestoneCnt'] : '';
            $extendDeadlineData = !empty($_POST['extendDeadlineData']) ? $_POST['extendDeadlineData'] : '';
            // pre($extendDeadlineData);
            /**
             * 1. Create log for create sub ticket (Done)
             * 2. Assign parent ticket to Assignee  (Done)
             *          - update table with hasChild-ticket ids and  (Done)
             *          - project status to wip and  (Done)
             *          - update task table with sub-ticket id and assignee at  (Done)
             *          - Update Task assigned  (Done)
             * 3. Create sub-tickets  - update table with parent-ticket id  (Done)
             * 4. Create tasks for each sub-tickets  (Done)
             *      -Update Task assigned  (Done)
             * 5. Update log  (Done)
             *          - Sub Ticket 
             *              - Sub-ticket created by AR  (Done)
             *              - Sub-ticket assigned to AT  (Done)
             *              - Parent Ticket ID  (Done)
             * 
             *          - Parent Ticket
             *              - Child Ticket created with IDs - show sub-ticket id (Done) 
             * 
             * 6. Update Ticket Detail - No action done on parent ticket  (Done)
             * 7. Sub-ticket can not be created of child ticket  (Done)
             * 8. Update parent ticket milestone status with sub-ticket status
             * 9. Close parent ticket when all sub-tickets gets closed (Done)
             *      - Check if ticket has parent and closed parent ticket if closing last milestone of parent ticket (Done)
             * 10. Update Task assigned  (Done)
             * 11. Copy project files to new projects (Done)
             * 12. Copy project_files entry with new tickets created (Done)
             * 13. Add flag for staff  (Done)
             * 14. Send mail notification  (Done)
             * 15. Sub-Ticket-Id  (Done)
             * 16. Sub-ticket can only be created if milestone more than 1 (Done)
             * 17. Reopen parent ticket if sub-ticket reopen ?  (Done)
             * 18. Reopen all sub-tickets if parent ticket reopen ? (we are not providing any action for parent ticket by any type of user)  (Done)
             */
            
            $this->load->model('projects_model');

            //Create log sub_ticket_created_by_ar
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 1,
                'comment' => ''
            ); 
            $this->projects_model->log_activity($projectId, 'sub_ticket_created_by_ar', get_staff_full_name(get_staff_user_id()),json_encode($additional_data));

            //Update AT assign to parent ticket
            // $checkAssigneeExists = $this->projects_model->checkProjectAssignee($projectId,$assignAt);
            // $reassigned = ($checkAssigneeExists)?1:0;
            $reassigned = 2;
            //Update Assigned user
            $this->projects_model->updateReAssignedUser($projectId, $assignAt,$reassigned);

            // Update Task Assigned
            $statusData = array(
                'status' => 2,
                'staffid' => $assignAt,
                'assigned_from' => get_staff_user_id()
            );
            $this->load->model('tasks_model');
            $this->tasks_model->updateTaskStatus($projectId, $statusData);

            //Update Ticket Status
            $subTicket = $this->projects_model->subTickets($projectId, $assignAt, $extendDeadlineData,$milestoneCnt);

            if ($subTicket) {
                echo json_encode([
                    'success' => TRUE,
                    'message' => 'Projects created successfully',
                ]);
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Projects not created.',
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }
    //sms api integration
    public function sendSms($project_id,$user,$type){
        $due_date=get_project_due_date($project_id);
        $ch  =  curl_init();
        $timeout  =  30; 
        if($type=='reopened'){
            $phonenumber=get_staff_phone($user->staff_id);
            $message=$this->load->view('sms_templates/reopened',['project_id'=>$project_id,'due_date'=>$due_date],true);
            $url=SMS_API_URL.'&number='.$phonenumber.'&text='.urlencode($message).'&route=05';
            curl_setopt ($ch,CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
            $response = curl_exec($ch) ;
            curl_close($ch) ;
        }
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
    // end
    public function reassignment(){
        if ($this->input->is_ajax_request()) {
            $projectId = !empty($_POST['projectId']) ? $_POST['projectId'] : '';
            $assignAt = !empty($_POST['assignAt']) ? $_POST['assignAt'] : '';
            
            $this->load->model('projects_model');
            $additional_data = array(
                'assigned_by' => get_staff_user_id(),
                'assigned_to' => get_staff_user_id(),
                'taskId' => '',
                'status' => 1,
                'comment' => ''
            ); 
            $this->projects_model->log_activity($projectId, 'ticket_reassigned_by_ar', get_staff_full_name(get_staff_user_id()),json_encode($additional_data));

            //Update Task Assigned
            $statusData = array(
                'status' => 1,
                'staffid' => $assignAt,
                'assigned_from' => get_staff_user_id()
            );
            $this->load->model('tasks_model');
            $this->tasks_model->updateTaskStatus($projectId, $statusData);

            $this->projects_model->updateAssignedUser($projectId, $assignAt,1);

            // $projectDetail = $this->projects_model->get_project_details($projectId);
            // $action_date = !empty($projectDetail->deadline)?$projectDetail->deadline:date('Y-m-d');
            $updateStatusData = array(
                'status' => 1,
                // 'action_date' => $action_date
            );
            $updateTicketDetails = $this->projects_model->updateProjectStatus($projectId,$updateStatusData);
            //Update Action Date
            $this->projects_model->update_parent_action_date($projectId);

            if ($updateTicketDetails) {
                echo json_encode([
                    'success' => TRUE,
                    'message' => 'Project re-assigned successfully',
                ]);
            } else {
                echo json_encode([
                    'fail' => FALSE,
                    'message' => 'Project not re-assigned.',
                ]);
            }
        } else {
            echo json_encode([
                'fail' => FALSE,
                'message' => 'Invalid request. Please try again.',
            ]);
        }
    }
}

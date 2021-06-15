<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $this->db->where('(start BETWEEN "' . date('Y-m-d', strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    /**
     * @param  integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where('(start BETWEEN "' . $monday_this_week . '" AND "' . $sunday_this_week . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results(db_prefix() . 'events');
    }

    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    public function get_weekly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = has_permission('payments', '', 'view');
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ')');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ');

        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }
        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = [
            'labels'   => get_weekdays(),
            'datasets' => [
                [
                    'label'           => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label'           => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor'     => '#c53da9',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x           = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = has_permission('projects', '', 'view');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'projects';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $result = get_leads_summary();

        foreach ($result as $status) {
            if (!isset($status['junk']) && !isset($status['lost'])) {
                if ($status['color'] == '') {
                    $status['color'] = '#737373';
                }
                array_push($chart['labels'], $status['name']);
                array_push($_data['backgroundColor'], $status['color']);
                array_push($_data['statusLink'], admin_url('leads?status=' . $status['id']));
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
                array_push($_data['data'], $status['total']);
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors      = get_system_favourite_colors();
        $chart       = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];

        $i = 0;
        foreach ($departments as $department) {
            if (!is_admin()) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids      = [];
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', [
                1,
                2,
                4,
            ]);

            $this->db->where('department', $department['departmentid']);
            $total = $this->db->count_all_results(db_prefix() . 'tickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $statuses             = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = [
            1,
            2,
            4,
        ];

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!is_admin()) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids      = [];
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $total = $this->db->count_all_results(db_prefix() . 'tickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['statusLink'], admin_url('tickets/index/' . $status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    public function get_action_items()
    {
        $pageno = !empty($_POST['pageno']) ? $_POST['pageno'] : 1;
        // $pageno = 1;
        $no_of_records_per_page = ACTION_ITEM_LIST;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        $userId = get_staff_user_id(); //$GLOBALS['current_user']->staffid;
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $roles = array('at', 'ata');
        $userDetails =  $this->staff_model->get_userDetails($userId);

        if ($userRole == 'at') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');

            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('(p.status IN (' . ACTION_ITEMS . ') OR (p.status = 2 AND p.action_date < CURDATE()) OR (p.action_date = "' . date('Y-m-d') . '" AND p.status NOT IN (3,5)) )');
            // $this->db->where('(p.status IN (1,2,4,6) OR (p.action_date = "'. date('Y-m-d'). '") )');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('action_date,p.status');
        } else if ($userRole == 'ata') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,ta.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'task_assigned ta', 'ta.taskid = p.id', 'left');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = ta.status', 'left');

            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('(ta.status IN (1,6) OR (ta.status = 2 AND p.action_date < CURDATE()) OR (p.action_date = "' . date('Y-m-d') . '" AND p.status NOT IN (3,5)) )');
            // $this->db->where('(ta.status IN (1,2,4,6) OR (p.action_date = "'. date('Y-m-d'). '") )');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('action_date,ta.status');
        }
        $this->db->limit($no_of_records_per_page, $offset);

        // $this->db->get()->result_array();
        // echo $this->db->last_query(); die;
        return $this->db->get()->result_array();
    }

    public function get_action_item_counts()
    {
        $userId = get_staff_user_id();
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $roles = array('at', 'ata');
        $userDetails =  $this->staff_model->get_userDetails($userId);

        if ($userRole == 'at') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');

            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('(p.status IN (' . ACTION_ITEMS . ') OR (p.status = 2 AND p.action_date < CURDATE()) OR (p.action_date = "' . date('Y-m-d') . '" AND p.status NOT IN (3,5)) )');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('p.status,action_date');
        } else if ($userRole == 'ata') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,ta.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'task_assigned ta', 'ta.taskid = p.id', 'left');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = ta.status', 'left');
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('(ta.status IN (1,6) OR (ta.status = 2 AND p.action_date < CURDATE()) OR (p.action_date = "' . date('Y-m-d') . '" AND p.status NOT IN (3,5)) )');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('p.status,action_date');
        }
        return $this->db->count_all_results();
    }

    public function next_week_deadline()
    {
        $pageno = !empty($_POST['nextpageno']) ? $_POST['nextpageno'] : 1;
        // $pageno = 1;
        $no_of_records_per_page = ACTION_ITEM_LIST;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        $tomorrow = date('Y-m-d', strtotime(' +1 day'));
        $nextWeek = date('Y-m-d', strtotime(' +7 day'));

        $userId = $GLOBALS['current_user']->staffid;
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $roles = array('at', 'ata');
        $userDetails =  $this->staff_model->get_userDetails($userId);

        if ($userRole == 'at') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');

            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('p.status IN (2,6)');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.action_date BETWEEN "' . $tomorrow . '" and "' . $nextWeek . '"');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('p.status,action_date');
        } else if ($userRole == 'ata') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,ta.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'task_assigned ta', 'ta.taskid = p.id', 'left');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = ta.status', 'left');

            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            $this->db->where('p.status IN (2,6)');
            $this->db->where('ta.status IN (2,6)');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.action_date BETWEEN "' . $tomorrow . '" and "' . $nextWeek . '"');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('ta.status,action_date');
        }
        $this->db->limit($no_of_records_per_page, $offset);

        return $this->db->get()->result_array();
    }

    public function next_week_deadline_count()
    {
        $tomorrow = date('Y-m-d', strtotime(' +1 day'));
        $nextWeek = date('Y-m-d', strtotime(' +7 day'));

        $userId = $GLOBALS['current_user']->staffid;
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $roles = array('at', 'ata');
        $userDetails =  $this->staff_model->get_userDetails($userId);

        if ($userRole == 'at') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            // $this->db->where('p.status NOT IN (1,3,5)');
            $this->db->where('p.status IN (2,6)');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.action_date BETWEEN "' . $tomorrow . '" and "' . $nextWeek . '"');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('p.status,action_date');
        } else if ($userRole == 'ata') {
            $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,ta.status as status_id, p.frozen,p.reassigned');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'task_assigned ta', 'ta.taskid = p.id', 'left');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = ta.status', 'left');
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
            // $this->db->where('p.status  NOT IN (1,3,5)');
            // $this->db->where('ta.status NOT IN (1,3,5)');
            $this->db->where('p.status IN (2,6)');
            $this->db->where('ta.status IN (2,6)');
            $this->db->where('p.start_date <= CURDATE()');
            $this->db->where('p.action_date BETWEEN "' . $tomorrow . '" and "' . $nextWeek . '"');
            $this->db->where('p.frozen = 0');
            $this->db->order_by('ta.status,action_date');
        }
        return $this->db->count_all_results();
    }

    public function get_dashboard_widget_data()
    {
        $userId = $GLOBALS['current_user']->staffid;
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $roles = array('at', 'ata');
        $userDetails =  $this->staff_model->get_userDetails($userId);

        //2,4,6 -> In progress,Resolved,Re-Opened
        if ($userRole == 'at') {
            $this->db->select('count(*) AS total_activity, 
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '") THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN p.status = 3 THEN 1 ELSE 0  END) as closed,
            SUM(CASE WHEN ((p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '") ) THEN 1 ELSE 0  END) as escalated,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() ) THEN 1 ELSE 0  END) as ongoing');

            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status pm ', 'pm.id = p.status', 'left');
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
        } elseif ($userRole == 'ata') {
            $this->db->select('count(*) AS total_activity, 
            SUM(CASE WHEN ta.status = 1 THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN ta.status = 3 THEN 1 ELSE 0  END) as closed,
            SUM(CASE WHEN ((ta.status IN (2,6) AND p.action_date < CURDATE())) THEN 1 ELSE 0  END) as escalated,
            SUM(CASE WHEN (ta.status IN (2,6)  AND p.action_date >= CURDATE()) THEN 1 ELSE 0  END) as ongoing');

            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'task_assigned ta', 'ta.taskid = p.id', 'left');
            $this->db->join(db_prefix() . 'project_status ps', 'ps.id = ta.status', 'left');
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id = ' . $userId . ')');
        } elseif ($userRole == 'ar') {
            //Get list of all ATs reporting to AR
            $at_ids = $this->get_ar_assistant($userId);
            if (empty($at_ids))
                return array();

            $this->db->select('count(*) AS total_activity, 
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '") THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN p.status = 3 THEN 1 ELSE 0  END) as closed,
            SUM(CASE WHEN p.status = 5 THEN 1 ELSE 0  END) as rejected,
            SUM(CASE WHEN ((p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '") ) THEN 1 ELSE 0  END) as escalated,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() ) THEN 1 ELSE 0  END) as ongoing');

            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status pm ', 'pm.id = p.status', 'left');
            $this->db->where('p.area_id = ' . $userDetails->area);
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        } else {
            //Get list of all ATs reporting to AR
            $at_ids = $this->get_ar_assistant($userId);

            if (empty($at_ids))
                return array();

            $this->db->select('count(*) AS total_activity, 
            SUM(CASE WHEN (p.status = 1 AND p.action_date > "' . date('Y-m-d') . '") THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN p.status = 3 THEN 1 ELSE 0  END) as closed,
            SUM(CASE WHEN ((p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date <= "' . date('Y-m-d') . '") ) THEN 1 ELSE 0  END) as escalated,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() ) THEN 1 ELSE 0  END) as ongoing');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'project_status pm ', 'pm.id = p.status', 'left');
            $this->db->where('p.area_id = ' . $userDetails->area);
            $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        }

        $this->db->where('p.start_date <= CURDATE()');
        $this->db->where('p.frozen = 0');
        // $this->db->get()->row();
        // echo $this->db->last_query(); die;
        return $this->db->get()->row();
    }

    public function get_evidence_image($projectId, $taskId = NULL, $milestone = NULL)
    {
        if (is_array($taskId)) {
            $date = @$taskId['date'];
            $taskId = @$taskId['task_id'];
            $date = date('Y-m-d', strtotime($date));
            $this->db->where('DATE_FORMAT(dateadded,"%Y-%m-%d")', $date);
        }
        $this->db->where('project_id', $projectId);
        if (!empty($taskId)) {
            $this->db->where("milestone", $taskId);
        } else if ($milestone == 1) {
            $this->db->where("milestone", 0);
        }else if ($milestone == 2) {
            $this->db->where("milestone", 4);
        }
        $this->db->order_by('dateadded', 'desc');
        // $this->db->get(db_prefix().'project_files')->result_array();
        // echo $this->db->last_query(); die;
        return $this->db->get(db_prefix() . 'project_files')->result_array();
    }

    public function get_evidence_location($projectId, $fileId)
    {
        if (!empty($fileId)) {
            $this->db->where('id', $fileId);
        }
        $this->db->where('project_id', $projectId);
        $this->db->order_by('milestone');
        return $this->db->get(db_prefix() . 'project_files')->row();
    }

    public function get_ar_assistant($userId)
    {
        // $assistantLists = $this->staff_model->get_action_reviewer_takers($userId);
        $assistantLists = $this->staff_model->get_action_reviewer_action_takers($userId);
        $assistant_list = array();
        foreach ($assistantLists as $assistant) {
            if(!empty($assistant['staff_id']))
                $assistant_list[] = $assistant['staff_id'];
        }
        return !empty($assistant_list) ? implode(',', $assistant_list) : '';
    }

    public function get_ar_action_items()
    {
        // -> Delayed - AT Name - Status 2
        // -> Unaccepted - AT Name - Status 1 and time > 48 hrs
        // -> Rejected - only action - instead of AT name show Rejection reason - Status 2
        $pageno = !empty($_POST['pageno']) ? $_POST['pageno'] : 1;
        // $pageno = 1;
        $no_of_records_per_page = ACTION_ITEM_LIST;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        $userId = get_staff_user_id();
        $userRole = $GLOBALS['current_user']->role_slug_url;
        $userDetails =  $this->staff_model->get_userDetails($userId);

        //Get list of all ATs reporting to AR
        $at_ids = $this->get_ar_assistant($userId);

        if (empty($at_ids))
            return array();

        $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');
        // $this->db->where('(p.status IN ('.AR_ACTION_ITEMS.') OR (p.status = 3 AND DATE_FORMAT(p.date_finished,"%Y-%m-%d") = CURDATE()) OR (p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "'.ESCALATION_TIME.'") )');
        $this->db->where('(p.status IN (' . AR_ACTION_ITEMS . ') OR (p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '") )');
        $this->db->where('p.area_id = ' . $userDetails->area);
        $this->db->where('p.start_date <= CURDATE()');
        $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        $this->db->where('p.frozen = 0');
        $this->db->order_by('action_date,p.status');
        $this->db->limit($no_of_records_per_page, $offset);

        // $this->db->get()->result_array();
        // echo $this->db->last_query(); die;
        return $this->db->get()->result_array();
    }

    public function total_ar_action_items()
    {
        $userId = get_staff_user_id();
        $userDetails =  $this->staff_model->get_userDetails($userId);

        //Get list of all ATs reporting to AR
        $at_ids = $this->get_ar_assistant($userId);
        
        if (empty($at_ids))
            return array();

        $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,p.status as status_id, p.frozen,p.reassigned');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');
        // $this->db->where('(p.status IN ('.AR_ACTION_ITEMS.') OR (p.status = 3 AND DATE_FORMAT(p.date_finished,"%Y-%m-%d") = CURDATE()) OR (p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "'.ESCALATION_TIME.'") )');
        $this->db->where('(p.status IN (' . AR_ACTION_ITEMS . ') OR (p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '") )');
        $this->db->where('p.area_id = ' . $userDetails->area);
        $this->db->where('p.start_date <= CURDATE()');
        $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        $this->db->where('p.frozen = 0');
        $this->db->order_by('p.status,action_date');

        return $this->db->count_all_results();
    }

    public function recently_ar_closed_tickets()
    {
        $pageno = !empty($_POST['nextpageno']) ? $_POST['nextpageno'] : 1;
        // $pageno = 1;
        $no_of_records_per_page = ACTION_ITEM_LIST;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        $today = date('Y-m-d');
        $lastWeek = date('Y-m-d', strtotime(' -7 day'));

        $userId = get_staff_user_id();
        $userDetails =  $this->staff_model->get_userDetails($userId);

        //Get list of all ATs reporting to AR
        $at_ids = $this->get_ar_assistant($userId);

        if (empty($at_ids))
            return array();

        $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.bg-color,ps.name as status_name,p.status as status_id');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');
        $this->db->where('p.status = 3 and DATE_FORMAT(p.date_finished,"%Y-%m-%d") BETWEEN "' . $lastWeek . '" and "' . $today . '"');
        $this->db->where('p.area_id = ' . $userDetails->area);
        $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        $this->db->where('p.frozen = 0');
        $this->db->order_by('p.status,date_finished', 'desc');
        $this->db->limit($no_of_records_per_page, $offset);

        return $this->db->get()->result_array();
    }

    public function total_ar_closed_tickets()
    {
        $today = date('Y-m-d');
        $lastWeek = date('Y-m-d', strtotime(' -7 day'));

        $userId = get_staff_user_id();
        $userDetails =  $this->staff_model->get_userDetails($userId);

        //Get list of all ATs reporting to AR
        $at_ids = $this->get_ar_assistant($userId);

        if (empty($at_ids))
            return array();

        $this->db->select('p.name as project_name,p.id as project_id, p.*,ps.color,ps.name as status_name,p.status as status_id');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'project_status ps', 'ps.id = p.status', 'left');
        $this->db->where('p.status = 3 and DATE_FORMAT(p.date_finished,"%Y-%m-%d") BETWEEN "' . $lastWeek . '" and "' . $today . '"');
        $this->db->where('p.area_id = ' . $userDetails->area);
        $this->db->where('p.id IN (select project_id from ' . db_prefix() . 'project_members where active = 1 AND staff_id IN (' . $at_ids . ') )');
        $this->db->where('p.frozen = 0');
        $this->db->order_by('p.status,date_finished', 'desc');

        return $this->db->count_all_results();
    }

    public function get_ae_global($tableParams)
    {


        $report_date = (!empty($tableParams['report_date'])) ? $tableParams['report_date'] : '';
        $to_date = (!empty($tableParams['to_date'])) ? date("Y-m-d", strtotime($tableParams['to_date'])) : '';
        $from_date = (!empty($tableParams['from_date'])) ? date("Y-m-d", strtotime($tableParams['from_date']))  : '';
        $category = (!empty($tableParams['category'])) ? $tableParams['category'] : '';
        $area = !empty($tableParams["area"]) ? $tableParams["area"] : "";
        $region_list = !empty($tableParams["region_list"]) ? $tableParams["region_list"] : "";
        $sub_region_list = !empty($tableParams["sub_region_list"]) ? $tableParams["sub_region_list"] : "";
        $duration = (!empty($tableParams['duration'])) ? $tableParams['duration'] : '';
        $statusIds = ['1', '2', '3', '5', '7', '8', '9'];
        $cat_id = [];
        //  pre($tableParams);


        if (!empty($tableParams['category'])) {
            foreach ($tableParams['category'] as $c_id) {
                $cat_id[] = $c_id;
            }
            $catids = $this->get_parent_category($category);
            if (count($catids) > 0) {
                foreach ($catids as $var) {
                    $cat_id[] = $var['id'];
                }
            }
        }
        // pre($cat_id);

        // pre($report_date);
        $pro_id = [];
        if (!empty($report_date)) {
            $proIds = $this->report_model->get_project_action($report_date, $to_date, $from_date, $statusIds);
            if (!empty($proIds)) {
                foreach ($proIds as $var) {
                    $pro_id[] = $var['project_id'];
                }
            }
        }

        if ($region_list == "" && $sub_region_list == "") {

            $this->db->select('areaid,a.name, 
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as new,
            SUM(CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as escalated,
            SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as wip,
            SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as rejected,
            SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassigned,
            SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen,
            SUM((CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) + (CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END)) as total');
            $this->db->from(db_prefix() . 'projects p');
            $this->db->join(db_prefix() . 'area a', 'a.areaid = p.area_id', 'left');
            $this->db->order_by("a.name", "ASC");
        }
        if ($region_list != "") {
            $this->db->select('r.id as region_id, r.region_name,
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as new,
            SUM(CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as escalated,
            SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as wip,
            SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as rejected,
            SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassigned,
            SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen,
            SUM((CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) + (CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END)) as total');
            $this->db->from(db_prefix() . 'region r');
            $this->db->join(db_prefix() . 'projects p', 'r.id = p.region_id', 'left');
            // $this->db->where(["r.status" => 1, "r.is_deleted" => 0]);
            $this->db->order_by("r.region_name", "ASC");
        }
        if ($sub_region_list != "") {
            $this->db->select('sr.id as sub_region_id, sr.region_name as sub_region_name,
            SUM(CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as new,
            SUM(CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as escalated,
            SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as close,
            SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as wip,
            SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as rejected,
            SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as unassigned,
            SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as frozen,
            SUM((CASE WHEN (p.status = 1 AND p.action_date >= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date < "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) + (CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) + (CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END)) as total');
            $this->db->from(db_prefix() . 'sub_region sr ');
            $this->db->join(db_prefix() . 'projects p', 'sr.id = p.subregion_id', 'left');
            // $this->db->where(["sr.status" => 1, "sr.is_deleted" => 0]);
            $this->db->order_by("sr.region_name", "ASC");
        }

        // if (!empty($cat_id) && count($cat_id) > 0) {
        //     $this->db->join(db_prefix() . 'issue_categories ic ', 'ic.id = p.issue_id', 'left');
        //     $this->db->where_in('ic.id', $cat_id);
        // }

        if (!empty($duration)  && empty($category)) {
            $this->db->where('p.issue_id', 0);
        }

        // if (!empty($category) && count($category) > 0 && $role != 5) {
        //     $this->db->where_in('p.issue_id', $category);
        // }

        if (!empty($cat_id) && count($cat_id) > 0) {
            // $this->db->where_in('p.issue_id', $cat_id);
            $this->db->group_start();
            $this->db->where_in('p.issue_id', $cat_id);
            $this->db->or_where_in('p.issue_id', $category);
            $this->db->group_end();
        }

        if (!empty($report_date)) {
            if (!empty($pro_id)) {
                $this->db->where_in('p.id', $pro_id);
            } else {
                $this->db->where('p.id', 0);
            }
        }

        if ($area != "") {
            $this->db->where('p.area_id', $area);
        }
        if ($region_list != "") {
            $this->db->where_in("p.region_id", $region_list);
        }
        if ($sub_region_list != "") {
            $this->db->where_in("p.subregion_id", $sub_region_list);
        }

        if (!empty($report_date) && $report_date != 'custom') {

            // if ($report_date == 'this_month') {
            //     $this->db->where("p.action_date", date('m'));
            // } else if ($report_date == 'last_month') {
            //     $this->db->where("p.action_date", date('m', strtotime('-1 month')));
            // } else if ($report_date == 'this_year') {
            //     $this->db->where("p.action_date", date('Y'));
            // } else if ($report_date == 'last_year') {
            //     $this->db->where("p.action_date", date('Y', strtotime('-1 year')));
            // } else if ($report_date == '3') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-2 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '6') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-5 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '12') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-11 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // }
        }

        // if (!empty($report_date) && $report_date == 'custom' && !empty($to_date) && !empty($from_date)) {
        //     $this->db->where('p.action_date >=', $from_date);
        //     $this->db->where('p.action_date <=', $to_date);
        // }
        // if ($region_list != ""){
        //     $this->db->group_by("")
        // }
        if ($region_list == "" && $sub_region_list == "") {

            $this->db->group_by('a.areaid');
            $this->db->order_by('a.name');
        }
        if ($region_list != "") {

            $this->db->group_by('p.region_id');
            $this->db->order_by('r.region_name');
        }
        if ($sub_region_list != "") {

            $this->db->group_by('p.subregion_id');
            $this->db->order_by('sr.region_name');
        }
        // return $this->db->get()->result_array();
        $result = $this->db->get()->result_array();
        // pre($this->db->last_query());
        //echo $this->db->last_query(); die();
        return $result;
    }

    public function get_chart_data($area = null, $data)
    {
        $report_date = (!empty($data['report_months'])) ? $data['report_months'] : '';
        $to_date = (!empty($data['report_to'])) ? date("Y-m-d", strtotime($data['report_to'])) : '';
        $from_date = (!empty($data['report_from'])) ? date("Y-m-d", strtotime($data['report_from']))  : '';
        $category = (!empty($data['category'])) ? $data['category'] : '';
        $duration = (!empty($data['duration'])) ? $data['duration'] : '';
        $statusIds = ['1', '2', '3', '5', '7', '8', '9'];
        $cat_id = [];
        if (!empty($category)) {
            foreach ($category as $c_id) {
                $cat_id[] = $c_id;
            }
            $catids = $this->get_parent_category($category);
            if (count($catids) > 0) {
                foreach ($catids as $var) {
                    $cat_id[] = $var['id'];
                }
            }
        }


        $pro_id = [];
        if (!empty($report_date)) {
            $proIds = $this->report_model->get_project_action($report_date, $to_date, $from_date, $statusIds);
            if (!empty($proIds)) {
                foreach ($proIds as $var) {
                    $pro_id[] = $var['project_id'];
                }
            }
        }


        $this->db->select('SUM(CASE WHEN (p.status = 1 AND p.action_date > "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0  END) as New,
        SUM(CASE WHEN (p.status IN (2,4,6) AND p.action_date < CURDATE() AND p.frozen = 0) OR (p.status = 1 AND p.action_date <= "' . date('Y-m-d') . '" AND p.frozen = 0) THEN 1 ELSE 0 END) as Escalated,
        SUM(CASE WHEN (p.status = 3 AND p.frozen = 0) THEN 1 ELSE 0  END) as Closed,
        SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() AND p.frozen = 0 ) THEN 1 ELSE 0  END) as WIP,
        SUM(CASE WHEN (p.status = 5 AND p.frozen = 0) THEN 1 ELSE 0  END) as Rejected,
        SUM(CASE WHEN (p.status = 9 AND p.is_assigned = 0 AND p.frozen = 0) THEN 1 ELSE 0  END) as Unassigned,
        SUM(CASE WHEN p.frozen = 1 THEN 1 ELSE 0  END) as Frozen');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'area a', "p.area_id = a.areaid", "LEFT");
        if (!empty($cat_id) && count($cat_id) > 0) {
            $this->db->group_start();
            $this->db->where_in('p.issue_id', $cat_id);
            $this->db->or_where_in('p.issue_id', $category);
            $this->db->group_end();
        }
        if ($area != null) {
            $this->db->where("p.area_id", $area);
        }

        if (!empty($duration)  && empty($category)) {
            $this->db->where('p.issue_id', 0);
        }

        // pre($report_date);
        if (!empty($report_date)) {
            if (!empty($pro_id)) {
                $this->db->where_in('p.id', $pro_id);
            } else {
                $this->db->where('p.id', 0);
            }
        }



        if (!empty($report_date) && $report_date != 'custom') {

            // if ($report_date == 'this_month') {
            //     $this->db->where("p.action_date", date('m'));
            // } else if ($report_date == 'last_month') {
            //     $this->db->where("p.action_date", date('m', strtotime('-1 month')));
            // } else if ($report_date == 'this_year') {
            //     $this->db->where("p.action_date", date('Y'));
            // } else if ($report_date == 'last_year') {
            //     $this->db->where("p.action_date", date('Y', strtotime('-1 year')));
            // } else if ($report_date == '3') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-2 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '6') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-5 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // } else if ($report_date == '12') {
            //     $this->db->where('p.action_date >=', date('Y-m-01', strtotime('-11 MONTH')));
            //     $this->db->where('p.action_date <=', date('Y-m-t'));
            // }
        }

        // if (!empty($report_date) && $report_date == 'custom' && !empty($to_date) && !empty($from_date)) {
        //     $this->db->where('p.action_date >=', $from_date);
        //     $this->db->where('p.action_date <=', $to_date);
        // }
        //return $this->db->get()->result_array();

        $result = $this->db->get()->result_array();
        // pre($this->db->last_query());

        //  echo $this->db->last_query(); die();
        return $result;
    }


    public function get_region($tableParams)
    {
        $category = (!empty($tableParams['category'])) ? $tableParams['category'] : '';
        $area_id = (!empty($tableParams['area_id'])) ? $tableParams['area_id'] : '';
        $cat_id = [];
        if (!empty($tableParams['category'])) {
            $catid = $this->get_parent_category($category);
            foreach ($catid as $var) {
                $cat_id[] = $var['id'];
            }
        }

        $this->db->select('GROUP_CONCAT( ic.id)as issue,r.id,r.region_name,count(*) AS total, 
        SUM(CASE WHEN (p.status = 1 AND p.action_date > "' . date('Y-m-d') . '") THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN p.status = 3 THEN 1 ELSE 0  END) as close,
        SUM(CASE WHEN ((p.status IN (2,4,6) AND p.action_date < CURDATE()) OR (p.status = 1 AND p.project_created <= "' . date('Y-m-d') . '") ) THEN 1 ELSE 0  END) as escalated,
        SUM(CASE WHEN (p.status IN (2,4,6)  AND p.action_date >= CURDATE() ) THEN 1 ELSE 0  END) as wip');
        $this->db->from(db_prefix() . 'projects p');
        $this->db->join(db_prefix() . 'region r ', 'r.id = p.region_id', 'left');
        $this->db->join(db_prefix() . 'project_status pm ', 'pm.id = p.status', 'left');
        $this->db->join(db_prefix() . 'issue_categories ic ', 'ic.id = p.issue_id', 'left');
        if (!empty($cat_id) && count($cat_id) > 0) {
            $this->db->where_in('ic.id', $cat_id);
        }
        $this->db->where('r.area_id', $area_id);
        $this->db->group_by('r.id');
        return $this->db->get()->result_array();
    }

    public function get_parent_category($category)
    {
        $this->db->select('id')->from(db_prefix() . 'issue_categories');
        if ($category) {
            $this->db->where_in('parent_issue_id', $category);
        }
        $query = $this->db->get();
        $catid =  $query->result_array();
        return $catid;
    }
}

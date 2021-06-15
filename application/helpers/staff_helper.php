<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @since  2.3.3
 * Get available staff permissions, modules can use the filter too to hook permissions
 * @param  array  $data additional data passed from view role.php and member.php
 * @return array
 */
function get_available_staff_permissions($data = [])
{
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';

    $allPermissionsArray = [
        'view_own' => _l('permission_view_own'),
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
    ];

    $withoutViewOwnPermissionsArray = [
        'view'   => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    $withNotApplicableViewOwn = array_merge(['view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')]], $withoutViewOwnPermissionsArray);

    $corePermissions = [
        'customers' => [
            'name'         => _l('clients'),
            'capabilities' => $withNotApplicableViewOwn,
            'help'         => [
                'view_own' => _l('permission_customers_based_on_admins'),
            ],
        ],
        'email_templates' => [
            'name'         => _l('email_templates'),
            'capabilities' => [
                'view' => $viewGlobalName,
                'edit' => _l('permission_edit'),
            ],
        ],

        'projects' => [
            'name'         => _l('projects'),
            'capabilities' => $withNotApplicableViewOwn,
            'help'         => [
                'view'     => _l('help_project_permissions'),
                'view_own' => _l('permission_projects_based_on_assignee'),
            ],
        ],

        'reports' => [
            'name'         => _l('reports'),
            'capabilities' => [
                'view' => $viewGlobalName,
            ],
        ],
        // 'roles' => [
        //     'name'         => _l('roles'),
        //     'capabilities' => $withoutViewOwnPermissionsArray,
        // ],
        'settings' => [
            'name'         => _l('settings'),
            'capabilities' => [
                'view' => $viewGlobalName,
                'edit' => _l('permission_edit'),
            ],
        ],


        'tasks' => [
            'name'         => _l('tasks'),
            'capabilities' => $withNotApplicableViewOwn,
            'help'        => [
                'view'     => _l('help_tasks_permissions'),
                'view_own' => _l('permission_tasks_based_on_assignee'),
            ],
        ],
        'area' => [
            'name'         => _l('area'),
            'capabilities' => $withNotApplicableViewOwn,
            'help'        => [
                'view'     => _l('help_tasks_permissions'),
                'view_own' => _l('permission_tasks_based_on_assignee'),
            ],
        ],

        'region' => [
            'name'         => _l('region'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'subregion' => [
            'name'         => _l('subregion'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'categories' => [
            'name'         => _l('categories'),
            'capabilities' => $withNotApplicableViewOwn,
            'help'        => [
                'view'     => _l('help_categories_permissions'),
                'view_own' => _l('permission_categories_based_on_assignee'),
            ],
        ],

        'categories_manage' => [
            'name'         => _l('Manage Categories'),
            'capabilities' => $withoutViewOwnPermissionsArray,
            'help'        => [
                'view'     => _l('for Area Admin only'),
                'view_own' => _l('permission_categories_based_on_assignee'),
            ],
        ],

        'staff' => [
            'name'         => _l('Manage Users'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'staff_aa' => [
            'name'         => _l('Area Admin'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'staff_ae-global' => [
            'name'         => _l('AE God Mode'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'staff_ae-area' => [
            'name'         => _l('Action Enforcers'),
            'capabilities' => $withoutViewOwnPermissionsArray,
        ],

        'staff_ar' => [
            'name'         => _l('Action Reviewers'),
            'capabilities' => $withNotApplicableViewOwn,
        ],

        'staff_at' => [
            'name'         => _l('Action Takers'),
            'capabilities' => $withNotApplicableViewOwn,
        ],

        'staff_ata' => [
            'name'         => _l('Action Taker Assistants'),
            'capabilities' => $withNotApplicableViewOwn,
        ],

    ];

    $addLeadsPermission = false;
    if (isset($data['staff_id']) && $data['staff_id']) {
        $is_staff_member = is_staff_member($data['staff_id']);
        if (!$is_staff_member) {
            $addLeadsPermission = false;
        }
    }

    if ($addLeadsPermission) {
        $corePermissions['leads'] = [
            'name'         => _l('leads'),
            'capabilities' => [
                'view'   => $viewGlobalName,
                'delete' => _l('permission_delete'),
            ],
            'help' => [
                'view' => _l('help_leads_permission_view'),
            ],
        ];
    }

    return hooks()->apply_filters('staff_permissions', $corePermissions, $data);
}

/**
 * Get staff by ID or current logged in staff
 * @param  mixed $id staff id
 * @return mixed
 */
function get_staff($id = null)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }

    // Staff not logged in
    if (empty($id)) {
        return null;
    }

    if (!class_exists('staff_model', false)) {
        get_instance()->load->model('staff_model');
    }

    return get_instance()->staff_model->get($id);
}
// roles function

// function get_role($id=''){
//     $data= json_decode(json_encode(get_instance()->roles_model->get($id)), true);
//     return $data['roleid'];

// }
/**
 * Return staff profile image url
 * @param  mixed $staff_id
 * @param  string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');

    if ((string) $staff_id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $staff = $GLOBALS['current_user'];
    } else {
        $CI = &get_instance();
        $CI->db->select('profile_image')
            ->where('staffid', $staff_id);

        $staff = $CI->db->get(db_prefix() . 'staff')->row();
    }

    if ($staff) {
        if (!empty($staff->profile_image)) {
            $profileImagePath = 'uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image;
            if (file_exists($profileImagePath)) {
                $url = base_url($profileImagePath);
            }
        }
    }

    return $url;
}

/**
 * Staff profile image with href
 * @param  boolean $id        staff id
 * @param  array   $classes   image classes
 * @param  string  $type
 * @param  array   $img_attrs additional <img /> attributes
 * @return string
 */
function staff_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
    $url = base_url('assets/images/user-placeholder.jpg');

    $id = trim($id);

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . html_escape($val) . '" ';
    }

    $blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

    if ((string) $id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $result = $GLOBALS['current_user'];
    } else {
        $CI     = &get_instance();
        $result = $CI->app_object_cache->get('staff-profile-image-data-' . $id);

        if (!$result) {
            $CI->db->select('profile_image,firstname,lastname');
            $CI->db->where('staffid', $id);
            $result = $CI->db->get(db_prefix() . 'staff')->row();
            $CI->app_object_cache->add('staff-profile-image-data-' . $id, $result);
        }
    }

    if (!$result) {
        return $blankImageFormatted;
    }

    if ($result && $result->profile_image !== null) {
        $profileImagePath = 'uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image;
        if (file_exists($profileImagePath)) {
            $profile_image = '<img ' . $_attributes . ' src="' . base_url($profileImagePath) . '" class="' . implode(' ', $classes) . '" />';
        } else {
            return $blankImageFormatted;
        }
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    }

    return $profile_image;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $tmpStaffUserId = get_staff_user_id();
    if ($userid == '' || $userid == $tmpStaffUserId) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname;
        }
        $userid = $tmpStaffUserId;
    }

    $CI = &get_instance();

    $staff = $CI->app_object_cache->get('staff-full-name-data-' . $userid);

    if (!$staff) {
        $CI->db->where('staffid', $userid);
        $staff = $CI->db->select('firstname,lastname')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-full-name-data-' . $userid, $staff);
    }

    return html_escape($staff ? $staff->firstname . ' ' . $staff->lastname : '');
}

/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        // checking for current user if is admin
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->default_language;
        }

        $staffid = get_staff_user_id();
    }
    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }

    return '';
}

function get_staff_recent_search_history($staff_id = null)
{
    $recentSearches = get_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches');

    if ($recentSearches == '') {
        $recentSearches = [];
    } else {
        $recentSearches = json_decode($recentSearches);
    }

    return $recentSearches;
}
//  function validate_field($data){
//     $error=array();
//     $realdata=explode(',',$data);
//     for($i=0;$i<count($realdata);$i++){
//         if(empty($realdata[$i])){
//             array_push($error,$i);
//         }
//     }
//     $js=implode(',',$error);
//     return $js;


// }
function getstaffid()
{
    $CI = &get_instance();
    $id = $CI->session->userdata('staff_user_id');
    return $id;
}
function getstaffarea($staffid)
{
    $CI = &get_instance();
    $CI->db->select('area');
    $CI->db->where('staffid', $staffid);
    $rows = $CI->db->get(db_prefix() . 'staff')->row();
    $staff = json_decode(json_encode($rows), true);
    if (!empty($staff)) {
        return $staff['area'];
    } else {
        return false;
    }
}
function update_staff_recent_search_history($history, $staff_id = null)
{
    $totalRecentSearches = hooks()->apply_filters('total_recent_searches', 5);
    $history             = array_reverse($history);
    $history             = array_unique($history);
    $history             = array_splice($history, 0, $totalRecentSearches);

    update_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches', json_encode($history));

    return $history;
}


/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string  $staff_id staff id
 * @return boolean
 */
function is_staff_member($staff_id = '')
{
    $CI = &get_instance();
    if ($staff_id == '') {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
        ->where('is_not_staff', 0);

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}


/**
 * Ganerate Random Password
 * @param  length
 * @return string
 */
function random_password($len = 8)
{
    //enforce min length 8
    if ($len < 8)
        $len = 8;

    //define character libraries - remove ambiguous characters like iIl|1 0oO
    $sets = array();
    $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    $sets[] = '23456789';
    $sets[]  = '@#$%&';

    $password = '';

    //append a character from each set - gets first 4 characters
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
    }

    //use all characters to fill up to $len
    while (strlen($password) < $len) {
        //get a random set
        $randomSet = $sets[array_rand($sets)];

        //add a random char from the random set
        $password .= $randomSet[array_rand(str_split($randomSet))];
    }

    //shuffle the password string before returning!
    return str_shuffle($password);
}

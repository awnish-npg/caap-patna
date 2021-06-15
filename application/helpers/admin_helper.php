<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\messages\Message;
use app\services\messages\PopupMessage;

function app_admin_head()
{
    hooks()->do_action('app_admin_head');
}

/**
 * @since 2.3.2
 * @return null
 */
function app_admin_footer()
{
    /**
     * @deprecated 2.3.2 Use app_admin_footer instead
     */
    do_action_deprecated('after_js_scripts_render', [], '2.3.2', 'app_admin_footer');

    hooks()->do_action('app_admin_footer');
}

/**
 * @since  1.0.0
 * Init admin head
 * @param  boolean $aside should include aside
 */
function init_head($aside = true)
{
    $CI = &get_instance();
    $CI->load->view('admin/includes/head');
    $CI->load->view('admin/includes/header', []);
    $CI->load->view('admin/includes/setup_menu');
    if ($aside == true) {
        $CI->load->view('admin/includes/aside');
    }
}
/**
 * @since  1.0.0
 * Init admin footer/tails
 */
function init_tail()
{
    $CI = &get_instance();
    $CI->load->view('admin/includes/scripts');
}
/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    $adminURI = get_admin_uri();

    if ($url == '' || $url == '/') {
        if ($url == '/') {
            $url = '';
        }

        return site_url($adminURI) . '/';
    }

    return site_url($adminURI . '/' . $url);
}

/**
 * @since  2.3.3
 * Helper function for checking staff capabilities, this function should be used instead of has_permission
 * Can be used e.q. staff_can('view', 'invoices');
 *
 * @param  string $capability         e.q. view | create | edit | delete | view_own | can_delete
 * @param  string $feature            the feature name e.q. invoices | estimates | contracts | my_module_name
 *
 *    NOTE: The $feature parameter is available as optional, but it's highly recommended always to be passed
 *    because of the uniqueness of the capability names.
 *    For example, if there is a capability "view" for feature "estimates" and also for "invoices" a capability "view" exists too
 *    In this case, if you don't pass the feature name, there may be inaccurate results
 *    If you are certain that your capability name is unique e.q. my_prefixed_capability_can_create , you don't need to pass the $feature
 *    and you can use this function as e.q. staff_can('my_prefixed_capability_can_create')
 *
 * @param  mixed $staff_id            staff id | if not passed, the logged in staff will be checked
 *
 * @return boolean
 */
function staff_can($capability, $feature = null, $staff_id = '')
{
    $staff_id = $staff_id == '' ? get_staff_user_id() : $staff_id;

    /**
     * Maybe permission is function?
     * Example is_admin or is_staff_member
     */
    if (function_exists($capability) && is_callable($capability)) {
        return call_user_func($capability, $staff_id);
    }

    /**
     * If user is admin return true
     * Admins have all permissions
     */
    if (is_admin($staff_id)) {
        return true;
    }

    $CI = &get_instance();

    $permissions = null;
    /**
     * Stop making query if we are doing checking for current user
     * Current user is stored in $GLOBALS including the permissions
     */
    if ((string) $staff_id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $permissions = $GLOBALS['current_user']->permissions;
    }

    /**
     * Not current user?
     * Get permissions for this staff
     * Permissions will be cached in object cache upon first request
     */
    if (!$permissions) {
        if (!class_exists('staff_model', false)) {
            $CI->load->model('staff_model');
        }

        $permissions = $CI->staff_model->get_staff_permissions($staff_id);
    }

    if (!$feature) {
        $retVal = in_array_multidimensional($permissions, 'capability', $capability);

        return hooks()->apply_filters('staff_can', $retVal, $capability, $feature, $staff_id);
    }

    foreach ($permissions as $permission) {
        if (
            $feature == $permission['feature']
            && $capability == $permission['capability']
        ) {
            return hooks()->apply_filters('staff_can', true, $capability, $feature, $staff_id);
        }
    }

    return hooks()->apply_filters('staff_can', false, $capability, $feature, $staff_id);
}

function get_area_id()
{
    $CI          = &get_instance();
    $CI->db->select('areaid');
    $rows = $CI->db->get(db_prefix() . 'area')->result_array();
    // print_r($rows);
    $areas = array();
    $count = 0;
    foreach ($rows as $row) {
        $areas[$count] = $row['areaid'];
        $count++;
    }
    return $areas;
}
function get_staff_phone($id){
    $CI          = &get_instance();
    if($id !=""){
        $CI->db->select('phonenumber');
        $CI->db->where('staffid', $id);
        $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    
        if(!empty($rows) and !empty($rows[0])){
            $phone_numbers = $rows[0];
            $phone = $phone_numbers['phonenumber'];
            return $phone; 
        }
        else{
            return '';
        }

    }else{
        return '';
    }
}
function get_staff_area_id($id)
{
    $CI          = &get_instance();
    $CI->db->select('area');
    $CI->db->where('staffid', $id);
    $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    $staffids = $rows[0];
    $staffid = $staffids['area'];
    return $staffid;
}
function get_project_due_date($projectId){
    $CI          = &get_instance();
    $CI->db->select('deadline');
    $CI->db->where('id', $projectId);
    $rows = $CI->db->get(db_prefix() . 'projects')->result_array();
    if(!empty($rows)){
        $p_details = $rows[0];
        $deadline = $p_details['deadline'];
        return $deadline;
    }else{
        return '';
    }
}
function get_area_name($id)
{
    $CI          = &get_instance();
    $CI->db->select('name');
    $CI->db->where('areaid', $id);
    $rows = $CI->db->get(db_prefix() . 'area')->result_array();
    $areaname = $rows[0];
    return $areaname['name'];
}
function get_slug($id){
    $CI          = &get_instance();
    $CI->db->select('slug_url');
    $CI->db->where('roleid', $id);
    $rows = $CI->db->get(db_prefix() . 'roles')->result_array();
    $staffslugs = $rows[0];
    $staffslug = $staffslugs['slug_url'];
    return $staffslug;
}
function get_area_admin_by_area($area){
    $CI          = &get_instance();
    $CI->db->select('staffid');
    $CI->db->where('area', $area);
    $CI->db->where('role', 6);
    $CI->db->where('active', 1);
    $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    if(!empty($rows)){
    $staffemails = $rows[0];
    $staffemail = $staffemails['staffid'];
    return $staffemail;
    }else{
        return "";
    } 
}
function get_staff_email($id,$area){
    $CI          = &get_instance();
    if($id !=""){
    $CI->db->select('email');
    $CI->db->where('staffid', $id);
    $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    $staffemails = $rows[0];
    $staffemail = $staffemails['email'];
    return $staffemail; 
    }else{
        $CI->db->select('email');
        $CI->db->where('area', $area);
        $CI->db->where('role', 6);
        $CI->db->where('active', 1);
        $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
        if(!empty($rows)){
        $staffemails = $rows[0];
        $staffemail = $staffemails['email'];
        return $staffemail;
        }else{
            return "";
        } 
    } 
}
function get_phone_number_by_email($email){
    $CI          = &get_instance();
    $CI->db->select('phonenumber');
    $CI->db->where('active', 1);
    $CI->db->where('email',$email);
    $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    if(!empty($rows)){
    $phone_numbers = $rows[0];
    $phone_no = $phone_numbers['phonenumber'];
    return $phone_no;
    }else{
        return "";
    } 
}
function get_superadmin($key){
        $CI          = &get_instance();
    $CI->db->select('roleid');
    $CI->db->where('slug_url', 'ap-sa');
    $rows = $CI->db->get(db_prefix() . 'roles')->result_array();
    if(!empty($rows)){
        $sadata=$rows[0];
        $saroleid=$sadata['roleid'];
        if($key=="email"){
            $CI->db->select('email');
            $CI->db->where('role', $saroleid);
            $sdata = $CI->db->get(db_prefix() . 'staff')->result_array();
            if(!empty($sdata)){
                $sa=$sdata[0];
                return $sa['email'];
            }
        }else if($key=="id"){
            $CI->db->select('staffid');
            $CI->db->where('role', $saroleid);
            $sdata = $CI->db->get(db_prefix() . 'staff')->result_array();
            if(!empty($sdata)){
                $sa=$sdata[0];
                return $sa['staffid'];
            }
        }
        else{
            return "";
        }
     }else{
        return "";
    }
}
function get_roleid($id)
{
    $CI          = &get_instance();
    $CI->db->select('role');
    $CI->db->where('staffid', $id);
    $rows = $CI->db->get(db_prefix() . 'staff')->result_array();
    $staffroleids = $rows[0];
    $staffroleid = $staffroleids['role'];
    return $staffroleid;
}

function get_staff_location($type = '')
{
    $location = [];
    if (!empty($GLOBALS['current_user']->area) && $GLOBALS['current_user']->area != 0) {
        if (isset($GLOBALS['current_user']->location['area'])) {
            $location[] = $GLOBALS['current_user']->location['area'];
        }
        if (!empty($GLOBALS['current_user']->location['region_name'])) {
            // $location[] = $GLOBALS['current_user']->location['region_name'];
        }
        if (!empty($GLOBALS['current_user']->location['subregion_name'])) {
            // $location[] = $GLOBALS['current_user']->location['subregion_name'];
        }
    }

    $sub_region_loc = ["at", "ata"];
    $output = '';
    
    if (in_array($GLOBALS['current_user']->role_slug_url, $sub_region_loc)) {
        if (!empty($GLOBALS['current_user']->location['subregion_name'])) {
            $output .= '<li>' . $GLOBALS['current_user']->location['subregion_name'] . '</li>';
        }
    } else if($GLOBALS['current_user']->role_slug_url == 'ar'){
        if (isset($GLOBALS['current_user']->location['area'])) {
            $output .= ' <li>' . $GLOBALS['current_user']->location['area'] . '</li>';
        }
        if (!empty($GLOBALS['current_user']->location['region_name'])) {
            $output .= ' <li>' . $GLOBALS['current_user']->location['region_name'] . '</li>';
        }
        if (!empty($GLOBALS['current_user']->location['subregion_name'])) {
            $output .= '<li>' . $GLOBALS['current_user']->location['subregion_name'] . '</li>';
        }
    } else {
        if (isset($GLOBALS['current_user']->location['area'])) {
            $output .= ' <li>' . $GLOBALS['current_user']->location['area'] . '</li>';
        }
        if (!empty($GLOBALS['current_user']->location['region_name'])) {
            // $output .= ' <li>' . $GLOBALS['current_user']->location['region_name'] . '</li>';
        }
        if (!empty($GLOBALS['current_user']->location['subregion_name'])) {
            // $output .= '<li>' . $GLOBALS['current_user']->location['subregion_name'] . '</li>';
        }
    }
    $CI = &get_instance();
    $CI->load->model('staff_model');
    $ar_region = $CI->staff_model->get_action_reviewer_takers($GLOBALS['current_user']->staffid);
    

  
    if (!empty($ar_region)) {

        $sort = array();
        foreach($ar_region as $k=>$v) {    
            $sort['region_name'][$k] = $v['region_name'];   
        }
        array_multisort($sort['region_name'], SORT_DESC,$ar_region);
        $ar_location = '';
        $ar_locations = array();
      
        $map_key = '';
        foreach ($ar_region as $k => $at_list) {
            $region_map_key = $at_list['region_name'].'-'.$at_list['sub_region_name'];
            if($region_map_key != $map_key){
                $ar_locations[$at_list['region_name']][] = $at_list['sub_region_name'];
            }
            $map_key = $region_map_key;
          
            //$ar_locations[$at_list['region_name']] = $at_list['sub_region_name'];
        }
        
      //  pre(arsort($ar_region));
        // Goa - Panaji - Panaji Municipal Corp Two, Panaji Municipal Corp Three
        // Goa - Margao - Margao Municipal Corp One
        foreach ($ar_locations as $region => $subregion) {
            $area = !empty($location[0]) ? $location[0] : '';
            $subregion_list = !empty($subregion) ? implode(', ',$subregion) : '';
            $ar_location .=  $area . " - ".$region . " - ".$subregion_list .'<br>';
        }
        if ($type == 'plain') {
            return $ar_location;
        }
    }
    if ($type == 'plain') {
        return implode(" / ", $location);
    }

    if (!empty($output)) return $output;
    else return '<span>&nbsp;</span>';
}
/**
 * @since  2.3.3
 * Check whether a role has specific permission applied
 * @param  mixed  $role_id    role id
 * @param  string  $capability e.q. view|create|read
 * @param  string  $feature    the feature, e.q. invoices|estimates etc...
 * @return boolean
 */
function has_role_permission($role_id, $capability, $feature)
{
    $CI          = &get_instance();
    $permissions = $CI->roles_model->get($role_id)->permissions;

    foreach ($permissions as $appliedFeature => $capabilities) {
        if ($feature == $appliedFeature && in_array($capability, $capabilities)) {
            return true;
        }
    }

    return false;
}

/**
 * @since 1.0.0
 * NOTE: This function will be deprecated in future updates, use staff_can($do, $feature = null, $staff_id = '') instead
 *
 * Check if staff user has permission
 * @param  string  $permission permission shortname
 * @param  mixed  $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '', $can = '')
{
    return staff_can($can, $permission, $staffid);
}
/**
 * @since  1.0.0
 * Load language in admin area
 * @param  string $staff_id
 * @return string return loaded language
 */
function load_admin_language($staff_id = '')
{
    $CI = &get_instance();

    $CI->lang->is_loaded = [];
    $CI->lang->language  = [];

    $language = get_option('active_language');
    if (is_staff_logged_in() || $staff_id != '') {
        $staff_language = get_staff_default_language($staff_id);
        if (
            !empty($staff_language)
            && file_exists(APPPATH . 'language/' . $staff_language)
        ) {
            $language = $staff_language;
        }
    }

    $CI->lang->load($language . '_lang', $language);
    if (file_exists(APPPATH . 'language/' . $language . '/custom_lang.php')) {
        $CI->lang->load('custom_lang', $language);
    }

    $GLOBALS['language'] = $language;
    $GLOBALS['locale']   = get_locale_key($language);

    hooks()->do_action('after_load_admin_language', $language);

    return $language;
}


/**
 * Return admin URI
 * CUSTOM_ADMIN_URL is not yet tested well, don't define it
 * @return string
 */
function get_admin_uri()
{
    return ADMIN_URI;
}

/**
 * @since  1.0.0
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{
    /**
     * Checking for current user?
     */
    if (!is_numeric($staffid)) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->admin === '1';
        }
        $staffid = get_staff_user_id();
    }

    $CI = &get_instance();
    $CI->db->select('1')
        ->where('admin', 1)
        ->where('staffid', $staffid);

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}

function admin_body_class($class = '')
{
    echo 'class="' . join(' ', get_admin_body_class($class)) . '"';
}

function get_admin_body_class($class = '')
{
    $classes   = [];
    $classes[] = 'app';
    $classes[] = 'admin';
    $classes[] = $class;

    $ci = &get_instance();

    $first_segment  = $ci->uri->segment(1);
    $second_segment = $ci->uri->segment(2);
    $third_segment  = $ci->uri->segment(3);

    $classes[] = $first_segment;

    // Not valid eq users/1 - ID
    if ($second_segment != '' && !is_numeric($second_segment)) {
        $classes[] = $second_segment;
    }

    // Not valid eq users/edit/1 - ID
    if ($third_segment != '' && !is_numeric($third_segment)) {
        $classes[] = $third_segment;
    }

    if (is_staff_logged_in()) {
        $classes[] = 'user-id-' . get_staff_user_id();
    }

    $classes[] = strtolower($ci->agent->browser());

    if (is_mobile()) {
        $classes[] = 'mobile';
        $classes[] = 'hide-sidebar';
    }

    if (is_rtl()) {
        $classes[] = 'rtl';
    }

    $classes = hooks()->apply_filters('admin_body_class', $classes);

    return array_unique($classes);
}


/**
 * Feature that will render all JS necessary data in admin head
 * @return void
 */
function render_admin_js_variables()
{
    $date_format   = get_option('dateformat');
    $date_format   = explode('|', $date_format);
    $maxUploadSize = file_upload_max_size();
    $date_format   = $date_format[0];
    $CI            = &get_instance();

    $options = [
        'date_format'                                 => $date_format,
        'decimal_places'                              => get_decimal_places(),
        'scroll_responsive_tables'                    => get_option('scroll_responsive_tables'),
        'company_is_required'                         => get_option('company_is_required'),
        'default_view_calendar'                       => get_option('default_view_calendar'),
        'calendar_events_limit'                       => get_option('calendar_events_limit'),
        'tables_pagination_limit'                     => get_option('tables_pagination_limit'),
        'time_format'                                 => get_option('time_format'),
        'decimal_separator'                           => get_option('decimal_separator'),
        'thousand_separator'                          => get_option('thousand_separator'),
        'timezone'                                    => get_option('default_timezone'),
        'calendar_first_day'                          => get_option('calendar_first_day'),
        'allowed_files'                               => get_option('allowed_files'),
        'desktop_notifications'                       => get_option('desktop_notifications'),
        'show_table_export_button'                    => get_option('show_table_export_button'),
        'has_permission_tasks_checklist_items_delete' => has_permission('checklist_templates', '', 'delete'),
        'show_setup_menu_item_only_on_hover'          => get_option('show_setup_menu_item_only_on_hover'),
        'newsfeed_maximum_files_upload'               => get_option('newsfeed_maximum_files_upload'),
        'dismiss_desktop_not_after'                   => get_option('auto_dismiss_desktop_notifications_after'),
        'enable_google_picker'                        => get_option('enable_google_picker'),
        'google_client_id'                            => get_option('google_client_id'),
        'google_api'                                  => get_option('google_api_key'),
    ];

    // by remove it means do not prefix it

    $lang = [
        'invoice_task_billable_timers_found'                      => _l('invoice_task_billable_timers_found'),
        'validation_extension_not_allowed'                        => _l('validation_extension_not_allowed'),
        'tag'                                                     => _l('tag'),
        'options'                                                 => _l('options'),
        'no_items_warning'                                        => _l('no_items_warning'),
        'item_forgotten_in_preview'                               => _l('item_forgotten_in_preview'),
        'new_notification'                                        => _l('new_notification'),
        'estimate_number_exists'                                  => _l('estimate_number_exists'),
        'invoice_number_exists'                                   => _l('invoice_number_exists'),
        'confirm_action_prompt'                                   => _l('confirm_action_prompt'),
        'calendar_expand'                                         => _l('calendar_expand'),
        'media_files'                                             => _l('media_files'),
        'credit_note_number_exists'                               => _l('credit_note_number_exists'),
        'item_field_not_formatted'                                => _l('numbers_not_formatted_while_editing'),
        'email_exists'                                            => _l(''),
        'phonenumber_exists'                                      => _l('phonenumber_exists'),
        'website_exists'                                          => _l('website_exists'),
        'company_exists'                                          => _l('company_exists'),
        'filter_by'                                               => _l('filter_by'),
        'you_can_not_upload_any_more_files'                       => _l('you_can_not_upload_any_more_files'),
        'cancel_upload'                                           => _l('cancel_upload'),
        'browser_not_support_drag_and_drop'                       => _l('browser_not_support_drag_and_drop'),
        'drop_files_here_to_upload'                               => _l('drop_files_here_to_upload'),
        'file_exceeds_max_filesize'                               => _l('file_exceeds_max_filesize') . ' (' . bytesToSize('', $maxUploadSize) . ')',
        'file_exceeds_maxfile_size_in_form'                       => _l('file_exceeds_maxfile_size_in_form') . ' (' . bytesToSize('', $maxUploadSize) . ')',
        'unit'                                                    => _l('unit'),
        'dt_length_menu_all'                                      => _l('dt_length_menu_all'),
        'dt_button_reload'                                        => _l('dt_button_reload'),
        'dt_button_excel'                                         => _l('dt_button_excel'),
        'dt_button_csv'                                           => _l('dt_button_csv'),
        'dt_button_pdf'                                           => _l('dt_button_pdf'),
        'dt_button_print'                                         => _l('dt_button_print'),
        'dt_button_export'                                        => _l('dt_button_export'),
        'search_ajax_empty'                                       => _l('search_ajax_empty'),
        'search_ajax_initialized'                                 => _l('search_ajax_initialized'),
        'search_ajax_searching'                                   => _l('search_ajax_searching'),
        'not_results_found'                                       => _l('not_results_found'),
        'search_ajax_placeholder'                                 => _l('search_ajax_placeholder'),
        'currently_selected'                                      => _l('currently_selected'),
        'task_stop_timer'                                         => _l('task_stop_timer'),
        'dt_button_column_visibility'                             => _l('dt_button_column_visibility'),
        'note'                                                    => _l('note'),
        'search_tasks'                                            => _l('search_tasks'),
        'confirm'                                                 => _l('confirm'),
        'showing_billable_tasks_from_project'                     => _l('showing_billable_tasks_from_project'),
        'invoice_task_item_project_tasks_not_included'            => _l('invoice_task_item_project_tasks_not_included'),
        'credit_amount_bigger_then_invoice_balance'               => _l('credit_amount_bigger_then_invoice_balance'),
        'credit_amount_bigger_then_credit_note_remaining_credits' => _l('credit_amount_bigger_then_credit_note_remaining_credits'),
        'save'                                                    => _l('save'),
        'expense'                                                 => _l('expense'),
        'ticket'                                                  => _l('ticket'),
        'lead'                                                    => _l('lead'),
        'create_reminder'                                         => _l('create_reminder'),
    ];

    echo '<script>';

    echo 'var site_url = "' . site_url() . '";';
    echo 'var admin_url = "' . admin_url() . '";';

    echo 'var app = {};';
    echo 'var app = {};';

    echo 'app.available_tags = ' . json_encode(get_tags_clean()) . ';';
    echo 'app.available_tags_ids = ' . json_encode(get_tags_ids()) . ';';
    echo 'app.user_recent_searches = ' . json_encode(get_staff_recent_search_history()) . ';';
    echo 'app.months_json = ' . json_encode([_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')]) . ';';
    echo 'app.tinymce_lang = "' . get_tinymce_language($GLOBALS['locale']) . '";';
    echo 'app.locale = "' . $GLOBALS['locale'] . '";';
    echo 'app.browser = "' . strtolower($CI->agent->browser()) . '";';
    echo 'app.user_language = "' . get_staff_default_language() . '";';
    echo 'app.is_mobile = "' . is_mobile() . '";';
    echo 'app.user_is_staff_member = "' . is_staff_member() . '";';
    echo 'app.user_is_admin = "' . is_admin() . '";';
    echo 'app.max_php_ini_upload_size_bytes = "' . $maxUploadSize . '";';
    echo 'app.calendarIDs = "";';

    echo 'app.options = {};';
    echo 'app.lang = {};';

    foreach ($options as $var => $val) {
        echo 'app.options.' . $var . ' = "' . $val . '";';
    }

    foreach ($lang as $key => $val) {
        echo 'app.lang. ' . $key . ' = "' . $val . '";';
    }

    echo 'app.lang.datatables = ' . json_encode(get_datatables_language_array()) . ';';

    /**
     * @deprecated 2.3.2
     */

    $deprecated = [
        'app_language'                                => get_staff_default_language(), // done, prefix it
        'app_is_mobile'                               => is_mobile(), // done, prefix it
        'app_user_browser'                            => strtolower($CI->agent->browser()), // done, prefix it
        'app_date_format'                             => $date_format, // done, prefix it
        'app_decimal_places'                          => get_decimal_places(), // done, prefix it
        'app_scroll_responsive_tables'                => get_option('scroll_responsive_tables'), // done, prefix it
        'app_company_is_required'                     => get_option('company_is_required'), // done, prefix it
        'app_default_view_calendar'                   => get_option('default_view_calendar'), // done, prefix it
        'app_calendar_events_limit'                   => get_option('calendar_events_limit'), // done, prefix it
        'app_tables_pagination_limit'                 => get_option('tables_pagination_limit'), // done, prefix it
        'app_time_format'                             => get_option('time_format'), // done, prefix it
        'app_decimal_separator'                       => get_option('decimal_separator'), // done, prefix it
        'app_thousand_separator'                      => get_option('thousand_separator'), // done, prefix it
        'app_timezone'                                => get_option('default_timezone'), // done, prefix it
        'app_calendar_first_day'                      => get_option('calendar_first_day'), // done, prefix it
        'app_allowed_files'                           => get_option('allowed_files'), // done, prefix it
        'app_desktop_notifications'                   => get_option('desktop_notifications'), // done, prefix it
        'max_php_ini_upload_size_bytes'               => $maxUploadSize, // done, dont do nothing
        'app_show_table_export_button'                => get_option('show_table_export_button'), // done, dont to nothing
        'calendarIDs'                                 => '', // done, dont do nothing
        'is_admin'                                    => is_admin(), // done, dont do nothing
        'is_staff_member'                             => is_staff_member(), // done, dont do nothing
        'has_permission_tasks_checklist_items_delete' => has_permission('checklist_templates', '', 'delete'), // done, dont do nothing
        'app_show_setup_menu_item_only_on_hover'      => get_option('show_setup_menu_item_only_on_hover'), // done, dont to nothing
        'app_newsfeed_maximum_files_upload'           => get_option('newsfeed_maximum_files_upload'), // done, dont to nothing
        'app_dismiss_desktop_not_after'               => get_option('auto_dismiss_desktop_notifications_after'), // done, dont to nothing
        'app_enable_google_picker'                    => get_option('enable_google_picker'), // done, dont to nothing
        'app_google_client_id'                        => get_option('google_client_id'), // done, dont to nothing
        'google_api'                                  => get_option('google_api_key'), // done, dont do nothing
    ];

    $firstKey = key($deprecated);

    $vars = 'var ' . $firstKey . '="' . $deprecated[$firstKey] . '",';

    unset($deprecated[$firstKey]);

    foreach ($deprecated as $var => $val) {
        $vars .= $var . '="' . $val . '",';
    }

    echo rtrim($vars, ',') . ';';

    echo 'var appLang = {};';
    foreach ($lang as $key => $val) {
        echo 'appLang["' . $key . '"] = "' . $val . '";';
    }

    echo '</script>';
}

function _maybe_system_setup_warnings()
{
    if (!defined('DISABLE_APP_SYSTEM_HELP_MESSAGES') || (defined('DISABLE_APP_SYSTEM_HELP_MESSAGES') && DISABLE_APP_SYSTEM_HELP_MESSAGES)) {
        hooks()->add_action('ticket_created', [new PopupMessage('app\services\messages\FirstTicketCreated'), 'check']);
        hooks()->add_action('lead_created', [new PopupMessage('app\services\messages\FirstLeadCreated'), 'check']);
        hooks()->add_action('new_tag_created', [new PopupMessage('app\services\messages\FirstTagCreated'), 'check']);
        hooks()->add_action('task_timer_started', [new PopupMessage('app\services\messages\StartTimersWithNoTasks'), 'check']);
        hooks()->add_action('task_checklist_item_created', [new PopupMessage('app\services\messages\ReOrderTaskChecklistItems'), 'check']);
        hooks()->add_action('smtp_test_email_success', [new PopupMessage('app\services\messages\MailConfigured'), 'check']);
    }

    // Check for just updates message
    hooks()->add_action('before_start_render_dashboard_content', '_maybe_show_just_updated_message');
    // Check whether mod_security is enabled
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\ModSecurityEnabled'), 'check']);
    // Check if there is index.html file in the root crm directory, on some servers if this file exists eq default server index.html file the authentication/login page may not work properly
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\StaticIndexHtml'), 'check']);
    // Show development message
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\DevelopmentEnvironment'), 'check']);
    // Check if cron is required to be configured for some features
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\IsCronSetupRequired'), 'check']);
    // Base url check for https
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\IsBaseUrlChangeRequired'), 'check']);
    // Check if timezone is set
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\Timezone'), 'check']);
    // Notice for cloudflare rocket loader
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\CloudFlare'), 'check']);
    // Php version notice, version 2.4.1
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\PhpVersionNotice'), 'check']);
    // Notice for iconv extension
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\Iconv'), 'check']);
    // Check if there is dot in database name, causing problem on upgrade
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\DatabaseNameHasDot'), 'check']);
    // Some hosting providers cast this file as a malicious and may be deleted
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\TcpdfFileMissing'), 'check']);
    // Check for cron job running
    hooks()->add_action('before_start_render_dashboard_content', [new Message('app\services\messages\CronJobFailure'), 'check']);
}

function pre($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    exit;
}

function recurse_copy_evidence($src, $dst) {  
    // open the source directory 
    $dir = opendir($src);  
    // Make the destination directory if not exist 
    @mkdir($dst);  
    // Loop through the files in source directory 
    foreach (scandir($src) as $file) {  
        if (( $file != '.' ) && ( $file != '..' )) {  
            if ( is_dir($src . '/' . $file) )  
            {  
                // Recursively calling custom copy function 
                // for sub directory  
                recurse_copy_evidence($src . '/' . $file, $dst . '/' . $file);  
            }  
            else {  
                copy($src . '/' . $file, $dst . '/' . $file);  
            }  
        }  
    }  
    closedir($dir); 
} 

function get_child_task_details($taskId){
    $task_details = array();
    if (!empty($taskId)) {
        $CI = &get_instance();
        $CI->db->where('milestone',$taskId);
        $task_details = $CI->db->get(db_prefix() . 'tasks')->row();
    }
    return $task_details;    
}

function getProjectAssignedUser($projectId){
    $staff_id = 0;
    if (!empty($projectId)) {
        $CI = &get_instance();
        $CI->db->select('staff_id')
            ->where('project_id', $projectId)
            ->where('assigned', 1)
            ->where('active', 1)
            ->order_by('id', 'DESC');
        $rows = $CI->db->get(db_prefix() . 'project_members')->row();
        if(!empty($rows)){
            $staff_id = $rows->staff_id;
        }
    }
    return $staff_id;    
}

function get_staff_assistance($staff_id){
    $assistant_id='';
    if (!empty($staff_id)) {
        $CI = &get_instance();
        $CI->db->select('assistant_id')
        ->where('staff_id',$staff_id);
        $rows = $CI->db->get(db_prefix() . 'staff_assistance')->row();
        if(!empty($rows)){
            $assistant_id = $rows->assistant_id;
        }
    }
    return $assistant_id;  
}

function dateDiffInDays($date1, $date2)  
{ 
    // Calculating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1); 
      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
} 

function getReminderOne($task_days){
    $reminderOneDays = 0;
    if($task_days <=10){
        $reminderOneDays = 0;
    }else{
        $reminderOneDays = floor(0.5*$task_days);
    }
    return $reminderOneDays;
}

function getReminderTwo($task_days){
    $reminderTwoDays = 0;
    if($task_days <=10){
        $reminderTwoDays = 0;
    }else{
        $reminderTwoDays = floor(0.8*$task_days);
    }
    return $reminderTwoDays;
}

function get_area_logo(){
    $path = 'uploads/logo/';
    if(!empty($GLOBALS['current_user']->area) && $GLOBALS['current_user']->area != 0){
        $CI = &get_instance();
        $query = $CI->db->query('SELECT `logo` FROM ' . db_prefix() . 'area  WHERE areaid = ' . $GLOBALS['current_user']->area )->row();
        $logo =  $query->logo ;
    }else{
        $logo = "";
    }
    if(!empty($logo)){
        $logo_url = $path.$logo;
    }else{
        // $logo_url = 'assets/images/dpcc-logo.jpg';     
        $logo_url = '';
    } 
    return $logo_url;
}

// company_logo_dark
// company_logo
function get_logo($type){
    $path = 'uploads/company/';
    if(!empty($GLOBALS['current_user']->area) && $GLOBALS['current_user']->area != 0){
        $CI = &get_instance();
        $query = $CI->db->query('SELECT `value` FROM ' . db_prefix() . 'options  WHERE name = "' . $type.'"')->row();
        $logo =  $query->value ;
    }else{
        $logo = "";
    }
    if(!empty($logo)){
        $logo_url = $path.$logo;
    }else{
        $logo_url = '';
    } 
    return $logo_url;
}
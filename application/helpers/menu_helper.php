<?php

defined('BASEPATH') or exit('No direct script access allowed');

function app_init_admin_sidebar_menu_items()
{
    $role = $_SESSION['staff_role'];
    $role_slug =  $GLOBALS['current_user']->role_slug_url;

   
    $file = '';
    $CI = &get_instance();
    if($GLOBALS['current_user']->area != 0){
        $query = $CI->db->query('SELECT `file` FROM ' . db_prefix() . 'area  WHERE areaid = ' . $GLOBALS['current_user']->area )->row();
        $file =  $query->file ;
    }
    if($role_slug == 'ae-global' || $role_slug == 'ae-area' || $role_slug == 'ar' || $role_slug == 'at' || $role_slug == 'ata'){
        $CI->app_menu->add_sidebar_menu_item('dashboard', [
            'name'     => _l('als_dashboard'),
            'href'     => admin_url(),
            'position' => 1,
            'icon'     => 'fa fa-home',
        ]);
    }

    if($role_slug == 'aa'){
        $CI->app_menu->add_sidebar_menu_item('dashboard', [
            'name'     => _l('Checklist'),
            'href'     => admin_url(),
            'position' => 1,
            'icon'     => 'fa fa-home',
        ]);
    }

    $master_url =    '#';     
    if($role_slug == 'ae-global' || $role_slug == 'ap-sa'){
        $master_url = admin_url('masterplan');
    }  
    if(!empty($file)){
        $file_url = base_url('uploads/area/'.$file);
    }else{
        $file_url =    '';     
    }  
  
    // Master Plan

    if($role_slug == 'ae-global' ){
        $CI->app_menu->add_sidebar_menu_item('master_plan_gm', [
            'name'     => _l('City Action Plan'), 
            'href'     => $master_url,
            'icon'     => 'fa fa-file-pdf-o',
            'position' => 60,
        ]);
    }

    if(( ($role_slug == 'at' || $role_slug == 'ar' || $role_slug == 'ae-area' || $role_slug == 'ata') && $file_url != '') || /*($role_slug == 'ae-global' && $file_url == '')|| ($role_slug == 'ap-sa' && $file_url == '') ||*/ ($role_slug == 'aa') ){
   
        $CI->app_menu->add_sidebar_menu_item('master_plan', [
            'name'     => _l('City Action Plan'), 
            'href'     => $master_url,
            'icon'     => 'fa fa-file-pdf-o',
            'position' => 60,
        ]);
        if( ($role_slug == 'aa') &&  $file_url != '' ){   
            $CI->app_menu->add_sidebar_children_item('master_plan', [
                'slug'     => 'view_master_plan',
                'name'     => _l('View City Action Plan'),
                'href'     => $file_url,
                'target'     => '_blank',
                'position' => 26,
            ]);
        }
        if( ($role_slug == 'at' || $role_slug == 'ar' || $role_slug == 'ae-area' || $role_slug == 'ata' ) &&  $file_url != '' ){   
            $CI->app_menu->add_sidebar_menu_item('master_plan', [
                'slug'     => 'view_master_plan',
                'name'     => _l('City Action Plan'),
                'href'     => $file_url,
                'target'   => '_blank',
                'icon'     => 'fa fa-file-pdf-o',
                'position' => 26,
            ]);
        }
        if( $role_slug == 'aa' ){    
            $CI->app_menu->add_sidebar_children_item('master_plan', [
                'slug'     => 'edit_master_plan',
                'name'     => _l('Edit City Action Plan'),
                'href'     => admin_url('masterplan/edit'),
                'position' => 26,
            ]);
        }
        
    }


    if (has_permission('area', '', 'view')) {
          $CI->app_menu->add_sidebar_menu_item('area', [
              'slug'     => 'area',
              'name'     => 'Manage States',
              'icon'     => 'fa fa-globe',
              'href'     => admin_url('area'),
              'position' => 2,
          ]);
    }
    if (has_permission('area', '', 'view')) {
    $CI->app_menu->add_sidebar_menu_item('exception', [
        'slug'     => 'exception',
        'name'     => 'Manage Exceptions',
        'icon'     => 'fa fa-list',
        'href'     => admin_url('exceptions'),
        'position' => 2,
    ]);
    }
    if (has_permission('categories', '', 'view') || has_permission('categories', '', 'view_own')) {
          $CI->app_menu->add_sidebar_menu_item('categories', [
                  'slug'     => 'categories',
                  'name'     => _l('category_superadmin'),
                  'icon'     => 'fa fa-list-alt',
                  'href'     => admin_url('issues/'),
                  'position' => 3,
           ]);

           if (has_permission('categories_manage', '', 'view')) {
            //    $CI->app_menu->add_sidebar_children_item('categories', [
            //        'slug'     => 'issues',
            //        'name'     => _l('Category Master'),
            //        'href'     => admin_url('issues'),
            //        'position' => 1,
            //    ]);

            //    $CI->app_menu->add_sidebar_children_item('categories', [
            //        'slug'     => 'issues_manage',
            //        'name'     => _l('Manage Categories'),
            //        'href'     => admin_url('issues_manage'),
            //        'position' => 2,
            //    ]);
            $CI->app_menu->add_sidebar_menu_item('categories', [
                'slug'     => 'issues',
                'name'     => _l('category_areaadmin'),
                'icon'     => 'fa fa-list-alt',
                'href'     => admin_url('issues_manage/'),
                'position' => 3,
         ]);
           }

      }

    // $CI->app_menu->add_sidebar_menu_item('admin-mgmt', [
    //     'name'     => _l('area admin'),
    //     'href'     => admin_url('staff'),
    //     'position' => 4,
    //     'icon'     => 'fa fa-user',
    // ]);
    $position = 6;
    if($role_slug == 'aa'){
        $position = 45;
    }

  if (has_permission('staff', '', 'view')) {
      $CI->app_menu->add_sidebar_menu_item('manage-user', [
                  'slug'     => 'media',
                  'name'     => _l('manage users'),
                  'href'     => admin_url('#'),
                  'position' => $position,
                  'icon'     => 'fa fa-user',
      ]);
      if (has_permission('staff_aa', '', 'view')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'slug'     => 'area-admin',
              'name'     => _l('State Admin'),
              'href'     => admin_url('staff'),
              'position' => 1,
          ]);
      }
      if (has_permission('staff_ae-global', '', 'view')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'name'     => _l('National Observer'),
              'slug'     => 'ae-god-mode',
              'href'     => admin_url('staff?role=ae-global'),
              'position' => 2,
          ]);
      }
      if (has_permission('staff_ae-area', '', 'view')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'name'     => _l('State Observer'),
              'slug'     => 'ae-area',
              'href'     => admin_url('staff?role=ae-area'),
              'position' => 2,
          ]);
      }
      if (has_permission('staff_ar', '', 'view_own')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'name'     => _l('Reviewer'),
              'slug'     => 'ar',
              'href'     => admin_url('staff?role=ar'),
              'position' => 3,
          ]);
      }
      if (has_permission('staff_at', '', 'view_own')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'name'     => _l('Project Leader'),
              'slug'     => 'at',
              'href'     => admin_url('staff?role=at'),
              'position' => 4,
          ]);
      }
      if (has_permission('staff_ata', '', 'view_own')) {
          $CI->app_menu->add_sidebar_children_item('manage-user', [
              'name'     => _l('Project Support'),
              'slug'     => 'ata',
              'href'     => admin_url('staff?role=ata'),
              'position' => 5,
          ]);
      }

  }

    // if (has_permission('customers', '', 'view')
    //     || (have_assigned_customers()
    //     || (!have_assigned_customers() && has_permission('customers', '', 'create')))) {
    //     $CI->app_menu->add_sidebar_menu_item('customers', [
    //         'name'     => _l('als_clients'),
    //         'href'     => admin_url('clients'),
    //         'position' => 5,
    //         'icon'     => 'fa fa-user-o',
    //     ]);
    // }

    if (has_permission('subscriptions', '', 'view') || has_permission('subscriptions', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('subscriptions', [
            'name'     => _l('subscriptions'),
            'href'     => admin_url('subscriptions'),
            'icon'     => 'fa fa-repeat',
            'position' => 15,
        ]);
    }

    // $CI->app_menu->add_sidebar_menu_item('projects', [
    //     'name'     => _l('projects'),
    //     'href'     => admin_url('projects'),
    //     'icon'     => 'fa fa-bars',
    //     'position' => 70,
    // ]);

    // $CI->app_menu->add_sidebar_menu_item('tasks', [
    //             'name'     => _l('als_tasks'),
    //             'href'     => admin_url('tasks'),
    //             'icon'     => 'fa fa-tasks',
    //             'position' => 35,
    //     ]);



        if (is_admin()) {
            $CI->app_menu->add_sidebar_menu_item('area', [
                'slug'     => 'area',
                'name'     => 'Manage Area',
                'icon'     => 'fa fa-tasks',
                'href'     => admin_url('area'),
                'position' => 35,
            ]);




        }
        if (!is_admin()) {
            if (has_permission('region', '', 'view')) {
            $CI->app_menu->add_sidebar_menu_item('region', [
                'slug'     => 'region',
                'name'     => 'City/ Corporation',
                'icon'     => 'fa fa-map-marker',
                'href'     => admin_url('region'),
                'position' => 35,
            ]);
            }
            if (has_permission('subregion', '', 'view')) {
            $CI->app_menu->add_sidebar_menu_item('subregion', [
                'slug'     => 'subregion',
                'name'     => 'Municipal Zone',
                'icon'     => 'fa fa-map-marker',
                'href'     => admin_url('subregion'),
                'position' => 35,
            ]);
            }
        }


    // Utilities
    // $CI->app_menu->add_sidebar_menu_item('utilities', [
    //         'collapse' => true,
    //         'name'     => _l('als_utilities'),
    //         'position' => 55,
    //         'icon'     => 'fa fa-cogs',
    //     ]);

    /*$CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'media',
                'name'     => _l('als_media'),
                'href'     => admin_url('utilities/media'),
                'position' => 5,
        ]);

    if (has_permission('bulk_pdf_exporter', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'bulk-pdf-exporter',
                'name'     => _l('bulk_pdf_exporter'),
                'href'     => admin_url('utilities/bulk_pdf_exporter'),
                'position' => 10,
        ]);
    }
    */
    $CI->app_menu->add_sidebar_children_item('utilities', [
        'slug'     => 'calendar',
        'name'     => _l('als_calendar_submenu'),
        'href'     => admin_url('utilities/calendar'),
        'position' => 15,
    ]);


    if (is_admin()) {
        /*  $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'announcements',
                'name'     => _l('als_announcements_submenu'),
                'href'     => admin_url('announcements'),
                'position' => 20,
        ]);
        */

        $CI->app_menu->add_sidebar_children_item('utilities', [
            'slug'     => 'activity-log',
            'name'     => _l('als_activity_log_submenu'),
            'href'     => admin_url('utilities/activity_log'),
            'position' => 25,
        ]);
        /*
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'ticket-pipe-log',
                'name'     => _l('ticket_pipe_log'),
                'href'     => admin_url('utilities/pipe_log'),
                'position' => 30,
        ]); */
    }

    $CI->app_menu->add_sidebar_menu_item('edit-profile', [
        'slug'     => 'edit-profile',
        'name'     => 'My Profile',
        'icon'     => 'fa fa-user-circle-o',
        'href'     => admin_url('staff/edit_profile'),
        'position' => 80,
    ]);

    // if (has_permission('reports', '', 'view')) {
    //     $CI->app_menu->add_sidebar_menu_item('reports', [
    //         // 'collapse' => true,
    //         'name'     => _l('als_reports'),
    //         // 'href'     => admin_url('reports'),
    //         'href'     => admin_url('#'),
    //         'icon'     => 'fa fa-area-chart',
    //         'position' => 60,
    //     ]);


        // if (is_admin()) {
        //     $CI->app_menu->add_sidebar_children_item('reports', [
        //             'slug'     => 'timesheets-reports',
        //             'name'     => _l('timesheets_overview'),
        //             'href'     => admin_url('staff/timesheets?view=all'),
        //             'position' => 25,
        //     ]);
        // }


    //}
    $report_mag_url =    admin_url('#');     
    // if($role == 3){
    //     $report_mag_url = admin_url('report?role=at');
    // }else if($role == 4){
    //     $report_mag_url = admin_url('report?role=ar');
    // }else if($role == 7){
    //     $report_mag_url = admin_url('report?role=ae-area');
    // }else if($role == 8){
    //     $report_mag_url = admin_url('report?role=ata');
    // }else if($role == 6){
    //     $report_mag_url = admin_url('report?role=aa');
    // }else if($role == 5){
    //     $report_mag_url = admin_url('report');
    // }
    if($role == 3 || $role == 4 || $role == 7 || $role == 8 || $role == 6 || $role == 5){
        $report_mag_url = admin_url('report');
        $name = 'ATR';
    }else{
        $name = 'REPORTS';
    }

    $CI->app_menu->add_sidebar_menu_item('report', [
        'name'     => $name, 
        'href'     => $report_mag_url,
        'icon'     => 'fa fa-area-chart',
        'position' => 60,
    ]);
      // Super admin report   
    if (has_permission('area', '', 'view')) {        

        $CI->app_menu->add_sidebar_menu_item('report', [
            'slug'     => 'region-list',
            'name'     => _l('region-list'),
            'href'     => admin_url('report/region'),
            'icon'     => 'fa fa-location-arrow',
            'position' => 26,
        ]);
    }



    // Setup menu
    if (has_permission('staff', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('staff', [
            'name'     => _l('als_staff'),
            'href'     => admin_url('staff'),
            'position' => 5,
        ]);
    }

  if (is_admin()) {
        $CI->app_menu->add_setup_menu_item('customers', [
                    'collapse' => true,
                    'name'     => _l('clients'),
                    'position' => 10,
            ]);

        $CI->app_menu->add_setup_children_item('customers', [
                    'slug'     => 'customer-groups',
                    'name'     => _l('customer_groups'),
                    'href'     => admin_url('clients/groups'),
                    'position' => 5,
            ]);
    }
    $CI->app_menu->add_setup_menu_item('support', [
                'collapse' => true,
                'name'     => _l('master'),
                'position' => 15,
        ]);


    if (is_admin()) {
    // if (is_admin()) {
    //     $CI->app_menu->add_setup_menu_item('customers', [
    //         'collapse' => true,
    //         'name'     => _l('clients'),
    //         'position' => 10,
    //     ]);

    //     $CI->app_menu->add_setup_children_item('customers', [
    //         'slug'     => 'customer-groups',
    //         'name'     => _l('customer_groups'),
    //         'href'     => admin_url('clients/groups'),
    //         'position' => 5,
    //     ]);
    // }
    $CI->app_menu->add_setup_menu_item('support', [
        'collapse' => true,
        'name'     => _l('master'),
        'position' => 15,
    ]);
  }
    if (is_admin()) {
        // $CI->app_menu->add_setup_children_item('support', [
        //             'slug'     => 'departments',
        //             'name'     => _l('acs_departments'),
        //             'href'     => admin_url('departments'),
        //             'position' => 5,
        //     ]);

        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'area',
            'name'     => 'Area',
            'href'     => admin_url('area'),
            'position' => 5,
        ]);
    }

    if (has_permission('region', '', 'view')) {
        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'region',
            'name'     => 'Region',
            'href'     => admin_url('region'),
            'position' => 5,
        ]);
    }
    if (has_permission('subregion', '', 'view')) {
        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'subregion',
            'name'     => 'Sub-region',
            'href'     => admin_url('subregion'),
            'position' => 5,
        ]);
    }
    if (has_permission('issues', '', 'view')) {
        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'issues',
            'name'     => _l('issues'),
            'href'     => admin_url('issues'),
            'position' => 15,
        ]);
    }

    if (is_admin()) {
        // $CI->app_menu->add_setup_children_item('support', [
        //     'slug'     => 'tickets-priorities',
        //     'name'     => _l('acs_ticket_priority_submenu'),
        //     'href'     => admin_url('tickets/priorities'),
        //     'position' => 15,
        // ]);
        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'tickets-status',
            'name'     => _l('acs_ticket_statuses_submenu'),
            'href'     => admin_url('tickets/statuses'),
            'position' => 20,
        ]);

        $CI->app_menu->add_setup_children_item('support', [
            'slug'     => 'tickets-services',
            'name'     => _l('acs_ticket_services_submenu'),
            'href'     => admin_url('tickets/services'),
            'position' => 25,
        ]);




        $CI->app_menu->add_setup_menu_item('custom-fields', [
            'href'     => admin_url('custom_fields'),
            'name'     => _l('asc_custom_fields'),
            'position' => 45,
        ]);


        $CI->app_menu->add_setup_menu_item('roles', [
            'href'     => admin_url('roles'),
            'name'     => _l('acs_roles'),
            'position' => 55,
        ]);

        /*             $CI->app_menu->add_setup_menu_item('api', [
                          'href'     => admin_url('api'),
                          'name'     => 'API',
                          'position' => 65,
                  ]);*/
    }

    if (has_permission('settings', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('settings', [
            'href'     => admin_url('settings'),
            'name'     => _l('acs_settings'),
            'position' => 200,
        ]);
    }

    if (has_permission('email_templates', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('email-templates', [
            'href'     => admin_url('emails'),
            'name'     => _l('acs_email_templates'),
            'position' => 40,
        ]);
    }


}

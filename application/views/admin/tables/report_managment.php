<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    $aColumns = [
        'projects.id',
        //'CONCAT(name, " near ", landmark)',
        'projects.name',
        'projects.project_created',
        'projects.description',

        ];
    $sIndexColumn = 'id';
    $sTable       = db_prefix().'projects';
    $join         = [' LEFT JOIN ' . db_prefix() . 'project_members ON ' . db_prefix() . 'project_members.project_id = ' . db_prefix() . 'projects.id LEFT JOIN ' . db_prefix() . 'staff_assistance ON ' . db_prefix() . 'staff_assistance.staff_id = ' . db_prefix() . 'project_members.staff_id LEFT JOIN ' . db_prefix() . 'task_assigned ON ' . db_prefix() . 'task_assigned.taskid = ' . db_prefix() . 'projects.id',];  
    $where = [];
    $filter = ['1=1'];

    array_push($filter,  ' AND ' . db_prefix() .'project_members.active  = 1');

    if (!empty($role) && $role != 5) {
        array_push($filter,  ' AND ' . db_prefix() .'projects.area_id  = '.$area);
    }

    if (!empty($role) && ($role == 3 || $role == 8)) {
        array_push($filter,  ' AND ' . db_prefix() .'project_members.staff_id  = '.$GLOBALS['current_user']->staffid);
    }

    if (!empty($role) && $role == 4) {
        array_push($filter,  ' AND ' . db_prefix() .'staff_assistance.assistant_id  = '.$GLOBALS['current_user']->staffid);
    }


    if (!empty($areaid) && count($areaid) > 0) {
        array_push($filter, ' AND ' . db_prefix() .'projects.area_id  IN (' . implode(', ', $areaid) . ')');
    }

    if (!empty($region) && count($region) > 0) {
        array_push($filter, ' AND ' . db_prefix() .'projects.region_id  IN (' . implode(', ', $region) . ')');
    }

    if (!empty($subregion) && count($subregion) > 0) {
        array_push($filter, ' AND ' . db_prefix() .'projects.subregion_id  IN (' . implode(', ', $subregion) . ')');
    }

    if (empty($statusIds) ) {
        array_push($filter,  ' AND ' . db_prefix() .'projects.status NOT IN (3)');
    }


    // if (empty($statusIds) && $role != 3) {
    //     array_push($filter,  ' AND ' . db_prefix() .'projects.status IN (1,2,5,6)');
    // }else if (empty($statusIds) && $role == 3){
    //     array_push($filter,  ' AND ' . db_prefix() .'task_assigned.status IN (1,2,5,6)');
    // }

    if (!empty($category) && count($category) > 0) {
        array_push($filter,  ' AND ' . db_prefix() .'projects.issue_id IN (' . implode(', ', $category) . ')');
    }

    if (!empty($action_taker) && count($action_taker) > 0) {
        array_push($filter,  ' AND ' . db_prefix() .'project_members.staff_id IN (' . implode(', ', $action_taker) . ')');
    }

    if (!empty($action_reviewer) && count($action_reviewer) > 0) {
        array_push($where, ' AND ' . db_prefix() . 'staff_assistance.assistant_id IN (' . implode(', ', $action_reviewer) . ')');
    }

    if (!empty($bug) ) {
        array_push($filter,  ' AND ' . db_prefix() .'projects.id  = '.$bug);
    }
   // echo $report_date; exit;
    if (!empty($report_date) && $report_date != 'custom' ) {
       
        if($report_date == 'this_month'){
            array_push($filter,  ' AND MONTH(' . db_prefix() .'projects.action_date ) = '.date('m'));
        }else if($report_date == 'last_month'){
            array_push($filter,  ' AND MONTH(' . db_prefix() .'projects.action_date ) = '.date('m',strtotime('-1 month')));
        }else if($report_date == 'this_year'){
            array_push($filter,  ' AND YEAR(' . db_prefix() .'projects.action_date )  = '.date('Y'));
        }else if($report_date == 'last_year'){
            array_push($filter,  ' AND YEAR(' . db_prefix() .'projects.action_date )  = '.date('Y',strtotime('-1 year')));
        }else if($report_date == '3'){
            array_push($filter,  ' AND DATE(' . db_prefix() .'projects.action_date )  >= "'.date('Y-m-01', strtotime('-2 MONTH')).'" AND DATE(' . db_prefix() .'projects.action_date ) <= "'.date('Y-m-t').'"');
        }else if($report_date == '6'){
            array_push($filter,  ' AND DATE(' . db_prefix() .'projects.action_date ) >= "'.date('Y-m-01', strtotime('-5 MONTH')).'" AND DATE(' . db_prefix() .'projects.action_date ) <= "'.date('Y-m-t').'"');
        }else if($report_date == '12'){
            array_push($filter,  ' AND DATE(' . db_prefix() .'projects.action_date  ) >= "'.date('Y-m-01', strtotime('-11 MONTH')).'" AND DATE(' . db_prefix() .'projects.action_date ) <= "'.date('Y-m-t').'"');
        } 
    }

    if (!empty($report_date) && $report_date == 'custom' && !empty($to_date) && !empty($from_date)) {
        array_push($filter,  ' AND DATE(' . db_prefix() .'projects.action_date )  >= "'.$from_date.'" AND DATE( ' . db_prefix() .'projects.action_date ) <= "'.$to_date.'"');
    }

    if (!empty($statusIds) && count($statusIds) > 0 && $role != 8) {
        array_push($filter,  ' AND ' . db_prefix() .'projects.status IN (' . implode(', ', $statusIds) . ')');
        if(in_array("2", $statusIds)){
            array_push($filter,  ' OR ' . db_prefix() .'projects.status = 4');    
        }
    }

    if (!empty($statusIds) && count($statusIds) > 0 && $role == 8) {
        array_push($filter,  ' AND ' . db_prefix() .'task_assigned.status IN (' . implode(', ', $statusIds) . ')');
    }


    // if (!empty($statusIds) && count($statusIds) > 0 && $role == 3) {
    //     if(in_array("1", $statusIds)){
    //         array_push($filter,  ' AND ' . db_prefix() .'projects.status IN (1)');
    //         array_shift($statusIds);
    //         if(!empty($statusIds) && count($statusIds) > 0){
    //             array_push($filter,  ' OR ' . db_prefix() .'task_assigned.status IN (' . implode(', ', $statusIds) . ')');
    //         }
    //     }else{
    //         if(!empty($statusIds) && count($statusIds) > 0){
    //               array_push($filter,  ' AND ' . db_prefix() .'task_assigned.status IN (' . implode(', ', $statusIds) . ')');
    //         }
          
    //     }

    // }

    if (count($filter) > 0) {
        array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
    }
    
    $aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);
    
  
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['projects.id',db_prefix() . 'staff_assistance.assistant_id, landmark, projects.status'],'GROUP by projects.id');
    $output  = $result['output'];
    //$output['iTotalRecords'] = $output['iTotalDisplayRecords'];
    $rResult = $result['rResult'];
    
    foreach ($rResult as $aRow) {
    
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) 
            {
                $_data = $aRow[$aColumns[$i]];

                if ($aColumns[$i] == 'projects.name') {
                    if($aRow['projects.name'] != NULL){

                        $_data = '<div class="dashboard-cell w20P action-item">
                        <p class="ticket_details" data-project_id="' . $aRow['id'] .'" data-role="'.$GLOBALS['current_user']->role_slug_url.'" data-status="' . $aRow['status'] .'"><strong>'.$aRow['projects.name'].'</strong> <br/><span>near,</span> <strong class="place-name">' . $aRow['landmark'] .'</strong></p>
                        </div>';
  
                    }
                }     
                $row[] = $_data;        
            }
         
          $options = '<div class="d-flex justify-content-center align-flex-end">
                        <p class="evidence_img evidence" data-project_id="' . $aRow['id'] .'">
                            <img src="'. base_url('assets/images/view-icon.png') .'" alt="">
                            <span>View</span></p>
                    </div>';

                    // '<p class="evidence_loc evidence" data-project_id="' . $aRow['id'] .'">
                    // <img src="'. base_url('assets/images/location-icon.png') .'" alt="">
                    // <span>Location</span></p>'

       
            $row[] = $options;

            $output['aaData'][] = $row;
         
    }

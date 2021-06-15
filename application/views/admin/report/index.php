<style>
    .dt-page-jump { display: none;}
    #report-mg_wrapper ul.dt-button-collection.dropdown-menu { display: none !important;  }
    /* .pdf-download, .pdf-download{position: unset !important;} 
    .pdf-download, .pdf-download:hover{position: unset !important;} */

</style>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
ini_set('error_reporting', ~E_NOTICE );
$area = $GLOBALS['current_user']->area;
$role = $GLOBALS['current_user']->role;

if ($role == 3 || $role == 4 || $role == 7 || $role == 8 || $role == 6 || $role == 5) {
    $report_mag_url = admin_url('report/index');
}
?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s no-shadow custom-panel1">
                    <div class="panel-body mB20">
                        <div class="panel-header">
                            <h1>Action Taken Report (ATR)<span>Here you can view and download custom reports</span></h1>
                            <hr class="hr-panel-heading" />
                        </div>
                        <!-- <div class="panel-header">
                            <h2>Query Section</h2>
                        </div> -->
                        <span id="filter-id" class="hide"><?php echo $filter; ?></span>
                        <div class="">
                            <div class="1mB20">
                                <?php echo form_open($report_mag_url); ?>
                                <?php if ($role != 5) { ?>
                                    <input type="hidden" name="area" value="<?php echo $area ?>" />
                                <?php } ?>
                               <input type="hidden" name="role" value="<?php echo $role ?>" />
                                <div class="row">
                                    <?php if ($role == 5) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group" app-field-wrapper="area">
                                                <div class="form-select-field">
                                                    <?php
                                                    $selected = array();
                                                    if (isset($tableParams['areaid'])) {
                                                        $selected = $tableParams['areaid'];
                                                    }
                                                  
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('areaid[]', $areas, array('areaid', 'name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => ' States', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                    echo '</div>';
                                                    ?>
                                                  
                                                    <label class="select-label">State</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php   if($role == 4 || $role == 5 || $role == 7 || $role == 6) { 
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="region">
                                            <div class="form-select-field">
                                                <?php
                                              
                                                $selected = array();
                                                if (isset($tableParams['region'])) {
                                                    $selected = $tableParams['region'];
                                                }
                                                //$region
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('region[]', $region , array('id', 'region_name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => ' Cities/ Corporations',  'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>
                                                <label class="select-label">City/ Corporation</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }  if($role == 4 || $role == 5 || $role == 7 || $role == 6) { 
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="subregion">
                                            <div class="form-select-field">
                                                <?php
                                                $selected = array();
                                                if (isset($tableParams['subregion'])) {
                                                    $selected = $tableParams['subregion'];
                                                }
                                                //$subregion
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('subregion[]', $subregion, array('id', 'region_name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => ' Municipal Zones', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>
                                                <label class="select-label">Municipal Zone</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php  } if($role == 4 || $role == 5 || $role == 7 || $role == 6) {
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="action_taker">
                                            <div class="form-select-field">
                                                <?php
                                                $selected = array();
                                                if (isset($tableParams['action_taker'])) {
                                                    $selected = $tableParams['action_taker'];
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('action_taker[]', $action_taker, array('staffid', 'name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => ' Project Leaders', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>
                                                <label class="select-label">Project Leader</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } if($role == 4 || $role == 5 || $role == 7 || $role == 6) { 
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="action_reviewer">
                                            <div class="form-select-field">
                                                <?php
                                                $selected = array();
                                                if (isset($tableParams['action_reviewer'])) {
                                                    $selected = $tableParams['action_reviewer'];
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('action_reviewer[]', $action_reviewer, array('staffid', 'name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => 'Reviewers', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>
                                                <label class="select-label">Reviewer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php  } ?>

                                     <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="categories">
                                            <div class="form-select-field">
                                            <?php
                                                $selected = array();
                                                if (isset($tableParams['duration'])) {
                                                    $selected = $tableParams['duration'];
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('duration[]', $durations, array('id', 'duration'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => 'Duration', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                            ?>
                                                <label class="select-label">Duration</label>
                                            </div>
                                        </div>
                                    </div>           

                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="categories">
                                            <div class="form-select-field">
                                                <?php
                                                $selected = array();
                                                if (isset($tableParams['category'])) {
                                                    $selected = $tableParams['category'];
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('category[]', $categories, array('id', 'issue_name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => 'Action Items', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', 'custom-categories', false);
                                                echo '</div>';
                                                ?>
                                                <label class="select-label">Action Items</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="ticket">
                                            <div class="form-select-field">
                                                <?php
                                                $selected = array();
                                                if (isset($tableParams['statusIds'])) {
                                                    $selected = $tableParams['statusIds'];
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('ticket[]', $ticket, array('id', 'name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => 'Project Status', 'multiple' => true, 'data-actions-box' => true), array(), 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>

                                                <label class="select-label">Project Status</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <div class="form-input-field">
                                                <?php
                                                $cls = '';
                                                if (!empty($tableParams['bug'])) {
                                                    $cls = 'label-up';
                                                }

                                                if (isset($tableParams['bug'])) {
                                                    $selected = $tableParams['bug'];
                                                } ?>
                                                <input type="text" autocomplete="off" name="bug" id="bug_id" value="<?php echo $selected ?>" class="<?php echo $cls ?>">
                                                <label for="bug_id" title="Project ID" data-title="Project ID"></label>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group " id="report-time">
                                            <div class="form-select-field singleSelect">
                                                <?php

                                                if (isset($tableParams['report_date'])) {
                                                    $sel = $tableParams['report_date'];
                                                } ?>
                                                <select class="selectpicker" name="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""><?php echo _l('report_sales_months_all_time'); ?>
                                                    </option>
                                                    <option value="this_month" data-subtext="<?php echo _d(date('Y-m-01')); ?> - <?php echo _d(date('Y-m-t')); ?>" <?php if ($sel == 'this_month') echo "selected"; ?>><?php echo _l('this_month'); ?></option>
                                                    <option value="last_month" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-1 MONTH"))); ?> - <?php echo _d(date('Y-m-t', strtotime("-1 MONTH"))); ?>" <?php if ($sel == 'last_month') echo "selected"; ?>><?php echo _l('last_month'); ?></option>
                                                    <option value="this_year" data-subtext="<?php echo _d(date('Y-01-01')); ?> - <?php echo _d(date('Y-12-31')); ?>" <?php if ($sel == 'this_year') echo "selected"; ?>><?php echo _l('this_year'); ?></option>
                                                    <option value="last_year" data-subtext="<?php echo _d(date('Y-01-01', strtotime("-1 YEAR"))); ?> - <?php echo _d(date('Y-12-31', strtotime("-1 YEAR"))); ?>" <?php if ($sel == 'last_year') echo "selected"; ?>><?php echo _l('last_year'); ?></option>
                                                    <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>" <?php if ($sel == '3') echo "selected"; ?>>
                                                        <?php echo _l('report_sales_months_three_months'); ?></option>
                                                    <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>" <?php if ($sel == '6') echo "selected"; ?>>
                                                        <?php echo _l('report_sales_months_six_months'); ?></option>
                                                    <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>" <?php if ($sel == '12') echo "selected"; ?>>
                                                        <?php echo _l('report_sales_months_twelve_months'); ?></option>
                                                    <option value="custom" <?php if ($sel == 'custom') echo "selected"; ?>><?php echo _l('Custom Period'); ?></option>
                                                </select>
                                                <label class="select-label">Date Range</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div id="date-range" class="row hide mbot15">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-input-field date">
                                                        <?php
                                                        $cls = '';
                                                        if (isset($tableParams['from_date'])) {
                                                            $selected = $tableParams['from_date'];
                                                            $cls = 'label-up';
                                                        } ?>
                                                        <input type="text" class="datepicker <?php echo $cls ?>" id="report-from" name="report-from" value="<?php echo $selected ?>">
                                                        <label for="report-from" class="control-label" title="<?php echo _l('report_sales_from_date'); ?>" data-title="<?php echo _l('report_sales_from_date'); ?>"></label>
                                                        <div class="input-group-addon date-icon">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-input-field date">
                                                        <?php
                                                        $cls = '';
                                                        if (isset($tableParams['to_date'])) {
                                                            $selected = $tableParams['to_date'];
                                                            $cls = 'label-up';
                                                        } ?>
                                                        <input type="text" class="datepicker <?php echo $cls ?>" disabled="disabled" id="report-to" name="report-to" value="<?php echo $selected ?>">
                                                        <label for="report_to" class="control-label" title="<?php echo _l('report_sales_to_date'); ?>" data-title="<?php echo _l('report_sales_to_date'); ?>"></label>
                                                        <div class="input-group-addon date-icon">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" id="search_id" class="btn btn-custom"><?php echo _l('filter'); ?></button>

                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        </div>
                        
                        <div class="panel-body mB20">
                        <div class="panel-header atr-summary">
                            
                            <h1>Summary <a href="#"  class="pdf-download position-relative pull-right" title="Download as PDF" id="export_pdf"> <i class="fa fa-file-pdf-o"></i></a></h1>
                            <?php
                            if (!empty($tableParams['report_date'])) {
                                $summ =   _l('historical');
                            } else{
                                $summ =   _l('as_on_date');;
                            }
                            ?>
                     
                            <h4 class="current-summary"><?php echo $summ; ?></h4>
                            <hr class="hr-panel-heading" />
                           
                        </div>
                        <div class="table-scroll">
                        <table class="table report-summary" id="rep-total">

                            <thead>
                                <th width="70" class="new"><?php echo _l('New'); ?></th>
                                <th width="70" class="escalated"><?php echo _l('Delayed'); ?></th>
                                <th width="100" class="wip"><?php echo _l('In Progress'); ?></th>
                                <th width="70" class="closed"><?php echo _l('Closed'); ?></th>
                                <th width="70" class="rejected"><?php echo _l('Rejected'); ?></th>
                                <th width="120" class="unassigned"><?php echo _l('Unassigned'); ?></th>
                                <th width="70" class="frozen"><?php echo _l('Frozen'); ?></th>
                                <th width="100" class="total-column"><?php echo _l('Total'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($totals as $val) {
                                    //pre($val);
                                    $close =  (!empty($val['close'])) ? $val['close'] : '0';
                                    $escalated =  (!empty($val['escalated'])) ? $val['escalated'] : '0';
                                    $wip =  (!empty($val['wip'])) ? $val['wip'] : '0';
                                    $new =  (!empty($val['new'])) ? $val['new'] : '0';
                                    $reject =  (!empty($val['reject'])) ? $val['reject'] : '0';
                                    $unassign =  (!empty($val['unassign'])) ? $val['unassign'] : '0';
                                    $frozen =  (!empty($val['frozen'])) ? $val['frozen'] : '0';
                                    $total = $close + $escalated + $wip + $new + $reject + $unassign + $frozen;
                                    ?>

                                    <tr>
                                        <td><?php echo $new ?></td>
                                        <td><?php echo $escalated  ?></td>
                                        <td><?php echo $wip ?></td>
                                        <td><?php echo $close  ?></td>
                                        <td><?php echo $reject  ?></td>
                                        <td><?php echo $unassign  ?></td>
                                        <td><?php echo $frozen  ?></td>
                                        <td class="total_bold"><?php echo $total; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            </table>
                        </div>
                            <hr class="hr-panel-heading" />
                        <table class="table dt-table scroll-responsive" id="rep-summ">

                            <thead>
                                <th style="width:25%;"><?php echo _l('Action Items'); ?></th>
                                <th class="dt_center_align"><?php echo _l('New'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Delayed'); ?></th>
                                <th class="dt_center_align"><?php echo _l('In Progress'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Closed'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Rejected'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Unassigned'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Frozen'); ?></th>
                                <th class="dt_center_align"><?php echo _l('Total'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($statuses as $val) {
                                    $close =  (!empty($val['close'])) ? $val['close'] : '0';
                                    $escalated =  (!empty($val['escalated'])) ? $val['escalated'] : '0';
                                    $wip =  (!empty($val['wip'])) ? $val['wip'] : '0';
                                    $new =  (!empty($val['new'])) ? $val['new'] : '0';
                                    $reject =  (!empty($val['reject'])) ? $val['reject'] : '0';
                                    $unassign =  (!empty($val['unassign'])) ? $val['unassign'] : '0';
                                    $frozen =  (!empty($val['frozen'])) ? $val['frozen'] : '0';
                                    $total = $close + $escalated + $wip + $new + $reject + $unassign + $frozen;
                                    ?>

                                    <tr>
                                        <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['name'])) ?  strip_tags($val['name']) : '' ?>"><?php echo (!empty( $val['name'])) ?  mb_strimwidth($val['name'], 0, 40, '...') : '' ?></p></td>
                                        <td class="dt_center_align"><?php echo $new ?></td>
                                        <td class="dt_center_align"><?php echo $escalated  ?></td>
                                        <td class="dt_center_align"><?php echo $wip ?></td>
                                        <td class="dt_center_align"><?php echo $close  ?></td>
                                        <td class="dt_center_align"><?php echo $reject  ?></td>
                                        <td class="dt_center_align"><?php echo $unassign  ?></td>
                                        <td class="dt_center_align"><?php echo $frozen  ?></td>
                                        <td class="total_bold dt_center_align"><?php echo $total; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        </div>

                        <div class="panel-body">
                        <div class="panel-header">
                            <h1>Details</h1>
                            <hr class="hr-panel-heading" />

                        </div>
                        
                                                               
						<table class="table dt-table scroll-responsive table-reportmng table-fixed" data-order-col="7" id="report-mg">
							<thead>
                            <tr> 
								<th width= "80"><?php echo _l('Project ID'); ?></th>
                                <th width= "130"><?php echo _l('Action Items'); ?></th>
                                <th width= "120"><?php echo _l('Status'); ?></th>       
                                <th width= "130"><?php echo _l('Assigned To'); ?></th>                              
                                <th width= "80"><?php echo _l('Contact'); ?></th>
                                <th width= "80"><?php echo _l('Due Date'); ?></th>
                                <th width= "100"><?php echo _l('Municipal Zone'); ?></th>
                                <th width= "100"><?php echo _l('Landmark'); ?></th>
								<th width= "120"><?php echo _l('City/ Corporation '); ?></th>
                                <th width= "80"><?php echo _l('State'); ?></th>
                                <th width= "80" id="raised"><?php echo _l('Raised On'); ?></th>
                                <th width= "210"><?php echo _l('Raised Comment'); ?></th>  
                                <th width= "120"><?php echo _l('Raised Evidence'); ?></th>
                                <th width= "120"><?php echo _l('Raised Location'); ?></th>
                                <th width= "120"><?php echo _l('Latest Comment'); ?></th>
                                <th width= "120"><?php echo _l('Latest Evidence'); ?></th>
                                <th width= "120"><?php echo _l('Latest Location'); ?></th>
                                <th width= "100"><?php echo _l('Role'); ?></th>
                                <th width= "120"><?php echo _l('Email ID'); ?></th>
                                <th width= "120"><?php echo _l('Project Leader'); ?></th>
                                <th width= "120"><?php echo _l('Reviewer'); ?></th>
                                <!-- <th width= "80"><?php //echo _l('Type'); ?></th>           -->
                                <th width= "120"><?php echo _l('Milestone'); ?></th>
                                <th width= "120"><?php echo _l('Closed On'); ?></th>
                                <th width= "120"><?php echo _l('Raised By'); ?></th>
                                <th width= "100"><?php echo _l('Raised Name'); ?></th>
                                <th width= "100"><?php echo _l('Raised Contact'); ?></th>
                                <th width= "120"><?php echo _l('Raised Email ID'); ?></th>
                         
                                <th width= "80" class="not-export"><?php echo _l('Evidence'); ?></th> 
                                
                                </tr>  
							</thead>
							<tbody>
                                <?php  //pre($projects); 
                                foreach($projects as $val){ 
                                $project_id = $val['id'];
                                 $ticketDetails = $this->report_model->get_project_details( $project_id);
                                // $assignedUser = !empty($ticketDetails->assigned_user_id) ? $ticketDetails->assigned_user_id : '';
                                //print_r($ticketDetails); exit;
                                 $assignedUser = getProjectAssignedUser($project_id);
                                 $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                                 $tasks = $this->projects_model->get_task_details( $project_id);
                                 $milestone = $this->report_model->get_current_milestone( $project_id);
                                 $milestone = !empty($milestone[0])?$milestone[0]:array(); 
                                 $milestone_name = '';
                                //  if(!empty($milestone['task_name']) && $val['status'] != 3 && $val['frozen'] == 0){
                                if(!empty($milestone['task_name']) && in_array($val['status'],[2,4,6]) && $val['frozen'] == 0){
                                     $milestone_name = $milestone['task_name'];
                                 }

                                if($val['status'] == 3){
                                $milestone_name = 'NA';
                                }

                                if(in_array($val['status'], [2, 4, 6])){
                                    $resolvedMilestone = $this->report_model->get_current_milestone($project_id,4);
                                    // $milestone['task_id'] = $resolvedMilestone[0]['task_id'];
                                    $milestone['task_id'] = !empty($resolvedMilestone[0]['task_id']) ? $resolvedMilestone[0]['task_id'] : (!empty($milestone['task_id'])?$milestone['task_id']:'');
                                }

                                 $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
                                 $latestImages = array();
                                 $latest_image ='';
                                 $latest_location = '';
                                 $resolved_evidence = '';
                                 $resolved_location = '';
                                 if(!empty($taskId)){
                                    $latestImages = $this->dashboard_model->get_evidence_image($project_id, $taskId);
                                    if(!empty($latestImages[0]['file_name']) && in_array( $val['status'], [ 2, 4, 6, 3]) ){
                                        $latest_image = base_url('uploads/tasks/' . $taskId . '/') . $latestImages[0]['file_name'];

                                        $resolved_evidence =  '<a href="'. $latest_image .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View </a>';
                                    
                                        $resolved_location = 'NA';
                                        if(!empty($latestImages[0]['latitude']) && $latestImages[0]['latitude'] != 0 && !empty($latestImages[0]['longitude']) &&  $latestImages[0]['longitude'] != 0){
                                            $latest_location = 'https://maps.google.com/maps?q=' . $latestImages[0]['latitude'] . ',' . $latestImages[0]['longitude'] . '"';

                                            $resolved_location = '<a href="'. $latest_location .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>';
                                        }
                                    }
                                }

                                 $projectNotes = project_latest_notes($project_id);
                                 $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
                                 //2, 4, 6, 
                                 if(!empty($projectNote_content) && $val['frozen'] == 0 && (in_array( $val['status'], [3,5]) || !empty($val['sub_ticket_id']) || is_project_reopened($project_id))){
                                    $resolved_evidence = !empty($resolved_evidence)?$resolved_evidence:'NA';
                                    $resolved_location = !empty($resolved_location)?$resolved_location:'NA';
                                 }
                                    
                                 $evidence = $this->report_model->get_evidence($project_id);
                                 $location = $this->report_model->get_location($project_id);
                                 $ticket_id = !empty( $val['sub_id']) ?  $val['sub_id'] :  $project_id;   
                                 $img_type = '';
                                 if($val['status'] == 3){
                                    $img_type = "closed";
                                 }else{
                                    $img_type = "original";
                                 }
                                
                                 if(!empty( $assignedUserDetails->full_name) && !empty( $assignedUserDetails->organisation)){
                                     $assign = $assignedUserDetails->full_name  ." (".  $assignedUserDetails->organisation.")";
                                 }else{
                                    $assign = '';
                                 }
                                 $status = (!empty( $ticketDetails->status_name)) ?  $ticketDetails->status_name : '';
                                 $assign_email = (!empty( $assignedUserDetails->email)) ?  $assignedUserDetails->email : '';
                                 

                                $raised_name = "";
                                $raised_email = "";
                                $raised_phone = "";
                                $user_type = "";
                                if(!empty( $val['user_type']) && $val['user_type'] == 'Call-Center'){
                                    $user_type = "Call Center Executive";
                                    $raised_name = (!empty( $val['rname'])) ?  $val['rname'] : '';
                                    $raised_email = (!empty( $val['remail'])) ?  $val['remail'] : '';
                                    $raised_phone = (!empty( $val['rphonenumber'])) ?  $val['rphonenumber'] : '';
                                }else if(!empty( $val['user_type']) && $val['user_type'] == 'Surveyor'){
                                   $user_type = "Surveyor";
                                   $raised_name = (!empty( $val['firstname'])) ?  $val['firstname'] : '';
                                   $raised_email = (!empty( $val['email'])) ?  $val['email'] : '';
                                   $raised_phone = (!empty( $val['phonenumber'])) ?  $val['phonenumber'] : '';
                                }

                                // project leader
                                $leader_name = '';
                                $leadername = $this->report_model->get_report_leader($project_id);
                                if(!empty($leadername)){
                                    $leader_name = $leadername->firstname;
                                }

                                // reviewer
                                $reviewer_name = '';
                                if(!empty($leadername)){
                                    $reviewername = $this->staff_model->get_reporting_person($leadername->staff_id);
                                    if(!empty($reviewername)){
                                        $reviewer_name = $reviewername->firstname;
                                    }
                                }

                                // status
                                $status_tag = '';
                                $statusname = '';
                               
                                if($val['status'] == 9 && $val['is_assigned'] == 0 && $val['frozen'] == 0 ){
                                    $status_tag = 'Unassigned';
                                }else  if (in_array( $val['status'], [ 2, 4, 6])  && $val['action_date'] >= date('Y-m-d') && $val['frozen'] == 0  ) {
                                    $status_tag = 'In Progress';
                                }else if ((in_array( $val['status'], [ 2, 4, 6]) && $val['action_date'] < date('Y-m-d') && $val['frozen'] == 0 ) || ($val['status'] == 1 && $val['action_date'] < date('Y-m-d') && $val['frozen'] == 0 )) {
                                    $status_tag = 'Delayed';
                                }else if( $val['status'] == 5 && $val['frozen'] == 0 ){
                                    $status_tag = 'Rejected';
                                }else if( $val['status'] == 1 && $val['frozen'] == 0 ){
                                    $status_tag = 'New';
                                }else if( $val['status'] == 3 && $val['frozen'] == 0 ){
                                    $status_tag = 'Closed';
                                } else if($val['frozen'] == 1){
                                    $status_tag = 'Frozen';
                                }

                                
             
                                if(!empty($status_tag)){
                                    
                                    //  
                                    if ($status_tag == "Delayed" && $ticketDetails->project_status == 1 ) {
                                        $statusname = 'Unaccepted';
                                    }else if ( $status_tag == "Delayed" &&  ($ticketDetails->project_status == 2 ||  $ticketDetails->project_status == 6 || $ticketDetails->project_status == 4) ) {
                                        $statusname = 'Overdue';
                                    }else if ($status_tag == 'In Progress'   && $val['reassigned'] == 1 ) {
                                        $statusname = 'Reassigned';
                                    }else if($status_tag == 'In Progress' && $ticketDetails->project_status == '2'){
                                        $statusname = '';
                                    }else if( $status_tag == 'In Progress' && $ticketDetails->project_status == '4'){
                                        $statusname = '';
                                    }else if( $status_tag == 'Rejected' && $ticketDetails->project_status == '5'){
                                        $statusname = '';
                                    }else if( $status_tag == 'In Progress' && $ticketDetails->project_status == '6'){
                                        $statusname = 'Reopened';
                                    }
                                }
  
                                if(!empty($statusname)){
                                    $status_name = $status_tag."-".$statusname;
                                }else{
                                    $status_name = $status_tag;
                                }

                                ?>
								<tr>
         
                                    <td><div class="dashboard-cell w20P action-item">
                                    <p class="ticket_details" data-project_id="<?php echo $project_id ?>" data-role="<?php echo $GLOBALS['current_user']->role_slug_url  ?>" data-status="<?php echo $val['status'] ?>" data-report="report"><strong><?php echo $ticket_id ?></strong></p>
                                    </div></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['name'])) ?  strip_tags($val['name']) : '' ?>"><?php echo (!empty( $val['name'])) ?  mb_strimwidth($val['name'], 0, 30, '...') : '' ?></p></td>
                                    <td><?php echo $status_name ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($assign); ?>"><?php echo (!empty( $assign)) ?  mb_strimwidth($assign, 0, 25, '...') : '' ?></p></td>
                                    <td><?php echo (!empty( $assignedUserDetails->phonenumber)) ?  $assignedUserDetails->phonenumber : '' ?></td>
                                    <td><?php echo (!empty( $val['deadline'])) ? '<span class="hide">'.date('Ymd',strtotime($val['deadline'])).'</span>'.date('d-m-Y',strtotime($val['deadline'])) : '' ?></td>
                                    <td><?php echo (!empty( $ticketDetails->sub_region_name)) ? '<p class="ellipsis" data-toggle="tooltip" data-placement="top" title="'.strip_tags($ticketDetails->sub_region_name).'">'.mb_strimwidth($ticketDetails->sub_region_name, 0, 25, '...').'</p>'  : '' ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['landmark'])) ?  strip_tags($val['landmark']) : '' ?>"><?php echo (!empty( $val['landmark'])) ?  mb_strimwidth($val['landmark'], 0, 30, '...') : '' ?></p></td>
                                    <td><?php echo (!empty( $ticketDetails->region_name)) ? '<p class="ellipsis" data-toggle="tooltip" data-placement="top" title="'.strip_tags($ticketDetails->region_name).'">'.mb_strimwidth($ticketDetails->region_name, 0, 25, '...').'</p>'  : '' ?></td>
                                    <td><?php echo (!empty( $ticketDetails->area_name)) ?  $ticketDetails->area_name : '' ?></td>
                                    <td><?php echo _d($val['project_created']) ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['description'])) ?  strip_tags($val['description']) : '' ?>"><?php echo (!empty( $val['description'])) ?  mb_strimwidth($val['description'], 0, 100, '...') : '' ?></p></td>
                                    
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo (!empty( $evidence[0])) ?  '<a href="'. $evidence[0] .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View  </a>' : 'NA' ?></div></td>
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo (!empty( $location[0])) ? '<a href="'. $location[0] .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>' : 'NA'  ?></div></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $projectNote_content)) ?  strip_tags($projectNote_content) : '' ?>"><?php echo (!empty( $projectNote_content)) ?  mb_strimwidth($projectNote_content, 0, 50, '...') : '' ?></p></td>

                                    <!-- <td><div class="d-flex justify-content-center align-flex-end">< ?php echo (!empty( $latest_image)) ?  '<a href="'. $latest_image .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View </a>' : 'NA' ?></div></td>
                                   
                                    <td><div class="d-flex justify-content-center align-flex-end">< ?php echo (!empty( $latest_location)) ? '<a href="'. $latest_location .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>' : 'NA'  ?></div></td> -->

                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo $resolved_evidence; ?></div></td>
                                   
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo $resolved_location;  ?></div></td>

                                   
                                    <td><?php echo (!empty( $assignedUserDetails->role_name)) ?  $assignedUserDetails->role_name : '' ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($assign_email); ?>"><?php echo  mb_strimwidth($assign_email, 0, 25, '...') ?></p></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($leader_name); ?>"><?php echo  mb_strimwidth($leader_name, 0, 25, '...') ?></p></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($reviewer_name); ?>"><?php echo  mb_strimwidth($reviewer_name, 0, 25, '...') ?></p></td>
                                    <!-- <td><?php //echo (!empty( $milestone['tag'])) ?  $milestone['tag'] : '' ?></td> -->
                                    <td><?php echo (!empty( $milestone_name)) ?  $milestone_name : '' ?></td>
                                    <td><?php echo (!empty( $val['date_finished'] && $val['date_finished'] != '0000-00-00 00:00:00')) ?  date("d-m-Y", strtotime($val['date_finished'])) : '' ?></td>
                                    <td><?php echo $user_type ?></td>  
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo $raised_name  ?>"><?php echo  mb_strimwidth($raised_name , 0, 25, '...')  ?></p></td>
                                    <td><?php echo $raised_phone ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($raised_email); ?>"><?php echo  mb_strimwidth($raised_email, 0, 25, '...') ?></p></td>
                                    
                                     <td><div class="d-flex justify-content-center align-flex-end">
                                        <a class="evidence_img evidence report-location" data-project_id="<?php echo $project_id ?>" data-img_type="<?php echo $img_type; ?>">
                                        <i class="fa fa-eye" aria-hidden="true"></i> View 
                                           </a>  </div>
                                    </td> 
                                   
								</tr>
								<?php } ?>
							</tbody>
						</table>
						

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Image Evidence Popup -->
<div class="modal sidebarModal fade ticket-detail-modal w70P" id="sidebarModal">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form autocomplete="off" action="javascript:void(0)" id="area_admin_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 evidence-data"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>

<!-- Export Popup -->
<div class="modal exportModal fade ticket-detail-modal" id="exportModal">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form autocomplete="off" action="javascript:void(0)" id="area_admin_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="panel-header">
                            <h1>Report Download Request</h1>
                        </div>
                        <hr class="hr-panel-heading" style="margin-left: -15px;margin-right: -15px; " />

                        <div class="row">
                            <div class="col-md-12">
                                <div id="search_filter"></div>
                                Your report download request has been recorded. It will be emailed to you shortly.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: left;">
                        <button type="button" class="btn btn-cancel" data-dismiss="modal">OK</button>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>
<?php $this->load->view('admin/dashboard/dashboard_popup'); ?>
<?php init_tail(); ?>

<script>
    let role_slug = '<?php echo $role_slug;?>';
    $(document).ready(function() {
        //$('#rep-total_length .input-sm').val('10').trigger('change');
        $('#report-mg #raised').trigger('click');
        $('#report-mg_length .input-sm').val('10').trigger('change');
        $('#rep-summ_length .input-sm').val('10').trigger('change');
        $(".input-sm option[value='-1']").remove();
        $('#report-mg_wrapper .btn-default-dt-options').html('<a  id="download" >Download</a>');
        $(".dt-button-collection dropdown-menu").css("display", "none");
    });


  

    $( document ).ready(function() {
        var report_from = $('input[name="report-from"]');
        var report_to = $('input[name="report-to"]');
        var date_range = $('#date-range');

        var time = $('[name="report_months"]').val();
        if (time == 'custom') {
            report_to.attr('disabled', false);
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }
    });

    <?php if($role_slug == 'ae-global') {?>
        region_subregion_status('region', true);
        region_subregion_status('subregion', true);
    <?php }else{?>
        region_subregion_status('subregion', true);
    <?php } 
    if (!empty($tableParams['subregion'])){?>   
        region_subregion_status('subregion', false);
    <?php }
    if (!empty($tableParams['areaid'])){?>
        region_subregion_status('region', false);
    <?php } 
    if (!empty($tableParams['region'])){?>
        region_subregion_status('subregion', false);
    <?php } ?>
    
    

    function region_subregion_status(field_name, is_disabled = true){
        $('[name="'+field_name+'[]"]').prop("disabled", is_disabled);
        $('[name="'+field_name+'[]"]').selectpicker('refresh');
    }

    var report_from = $('input[name="report-from"]');
    var report_to = $('input[name="report-to"]');
    var date_range = $('#date-range');

    $(document).on('change', 'select[name="report_months"]', function() {
        var val = $(this).val();
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val == 'custom') {
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }
    });

    $(document).on('change', 'input[name="report-from"]', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
            report_to.attr('disabled', false);
            $('#report-to').val(val);
        } else {
            report_to.attr('disabled', true);
        }
    });

    function getTime(date){
        date = date.split("-");
        var date = new Date( date[2], date[1] - 1, date[0]);
        var gdate = date.getTime();
        return gdate;
    }

    function validateDate(fromdate,todate){
        var from_date = getTime(fromdate);
        var to_date = getTime(todate);

        if(from_date !=''  && to_date != ''  ){
            if (from_date > to_date) {
                alert_float('danger', 'From date should not be greater than To date');
                return false;
            }
        }
    }


    $("form").submit(function(){
        var fromdate = document.getElementById("report-from").value;
        var todate = document.getElementById("report-to").value;
        var time = $('[name="report_months"]').val();

    
        if(time == 'custom' && fromdate == ''  && todate == ''  ){
            alert_float('danger', 'Select date range');
            return false;           
        }

        var validate = validateDate(fromdate,todate);
        if(validate == false){
            return false;
        }
       
    });

    $(document).on('click', '#export_pdf', function() {
        var filter =  document.getElementById('filter-id').innerHTML;
        var data = JSON.parse(filter);

        var data1 = {
            'area': <?php echo $area ?>,
            'role': <?php echo $role ?>,
            'areaid': $('[name="areaid[]"]').val(),
            'region': $('[name="region[]"]').val(),
            'subregion': $('[name="subregion[]"]').val(),
            'action_taker': $('[name="action_taker[]"]').val(),
            'action_reviewer': $('[name="action_reviewer[]"]').val(),
            'category': $('[name="category[]"]').val(),
            'bug': $('[name="bug[]"]').val(),
            'report_months': $('[name="report_months"]').val(),
            'report-from': $('[name="report-from"]').val(),
            'report-to': $('[name="report-to"]').val(),
            'ticket': $('[name="ticket[]"]').val(),
            'duration': $('[name="duration[]"]').val(),
            //'filename': 'Report_'+time+'.csv',
        
        }   
       
        
       // console.log(filter); return false;
            

        var fromdate = document.getElementById("report-from").value;
        var todate = document.getElementById("report-to").value;

        var validate = validateDate(fromdate,todate);
        if(validate == false){
            return false;
        }
            
            $.ajax({
                url: admin_url + "report/create_pdf",
                data: data,
                complete : function(){
                   // alert(this.url);
                },
                success: function(){
                   window.location.replace(this.url);
                    //window.open(this.url, '_blank');
                }
            });
            return false;
        });


           //$('#download').on('click', function() {
        $(document).on('click', '#download', function() {
            var d = new Date($.now());
            $(".dt-button-collection dropdown-menu").css("display", "none");
            var time = '<?php echo date('dmyhis') ?>';
            var filter =  document.getElementById('filter-id').innerHTML;
            var data = JSON.parse(filter);
        //     if(filter == 0){
        //         let data = {
        //         'area': <?php //echo $area ?>,
        //         'role': <?php //echo $role ?>,
        //         'areaid': $('[name="areaid[]"]').val(),
        //         'region': $('[name="region[]"]').val(),
        //         'subregion': $('[name="subregion[]"]').val(),
        //         'action_taker': $('[name="action_taker[]"]').val(),
        //         'action_reviewer': $('[name="action_reviewer[]"]').val(),
        //         'category': $('[name="category[]"]').val(),
        //         'bug': $('[name="bug[]"]').val(),
        //         'report_months': $('[name="report_months"]').val(),
        //         'report-from': $('[name="report-from"]').val(),
        //         'report-to': $('[name="report-to"]').val(),
        //         'ticket': $('[name="ticket[]"]').val(),
        //         'duration': $('[name="duration[]"]').val(),
        //         //'filename': 'Report_'+time+'.csv',
        
        //     }
        // }else{
        //     var data = JSON.parse(filter);
        // }
          
        var fromdate = document.getElementById("report-from").value;
        var todate = document.getElementById("report-to").value;

        var validate = validateDate(fromdate,todate);
        if(validate == false){
            return false;
        }
            
            setTimeout(function(){ send_mail(data); }, 5000);
          
            $.get(admin_url + 'report/show_filter', data).done((response) => {   
                    if(response != ''){
                        //$('#search_filter').html(response)
                        $('#exportModal').modal('show');
                    }

            });
            return false;
        });

        
        function send_mail(data) {
          
          $.ajax({
              type: 'GET',
              url: admin_url+'report/send_email',
              
              data: data,
              async: false,
              success: function(response) {
                  //$('#exportModal').modal('hide');

                  if (response != '') {

                      alert_float('success', 'Confirmation email sent successfully');
                  } else {
                      alert_float('danger', 'No data found');
                  }
              }
          });
      }

      $(document).on('change', '[name="areaid[]"]', function(e) {

            let areaval = $(e.currentTarget).val();
            if(areaval == ''){
                options = [];
                $('[name="region[]"]').selectpicker('val', options);
                $('[name="subregion[]"]').selectpicker('val', options);
                region_subregion_status('region',true);
                region_subregion_status('subregion',true);
                return false;
            }
                    
            var area = $('[name="areaid[]"]').val();
            let role = "<?= $role ?>";

            let data = {
                'area_id': area,
                'role': role,
            }
            $.post(admin_url + 'report/get_region', data).done((res) => {
                res = JSON.parse(res);
                if (res.success == true) {
                    let options = "";
                    res.data.map(val => {
                        options += `<option value='${val.id}'>${val.region_name} </option>`;
                    });
                    $('[name="region[]"]').html(options);
                    region_subregion_status('region',false);
                    region_subregion_status('subregion',true);
                    $('[name="region[]"]').selectpicker('refresh');
                    $('[name="subregion[]"]').selectpicker('val', []);      
                } else if (res.success == false) {
                    let options = "";
                    $('[name="region[]"]').html(options);
                    region_subregion_status('region',true);
                    $('[name="region[]"]').selectpicker('refresh');
                }
            })
            });

            $(document).on('change', '[name="region[]"]', function(e) {

            let regionval = $(e.currentTarget).val();
            if(regionval == ''){
                options = [];
                $('[name="subregion[]"]').selectpicker('val', options);
                region_subregion_status('subregion',true);
                return false;
            }

            var region = $('[name="region[]"]').val();
            let role = "<?= $role ?>";

            let data = {
                'region_id': region,
                'role': role,
            }
            $.post(admin_url + 'report/get_subregion', data).done((res) => {
                res = JSON.parse(res);
                if (res.success == true) {
                    let options = "";
                    res.data.map(val => {
                        options += `<option value='${val.id}'>${val.region_name} </option>`;
                    });
                    $('[name="subregion[]"]').html(options);
                    region_subregion_status('subregion',false);
                // console.log("Awnish", region.length);
                    if(region.length == ''){
                        region_subregion_status('subregion', true);
                    }
                    $('[name="subregion[]"]').selectpicker('refresh');
                } else if (res.success == false) {

                    let options = "";
                    $('[name="subregion[]"]').html(options);
                    region_subregion_status('subregion',true);
                    $('[name="subregion[]"]').selectpicker('refresh');
                }
            })
            });


  
    $(function() {
     
        var report_from = $('input[name="report-from"]');
        var report_to = $('input[name="report-to"]');
        var date_range = $('#date-range');

        var time = $('[name="report_months"]').val();
        if (time == 'custom') {
            report_to.attr('disabled', false);
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }

        var fnServerParams = {
            "report_months": '[name="report_months"]',
            "report_from": '[name="report-from"]',
            "report_to": '[name="report-to"]',
            "area": '[name="areaid[]"]',
            "region": '[name="region[]"]',
            "subregion": '[name="subregion[]"]',
            "action_taker": '[name="action_taker[]"]',
            "action_reviewer": '[name="action_reviewer[]"]',
            "category": '[name="category[]"]',
            "bug": '[name="bug"]',
            "ticket": '[name="ticket[]"]',
        }

  


       

    });


    $(document).on('click', '.evidence_img', function() {
        let projectId = $(this).data('project_id');
        $.ajax({
            type: 'GET',
            url: admin_url + 'dashboard/evidence_image',
            data: {
                projectId: projectId
            },
            success: function(response) {
                if (response != '') {
                    $('.evidence-data').html(response);
                } else {
                    $(".evidence-data").html('<p>No Evidence Found</p>');
                }
                $('#sidebarModal').modal('show');
            }
        });
    });

    $(document).on('change', '[name="duration[]"]', function(e) {
      let durations = $(e.currentTarget).val();
      if(durations == ''){
        options = [];
        $('.custom-categories').selectpicker('val', options);
      }
      selectCategories(durations);
   });

   function selectCategories(duration) {
      if (duration.length) {
         $.post(admin_url + "issues/get_duration_issues", {
            "duration": duration
         }).done((res) => {
            res = JSON.parse(res);
            if (res.success == true) {
                let issues = [...res.issues];
                let options = [];
                if (issues.length > 0) {

                issues.map(issue => {
                    issue.map(val => {
                        options.push(val.id);
                    });
                });
                } else {
                options = [];
                }
            $('.custom-categories').selectpicker('val', options);
            }else if (res.success == false) {
                options = [];
                $('.custom-categories').selectpicker('val', options);
                $('.custom-categories .filter-option-inner-inner').text('No Action Items Selected');
            }
          
         }).fail((err) => console.log(err));

      }
   }
</script>
<?php $this->load->view('admin/dashboard/dashboard_scripts'); ?>
</body>

</html>
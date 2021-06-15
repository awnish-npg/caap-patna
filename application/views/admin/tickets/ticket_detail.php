<!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" /> -->
<link href="<?= base_url('/assets/css/jquery-ui.css'); ?>" rel="stylesheet" />
<?php
$project_id = ($ticketDetails->project_id) ? $ticketDetails->project_id : '';
$project_name = !empty($ticketDetails->project_name) ? ucfirst($ticketDetails->project_name) : '';
$landmark = !empty($ticketDetails->landmark) ? $ticketDetails->landmark : '';
$projectStatus = !empty($ticketDetails->project_status) ? $ticketDetails->project_status : '';
$frozen = !empty($ticketDetails->frozen) ? $ticketDetails->frozen : 0;
$sub_ticket_id = !empty($ticketDetails->sub_ticket_id) ? $ticketDetails->sub_ticket_id : 0;
$parent_id = !empty($ticketDetails->parent_id) ? $ticketDetails->parent_id : 0;
$title = $project_name . '<span> near, </span>' . $landmark;
$ticket_id = !empty($ticketDetails->sub_id) ? $ticketDetails->sub_id : $project_id;
$projectDueDate = !empty($ticketDetails->deadline) ? setDateFormat($ticketDetails->deadline) : "";

$area = !empty($ticketDetails->area_name) ? $ticketDetails->area_name : '';
$region = !empty($ticketDetails->region_name) ? $ticketDetails->region_name : '';
$subregion =  !empty($ticketDetails->sub_region_name) ? $ticketDetails->sub_region_name : '';
$source = !empty($ticketDetails->company) ? $ticketDetails->company : '';
$userRole = $GLOBALS['current_user']->role_slug_url;

$ticketStatus = !empty($ticketDetails->status_name) ? $ticketDetails->status_name : '';
if ($projectStatus == 5) {
    $projectNotes = project_latest_notes($project_id, $projectStatus);
    $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
    // $ticketStatus = $ticketStatus . ' (' . $projectNote_content . ')';
}
?>
<div class="content pT0">
    <div class="box-wrapper">

    </div>
    <input type="hidden" class="ticketPopupId" value="<?php echo $project_id; ?>" />
    <?php
    if (empty($frozen)) {
        if ((get_staff_user_id() == $assignedUser)) {
            if ($userRole == 'at') {
                if ($projectStatus == 1 || ($projectStatus == 4 && $milestoneStatus == 4)) {
                    $actionLabel = '';
                    if ($projectStatus == 1) {
                        $actionLabel = ' - Accept or Reject';
                    } else if ($projectStatus == 4 && $milestoneStatus == 4) {
                        $actionLabel = ' - Close/Reopen Project';
                    }
    ?>
                    <div class="box-wrapper mB30">
                        <div class="panel panel-default sub-ticket-panel mB0 border-bottom-0">
                            <div class="panel-heading">
                                Action<?= $actionLabel ?>
                            </div>
                        </div>
                        <?php $this->load->view('admin/tickets/at_ticket_detail'); ?>
                    </div>
                <?php }
            }
            if ((($userRole == 'ata') || (get_staff_user_id() == $assignedUser)) && empty($sub_ticket_id)) {
                if ($projectStatus == 2 || $projectStatus == 6) {
                ?>
                    <div class="box-wrapper mB30">
                        <div class="panel panel-default sub-ticket-panel mB0 border-bottom-0">
                            <div class="panel-heading">
                                Action - Close Project
                            </div>
                        </div>
                        <?php $this->load->view('admin/tickets/ata_ticket_detail'); ?>
                    </div>
            <?php }
            }
        }
        //3=>Closed, 5=>Rejected
        // if ($userRole == 'ar' && ($projectStatus == 3 || $projectStatus == 5)) {
        // if ($userRole == 'ar' && (($projectStatus == 3 && (get_staff_user_id() != $assignedUser) && empty($sub_ticket_id)) || $projectStatus == 5)) {

        // if ($userRole == 'ar' && (get_staff_user_id() == $assignedUser || (!empty($assignedAts) && (in_array($assignedUser,$assignedAts)))) && (($projectStatus == 3  && empty($sub_ticket_id)) || $projectStatus == 5)) {
        if ($userRole == 'ar' && ((get_staff_user_id() == $assignedUser && $projectStatus != 3)|| (!empty($assignedAts) && (in_array($assignedUser,$assignedAts)))) && (empty($sub_ticket_id) || $projectStatus == 5)) {
            $actionLabel = '';

            if ($projectStatus == 3 && !$activeUser) {
               
            }else{
                if ($projectStatus == 3 && empty($sub_ticket_id)) {
                    $actionLabel = ' - Reopen';
                }
                if(in_array($projectStatus,['3','5'])){
                ?>
                <div class="box-wrapper mB30">
                    <div class="panel panel-default sub-ticket-panel mB0 border-bottom-0">
                        <div class="panel-heading">
                            Action<?= $actionLabel ?>
                        </div>
                    </div>
                    <?php $this->load->view('admin/tickets/ar_ticket_detail'); ?>
                </div>
        <?php } 
            } 
        }
    } ?>
    <div class='thetop' id="section2"></div>
    <div class="box-wrapper">
        <h4 class="action-title sliderTitle no-border pull-right">
            <?php //echo $title; 
            ?>
            <!-- <a href="#" class="pdf-download position-relative pull-right" title="Download as PDF" onclick="printDiv('ticketDetailsPopup')"> <i class="fa fa-file-pdf-o"></i></a> -->
            <a href="#" class="pdf-download position-relative pull-right" title="Download as PDF" id="ticket_detail_pdf"> <i class="fa fa-file-pdf-o"></i></a>
        </h4>
        <ul class="nav nav-tabs pull-left ticket-detail-tabs">
            <li class="active"><a href="#ticket-details" data-toggle="tab">Project Details</a></li>
            <li class="print-none"><a href="#ticket-history" data-toggle="tab">Project History</a></li>
        </ul>

        <div class="clearfix"></div>
        <div class="tab-content faq-cat-content">
            <div class="tab-pane active in fade" id="ticket-details">
                <div class="panel-group" id="accordion-cat-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a data-parent="#accordion-cat-1" href="javascript:void(0)">
                                <h4 class="panel-title text-center">
                                    <p class="pull-left"><label>Project ID: </label><span><?php echo $ticket_id; ?></span></p>
                                    <?php if ($frozen) { ?>
                                    <p class="d-inline-block"><label class="frozen mR0">FROZEN</label></p>
                                    <?php } ?>
                                    <p class="pull-right"><label>Raised On: </label><span><?php echo !empty($ticketDetails->logged_date) ? setDateFormat($ticketDetails->logged_date) : ''; ?></span></p>
                                    <div class="clearfix "></div>
                                </h4>
                            </a>
                        </div>
                        <div id="basic-details" class="panel-collapse ">
                            <div class="panel-body pB0">
                                <p class="reported-detail font-size-14 semibold"><?php echo $project_name; ?></p>
                                <!-- <p>This has been reported in (Region), (Subregion) near (Landmark) by (Type of user) </p> -->
                                <p class="reported-detail">This has been reported in <span class="font-size-14"><?php echo $region; ?></span>, <span class="font-size-14"><?php echo $subregion; ?></span> near <span class="font-size-14"><?php echo $landmark; ?></span> by <span class="font-size-14"><?php echo $source; ?></span></p>

                                <p class="reported-detail due-date">
                                    Due Date: <strong class="font-size-14"> <?php echo $projectDueDate; ?></strong>
                                </p>
                                <?php if (!empty($tasks)) { ?>
                                    <div class="panel panel-default border-0 box-shadow-0">
                                        <!-- <div class="panel-heading">
                                            <a data-parent="#accordion-cat-1" href="javascript:void(0)">
                                                <h4 class="panel-title">
                                                    Status (<?php echo $ticketStatus; ?>) -->
                                        <!--<span class="pull-right"><i></i></span>-->
                                        <!-- </h4>
                                            </a>
                                        </div> -->

                                        <div id="status" class="panel-collapse  status-panel">
                                            <div class="panel-body pL0 pR0 table-scroll">
                                                <table class="table table-borderless table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th width="100">Milestone(s)</th>
                                                            <!-- <th>Duration</th> -->
                                                            <th width="100">Status</th>
                                                            <th width="120">Start Date</th>
                                                            <!-- <th>Due Date</th> -->
                                                            <th width="140">Assigned To</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($tasks as $task) {
                                                            $task_status = !empty($task['task_status']) ? $task['task_status'] : "Pending";
                                                            $taskId = $task['task_id'];
                                                            $assignedUserName = '';
                                                            $childProjectStatus = '';
                                                            if ($hasChild) {
                                                                $childTaskDetails = get_child_task_details($taskId);
                                                                $task['status'] = !empty($childTaskDetails->status) ? $childTaskDetails->status : 0;
                                                                $subTaskTicketId = !empty($childTaskDetails->rel_id) ? $childTaskDetails->rel_id : $ticket_id;
                                                                $subTicketAssignee = !empty(getProjectAssignedUser($subTaskTicketId)) ? getProjectAssignedUser($subTaskTicketId) : '';
                                                                $assignedUserName = get_staff_full_name($subTicketAssignee);
                                                                // pre($task['status']);
                                                                $childProjectStatus = get_project_status($subTaskTicketId);
                                                                if ((empty($childProjectStatus) || $childProjectStatus == 1) && $task['status'] == 2) {
                                                                    $task['status'] = 0;
                                                                }
                                                                if ($childProjectStatus == 3) {
                                                                    $task['status'] = 4;
                                                                }
                                                            } else {
                                                                $assignedUserName = !empty($assignedUserDetails->full_name) ? $assignedUserDetails->full_name : '';
                                                            }
                                                            
                                                            if(!empty($ticketDetails->task_status) && $ticketDetails->task_status == 1){
                                                                $task['status'] = 0;
                                                            }
                                                            if(!empty($ticketDetails->task_status) && $ticketDetails->task_status == 3 && $task['status'] == 2){
                                                                $task['status'] = 0;
                                                            }
                                                            
                                                            $td_color = "complete";
                                                            if (empty($task['status'])) {
                                                                $td_color = "pending";
                                                                $task_status = 'Pending';
                                                            } else if ($task['status'] == 2) {
                                                                $td_color = "wip";
                                                                $task_status = 'In Progress';
                                                            } else if ($task['status'] == 4) {
                                                                $td_color = "complete";
                                                                $task_status = 'Completed';
                                                            }
                                                            if ($hasChild && $task['status'] == 4) {
                                                                $task_status = 'Completed';
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td><?= $task['task_name'] ? $task['task_name'] : ""; ?></td>
                                                                <!-- <td><?= $task['task_days'] ? $task['task_days'] . " Days" : ""; ?></td> -->
                                                                <td class="<?= $td_color; ?>"><?= $task_status; ?></td>
                                                                <td><?= $task['startdate'] ? setDateFormat($task['startdate']) : ""; ?></td>
                                                                <!-- <td><?= $task['duedate'] ? $task['duedate'] : ""; ?></td> -->
                                                                <td><?= $assignedUserName ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="spearator"></div>
                            </div>
                            <div id="issue-details" class="panel-collapse ">
                                <div class="panel-body pL0 pR0">
                                    <ul class="detail-list issue-detail-list pR0">

                                        <!-- Separate  Image and Document Start -->
                                        <?php if (!empty($project_id) && !empty($evidenceImages)) {
                                            $evidenceImageData = array();
                                            if(!empty($evidenceImages)){
                                                foreach ($evidenceImages as $image) {
                                                    $file_id = $image['id'];
                                                    $file_name = $image['file_name'];
                                                    $file_thumb = $image['subject'];
                                                    $file_optimized = $image['thumbnail_link'];
                                                    $milestone = $image['milestone'];
                                                    $latitude = $image['latitude'];
                                                    $longitude = $image['longitude'];
                                                    $filetype = $image['filetype'];

                                                    $imgPath = 'uploads/projects/' . $project_id . '/';

                                                    $location = '';
                                                    if (!empty($latitude) && !empty($longitude)) {
                                                        // $url = base_url('/admin/tickets/viewmap/' . $project_id . '?lat=' . $latitude . '&lang=' . $longitude . '&output=embed');
                                                        // $location = '<a href="' . $url . '" class="mT10 text-center d-block" target="_blank"><i class="fa fa-map-marker"></i>View Location</a>';
                                                        $location = '<a href="https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '" class="mT10 text-center d-block" target="_blank"><i class="fa fa-map-marker"></i>View Location</a>';
                                                    } else {
                                                        $location = '';
                                                    }

                                                    if ($filetype == 'application/pdf') {
                                                        $evidenceImageData['document'][] = array(
                                                            'imgPath' => $imgPath,
                                                            'milestone' => $milestone,
                                                            'projectId' => $project_id,
                                                            'file_id' => $file_id,
                                                            'file_name' => $file_name,
                                                            'file_thumb' => $file_thumb,
                                                            'file_optimized' => $file_optimized,
                                                            'location' => $location,
                                                            'filetype' => $filetype
                                                        );
                                                    } else {
                                                        $evidenceImageData['image'][] = array(
                                                            'imgPath' => $imgPath,
                                                            'milestone' => $milestone,
                                                            'projectId' => $project_id,
                                                            'file_id' => $file_id,
                                                            'file_name' => $file_name,
                                                            'file_thumb' => $file_thumb,
                                                            'file_optimized' => $file_optimized,
                                                            'location' => $location,
                                                            'filetype' => $filetype
                                                        );
                                                    }
                                                }
                                            }

                                            $latestImageData = array();
                                            if(!empty($latestImages )){
                                                $date = '';
                                                foreach ($latestImages as $image) {
                                                    $file_id = $image['id'];
                                                    $file_name = $image['file_name'];
                                                    $file_thumb = $image['subject'];
                                                    $file_optimized = $image['thumbnail_link'];
                                                    $milestone = $image['milestone'];
                                                    $latitude = $image['latitude'];
                                                    $longitude = $image['longitude'];
                                                    $filetype = $image['filetype'];
                                                    $dateAdded = date('Y-m-d',strtotime($image['dateadded']));
                                                    if(empty($date)){
                                                        $date = $dateAdded;
                                                    }

                                                    if($date != $dateAdded){
                                                        continue;
                                                    }

                                                    $imgPath = 'uploads/tasks/' . $milestone . '/';

                                                    $location = '';
                                                    if (!empty($latitude) && !empty($longitude)) {
                                                        // $url = base_url('/admin/tickets/viewmap/' . $project_id . '?lat=' . $latitude . '&lang=' . $longitude . '&output=embed');
                                                        // $location = '<a href="' . $url . '" class="mT10 text-center d-block" target="_blank"><i class="fa fa-map-marker"></i>View Location</a>';
                                                        $location = '<a href="https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '" class="mT10 text-center d-block" target="_blank"><i class="fa fa-map-marker"></i>View Location</a>';
                                                    } else {
                                                        $location = '';
                                                    }

                                                    if ($filetype == 'application/pdf') {
                                                        $latestImageData['document'][] = array(
                                                            'imgPath' => $imgPath,
                                                            'milestone' => $milestone,
                                                            'projectId' => $project_id,
                                                            'file_id' => $file_id,
                                                            'file_name' => $file_name,
                                                            'file_thumb' => $file_thumb,
                                                            'file_optimized' => $file_optimized,
                                                            'location' => $location,
                                                            'filetype' => $filetype
                                                        );
                                                    } else {
                                                        $latestImageData['image'][] = array(
                                                            'imgPath' => $imgPath,
                                                            'milestone' => $milestone,
                                                            'projectId' => $project_id,
                                                            'file_id' => $file_id,
                                                            'file_name' => $file_name,
                                                            'file_thumb' => $file_thumb,
                                                            'file_optimized' => $file_optimized,
                                                            'location' => $location,
                                                            'filetype' => $filetype
                                                        );
                                                    }
                                                }
                                            }
                                            // pre($evidenceImageData);
                                            $imageActive = '';
                                            $docActive = '';
                                            $latImageActive = '';
                                            $latDocActive = '';
                                            if (!empty($evidenceImageData['image'])) {
                                                $imageActive = ' active in ';
                                                $docActive = '';
                                            }else{
                                                $imageActive = '';
                                                $docActive = ' active in ';
                                            }
                                            if(!empty($latestImageData )){
                                                $imageActive = '';
                                                $docActive = '';
                                                if (!empty($latestImageData['image'])) {
                                                    $latImageActive = ' active in ';
                                                    $latDocActive = '';
                                                }else{
                                                    $latImageActive = '';
                                                    $latDocActive = ' active in ';
                                                }
                                            }
                                            ?>

                                            <ul class="nav nav-tabs ticket-detail-tabs w100P pL15">
                                                <li class="<?= $imageActive ?> print-none"><a href="#image" data-toggle="tab">Raised Image(s)</a></li>
                                                <li class="<?= $docActive ?> print-none"><a href="#document" data-toggle="tab">Raised Document(s)</a></li>
                                            <?php if(!empty($latestImageData )){ ?>
                                                <li class="mT0 pL15 pT5 mT0 mL15 md-hide" style="border-left: 1px solid #ddd"><a style=" border-radius:0" class="p-0 deskH35"></a></li>
                                                <li class="<?= $latImageActive ?> print-none mL15"><a href="#latestEvi" data-toggle="tab">Latest Image(s)</a></li>
                                                <li class="<?= $latDocActive ?> print-none"><a href="#latestLoc" data-toggle="tab">Latest Document(s)</a></li>
                                            <?php } ?>
                                            </ul>

                                            <ul class="nav nav-tabs only-print">
                                                <li class="" style="font-size: 14px; font-weight: 700; margin-top:15px;">Image(s)</li>
                                            </ul>
                                            <div class="clearfix "></div>

                                            <div class="tab-content faq-cat-content">
                                                <div class="panel-body pL0">
                                                    <div class="tab-content">
                                                        <!-- Images -->
                                                        <div class="tab-pane <?= $imageActive ?> fade  pr-mT0" id="image">
                                                            <div class="content pr-mT0 print-p-0 pB10">
                                                                <?php if (!empty($evidenceImageData['image'])) { ?>
                                                                    <ul class="row lightgallery">
                                                                        <?php
                                                                        foreach ($evidenceImageData['image'] as $imageData) {
                                                                            $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                            $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                            $file_thumb = !empty($imageData['file_thumb']) ? $imageData['file_thumb'] : '';
                                                                            $file_optimized = !empty($imageData['file_optimized']) ? $imageData['file_optimized'] : '';
                                                                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                <a href="javascript:void(0);">
                                                                                    <figure>
                                                                                        <img class="" src="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                    </figure>
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                    <ul class="row">
                                                                        <?php
                                                                        if (!empty($evidenceImageData['image']))
                                                                            foreach ($evidenceImageData['image'] as $imageData) {
                                                                                $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                <?php echo $loc; ?>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                <?php   } else { ?>
                                                                    <p class="text-warning mT10"><i class="fa fa-warning"></i>No evidence image available</p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <!-- Document -->
                                                        <div class="tab-pane <?= $docActive ?> fade" id="document">
                                                            <ul class="nav nav-tabs only-print">
                                                                <li class="" style="font-size: 14px; font-weight: 700; margin-top:25px; margin-bottom: 20px">Document(s)</li>
                                                            </ul>
                                                            <ul class="row docgallery content">
                                                                <?php if (!empty($evidenceImageData['document'])) {
                                                                    foreach ($evidenceImageData['document'] as $imageData) {
                                                                        $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                        $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                        $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                ?>
                                                                        <li class="col-xs-6 col-sm-4 col-md-3" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                            <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                <figure><img class="print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>"></figure>
                                                                            </a>
                                                                        </li>
                                                                    <?php }
                                                                } else { ?>
                                                                    <p class="text-warning mT10"><i class="fa fa-warning"></i> No document available</p>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    <?php if(!empty($latestImageData )){ ?>
                                                        <!-- Latest Evidence -->
                                                        <div class="tab-pane <?= $latImageActive ?> fade" id="latestEvi">
                                                            <div class="content pr-mT0 print-p-0 pB10">
                                                                <?php if (!empty($latestImageData['image'])) { ?>
                                                                    <ul class="row lightgallery">
                                                                        <?php
                                                                        foreach ($latestImageData['image'] as $imageData) {
                                                                            $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                            $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                            $file_thumb = !empty($imageData['file_thumb']) ? $imageData['file_thumb'] : '';
                                                                            $file_optimized = !empty($imageData['file_optimized']) ? $imageData['file_optimized'] : '';
                                                                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                <a href="javascript:void(0);">
                                                                                    <figure>
                                                                                        <img class="" src="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                    </figure>
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                    <ul class="row">
                                                                        <?php
                                                                        if (!empty($latestImageData['image']))
                                                                            foreach ($latestImageData['image'] as $imageData) {
                                                                                $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-6 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                <?php echo $loc; ?>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                <?php   } else { ?>
                                                                    <p class="text-warning mT10"><i class="fa fa-warning"></i>No evidence image available</p>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <!-- Latest Location -->
                                                        <div class="tab-pane <?= $latDocActive ?> fade" id="latestLoc">
                                                            <ul class="nav nav-tabs only-print">
                                                                <li class="" style="font-size: 14px; font-weight: 700; margin-top:25px; margin-bottom: 20px">Document(s)</li>
                                                            </ul>
                                                            <ul class="row docgallery content">
                                                                <?php if (!empty($latestImageData['document'])) {
                                                                    foreach ($latestImageData['document'] as $imageData) {
                                                                        $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                        $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                        $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                ?>
                                                                        <li class="col-xs-6 col-sm-4 col-md-3" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                            <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                <figure><img class="print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>"></figure>
                                                                            </a>
                                                                        </li>
                                                                    <?php }
                                                                } else { ?>
                                                                    <p class="text-warning mT10"><i class="fa fa-warning"></i> No document available</p>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Separate  Image and Document End -->

                                        <?php } else { ?>
                                            <p class="text-warning mT10"><i class="fa fa-warning"></i> No evidence available</p>
                                        <?php } ?>
                                        <li class="w100P mT20 pL15 pR15 pT18 mR0" style="border-top:1px solid #ddd"><label style="font-size:13px; font-weight:700; color: #2d2d2d;">Raised Comment</label><span><?php echo !empty($ticketDetails->description) ? $ticketDetails->description : ''; ?></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="page-break-after: always;"></div>
            <ul class="nav nav-tabs only-print">
                <li class="" style="font-size: 14px; font-weight: 700; margin-top:15px; margin-bottom: 10px">Project History</li>
            </ul>
            <div class="tab-pane fade" id="ticket-history">
                <p>No data</p>
                NO DATA
            </div>
        </div>
    </div>

    <style>
        .lg-outer .lg-thumb {
            margin: auto;
        }

        #lightgallery>li {
            margin-right: 0;
            margin-top: 0;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('.lightgallery').lightGallery();
        });

        $(document).on('click', "#scroll-bottom", function() {
            $('#mCSB_2_container').css('top', 0);
        });

        $(document).on('click', "#scroll-top", function() {
            let divHeight = $('#ticket-history').innerHeight();
            // $('#mCSB_2_container').css('top',-(height-400));
            // $('#mCSB_2_container').css('top',height);
            $('#mCSB_2_container').animate({
                top: -(divHeight - 400)
            }, 500);
        });
    </script>
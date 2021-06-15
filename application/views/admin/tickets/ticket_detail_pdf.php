<!DOCTYPE html>
<html>
<?php
    $project_id = ($ticketDetails->project_id) ? $ticketDetails->project_id : '';
?>
<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta charset="utf-8">
    <title>Projects Summary <?=$project_id?></title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
     <link rel=" stylesheet" href="<?php echo base_url('assets/css/ticketdetail-pdf.css') ?>">
</head>

<body>
    <table class=" header">
        <tr>
            <td class="text-left" width="75">
                <!-- <img src="<?php //echo base_url('assets/images/cpcb-pdf-logo.png') ?>" alt=""> -->
                <?php if(!empty(get_logo('company_logo'))){ ?>
                    <img src="<?php echo base_url(get_logo('company_logo')); ?>" alt="" style="width: 75px;height: 55px;">
                <?php } ?>
            </td>
            <td>
                <h1 class="text-center"><?php echo get_option('companyname');?></h1>
            </td>
            
            <td class="text-right" width="75">
            <?php if(!empty(get_area_logo())){ ?>
                <img src="<?php echo base_url(get_area_logo()); ?>" alt="" style="width: 75px;height: 55px;">
            <?php } ?>
            </td>
        </tr>

    </table>
    <table class="no-bdr">
        <tr>
            <td class="text-left">
            <span class="generated">In case of any queries, please contact support at <a href="mailto:<?php echo get_option('email_signature'); ?>"><?php echo get_option('email_signature'); ?></a></span>

            </td>
        </tr>
    </table>
    <?php
    // $project_id = ($ticketDetails->project_id) ? $ticketDetails->project_id : '';
    $project_name = !empty($ticketDetails->project_name) ? ucfirst($ticketDetails->project_name) : '';
    $landmark = !empty($ticketDetails->landmark) ? $ticketDetails->landmark : '';
    $projectStatus = !empty($ticketDetails->project_status) ? $ticketDetails->project_status : '';
    $frozen = !empty($ticketDetails->frozen) ? $ticketDetails->frozen : 0;
    $sub_ticket_id = !empty($ticketDetails->sub_ticket_id) ? $ticketDetails->sub_ticket_id : 0;
    $parent_id = !empty($ticketDetails->parent_id) ? $ticketDetails->parent_id : 0;
    $title = $project_name . '<span> near, </span>' . $landmark;
    $ticket_id = !empty($ticketDetails->sub_id) ? $ticketDetails->sub_id : $project_id;

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
    <div class=" content pT0">
        <div class="box-wrapper"></div>
        <input type="hidden" class="ticketPopupId" value="<?php echo $project_id; ?>" />
        <div class='thetop' id="section2"></div>
        <div class="box-wrapper">

            <h2>Project Details</h2>
            <table class="mB15">
                <tr>
                    <td>Project ID: <strong><?php echo $ticket_id; ?></strong></td>
                    <?php if ($frozen) { ?>
                    <td class="frozen text-center">FROZEN</td>
                    <?php } ?>
                    <td class="text-right">Raised On: <strong><?php echo !empty($ticketDetails->logged_date) ? setDateFormat($ticketDetails->logged_date) : ''; ?></strong></td>
                </tr>
            </table>
            <div class="tab-content faq-cat-content ">
                <div class="tab-pane active in fade" id="ticket-details">
                    <div class="panel-group" id="accordion-cat-1">
                        <div class="panel panel-default">
                            <div id="basic-details" class="panel-collapse ">
                                <div class="panel-body pB0">
                                    <table class="mB15">
                                        <tr>
                                            <td>
                                                <p class="reported-detail" style="font-family: 'Open Sans', sans-serif;"><?php echo $project_name; ?></p>
                                                <!-- <p>This has been reported in (Region), (Subregion) near (Landmark) by (Type of user) </p> -->
                                                <p class="reported-detail">This has been reported in <span class="font-size-14"><?php echo $region; ?></span>, <span class="font-size-14"><?php echo $subregion; ?></span> near <span class="font-size-14"><?php echo $landmark; ?></span> by <span class="font-size-14"><?php echo $source; ?></span></p>

                                                <p class="reported-detail due-date">
                                                    Due Date: <strong class="font-size-14"> <?php echo $taskDueDate; ?></strong>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php if (!empty($tasks)) { ?>
                                        <div class="panel panel-default border-0 box-shadow-0">
                                            <div id="status" class="panel-collapse  status-panel">
                                                <div class="panel-body pL0 pR0">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Milestone(s)</th>
                                                                <!-- <th>Duration</th> -->
                                                                <th>Status</th>
                                                                <th>Start Date</th>
                                                                <!-- <th>Due Date</th> -->
                                                                <th>Assigned To</th>
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

                                                                    $childProjectStatus = get_project_status($subTaskTicketId);
                                                                    if ((empty($childProjectStatus) || $childProjectStatus == 1) && $task['status'] == 2) {
                                                                        $task['status'] = 0;
                                                                    }
                                                                } else {
                                                                    $assignedUserName = !empty($assignedUserDetails->full_name) ? $assignedUserDetails->full_name : '';
                                                                }
                                                                if ($ticketDetails->project_status == 1 && $task['status'] == 2) {
                                                                    $task['status'] = 0;
                                                                }
                                                                if(!empty($ticketDetails->task_status) && $ticketDetails->task_status == 1){
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
                                        <div class="detail-list issue-detail-list pR0">

                                            <!-- Separate  Image and Document Start -->
                                            <?php if (!empty($project_id) && !empty($evidenceImages)) {
                                                $evidenceImageData = array();
                                                if (!empty($evidenceImages)) {
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
                                                if (!empty($latestImages)) {
                                                    foreach ($latestImages as $image) {
                                                        $file_id = $image['id'];
                                                        $file_name = $image['file_name'];
                                                        $file_thumb = $image['subject'];
                                                        $file_optimized = $image['thumbnail_link'];
                                                        
                                                        $milestone = $image['milestone'];
                                                        $latitude = $image['latitude'];
                                                        $longitude = $image['longitude'];
                                                        $filetype = $image['filetype'];

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

                                                ?>
                                                <div class="clearfix"></div>
                                                <div class="content">
                                                    <h3>Raised Image(s)</h3>
                                                    <?php if (!empty($evidenceImageData['image'])) { ?>
                                                        <table>
                                                            <tr>
                                                                <?php
                                                                foreach ($evidenceImageData['image'] as $imageData) {
                                                                    $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                    $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                    $file_thumb = !empty($imageData['file_thumb']) ? $imageData['file_thumb'] : '';
                                                                    $file_optimized = !empty($imageData['file_optimized']) ? $imageData['file_optimized'] : '';
                                                                    $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                ?>
                                                                    <td class="lightgallery">
                                                                        <figure>
                                                                            <a href="<?php echo base_url($imgPath . $file_optimized); ?>" target="_blank">
                                                                                <img class="" src="<?php echo base_url($imgPath . $file_thumb); ?>">
                                                                            </a>
                                                                            <?php echo $loc; ?>
                                                                        </figure>
                                                                    </td>
                                                                <?php } ?>
                                                            </tr>

                                                        </table>
                                                    <?php   } else { ?>
                                                        <span class="text-warning no-doc"><i class="fa fa-warning"></i>No evidence image available</span>
                                                    <?php } ?>
                                                </div>
                                                <div class="clearfix"></div>

                                                <div class="content">
                                                    <h3>Raised Document(s)</h3>
                                                    <table>
                                                        <tr>
                                                            <?php if (!empty($evidenceImageData['document'])) {
                                                                foreach ($evidenceImageData['document'] as $imageData) {
                                                                    $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                    $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                    $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                            ?>
                                                                    <td class="lightgallery">
                                                                    <figure>
                                                                        <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                            <img class="print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>">
                                                                        </a>
                                                                        </figure>
                                                                    </td>
                                                                <?php }
                                                            } else { ?>

                                                                <span class="text-warning no-doc"><i class="fa fa-warning"></i> No document available</span>
                                                            <?php } ?>
                                                        </tr>
                                                    </table>
                                                </div>
                                                
                                                <?php if(!empty($latestImageData )){ ?>
                                                    <div class="clearfix"></div>
                                                    <div class="content">
                                                        <h3>Latest Image(s)</h3>
                                                        <?php if (!empty($latestImageData['image'])) { ?>
                                                            <table>
                                                                <tr>
                                                                    <?php
                                                                    foreach ($latestImageData['image'] as $imageData) {
                                                                        $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                        $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                        $file_thumb = !empty($imageData['file_thumb']) ? $imageData['file_thumb'] : '';
                                                                        $file_optimized = !empty($imageData['file_optimized']) ? $imageData['file_optimized'] : '';

                                                                        $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                    ?>
                                                                        <td class="lightgallery">
                                                                            <figure>
                                                                                <a href="<?php echo base_url($imgPath . $file_optimized); ?>" target="_blank">
                                                                                    <img class="" src="<?php echo base_url($imgPath . $file_thumb); ?>">
                                                                                </a>
                                                                                <?php echo $loc; ?>
                                                                            </figure>
                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>


                                                            </table>
                                                        <?php   } else { ?>
                                                            <span class="text-warning no-doc"><i class="fa fa-warning"></i>No evidence image available</span>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="clearfix"></div>

                                                    <div class="content">
                                                        <h3>Latest Document(s)</h3>
                                                        <table>
                                                            <tr>
                                                                <?php if (!empty($latestImageData['document'])) {
                                                                    foreach ($latestImageData['document'] as $imageData) {
                                                                        $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                        $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                        $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                ?>
                                                                        <td class="lightgallery">
                                                                        <figure>
                                                                            <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                <img class="print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>">
                                                                            </a>
                                                                            </figure>
                                                                        </td>
                                                                    <?php }
                                                                } else { ?>

                                                                    <span class="text-warning no-doc"><i class="fa fa-warning"></i> No document available</span>
                                                                <?php } ?>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                <?php } ?>
                                                

                                                <!-- Separate  Image and Document End -->
                                            <?php } else { ?>

                                                <span class="text-warning no-doc"><i class="fa fa-warning"></i> No evidence available</span>
                                            <?php } ?>

                                            <h3>Raised Comment</h3>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <span><?php echo !empty($ticketDetails->description) ? $ticketDetails->description : ''; ?></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="page-break-after: always;"></div>
                <!-- Ticket History -->
                <h2>Project History</h2>
                <div class="tab-pane fade" id="ticket-history">
                    <div class="clearfix"></div>
                    <div class="activity-feed" style="color: #000">
                        <?php foreach ($activities as $activity) { ?>
                            <div class="feed-item">
                                <div class="feed-container">
                                    <div class="">
                                        <div class="">
                                            <div class="date"><span class="text-has-action"><?= date_format(new DateTime($activity["dateadded"]), "d-M-Y") ?></span></div>
                                            <?php $description =  ($activity["description"] == 'project_frozen')? 'Project frozen' : $activity["description"]; ?>
                                            <div class="text">
                                                <p class="mB10"><?= $description ?>
                                                    <strong>
                                                        <?= !empty($activity["staff_data"]["name"]) ? " - " . trim($activity["staff_data"]["name"]) : ""; ?>
                                                        <?php echo !empty($activity["staff_data"]["email"]) ? ', <a href="mailto:' . $activity["staff_data"]["email"] . '">' . $activity["staff_data"]["email"] . '</a>' : ''; ?>
                                                    </strong>
                                                    <strong>
                                                        <?= !empty($activity["staff_data"]["phone"]) ? ", " . $activity["staff_data"]["phone"] : ""; ?>
                                                        <?= !empty($activity["staff_data"]["organisation"]) ? ", " . $activity["staff_data"]["organisation"] : ""; ?>
                                                    </strong>
                                                </p>
                                                <?php if (!empty($activity["additional_data"]->parent_ticket_id)) { ?>
                                                    <p class="no-mbot mtop5 mB10">Parent Project ID: <strong><?php echo $activity["additional_data"]->parent_ticket_id; ?></strong></p>
                                                <?php } ?>
                                                <?php if (!empty($activity["task_name"])) { ?>
                                                    <p class="mB10 mtop5"> Milestone: <strong><?php echo $activity["task_name"]; ?></strong></p>
                                                <?php }
                                                if (!empty($activity["additional_data"]->assigned_details)) { ?>
                                                    <p class="no-mbot mtop5 mB10"><strong><?php echo $activity["additional_data"]->assigned_details; ?></strong></p>
                                                <?php }
                                                if (!empty($activity["additional_data"]->comment)) { ?>
                                                    <p class="no-mbot mtop5 mB10">Comment: <strong><?php echo $activity["additional_data"]->comment; ?></strong></p>
                                                <?php }
                                                if (!empty($activity["additional_data"]->unassigned_detail)) { ?>
                                                    <p class="no-mbot mtop5 mB10"><strong><?php echo $activity["additional_data"]->unassigned_detail; ?></strong></p>
                                                <?php }
                                                if (!empty($activity["additional_data"]->child_ids)) {
                                                    $childIds = json_decode($activity["additional_data"]->child_ids);
                                                    // pre($childIds);       
                                                ?>
                                                    <p class="no-mbot mtop5 mB10">
                                                            <?php foreach ($childIds as $subid) {
                                                                $cStaffId = !empty($subid->staff_id) ? get_staff($subid->staff_id) : '';
                                                            ?>
                                                                <p class="m-0 font-size-11"><?php echo $subid->sub_id; ?>
                                                                    <strong>
                                                                        <?= !empty($cStaffId->firstname) ? " - " . trim($cStaffId->firstname) : ""; ?>
                                                                        <?php echo !empty($cStaffId->email) ? ', <a href="mailto:' . $cStaffId->email . '">' . $cStaffId->email . '</a>' : ''; ?>
                                                                    </strong>
                                                                    <strong>
                                                                        <?= !empty($cStaffId->phonenumber) ? ", " . $cStaffId->phonenumber : ""; ?>
                                                                        <?= !empty($cStaffId->organisation) ? ", " . $cStaffId->organisation : ""; ?>
                                                                    </strong>
                                                            </p>
                                                            <?php } ?>
                                                        
                                                    </p>
                                                <?php  } ?>

                                                <!-- Separate  Image and Document Start -->
                                                <?php if (!empty($activity["evidences"])) { ?>
                                                    <div id="issue-details" class="panel-collapse ">
                                                        <div class="panel-body pL0">
                                                            <div class="detail-list issue-detail-list">
                                                                <!-- Separate  Image and Document Start -->
                                                                <?php
                                                                $evidenceImageData = array();
                                                                $milestoneId = '';
                                                                foreach ($activity["evidences"] as $image) {
                                                                    $file_id = $image['id'];
                                                                    $file_name = $image['file_name'];
                                                                    $milestone = $image['milestone'];
                                                                    $latitude = $image['latitude'];
                                                                    $longitude = $image['longitude'];
                                                                    $filetype = $image['filetype'];
                                                                    $project_id = $image['project_id'];
                                                                    $milestoneId = $milestone;

                                                                    // $imgPath = 'uploads/projects/' . $project_id . '/';
                                                                    $imgPath = 'uploads/tasks/' . $image['milestone'] . '/';

                                                                    $location = '';
                                                                    if (!empty($latitude) && !empty($longitude)) {
                                                                        // $url = base_url('/admin/tickets/viewmap/' . $project_id . '?lat=' . $latitude . '&lang=' . $longitude . '&output=embed');
                                                                        // $location = '<a href="' . $url . '" class="mT10 text-center d-block" target="_blank">View Location</a>';
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
                                                                            'location' => $location,
                                                                            'filetype' => $filetype
                                                                        );
                                                                    }
                                                                }
                                                                // pre($evidenceImageData);
                                                                $imageActive = '';
                                                                $docActive = '';
                                                                if (!empty($evidenceImageData['image'])) {
                                                                    $imageActive = ' active in ';
                                                                    $docActive = '';
                                                                } else {
                                                                    $imageActive = '';
                                                                    $docActive = ' active in ';
                                                                }
                                                                ?>

                                                                <h3>Image(s)</h3>
                                                                <div class="tab-pane <?= $imageActive ?> fade  pr-mT0" id="image">
                                                                    <div class="content pr-mT0 print-p-0 pB10">
                                                                        <?php if (!empty($evidenceImageData['image'])) { ?>
                                                                            <table>
                                                                                <tr>
                                                                                    <?php
                                                                                    foreach ($evidenceImageData['image'] as $imageData) {
                                                                                        $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                                        $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                                        $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                                    ?>
                                                                                        <td class="lightgallery">
                                                                                            <figure>
                                                                                                <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                                    <img class="" src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                                </a>
                                                                                                <?php echo $loc; ?>
                                                                                            </figure>
                                                                                        </td>
                                                                                    <?php } ?>
                                                                                </tr>


                                                                            </table>
                                                                        <?php   } else { ?>
                                                                            <span class="text-warning no-doc"><i class="fa fa-warning"></i>No evidence image available</span>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                                <h3>Document(s)</h3>
                                                                <div class="" id="">
                                                                    <table>
                                                                        <tr>
                                                                            <?php if (!empty($evidenceImageData['document'])) {
                                                                                foreach ($evidenceImageData['document'] as $imageData) {
                                                                                    $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                                    $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                                    $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                            ?>
                                                                                    <td class="lightgallery">
                                                                                    <figure>
                                                                                        <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                            <img class="print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>">
                                                                                        </a>
                                                                                        </figure>
                                                                                    </td>
                                                                                <?php }
                                                                            } else { ?>
                                                                                <span class="text-warning no-doc m-0">No document available</span>
                                                                            <?php } ?>
                                                                        </tr>
                                                                    </table>
                                                                </div>


                                                                <!-- Separate  Image and Document End -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- Separate  Image and Document END -->

                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <?php if($activity["description"] == 'project_frozen' && !empty($activity["updated_at"])){ ?>
                                <div class="feed-item">
                                    <div class="feed-container">
                                        <div class="">
                                            <div class="">
                                                <div class="date">
                                                    <span class="text-has-action"><?= date_format(new DateTime($activity["updated_at"]), "d-M-Y") ?></span>
                                                </div>
                                                <div class="text">
                                                    <p class="mB10">Project unfrozen</p>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } 
                        }
                        ?>
                    </div>
                </div>
                <!-- Ticket History Ends Here -->
            </div>
        </div>

            <!-- <p class="text-center footer">In case of any queries, please contact us at <a href="mailto:info@a-pag.org">info@a-pag.org</a></p> -->


        </body>

</html>
<div class="clearfix"></div>
<div class="activity-feed" style="color: #000">
    <?php foreach ($activities as $activity) { ?>
        <div class="feed-item">
            <div class="feed-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="date">
                            <span class="text-has-action"><?= date_format(new DateTime($activity["dateadded"]), "d-M-Y") ?></span>
                        </div>
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
                                        <ul>
                                            <?php foreach ($childIds as $subid) { 
                                                $cStaffId = !empty($subid->staff_id)?get_staff($subid->staff_id):'';
                                                ?>
                                                <li><?php echo $subid->sub_id; ?>
                                                    <strong> 
                                                        <?= !empty($cStaffId->firstname) ? " - " . trim($cStaffId->firstname) : ""; ?>
                                                        <?php echo !empty($cStaffId->email) ? ', <a href="mailto:' . $cStaffId->email . '">' . $cStaffId->email . '</a>' : ''; ?>
                                                    </strong>
                                                    <strong>
                                                        <?= !empty($cStaffId->phonenumber) ? ", " . $cStaffId->phonenumber : ""; ?>
                                                        <?= !empty($cStaffId->organisation) ? ", " . $cStaffId->organisation : ""; ?>
                                                    </strong>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                </p>
                            <?php  } ?>

                            <!-- Separate  Image and Document Start -->
                            <?php if (!empty($activity["evidences"])) { ?>
                                <div id="issue-details" class="panel-collapse ">
                                    <div class="panel-body pL0">
                                        <ul class="detail-list issue-detail-list">
                                        <!-- Separate  Image and Document Start -->
                                        <?php 
                                            $evidenceImageData = array();
                                            $milestoneId = '';
                                            foreach ($activity["evidences"] as $image) {
                                                $file_id = $image['id'];
                                                $file_name = $image['file_name'];
                                                $file_thumb = $image['subject'];
                                                $file_optimized = $image['thumbnail_link'];
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
                                            // pre($evidenceImageData);
                                            $imageActive = '';
                                            $docActive = '';
                                            if (!empty($evidenceImageData['image'])) {
                                                $imageActive = ' active in ';
                                                $docActive = '';
                                            }else{
                                                $imageActive = '';
                                                $docActive = ' active in ';
                                            }
                                            ?>

                                                <ul class="nav nav-tabs w100P ticket-detail-tabs">
                                                    <li class="<?= $imageActive ?> print-none"><a href="#image_<?=$activity["id"]?>" data-toggle="tab">Image(s)</a></li>
                                                    <li class="<?= $docActive ?> print-none"><a href="#document_<?=$activity["id"]?>" data-toggle="tab">Document(s)</a></li>
                                                </ul>
                                                
                                                <ul class="nav nav-tabs only-print">
                                                    <li class="" style="font-size: 14px; font-weight: 700; margin-top:15px;">Image(s)</li>
                                                </ul>
                                                <div class="clearfix "></div>

                                                <div class="tab-content faq-cat-content">
                                                    <div class="panel-body pL0">
                                                        <div class="tab-content">
                                                            <!-- Images -->
                                                            <div class="tab-pane <?= $imageActive ?> fade  pr-mT0" id="image_<?=$activity["id"]?>">
                                                                <div class="content pr-mT0 print-p-0">
                                                                <?php  if(!empty($evidenceImageData['image'])){ ?>
                                                                    <ul class="row lightgallery">
                                                                        <?php
                                                                        // if(!empty($evidenceImageData['image']))
                                                                        foreach ($evidenceImageData['image'] as $imageData) {
                                                                            $imgPath = !empty($imageData['imgPath']) ? $imageData['imgPath'] : '';
                                                                            $file_name = !empty($imageData['file_name']) ? $imageData['file_name'] : '';
                                                                            $file_thumb = !empty($imageData['file_thumb']) ? $imageData['file_thumb'] : '';
                                                                            $file_optimized = !empty($imageData['file_optimized']) ? $imageData['file_optimized'] : '';
                                                                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-12 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
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
                                                                        if(!empty($evidenceImageData['image']))
                                                                        foreach ($evidenceImageData['image'] as $imageData) {
                                                                            $loc = !empty($imageData['location']) ? $imageData['location'] : '';
                                                                        ?>
                                                                            <li class="col-xs-12 col-sm-4 col-md-3 text-center" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
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
                                                            <div class="tab-pane <?= $docActive ?> fade" id="document_<?=$activity["id"]?>">
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
                                                                            <li class="col-xs-12 col-sm-4 col-md-3" data-responsive="<?php echo base_url($imgPath . $file_name); ?>" data-src="<?php echo base_url($imgPath . $file_name); ?>">
                                                                                <a href="<?php echo base_url($imgPath . $file_name); ?>" target="_blank">
                                                                                    <figure><img class="img-responsive print-document-icon" src="<?php echo base_url('/assets/images/pdf-icon.png'); ?>"></figure>
                                                                                </a>
                                                                            </li>
                                                                        <?php }
                                                                    } else { ?>
                                                                        <p class="text-warning mT10"><i class="fa fa-warning"></i> No document available</p>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- Separate  Image and Document End -->
                                        </ul>
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
                    <div class="row">
                        <div class="col-md-12">
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
</script>
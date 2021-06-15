
<div class="content pT0">
<div id="issue-details" class="panel-collapse ">
    <div class="modal-header">
        <h4 class="modal-title"><span class="add-title">Evidence</span></h4>
    </div>
    <div class="panel-body pL0 pR0 evidenceTabs">
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
                            // $url = "https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '&output=embed";
                            // echo $url.'<br>';
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
                if(!empty($latestImages)){
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
                            //$url = base_url('/admin/tickets/viewmap/' . $project_id . '?lat=' . $latitude . '&lang=' . $longitude . '&output=embed');
                            //$location = '<a href="' . $url . '" class="mT10 text-center d-block" target="_blank"><i class="fa fa-map-marker"></i>View Location</a>';

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
                    <li class="<?= $imageActive ?> print-none"><a href="#raisedImage" data-toggle="tab">Raised Image(s)</a></li>
                    <li class="<?= $docActive ?> print-none"><a href="#raisedDocument" data-toggle="tab">Raised Document(s)</a></li>
                <?php if(!empty($latestImageData )){ ?>
                    <li class="mT0 pL15 pT5 mT0 mL15 md-hide" style="border-left: 1px solid #ddd"><a style="border-radius:0" class="p-0 deskH35"></a></li>
                    <li class="<?= $latImageActive ?> print-none mL15"><a href="#latestImage" data-toggle="tab">Latest Image(s)</a></li>
                    <li class="<?= $latDocActive ?> print-none"><a href="#latestDocument" data-toggle="tab">Latest Document(s)</a></li>
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
                            <div class="tab-pane <?= $imageActive ?> fade  pr-mT0" id="raisedImage">
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
                            <div class="tab-pane <?= $docActive ?> fade" id="raisedDocument">
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
                            <div class="tab-pane <?= $latImageActive ?> fade" id="latestImage">
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
                            <div class="tab-pane <?= $latDocActive ?> fade" id="latestDocument">
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
        </ul>
    </div>
</div>
</div>
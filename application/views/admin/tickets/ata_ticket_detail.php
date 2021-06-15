<div class="panel panel-default sub-ticket-panel">
    <?php if ($GLOBALS['current_user']->role_slug_url == "ata"  || (get_staff_user_id() == $assignedUser)) { ?>
        <div class="panel-body pT0">
            <?php if (isset($wip_task)) { ?>
                <div class="create-dropdown pT0">
                    <div class="row mB20">
                        <div class="col-lg-12 mB10">
                            <ul class="detail-list">
                                <li class="mT10">
                                    <span><?= $wip_task['task_name']; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <input type="hidden" name="project_id" id="project_id" value=<?= $ticketDetails->project_id; ?>>
                            <input type="hidden" name="task_id" id="task_id" value=<?= $wip_task["task_id"] ?>>
                            <div class="form-group file-group row">
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file1">
                                            <img class="evidence-pre" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>
                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file1" class="form-control evidence-uploader" filesize="<?php echo file_upload_max_size(); ?>">
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file2">
                                            <img class="evidence-pre" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>

                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file2" class="form-control evidence-uploader" filesize="<?php echo file_upload_max_size(); ?>">
                                        <!-- <span class="img-label">Upload Evidence</span> -->
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file3">
                                            <img class="evidence-pre" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>

                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file3" class="form-control evidence-uploader" filesize="<?php echo file_upload_max_size(); ?>">
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file4">
                                            <img class="evidence-pre" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>
                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file4" class="form-control evidence-uploader" filesize="<?php echo file_upload_max_size(); ?>">
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                </div>
                                <p style="color:red; display: none; margin-left: 15px" id="upload_error">Please upload files with type png, jpg, jpeg or PDF only.</p>

                            </div>
                            <?php echo form_error('file'); ?>
                            <div class="open-ticket-message-group">
                                <textarea name="comment" id="comment" class="form-textarea h60" maxlength="500"></textarea>
                                <label for="comment" title="Comment" data-title="Comment"></label>

                                <p style="color:red" id="alertspan"></p>
                            </div>
                        </div>

                    </div>
                    <?php
                    $btnLabel = 'Close';
                    if ($GLOBALS['current_user']->role_slug_url == "at" && $lastMilestone) {
                        $btnLabel = 'Close Project';
                    } ?>
                    <input type="hidden" name="lastMilestone" class="lastMilestone" value="<?php echo $lastMilestone; ?>" />
                    <button class="btn ticket-btn updateMilestone" type="button"><?php echo $btnLabel; ?></button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<script>
    var elements = document.getElementsByClassName("evidence-uploader");
    var imgFileType = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png'];
    var allowedFileType = [...imgFileType, "application/pdf"];
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!allowedFileType.includes(file.type)) {
                document.getElementById("upload_error").style.display = "block";
                return;
            }
            document.getElementById("upload_error").style.display = "none";
            var imgElem = e.target.parentNode.getElementsByClassName("evidence-pre")[0];
            var imgLabel = e.target.parentNode.getElementsByClassName("img-label")[0]
            console.log(imgFileType.includes(file.type))
            if (imgFileType.includes(file.type)) {
                imgElem.src = URL.createObjectURL(file);
                imgElem.className += " cancel"; 
                imgElem.onload = function() {
                    URL.revokeObjectURL(imgElem.src) // free memory
                }
            } else {
                imgElem.src = "<?php echo base_url() . 'assets/images/pdf-icon.png'; ?>";
                imgElem.className += " cancel"; 

            }
            imgLabel.innerHTML = '';
        });
    }
    
    // change by umair 

    $('.del').click(function (e) { 
    var img=$(this).closest('.image-upload').find("label").find('img');
    var file=$(this).closest('.image-upload').find("input");
    img.attr('src',"<?php echo base_url('assets/images/evidence.png') ?>");
    img.removeClass('cancel');
    file.val('')  
});
// change by umair
</script>
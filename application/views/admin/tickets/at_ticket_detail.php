<div class="panel panel-default sub-ticket-panel">
    <?php
    $projectStatus = !empty($ticketDetails->project_status) ? $ticketDetails->project_status : '';
    $project_id = ($ticketDetails->project_id) ? $ticketDetails->project_id : '';
    if ($projectStatus == 1) {
        $userId = $GLOBALS['current_user']->staffid;
        $userName = $GLOBALS['current_user']->full_name;

        $assistantList = '<option value="">Assign To</option>';
        $assistantList .= '<option value="'.$userId .'">'. $userName . ' (Self)</option>';
        if (!empty($assistantDetails))
            foreach ($assistantDetails as $assistant) {
                $name = $assistant['full_name'].'('.$assistant['organisation'].')';
                $assistantList .= '<option value="'.$assistant['staffid'] .'">'. $name . '</option>';
            }

        $exceptionList = '<option value="">Reason</option>';
        if (!empty($exceptionDetails))
            foreach ($exceptionDetails as $exception) {
                $exceptionList .= '<option value="'.$exception['id'] .'">'. $exception['name'] . '</option>';
            }
    ?>
    <div class="panel-body p-0">
        <div class="create-dropdown accept-reject before-seprator p15">
            <div class="row">
                <div class="col-lg-5">
                    <label>Accept Project</label>
                    <div class="form-select-field mB15">
                        <select class="assignTDList">
                            <?php echo $assistantList; ?>
                        </select>
                        <label class="select-label">Assigned To</label>
                    </div>
                    <div class="">
                        <a href="#" class="btn accept-btn pull-right assignTDTicket" data-ticketdetail="ticketDetail">Accept</a>
                    </div>
                </div>
                <p class="or">OR</p>
                <div class="col-lg-2"></div>
                <div class="col-lg-5">
                    <label><?php if($GLOBALS['current_user']->role_slug_url =='at') { 
                        echo "Refer Project"; 
                    }else{
                        echo "Reject Project";
                    }?>
                    </label>
                    <div class="form-select-field mB15">
                        <select class="rejectTDList">
                            <?php echo $exceptionList; ?>
                        </select>
                        <label class="select-label">Select Reason</label>
                    </div>
                    <div class="otherTDReason hide mB10">
                        <textarea id="other-area" name="otherTDException" class="form-textarea otherTDException"></textarea>
                        <label for="other-area" title="Reason" data-title="Reason"></label>
                    </div>
                    <div class="">
                        <a href="#" class="btn reject-btn pull-right rejectTDTicket" data-ticketdetail="ticketDetail">
                        <?php if($GLOBALS['current_user']->role_slug_url =='at') { 
                        echo "Refer"; 
                        }else{
                            echo "Reject";
                        }?>
                        </a>
                    </div>
                </div>
                <input type="hidden" name="rejectProjectId" class="rejectProjectId" value="<?php echo $project_id; ?>" />
                <div class="col-lg-6 mT10 ticketDetailsRejection hide">
                    <div class="form-group mB0">
                        <div class="form-input-field mB0">
                            <input type="hidden" name="exceptionId" class="exceptionId" value="" />
                            
                            <input type="text" name="detialOtherException" class="otherException">
                            <label for="name" title="Reason" data-title="Reason"></label>
                            <label class="btn-container mT10"><a href="#" class="text-white semibold btn reject-btn submitException" data-ticketdetail="ticketDetail">Reject Project</a>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php } else if ($projectStatus == 4 && $milestoneStatus == 4) { ?>
    <div class="panel-body">
        <div class="create-dropdown pT0">
            <div class="row">
                <div class="col-lg-12 action-ticket-btn">
                    <label class="btn-container"><a href="javascript:void(0)" class="semibold btn accept-btn close-ticket" data-ticketdetail="ticketDetail" data-projectid="<?php echo $project_id; ?>">Close</a>
                    </label>
                    <label class="btn-container"><a href="javascript:void(0)" class="semibold btn reject-btn reopen-ticket mL5" data-projectid="<?php echo $project_id; ?>">Reopen</a>
                    </label>
                </div>

                <div class="col-lg-6 mT10 reopenTicketComment hide">
                    <div class="form-group mB0">
                        <div class="form-input-field mB0">
                            <input type="hidden" name="reopenProjectId" class="reopenProjectId" value="<?php echo $project_id; ?>" />
                            <input type="text" name="reopenReason" class="reopenReason">
                            <label for="reopenReason" title="Reason" data-title="Reason"></label>
                        </div>
                        <label class="btn-container mT10">
                            <a href="javascript:void(0)" class="semibold btn reject-btn reopenTicket" data-ticketdetail="ticketDetail" data-projectid="<?php echo $project_id; ?>">Reopen Project</a>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php } ?>
</div>
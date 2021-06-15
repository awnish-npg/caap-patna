
<div class="panel panel-default sub-ticket-panel  border-top-0">
    <?php
    $projectStatus = !empty($ticketDetails->project_status) ? $ticketDetails->project_status : '';
    $project_id = ($ticketDetails->project_id) ? $ticketDetails->project_id : '';
    $parent_id = !empty($ticketDetails->parent_id) ? $ticketDetails->parent_id : 0;
    $sub_ticket_id = !empty($ticketDetails->sub_ticket_id) ? $ticketDetails->sub_ticket_id : 0;
    /*
    5 => Rejected =>
        Close the ticket
        Extend deadlines & Reassign the ticket
        Create sub-tickets

    3 => closed =>
        Reopen the ticket
    */
    if ($projectStatus == 3 && empty($sub_ticket_id) && $activeUser) { ?>
        <div class="panel-body">
            <div class="create-dropdown pT0">
                <div class="row">
                    <!-- <div class="col-lg-12 action-ticket-btn">
                        <label class="btn-container"><a href="javascript:void(0)" class=" semibold btn reject-btn reopen-ticket" data-projectid="<?php echo $project_id; ?>">Reopen</a>
                        </label>
                    </div> -->

                    <div class="col-lg-6 mT10 reopenTicketComment ">
                        <div class="form-group mB0">
                            <div class="form-input-field mB0">
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
    <?php } else if ($projectStatus == 5) { 
        $arAssistantList = '';
        if (!empty($atLists)){
            foreach ($atLists as $at) {
                $selected = ($assignedUser == $at['staffid'])?'selected':'';
                $name = $at['full_name'].'('.$at['organisation'].')';
                $arAssistantList .= '<option value="'.$at['staffid'] .'" '.$selected.'>'. $name  . '</option>';
            }
        }
        if(empty($sub_ticket_id)){
        ?>
            <div class="panel-body">
                <div class="create-dropdown pT0">
                    <div class="row">
                        <div class="col-lg-6 action-ticket-btn">
                            <label class="mB10">What do you want to do?</label>
                            <div class="form-group form-select-field singleSelect">
                                <select class="ar-action">
                                    <option value="">Select</option>
                                    <option value="1">Close Project</option>
                                    <option value="2">Edit Project</option>
                                    <?php if(empty($parent_id) && !empty($tasks) && count($tasks) > 1){ ?>
                                        <option value="3">Create Multiple Projects</option>
                                    <?php } ?>
                                </select>
                                <label class="select-label">Action</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="milestone-cnt" class="milestone-cnt" value="<?php echo count($tasks); ?>">

            <!-- AR Close Ticket Action -->
            <div class="ar-close-ticket hide">
                <div class="panel-body panel-border-top">
                    <div class="form-group mB0 col-lg-6 mT10 pL0">
                        <label class="mB10">Close Project</label>
                        <div class="form-input-field mB0">
                            <input type="text" name="closeReason" class="closeReason">
                            <label for="closeReason" title="Reason" data-title="Reason"></label>
                        </div>
                        <div class="btn-container mT10"><a href="javascript:void(0)" class="semibold btn reject-btn arCloseTicket" data-projectid="<?php echo $project_id; ?>">Close Project</a>
                            </div>
                    </div>
                </div>
            </div>
            <!-- AR Close Ticket Action END-->

            <!-- AR Extend Deadline Action -->
            <div class="ar-extend-deadline hide">
                <div class="panel-body panel-border-top">
                    <div class="create-dropdown pT0">
                        <div class="row">
                            <div class="col-lg-6 action-ticket-btn">
                                <label class="mB10">Reassign Project To</label>
                                <div class="form-group form-select-field singleSelect">
                                    <select class="ar-reassignment-action w100P selectpicker" name="ar-reassignment-action">
                                        <option value="">Project Leader</option>
                                        <?php echo $arAssistantList; ?>
                                    </select>
                                    <label class="select-label">Project Leader</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body panel-border-top">
                    <div class="create-dropdown pT0">
                        <div class="row">
                            <div class="col-lg-9 action-ticket-btn">
                                <label class="mB20">Extend Deadlines</label>
                                <div class="form-group extended-table table-scroll">
                                    <table class="table table-borderless ticket-milestone table-fixed">
                                        <tbody>
                                            <tr>
                                                <td class="w120 pB0 pT0"><label class="table-label">Milestones</label></td>
                                                <td class="w120 pB0 pT0"><label class="table-label">Start Date</label></td>
                                                <td class="w120 pB0 pT0"></td>
                                            </tr>
                                            <?php 
                                            if(!empty($tasks))
                                            foreach ($tasks as $key => $task) {
                                                $tKey = $key + 1;
                                            ?>
                                                <tr>
                                                    <td class="w120">
                                                        <span class="reassignment_name_<?= $tKey ?>"><?= $task['task_name'] ? $task['task_name'] : ""; ?></span>
                                                    </td>
                                                    <td class="w120"><span class="w120 reassignment_start_date_<?= $tKey ?>"><?= setDateFormat(date('Y-m-d')) ?></span></td> 
                                                    <td class="w180">
                                                        <div class="form-input-field mB0">
                                                            <input type="text" class="datepicker datepicker1 label-up reassignment_date reassignment_due_date_<?= $tKey ?>" id="reassignment_due_date_<?= $tKey ?>" name="reassignment_due_date_<?= $tKey ?>" data-milestone="<?= $tKey ?>">
                                                            <label for="reassignment_due_date_<?= $tKey ?>" class="control-label" title="Due Date" data-title="Due Date"></label>
                                                            <div class="input-group-addon date-icon">
                                                                <i class="fa fa-calendar calendar-icon"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <input type="hidden" name="reassignment_days_<?= $tKey ?>" class="reassignment_days_<?= $tKey ?>" value="<?= $task['task_days'] ?>">
                                                    <input type="hidden" name="reassignment_milestoneid_<?= $tKey ?>" class="reassignment_milestoneid_<?= $tKey ?>" value="<?= $task['task_id'] ?>">
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body pT0">
                    <button type="button" id="extend-deadline-submit" class="btn btn-custom extend-deadline-submit">Update</button>
                </div>
            </div>
            <!-- AR Extend Deadline Action END-->

            <!-- AR Sub Ticket Action -->
            <div class="ar-sub-ticket hide">
                <div class="panel-body panel-border-top">
                    <div class="create-dropdown pT0">
                        <div class="row">
                            <div class="col-lg-6 action-ticket-btn">
                                <label class="mB10">Reassign Project To</label>
                                <div class="form-group form-select-field singleSelect">
                                    <select class="ar-subtask-action w100P selectpicker" name="ar-subtask-action">
                                        <option value="">Project Leader</option>
                                        <?php echo $arAssistantList; ?>
                                    </select>
                                    <label class="select-label">Project Leader</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body panel-border-top">
                    <div class="create-dropdown pT0">
                        <div class="row">
                            <div class="col-lg-11 action-ticket-btn">
                                <label class="mB20">Projects</label>
                                <div class="form-group extended-table">
                                    <table class="table table-borderless ticket-milestone">
                                        <tbody>
                                            <tr>
                                                <td class="w120 pT0 pB0"><label class="table-label">Milestones</label></td>
                                                <td class="w120 pT0 pB0"><label class="table-label">Start Date</label></td>
                                                <td pT0 pB0></td>
                                                <td pT0 pB0></td>
                                            </tr>
                                            <?php 
                                            if(!empty($tasks))
                                            foreach ($tasks as $key => $task) {
                                                $tKey = $key + 1;
                                            ?>
                                                <tr>
                                                    <td class="w120">
                                                        <span class="subtask_name_<?= $tKey ?>"><?= $task['task_name'] ? $task['task_name'] : ""; ?></span>
                                                    </td>
                                                    <td class="w120"><span class="w120 subtask_start_date_<?= $tKey ?>"><?= date('Y-m-d') ?></span></td> 
                                                    <td class="w180">
                                                        <div class="form-input-field mB0">
                                                            <input type="text" class="datepicker datepicker1 label-up subtask_date subtask_due_date_<?= $tKey ?>" id="subtask_due_date_<?= $tKey ?>" name="subtask_due_date_<?= $tKey ?>" data-milestone="<?= $tKey ?>">
                                                            <label for="subtask_due_date_<?= $tKey ?>" class="control-label" title="Due Date" data-title="Due Date"></label>
                                                            <div class="input-group-addon date-icon">
                                                                <i class="fa fa-calendar calendar-icon"></i>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="form-select-field w180 singleSelect">
                                                        <select class="subtask_at_<?= $tKey ?> subtaskfiled selectpicker" name="subtask_at_<?= $tKey ?>">
                                                            <option value="">Project Leader</option>
                                                            <?php echo $arAssistantList; ?>
                                                        </select>
                                                        <label class="select-label">Project Leader</label>
                                                    </td>
                                                    <input type="hidden" name="subtask_days_<?= $tKey ?>" class="subtask_days_<?= $tKey ?>" value="<?= $task['task_days'] ?>">
                                                    <input type="hidden" name="subtask_milestoneid_<?= $tKey ?>" class="subtask_milestoneid_<?= $tKey ?>" value="<?= $task['task_id'] ?>">
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body pT0">
                    <button type="button" id="subticket-submit" class="btn btn-custom subticket-submit">Update</button>
                </div>
            </div>
            <!-- AR Sub Ticket Action END-->
        <?php }else{ ?>
            <!-- AR Extend Deadline Action -->
            <div class="ar-extend-deadline">
                <div class="panel-body panel-border-top">
                    <div class="create-dropdown pT0">
                        <div class="row">
                            <div class="col-lg-6 action-ticket-btn">
                                <label class="mB10">Reassign Project To</label>
                                <div class="form-group form-select-field singleSelect">
                                    <select class="ar-reassignment-action w100P selectpicker" name="ar-reassignment-action">
                                        <option value="">Project Leader</option>
                                        <?php echo $arAssistantList; ?>
                                    </select>
                                    <label class="select-label">Project Leader</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body pT0">
                    <button type="button" id="reassign-ticket" class="btn btn-custom reassign-ticket">Update</button>
                </div>
            </div>
            <!-- AR Extend Deadline Action END-->

        <?php } ?>
    <?php }  ?>


    <!-- Ajay Code-->

</div>
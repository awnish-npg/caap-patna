<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<input type="hidden" value="<?=$userRole?>" name="userRole" class="userRole" />
<input type="hidden" value="<?=!empty($userDetails->region)?$userDetails->region:''?>" name="uregion" class="uregion" />
<input type="hidden" value="<?=!empty($userDetails->sub_region)?$userDetails->sub_region:''?>" name="usub_region" class="usub_region" />
<input type="hidden" value="<?=!empty($userDetails->staffid)?$userDetails->staffid:''?>" name="ustaffid" class="ustaffid" />
<input type="hidden" class="dashboard" name="dashboard" value="dashboard" />

<div id="wrapper">
    <div class="content">
        <div class="new-dashboard">
        <h2 class="action-head">Summary</h2>

            <div class="summary-section">
                <div class="escalated" onclick="reportFilter(7)">
                    <!-- <figure>
                        <img src="<?php echo base_url('assets/images/esc.png') ?>" alt="">
                    </figure> -->
                    <div>
                        <label class="escalated_total"><?php echo $escalated; ?></label>
                        <span>Delayed</span>
                    </div>
                </div>
                <!-- <div class="new">
                    <figure>
                        <img src="<?php echo base_url('assets/images/new-ticket.png') ?>" alt="">
                    </figure>
                    <div>
                        <label class="new_total"><?php //echo $new; 
                                                    ?></label>
                        <span>New</span>
                    </div>
                </div> -->
                <div class="ongoing" onclick="reportFilter(2)">
                    <!-- <figure>
                        <img src="<?php echo base_url('assets/images/wip.png') ?>" alt="">
                    </figure> -->
                    <div>
                        <label class="ongoing_total"><?php echo $ongoing; ?></label>
                        <span class="">In Progress</span>
                    </div>
                </div>
                <div class="closed" onclick="reportFilter(3)">
                    <!-- <figure>
                        <img src="<?php echo base_url('assets/images/closed.png') ?>" alt="">
                    </figure> -->
                    <div>
                        <label class="closed_total"><?php echo $closed; ?></label>
                        <span>Closed</span>
                    </div>
                </div>
                <!--<div class="total-task">
                    <figure>
                        <img src="<?php echo base_url('assets/images/total.png') ?>" alt="">
                    </figure>
                    <div>
                        <label class="text-black total_act"><?php //echo $total_activity; 
                                                            ?></label>
                        <span class="text-black">Total</span>
                    </div>
                </div> -->
            </div>
            <input type="hidden" name="action_item_list" class="action_item_list" value="<?php echo ACTION_ITEM_LIST; ?>">
            <?php
            $userRole = $GLOBALS['current_user']->role_slug_url;
            if (in_array($userRole, ['at', 'ata'])) { ?>
            <h2 class="action-head">Your Action Items</h2>
                <ul class="nav nav-tabs dashboard-tab">
                    <li class="active action_items_data"><a href="#action-items" data-toggle="tab">Actions Due Today (<span class="action_items_cnt"><?=(!empty($action_items_cnt))?$action_items_cnt:0; ?></span>)</a></li>
                    <li class="upcoming_deadline_data"><a href="#upcoming-deadlines" data-toggle="tab">Upcoming Deadlines (<span class="upcoming_deadline_cnt"><?=(!empty($upcoming_deadline_data_cnt))?$upcoming_deadline_data_cnt:0; ?></span>)</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active in" id="action-items">
                        <div class="dashboard-table action-items-data">
                            <!-- <div class="dashboard-heading">
                                <div class="dashboard-cell w20P">
                                    <p>Action Item</p>
                                </div>
                                <div class="dashboard-cell w15P">
                                    <p>Due Date</p>
                                </div>
                                <div class="dashboard-cell w30P">
                                    <p>Comment</p>
                                </div>
                                <div class="dashboard-cell w15P">
                                    <p>Evidence</p>
                                </div>
                                <?php if ($userRole == 'at') { ?>
                                    <div class="dashboard-cell w20P">
                                        <p>Action</p>
                                    </div>
                                <?php } ?>
                            </div> -->
                            <div id="actionItems" class="table-row-group"></div>
                        </div>
                        <input type="hidden" id="pageno" value="0">
                        <div class="load-btn load-btn-action-item hide">
                            <!-- <a href="javascript:void(0)" class="btn loadMore-btn" id="loadMoreAction">Show More</a> -->
                            <button class="btn loadMore-btn" id="loadMoreAction">Show More</button>
                        </div>
                        <div class="dashboard-table no-action-item-data hide">
                            <p class="text-center no-data-row"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                You have no action items.</p>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="upcoming-deadlines">
                        <div class="dashboard-table upcoming-deadlines-data">
                            <!-- <div class="dashboard-heading">
                                <div class="dashboard-cell w20P">
                                    <p>Action Item</p>
                                </div>
                                <div class="dashboard-cell w15P">
                                    <p>Due Date</p>
                                </div>
                                <div class="dashboard-cell w30P">
                                    <p>Comment</p>
                                </div>
                                <div class="dashboard-cell w15P">
                                    <p>Evidence</p>
                                </div>
                                <?php if ($userRole == 'at') { ?>
                                    <div class="dashboard-cell w20P">
                                        <p>Action</p>
                                    </div>
                                <?php } ?>
                            </div> -->
                            <div id="upcomingItems" class="table-row-group"></div>
                        </div>
                        <input type="hidden" id="nextpageno" value="0">
                        <div class="load-btn load-btn-upcoming_deadline hide">
                            <!-- <a href="javascript:void(0)" class="btn loadMore-btn" id="upcomingDeadlineLoader">Show More</a> -->
                            <button class="btn loadMore-btn" id="upcomingDeadlineLoader">Show More</button>
                        </div>
                        <div class="dashboard-table no-upcoming-deadlines hide">
                            <p class="text-center no-data-row"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                You have no upcoming deadlines.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php $this->load->view('admin/dashboard/dashboard_popup'); ?>

<?php //$this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<style>
    #loader,
    #deadlineloader {
        display: block;
        margin: auto;
    }

    /* .dashboard-row div,
    img {
        cursor: pointer;
    } */
</style>
<?php $this->load->view('admin/dashboard/dashboard_scripts'); ?>
</body>

</html>
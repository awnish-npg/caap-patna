<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade sidebarModal dash-filter" id="modify_ae_filter_modal" tabindex="-1" role="dialog" style="width:40%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('Modify Filter'); ?></span>
                </h4>
            </div>
            <form action="javascript:void(0)"></form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="addition"></div>
                        <p class="form-instruction add-title">Fill in the following fields to filter the projects list.</p>
                    </div>
                    <hr class="hr-panel-model" />
                </div>
                <!-- Filter Section-->
                <div class="form-group">
                    <div class="">
                        <div class="" id="report-time">
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
                </div><!-- form group ends here -->

                <div class="filter-list1">


                    <div id="date-range" class="hide mbot15">
                        <div class="">
                            <div class="form-group mB15 input-form-group">
                                <div class="form-group input-form-group">
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
                        </div>
                        <div class="">
                            <div class="form-group input-form-group">
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


                    <!-- Filter Section-->
                    <div class="form-group">
                        <div class="form-select-field">

                        </div>
                    </div>
                </div><!-- form group ends here -->

                <div class="input-form-group mB15" app-field-wrapper="duration">
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

                <div class="input-form-group" app-field-wrapper="categories">
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
            <!-- Filter Section Ends-->


            <div class="modal-footer">
                <button type="button" onclick="loadTable()" id="search_id" class="btn btn-custom">Apply</button>
                <button type="button" id="search_id" class="btn btn-cancel" data-dismiss="modal">Cancel</button>

            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

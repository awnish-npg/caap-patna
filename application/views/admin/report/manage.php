<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
 $area = $GLOBALS['current_user']->area; 
 $role = $GLOBALS['current_user']->role;

// if($role == 3){
// 	$report_mag_url = admin_url('report/rp?role=at');
// }else if($role == 4){
// 	$report_mag_url = admin_url('report/rp?role=ar');
// }else if($role == 7){
// 	$report_mag_url = admin_url('report/rp?role=ae-area');
// }else if($role == 8){
// 	$report_mag_url = admin_url('report/rp?role=ata');
// }else if($role == 6){
// 	$report_mag_url = admin_url('report/rp?role=aa');
// }else if($role == 5){
// 	$report_mag_url = admin_url('report/rp');
// }
if($role == 3 || $role == 4 || $role == 7 || $role == 8 || $role == 6 || $role == 5){
    $report_mag_url = admin_url('report/rp');
}
?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s custom-panel1">
                    <div class="panel-body">
                        <div class="panel-header">
                            <h1>Report Managment<span>Here you can view the report</span></h1>
                            <hr class="hr-panel-heading" />
                        </div>
                        <div class="">
                            <div class="mB20">
                                <?php echo form_open(); ?>
                                <div class="row">
                                    <?php if($role == 5 ){?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="area">
                                            <div class="form-select-field">
                                                <?php
                                                    $selected = array('');
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('area[]',$areas,array('areaid','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Area', 'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Area</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } if($role == 5 || $role == 7 || $role == 6) { ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="region">
                                            <div class="form-select-field">
                                                <?php                                  
                                                    $selected = array();
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('region[]',$region,array('id','region_name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Region','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Region</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }  if($role == 5 || $role == 7 || $role == 4 || $role == 6) { ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="subregion">
                                            <div class="form-select-field">
                                                <?php
                                                    $selected = array();
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('subregion[]',$subregion,array('id','region_name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Sub-region','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Sub-region</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } if($role == 5 || $role == 7 || $role == 4 || $role == 6) {?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="action_taker">
                                            <div class="form-select-field">
                                                <?php
                                                    $selected = array();
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('action_taker[]',$action_taker,array('staffid','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Action Taker','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Action Taker</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } if($role == 5 || $role == 7 || $role == 6) { ?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="action_reviewer">
                                            <div class="form-select-field">
                                                <?php
                                                    $selected = array();
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('action_reviewer[]',$action_reviewer,array('staffid','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Action Reviewer','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Action Reviewer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if($role != 5 || $role == 6){?>
                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="categories">
                                            <div class="form-select-field">
                                                <?php
                                                    $selected = array();
                                                    echo '<div id="leads-filter-status">';
                                                    echo render_select('category[]',$categories,array('id','issue_name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Category','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                                                    echo '</div>';
                                                ?>
                                                <label class="select-label">Category</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="col-md-3">
                                        <div class="form-group" app-field-wrapper="ticket">
                                        <div class="form-select-field">
                                            <?php
										$selected = array();
										echo '<div id="leads-filter-status">';
                                    	echo render_select('ticket[]',$ticket,array('id','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'All Ticket Status except CLOSED','multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
										echo '</div>';
                                    ?>

                                    <label class="select-label">Ticket Status</label>
                                    </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <div class="form-input-field">
                                                <input type="text" autocomplete="off" name="bug" id="bug_id">
                                                <label for="admin_organisation" title="Ticket Id"
                                                    data-title="Ticket Id"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group " id="report-time">
                                        <div class="form-select-field singleSelect">
                                            <!-- <label for="months-report"><?php // echo _l('period_datepicker'); ?></label><br /> -->
                                            <select class="selectpicker" name="months-report" data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('report_sales_months_all_time'); ?>
                                                </option>
                                                <option value="this_month"  data-subtext="<?php echo _d(date('Y-m-01')); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('this_month'); ?></option>
                                                <option value="last_month"  data-subtext="<?php echo _d(date('Y-m-01', strtotime("-1 MONTH"))); ?> - <?php echo _d(date('Y-m-t', strtotime("-1 MONTH"))); ?>"><?php echo _l('last_month'); ?></option>
                                                <option value="this_year"  data-subtext="<?php echo _d(date('Y-01-01')); ?> - <?php echo _d(date('Y-12-t')); ?>"><?php echo _l('this_year'); ?></option>
                                                <option value="last_year"  data-subtext="<?php echo _d(date('Y-01-01', strtotime("-1 YEAR"))); ?> - <?php echo _d(date('Y-12-t', strtotime("-1 YEAR"))); ?>"><?php echo _l('last_year'); ?></option>
                                                <option value="3"
                                                    data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>">
                                                    <?php echo _l('report_sales_months_three_months'); ?></option>
                                                <option value="6"
                                                    data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>">
                                                    <?php echo _l('report_sales_months_six_months'); ?></option>
                                                <option value="12"
                                                    data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>">
                                                    <?php echo _l('report_sales_months_twelve_months'); ?></option>
                                                <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                                            </select>
                                    <label class="select-label">All Time</label>
                                    </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div id="date-range" class="row hide mbot15">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-input-field date">
                                                        <input type="text" class="datepicker" id="report-from"
                                                            name="report-from">
                                                        <label for="report-from" class="control-label"
                                                            title="<?php echo _l('report_sales_from_date'); ?>"
                                                            data-title="<?php echo _l('report_sales_from_date'); ?>"></label>
                                                        <div class="input-group-addon date-icon">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
												<div class="form-group">
                                                    <div class="form-input-field date">
                                                        <input type="text" class="datepicker"
                                                            disabled="disabled" id="report-to" name="report-to">
                                                            <label for="report-to"
                                                        class="control-label" title="<?php echo _l('report_sales_to_date'); ?>" data-title="<?php echo _l('report_sales_to_date'); ?>"></label>
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
                                            <button type="submit" id="search_id"
                                                class="btn btn-custom"><?php echo _l('filter'); ?></button>
                                             <button type="submit" id="download"
                                                class="btn btn-download mL5">Download</button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>

                        <?php render_datatable(array(
                            _l('Ticket Id'),
                           	_l('Action Item'),
                            _l('Date Assigned'),
                            _l('Comment'),
                            _l('Evidence'),
                        ), 'reportmng'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Image Evidence Popup -->
<div class="modal sidebarModal fade ticket-detail-modal" id="sidebarModal">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>    
<div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form autocomplete="off" action="javascript:void(0)" id="area_admin_form" method="post"
            accept-charset="utf-8" novalidate="novalidate">
            <div class="modal-content">
                <div class="modal-header">
                $areaname           
                </div>
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
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>    
<div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form autocomplete="off" action="javascript:void(0)" id="area_admin_form" method="post"
            accept-charset="utf-8" novalidate="novalidate">
            <div class="modal-content">
                <div class="modal-header">
                Email is successfully sent                 
                </div>
                <div class="modal-body">
                    <div class="row">  
                        <div class="col-md-12">
                            <div id="search_filter"></div>
                        </div>  
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Close</button>
                </div>         
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>
</div>
<?php $this->load->view('admin/dashboard/dashboard_popup'); ?>
<?php init_tail(); ?>

<script>
        $(function() {

			var report_from = $('input[name="report-from"]');
       		var report_to = $('input[name="report-to"]');
            var date_range = $('#date-range');
            
            var fnServerParams = {
				"report_months": '[name="months-report"]',
				"report_from": '[name="report-from"]',
				"report_to": '[name="report-to"]',
				"area": '[name="area[]"]',
				"region": '[name="region[]"]',
				"subregion": '[name="subregion[]"]',
				"action_taker": '[name="action_taker[]"]',
				"action_reviewer": '[name="action_reviewer[]"]',
                "category": '[name="category[]"]',
				"bug": '[name="bug"]',
				"ticket": '[name="ticket[]"]',
                }	
                
            var columnDefs = [{ "width": "10%" },{ "width": "30%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%", "className": "dt_center_align not-export" }];
            _table_api = initDataTable('.table-reportmng', '<?php echo $report_mag_url; ?>', [4], [4], undefined, [2, 'asc'],'',columnDefs);


			$('select[name="months-report"]').on('change', function() {
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

			report_from.on('change', function() {
				var val = $(this).val();
				var report_to_val = report_to.val();
				if (val != '') {
				report_to.attr('disabled', false);
				} else {
				report_to.attr('disabled', true);
				}
			});


		

        $(document).on('change','[name="area[]"]',function(){
    
            var area = $('[name="area[]"]').val();
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
                    $('[name="region[]"]').selectpicker('refresh');
                }else if (res.success == false) {
                    let options = "";
                    $('[name="region[]"]').html(options);
                    $('[name="region[]"]').selectpicker('refresh');
                }
            })
        });

        $(document).on('change','[name="region[]"]',function(){
    
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
                    $('[name="subregion[]"]').selectpicker('refresh');
                }else if (res.success == false) {
                    let options = "";
                    $('[name="subregion[]"]').html(options);
                    $('[name="regisubregionon[]"]').selectpicker('refresh');
                }
            })
        });
                
           
		$('#search_id').on('click', function() {
            let from_date = $('#report-from').val();
            let to_date = $('#report-to').val();
            if(from_date !=''  && from_date != '' && from_date > to_date){
                alert('From date should not be greater than To date');
                return false;
            }
			$('.bootstrap-select.open').removeClass('open');
			if ($.fn.DataTable.isDataTable('.table-reportmng')) {
			$('.table-reportmng').DataTable().destroy();
			}
            var columnDefs = [{ "width": "10%" },{ "width": "30%" },{ "width": "20%" },{ "width": "20%" },{ "width": "20%", "className": "dt_center_align not-export" }];
			initDataTable('.table-reportmng',  '<?php echo $report_mag_url; ?>', [4], [4], fnServerParams, [2, 'asc'],'10',columnDefs);
			return false;
        });

        $('#download').on('click', function() {

            let data = {
                'area': <?php echo $area ?>,
                'role': <?php echo $role ?>,
                'areaid': $('[name="area[]"]').val(),
                'region': $('[name="region[]"]').val(),
                'subregion': $('[name="subregion[]"]').val(),
                'action_taker': $('[name="action_taker[]"]').val(),
                'action_reviewer': $('[name="action_reviewer[]"]').val(),
                'category': $('[name="category[]"]').val(),
                'bug': $('[name="bug[]"]').val(),
                'report_months': $('[name="months-report"]').val(),
                'report_from': $('[name="report-to"]').val(),
                'report_to': $('[name="report-from"]').val(),
                'ticket': $('[name="ticket[]"]').val(),
            }
        
        $.ajax({
            type: 'GET',
            url: admin_url+'report/send_email',
            data: data,
            success: function(response){
              
                if(response != ''){
                    $('#search_filter').html(response)
                }
                $('#exportModal').modal('show');
               
            }
            
        });
        //$('#exportModal').modal('show');
        return false;
    });
        
	});

   

    $(document).on('click','.evidence_img',function(){
        let projectId = $(this).data('project_id');
        $.ajax({
            type: 'GET',
            url: admin_url+'dashboard/evidence_image',
            data: {projectId: projectId},
            success: function(response){
                if(response != ''){
                    $('.evidence-data').html(response);
                } else {
                    $(".evidence-data").html('<p>No Evidence Found</p>');
                }
                $('#sidebarModal').modal('show');
            }
        });
    });

   

    </script>
 <?php $this->load->view('admin/dashboard/dashboard_scripts'); ?>
</body>

</html>
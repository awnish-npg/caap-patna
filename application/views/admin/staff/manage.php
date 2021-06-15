<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
?>
<?php //echo "<pre>";
//print_r($area);
//die;
?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s custom-panel1">
					<div class="panel-body">
						<?php if (has_permission('staff', '', 'create')) { ?>
							<div class="panel-header">
								<a href="javascript:void(0)" onclick="toggleAdminForm(); return false;" class="btn btn-custom add-area-admin pull-right display-block">
									Add State Admin
								</a>

								<h1>Manage State Admins <span>Here you can view, add, edit and deactivate State Admins. </span></h1>
								<hr class="hr-panel-heading" />
							</div>
						<?php } ?>
						<div class="clearfix"></div>
						<div class="table-responsive">
							<?php
							$table_data = array(
								_l('admin_name'),
								_l('admin_state'),
								_l('staff_dt_email'),
								_l('staff_add_edit_phonenumber'),
								_l('Org. - Dept.'),
								_l('status'),
								_l('options')
							);
							$custom_fields = get_custom_fields('staff', array('show_on_table' => 1));
							foreach ($custom_fields as $field) {
								array_push($table_data, $field['name']);
							}
							render_datatable($table_data, 'staff');
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<div class="modal fade sidebarModal" id="add_edit_staff" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<?= form_open("javascript:void(0)", ['id' => 'area_admin_form']); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="add-title"><?php echo _l('add_new_area_admin'); ?></span>
					<span class="edit-title"><?php echo _l('edit_new_area_admin'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<p class="form-instruction add-title">Fill in the following field to add a State Admin</p>
						<p class="form-instruction edit-title">Fill in the following field to edit a State Admin</p>
						<hr class="hr-panel-model" />
						<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
						<input type="hidden" name="id" value="">
						<input type="hidden" name="departments" value="">
						<div class="form-group" app-field-wrapper="departments">
							<div class="form-select-field">
								<select name="departments" id="departments" class="selectpicker show-tick" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select State" data-width="100%">
								</select>
								<label class="select-label">State*</label>
								<p id="departments-error" class="text-danger" style="display: none;"></p>
							</div>
						</div>
						<div class="form-group">
							<div class="form-input-field">
								<input type="text" name="name" id="name">
								<label for="name" title="<?php echo _l('aa_full_name_req'); ?>" data-title="<?php echo _l('aa_full_name_req'); ?>"></label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-input-field">
								<input type="text" name="organisation" id="organisation">
								<label for="organisation" title="<?php echo _l('org_dept_req'); ?>" data-title="<?php echo _l('org_dept_req'); ?>"></label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-input-field">
								<input type="email" name="email" id="email">
								<label for="email" title="Email*" data-title="Email"></label>
							</div>
						</div>
						<div class="form-group">
							<div class="form-input-field">
								<input type="text" name="phone" id="phone">
								<label for="phone" title="Phone*" data-title="Phone"></label>
							</div>
						</div>
						<!-- <div class="form-group">
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
								<label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
							</div>
						</div> -->
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-custom">Save</button>
				<button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<style>
	.has-error .bootstrap-select .dropdown-toggle {
		border-color: #d7d7d7 !important;
	}
</style>
<script>
	$(function() {
		var columnDefs = [{
			"width": "20%"
		}, {
			"width": "15%"
		}, {
			"width": "25%"
		}, {
			"width": "10%"
		}, {
			"width": "20%"
		}, {
			"width": "5%"
		}, {
			"width": "5%",
			"className": "dt_center_align"
		}];
		initDataTable('.table-staff', window.location.href, [6], [6], undefined, [0, 'asc'], '', columnDefs);
		appValidateForm($('form'), {
			departments: 'required',
			name: {
				required: true,
				maxlength: 50,
				charsonly: true,
				fullname: true,
				normalizer: function(value) {
					return $.trim(value);
				}

			},
			organisation: {
				required: true,
				maxlength: 100,
				chars_allowed_special: true,
				organization: true,
			},
			email: {
				required: true,
				validEmail: true,
				maxlength: 50
			},
			phone: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 12
			},
		}, adminSubmit);

		// getArea(false);
	});

	function getArea(excludeStaffArea, invoker = null, id = null) {
		let url = admin_url + 'area/get_admin_area';
		let data;
		if (excludeStaffArea)
			data = {
				"exclude_staff_area": 1,
				"role": <?php echo $role->roleid; ?>
			}
		else
			data = {
				"exclude_staff_area": 0
			}
		$.ajax({
			processing: 'true',
			serverSide: 'true',
			type: "POST",
			url: url,
			data: data,
			success: function(res) {
				console.log($(invoker).data('department'));
				let options = ""
				res = JSON.parse(res);
				res.area_list.map(area => {
					options += `<option value=${area.areaid} ${invoker ? $(invoker).data('department') == area.areaid ? "selected" : "" : ""}>${area.name}</option>`
				});
				$('select[name="departments"]').html(options);
				$('#departments').selectpicker('refresh');
				if (invoker && id) {
					var area = $(invoker).data('area');
					$('input[name="name"]').val($(invoker).data('name'))
					$('input[name="organisation"]').val($(invoker).data('organisation'))
					$('input[name="email"]').val($(invoker).data('email'))
					$('input[name="phone"]').val($(invoker).data('phone'))
					$('input[name="id"]').val(id)
					$('#departments').selectpicker('val', $(invoker).data('department'));
					$('#departments').selectpicker('render');
					$('#add_edit_staff').modal('show');
					$('.add-title').hide();
					$(".edit-title").show();
				}
				if (!excludeStaffArea) {
					$('#departments').addClass("disabled");
				} else {
					$('#departments').removeClass("disabled");
				}
			}
		})
	}

	$('#add_edit_staff').on('hidden.bs.modal', function() {
		$('#departments-error').remove();
	});

	const toggleAdminForm = (action = "show") => {

	
		if (action === "hide") {
			$("#area_admin_form").trigger('reset');
			$("#add_edit_staff").modal('hide');
		} else {
			$('#departments-error').addClass("hide");
			$('#departments').parents(".form-group").append('<p id="departments-error" class="text-danger"></p>');
			$('input[name="id"]').val("");
			$('select[name="departments"]').attr('disabled', false);
			$('input[name="departments"]').attr('disabled', true);
			getArea(true);
			$("#area_admin_form").trigger('reset');
			$('.edit-title').hide();
			$('.add-title').show();
			$("#add_edit_staff").modal('show');
			$('input').removeClass("label-up");
		}
	}

	const adminSubmit = (form) => {
		var formData = $(form).serialize();
		var url = admin_url + "staff/save";
		$.ajax({
			processing: 'true',
			serverSide: 'true',
			type: "POST",
			url: url,
			data: formData,
			success: function(res) {
				res = JSON.parse(res);
				if (res.success) {
					alert_float('success', res.message);
					$('.table-staff').DataTable().ajax.reload();
					$("#area_admin_form").trigger('reset');
					$('#add_edit_staff').modal('hide');
				} else {
					alert_float('danger', res.message);
				}
			}
		})
	}

	const changeStatus = (invoker, id, area, slug_url) => {
		console.log("hello")
		let url = admin_url + "staff/change_staff_status";
		let data = {};
		if ($(invoker).is(":checked")) {
			data = {
				'id': id,
				'area': area,
				'status': 1,
				'slug_url': slug_url
			}
		} else {
			data = {
				'id': id,
				'area': area,
				'status': 0,
				'slug_url': slug_url
			}
		}
		$.ajax({
			processing: 'true',
			serverSide: 'true',
			type: "POST",
			url: url,
			data: data,
			success: function(res) {
				res = JSON.parse(res);
				if (res.success) {
					$(this).prop('checked', !$(this).prop('checked'));
					if (res.check_status) {
						$(invoker).prop('checked', true)
					} else if (!res.check_status) {
						$(invoker).prop('checked', false)
					}
					alert_float('success', res.message);
					$('.table-staff').DataTable().ajax.reload();
				} else {
					if (res.check_status) {
						$(invoker).prop('checked', true)
					} else if (!res.check_status) {
						$(invoker).prop('checked', false)
					}
					alert_float('danger', res.message);
				}
			}
		})

	}

	function edit_admin(invoker, id) {
		$('select[name="departments"]').attr('disabled', true);
		$('input[name="departments"]').attr('disabled', false);
		$('input[name="departments"]').val($(invoker).data('department'));
		$('input').addClass("label-up");
		getArea(false, invoker, id);
	}
</script>
</body>

</html>
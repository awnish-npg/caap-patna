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
						<div class="panel-header">
							<?php if (has_permission($permission, '', 'create')) { ?>
								<a href="#" onclick="new_staff(); return false;" class="btn btn-custom add-area-admin pull-right display-block">
									Add National Observer
								</a>
							<?php } ?>
							<h1>Manage National Observers <span>Here you can view, add, edit and deactivate National Observers</span></h1>
							<hr class="hr-panel-heading" />
						</div>

						<div class="table-responsive">
						<?php
						$table_data = array(
							_l('admin_name'),
							_l('staff_dt_email'),
							_l('staff_add_edit_phonenumber'),
							_l('organisation'),
							_l('status'),
							_l('options')
						);;
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
<div class="modal sidebarModal fade" id="delete_staff" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<?php echo form_open(admin_url('staff/delete', array('delete_staff_form'))); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="delete_id">
					<?php echo form_hidden('id'); ?>
				</div>
				<p><?php echo _l('delete_staff_info'); ?></p>
				<?php
				echo render_select('transfer_data_to', $staff_members, array('staffid', array('firstname', 'lastname')), 'staff_member', get_staff_user_id(), array(), array(), '', '', false);
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-custom" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-cancel btn-danger _delete"><?php echo _l('confirm'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal sidebarModal fade" id="add_area_admin" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('staff/create')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">

					<span class="edit-title">Edit National Observers</span>
					<span class="add-title">Add National Observers</span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="additional"></div>
				<p class="form-instruction add-title">Fill in the following field to add a National Observer</p>
				<p class="form-instruction edit-title">Fill in the following field to edit a National Observer</p>
				<hr class="hr-panel-model" />
				<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
				<input type="hidden" name="id" value="">
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" id="admin-name" name="firstname">
						<label for="admin-name" title="<?php echo _l('aeg_full_name_req');?>" data-title="<?php echo _l('aeg_full_name_req');?>"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" autocomplete="off" id="org" name="organisation">
						<label for="org" title="<?php echo _l('org');?>" data-title="<?php echo _l('org');?>"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" id="adm-email" name="email">
						<label for="adm-email" title="Email*" data-title="Email*"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" id="adm-phone" name="phonenumber">
						<label for="adm-phone" title="Phone Number*" data-title="Phone Number"></label>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-custom">Save</button>
					<button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>

				</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>
			<p class="form-field-notes"><?php echo _l('auto_password_email_hint'); ?></p>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<?php init_tail(); ?>
<script>
	$(function() {
		//	initDataTable('.table-staff', window.location.href);
	});

	$(function() {
		var columnDefs = [{ "width": "25%" },{ "width": "25%"},{ "width": "10%" }, { "width": "25%" },{ "width": "5%"}, { "width": "5%", "className": "dt_center_align"}];
		initDataTable('.table-staff', window.location.href, [5], [5], undefined, [0, 'asc'], '',columnDefs);
		appValidateForm($('form'), {
			firstname: {
				required: true,
				maxlength: 50,
				charsonly: true,
				fullname: true,
				noSpace: true,
				normalizer: function(value) {
					return $.trim(value);
				}
			},
			email: {
				required: true,
				validEmail: true,
				maxlength: 100
			},
			organisation: {
				required: true,
				maxlength: 125,
				chars_allowed_special: true,
				noSpace: true,
				// organization: true,
			},
			phonenumber: {
				required: true,
				digits: true,
				minlength: 8,
				maxlength: 12
			},
		}, manage_staff);
		$('#add_area_admin').on('hidden.bs.modal', function(event) {
			$('#additional').html('');
			$('#add_area_admin input[type="text"]').val('');
			$('#add_area_admin input[type="email"]').val('');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
			$('#add_area_admin input').removeClass("label-up");
		});
	});

	function manage_staff(form) {
		var data = $(form).serialize();
		var url = form.action;

		$.post(url, data).done(function(response) {
			response = JSON.parse(response);
			console.log(response);
			if (response.success == true) {
				alert_float('success', response.message);
				$('#add_area_admin').modal('hide');
			}
			if (response.success == false) {
				alert_float('danger', response.message);
			}
			$('.table-staff').DataTable().ajax.reload();
			//$('#add_area_admin').modal('hide');
		}).fail(function(data) {
			var error = JSON.parse(data.responseText);
			alert_float('danger', error.message);
		});
		return false;
	}

	function new_staff() {
		$('#add_area_admin input[name="id"]').val("");
		$('#add_area_admin input[name="id"]').prop("disabled", true);
		$('.edit-title').addClass('hide');
		$('#add_area_admin').modal('show');
	}

	function edit_admin(invoker, id) {

		$.ajax({
			processing: 'true',
			serverSide: 'true',
			url: admin_url + "staff/get_staff_detail",
			type: "POST",
			data: {
				staffid: $(invoker).data('staffid')
			},
			dataType: "json",
			success: function(response) {
				if (response.success == true) {
					$('input[name="status"]').prop('checked', true);
					$('#add_area_admin input').addClass("label-up");
					$('#add_area_admin input[name="firstname"]').val(response.staff.full_name);
					$('#add_area_admin input[name="organisation"]').val(response.staff.organisation);
					$('#add_area_admin input[name="email"]').val(response.staff.email);
					$('#add_area_admin input[name="phonenumber"]').val(response.staff.phonenumber);
					$('#additional').append(hidden_input('id', response.staff.staffid));
				} else {
					$('input[name="status"]').prop('checked', false);
				}
			},
		});

		$('#add_area_admin input[name="id"]').val($(invoker).data('staffid'));
		$('#add_area_admin input[name="id"]').prop("disabled", false);
		$('#department input[name="name"]').val($(invoker).data('name'));
		$('#department input[name="email"]').val($(invoker).data('email'));
		$('#add_area_admin').modal('show');
		$('.add-title').addClass('hide');
	}

	const changeStatus = (invoker, id, slug_url) => {
		console.log("hello")
		let url = admin_url + "staff/change_staff_status";
		let data = {};
		if ($(invoker).is(":checked")) {
			data = {
				'id': id,
				'slug_url': slug_url,
				'status': 1
			}
		} else {
			data = {
				'id': id,
				'slug_url': slug_url,
				'status': 0
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
					$('.table-staff-ae').DataTable().ajax.reload();
					$('.table-staff').DataTable().ajax.reload();
					alert_float('success', res.message);
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
</script>
</body>

</html>

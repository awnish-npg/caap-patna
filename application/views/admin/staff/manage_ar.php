<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$area = $GLOBALS['current_user']->area;
?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s custom-panel">
					<div class="panel-body">
						<div class="panel-header">
							<?php if (has_permission($permission, '', 'create')) { ?>
								<a href="#" onclick="new_staff(); return false;" class="btn btn-custom add-area-admin pull-right display-block">
									Add Reviewer
								</a>
							<?php } ?>
							<h1>Manage Reviewers <span>Here you can view, add, edit and deactivate Reviewers </span></h1>
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
						$custom_fields = get_custom_fields('staff-ar', array('show_on_table' => 1));
						foreach ($custom_fields as $field) {
							array_push($table_data, $field['name']);
						}
						render_datatable($table_data, 'staff-ar');
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal sidebarModal fade" id="add_area_admin" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('staff/save_action_reviewer')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">

					<span class="edit-title">Edit Reviewer</span>
					<span class="add-title">Add Reviewer</span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="addition"></div>
				<p class="form-instruction add-title">Fill in the following fields to add a Reviewer</p>
				<p class="form-instruction edit-title">Fill in the following field(s) to edit a Reviewer</p>
				<hr class="hr-panel-model" />
				<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
				<input type="hidden" name="area" value="<?php echo $area; ?>">
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" id="admin_name" name="firstname">
						<label for="admin_name" title="Full Name*" data-title="Full Name*"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" name="organisation" id="admin_organisation">
						<label for="admin_organisation" title="Organization - Department*" data-title="Organisation*"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" name="email" id="admin_email">
						<label for="admin_email" title="Email*" data-title="Email*"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" name="phonenumber" id="admin_phone">
						<label for="admin_phone" title="Phone No.*" data-title="Phone No.*"></label>
					</div>
				</div>
				<div class="action-taker-wrap">
					<hr class="hr-panel-heading" />
					<p class="form-instruction region-instruction">Assigned Project Leaders:</p>
					<div class="ar-card"></div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-custom">Save</button>
					<button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
				</div>
					</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<?php init_tail(); ?>
<script>
	$(function() {
		var columnDefs = [{ "width": "25%" },
						  { "width": "25%" },
						  { "width": "10%"},
						  { "width": "30%"}, 
						  { "width": "5%" },
						  { "width": "5%", "className": "dt_center_align" }];
		initDataTable('.table-staff-ar', window.location.href, [2], [5], undefined, [0, 'asc'], '',columnDefs);
		appValidateForm($('form'), {

			firstname: {
				required: true,
				maxlength: 50,
				charsnameonly: true,
				fname: true,
				noSpace: true,
				normalizer: function(value) {
					return $.trim(value);
				}
			},
			email: {
				required: true,
				validEmail: true,
				maxlength: 50
			},
			organisation: {
				required: true,
				maxlength: 125,
				chars_allowed_special: true,
				organization: true,
				noSpace: true,
			},
			phonenumber: {
				required: true,
				digits: true,
				minlength: 8,
				maxlength: 12
			},
		}, manage_staff);
		$('#add_area_admin').on('hidden.bs.modal', function(event) {
			$('#addition').html('');
			$('#add_area_admin input[type="text"]').val('');
			$('#add_area_admin input[type="email"]').val('');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
			$('input').removeClass("label-up");
			$('.text-danger').css("display", "none");
		});

	});


	const changeStatus = (invoker, id, slug_url) => {

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
					$('.table-staff-ar').DataTable().ajax.reload();

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


	function manage_staff(form) {
		var data = $(form).serialize();
		var url = form.action;

		$.post(url, data).done(function(response) {
			response = JSON.parse(response);

			if (response.success == true) {
				alert_float('success', response.message);
				$('#add_area_admin').modal('hide');
				$('.table-staff-ar').DataTable().ajax.reload();
			}
			if (response.success == false) {
				alert_float('danger', response.message);
			}
		}).fail(function(data) {
			var error = JSON.parse(data.responseText);
			alert_float('danger', error.message);
		});
		return false;
	}

	function new_staff() {
		$(".action-taker-wrap").hide();
		$('.edit-title').addClass('hide');
		$('#add_area_admin').modal('show');
	}

	const getActionTakers = (id) => {
		$.post(admin_url + "staff/get_action_takers", {
			id: id
		}).done((res) => {
			res = JSON.parse(res);
			let at_cards = 'No Records found';
			if (res.success == true) {
				at_cards = '';
				res.action_takers.map(action_taker => {
					at_cards += `<div class="ar-card-body"><div class="personal-info"><p class="head-name">${action_taker.at_name}</p><p class="ar-org">${action_taker.organisation}</p><p class="icon-info"><span class="material-icons">place</span> ${action_taker.region_name}</p></div><div class="contact-info"><p><span class="material-icons">call</span> ${action_taker.phonenumber}</p><p><span class="material-icons">mail</span> ${action_taker.email}</p><p><span class="material-icons">pin_drop</span> ${action_taker.sub_region_name}</p></div></div>`
				})
			}
			$(".ar-card").html(at_cards);
		})
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
					$('#add_area_admin input[name="firstname"]').val(response.staff.full_name);
					$('#add_area_admin input[name="organisation"]').val(response.staff.organisation);
					$('#add_area_admin input[name="email"]').val(response.staff.email);
					$('#add_area_admin input[name="phonenumber"]').val(response.staff.phonenumber);
					$('#addition').append(hidden_input('id', response.staff.staffid));
					getActionTakers(response.staff.staffid);
				} else {
					$('input[name="status"]').prop('checked', false);
				}
			},
		});

		$('.add-title').addClass('hide');
		$(".action-taker-wrap").show();
		$('input').addClass("label-up");
		$('#add_area_admin').modal('show');
	}
</script>
</body>

</html>
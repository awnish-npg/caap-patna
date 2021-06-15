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
									Add Project Support
								</a>
							<?php } ?>
							<h1>Manage Project Support <span>Here you can view, add, edit and deactivate Project Support details </span></h1>
							<hr class="hr-panel-heading" />
						</div>
						<div class="table-responsive">
						<?php
						$table_data = array(
							_l('at_name'),
							_l('action_taker'),
							_l('Reviewer'),
							//_l('region'),
							_l('subregion'),
							_l('staff_dt_email'),
							_l('staff_add_edit_phonenumber'),
							_l('organisation'),
							_l('status'),
							_l('options')
						);;
						$custom_fields = get_custom_fields('staff-ae', array('show_on_table' => 1));
						foreach ($custom_fields as $field) {
							array_push($table_data, $field['name']);
						}
						render_datatable($table_data, 'staff-ae');
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
		<?php echo form_open(admin_url('staff/save_action_assistance')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">

					<span class="edit-title">Edit Project Support</span>
					<span class="add-title">Add Project Support</span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="addition"></div>
				<p class="form-instruction add-title">Fill in the following fields to add a Project Support</p>
				<p class="form-instruction edit-title">Fill in the following field(s) to edit a Project Support</p>
				<hr class="hr-panel-model" />
				<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
				<input type="hidden" name="area" value="<?php echo $area; ?>">

				<div class="form-group" app-field-wrapper="region_id">
					<div class="form-select-field">
						<select name="region" class="form-control selectpicker show-tick" id="region_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select City/ Corporation*" onchange="getSubregion(this.value,val=0)">
						</select>
						<label class="select-label">City/ Corporation*</label>
						<p id="region_id-error" class="text-danger"></p>
					</div>
				</div>
				<div class="form-group" app-field-wrapper="sub_region_id">
					<div class="form-select-field">
						<select name="sub_region" class="form-control selectpicker show-tick" id="sub_region_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select Municipal Zone*" onchange="getActionTaker(region=0,this.value,val=0)">
						</select>
						<label class="select-label">Municipal Zone*</label>
						<p id="sub_region_id-error" class="text-danger"></p>
					</div>
				</div>
				<div class="form-group" app-field-wrapper="at_id">
					<div class="form-select-field">
						<select name="action_taker" class="form-control selectpicker show-tick" id="at_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select Project Leader*">
						</select>
						<label class="select-label">Project Leader*</label>
						<p id="at_id-error" class="text-danger"></p>
					</div>
				</div>
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
				<div class="modal-footer">
					<button type="submit" class="btn btn-custom">Save</button>
					<button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
				</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<?php init_tail(); ?>
<script>
	let REGION_LIST = {};

	$(function() {
		var columnDefs = [{
				"width": "25%"
			},
			{
				"width": "25%"
			},
			{
				"width": "25%"
			},
			{
				"width": "25%"
			},
			{
				"width": "25%"
			},
			{
				"width": "10%"
			},
			{
				"width": "25%"
			},
			{
				"width": "5%"
			},
			{
				"width": "5%",
				"className": "dt_center_align"
			}
		];
		initDataTable('.table-staff-ae', window.location.href, [2], [8], undefined, [0, 'asc'], '', columnDefs);
		appValidateForm($('form'), {
			region: {
				required: true
			},
			sub_region: {
				required: true
			},
			action_taker: {
				required: true
			},
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
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
			$('input').removeClass("label-up");
			$('.text-danger').css("display", "none");
		});




	});

	/*Function to get and populate Region Data*/
	const getRegionData = (val = '') => {
		let area = "<?= $area ?>";
		let data = {
			'area_id': area,
			'group_by': false
		}
		$.post(admin_url + 'region/get_region', data).done((res) => {
			res = JSON.parse(res);

			if (res.success == true) {
				REGION_LIST = {
					...res.region_list
				};
				let options = "";
				//let options = "<option value=''>Select City/ Corporation*</option>";

				for (let region in REGION_LIST) {

					if (val != 0 && val == REGION_LIST[region][0].region_id) {
						options += `<option value='${REGION_LIST[region][0].region_id}' selected>${REGION_LIST[region][0].region_name}</option>`
					} else {
						options += `<option value='${REGION_LIST[region][0].region_id}'>${REGION_LIST[region][0].region_name}</option>`
					}

				}
				$("#region_id").html(options);

				$('#region_id').selectpicker('refresh');
				if (val !== null) {
					$('#region_id').selectpicker('val', val);
				}
			}
		}).fail(function(data) {
			var error = JSON.parse(data.responseText);
			console.log("Region option ajax error:", error);
		});
	}

	$(document).on('changed.bs.select', '#region_id', function(e, clickedIndex, newValue, oldValue) {

		getSubregion($(e.currentTarget).val(), window.SubRegionToSelect);
		if (window.SubRegionToSelect) window.SubRegionToSelect = null;
	})

	function getSubregion(region_id, val = '') {
		let options = "";
		//let options = "<option value=''>Select Municipal Zone*</option>";
		for (let region in REGION_LIST) {
			if (REGION_LIST[region][0].region_id == region_id) {
				REGION_LIST[region].map(sub_region => {
					if (sub_region.sub_region_id != null)
						if (val != 0 && val == sub_region.sub_region_id) {
							options += `<option value='${sub_region.sub_region_id}' selected>${sub_region.sub_region_name}</option>`
						} else {
							options += `<option value='${sub_region.sub_region_id}'>${sub_region.sub_region_name}</option>`
						}
				})
			}
		}
		$('#sub_region_id').html(options);

		$('#sub_region_id').selectpicker('refresh');
		if (val !== null) {
			$('#sub_region_id').selectpicker('val', val);
			$('#sub_region_id').selectpicker('render');
		}

	}

	// change status
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
					$('.table-staff-ae').DataTable().ajax.reload();

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
				$('.table-staff-ae').DataTable().ajax.reload();
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
		// openTicket
		$('#region_id').empty('');
		//$('#region_id').append('<option value="">Select City/ Corporation*</option>');
		//
		getRegionData();
		// master
		$('#sub_region_id').empty('');
		//$('#sub_region_id').append('<option value="">Select Municipal Zone*</option>');
		$('#at_id').empty();
		//$('#at_id').append('<option value="">Select Project Leader*</option>');
		$('#add_area_admin').modal('show');
		$('.edit-title').addClass('hide');
	}

	function edit_admin(invoker, id) {

		$("#add_area_admin").modal('show');

		$.ajax({
			processing: 'true',
			serverSide: 'true',
			url: admin_url + "staff/get_ata",
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
					getRegionData(response.staff.region);
					//getSubregion(response.staff.region, response.staff.sub_region);
					window.SubRegionToSelect = response.staff.sub_region;
					getActionTaker(response.staff.region, response.staff.sub_region, response.staff.assistant_id);
				} else {
					$('input[name="status"]').prop('checked', false);
				}
			},
		});

		$('#add_area_admin').modal('show');
		$('.add-title').addClass('hide');
		$('input').addClass("label-up");
	}

	// get action taker
	function getActionTaker(region_id, sub_region, val) {

		var area = "<?= $area ?>";
		if (region_id == 0) {
			var region = $('#region_id').val();
		} else {
			var region = region_id;
		}

		if (sub_region) {
			$.ajax({
				processing: 'true',
				serverSide: 'true',
				url: admin_url + "staff/get_action_taker",
				type: "POST",
				data: {
					area_id: area,
					region: region,
					sub_region: sub_region
				},
				dataType: "json",

				success: function(data) {

					if (data) {
						$('#at_id').empty();
						let options = "";
						//let options = "<option value=''>Select Project Leader*</option>";
						$.each(data, function(key, value) {
							if (val != 0 && val == value.staffid) {
								options += '<option value="' + value.staffid + '" selected>' + value.name + ' (' + value.organisation + ') </option>'
							} else {
								options += '<option value="' + value.staffid + '">' + value.name + ' (' + value.organisation + ') </option>'
							}
							$('#at_id').html(options);
							$('#at_id').selectpicker('refresh');
							if (val !== null) {
								$('#at_id').selectpicker('val', val);
								$('#at_id').selectpicker('render');
							}
						});
					} else {
						$('#at_id').empty();
						$('#at_id').append('<option value="">No Project Leader</option>');
					}
				},
			});
		} else {
			$('select[name="at_id"]').empty();
		}
	}
</script>
</body>

</html>
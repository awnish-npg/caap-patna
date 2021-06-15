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
									Add State Observer
								</a>
							<?php } ?>
							<h1>Manage State Observers <span>Here you can view, add, edit and deactivate State Observers </span></h1>
							<hr class="hr-panel-heading" />

						</div>

						<div class="table-responsive">
						<?php
						$table_data = array(
							//	_l('region'),
							_l('admin_name'),
							_l('City/ Corporation'),
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
		<?php echo form_open(admin_url('staff/save_area_enforcer')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">

					<span class="edit-title">Edit State Observer</span>
					<span class="add-title">Add State Observer</span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="additional"></div>
				<p class="form-instruction add-title">Fill in the following fields to add a State Observer</p>
				<p class="form-instruction edit-title">Fill in the following field(s) to edit a State Observer</p>
				<hr class="hr-panel-model" />
				<input type="hidden" name="id">
				<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
				<input type="hidden" name="area" value="<?php echo $area; ?>">
				<div class="form-group" app-field-wrapper="region_id">
					<div class="form-select-field">
						<select name="region[]" class="form-control selectpicker show-tick" id="region_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" data-actions-box="true" multiple title="Select City/ Corporation*">
						</select>
						<label class="select-label">City/ Corporation*</label>
						<p id="region_id-error" class="text-danger"></p>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" id="admin_name" name="firstname">
						<label for="admin_name" title="<?php echo _l('ae_full_name_req'); ?>" data-title="<?php echo _l('aa_full_name_req'); ?>"></label>
					</div>
				</div>
				<div class="form-group">
					<div class="form-input-field">
						<input type="text" required autocomplete="off" name="organisation" id="admin_organisation">
						<label for="admin_organisation" title="<?php echo _l('org_dept_req'); ?>" data-title="<?php echo _l('org_dept_req'); ?>"></label>
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

<div class="modal sidebarModal fade" id="temp_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
				</h4>
			</div>
			<div class="modal-body">
				<h1>UNDER DEVELOPMENT</h1>
				<div class="modal-footer">
					<button type="button" class="btn btn-cancel" data-dismiss="modal">CLOSE</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<?php init_tail(); ?>
<script>
	$(function() {
		var columnDefs = [{
			"width": "20%"
		}, {
			"width": "20%"
		}, {
			"width": "20%"
		}, null, null, {
			"width": "5%"
		}, {
			"width": "5%",
			"className": "dt_center_align"
		}];
		initDataTable('.table-staff-ae', window.location.href, [6], [6], undefined, [0, 'asc'], '', columnDefs);
		appValidateForm($('form'), {
			"region[]": {
				required: true
			},
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
			$('#additional').html('');
			// $('#region_grid').html('');
			$('#add_area_admin input[type="text"]').val('');
			$('#add_area_admin input[type="email"]').val('');
			$('#add_area_admin input[name="id"]').val('');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
		});

		getRegionData()
	});

	/*Function to get and populate Region Data*/
	const getRegionData = (selectedRegion = null) => {
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
				for (let region in REGION_LIST) {
					let regionId = REGION_LIST[region][0].region_id;
					let regionName = REGION_LIST[region][0].region_name;
					options += `<option value='${regionId}'>${regionName}</option>`

				}
				$("#region_id").html(options);
				$('#region_id').selectpicker('refresh');
				if (selectedRegion !== null) {
					$('#region_id').selectpicker('val', selectedRegion);
				}
			}
		}).fail(function(data) {
			var error = JSON.parse(data.responseText);
			console.log("Region option ajax error:", error);
		});
	}

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
		$('#add_area_admin').modal('show');
		$('.edit-title').addClass('hide');
	}

	function edit_admin(invoker, id) {
		// getRegionData()
		$('#add_area_admin input[name="firstname"]').val($(invoker).data('name'));
		$('#add_area_admin input[name="email"]').val($(invoker).data('email'));
		$('#add_area_admin input[name="organisation"]').val($(invoker).data('organisation'));
		$('#add_area_admin input[name="phonenumber"]').val($(invoker).data('phone'));
		$('#add_area_admin input[name="id"]').val(id);
		$('.add-title').addClass('hide');
		$('#add_area_admin').modal('show');

		$.post(admin_url + 'staff/get_staff_region', {
			'id': id
		}).done(function(response) {
			response = JSON.parse(response);
			if (response.success == true) {
				let selectedRegions = [];
				response.regions.map(region => selectedRegions.push(region.region));
				getRegionData(selectedRegions)
			}
		});
		triggerInputClass();
	}

	function triggerInputClass() {
		$("form :input").each(function() {
			if ($(this).val()) {
				$(this).addClass("label-up");
			} else {
				$(this).addClass("labellll-up");
			}
		})
	}
</script>
</body>

</html>
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
									Add Project Leader
								</a>
							<?php } ?>
							<h1>Manage Project Leaders <span>Here you can view, add, edit and deactivate Project Leaders </span></h1>
							<hr class="hr-panel-heading" />
						</div>
						<div class="table-responsive">
							<?php
							$table_data = array(
								_l('name'),
								_l('reviewer'),
								_l('City/ Corp.'),
								_l('subregion'),
								_l('categories_at'),
								_l('staff_dt_email'),
								_l('staff_add_edit_phonenumber'),
								_l('organisation'),
								_l('status'),
								_l('options')
							);;
							$custom_fields = get_custom_fields('staff-at', array('show_on_table' => 1));
							foreach ($custom_fields as $field) {
								array_push($table_data, $field['name']);
							}
							render_datatable($table_data, 'staff-at');
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
		<?php echo form_open(admin_url('staff/save_action_taker')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">

					<span class="edit-title">Edit Project Leader</span>
					<span class="add-title">Add Project Leader</span>
				</h4>
			</div>
			<div class="modal-body">
				<div id="additional"></div>
				<p class="form-instruction add-title">Fill in the following fields to add a Project Leader</p>
				<p class="form-instruction edit-title">Fill in the following field(s) to edit a Project Leader</p>
				<hr class="hr-panel-model" />

				<!-- hidden inputs for staff_id, role and area -->
				<input type="hidden" name="id">
				<input type="hidden" name="role" value="<?php echo $role->roleid; ?>">
				<input type="hidden" name="area" value="<?php echo $area; ?>">
				<div class="form-group" app-field-wrapper="region_id">
					<div class="form-select-field">
						<select name="region" class="selectpicker show-tick" id="region_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select City/ Corporation">
						</select>
						<label class="select-label">City/ Corporation*</label>
						<p id="region_id-error" class="text-danger"></p>
					</div>
				</div>
				<div class="form-group" app-field-wrapper="sub_region_id">
					<div class="form-select-field">
						<select name="sub_region" class="form-control selectpicker show-tick" id="sub_region_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select Municipal Zone">
						</select>
						<label class="select-label">Municipal Zone*</label>
						<p id="sub_region_id-error" class="text-danger"></p>
					</div>
				</div>
				<div class="form-group" app-field-wrapper="reviewer_id">
					<div class="form-select-field">
						<select name="reviewer" class="form-control selectpicker show-tick" id="reviewer_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select Reviewer">
						</select>
						<label class="select-label">Reviewer*</label>
						<p id="reviewer_id-error" class="text-danger"></p>
					</div>
				</div>

				<div class="form-group" app-field-wrapper="categories">
					<div class="form-select-field">
						<select name="categories[]" class="form-control selectpicker show-tick" id="categories" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" data-actions-box="true" multiple title="Select Action Items">
							<?php foreach ($categories as $key => $cat_name) { ?>
								<option value="<?php echo $cat_name['id'] ?>"><?php echo $cat_name['issue_name'] ?></option>
							<?php } ?>
						</select>
						<?php /* echo render_select('categories[]', $categories, array('id', 'issue_name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => 'Action Items*', 'multiple' => true, 'data-actions-box' => true), array("id","categories"), 'no-mbot', 'custom-categories', false);
						*/ ?>
						<label class="select-label">Action Items*</label>
						<p id="categories-error" class="text-danger"></p>
					</div>
				</div>
				<?php
				echo "<div class='form-group'>" . render_input('firstname', 'at_full_name_req', '', 'text', ['required' => 'required', 'autocomplete' => 'off', 'id' => 'admin_name']) . "</div>";
				echo "<div class='form-group'>" . render_input('organisation', 'org_dept_req', '', 'text', ['required' => 'required', 'autocomplete' => 'off', 'id' => 'admin_organisation']) . "</div>";
				echo "<div class='form-group'>" . render_input('email', 'email_req', '', 'email', ['required' => 'required', 'autocomplete' => 'off', 'id' => 'admin_email']) . "</div>";
				echo "<div class='form-group'>" . render_input('phonenumber', 'phone_req', '', 'text', ['required' => 'required', 'autocomplete' => 'off', 'id' => 'admin_phone']) . "</div>";

				?>
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
	/**
	Global Variable to provide Region List.
	 */
	let REGION_LIST = {};
	var PAGINATION_INFO = 'Showing _START_ to _TOTAL_ entries';
	$(function() {
		var columnDefs = [{
				"width": "15%"
			},
			{
				"width": "15%"
			},
			{
				"width": "10%"
			},
			{
				"width": "15%"
			},
			{
				"width": "15%"
			},
			{
				"width": "10%"
			},
			{
				"width": "10%"
			},
			{
				"width": "10%"
			},
			{
				"width": "5%"
			},
			{
				"width": "5%",
				"className": "dt_center_align"
			}
		];
		initDataTable('.table-staff-at', window.location.href, [9], [9], undefined, [0, 'asc'], '', columnDefs);
		appValidateForm($('form'), {
			// region: {
			// 	required: true
			// },
			// sub_region: {
			// 	required: true
			// },
			// reviewer: {
			// 	required: true
			// },
			// categories: {
			// 	required: true
			// },
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
				organization: true,
			},
			phonenumber: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 12
			},
			region: 'required',
			sub_region: 'required',
			reviewer: 'required',
			"categories[]": 'required',
		}, manage_staff);
		$('#add_area_admin').on('hidden.bs.modal', function(event) {
			$('#additional').html('');
			$('#add_area_admin input[type="text"]').val('');
			$('#add_area_admin input[type="email"]').val('');
			$('#add_area_admin input[name="id"]').val('');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
		});

		/*Function Call to get and populate Regions in select box under Admin Form*/
		getRegionData();

		/*Function call to get and populate Reviewers*/
		getReviewers();
	});

	/*Function to Add member*/
	function manage_staff(form) {
		var data = $(form).serialize();
		var url = form.action;
		console.log($(".form-group:has(select)"));
		$.post(url, data).done(function(response) {
			response = JSON.parse(response);
			if (response.success == true) {
				alert_float('success', response.message);
				$('.table-staff-at').DataTable().ajax.reload();
				$('#add_area_admin').modal('hide');
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


	/*Function to edit member*/
	function edit_admin(invoker, id) {
		$(".modal").waitMe("show")
		let regionId = $(invoker).data('rid');
		let subRegionId = $(invoker).data('srid');
		let issues = $(invoker).data('issue-ids');
		window.SubRegionToSelect = subRegionId;
		let issue_ids = [];
		$.ajax({
			type: "POST",
			url: admin_url + 'staff/get_staff_issues',
			data: {
				"staffId": id
			},
			async: false,
			success: (res) => {
				res = JSON.parse(res);
				res.issues && res.issues.map(issue => {
					issue_ids.push(issue.issue_id)
				})
				window.categoryToSelect = [...issue_ids];
			},
			error: function(data) {
				var error = JSON.parse(data.responseText);
				console.log("Issues option ajax error:", error);
				alert_float("Not able to load action items, please refresh the page");
				window.categoryToSelect = [];
			}
		})
		getRegionData(regionId);
		getReviewers($(invoker).data('rwid'));
		$('#add_area_admin input[name="firstname"]').val($(invoker).data('name'));
		$('#add_area_admin input[name="email"]').val($(invoker).data('email'));
		$('#add_area_admin input[name="organisation"]').val($(invoker).data('organisation'));
		$('#add_area_admin input[name="phonenumber"]').val($(invoker).data('phone'));
		$('#add_area_admin input[name="id"]').val(id);
		$('.add-title').addClass('hide');
		$('#add_area_admin').modal('show');
		triggerInputClass();
		$(".modal").waitMe("hide")

	}

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
	$(document).on('changed.bs.select', '#region_id', function(e) {

		getSubRegion($(e.currentTarget).val(), window.SubRegionToSelect);
		if (window.SubRegionToSelect) window.SubRegionToSelect = null;
	})


	$(document).on('changed.bs.select', '#sub_region_id', function(e) {
		console.log("window.categoryToSelect", window.categoryToSelect);
		getCategories($("#sub_region_id").val(), $("#region_id").val(), window.categoryToSelect);
		if (window.categoryToSelect) window.categoryToSelect = null;
	})

	const getSubRegion = (regionId, subRegionId = null) => {
		let options = "";
		for (let region in REGION_LIST) {
			if (REGION_LIST[region][0].region_id == regionId) {
				REGION_LIST[region].map(sub_region => {
					if (sub_region.sub_region_id != null)
						options += `<option  value='${sub_region.sub_region_id}'>${sub_region.sub_region_name}</option>`
				})
			}
		}
		$('#sub_region_id').html(options);
		$('#sub_region_id').selectpicker('refresh');
		if (subRegionId !== null) {
			$('#sub_region_id').selectpicker('val', subRegionId);
			$('#sub_region_id').selectpicker('render');
		}
	}

	/*Function to get Action Reviewers based on area*/
	const getReviewers = (rewiewerId = null) => {
		let area = "<?= $area ?>";
		let data = {
			'area_id': area,
		}
		$.post(admin_url + 'staff/get_area_reviewers', data).done((res) => {
			res = JSON.parse(res);
			if (res.success == true) {
				let options = "";
				res.reviewers.map(reviewer => {
					options += `<option value='${reviewer.staffid}' ${reviewer.staffid == rewiewerId && 'selected'}>${reviewer.name} ( ${reviewer.organisation} )</option>`;
				});
				$("#reviewer_id").html(options);
				$('#reviewer_id').selectpicker('refresh');
				if (rewiewerId !== null) {
					$('#reviewer_id').selectpicker('val', rewiewerId);
				}
			}
		}).fail(function(data) {
			var error = JSON.parse(data.responseText);
			console.log("Reviewers option ajax error:", error);
		});
	}

	/*Function to get and populate Categories*/
	const getCategories = (subRegionId, regionId, selectedCats = null) => {
		let area = "<?= $area ?>";
		let data = {
			'area_id': area,
			'sub_region_id': subRegionId,
			'region_id': regionId
		}
		console.log("selectedCats", selectedCats);
		$.post(admin_url + 'issues/get_area_issues', data).done((res) => {
			res = JSON.parse(res);
			let options = "";
			let allIssues = [];
			let existingIssues = [];
			if (res.success == true) {
				allIssues = [...res.issues.all_issues];
				let existingIssueIds = [];
				if (res.issues.existing_issues) {
					existingIssues = [...res.issues.existing_issues];
					existingIssueIds = existingIssues.filter(issue => {
						if (selectedCats !== null && selectedCats.includes(issue.issue_id))
							return false
						return true
					}).map(issue => issue.issue_id);
				}
				// console.log('selectedCats...', selectedCats);
				// console.log("existing_issues...", existingIssues);
				// console.log("allIssues...", allIssues);
				allIssues.forEach(issue => {
					if (!existingIssueIds.includes(issue.id))
						options += `<option value='${issue.id}'>${issue.issue_name}</option>`
				});
			}
			// if (res.success == true) {
			// 	selectedCats = [];
			// 	existingIssues = [...res.issues.existing_issues];
			// 	//console.log("existing_issues...", existingIssues);
			// 	existingIssues.forEach(function (issue) {
			// 		selectedCats.push(issue.issue_id)
			// 	});
			// }
			//console.log("selectedCats...", selectedCats);
			$("#categories").html(options);
			$('#categories').selectpicker('refresh');
			if (selectedCats !== null) {
				$('#categories').selectpicker('val', selectedCats);
				$('#categories').selectpicker('render');

			}
		}).fail((data) => {
			let error = JSON.parse(data.responseText);
			console.log("Categories option ajax error:", error);
		})
	}

	/* Function to change status */
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
					$('.table-staff-at').DataTable().ajax.reload();

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
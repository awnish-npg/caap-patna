<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
?>
<?php //echo "<pre>"; print_r($area); die; 
?>
<?php

//die(random_string()); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php if (isset($member)) { ?>
            <div class="member">
               <?php echo form_hidden('isedit'); ?>
               <?php echo form_hidden('memberid', $member->staffid); ?>
            </div>
         <?php } ?>
         <?php if (isset($member)) { ?>

            <div class="col-md-12">
               <?php if (total_rows(db_prefix() . 'departments', array('email' => $member->email)) > 0) { ?>
                  <div class="alert alert-danger">
                     The staff member email exists also as support department email, according to the docs, the support department email must be unique email in the system, you must change the staff email or the support department email in order all the features to work properly.
                  </div>
               <?php } ?>
               <div class="panel_s">
                  <div class="panel-body">
                     <h4 class="no-margin"><?php echo $member->firstname . ' ' . $member->lastname; ?>
                        <?php if ($member->last_activity && $member->staffid != get_staff_user_id()) { ?>
                           <small> - <?php echo _l('last_active'); ?>:
                              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_activity); ?>">
                                 <?php echo time_ago($member->last_activity); ?>
                              </span>
                           </small>
                        <?php } ?>
                        <!-- <a href="#" onclick="small_table_full_view(); return false;" data-placement="left" data-toggle="tooltip" data-title="<?php // echo _l('toggle_full_view');
                                                                                                                                                   ?>" class="toggle_view pull-right">
                           <i class="fa fa-expand"></i></a> -->
                     </h4>
                  </div>
               </div>
            </div>
         <?php } ?>
         <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'staff-form', 'autocomplete' => 'off')); ?>
         <div class="col-md-<?php if (!isset($member)) {
                                 echo '8 col-md-offset-2';
                              } else {
                                 echo '5';
                              } ?>" id="small-table">
            <div class="panel_s">
               <div class="panel-body">
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                           <?php echo _l('staff_profile_string'); ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#staff_permissions" aria-controls="staff_permissions" role="tab" data-toggle="tab">
                           <?php echo _l('staff_add_edit_permissions'); ?>
                        </a>
                     </li>
                  </ul>
                  <div class="tab-content">
                     <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
                        <input type="hidden" name="role" value="1">
                        <input type="hidden" name="password" value="<?php echo random_string(); ?>">
                        <!-- <div class="is-not-staff<?php if (isset($member) && $member->admin == 1) {
                                                   echo ' hide';
                                                } ?>">
                           <div class="checkbox checkbox-primary">
                              <?php
                              $checked = '';
                              if (isset($member)) {
                                 if ($member->is_not_staff == 1) {
                                    $checked = ' checked';
                                 }
                              }
                              ?>
                              <input type="checkbox" value="1" name="is_not_staff" id="is_not_staff" <?php echo $checked; ?>>
                              <label for="is_not_staff"><?php echo _l('is_not_staff_member'); ?></label>
                           </div>
                           <hr />
                        </div> -->
                        <?php //if ((isset($member) && $member->profile_image == NULL) || !isset($member)) { ?>
                           <!-- <div class="form-group">
                              <label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                              <input type="file" name="profile_image" class="form-control" id="profile_image">
                           </div> -->
                        <?php //} ?>
                        <?php //if (isset($member) && $member->profile_image != NULL) { ?>
                           <!-- <div class="form-group">
                              <div class="row">
                                 <div class="col-md-9">
                                    <?php echo staff_profile_image($member->staffid, array('img', 'img-responsive', 'staff-profile-image-thumb'), 'thumb'); ?>
                                 </div>
                                 <div class="col-md-3 text-right">
                                    <a href="<?php echo admin_url('staff/remove_staff_profile_image/' . $member->staffid); ?>"><i class="fa fa-remove"></i></a>
                                 </div>
                              </div>
                           </div> -->
                        <?php //} ?>
                        <?php $value = (isset($member) ? $member->firstname : ''); ?>
                        <?php $attrs = (isset($member) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('firstname', 'staff_add_edit_firstname', $value, 'text', $attrs); ?>
                        <?php $value = (isset($member) ? $member->lastname : ''); ?>
                        <?php echo render_input('lastname', 'staff_add_edit_lastname', $value); ?>
                        <?php $value = (isset($member) ? $member->email : ''); ?>
                        <?php echo render_input('email', 'staff_add_edit_email', $value, 'email', array('autocomplete' => 'off')); ?>
                        <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
                        <?php // if (get_option('disable_language') == 0) {
                        ?>
                        <!-- <div class="form-group select-placeholder">

                              <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
                              <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                 <option value=""><?php echo _l('system_default_string'); ?></option>
                                 <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                                    $selected = '';
                                    if (isset($member)) {
                                       if ($member->default_language == $availableLanguage) {
                                          $selected = 'selected';
                                       }
                                    }
                                 ?>
                                    <option value="<?php echo $availableLanguage; ?>" <?php echo $selected; ?>><?php echo ucfirst($availableLanguage); ?></option>
                                 <?php } ?>
                              </select>
                           </div> -->

                        <?php // }
                        ?>

                        <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
                        <?php $value = (isset($member) ? $member->email_signature : ''); ?>
                        <?php echo render_textarea('email_signature', 'settings_email_signature', $value, ['data-entities-encode' => 'true']); ?>
                        <!-- <div class="form-group select-placeholder">
                        <label for="direction"><?php echo _l('document_direction'); ?></label>
                        <select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
                           <option value="" <?php if (isset($member) && empty($member->direction)) {
                                                echo 'selected';
                                             } ?>></option>
                           <option value="ltr" <?php if (isset($member) && $member->direction == 'ltr') {
                                                   echo 'selected';
                                                } ?>>LTR</option>
                           <option value="rtl" <?php if (isset($member) && $member->direction == 'rtl') {
                                                   echo 'selected';
                                                } ?>>RTL</option>
                        </select>
                     </div> -->
                        <div class="form-group">
                           <?php if (count($departments) > 0) { ?>
                              <label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
                           <?php } ?>
                           <?php foreach ($departments as $department) { ?>
                              <div class="checkbox checkbox-primary">
                                 <?php
                                 $checked = '';
                                 if (isset($member)) {
                                    foreach ($staff_departments as $staff_department) {
                                       if ($staff_department['departmentid'] == $department['departmentid']) {
                                          $checked = ' checked';
                                       }
                                    }
                                 }
                                 ?>
                                 <input type="checkbox" id="dep_<?php echo $department['departmentid']; ?>" name="departments[]" value="<?php echo $department['departmentid']; ?>" <?php echo $checked; ?>>
                                 <label for="dep_<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></label>
                              </div>
                           <?php } ?>
                        </div>
                        <?php $rel_id = (isset($member) ? $member->staffid : false); ?>
                        <?php echo render_custom_fields('staff', $rel_id); ?>

                        <div class="row">
                           <div class="col-md-12">
                              <hr class="hr-10" />
                              <?php //if (is_admin()) {
                              ?>
                              <!-- <div class="checkbox checkbox-primary">
                                    <?php
                                    $isadmin = '';
                                    if (isset($member) && ($member->staffid == get_staff_user_id() || is_admin($member->staffid))) {
                                       $isadmin = ' checked';
                                    }
                                    ?>
                                    <input type="checkbox" name="administrator" id="administrator" <?php echo $isadmin; ?>>
                                    <label for="administrator"><?php echo _l('staff_add_edit_administrator'); ?></label>
                                 </div> -->
                              <?php //}
                              ?>
                              <?php if (!isset($member) && total_rows(db_prefix() . 'emailtemplates', array('slug' => 'new-staff-created', 'active' => 0)) === 0) { ?>
                                 <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
                                    <label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
                                 </div>
                              <?php } ?>
                           </div>
                        </div>
                        <?php if (!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
                           <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                           <!-- <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" /> -->
                           <?php if (isset($member)) { ?>
                              <!-- <p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p> -->
                              <?php if ($member->last_password_change != NULL) { ?>
                                 <?php echo _l('staff_add_edit_password_last_changed'); ?>:
                                 <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
                                    <?php echo time_ago($member->last_password_change); ?>
                                 </span>
                           <?php }
                           } ?>
                        <?php } ?>
                     </div>
                     <!-- <div role="tabpanel" class="tab-pane" id="staff_permissions"> -->
                     <?php
                     // hooks()->do_action('staff_render_permissions');
                     // $selected = '';
                     // foreach ($roles as $role) {
                     //    if (isset($member)) {
                     //       if ($member->role == $role['roleid']) {
                     //          $selected = $role['roleid'];
                     //       }
                     //    } else {
                     //       $default_staff_role = get_option('default_staff_role');
                     //       if ($default_staff_role == $role['roleid']) {
                     //          $selected = $role['roleid'];
                     //       }
                     //    }
                     // }
                     ?>
                     <?php //echo render_select('role', $roles, array('roleid', 'name'), 'staff_add_edit_role', $selected);
                     ?>
                     <!-- <hr />
                        <h4 class="font-medium mbot15 bold"><?php //echo _l('staff_add_edit_permissions');
                                                            ?></h4> -->
                     <?php
                     // $permissionsData = ['funcData' => ['staff_id' => isset($member) ? $member->staffid : null]];
                     // if (isset($member)) {
                     //    $permissionsData['member'] = $member;
                     // }
                     // $this->load->view('admin/staff/permissions', $permissionsData);
                     ?>
                     <!-- </div> -->
                  </div>
               </div>
            </div>
         </div>
         <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
         <?php if (isset($member)) { ?>
            <div class="col-md-7 small-table-right-col">
               <!-- <div class="panel_s">
                  <div class="panel-body">
                     <h4 class="no-margin">
                        <?php echo _l('staff_add_edit_notes'); ?>
                     </h4>
                     <hr class="hr-panel-heading" />
                     <a href="#" class="btn btn-success" onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
                     <div class="clearfix"></div>
                     <hr class="hr-panel-heading" />
                     <div class="mbot15 usernote hide inline-block full-width">
                        <?php echo form_open(admin_url('misc/add_note/' . $member->staffid . '/staff')); ?>
                        <?php echo render_textarea('description', 'staff_add_edit_note_description', '', array('rows' => 5)); ?>
                        <button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                     </div>
                     <div class="clearfix"></div>
                     <div class="mtop15">
                        <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
                           <thead>
                              <tr>
                                 <th width="50%"><?php echo _l('staff_notes_table_description_heading'); ?></th>
                                 <th><?php echo _l('staff_notes_table_addedfrom_heading'); ?></th>
                                 <th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
                                 <th><?php echo _l('options'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($user_notes as $note) { ?>
                                 <tr>
                                    <td width="50%">
                                       <div data-note-description="<?php echo $note['id']; ?>">
                                          <?php echo check_for_links($note['description']); ?>
                                       </div>
                                       <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide inline-block full-width">
                                          <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                          <div class="text-right mtop15">
                                             <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                             <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                          </div>
                                       </div>
                                    </td>
                                    <td><?php echo $note['firstname'] . ' ' . $note['lastname']; ?></td>
                                    <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
                                    <td>
                                       <?php if ($note['addedfrom'] == get_staff_user_id() || has_permission('staff', '', 'delete')) { ?>
                                          <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                                          <a href="<?php echo admin_url('misc/delete_note/' . $note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                       <?php } ?>
                                    </td>
                                 </tr>
                              <?php } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div> -->
               
               <div class="panel_s">
                  <div class="panel-body">
                     <h4 class="no-margin">
                        <?php echo _l('projects'); ?>
                     </h4>
                     <hr class="hr-panel-heading" />
                     <div class="_filters _hidden_inputs hidden staff_projects_filter">
                        <?php echo form_hidden('staff_id', $member->staffid); ?>
                     </div>
						<div class="table-responsive">

                     <?php render_datatable(array(
                        _l('project_name'),
                        _l('project_start_date'),
                        _l('project_deadline'),
                        _l('project_status'),
                     ), 'staff-projects'); ?>
						</div>
                  </div>
               </div>
            </div>
         <?php } ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
   <?php init_tail(); ?>
   <script>
      $(function() {

         $('select[name="role"]').on('change', function() {
            var roleid = $(this).val();
            init_roles_permissions(roleid, true);
         });

         $('input[name="administrator"]').on('change', function() {
            var checked = $(this).prop('checked');
            var isNotStaffMember = $('.is-not-staff');
            if (checked == true) {
               isNotStaffMember.addClass('hide');
               $('.roles').find('input').prop('disabled', true).prop('checked', false);
            } else {
               isNotStaffMember.removeClass('hide');
               isNotStaffMember.find('input').prop('checked', false);
               $('.roles').find('.capability').not('[data-not-applicable="true"]').prop('disabled', false)
            }
         });

         $('#is_not_staff').on('change', function() {
            var checked = $(this).prop('checked');
            var row_permission_leads = $('tr[data-name="leads"]');
            if (checked == true) {
               row_permission_leads.addClass('hide');
               row_permission_leads.find('input').prop('checked', false);
            } else {
               row_permission_leads.removeClass('hide');
            }
         });

         init_roles_permissions();

         appValidateForm($('.staff-form'), {
            firstname: 'required',
            lastname: 'required',
            username: 'required',
            password: {
               required: {
                  depends: function(element) {
                     return ($('input[name="isedit"]').length == 0) ? true : false
                  }
               }
            },
            email: {
               required: true,
               email: true,
               remote: {
                  url: admin_url + "misc/staff_email_exists",
                  type: 'post',
                  data: {
                     email: function() {
                        return $('input[name="email"]').val();
                     },
                     memberid: function() {
                        return $('input[name="memberid"]').val();
                     }
                  }
               }
            }
         });
      });
   </script>
   </body>

   </html>

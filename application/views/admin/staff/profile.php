<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="panel_s custom-panel1 edit_profile_panel">

                    <div class="panel-body">
                      <div class="panel-header">
                        <h1><?php echo $title; ?> <span>Here you can update your profile information. </span></h1>
                       <hr class="hr-panel-heading" />
                      </div>
                       <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'staff_profile_table','autocomplete'=>'off')); ?>

                     <?php /*if($current_user->profile_image == NULL){ ?>
                     <div class="form-group">
                        <label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                        <input type="file" name="profile_image" class="form-control" id="profile_image">
                    </div>
                    <?php } */?>
                    <?php /*if($current_user->profile_image != NULL){ ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-9">
                                <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-thumb'),'thumb'); ?>
                            </div>
                            <div class="col-md-3 text-right">
                                <a href="<?php echo admin_url('staff/remove_staff_profile_image'); ?>"><i class="fa fa-remove"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php } */?>
                    <div class="form-group">
                      <?php
                  //  echo "<pre>";print_r($member);echo "</pre>";
                      $firstname = (isset($member) ? $member->firstname : '');
                            $input_class = (!empty($firstname) ? 'label-up' : '');  ?>
                      <?php echo render_input('firstname',_l('full_name_req'), $firstname, '', [], [], '', $input_class); ?>
                    </div>
                    <?php /*
                    <div class="form-group">
                        <label for="lastname" class="control-label"><?php echo _l('staff_add_edit_lastname'); ?></label>
                        <input type="text" class="form-control" name="lastname" value="<?php if(isset($member)){echo $member->lastname;} ?>">
                    </div>
                    <?php */ ?>
                    <div class="form-group">
                      <?php $organisation = (isset($member) ? $member->organisation : '');
                            $input_class = (!empty($organisation) ? 'label-up' : '');  ?>
                      <?php echo render_input('organisation',(get_slug($this->session->userdata('staff_role'))=='ae-global')?_l('org'):_l('org_dept_req'), $organisation, '', [], [], '', $input_class); ?>
                    </div>

                    <div class="form-group">
                      <?php $email = (isset($member) ? $member->email : '');
                            $input_class = (!empty($email) ? 'label-up' : '');  ?>
                      <?php echo render_input('email','staff_add_edit_email', $email, '', ['disabled'=>'disabled'], [], '', $input_class); ?>
                    </div>

                    <div class="form-group">
                      <?php $phonenumber = (isset($member) ? $member->phonenumber : '');
                            $input_class = (!empty($phonenumber) ? 'label-up' : ''); ?>
                      <?php echo render_input('phonenumber','staff_add_edit_phonenumber', $phonenumber, '', [], [], '', $input_class); ?>
                    </div>


       				<button type="submit" class="btn btn-custom">Save</button>
             <?php echo form_close(); ?>
         </div>
     </div>
 </div>
 <div class="col-md-5">
    <div class="panel_s update_password_panel">

        <div class="panel-body">
          <div class="panel-header">
            <h1><?php echo _l('staff_edit_profile_change_your_password'); ?> <span>Here you can change your password. </span></h1>
           <hr class="hr-panel-heading" />
          </div>
        <?php echo form_open('admin/staff/change_password_profile',array('id'=>'staff_password_change_form')); ?>
        <div class="form-group">
            <?php echo render_input('oldpassword','staff_edit_profile_change_old_password', '','password'); ?>
        </div>
        <div class="form-group">
          <?php echo render_input('newpassword','staff_edit_profile_change_new_password', '', 'password',['id'=>"newpassword",'data-toggle'=>"tooltip",'data-html'=>'true','data-placement'=>'top','title'=> _l('password_detail'),'data-container'=>'body']); ?>
          <p id="password_hints" style="color:red;font-size:12px;"></p>
        </div>
        <div class="form-group">
          <?php echo render_input('newpasswordr','staff_edit_profile_change_repeat_new_password', '', 'password'); ?>

        </div>
        <button type="submit" class="btn btn-custom"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
        <hr>
        <!-- <div class="col-md-12 notes mB20">
                <p>The new password must meet the following criteria:</p>
                <ul>
                  <li>Should be between 8 and 17 characters long.</li>
                  <li>Should contain at least 1 number.</li>
                  <li>Should contain at least 1 character in uppercase.</li>
                  <li>Should contain at least 1 special character.</li>
                </ul>
          </div> -->
    </div>
        <?php if($member->last_password_change != NULL){ ?>
        <div class="panel-footer">
            <?php echo _l('staff_add_edit_password_last_changed'); ?>:
            <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
            <?php echo time_ago($member->last_password_change); ?>
          </span>
        </div>
        <?php } ?>
        
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
 $(function(){
   appValidateForm($('#staff_profile_table'),{
     firstname: {
       required: true,
       maxlength: 50,
       charsonly: true,
       fullname: true,
       noSpace: true,
     },
     organisation: {
       required: true,
       maxlength: 125,
       chars_allowed_special: true,
       noSpace: true,
       <?php $slug =get_slug($this->session->userdata('staff_role'));
       if($slug !='ae-global'){
       ?>
       organization: true,
       <?php }?>
     },
     email: {
       required: true,
       email: true,
       maxlength: 100
     },
     phonenumber: {
       required: true,
       digits: true,
       minlength: 8,
       maxlength: 12
     },
   }, adminSubmit);
   appValidateForm($('#staff_password_change_form'),{oldpassword:'required',newpassword:'required',newpasswordr: { equalTo: "#newpassword"}},
  //  adminSubmit2
   );
 });

 const adminSubmit = (form) => {
   add_loader('.edit_profile_panel');
   form.submit();
 }
 const adminSubmit2 = (form) => {
   add_loader('.update_password_panel');
   form.submit();
 }
 $("#newpassword").keyup(function (e) { 
        var str="";
        var count=0;
        const pwd=e.target.value;
        const lower=/[a-z]/g;
        const upper=/[A-Z]/g;
        const number=/[0-9]/g;
        const special=/[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if(pwd.match(upper)==null){
            str=str+", Uppercase";
            count=count+1;
        }
        if(pwd.match(number)==null){
            str=str+", Number";
            count=count+1;
        }
        if(pwd.match(lower)==null){
            str=str+", Lowercase";
            count=count+1;
        }
        if(pwd.match(special)==null){
            str=str+", Spl character";
            count=count+1;            
        }
        if(pwd.length<8){
            str=str+", Length between 8-17 characters";
            count=count+1;
        }
        if(pwd.length>17){
            str=str+", Length between 8-17 characters";
            count=count+1;
        }
        var error=str.substr(1,(str.length-1));
        $('#password_hints').html((error.length>0)?("*" +error+' is required'):error);
        console.log(count);
        if(count>0){
            $("#staff_password_change_form").submit(function(e){
            e.preventDefault();
            });
        }else{
            $("#staff_password_change_form").unbind("submit");
        }
    });
</script>
</body>
</html>

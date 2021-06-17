<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- <div class="mtop40">
  <div class="col-md-4 col-md-offset-4 text-center forgot-password-heading">
    <h1 class="text-uppercase mbot20"><?php echo _l('customer_forgot_password_heading'); ?></h1>
  </div>
  <div class="col-md-4 col-md-offset-4">
    <div class="panel_s">
      <div class="panel-body">
        <?php
        //  echo form_open($this->uri->uri_string(),['id'=>'forgot-password-form']);
        ?>
        <?php
        // echo validation_errors('<div class="alert alert-danger text-center">', '</div>');
        ?>
        <?php
        //  if($this->session->flashdata('message-danger')){
        ?>
        <div class="alert alert-danger">
          <?php
          // echo $this->session->flashdata('message-danger');
          ?>
        </div>
        <?php
        // }
        ?>
        <?php
        // echo render_input('email','customer_forgot_password_email',set_value('email'),'email');
        ?>
        <div class="form-group">
          <button type="submit" class="btn btn-info btn-block"><?php echo _l('customer_forgot_password_submit'); ?></button>
        </div>
        <?php
        //  echo form_close();
        ?>
      </div>
    </div>
  </div>
</div> -->











<?php echo form_open($this->uri->uri_string(), ['id' => 'forgot-password-form']); ?>
<!-- <?php
      // echo validation_errors('<div class="alert alert-danger text-center">', '</div>');
      ?>
        <?php
        //  if($this->session->flashdata('message-danger')){
        ?>
        <div class="alert alert-danger">
          <?php
          // echo $this->session->flashdata('message-danger');
          ?>
        </div>
        <?php
        // }
        ?> -->

<div class="login-container">
  <div class="row">
    <div class="col-md-5 col-sm-12">
      <div class="mobile-logo">
        <img src="<?php echo base_url('assets/images/caap-patna-mob.jpg') ?>" alt="">
        <h3><?php echo get_option('companyname');?></h3>
      </div>
      <div class="login-form">
        <div class="row mB0">
          <div class="form-group">
            <h1><i class="text-uppercase mB0"><?php echo _l('admin_auth_forgot_password_heading'); ?></i><span>
                Enter your registered email id, We will send you the reset password link.
              </span></h1>
          </div>
        </div>
        <div class="row">
          <div class="form-input-field mB15 mT10">
            <?php echo render_input('email', 'customer_forgot_password_email', set_value('email'), 'email'); ?>
            <?php echo form_error('email'); ?>
          </div>
          <div class="form-field">
            <button type="submit" class="btn btn-info btn-block">Recover Password</button>
          </div>
          <div class="input-field mT20">
            <span class="d-block text-center font12" style="color: #314e73">Back to login ? Click <a href="<?php echo site_url('login/'); ?>">here</a>
            </span>

          </div>

        </div>
        <?php echo form_close(); ?>
      </div>
    </div>

    <div class="col-lg-7 col-md-7 d-sm-none">
      <div class="login-logo">
        <img src="<?php echo base_url('assets/images/caap-patna.jpg') ?>" alt="">
        <h3><?php echo get_option('companyname'); ?></h3>
        <p><?php echo _l("company_name_tagline"); ?></p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <?php echo _l("footer_info_non_login"); ?>
    </div>
  </div>
</div>

<script>
  $('.navbar-default').attr('style', 'display:none;');
  $("form#forgot-password-form :input").each(function() {
    if ($(this).val()) {
      $(this).addClass("label-up");
    } else {
      $(this).addClass("labellll-up");
    }
  })
</script>

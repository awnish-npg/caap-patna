<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>
<!-- <body class="authentication reset-password">
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 authentication-form-wrapper">
    <div class="company-logo">
     <?php echo get_company_logo(); ?>
   </div>
   <div class="mtop40 authentication-form">
    <h1><?php echo _l('admin_auth_reset_password_heading'); ?></h1>
    <?php echo form_open($this->uri->uri_string()); ?>
    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
    <?php $this->load->view('authentication/includes/alerts'); ?>
    <?php echo render_input('password', 'admin_auth_reset_password', '', 'password'); ?>
    <?php echo render_input('passwordr', 'admin_auth_reset_password_repeat', '', 'password'); ?>
    <div class="form-group">
      <button type="submit" class="btn btn-info btn-block"><?php echo _l('auth_reset_password_submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</div>
</div>
</body>
</html> -->

<?php echo form_open($this->uri->uri_string(), ['id' => 'reset-password-form']); ?>
<div class="login-container">
    <div class="row">
        <div class="col-md-5">

            <div class="login-form">
                <div class="row mB0">
                    <div class="form-group">
                        <h1><i class="text-uppercase mB0">RESET PASSWORD</i>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group mB15 mT10">

                        <!-- Normal Inputs -->

                        <div class="form-input-field">
                        <input id="password" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php echo _l('password_detail');?>" data-container="body" class="" type="password" required="" name="password">
                        <label for="password" title="Password" data-title="Password"></label>
                        <p id="password_hints" style="color:red;font-size:12px;"></p>
                        <?php
                        echo form_error('password')
                        ?>
                        </div>
                    </div>
                    <div class="form-group mB15">
                        <div class="form-input-field recover-field">
                          <input class="" type="password" required="" id="passwordr" name="passwordr">
                          <label for="passwordr" title="Repeat Password" data-title="Repeat Password"></label>
                          <?php
                          echo form_error('passwordr')
                            ?>
                        </div>
                        <!-- end Normal Inputs -->
                        <?php //echo render_input('password', 'admin_auth_reset_password', '', 'password', ['id' => "password", 'data-toggle' => "tooltip", 'data-html' => 'true', 'data-placement' => 'top', 'title' => 'Password must contain atleast 8 and atmost 17 char, including UPPER/lowercase, numbers and special characters', 'data-container' => 'body']); ?>
                        <!-- <p id="password_hints" style="color:red;font-size:12px;"></p> -->
                        <?php //echo form_error('password') ?>

                        <?php //echo render_input('passwordr', 'admin_auth_reset_password_repeat', '', 'password'); ?>
                        <?php //echo form_error('passwordr') ?>
                    </div>
                    <div class="form-field">
                        <button type="submit" class="btn btn-info btn-block">RESET PASSWORD</button>
                    </div>
                    <!-- <div class="input-field mT20">
                            <span class="d-block text-center font12" style="color: #314e73"> Your password has been sent to your email id. Click <a href="<?php echo site_url('authentication/'); ?>">here</a> to login
                            </span>

                        </div> -->

                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <div class="col-lg-7 col-md-7">
            <div class="login-logo">
                <img src="<?php echo base_url('assets/images/cpcb-logo.png') ?>" alt="">
                <h3><?php echo get_option('companyname'); ?></h3>
                <p><?php echo _l("company_name_tagline"); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    $('.navbar-default').attr('style', 'display:none;');
    $("form#reset-password-form :input").each(function() {
        if ($(this).val()) {
            $(this).addClass("label-up");
        } else {
            $(this).addClass("labellll-up");
        }
    })
    $("#password").keyup(function(e) {
        var str = "";
        var count = 0;
        const pwd = e.target.value;
        const lower = /[a-z]/g;
        const upper = /[A-Z]/g;
        const number = /[0-9]/g;
        const special = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if (pwd.match(upper) == null) {
            str = str + ", Uppercase";
            count = count + 1;
        }
        if (pwd.match(number) == null) {
            str = str + ", Number";
            count = count + 1;
        }
        if (pwd.match(lower) == null) {
            str = str + ", Lowercase";
            count = count + 1;
        }
        if (pwd.match(special) == null) {
            str = str + ", Spl character";
            count = count + 1;
        }
        if (pwd.length < 8) {
            str = str + ", Length between 8-17 characters";
            count = count + 1;
        }
        if (pwd.length > 17) {
            str = str + ", Length between 8-17 characters";
            count = count + 1;
        }
        var error = str.substr(1, (str.length - 1));
        $('#password_hints').html((error.length > 0) ? ("*" + error + ' is required') : error);
        console.log(count);
        if (count > 0) {
            $("#reset-password-form").submit(function(e) {
                e.preventDefault();
            });
        } else {
            $("#reset-password-form").unbind("submit");
        }
    });
</script>
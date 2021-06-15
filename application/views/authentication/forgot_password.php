<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>

<body class="authentication forgot-password">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php echo form_open($this->uri->uri_string()); ?>
            <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
            <?php $this->load->view('authentication/includes/alerts'); ?>

            <div class="login-container">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <div class="mobile-logo">
                            <img src="<?php echo base_url('assets/images/mobile-logo.png') ?>" alt="">
                            <h3><?php echo get_option('companyname'); ?></h3>
                        </div>
                        <div class="login-form">
                            <div class="row mB0">
                                <div class="form-group">
                                    <h1><abbr class="text-uppercase"><?php echo _l('admin_auth_forgot_password_heading'); ?></abbr><span>Enter your email ID to recover your password</span></h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-input-field mB15 mT10">
                                    <?php echo render_input('email', 'admin_auth_forgot_password_email', '', 'email'); ?>
                                </div>
                                <div class="form-field">
                                    <button type="submit" class="btn btn-info btn-block">Recover Password</button>
                                </div>
                                <div class="input-field mT20">
                                    <span class="d-block text-center font12" style="color: #314e73"> Back to login? Click <a href="<?php echo site_url('admin/authentication'); ?>">here</a>
                                    </span>

                                </div>

                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-7 d-sm-none">
                        <div class="login-logo">
                            <img src="<?php echo base_url('assets/images/cpcb-logo.png') ?>" alt="">
                            <h3><?php echo get_option('companyname'); ?></h3>
                            <p><?php echo _l("company_name_tagline"); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php echo _l("footer_info_non_login"); ?>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#email').val('');
    });
</script>
</body>

</html>
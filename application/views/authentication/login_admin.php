<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head.php'); ?>

<body class="login_admin" <?php if (is_rtl()) {
                                echo ' dir="rtl"';
                            } ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 authentication-form-wrapper">
                <!-- <div class="company-logo">
                    <?php get_company_logo(); ?>
                </div> -->
                <div class="mtop40 authentication-form">
                    <?php $this->load->view('authentication/includes/alerts'); ?>
                    <?php echo form_open($this->uri->uri_string());  ?>
                    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                    <?php hooks()->do_action('after_admin_login_form_start'); ?>
                    <div class="login-container">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <div class="mobile-logo">
                                    <img src="<?php echo base_url('assets/images/caap-patna-mob.jpg') ?>" alt="">
                                    <h3><?php echo get_option('companyname'); ?></h3>
                                </div>
                                <div class="login-form">
                                    <div class="row mB0">
                                        <div class="form-group">
                                            <h1>USER LOGIN</h1>

                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="form-group mB15 mT10">
                                            <div class="form-input-field">
                                                <input type="email" name="email" id="email">
                                                <label for="email" title="<?php echo _l('admin_auth_login_email'); ?>" data-title="<?php echo _l('admin_auth_login_email'); ?>"></label>
                                            </div>
                                        </div>

                                        <div class="form-group mB15">
                                            <div class="form-input-field">
                                                <input type="password" id="password" name="password" autocomplete="new-password">
                                                <label for="password" title="<?php echo _l('admin_auth_login_password'); ?>" data-title="<?php echo _l('admin_auth_login_password'); ?>"></label>
                                                <a class="d-block text-right mT5" href="<?php echo admin_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                                            </div>
                                        </div>
                                        <!-- <div class="input-field">
                                            <input type="email" id="email" name="email" class="validate" autofocus>
                                            <label for="email"><?php echo _l('admin_auth_login_email'); ?></label>
                                        </div> -->

                                        <!-- <div class="form-group">

                                            <input type="email" id="email" name="email" class="form-control"
                                                autofocus="1" placeholder="<?php echo _l('admin_auth_login_email'); ?>">
                                        </div> -->
                                        <!-- <div class="input-field">
                                            <input type="password" id="password" name="password" class="validate"
                                                autofocus>

                                            <label for="password"><?php echo _l('admin_auth_login_password'); ?></label>
                                        </div> -->
                                        <?php if (get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') { ?>
                                            <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>">
                                            </div>
                                        <?php } ?>
                                        <!-- <div class="checkbox">
                                            <label for="remember">
                                                <input type="checkbox" id="remember" name="remember">
                                                <?php echo _l('admin_auth_login_remember_me'); ?>
                                            </label>
                                        </div> -->
                                        <div class="input-field">
                                            <button type="submit" class="btn btn-info btn-block"><?php echo _l('admin_auth_login_button'); ?>
                                                <div class="lds-dual-ring"></div>
                                            </button>
                                        </div>
                                        <p class="text-center mT30 font12 hide">
                                            An initiative by <br /> <img src="<?php echo base_url('assets/images/powered-by.png') ?>" alt="">
                                        </p>
                                        <!-- <div class="input-field">
                                            <a class="d-block text-center"
                                                href="<?php //echo admin_url('authentication/forgot_password');
                                                        ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                                        </div> -->

                                        <?php hooks()->do_action('before_admin_login_form_close'); ?>
                                        <?php echo form_close(); ?>
                                    </div>

                                </div>

                                <div class="login-links">
                                    <a href="<?php echo site_url('login'); ?>">Go to Other Login</a>
                                    <!-- <a href="< ?php //echo site_url('/authentication/registerccr'); ?>">Other user login/signup</a> -->
                                    <!-- <a href="#">Raise ticket links (by Surveyor)</a> -->
                                </div>

                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 d-sm-none">
                                <div class="login-logo">
                                    <img src="<?php echo base_url('assets/images/caap-patna.jpg') ?>" alt="">
                                    <h3><?php echo get_option('companyname'); ?></h3>
                                    <p><?php echo _l("company_name_tagline"); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?php echo _l("footer_info_non_login"); ?>
                            </div>
                            <div class="col-lg-6">
                                <ul class="footer-links">
                                    <li><a href="" class="pull-right" onclick="disclaimer(); return false;">Disclaimer</a></li>
                                    <li><a href="" class="pull-right hide">Terms &amp; Conditions</a></li>
                                    <li><a href="" class="pull-right hide">Privacy Policy</a></li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade assign-modal" id="disclaimer">
            <div class="modal-dialog" role="document" style="width: 350px;">

                <div class="modal-content">
                    <div class="modal-header p-0">
                        <div class="panel panel-default sub-ticket-panel mB0 border-0">
                            <div class="panel-heading accept">
                                Disclaimer
                            </div>
                        </div>

                    </div>
                    <div class="p15 font-size-13">
                        <p>The views/analysis expressed in this application do not necessarily reflect the views of Shakti Sustainable Energy Foundation. The Foundation also does not guarantee the accuracy of any data included in this application nor does it accept any responsibility for the consequences of its use.</p>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-container">
                            <button type="submit" class="btn btn-custom" data-dismiss="modal">OK</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->

            </div>
        </div>
</body>


<?php hooks()->do_action('before_js_scripts_render'); ?> <?php echo app_compile_scripts(); ?> <?php
                                                                                                /**
                                                                                                 * Check for any alerts stored in session
                                                                                                 */
                                                                                                app_js_alerts();
                                                                                                ?> <script>
    function disclaimer() {
        $('#disclaimer').modal('show');

    }
</script>

</html>

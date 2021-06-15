<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 authentication-form-wrapper">
            <?php //get_company_logo();
            ?>
            <div class="authentication-form">
                <?php echo form_open($this->uri->uri_string(), array('id' => 'login_form')); ?>
                <?php hooks()->do_action('clients_login_form_start'); ?>
                <div class="login-container">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                        <div class="mobile-logo">
                                    <img src="<?php echo base_url('assets/images/mobile-logo.png') ?>" alt="">
                                    <h3><?php echo get_option('companyname');?></h3>
                                </div>
                            <div class="login-form">
                                <div class="row mB0">
                                    <div class="form-group">
                                        <h1>SURVEYOR / CALL CENTER LOGIN</h1>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-input-field mB15 mT10">
                                        <input type="text" name="email" id="email" value="<?php echo set_value('email'); ?>">
                                        <label for="email" title="<?php echo _l('clients_login_email'); ?>" data-title="<?php echo _l('clients_login_email'); ?>"></label>
                                        <?php echo form_error('email'); ?>
                                    </div>
                                    <div class="form-input-field mB15">
                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>">
                                        <label for="password" title="<?php echo _l('clients_login_password'); ?>" data-title="<?php echo _l('clients_login_password'); ?>"></label>
                                        <?php echo form_error('password'); ?>
                                        <a class="d-block text-right mT5" href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>
                                    </div>
                                    <div class="input-field">
                                        <button type="submit" class="btn btn-info btn-block"><?php echo _l('admin_auth_login_button'); ?></button>
                                        <!-- <a  href="<?php echo site_url('authentication/register'); ?>"
                                                class="btn btn-info btn-register mT0"><?php echo _l('clients_register_string'); ?></a> -->
                                    </div>
                                    <div class="input-field mT20">
                                        <!-- <span class="font12 text-center d-block">Do not have an account? Please click <a href="< ?php //echo site_url('authentication/register'); ?>" class="text-center mT0">here to register</a></span> -->
                                    </div>
                                    <p class="text-center mT30 font12 hide">
                                    An initiative by <br /> <img src="<?php echo base_url('assets/images/powered-by.png') ?>" alt="">
                                    </p>

                                </div>
                            </div>
                            <div class="login-links">
                                <a href="<?php echo site_url('admin/authentication'); ?>">Go to User Login</a>
                                <!-- <a href="< ?php //echo site_url('authentication/registerccr'); ?>">Call center signup</a> -->
                                <!-- <a href="#">Raise ticket link(by Surveyor)</a> -->
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                        <div class="col-lg-7 col-md-7 col-sm-7 d-sm-none">
                            <div class="login-logo">
                                <img src="<?php echo base_url('assets/images/cpcb-logo.png') ?>" alt="">
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
</div>
<!-- <div class="mtop40">
    <div class="col-md-4 col-md-offset-4 text-center">
        <h1 class="text-uppercase mbot20 login-heading">
            <?php
            echo _l(get_option('allow_registration') == 1 ? 'clients_login_heading_register' : 'clients_login_heading_no_register');
            ?>
        </h1>
    </div>
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
        <?php echo form_open($this->uri->uri_string(), array('class' => 'login-form')); ?>
        <?php hooks()->do_action('clients_login_form_start'); ?>
        <div class="panel_s">
            <div class="panel-body">
                <div class="form-group">

                </div>
                <div class="form-group">

                </div>
                <?php if (
                    get_option('use_recaptcha_customers_area') == 1
                    && get_option('recaptcha_secret_key') != ''
                    && get_option('recaptcha_site_key') != ''
                ) { ?>
                <div class="g-recaptcha mbot15" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                <?php echo form_error('g-recaptcha-response'); ?>
                <?php } ?>
                <div class="checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">
                        <?php echo _l('clients_login_remember'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit"
                        class="btn btn-info btn-block"><?php echo _l('clients_login_login_string'); ?></button>
                    <?php if (get_option('allow_registration') == 1) { ?>
                    <a href="<?php echo site_url('authentication/register'); ?>"
                        class="btn btn-success btn-block"><?php echo _l('clients_register_string'); ?>
                    </a>
                    <?php } ?>
                </div>
                <a
                    href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('customer_forgot_password'); ?></a>
                <?php hooks()->do_action('clients_login_form_end'); ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div> -->
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
<script>
    $("form#login_form :input").each(function() {
        if ($(this).val()) {
            $(this).addClass("label-up");
        } else {
            $(this).addClass("labellll-up");
        }
    })
    function disclaimer() {
        $('#disclaimer').modal('show');

    }
</script>

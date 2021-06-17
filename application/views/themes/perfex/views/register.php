<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $data = $this->session->flashdata('type'); ?>
<div class="login_admin">
    <div class="">
        <div class="row">
            <div class="col-md-12 authentication-form-wrapper">
                <div class=" authentication-form">
                    <?php if ($data == "ccr") {
                        echo form_open('authentication/registerccr', ['id' => 'register-form']);
                    } else {
                        echo form_open('authentication/register', ['id' => 'register-form']);
                    }
                    ?>

                    <div class="login-container">
                        <div class="">
                            <div class="row">

                                <div class="col-lg-5 col-md-5 col-sm-12">
                                    <div class="mobile-logo">
                                        <img src="<?php echo base_url('assets/images/caap-patna-mob.jpg') ?>" alt="">
                                        <h3><?php echo get_option('companyname');?></h3>
                                    </div>
                                    <div class="login-form">
                                        <div class="row">
                                            <div class="form-group mB0">
                                                <h1 class="text-uppercase">
                                                    <?php echo _l('clients_register_heading'); ?></h1>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mB15 mT10">
                                                <div class="form-input-field">
                                                    <input type="text" name="firstname" id="name" value="<?php echo set_value('firstname'); ?>">
                                                    <label for="name" title="Full Name*" data-title="First Name"></label>
                                                    <?php echo form_error('firstname'); ?>
                                                </div>
                                            </div>

                                            <div class="form-group mB15">
                                                <div class="form-group">
                                                    <div class="form-input-field">
                                                        <input type="email" name="email" id="email" value="<?php echo set_value('email'); ?>">
                                                        <label for="name" title="Email*" data-title="Email"></label>
                                                        <?php echo form_error('email'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mB15">
                                                <div class="form-group">
                                                    <div class="form-input-field">
                                                        <input type="tel" name="contact_phonenumber" id="contact_phonenumber" value="<?php echo set_value('contact_phonenumber'); ?>">
                                                        <label for="name" title="Phone*" data-title="Phone"></label>
                                                        <?php echo form_error('contact_phonenumber'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mB15">
                                                <div class="form-group">
                                                    <div class="form-input-field">
                                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" data-toggle="tooltip" data-html="true" title="<?php echo _l('password_detail');?>" data-container="body" data-placement="top">
                                                        <label for="name" title="Password*" data-title="Password"></label>
                                                        <?php echo form_error('password'); ?>
                                                        <p id="password_hints" style="color:red;font-size:12px;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mB15">
                                                <div class="form-group">
                                                    <div class="form-input-field">
                                                        <input type="password" name="passwordr" id="passwordr" value="<?php echo set_value('passwordr'); ?>">
                                                        <label for="name" title="Confirm Password*" data-title="Confirm Password"></label>
                                                        <?php echo form_error('passwordr'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mB15">
                                                <div class="form-group">
                                                    <div class="form-input-field">
                                                        <input type="text" name="company" id="company" value="<?php echo set_value('company'); ?>">
                                                        <label for="org" title="<?php echo _l('org_dept_req'); ?>" data-title="Organization"></label>
                                                        <?php echo form_error('company'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-field">
                                                <div class="form-group">
                                                    <button type="submit" autocomplete="off" class="btn btn-info btn-block"><?php echo _l('clients_register_string'); ?></button>
                                                </div>
                                            </div>
                                            <div class="input-field mT20">
                                                <span class="d-block text-center"> Already registered? Enter your
                                                    credentials to <a href="<?php echo site_url('authentication/login'); ?>">Login</a>
                                                </span>

                                            </div>
                                        </div>

                                    </div>




                                    <?php if (get_option('use_recaptcha_customers_area') == 1 && get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') { ?>
                                        <div class="col-md-12 register-recaptcha">
                                            <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                                            <?php echo form_error('g-recaptcha-response'); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-7 d-sm-none">
                                    <div class="login-logo">
                                        <img src="<?php echo base_url('assets/images/caap-patna.jpg') ?>" alt="">
                                        <h3><?php echo get_option('companyname'); ?></h3>
                                        <p><?php echo _l("company_name_tagline"); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php echo form_close(); ?>
                    <div class="row">
                        <?php echo _l("footer_info_non_login"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            appValidateForm($('#register-form'), {
                firstname: {
                    required: true,
                    maxlength: 50,
                    charsonly: true,
                    fullname: true,

                },
                company: {
                    required: true,
                    maxlength: 125,
                    chars_allowed_special: true,
                    organization: true,
                },
                contact_phonenumber: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 12
                },

            });

        });


        $("form#register-form :input").each(function() {
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
                $("#register-form").submit(function(e) {
                    e.preventDefault();
                });
            } else {
                $("#register-form").unbind("submit");
            }
        });
    </script>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- <div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 mtop40 text-center">
            <h1 class="text-uppercase"><?php
            //  echo _l('customer_reset_password_heading'); ?></h1>
            <div class="panel_s text-left">
                <div class="panel-body">
                    <?php 
                    // echo form_open($this->uri->uri_string()); ?>
                    <?php 
                    // echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                    <?php
                    //  if($this->session->flashdata('message-danger')){ ?>
                    <div class="alert alert-danger">
                        <?php 
                        // echo $this->session->flashdata('message-danger'); ?>
                    </div>
                    <?php 
                // } ?>
                    <?php 
                    // echo render_input('password','customer_reset_password','','password'); ?>
                    <?php 
                    // echo render_input('passwordr','customer_reset_password_repeat','','password'); ?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-block"><?php 
                        // echo _l('customer_reset_action'); ?></button>
                    </div>
                    <?php 
                    // echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div> -->
<?php echo form_open($this->uri->uri_string(),['id'=>'reset-password-form']); ?>
    <!-- <?php
    //  echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
        <?php
        //  if($this->session->flashdata('message-danger')){ ?>
        <div class="alert alert-danger">
          <?php 
        //   echo $this->session->flashdata('message-danger'); ?>
        </div>
        <?php 
    //   } ?> -->

    <div class="login-container">
        <div class="row">
            <div class="col-md-5">

                <div class="login-form">
                    <div class="row mB0">
                        <div class="form-group">
                            <h1><i
                                    class="text-uppercase mB0">RESET PASSWORD</i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-input-field mB15 mT10">
                        <?php echo render_input('password','customer_reset_password','','password',['id'=>"password",'data-toggle'=>"tooltip",'data-html'=>'true','data-placement'=>'top','title'=>_l('password_detail'),'data-container'=>'body']); ?>
                        <?php echo form_error('password')?>
                        <p id="password_hints" style="color:red;font-size:12px;"></p>
                        <?php echo render_input('passwordr','customer_reset_password_repeat','','password'); ?>
                        <?php echo form_error('passwordr')?>
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
                    <img src="<?php echo base_url('assets/images/caap-patna.jpg') ?>" alt="">
                    <h3><?php echo get_option('companyname');?></h3>
                    <p><?php echo _l("company_name_tagline");?></p>
                </div>
            </div>
        </div>
    </div>

<!-- <body class="authentication reset-password">
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 authentication-form-wrapper">
    <div class="company-logo">
     <?php 
    //  echo get_company_logo(); ?>
   </div>
   <div class="mtop40 authentication-form">
    <h1><?php 
    // echo _l('admin_auth_reset_password_heading'); ?></h1>
    <?php 
    // echo form_open($this->uri->uri_string()); ?>
    <?php 
    // echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                    <?php 
                    // if($this->session->flashdata('message-danger')){ ?>
                    <div class="alert alert-danger">
                        <?php 
                        // echo $this->session->flashdata('message-danger'); ?>
                    </div>
                    <?php
                //  } ?>
    <?php
    //  echo render_input('password','customer_reset_password','','password'); ?>
     <?php 
    //  echo render_input('passwordr','customer_reset_password_repeat','','password'); ?>
    <div class="form-group">
      <button type="submit" class="btn btn-info btn-block"><?php echo _l('auth_reset_password_submit'); ?></button>
    </div>
    <?php 
    // echo form_close(); ?>
  </div>
</div>
</div>
</div>
</body>
 </html> -->
<script>
    $('.navbar-default').attr('style', 'display:none;');
    $("form#reset-password-form :input").each(function(){
        if ($(this).val()) {
            $(this).addClass("label-up");
        } else {
            $(this).addClass("labellll-up");
        }
    })
    $("#password").keyup(function (e) { 
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
            $("#reset-password-form").submit(function(e){
            e.preventDefault();
            });
        }else{
            $("#reset-password-form").unbind("submit");
        }
    });
</script> 
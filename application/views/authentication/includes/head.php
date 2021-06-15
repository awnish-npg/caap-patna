<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


    <title>
        <?php echo get_option('companyname'); ?> - <?php echo _l('admin_auth_login_heading'); ?>
    </title>
    <?php echo app_compile_css('admin-auth'); ?>
    <style>

    </style>
    <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <?php } ?>
    <?php if(file_exists(FCPATH.'assets/css/custom.css')){ ?>
    <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet" id="custom-css">
    <?php } ?>
    <?php hooks()->do_action('app_admin_authentication_head'); ?>
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>

    <script>
    $(document).on("blur", ".form-input-field input", function(e) {

        if ($(this).val() !== "") {
            $(this).addClass("label-up");
        } else {
            $(this).removeClass("label-up");

        }
    });
    </script>

</head>
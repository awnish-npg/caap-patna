<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php if (isset($title)) {
                echo $title;
            } ?></title>
    <?php echo compile_theme_css(); ?>
    <link rel="stylesheet" type="text/css" id="waitme-css" href="<?= base_url('assets/plugins/waitMe/waitMe.css?v=2.4.4'); ?>">
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <!-- <link href="<?= base_url('/assets/css/transition.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('/assets/css/dropdown.min.css'); ?>" rel="stylesheet" /> -->
    <link href="<?= base_url('/assets/css/lightgallery.css'); ?>" rel="stylesheet" />

    <?php app_customers_head(); ?>
</head>

<body class="registration-header customers<?php if (is_mobile()) {
                                                echo ' mobile';
                                            } ?>
<?php if (isset($bodyclass)) {
    echo ' ' . $bodyclass;
} ?>" <?php if ($isRTL == 'true') {
            echo 'dir="rtl"';
        } ?>>
    <?php hooks()->do_action('customers_after_body_start'); ?>

    <!-- <script src="<?= base_url('/assets/js/transition.min.js'); ?>"></script>
    <script src="<?= base_url('/assets/js/dropdown.min.js'); ?>"></script> -->
    <script>
        $(document).on("blur", ".form-input-field input", function(e) {

            if ($(this).val() !== "") {
                $(this).addClass("label-up");
            } else {
                $(this).removeClass("label-up");

            }
        });
    </script>
    <?php app_customers_footer();
    ?>
    <script type="text/javascript" id="waitme-js" src="<?= base_url('assets/plugins/waitMe/waitMe.js?v=2.4.4')?>"></script>

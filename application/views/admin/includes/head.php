<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>">

<head>
    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />

    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

    <?php echo app_compile_css(); ?>
    <link href="<?= base_url('/assets/css/select2.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('/assets/css/transition.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('/assets/css/dropdown.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('/assets/css/jquery.mCustomScrollbar.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('/assets/css/lightgallery.css'); ?>" rel="stylesheet" />
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" /> -->
    <?php render_admin_js_variables(); ?>

    <script>
        var totalUnreadNotifications = <?php echo $current_user->total_unread_notifications; ?>,
            proposalsTemplates = <?php echo json_encode(get_proposal_templates()); ?>,
            contractsTemplates = <?php echo json_encode(get_contract_templates()); ?>,
            billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'],
            isRTL = '<?php echo $isRTL; ?>',
            taskid, taskTrackingStatsData, taskAttachmentDropzone, taskCommentAttachmentDropzone, newsFeedDropzone, expensePreviewDropzone, taskTrackingChart, cfh_popover_templates = {},
            _table_api;
    </script>
    <?php app_admin_head(); ?>
</head>

<body <?php echo admin_body_class(isset($bodyclass) ? $bodyclass : ''); ?><?php if ($isRTL === 'true') {
                                                                                echo 'dir="rtl"';
                                                                            }; ?>>
<div id="loader-wrapper">
    <div id="loader"></div>
 
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
 
</div>
    <?php hooks()->do_action('after_body_start'); ?>

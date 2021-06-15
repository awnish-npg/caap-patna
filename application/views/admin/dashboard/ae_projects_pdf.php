<!DOCTYPE html>
<html>

<head>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta charset="utf-8">
    <title>Projects Summary</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel=" stylesheet" href="<?php echo base_url('assets/css/report-pdf.css') ?>"">


</head>

<body>
    <table class=" header">
    <tr>
        <td class="text-left" width="75">
            <!-- <img src="<?php //echo base_url('assets/images/cpcb-pdf-logo.png') ?>" alt=""> -->
            <?php if(!empty(get_logo('company_logo'))){ ?>
                <img src="<?php echo base_url(get_logo('company_logo')); ?>" alt="" style="width: 75px;height: 55px;">
            <?php } ?>
        </td>
        <td class="text-center">
            <h1 class="text-center"><?php echo get_option('companyname'); ?></h1>
        </td>
        <td class="text-right" width="75">
        <?php if(!empty(get_area_logo())){ ?>
            <img src="<?php echo base_url(get_area_logo()); ?>" alt="" style="width: 75px;height: 55px;">
        <?php } ?>
        </td>
    </tr>

    </table>
    <table class="no-bdr">
        <tr>
            <td>
                <span class="date">Date: <?php echo date('d-m-Y H:i'); ?></span>
                <?php if ($GLOBALS['current_user']->role_slug_url == "ae-global") { ?>
                    <span class="generated">Generated by: <?php echo $GLOBALS['current_user']->firstname . ", " . $GLOBALS['current_user']->role_name; ?></span>
                <?php } else { ?>
                    <span class="generated">Generated by: <?php echo $GLOBALS['current_user']->firstname . ", " . $GLOBALS['current_user']->role_name . ', ' . $area_name; ?></span>
                <?php } ?>
                <span class="generated">In case of any queries, please contact support at <a href="mailto:<?php echo get_option('email_signature'); ?>"><?php echo get_option('email_signature'); ?></a></span>


            </td>
        </tr>
    </table>
    <h2 class="text-center">Summary of India, <?php echo $area_name ?></h2>
    <table style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th><?php echo _l('New'); ?></th>
                <th class="escalated">Delayed</th>
                <th class="wip">In Progress</th>
                <th class="closed"><?php echo _l('Closed'); ?></th>
                <th class="wip">Rejected</th>
                <th class="wip">Unassigned</th>
                <th class="wip">Frozen</th>
                <th class="total-column"><?php echo _l('Total'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php foreach ($chart_data as $val) {
                ?>
                    <td><?php echo (!empty($val)) ? $val : '0'; ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th><?php echo _l('State'); ?></th>
                <th><?php echo _l('New'); ?></th>
                <th class="escalated">Delayed</th>
                <th class="wip">In Progress</th>
                <th class="closed"><?php echo _l('Closed'); ?></th>
                <th class="wip">Rejected</th>
                <th class="wip">Unassigned</th>
                <th class="wip">Frozen</th>
                <th class="total-column"><?php echo _l('Total'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statuses as $status) { ?>
                <tr class="region">
                    <td><?php echo $status["region_name"] ?></td>
                    <td><?php echo (!empty($status['new'])) ? $status['new'] : '0'; ?></td>
                    <td><?php echo (!empty($status['escalated'])) ? $status['escalated'] : '0';  ?></td>
                    <td><?php echo (!empty($status['wip'])) ? $status['wip'] : '0'; ?></td>
                    <td><?php echo (!empty($status['close'])) ? $status['close'] : '0';  ?></td>
                    <td><?php echo (!empty($status['rejected'])) ? $status['rejected'] : '0'; ?></td>
                    <td><?php echo (!empty($status['unassigned'])) ? $status['unassigned'] : '0'; ?></td>
                    <td><?php echo (!empty($status['frozen'])) ? $status['frozen'] : '0'; ?></td>
                    <td><?php echo (!empty($status['total'])) ? $status['total'] : '0'; ?></td>
                </tr>
                <?php if (!empty($status["sub_region_status"])) foreach ($status["sub_region_status"] as $sub_status) { ?>
                    <tr class="subregion">
                        <td><?php echo $sub_status["sub_region_name"] ?></td>
                        <td><?php echo (!empty($sub_status['new'])) ? $sub_status['new'] : '0'; ?></td>
                        <td><?php echo (!empty($sub_status['escalated'])) ? $sub_status['escalated'] : '0';  ?></td>
                        <td><?php echo (!empty($sub_status['wip'])) ? $sub_status['wip'] : '0'; ?></td>
                        <td><?php echo (!empty($sub_status['close'])) ? $sub_status['close'] : '0';  ?></td>
                        <td><?php echo (!empty($sub_status['rejected'])) ? $sub_status['rejected'] : '0'; ?></td>
                        <td><?php echo (!empty($sub_status['unassigned'])) ? $sub_status['unassigned'] : '0'; ?></td>
                        <td><?php echo (!empty($sub_status['frozen'])) ? $sub_status['frozen'] : '0'; ?></td>
                        <td><?php echo (!empty($sub_status['total'])) ? $sub_status['total'] : '0'; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>

        </tbody>
    </table>
            <!-- <p class="text-center footer">In case of any queries, please contact us at <a href="mailto:info@a-pag.org">info@a-pag.org</a></p> -->
    </body>

</html>
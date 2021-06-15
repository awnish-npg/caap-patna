<?php defined('BASEPATH') or exit('No direct script access allowed');
ob_start();
$role_slug =  $GLOBALS['current_user']->role_slug_url;
$logo = '';
// $CI = &get_instance();
// if($GLOBALS['current_user']->area != 0){
//     $query = $CI->db->query('SELECT `logo` FROM ' . db_prefix() . 'area  WHERE areaid = ' . $GLOBALS['current_user']->area )->row();
//     $logo =  $query->logo ;
// }
// if(!empty($logo)){
//     $logo_url = base_url('uploads/logo/'.$logo);
// }else{
//     // $logo_url = base_url('assets/images/dpcc-logo.jpg'); 
//     $logo_url = '';     
// } 

?>
<li id="top_search" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
    <input type="search" id="search_input" class="form-control" placeholder="<?php echo _l('top_search_placeholder'); ?>">
    <div id="search_results">
    </div>
    <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history">
    </ul>
</li>
<li id="top_search_button">
    <button class="btn"><i class="fa fa-search"></i></button>
</li>
<?php
$top_search_area = ob_get_contents();
ob_end_clean();
$role_slug =  $GLOBALS['current_user']->role_slug_url;
$sep = ',';
if($role_slug == 'ae-global' || $role_slug == 'ap-sa'){
    $sep = '';
}
?>
<div id="header">
    <div class="hide-menu"><i class="fa fa-align-left"></i></div>
    <div class="product-info">
    <p class="product-name"><?php echo get_option('companyname'); ?></p>
    <div class="profile-info">
        <a href="<?php echo admin_url('staff/edit_profile') ?>">
            <span><?php echo trim($GLOBALS['current_user']->full_name); ?> (<?php echo $GLOBALS['current_user']->role_name; ?>), <?php echo $GLOBALS['current_user']->organisation; ?><?php echo $sep ?></span>
            <!-- <a href="<?php echo 'mailto:' . $GLOBALS['current_user']->email; ?>"><?php echo $GLOBALS['current_user']->email; ?></a> -->
        </a>
        <?php echo get_staff_location();/* if($GLOBALS['current_user']->area!=0){
                  if(isset($GLOBALS['current_user']->location['area'])) {echo ' <li>'.$GLOBALS['current_user']->location['area'].'</li>';
                  }
                  if(!empty($GLOBALS['current_user']->location['region_name'])){
                    echo ' <li>'.$GLOBALS['current_user']->location['region_name'].'</li>';
                  }
                  if(!empty($GLOBALS['current_user']->location['subregion_name'])){
                    echo ' <li>'.$GLOBALS['current_user']->location['subregion_name'].'</li>';
                  }
                  ?>
              <?php } else{ ?><span>&nbsp;</span>
              <?php }*/ ?>
        <!-- <ul class="staff_location" data-toggle="tooltip" data-html="true" title="<?php echo get_staff_location('plain'); ?>" data-placement="bottom"> -->
        <!-- <ul class="staff_location">
            <?php echo get_staff_location();/* if($GLOBALS['current_user']->area!=0){
                  if(isset($GLOBALS['current_user']->location['area'])) {echo ' <li>'.$GLOBALS['current_user']->location['area'].'</li>';
                  }
                  if(!empty($GLOBALS['current_user']->location['region_name'])){
                    echo ' <li>'.$GLOBALS['current_user']->location['region_name'].'</li>';
                  }
                  if(!empty($GLOBALS['current_user']->location['subregion_name'])){
                    echo ' <li>'.$GLOBALS['current_user']->location['subregion_name'].'</li>';
                  }
                  ?>
              <?php } else{ ?><span>&nbsp;</span>
              <?php }*/ ?>
        </ul> -->

    </div>
    </div>
    <div id="logo">
        <!-- <img src="<?php //echo base_url('assets/images/header-logo.jpg') 
                        ?>" alt=""> -->
        <!-- <img src="<?php //echo base_url('assets/images/cpcb-pdf-logo.png') 
                        ?>" alt=""> -->
        <?php if (!empty(get_logo('company_logo'))) { ?>
            <!-- <img src="<?php echo base_url(get_logo('company_logo')); ?>" alt="" style="width: 75px;height: 55px;"> -->
        <?php } ?>
    </div>
    <!-- <p class="product-name"><?php echo get_option('companyname'); ?></p> -->
    <nav>
        <div class="small-logo">
            <span class="text-primary">
                <?php get_company_logo(get_admin_uri() . '/') ?>
            </span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            <ul class="mobile-icon-menu">
                <?php
                // To prevent not loading the timers twice
                // if (is_mobile()) {
                if (false) {

                     ?>
                    <li class="dropdown notifications-wrapper header-notifications">
                        <?php $this->load->view('admin/includes/notifications'); ?>
                    </li>
                    <li class="header-timers">
                        <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown"><i class="fa fa-clock-o fa-fw fa-lg"></i>
                            <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0) {
                                                                                                        echo ' hide';
                                                                                                    } ?>"><?php echo count($startedTimers); ?></span>
                        </a>
                        <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                            <?php $this->load->view('admin/tasks/started_timers', array('startedTimers' => $startedTimers)); ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
                <ul class="nav navbar-nav">
                    <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
                    <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a>
                    </li>
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a>
                    </li>
                    <?php if (is_staff_member()) { ?>
                        <li class="header-newsfeed">
                            <a href="#" class="open_newsfeed mobile">
                                <?php echo _l('whats_on_your_mind'); ?>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="header-logout"><a href="javascript:void(0)" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
                </ul>
            </div>
        </div>
        <ul class="nav navbar-nav navbar-right m-0">
            <?php
            //    if(!is_mobile()){
            //     echo $top_search_area;
            //  } 
            ?>
            <?php //hooks()->do_action('after_render_top_search'); 
            ?>
            
            <li class="partner-logo">
                <?php if (!empty(get_area_logo())) { ?>
                    <img src="<?php echo base_url(get_area_logo()); ?>" alt="" style="width: 75px;height: 55px;">
                <?php } ?>
                <!-- <img src="<?php //echo $logo_url; 
                                ?>" alt="" style="width: 75px;height: 55px;"> -->

            </li>
            <?php // if(is_staff_member()){ 
            ?>
           
            <?php // } 
            ?>
          
        </ul>
    </nav>
</div>
<div id="mobile-search" class="<?php if (!is_mobile()) {
                                    echo 'hide';
                                } ?>">
    <ul>
        <?php
        if (is_mobile()) {
            echo $top_search_area;
        } ?>
    </ul>
</div>

<div class="modal fade sidebarModal" id="change_pass" tabindex="-1" role="dialog" style="width:40%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span>Change Password</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <p class="form-instruction add-title">Here you can change your password.</p>
                    </div>
                    <hr class="hr-panel-model" />
                </div>
                <div class="form-group">
                    <div class="form-input-field">
                        <input class="" type="password" required="" id="name" name="name">
                        <label for="name" title="Enter New Password" data-title="Enter New Password"></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-input-field">
                        <input class="" type="password" required="" id="name" name="name">
                        <label for="name" title="Confirm Password" data-title="Confirm Password"></label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-custom">Save</button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
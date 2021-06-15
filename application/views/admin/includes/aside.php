<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<?php defined('BASEPATH') or exit('No direct script access allowed');
$totalQuickActionsRemoved = 0;
$quickActions = $this->app->get_quick_actions_links();
foreach ($quickActions as $key => $item) {
  if (isset($item['permission'])) {
    if (!has_permission($item['permission'], '', 'create')) {
      $totalQuickActionsRemoved++;
    }
  }
}
?>
<aside id="menu" class="sidebar">
  <ul class="nav metis-menu" id="side-menu">
    <li class="text-center pB10">
    <!-- <img src="<?php echo base_url('assets/images/white-logo-placeholder.jpg'); ?>" alt="" > -->
    <?php if (!empty(get_logo('company_logo'))) { ?>
            <!-- <img src="<?php echo base_url(get_logo('company_logo')); ?>" alt=""> -->
        <?php } ?>
    </li>
    <li class="menu-item profile-link hide">
      <a href="#" class="oflowH">
        <i class="material-icons menu-icon">account_circle</i>

      </a>
      <div class="profile-info">
        <a href="<?php echo admin_url('staff/edit_profile') ?>">
          <span><?php echo $GLOBALS['current_user']->full_name; ?></span>
          <span>(<?php echo $GLOBALS['current_user']->role_name; ?>)</span>
          <!-- <a href="<?php echo 'mailto:' . $GLOBALS['current_user']->email; ?>"><?php echo $GLOBALS['current_user']->email; ?></a> -->
        </a>
        <!-- <ul class="staff_location" data-toggle="tooltip" data-html="true" title="<?php echo get_staff_location('plain'); ?>" data-placement="bottom"> -->
        <ul class="staff_location">
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
        </ul>

      </div>
    </li>


    <?php if ($totalQuickActionsRemoved != count($quickActions)) { ?>
      <!-- <li class="quick-links">
         <div class="dropdown dropdown-quick-links">
            <a href="#" class="dropdown-toggle" id="dropdownQuickLinks" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <i class="fa fa-gavel" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownQuickLinks">
               <?php
                foreach ($quickActions as $key => $item) {
                  $url = '';
                  if (isset($item['permission'])) {
                    if (!has_permission($item['permission'], '', 'create')) {
                      continue;
                    }
                  }
                  if (isset($item['custom_url'])) {
                    $url = $item['url'];
                  } else {
                    $url = admin_url('' . $item['url']);
                  }
                  $href_attributes = '';
                  if (isset($item['href_attributes'])) {
                    foreach ($item['href_attributes'] as $key => $val) {
                      $href_attributes .= $key . '=' . '"' . $val . '"';
                    }
                  }
                ?>
               <li>
                  <a href="<?php echo $url; ?>" <?php echo $href_attributes; ?>>
                  <i class="fa fa-plus-square-o"></i>
                  <?php echo $item['name']; ?>
                  </a>
               </li>
               <?php } ?>
            </ul>
         </div>
      </li> -->
    <?php } ?>
    <?php
    hooks()->do_action('before_render_aside_menu');
    ?>
    <?php foreach ($sidebar_menu as $key => $item) {
      if (isset($item['collapse']) && count($item['children']) === 0) {
        continue;
      }
    ?>
      <li class="menu-item-<?php echo $item['slug']; ?>" <?php echo _attributes_to_string(isset($item['li_attributes']) ? $item['li_attributes'] : []); ?>>
        <a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>" aria-expanded="false" <?php echo _attributes_to_string(isset($item['href_attributes']) ? $item['href_attributes'] : []); ?>>
          <i class="<?php echo $item['icon']; ?> menu-icon"></i>
          <span class="menu-text">
            <?php echo _l($item['name'], '', false); ?>
          </span>
          <?php if (count($item['children']) > 0) { ?>
            <span class="fa arrow"></span>
          <?php } ?>
        </a>
        <?php if (count($item['children']) > 0) { ?>
          <ul class="nav nav-second-level collapse" aria-expanded="false">
            <?php foreach ($item['children'] as $submenu) {
            ?>
              <li class="sub-menu-item-<?php echo $submenu['slug']; ?>" <?php echo _attributes_to_string(isset($submenu['li_attributes']) ? $submenu['li_attributes'] : []); ?>>
                <a href="<?php echo $submenu['href'];  ?>" target="<?php echo $submenu['target'];  ?>" <?php echo _attributes_to_string(isset($submenu['href_attributes']) ? $submenu['href_attributes'] : []); ?>>
                  <?php if (!empty($submenu['icon'])) { ?>
                    <i class="<?php echo $submenu['icon']; ?> menu-icon"></i>
                  <?php } ?>
                  <span class="sub-menu-text">
                  <i class="fa fa-angle-right mR10"></i> <?php echo _l($submenu['name'], '', false); ?>
                  </span>
                </a>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>
      </li>
      <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>
    <?php } ?>
    <?php if ($this->app->show_setup_menu() == true && (/*is_staff_member() ||*/is_admin())) { ?>
      <li<?php if (get_option('show_setup_menu_item_only_on_hover') == 1) {
            echo ' style="display:none;"';
          } ?> id="setup-menu-item">
        <a href="#" class="open-customizer"><i class="fa fa-cog menu-icon"></i>
          <span class="menu-text">
            <?php echo _l('setting_bar_heading'); ?>
            <?php
            if ($modulesNeedsUpgrade = $this->app_modules->number_of_modules_that_require_database_upgrade()) {
              echo '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
            }
            ?>
          </span>
        </a>
      <?php } ?>
      </li>
      <?php hooks()->do_action('after_render_aside_menu'); ?>
      <!-- <li class="<?php if ($totalQuickActionsRemoved == count($quickActions)) {
                        echo ' dashboard-user-no-qa';
                      } ?>">
                <a href="#" onclick="showChangePass()"> <i class="fa fa-key menu-icon" data-toggle="tooltip"
                        data-title="Change Password" data-placement="right"></i>
                    <span class="menu-text">Change Password</span>
                </a>
            </li> -->
      <li class="<?php if ($totalQuickActionsRemoved == count($quickActions)) {
                    echo ' dashboard-user-no-qa';
                  } ?>">
        <?php //echo _l('welcome_top',$current_user->firstname);
        ?>
        <a href="<?php echo admin_url('authentication/logout')?>"> <i class="fa fa-power-off menu-icon"></i>
          <span class="menu-text">Logout</span>
        </a>

      </li>
    </ul>
        <div class="profile-info powered-by hide">
          <span>An initiative by</span> <img src="<?php echo base_url('assets/images/powered-by.png') ?>" alt="">
        </div>
</aside>

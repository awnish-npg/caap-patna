<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="screen-options-area"></div>
    <div class="screen-options-btn">
        <?php // echo _l('dashboard_options'); ?>
    </div>
    <div class="content">
        <div class="row">

            <?php //$this->load->view('admin/includes/alerts'); ?>

            <?php //hooks()->do_action( 'before_start_render_dashboard_content' ); ?>

            <div class="clearfix"></div>
            <h3 style="text-align:center; margin:20px 0px;">State Admin Checklist <?php if($this->session->userdata('staff_role')==2)
            {
            echo "SuperAdmin";
            }if($this->session->userdata('staff_role')==6){
                $areaid=get_staff_area_id($this->session->userdata('staff_user_id'));
                //echo get_area_name($areaid);
                }?>
                <div class="padding-5 bold widget-head">Please complete the tasks listed below to configure the system for your State.</div>
            </h3>
            
            <div class="col-md-8 mtop45 " data-container="top-12" style="margin:0px auto; float:none;">
            <div class="col-md-12">
            <div class="panel_s checklist">
               <div class="panel-body padding-10">
                  
                    <!-- Start task  -->
                    <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           1                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Customize Action Items for your State         
                            <span>Action Items have been mapped based on the list curated by the National Admin. These can be customized for your state by editing/disabling the existing Action Items and creating new Aciton Items in the “Manage Action Items” section. 
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                    <!-- Start task  -->
                    <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           2                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                                Define Cities/ Corporations for your State                    
                            <span>Cities/ Corporations can be defined at the city-level or at the municipal corporation level, in case there are multiple municipal corporations in your city. </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                     <!-- Start task  -->
                     <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           3                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Define Municipal Zones for the Cities/ Corporations defined in 1                  
                            <span>Municipal Zones are the logical subdivisions of cities/ corporations defined by you.</span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                     <!-- Start task  -->
                     <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           4                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Onboard the Reviewers in your State                   
                            <span>Reviewers should be leaders for their respective Urban Local Body. Ex. Deputy Commissioners in MCD Zones. </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                     <!-- Start task  -->
                     <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           5                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Onboard Project Leader users to your State                
                            <span>These are the users who will be assigned tickets for the selected categories within a Municipal Zone.  <br>
                            Functional heads in the ULBs should be defined as Project Leaders. Ex. Deputy Director – Horticulture and Sanitation Superintendent in MCDs
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                    <!-- Start task  -->
                    <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           6                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            On board Project Support users            
                            <span>These are the users who report to a Project Leader and assist the Project Leader in resolving tickets assigned to the Project Leader. Ex. Executive Engineers in the MCDs.
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                    <!-- Start task  -->
                    <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           7                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Onboard the State Observer users in your state               
                            <span>State Observer should be allocated to officials of the State Pollution Control Boards, Officials higher than Deputy Commissioners in MCDs or any other officials requiring state-wide visibility of actions. 
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                     <!-- Start task  -->
                     <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           8                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Map Unassigned Action Items to Project Leaders              
                            <span>It should be ensured that all action items are assigned to Project Leaders in the system. In case projects are raised against unassigned actions items, map them to the relevant Project Leaders. This would automatically assign the projects to the newly mapped Project Leaders. 
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="progress no-margin progress-bar-mini">
                           
                        </div>
                    </div>
                    <!-- end task -->

                    <!-- Start task  -->
                    <div class="goal padding-5">
                      <div class="col-md-1">
                         <h4 class="pull-left bold mright30 no-mtop text-left">
                         <small>Task</small><br>
                           9                           
                        </h4>
                        </div>
                        <div class="col-md-11 panel-header">
                            <h1 class="pull-left font-medium no-mtop">
                            Keep tab of all transfers taking place in your State            
                            <span>Transfers of existing users would need to be updated on a timely basis to ensure relevance of the dashboard in your State. Contact Tech Support to understand the procedure to transfer users within the system. 
                            </span>
                            </h1>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        
                    </div>
                    <!-- end task -->

                </div>
            </div>
         </div>
            </div>

            <?php //hooks()->do_action('after_dashboard_top_container'); ?>

            <div class="col-md-6" data-container="middle-left-6">
                <?php //render_dashboard_widgets('middle-left-6'); ?>
            </div>
            <div class="col-md-6" data-container="middle-right-6">
                <?php //render_dashboard_widgets('middle-right-6'); ?>
            </div>

            <?php //hooks()->do_action('after_dashboard_half_container'); ?>

            <div class="col-md-8" data-container="left-8">
                <?php //render_dashboard_widgets('left-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php //render_dashboard_widgets('right-4'); ?>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-4" data-container="bottom-left-4">
                <?php //render_dashboard_widgets('bottom-left-4'); ?>
            </div>
             <div class="col-md-4" data-container="bottom-middle-4">
                <?php //render_dashboard_widgets('bottom-middle-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-right-4">
                <?php //render_dashboard_widgets('bottom-right-4'); ?>
            </div>

            <?php //hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<?php //$this->load->view('admin/utilities/calendar_template'); ?>
<?php //$this->load->view('admin/dashboard/dashboard_js'); ?>
</body>
</html>

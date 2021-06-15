<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// print_r(form_error());
?>
<?php echo form_open_multipart('clients/open_ticket',array('id'=>'open-new-ticket-form')); ?>
<div class="container p-0">
    <div class="row">
        <div class="col-md-12">

            <?php hooks()->do_action('before_client_open_ticket_form_start'); ?>

            <div class="panel_s p-0">

                <div class="panel-body">
                    <div class="open-ticket-subject">
                        <div class="panel-header">
                            <h1><?php echo _l('clients_ticket_open_subject'); ?><span>Here you can raise a new project in your City/ Corporation. </span></h1>
                            <hr class="hr-panel-heading" />
                            <input type="hidden" name="loc" value="" id="locs">
                            <span class="locat"></span>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" id="selectedregion">
                        <!-- <div class="col-md-12"> -->

                        <!-- <?php
                  // if(total_rows(db_prefix().'projects',array('clientid'=>get_client_user_id())) > 0 && has_contact_permission('projects')){ ?>
                  <div class="form-group open-ticket-project-group">
                     <label for="project_id"><?php
                     // echo _l('project'); ?></label>
                     <select data-none-selected-text="<?php
                     // echo _l('dropdown_non_selected_tex'); ?>" name="project_id" id="project_id" class="form-control selectpicker">
                        <option value=""></option>
                        <?php
                        // foreach($projects as $project){ ?>
                        <option value="<?php
                        // echo $project['id']; ?>" <?php
                        // echo set_select('project_id',$project['id']); ?><?php
                        // if($this->input->get('project_id') == $project['id']){echo ' selected';} ?>><?php
                        // echo $project['name']; ?></option>
                        <?php
                     //  } ?>
                     </select> -->
                        <!-- </div> -->
                        <?php
               // } ?>
               <?php if(is_callcenter($this->session->userdata('client_user_id'))){?>
                <div class="row m-0">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php echo render_input('rname','Full Name*',set_value('rname'),'text',['id'=>'rname']);?>
                                    <p id="fnameeror" style="color:red;"></p>
                                    <?php echo form_error('rname'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php echo render_input('rphonenumber','Phone number*',set_value('rphonenumber'),'tel',['id'=>'rphone']);?>
                                    <?php echo form_error('rphonenumber'); ?>
                                    <p id="numbererror" style="color:red;"></p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input-field">
                                        <?php echo render_input('remail','Email*',set_value('remail'),'email',['id'=>'remail']);?>
                                    <?php echo form_error('remail'); ?>
                                    <p id="emailerror" style="color:red;"></p>
                                </div>
                            </div>
                </div>
               <?php }?>
                        <div class="row m-0">
                            <div class="col-md-6">
                            <div class="form-group">
                            <!-- <div class="form-select-field singleSelect">
                                        <select class="selectpicker" name="area"  id="area_id" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="">Select State</option>
                                        <?php
                                            foreach(get_all_areas() as $area){ ?>
                                                        <option value="<?php
                                            echo $area['areaid']; ?>" <?php echo set_select('area',  $area['areaid']); ?>><?php
                                             echo $area['name']?></option>
                                                        <?php
                                        } ?>
                                        </select>
                                        <label class="control-label" for="lastname">State*</label>   
                                    <?php 
                                    // echo form_error('area'); ?>
                                </div> -->

                                <div class="form-select-field">
                                        <select name="area" class="form-control selectpicker " id="area_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" title="Select State*">
                                        <option value="">Select State</option>
                                                        <?php
                                                            foreach(get_all_areas() as $area){ ?>
                                                                        <option value="<?php
                                                            echo $area['areaid']; ?>" <?php echo set_select('area',  $area['areaid']); ?>><?php
                                                            echo $area['name']?></option>
                                                                        <?php
                                                        } ?>
                                        </select>
                                        <label class="control-label">State*</label>
                                        <?php echo form_error('area'); ?>

                                 </div>
                            </div>    
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                            <div class="form-select-field singleSelect">
                                    <!-- <select class="selectpicker" name="region"  id="region_id" data-width="100%"
                                     data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    </select> -->
                                    <?php
                                    $selected =[];
                                    echo render_select_surveyor('region',[],array(),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'City/ Corporation','id'=>'region_id'),array(),'no-mbot');
								    ?>
                                    <label class="control-label" for="lastname">City/ Corporation*</label>
                                    <?php echo form_error('region'); ?>
                                </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                            <div class="form-select-field singleSelect">
                                    <!-- <select class="selectpicker" name="subregion"  id="sub_region_id" data-width="100%"
                                     data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    </select> -->
                                    <?php
                                    $selected =[];
                                    echo render_select_surveyor('subregion',[],array(),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'Municipal Zone','id'=>'sub_region_id'),array(),'no-mbot');
								    ?>
                                    <label class="control-label" for="lastname">Municipal Zone*</label>
                                    <?php echo form_error('subregion'); ?>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-select-field singleSelect category_dropdown">
                                        <!-- <select class="selectpicker" name="categories"  id="categories" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        </select> -->
                                        <?php
                                         $selected =[];
                                            echo render_select_surveyor('categories',[],array(),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>'Action Item','id'=>'categories'),array(),'no-mbot');
								    ?>
                                        <label class="control-label" for="lastname">Action Item*</label>                                    
                                    <?php echo form_error('categories'); ?>
                                </div>
                            </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group form-input-field">
                                        <?php echo render_input('landmark','Landmark*',set_value('landmark'),'text',['id'=>'landmark']);?>
                                    <?php echo form_error('landmark'); ?>
                                    <p id="landmarkeror" style="color:red;"></p>
                                </div>
                            </div>
                        </div>


                    <!-- <?php
                  // if(get_option('services') == 1 && count($services) > 0){ ?>
                  <div class="form-group open-ticket-service-group">
                     <label for="service"><?php
                     // echo _l('clients_ticket_open_service'); ?></label>
                     <select data-none-selected-text="<?php
                     // echo _l('dropdown_non_selected_tex'); ?>" name="service" id="service" class="form-control selectpicker">
                        <option value=""></option>
                        <?php
                        // foreach($services as $service){ ?>
                        <option value="<?php
                        // echo $service['serviceid']; ?>" <?php
                        // echo set_select('service',$service['serviceid'],(count($services) == 1 ? true : false)); ?>><?php
                        //  echo $service['name']; ?></option>
                        <?php
                     //  } ?>
                     </select>
                  </div>
                  <?php
               // } ?>

                  <div class="custom-fields">
                     <?php echo render_custom_fields('tickets','',array('show_on_client_portal'=>1)); ?>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </div> -->
                    <div class="col-md-12 mT10">
                        <div class="">
                                <div class="form-group open-ticket-message-group">
                                    <!-- <label for=""><?php echo _l('clients_ticket_open_body'); ?>*</label> -->
                                    <textarea type="text" id="message" name="message" class="form-textarea"
                                        rows="15"><?php echo set_value('message'); ?></textarea>
                                <label for="" data-title="<?php echo _l('clients_ticket_open_body'); ?>*" title="<?php echo _l('clients_ticket_open_body'); ?>*"></label>

                                    <p style="color:red" id="alertspan"></p>
                                </div>
                            <!-- <div class="attachments_area open-ticket-attachments-area">
                                <div class="row attachments">
                                    <div class="attachment">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="attachment"
                                                    class="control-label"><?php echo _l('clients_ticket_attachments'); ?><span style="font-size:12px;">&nbsp;(Select Images and PDF only | Max size: 5 MB)</span></label>
                                                <div class="input-group">
                                                    <input type="file"
                                                        extension="<?php 
                                                        // echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>"
                                                        class="form-control" name="file[]"
                                                        accept="<?php 
                                                        // echo get_ticket_form_accepted_mimes();?>image/*,application/pdf">
                                                    <span class="input-group-btn">
                                                        <button
                                                            class="btn btn-info add_more_attachments_test p8-half"
                                                            data-max="<?php echo get_option('maximum_allowed_ticket_attachments'); ?>"
                                                            type="button"><i class="fa fa-plus"></i></button>
                                                    </span>
                                                </div>
                                                <p class="imagedetail_error" style="color:red" id="attach0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_error('file');?>
                            </div> -->
                            <label for="attachment"
                                                    class="control-label"><?php echo _l('clients_ticket_attachments'); ?><span style="font-size:12px;">&nbsp;(Select Images and PDF only | Max size: 5 MB)</span></label>
                            <br/>
                            <div class="form-group file-group row">
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file1">
                                            <img class="evidence-pre" id="img0" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>
                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file1" class="form-control evidence-uploader" >
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                    <p class="imagedetail_error" style="color:red" id="attach0"></p>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file2">
                                            <img class="evidence-pre" id="img1" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>

                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file2" class="form-control evidence-uploader">
                                        <!-- <span class="img-label">Upload Evidence</span> -->
                                    </div>
                                    <p class="imagedetail_error" style="color:red" id="attach1"></p>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file3">
                                            <img class="evidence-pre" id="img2" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>

                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file3" class="form-control evidence-uploader" >
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                    <p class="imagedetail_error" style="color:red" id="attach2"></p>
                                </div>
                                <div class="col-lg-3">
                                    <div class="image-upload">
                                        <label for="file4">
                                            <img class="evidence-pre" id="img3" src="<?php echo base_url('assets/images/evidence.png') ?>" alt="">
                                            <a href="javascript:void(0)" class="del"><i class="fa fa-trash"></i></a>

                                        </label>

                                        <input type="file" accept="application/pdf, image/gif, image/jpeg, image/pjpeg, image/png, image/x-png" name="file[]" id="file4" class="form-control evidence-uploader" >
                                        <!-- <span class="img-label">Upload Evidence</span> -->

                                    </div>
                                    <p class="imagedetail_error" style="color:red" id="attach3"></p>

                                </div>
                            </div>
                            <p style="color:red; display: none; margin-left: 15px" id="upload_error">Please upload files with type png, jpg, jpeg or PDF only.</p>

                        </div>
                        <div class="mtop20 mB20">
                            <button type="submit" class="btn btn-custom btn-submit-ticket"
                                id="submitbutton"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
                <!-- confirmation model -->
<div class="modal fade sidebarModal reject-ticket-modal" id="informationmodal">
<div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header pT18">
                <h4 class="modal-title pL0" id="exampleModalLabel">Confirm Information</h4>
            </div>
            <hr class="hr-panel-model" />
            <div class="modal-body">

                <!-- <h4 id="confirmlandmark"></h4> -->
                <!-- <h4 id="confirmmessage"></h4> -->
                <div class="row confirm-popup-list basic-detail-list">
                <?php if(is_callcenter($this->session->userdata('client_user_id'))){?>
                    <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Reference User Name</label>
                            <span class="surveyor-list  d-block" id="rnamecheck"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Reference Phone Number</label>
                            <span class="surveyor-list  d-block" id="rphonecheck"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Reference Email</label>
                            <span class="surveyor-list  d-block" id="remailcheck"></span>

                        </div>
                    </div>
                        </div>
                    </div>
                    
                <?php }?>
                <div class="col-lg-12">
                    <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">State</label>
                            <span class="surveyor-list  d-block" id="confirmarea"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">City/ Corporation</label>
                            <span class="surveyor-list  d-block" id="confirmregion"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Municipal Zone</label>
                            <span class="surveyor-list  d-block" id="confirmsubregion"></span>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Action Item</label>
                            <span class="surveyor-list  d-block" id="confirmcategory"></span>
                        </div>
                    </div>
                    </div>
                </div>
                    
<div class="col-lg-12">
    <div class="row">
    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Landmark of Issue</label>
                            <span class="surveyor-list  d-block" id="confirmlandmark"></span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label class="control-label" for="lastname">Description</label>
                            <span class="surveyor-list  d-block" id="confirmmessage"></span>
                        </div>
                    </div>
    </div>
</div>
                    
                    <div class="col-md-12 ">
            <hr class="hr-panel-model" />

                        <div class="row evidence-data">

                        </div>

            </div>
            <div class="clearfix"></div>
            <div class="modal-footer mT30 pL15">
                <input type="button" id="confirmsubmit" class="btn btn-custom btn-submit-ticket" value="Raise Project">

                <input type="button" class="btn btn-cancel" data-dismiss="modal" value="Cancel" id="cancel">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button> -->
            </div>
            <hr>
            <div class="clearfix"></div>

            <div class="notes mB20 col-md-12 mL15 mT10" id="notes">
                    <h5>
                        <p class="form-field-notes mL0">Note</p>
                    </h5>
                    <p class="form-field-notes mL0" id="locationused"></p>
            </div>
        </div>
    </div>
</div>
</div>
                <!-- confirmation model end -->
<script>
$(document).on('keyup', 'input[name="landmark"], textarea[name="message"],input[name="rname"]', function(e) {
    name = $(this).val();
    const nameCapitalized = name.charAt(0).toUpperCase() + name.slice(1);
    $(this).val(nameCapitalized);
})
    //                 const getArea=()=>{
    //                     $.ajax({
    //                         type: "POST",
    //                         url: site_url + 'clients/getarea',
    //                         success: function (response) {
    //                             response = JSON.parse(response);
    //                             if(response.success==true){
    //                             let options = "<option value=''>Select State*</option>";
    //                              $.each(response.areas, function (indexInArray, value) { 
    //                                 options +=
    //                                 `<option value='${value.areaid}'>${value.name}</option>`;
    //                              });  
    //                              $("#area_id").append(options);
    //                             $('#area_id').selectpicker('refresh'); 
    //                             }
    //                         }
    //                     });                    
    //             }
    // getArea();
                // alert($('#region_id').val())
                // alert(<?php
                //echo $this->session->flashdata('region');?>)

                
    // Location 
function showPosition() {
			if(navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var positionInfo =  position.coords.latitude + "," + position.coords.longitude ;
                    $("#locs").val(positionInfo);
                    // alert(positionInfo);
				});
			} else {
				alert("Sorry, your browser does not support HTML5 geolocation.");
			}
		}
        showPosition();
// End Location
    var elements = document.getElementsByClassName("evidence-uploader");
    var imgFileType = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png'];
    var allowedFileType = [...imgFileType, "application/pdf"];
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('change', function(e) {
            var file = e.target.files[0];
            // if (!allowedFileType.includes(file.type)) {
            //     document.getElementById("upload_error").style.display = "block";
            //     return;
            // }
            document.getElementById("upload_error").style.display = "none";
            var imgElem = e.target.parentNode.getElementsByClassName("evidence-pre")[0];
            // alert(imgElem);
            // var imgLabel = e.target.parentNode.getElementsByClassName("img-label")[0]
            console.log(imgFileType.includes(file.type))
            if (imgFileType.includes(file.type)) {
                imgElem.src = URL.createObjectURL(file);
                imgElem.className += " cancel"; 
                imgElem.onload = function() {
                    URL.revokeObjectURL(imgElem.src) // free memory
                }
            } else {
                imgElem.className += " cancel"; 
                imgElem.src = "<?php echo base_url() . 'assets/images/pdf-icon.png'; ?>";
            }
            // imgLabel.innerHTML = '';
        });
    }
$('.del').click(function (e) { 
    var img=$(this).closest('.image-upload').find("label").find('img');
    var file=$(this).closest('.image-upload').find("input");
    img.attr('src',"<?php echo base_url('assets/images/evidence.png') ?>");
    img.removeClass('cancel');
    file.val('')  
});
        $.ajax({
                    type: "post",
                    url: site_url + 'clients/get_surveyor_detail' ,
                    success: function (response) {
                        const data=JSON.parse(response);
                        if(data.area !=0 && data.region !=0 && data.subregion != 0){
                            $('#area_id').find('option[value="'+data.area+'"]').attr("selected",true);
                            $('.selectpicker').selectpicker('refresh')
                            getRegionData(data.area,data.region);
                            getSubregion2(data.region,data.subregion);
                            getCategories(data.area);
                        }
                    }
                });
       
                var attach=0;
                $(".add_more_attachments_test").click(function(e) {
                    e.preventDefault();
                    if(attach<=2){
                        attach++;
                    // $('.attachments_area').append('<div class="row attachments"><div class="attachment"><div class="col-md-6 col-md-offset-3"><div class="form-group"><label for="attachment" class="control-label"><?php echo _l('clients_ticket_attachments '); ?></label><div class="input-group"><input type="file"  extension="<?php echo str_replace(['. ', ''], '', get_option('ticket_attachments_file_extensions ')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="file[]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>"><span class="input-group-btn"><button class="btn btn-success remove_attachments_test p8-half" onclick="remove(this)" data-max="<?php echo get_option('maximum_allowed_ticket_attachments '); ?>" type="button"><i class="fa fa-minus"></i></button> </span></div></div></div></div>');
                    $('.attachments_area').append('<div class="row attachments"><div class="attachment"><div class="col-md-6"><div class="form-group"><label for="attachment"class="control-label"></label><div class="input-group"><input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" class="form-control" name="file[]"  accept="image/*,application/pdf"><span class="input-group-btn"><button class="btn btn-info remove_attachments_test p8-half" onclick="remove(this)" data-max="<?php echo get_option('maximum_allowed_ticket_attachments'); ?>" type="button"><i class="fa fa-minus"></i></button></span></div><?php echo form_error('file');?><p class="imagedetail_error" style="color:red" id="attach'+attach+'"></p></div></div></div></div>');
                    }
                    else{
                        alert_float('warning','You can add only four pictures');                    }
                });
                const remove = (index) => {
                    attach--;
                    $(index).closest('div.attachments').remove();
                }
                $("#submitbutton").click(function (e) {
                    <?php if(is_callcenter($this->session->userdata('client_user_id'))){ ?>
                    const cc1 = validatename();
                    const cc2=validatephone();
                    const cc3=validateemail();
                    // alert(cc1);
                    if ( cc1>0 || cc2>0 || cc3>0) {
                        return;
                    }
                    <?php }?>
                    const ta=validatetextarea();
                    const res4=validatelandmark();
                    const res6=validatefileavailability();
                    const res5=validatefile();
                    if(ta>0 || res4>0 || res5>0 || res6>0){
                        return;
                    }
                    const formElem = document.querySelector('form');
                    const a=new FormData(formElem);
                    const area=$("#area_id").find("option:selected").text();
                    const region=$("#region_id").find("option:selected").text();
                    const subregion=$("#sub_region_id").find("option:selected").text();
                    const landmark=$("#landmark").val();
                    const category=$("#categories").find("option:selected").text();
                    const message=$("#message").val();
                    <?php if(is_callcenter($this->session->userdata('client_user_id'))){ ?>
                        const name=$("#rname").val();
                        const email=$("#rphone").val();
                        const phone=$("#remail").val();
                        const res1 = validatename();
                        const res2=validatephone();
                        const res3=validateemail();
                        if(res1>0 || res2>0 || res3>0 || res4>0){
                            return;
                        }  
                    <?php }?>

                <?php if(is_callcenter($this->session->userdata('client_user_id'))){ ?>
                    if(name !="" && email != "" && phone !="" && res1 ==0 && res2 ==0 && res3 ==0){
                    $(".panel-body").waitMe({
                        effect: "bounce",
                        text: "",
                        color: "#000",
                        maxSize: "",
                        waitTime: -1,
                        textPos: "vertical",
                        fontSize: "",
                        source: "",
                        onClose: function() {},
                    });
                    }
                <?php }else{?>
                    $(".panel-body").waitMe({
                        effect: "bounce",
                        text: "",
                        color: "#000",
                        maxSize: "",
                        waitTime: -1,
                        textPos: "vertical",
                        fontSize: "",
                        source: "",
                        onClose: function() {},
                    });
                 <?php
                }?>
                    var i=0;
                    var count=0;
                    $('input[type^="file"]').each(function() { 
                        var validimageExtensions = ["jpg","jpeg","png"];
                        var validfileExtensions = ["pdf"];
                        var file = $(this).val().split('.').pop();
                        console.log(file);
                        file=file.toLowerCase();
                        // if($(this).val()==""){
                        //     count++;
                        // }else if (validimageExtensions.indexOf(file) == -1 && validfileExtensions.indexOf(file)) {
                        // count++;
                        // }
                    if(validimageExtensions.indexOf(file) != -1){  
                    $(".evidence-data").append('<div class="col-md-3 surveyor-attach text-center"><figure><img class="" src="#" id="upload'+i+'"/></figure></div>');
                    readURL(this,i);
                    }
                    if(validfileExtensions.indexOf(file) != -1){
                        $(".evidence-data").append('<div class="col-md-3 surveyor-attach text-center"><figure><img class="" src="<?php echo base_url('assets/images')?>/pdf-icon.png" /></figure></div>');
                    }
                    i++;
                    });
                    <?php if(is_callcenter($this->session->userdata('client_user_id'))){ ?>
                    if(area !="" && region !="" && subregion != "" && landmark != "" && category !="" && message !="" && count==0 && name !="" && email!="" && phone !=""){
                        e.preventDefault();      
                      $.ajax({
                        type: "post",
                        url: site_url + 'clients/check_geotagg_image',
                        data:a,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                              const res=JSON.parse(response);
                              if(res.success==true){
                                $(".imagedetail_error").html("");   
                                $("#informationmodal").modal('show');
                                $("#notes").attr('hidden', true);
                                e.preventDefault();      
                                $("#confirmarea").html(area);
                                $("#confirmregion").html(region);
                                $("#confirmsubregion").html(subregion);
                                $("#confirmcategory").html(category)
                                $("#confirmlandmark").html(landmark);
                                $("#confirmmessage").html(message);
                                $('#rnamecheck').html(name);
                                $('#rphonecheck').html(email);
                                $('#remailcheck').html(phone);
                              }
                              else{
                             const loc=$("#locs").val();
                             if(loc !==''){
                                var coordinate=loc.split(',');
                                $(".locat").append('<input type="hidden" value="'+coordinate[0]+'" name="latitude">');
                                $(".locat").append('<input type="hidden" value="'+coordinate[1]+'" name="longitude">');
                                $(".imagedetail_error").html("");   
                                $("#informationmodal").modal('show');
                                e.preventDefault();      
                                $("#confirmarea").html(area);
                                $("#confirmregion").html(region);
                                $("#confirmsubregion").html(subregion);
                                $("#confirmcategory").html(category)
                                $("#confirmlandmark").html(landmark);
                                $("#confirmmessage").html(message);
                                $('#rnamecheck').html(name);
                                $('#rphonecheck').html(email);
                                $('#remailcheck').html(phone);
                                $('#notes').removeAttr('hidden');
                                $('#locationused').html("As, the uploaded image(s) are not geotagged, your current location will be used.");
                                
                             }
                                else{
                               $(".evidence-data").empty(); 
                               alert_float('danger','Enable location services of your browser <a href="https://support.google.com/chrome/answer/142065?hl=en" target="_blank">click here for more info.</a>');
                               var faulty=res.faulty_image;
                                    var indices=faulty.split(',');
                                    for(let i=0;i<=3;i++){
                                    $("#attach"+i).html("");
                                    }
                                    for(let i=0;i<indices.length;i++){
                                        if(indices[i] != ""){
                                            $('#attach'+indices[i]).html(res.message);
                                        }
                                    }
                                    $(".panel-body").waitMe("hide");
                              }
                            }
                        }
                        });

                    } 
                    <?php }else {?>
                    if(area !="" && region !="" && subregion != "" && landmark != "" && category !="" && message !="" && count==0){
                        e.preventDefault();      
                        $.ajax({
                            type: "post",
                            url: site_url + 'clients/check_geotagg_image',
                            data:a,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                const res=JSON.parse(response);
                                if(res.success==true){
                                    $(".imagedetail_error").html("");   
                                    $("#informationmodal").modal('show');
                                    $("#confirmarea").html(area);
                                    $("#confirmregion").html(region);
                                    $("#confirmsubregion").html(subregion);
                                    $("#confirmcategory").html(category)
                                    $("#confirmlandmark").html(landmark);
                                    $("#confirmmessage").html(message);
                                    $("#notes").attr('hidden', true);
                                }
                                else{
                                    // $(".evidence-data").empty();
                                    // var faulty=res.faulty_image;
                                    // var indices=faulty.split(',');
                                    // for(let i=0;i<=3;i++){
                                    // $("#attach"+i).html("");
                                    // }
                                    // for(let i=0;i<indices.length;i++){
                                    //     if(indices[i] != ""){
                                    //         $('#attach'+indices[i]).html(res.message);
                                    //     }
                                    // }
                                    // $(".panel-body").waitMe("hide");
                                    // var ans=confirm("Allow Location Serivces");
                                    const loc=$("#locs").val();
                                    if(loc !== ''){
                                        var coordinate=loc.split(',');
                                        $(".locat").append('<input type="hidden" value="'+coordinate[0]+'" name="latitude">');
                                        $(".locat").append('<input type="hidden" value="'+coordinate[1]+'" name="longitude">');
                                        $(".imagedetail_error").html("");   
                                        $("#informationmodal").modal('show');
                                        $("#confirmarea").html(area);
                                        $("#confirmregion").html(region);
                                        $("#confirmsubregion").html(subregion);
                                        $("#confirmcategory").html(category)
                                        $("#confirmlandmark").html(landmark);
                                        $("#confirmmessage").html(message);
                                        $('#notes').removeAttr('hidden');
                                        $('#locationused').html("If uploaded image(s) are not geotagged, your current location will be used.");
                                    }
                                    else{
                                        $(".evidence-data").empty(); 
                                        alert_float('danger','Enable location services of your browser <a href="https://support.google.com/chrome/answer/142065?hl=en" target="_blank">click here for more info.</a>');
                                        $(".locat").html(); 
                                        var faulty=res.faulty_image;
                                        var indices=faulty.split(',');
                                        for(let i=0;i<=3;i++){
                                        $("#attach"+i).html("");
                                        }
                                        for(let i=0;i<indices.length;i++){
                                            if(indices[i] != ""){
                                                $('#attach'+indices[i]).html(res.message);
                                            }
                                        }
                                        $(".panel-body").waitMe("hide");
  
                                    }
                                }
                            }
                        });

                    }
                    <?php }?>
  
                });
                function checksize(input,i){
                    var count=0
                    var size=Math.round((input.files.item(0).size/1024));
                    if(size > 5120){
                        $("#attach"+i).html("Evidence size must be of Max 5 MB")
                        count=count+1;
                    }
                    return count;
                }
                 function readURL(input,i) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        
                        reader.onload = function(e) {
                        $('#upload'+i+'').attr('src', e.target.result);
                        }
                        
                        reader.readAsDataURL(input.files[0]); // convert to base64 string
                    }
                }
                $('#informationmodal').on('hidden.bs.modal', function () {
                    $(".evidence-data").empty();
                    $('.locat').empty();
                })
                $("#cancel").click(function (e) { 
                    $(".panel-body").waitMe("hide");
                });
               

                $("#confirmsubmit").click(function (e) {
                    $(".panel-body").waitMe("hide");
                    $("#informationmodal").waitMe({
                        effect: "bounce",
                        text: "",
                        color: "#000",
                        maxSize: "",
                        waitTime: -1,
                        textPos: "vertical",
                        fontSize: "",
                        source: "",
                        onClose: function() {},
                    }); 
                    $('#open-new-ticket-form').submit();
                });
                $('#area_id').on('changed.bs.select', function(e, clickedIndex, newValue, oldValue) {
                    var selected = $(e.currentTarget).val();
                    //  alert(selected);
                    $("#region_id option").remove();
                    $('#region_id').selectpicker('refresh');
                    $("#categories option").remove();
                    $('#categories').selectpicker('refresh');
                    $("#sub_region_id option").remove();
                    $('#sub_region_id').selectpicker('refresh');
                    getRegionData(selected);
                    getCategories(selected);
                });
                $('#region_id').on('changed.bs.select', function(e, clickedIndex, newValue, oldValue) {
                    var selected = $(e.currentTarget).val();
                    $("#selectedregion").val(selected);
                    $("#sub_region_id option").remove();
                    $('#sub_region_id').selectpicker('refresh');
                    getSubregion2(selected);
                });
                let REGION_LIST = {};

                // getArea(false);
                // function getArea(excludeStaffArea, invoker = null, id = null) {
                // 		let url = site_url + 'clients/get_admin_area';
                // 		let data;
                // 		if (excludeStaffArea)
                // 			data = {
                // 				"exclude_staff_area": 1
                // 			}
                // 		else
                // 			data = {
                // 				"exclude_staff_area": 0
                // 			}
                // 		$.ajax({
                // 			processing: 'true',
                // 			serverSide: 'true',
                // 			type: "POST",
                // 			url: url,
                // 			data: data,
                // 			success: function(res) {
                // 				let options = "<option value=''>Select Area</option>"
                // 				res = JSON.parse(res);
                // 				res.area_list.map(area => {
                // 					options += `<option value=${area.areaid}>${area.name}</option>`
                // 				});
                // 				$('select[name="area"]').html(options);
                // 				// if (invoker && id) {
                // 				// 	var area = $(invoker).data('area');
                // 				// 	$('input[name="name"]').val($(invoker).data('name'))
                // 				// 	$('input[name="organisation"]').val($(invoker).data('organisation'))
                // 				// 	$('input[name="email"]').val($(invoker).data('email'))
                // 				// 	$('input[name="phone"]').val($(invoker).data('phone'))
                // 				// 	$('input[name="id"]').val(id)
                // 				// 	$('select[name="departments"]').val($(invoker).data('department'))
                // 				// 	$('#add_edit_staff').modal('show');
                // 				// 	$('.add-title').hide();
                // 				// 	$(".edit-title").show();
                // 				// }
                // 				$('select[name="area"]')
                // 					.dropdown({
                // 						transition: "slide down",
                // 						placeholder: "Select Area",
                // 						onChange: (value) => {
                // 							getRegionData(value);
                //                      getCategories(value);
                // 						}
                // 					});
                // 				// if (!excludeStaffArea) {
                // 				// 	$('.ui.dropdown').addClass("disabled");
                // 				// }else {
                // 				// 	$('.ui.dropdown').removeClass("disabled");
                // 				// }
                // 			}
                // 		})
                // 	}

                /*Function to get and populate Region Data*/
                const getRegionData = (areaid, val = '') => {
                    let area = areaid;
                    let data = {
                        'area_id': area,
                        'group_by': false
                    }
                    $.post(site_url + 'clients/get_region', data).done((res) => {
                        res = JSON.parse(res);
                        // $.each(res.region_list, function () {
                        // var options = "<option " + "value='" + this.region_id + "'>" + this.region_name + "";
                        // $("#region_id").append(options);
                        // });
                        // $('#region_id').selectpicker('refresh');
                        if (res.success == true) {
                            REGION_LIST = {
                                ...res.region_list
                            };
                            // let options = "<option value=''>Select Region*</option>";   
                            let options = ""; 
                            $('#region_id').selectpicker({title: "Select City/ Corporation*"}).selectpicker('render');
                            $('#sub_region_id').selectpicker({title: "Select Municipal Zone*"}).selectpicker('render');

                            for (let region in REGION_LIST) {

                                if (val != 0 && val == REGION_LIST[region][0].region_id) {
                                    options +=
                                        `<option value='${REGION_LIST[region][0].region_id}' selected>${REGION_LIST[region][0].region_name}</option>`
                                } else {
                                    options +=
                                        `<option value='${REGION_LIST[region][0].region_id}'>${REGION_LIST[region][0].region_name}</option>`
                                }
                            }
                            $("#region_id").append(options);
                            // $("#region_id").html(options);
                            $('#region_id').selectpicker('refresh');

                            // $('#region_id')
                            // 	.dropdown({
                            // 		transition: "slide down",
                            // 		placeholder: "Select Region*",
                            //       onChange: (value) => {
                            // 			getSubregion(value);

                            // 		}
                            // 	});
                        }
                        if(res.success == false){
                            $('#region_id').selectpicker({title: res.message}).selectpicker('render');
                            $('#sub_region_id').selectpicker({title: "No Municipal Zone found"}).selectpicker('render');

                        }
                    }).fail(function(data) {
                        var error = JSON.parse(data.responseText);
                        console.log("Region option ajax error:", error);
                    });
                }


                function getSubregion(region_id, val = '') {
                    console.log(REGION_LIST);
                    let options = "<option value=''>Select Municipal Zone*</option>";
                    for (let region in REGION_LIST) {
                        if (REGION_LIST[region][0].region_id == region_id) {
                            REGION_LIST[region].map(sub_region => {
                                // if (sub_region.sub_region_id != null)
                                if (val != 0 && val == sub_region.sub_region_id) {
                                    options +=
                                        `<option value='${sub_region.sub_region_id}' selected>${sub_region.sub_region_name}</option>`
                                } else {
                                    options +=
                                        `<option value='${sub_region.sub_region_id}'>${sub_region.sub_region_name}</option>`
                                }
                            })
                        }
                    }
                    $("#sub_region_id").append(options);
                    $('#sub_region_id').selectpicker('refresh');
                    // $('#sub_region_id').html(options);
                    // $('#sub_region_id')
                    // 	.dropdown({
                    // 		placeholder: "Select Sub-Region*",
                    // 		transition: "slide down",
                    // 	});

                }
                const getSubregion2 = (regionid, val = "") => {
                    $.post(site_url + 'clients/getsubregion', {
                        "regionid": regionid
                    }).done((res) => {
                        res = JSON.parse(res);
                        // console.log(res);
                        if(res.success==true){
                        // let options = "<option value=''>Select Sub-Region*</option>";
                        let options = "";
                        $('#sub_region_id').selectpicker({title: "Select Municipal Zone*"}).selectpicker('render');

                        // alert(val);
                        $.each(res.sub_region_list, function(indexInArray, value) {
                            if (val != '' && val == value.id) {
                                options +=
                                    `<option value='${value.id}' selected>${value.region_name}</option>`;
                            } else {
                                options +=
                                    `<option value='${value.id}'>${value.region_name}</option>`;
                            }
                        });
                        $("#sub_region_id").append(options);
                        $('#sub_region_id').selectpicker('refresh');
                        }
                        if(res.success == false){
                            $('#sub_region_id').selectpicker({title: res.message}).selectpicker('render');
                        }
                    }).fail(function(data) {
                        var error = JSON.parse(data.responseText);
                        console.log("Region option ajax error:", error);
                    });

                }
                // getSubregion2(1);
                const getCategories = (areaid, val = '') => {
                    let area = areaid;
                    let data = {
                        'area_id': area,
                    }

                    $.post(site_url + 'clients/get_area_issues', data).done((res) => {
                        res = JSON.parse(res);
                        if (res.success == true) {
                            // let options = "<option value=''>Select Category*</option>";
                            let options = "";
                            $('#categories').selectpicker({title: "Select Action Item*"}).selectpicker('render');

                            res.issues.map(issue => {
                                if (val != '' && val == issue.id) {
                                    options +=
                                        `<option value='${issue.id}' selected>${issue.issue_name}</option>`
                                } else {
                                    options +=
                                        `<option value='${issue.id}'>${issue.issue_name}</option>`
                                }
                            });
                            $("#categories").html(options);

                            // $("#categories").append(options);
                            $('#categories').selectpicker('refresh');
                            // $("#categories").html(options);
                            // $('#categories')
                            // 	.dropdown({
                            // 		placeholder: "Select Categories*",
                            // 		transition: "slide down",
                            // 	});
                            // if (selectedCats != null) {
                            // 	console.log("typeof ", selectedCats)
                            // 	$('#categories')
                            // 		.dropdown('set exactly', selectedCats);
                            // }
                        }
                        if(res.success == false){
                            $('#categories').selectpicker({title: res.message}).selectpicker('render');

                        }
                    }).fail((data) => {
                        let error = JSON.parse(data.responseText);
                        console.log("Categories option ajax error:", error);
                    })
                }

                if ($('#area_id').val() != "") {
                    // $("#region_id option").remove();
                    // $("#sub_region_id option").remove();
                    // $("#categories option").remove();
                    <?php
                    if ($this->session->flashdata('region') and $this->session-> flashdata('subregion') and $this->session->flashdata('categories'))
                     {
                        ?>
                        getRegionData($('#area_id').val(), <?php echo $this->session->flashdata('region');?>);
                        getSubregion2( <?php echo $this->session->flashdata('region'); ?>,<?php echo $this->session->flashdata('subregion'); ?> );
                        getCategories($('#area_id').val(), <?php echo $this->session->flashdata('categories'); ?>);
                        <?php
                    } else if ($this->session->flashdata('region') and $this->session->flashdata('categories'))
                    {
                        ?>
                        // alert("hii trio")
                        getRegionData($('#area_id').val(), <?php echo $this->session->flashdata('region'); ?>);
                        getSubregion2( <?php echo $this->session->flashdata('region'); ?>);
                        getCategories($('#area_id').val(), <?php echo $this->session->flashdata('categories');?>);

                        <?php
                    } else if ($this->session->flashdata('region') and $this->session->flashdata('subregion')) {
                        ?>
                        // alert('hii only region and subregion')
                        getRegionData($('#area_id').val(), <?php echo $this->session->flashdata('region'); ?>);
                        getCategories($('#area_id').val());
                        getSubregion2(<?php echo $this->session->flashdata('region');?>, <?php echo $this->session->flashdata('subregion'); ?>);
                        <?php
                    }

                    elseif($this->session->flashdata('region')) {
                        ?>
                        //   alert('hii only region');
                        getRegionData($('#area_id').val(), <?php echo $this->session->flashdata('region');?>);
                        getCategories($('#area_id').val());
                        getSubregion2( <?php echo $this->session->flashdata('region'); ?>);
                     <?php
                    }
                    else if ($this->session->flashdata('categories')) {
                        ?>
                        // alert('hii only cate')
                        getRegionData($('#area_id').val());
                        getCategories($('#area_id').val(), <?php echo $this->session-> flashdata('categories'); ?>);
                        <?php
                    }
                    else {
                        ?>

                        // alert("hii no")
                        getRegionData($('#area_id').val());
                        getCategories($('#area_id').val());

                        <?php
                    }
                    ?>



                    // getSubregion2(<?php echo $this->session->flashdata('region');?>,<?php echo $this->session->flashdata('subregion');?>)

                }

                $("#open-new-ticket-form").submit(function(e) {

                    const res = validatetextarea();
                    const res4=validatelandmark();
                    const res6=validatefileavailability();
                    const res5=validatefile();
                <?php if(is_callcenter($this->session->userdata('client_user_id'))){ ?>
                    const res1 = validatename();
                    const res2=validatephone();
                    const res3=validateemail();
                    if (res > 0 || res1>0 || res2>0 || res3>0 || res4>0 ||res5>0 ||res6>0) {
                        e.preventDefault();
                    }
                <?php }else{?>
                    if (res > 0 || res4>0 || res5>0 || res6>0) {
                        e.preventDefault();
                    }
                <?php }?>
               
                });

                const validatetextarea = () => {
                    var count = 0;
                    if ($("#message").val().trim().length == 0) {
                        $('#alertspan').html("The Description field cannot be empty.");
                        count = count + 1
                    } else if ($("#message").val().trim().length > 500) {

                        $('#alertspan').html("The Description field cannot exceed 500 characters in length.");
                        count = count + 1
                    }
                    //  else if (/^[a-zA-Z0-9.,:;'\s]*$/.test($("#message").val().trim()) == false) {
                    //     $('#alertspan').html(
                    //         "The Description field may only contain alpha-numeric characters,spaces,comma,Apostrophe(') and full stop.");
                    //     count = count + 1;
                    // }
                     else {
                        $("#submitbutton").removeAttr('disabled');
                        $('#alertspan').html("");
                    }
                    return count;
                }
                function validatelandmark(){
                    var count=0;
                    var name =$("#landmark").val();
                    if($("#landmark").val() != ""){
                    // if(/^[a-zA-Z0-9\s]*$/.test(name)==false){
                    //     $("#landmarkeror").html("Please enter alpha-numeric characters only");
                    //     count=count+1;
                    // }else
                     if(name.length>255){
                        $("#landmarkeror").html("Please enter less than 255 char");
                        count=count+1;
                    }
                    else{
                        $("#landmarkeror").html("");
                    }
                    return count;
                    }else{
                        return 1;
                    }
                }
                function validatefileavailability(){
                    var i=0;
                    var check=0;
                    var temp=0;
                    $('input[type^="file"]').each(function() {
                        if($(this).val()==""){
                           temp++;
                        }
                    });
                    if(temp==4){
                        alert_float('danger', "Please upload atleast one Image or PDF as evidence");
                        check++;
                    }
                    $('input[type^="file"]').each(function() {
                        var validfileExtensions = ["jpg","jpeg","png",'pdf'];
                        var file = $(this).val().split('.').pop();
                        file=file.toLowerCase();
                        // if($(this).val()==""){
                        //    $("#attach"+i).html("Attachment is required");
                        //     check++;
                        // }
                        // else 
                        if (validfileExtensions.indexOf(file) == -1 && $(this).val() !="") {
                            $("#attach"+i).html("Please select Images(png/jpg) or PDF only");
                            check++;
                        }
                        i++
                     });
                     return check;
                }
                function validatefile(){
                    var count=0;
                    var ind=0;
                    $('input[type^="file"]').each(function() {
                        var a;
                    if($(this).val()!=""){
                        a=checksize(this,ind);
                        count=count+a;
                        ind++;
                        }
                     });
                     return count;
                }
                function validatename(){
                    var count=0;
                    var name =$("#rname").val();
                    if($("#rname").val() != ""){
                    if(/^[a-zA-Z ]+ [a-zA-Z ]+$/.test(name)==false){
                        $("#fnameeror").html("Please enter full name (must contain space between first & last name).");
                        count=count+1;
                    }else if(name.length>50){
                        $("#fnameeror").html("Please enter name less than 50 char");
                        count=count+1;
                    }
                    else{
                        $("#fnameeror").html("");
                    }
                    return count;
                    }
                    else {
                        if($("#rname").val() == ""){
                            count=count+1;
                        }
                        return count;
                    }
                    
                }

                    function validatephone(){
                    var count=0
                    if($("#rphone").val() != ""){
                    if((/^[0-9]*$/.test($("#rphone").val())==false)){
                        $("#numbererror").html("Please enter only digits");
                        count=count+1;
                    }else if(($("#rphone").val()).length>12 || ($("#rphone").val()).length<8){
                        $("#numbererror").html("Please enter  8-12 digits");
                        count=count+1;
                    }
                    else{
                        $("#numbererror").html("");
                    }
                    return count;
                    }
                    else {
                        if($("#rphone").val() == ""){
                            count=count+1;
                        }
                        return count;
                    }
                }

                    function validateemail(){
                        var count=0;
                        if($("#remail").val() != ""){
                        if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#remail").val())==false){
                        $("#emailerror").html("Please enter valid email");
                        count=count+1;
                        }else{
                            $("#emailerror").html("");
                        }
                        return count;
                    }
                    else {
                        if($("#remail").val() == ""){
                            count=count+1;
                        }
                        return count;
                    }
                }
                // });

                // }
                // const getCategories = (areaid, selectedCats = null) => {
                //     let area = areaid;
                //     let data = {
                //         'area_id': area,
                //     }
                //     $.post(site_url + 'clients/get_area_issues', data).done((res) => {
                //         res = JSON.parse(res);
                //         if (res.success == true) {
                //             let options = "";
                //             res.issues.map(issue => {
                //                 options +=
                //                     `<option value='${issue.id}'>${issue.issue_name}</option>`
                //             });
                //             $("#categories").html(options);
                //             $('#categories')
                //                 .dropdown({
                //                     placeholder: "Select Categories*",
                //                     transition: "slide down",
                //                 });
                //             if (selectedCats != null) {
                //                 console.log("typeof ", selectedCats)
                //                 $('#categories')
                //                     .dropdown('set exactly', selectedCats);
                //             }
                //         }
                //     }).fail((data) => {
                //         let error = JSON.parse(data.responseText);
                //         console.log("Categories option ajax error:", error);
                //     })
                // }

$(document).on("focus", ".form-textarea", function(e) {
   
   $(this).addClass("label-up");

})
$(document).on("blur", ".form-textarea", function(e) {

if ($(this).val() !== "") {
   $(this).addClass("label-up");
}
else{
   $(this).removeClass("label-up");

}
});
                $("form#open-new-ticket-form :input").each(function(){
                    if ($(this).val()) {
                        $(this).addClass("label-up");
                    } else {
                        $(this).addClass("labellll-up");
                    }
                })



                </script>

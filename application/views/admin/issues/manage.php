<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
init_head(); ?>
<?php
// print_r($this->session->userdata());
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-header">
                            <?php
                            $roleid = $this->session->userdata('staff_role');
                            if (has_permission('categories', '', 'create')) {
                            ?>
                                <a href="#" onclick="new_issue(); return false;" class="btn btn-custom add-area-admin pull-right display-block">

                                    <?php echo _l('new_category'); ?>
                                </a>

                            <?php
                            }
                            ?>
                            <h1>Manage Action Items<span>Here you can view, add, edit and deactivate Action Items and Milestone(s).</span></h1>
                            <hr class="hr-panel-heading" />
                        </div>

                        <!-- <div class="_buttons"> -->
                        <!-- <a href="#" onclick="new_issue(); return false;" class="btn btn-custom pull-right display-block">
                            <?php
                            // echo _l('new_category'); 
                            ?>
                        </a> -->
                        <!-- </div> -->


                        <div class="clearfix"></div>
                        <div class="table-responsive">
                        <?php
                        if (has_permission('categories', '', 'edit')) {
                            render_datatable(array(
                                // _l('id'),
                                _l('category_name'),
                                _l('milestone_name'),
                                _l('duration'),
                                _l('reminder_one'),
                                _l('reminder_two'),
                                _l('status'),
                                _l('options'),
                            ), 'issues');
                        } else {
                            render_datatable(array(
                                //    _l('id'),
                                _l('category_name'),
                                _l('milestone_name'),
                                _l('duration'),
                                _l('reminder_one'),
                                _l('reminder_two'),
                                _l('import'),
                            ), 'issues');
                        }
                        ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sidebarModal" id="issue" tabindex="-1" role="dialog" style="width: 70%;">
    <div class="modal-dialog modal-lg">
        <?php echo form_open(admin_url('issues/newissue'), array('id' => 'issue_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_category');  ?>

                    </span>
                    <span class="add-title"><?php echo _l('new_category'); ?></span>
                </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <p class="form-instruction add-title">Fill in the following fields to add an Action Item </p>
                        <p class="form-instruction edit-title">Here you can edit details for existing action items</p>
                        <div id="additionalhiddenfields"></div>
                        <hr class="hr-panel-model">
                        <?php
                        // echo render_input('issue_name','Category Name*',set_value('milestone_name'));
                        ?>
                        <div class="form-input-field"><input class="labellll-up" type="text" required="" id="issue_name" name="issue_name" value="<?php set_value('milestone_name'); ?>">
                            <label for="issue_name" title="Action Item*" data-title="Action Item*"></label>
                            <span>
                                <p style="color:red;" id='issuename_span'></p>
                            </span>
                        </div>

                        <!-- </div> -->
                        <div class="col-md-12">
                            <div class="row">
                                <input type="hidden" name="catareas" id="catareasid">
                                <!-- wrapper for filled milestone fields -->
                                <div class="input_fields_wrap1"></div>
                                <!-- end of filled wrapper -->

                                <!-- disabled milestone wrapper -->
                                <div class="disabled_wrapper"></div>
                                <!-- end of disabled milestone wrapper -->

                                <!-- <label for="" style="margin-bottom:20px;">Add Milestone <span ><i class="fa fa-plus-circle add_field_button fa-2x "  style="color:#135cd6;" ></i></span></label> -->
                                <!-- <div class="input_fields_wrap"></div> -->
                                <!-- New Milestone fields  -->

                                <div class="input_fields_wrap_add_field"></div>
                                <div class="add-milestone-btn btn_add_test">
                                    <label for="" class="mR0">Add Milestone <span><i class="fa fa-plus-square-o btn-color add_field_button fa-2x" style=""></i></span></label>
                                </div>
                                <!-- end of New milestone fields -->

                                <!-- disabled milestone wrapper -->
                                <!-- <div class="disabled_wrapper"></div> -->

                                <!-- end of disabled milestone wrapper -->

                            </div>
                        </div>
                    </div>

                </div>



                <div class="modal-footer">
                    <button type="submit" id="submit_issue" class="btn btn-custom"><?php echo _l('submit'); ?></button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal"><?php echo _l('cancel'); ?></button>

                </div>
                <div class="notes mB20">
                    <h5>
                        <p class="form-field-notes mL0">Note</p>
                    </h5>
                    <p class="form-field-notes mL0"> Duration for the second milestone = Sum of durations of current milestone and all previous milestones - Sum of durations of all previous milestones</p>
                    <p>Example: If filling potholes has 3 milestones out of which 1st and 2nd are taking place together-:</p>
                    <ul>
                        <li>Raising Tender – takes 10 days</li>
                        <li>Clearing section of road – takes 15 days</li>
                        <li>End of Project</li>

                    </ul>
                    <p>Then, the duration will be filled as follows (assuming the 2nd milestone began when the first one did):</p>
                    <ul>
                        <li>Raising Tender – 10 days</li>
                        <li>Clearing section of road – 5 days</li>
                        <li>End of Project – 15 days (by formula)</li>
                    </ul>
                    <br>
                </div>
                <!-- /.modal-content -->
                <?php echo form_close(); ?>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php init_tail(); ?>
        <script>
            var x = 0;
            var totalduration = 0;
            $(document).ready(function() {
                $(".dataTables_length").hide();
                $(".dataTables_paginate").hide();
                var max_fields = 10;
                var wrapper = $(".input_fields_wrap_add_field"); //Fields wrapper
                var add_button = $(".add_field_button"); //Add button ID
                // $(add_button).click(function(e){ //on add input button click
                $(document).on("click", ".add_field_button", function(e) {
                    e.preventDefault();
                    if (x < max_fields) { //max input box allowed
                        x++; //text box increment
                        // alert(x);
                        if (x > 0) {
                            $('#filledmilestone0').attr('disabled', 'disabled');
                            $('#filleddurationvalue0').attr('disabled', 'disabled');
                            $('#filledr10').attr('disabled', 'disabled');
                            $('#filledr10').val(0);
                            $('#filledr20').attr('disabled', 'disabled');
                            $('#filledr20').val(0);
                            $('#filledr20').closest('div').attr('hidden', 'hidden');
                            $('#filledr10').closest('div').attr('hidden', 'hidden');

                            var durations = $('input[name^=durations]').map(function(idx, duration) {
                                return $(duration).val();
                            }).get();
                            changedurationonedit();
                            var value = findtotaldurationsum(durations);
                        }
                        $('#defaultmilestonename').attr('disabled', 'disabled');
                        $('#defaultmilestonename').val("End of Project");
                        $('#defaultmilestoneduration').attr('disabled', 'disabled');
                        // $('#defaultmilestoneduration').val(0);
                        changedefaultduration(this);
                        $('#defaultmilestoner1').val(0);
                        $('#defaultmilestoner1').attr('disabled', 'disabled');
                        $('#defaultmilestoner1').parent('div').attr('hidden', true);
                        $('#defaultmilestoner2').val(0);
                        $('#defaultmilestoner2').attr('disabled', 'disabled');
                        $('#defaultmilestoner2').parent('div').attr('hidden', true);
                        triggerInputClass();

                        //ajay sir code
                        // $(wrapper).append('<div class="milestone-row field'+x+'"'+'><input type="hidden" name="milesid[]" /><div class="form-input-field"><input name="milestones[]" type="text" ><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" type="number" required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input required name="reminder1[]" type="number" id="reminder1[]" value=""> <label for="" title="Reminder1(Days)" data-title="Reminder1(Days)"></label> <span> <p id="reminder1_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="reminder2[]" type="number" id="reminder2[]" value=""><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span'+(x-1)+'" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-circle remove_field fa-2x "  style="color:#135cd6;margin-top:6px" ></i></span></div></div>');
                        // $(wrapper).append('<div class="milestone-row field'+x+'"'+'><input type="hidden" name="milesid[]" /><div class="form-input-field"><input name="milestones[]" id="milestonename'+(x-1)+'" type="text" ><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" id="milestoneduration'+(x-1)+'" type="number" onblur=addreminder('+(x-1)+') required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input required name="reminder1[]" type="number" id="milestoner1'+(x-1)+'" > <label for="" title="Reminder1(Days)" data-title="Reminder1(Days)"></label> <span> <p id="reminder1_span'+(x-1)+'" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="reminder2[]" type="number" id="milestoner2'+(x-1)+'" value=""><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span'+(x-1)+'" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-circle remove_field fa-2x "  style="color:#135cd6;margin-top:6px" ></i></span></div></div>');
                        $(wrapper).append('<div class="milestone-row non-disabled field' + x + '"' + '><input type="hidden" name="milesid[]" /><div class="form-input-field full-width"><input name="milestones[]" id="milestonename' + (x - 1) + '" type="text" ><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span' + (x - 1) + '" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]"  onchange="changedefaultduration(event);changedurationonedit();" id="milestoneduration' + (x - 1) + '" type="number" onblur=addreminder(' + (x - 1) + ') required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span' + (x - 1) + '" name="durations_span" style="color:red"></p></span></div><div class="form-input-field duration-field r1"><input  name="reminder1[]" type="number" id="milestoner1' + (x - 1) + '" value=""><label for="" title="Reminder 1(Days)" data-title="Reminder 1(Days)"></label> <span> <p id="reminder1_span' + (x - 1) + '" style="color:red"></p></span></div><div class="form-input-field duration-field r2"><input  name="reminder2[]" type="number" id="milestoner2' + (x - 1) + '" value=""><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span' + (x - 1) + '" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-square-o btn-color remove_field fa-2x " onclick=removeappended("field' + x + '") style="margin-top:6px" ></i><i class="fa fa-plus-square-o btn-color add_field_after_button fa-2x " onclick=postappend("field' + (x) + '")  style="margin-top:6px;margin-left:3px;" ></i></span></div></div>');


                        // ajay sir code end
                    }

                });

            });
            <?php
            if (has_permission('categories', '', 'edit')) {
            ?>
                //  $(function(){
                var PAGINATION_INFO = 'Showing _START_ to _TOTAL_ action items';
                var columnDefs = [{
                    "width": "30%"
                }, {
                    "width": "29%"
                }, {
                    "width": "9%",
                    "className": "dt_center_align"
                }, {
                    "width": "11%",
                    "className": "dt_center_align"
                }, {
                    "width": "11%",
                    "className": "dt_center_align"
                }, {
                    "width": "5%",
                    "className": "dt_center_align"
                }, {
                    "width": "5%",
                    "className": "dt_center_align"
                }];
                initDataTable('.table-issues', window.location.href, [2, 3, 4, 5, 6], [1, 2, 3, 4, 5, 6], undefined, [0, 'asc'], -1, columnDefs);
            <?php } else {
            ?>
                initDataTable('.table-issues', window.location.href, [1, 2, 3, 4, 5], [1, 2, 3, 4, 5], undefined, [0, 'asc'], -1);

            <?php
            } ?>

            $('#issue').on('hidden.bs.modal', function(event) {
                $('#additionalhiddenfields').html('');
                var wrapper = $(".input_fields_wrap_filled");
                wrapper.remove();
                var wrapper1 = $(".input_fields_wrap_add_field");
                wrapper1.empty();
                var wrapper1 = $(".disabled_wrapper");
                wrapper1.empty();
                x = 0;
                totalduration = 0;
                $('#issue input[name="issue_name"]').val('');
                $('#issue input[name^="reminder1"]').val('');
                $('#issue input[name^="reminder2"]').val('');
                $('#issue input[name^="milestones"]').val('');
                $('#issue input[name^="durations"]').val('');
                $('.add-title').removeClass('hide');
                $('.edit-title').removeClass('hide');
                $("#issuename_span").empty();
                $("#reminder1_span").empty();
                $("#reminder2_span").empty();
                $("#milestone_span").empty();
                $("#durations_span").empty();
            })

            //  });

            const validateCategory = (a, spanname, fieldname, errorname) => {
                var count = 0;
                var milestone = [];
                if ($('input[name=id]').val()) {
                    id = $('input[name=id]').val();
                } else {
                    id = "";
                }
                $('input[name^=milestones]').each(function() {
                    if (($(this).val()).trim() == "") {
                        $(this).closest('.form-input-field').find("span").find('p').html("The " + errorname + " field is required");
                        count++;
                    } else if (/^[a-zA-Z0-9\s]*$/.test($(this).val().trim()) == false) {
                        $(this).closest('.form-input-field').find("span").find('p').html(errorname + " must be alphanumeric with space");
                        count++;
                    } else if (($(this).val()).trim().length > 50) {
                        $(this).closest('.form-input-field').find("span").find('p').html(errorname + " length must be less than 50 character");
                        count++;
                    } else if (milestone.indexOf((($(this).val()).trim()).toLowerCase()) > -1) {
                        $(this).closest('.form-input-field').find("span").find('p').html(errorname + " names have to be unique");
                        count++;
                    } else if ((($(this).val()).trim()).toLowerCase() == "end of project" && id == "") {
                        $(this).closest('.form-input-field').find("span").find('p').html(errorname + " names have to be unique");
                        count++;
                    } else {
                        $(this).closest('.form-input-field').find("span").find('p').html("");
                        milestone.push(($(this).val()).trim().toLowerCase());
                    }
                });
                return count;
            }



            const validatereminders = (a, reminder, spanname, fieldname, errorname) => {
                var count = 0;
                $('input[name^=' + fieldname + ']').each(function() {
                    var duration = $(this).closest('.milestone-row').find("div.duration-field").find('input').val();
                    // if(duration==""){
                    //     $(this).closest('.duration-field').find("span").find('p').html("");
                    //     count++;
                    // }
                    // if(parseInt(duration)<0){
                    //     $(this).closest('.duration-field').find("span").find('p').html("");
                    //     count++;
                    // }
                    // else
                    if ($(this).val() == "") {
                        $(this).closest('.duration-field').find("span").find('p').html("The " + errorname + " field is required");
                        count++;
                    } else if (parseInt($(this).val()) < 0) {
                        $(this).closest('.duration-field').find("span").find('p').html(errorname + " must be greater than 0");
                        count++;
                    } else if (($(this).val()).length > 4) {
                        $(this).closest('.duration-field').find("span").find('p').html(errorname + " field cannot exceed 4 characters in length and less than reminder");
                        count++;
                    } else {
                        var duration = $(this).closest('.milestone-row').find("div.duration-field").find('input').val();
                        if (fieldname == 'reminder1') {
                            var r2 = $(this).closest('.milestone-row').find('div.r2').find('input').val();
                            // if(parseInt(duration)<=10 && parseInt($(this).val())>0){
                            // $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be 0");
                            // count++;
                            // }
                            // else
                            // {
                            // if(parseInt(duration)>=10 && parseInt($(this).val())==0){
                            //     $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be greater than 0");
                            //     count++;
                            // }
                            // else
                            // if(parseInt($(this).val())> Math.floor((0.5*(parseInt(duration))))){
                            if (parseInt($(this).val()) >= parseInt(duration) && parseInt(duration) > 0) {

                                //   $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be less than or equal "+Math.floor((0.5*(parseInt(duration)))));
                                $(this).closest('.duration-field').find("span").find('p').html(errorname + " must be less than duration");

                                count++;
                            } else {
                                $(this).closest('.duration-field').find("span").find('p').html("");
                            }
                            // }
                        }
                        if (fieldname == 'reminder2') {
                            var r1 = $(this).closest('.milestone-row').find('div.r1').find('input').val();
                            // alert(r1);
                            // if(parseInt(duration)<=10 && parseInt($(this).val())>0){
                            //     if(parseInt($(this).val())<parseInt(r1)){
                            //     $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be 0");
                            //     count++;
                            //     }else{
                            //     $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be 0");
                            //     count++;
                            //     }
                            // }
                            // else{
                            // alert(parseInt(r1));

                            if (parseInt($(this).val()) <= parseInt(r1) && parseInt(r1) > 0 && parseInt(r1) < parseInt(duration)) {
                                // if(parseInt(r1)>=parseInt(duration) || parseInt(r1)<0){
                                //     $(this).closest('.duration-field').find("span").find('p').html("");
                                //     count++;
                                // }
                                // else{
                                $(this).closest('.duration-field').find("span").find('p').html(errorname + " must be greater than Reminder 1");
                                count++;
                                // }
                            }
                            // else if(parseInt($(this).val())>(0.8*(parseInt(duration)))){
                            else if (parseInt($(this).val()) >= parseInt(duration) && parseInt(duration) > 0) {

                                // $(this).closest('.duration-field').find("span").find('p').html(fieldname +" must be less than or equal"+Math.floor((0.8*(parseInt(duration)))));
                                $(this).closest('.duration-field').find("span").find('p').html(errorname + " must be less than duration");

                                count++;
                            } else {
                                $(this).closest('.duration-field').find("span").find('p').html("");

                            }
                        }

                        // }
                    }
                });
                return count;
            }

            const validateduration = (duration, spanname, fieldname, errorname) => {
                var count = 0;
                $('input[name^=durations]').each(function() {
                    if ($(this).val() == "") {
                        $(this).closest('.duration-field').find("span").find('p').html("The " + errorname + " field is required");
                        // alert($(this).closest('.duration-field').find("span").find('p').html(fieldname+" field is required"));
                        count++;
                    } else if ($(this).val() == 0) {
                        $(this).closest('.duration-field').find("span").find('p').html(errorname + " value must be grater than 0");
                        // alert($(this).closest('.duration-field').find("span").find('p').html(fieldname+" field is required"));
                        count++;
                    } else if (($(this).val()).length > 4) {
                        $(this).closest('.duration-field').find("span").find('p').html(errorname + " field should be less than 4");
                        count++;
                    } else if (parseInt($(this).val()) < 0) {
                        $(this).closest('.duration-field').find("span").find('p').html(errorname + " field should be greater than 0");
                        count++
                    } else {
                        $(this).closest('.duration-field').find("span").find('p').html("");
                    }
                });
                return count;

            }

            function changedefaultduration(event) {
                var durations = $('input[name^=durations]').map(function(idx, duration) {
                    return $(duration).val();
                }).get();
                var value = findtotaldurationsum(durations);
                $("#defaultmilestoneduration").val(value);
            }

            function findtotaldurationsum(a) {
                var count = 0;
                for (let i = 0; i < a.length; i++) {
                    if (a[i] == '') {
                        count = count + 0;
                    } else if (parseInt(a[i]) < 0) {
                        count = count + 0;
                    } else {
                        count = count + parseInt(a[i]);
                    }
                }
                // console.log(count);
                return count;
            }

            function addtomanage(id) {
                var staffid = <?php echo $this->session->userdata('staff_user_id'); ?>;
                $.ajax({
                    type: "post",
                    url: admin_url + "issues/manage_issue",
                    data: {
                        id: id,
                        staff: staffid
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.success == true) {
                            alert_float('success', response.message);
                            $('.table-issues').DataTable().ajax.reload();
                        }
                        if (response.success == false) {
                            alert_float('danger', response.message);
                            $('#issue').modal('hide');
                        }
                    }
                });

            }

            function addreminder(a) {
                var data = $('#milestoneduration' + a).val();
                var durationvalue = parseInt(data);
                if (durationvalue <= 10) {
                    $('#milestoner1' + a).val(0);
                    $('#milestoner2' + a).val(0)
                } else {
                    $('#milestoner1' + a).val(Math.floor((0.5 * durationvalue)));
                    $('#milestoner2' + a).val(Math.floor((0.8 * durationvalue)));
                }
                triggerInputClass();

            }

            function addreminderforshorttermproject() {
                if (!$('#defaultmilestoner1').is(":disabled")) {
                    var data = $('#defaultmilestoneduration').val();
                    var durationvalue = parseInt(data);
                    if (durationvalue <= 10) {
                        $('#defaultmilestoner1').val(0);
                        $('#defaultmilestoner2').val(0);
                    } else {
                        $('#defaultmilestoner1').val(Math.floor((0.5 * durationvalue)));
                        $('#defaultmilestoner2').val(Math.floor((0.8 * durationvalue)));
                    }
                    triggerInputClass();
                }
            }

            function addfilledfieldreminder(a) {
                var data = $('#filleddurationvalue' + a).val();
                var durationvalue = parseInt(data);
                if (durationvalue <= 10) {
                    $('#filledr1' + a).val(0);
                    $('#filledr2' + a).val(0);
                } else {
                    $('#filledr1' + a).val(Math.floor((0.5 * durationvalue)));
                    $('#filledr2' + a).val(Math.floor((0.8 * durationvalue)));
                }
                triggerInputClass();

            }
            const validateActionItemname=(name)=>{
                var count=0;
                console.log(name);
                if((name.trim()).length == 0){
                    $("#issuename_span").html("Action Item name  is required");
                    count ++;
                }else if(name.length>300){
                    $("#issuename_span").html("Action Item name length must be less than 300 char");
                    count++;
                }else if(/^[a-zA-Z ]+$/.test(name.trim()) == false){
                    $("#issuename_span").html("Action Item name must contain only alphabets with space");
                    count++;
                }
                else{
                    $("#issuename_span").html("");
                }
                return count;
            }
            $("#submit_issue").click(function(e) {
                e.preventDefault();
                var form = $('#issue_form');
                const name = $('#issue_name').val();
                var isnamevalid=validateActionItemname(name);
                if(isnamevalid >0){
                   return; 
                }
                var defaultmile = $("input[name=defaultmilestonename]").val();
                var defaultduration = $('input[name=defaultmilestoneduration]').val();
                var defaultreminder1 = $("input[name=defaultmilestoner1]").val();
                var defaultreminder2 = $("input[name=defaultmilestoner2]").val();
                // alert(defaultreminder1);
                var mile = $('input[name^=milestones]').map(function(idx, milestone) {
                    return $(milestone).val();
                }).get();
                var reminder_one = $('input[name^=reminder1]').map(function(idx, reminder_one) {
                    return $(reminder_one).val();
                }).get();
                var reminder_two = $('input[name^=reminder2]').map(function(idx, reminder_two) {
                    return $(reminder_two).val();
                }).get();
                var durations = $('input[name^=durations]').map(function(idx, duration) {
                    return $(duration).val();
                }).get();
                var id, rem2count = 0,
                    rem1count = 0,
                    durationcount = 0,
                    milecount = 0,
                    reminder1validity = 0,
                    reminder2validity = 0;
                if ($('input[name=id]').val()) {
                    id = $('input[name=id]').val();
                } else {
                    id = ""
                }

                // if(id=="" ){
                durationcount = validateduration(durations, 'durations_span', 'duration', 'Duration');
                milecount = validateCategory(mile, 'milestone_span', 'milestone', "Milestone");
                reminder1validity = validatereminders(durations, reminder_one, "reminder1_span", "reminder1", "Reminder 1");
                reminder2validity = validatereminders(durations, reminder_two, "reminder2_span", "reminder2", "Reminder 2");
                $("#issuename_span").empty();
                $("#defaultmilestone_span").empty();
                $("#defaultduration_span").empty();
                $("#defaultreminderone_span").empty();
                $("#defaultremindertwo_span").empty();

                if (id == "") {
                    var data = {
                        "id": id,
                        "issue_name": name,
                        "defaultmile": defaultmile,
                        "defaultduration": defaultduration,
                        "defaultr1": defaultreminder1,
                        "defaultr2": defaultreminder2,
                        x: x
                    }
                } else {
                    var disabled = form.find(':input:disabled').removeAttr('disabled');
                    var data = form.serialize();
                    disabled.attr('disabled', 'disabled');
                }
                $.ajax({
                    type: "post",
                    url: admin_url + "issues/validate",
                    //  data: {id:id,issue_name:name,reminder_one:reminder_one.toString(),reminder_two:reminder_two.toString(),milestone:mile.toString(),duration:durations.toString()},
                    //  data: {id:id,issue_name:name,reminder_one:reminder_one[0],reminder_two:reminder_two[0],milestone:mile[0],duration:durations[0],defaultmile:defaultmile,defaultduration:defaultduration,defaultr1:defaultreminder1,defaultr2:defaultreminder2},
                    // data: {id:id,issue_name:name,defaultmile:defaultmile,defaultduration:defaultduration,defaultr1:defaultreminder1,defaultr2:defaultreminder2},
                    data: data,
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.success == true) {
                            if ($('input[name=id]').val()) {
                                var id1 = $('input[name=id]').val();
                            } else {
                                var id1 = ""
                            }
                            if (id1 == "" && milecount == 0 && rem1count == 0 && reminder1validity == 0 && reminder2validity == 0 && durationcount == 0) {
                                if (response.data != "" && response.id != "") {
                                    var answer = confirm("A similar action item already exists in - " + response.data + ". Please contact respective State Admin(s) immediately. \n Are you sure you wish to create it for all States?")
                                    $('#catareasid').val(response.id)
                                }
                                if (response.data == "" && response.id == "") {
                                    var answer = true;
                                    $('#catareasid').val("")
                                }
                            } else {
                                var answer = true
                            }

                            if (milecount == 0 && rem1count == 0 && reminder1validity == 0 && reminder2validity == 0 && durationcount == 0 && answer == true) {
                                //  alert("submit");
                                var disabled = form.find(':input:disabled').removeAttr('disabled');
                                var data = form.serialize();
                                disabled.attr('disabled', 'disabled');
                                $.ajax({
                                    type: "post",
                                    url: admin_url + "issues/newissue",
                                    data: data,
                                    success: function(response) {
                                        response = JSON.parse(response);
                                        if (response.success == true) {

                                            alert_float('success', response.message);
                                            $('.table-issues').DataTable().ajax.reload();
                                            $('#issue').modal('hide');
                                        }
                                        if (response.success == false) {
                                            alert_float('danger', response.message);
                                            $('#issue').modal('hide');
                                        }

                                    }
                                });
                            }


                        } else {
                            if (id == "") {
                                //  alert(response)
                                // const data=JSON.parse(response);
                                const data = response.message;
                                $("#issuename_span").html(data.issue_name);
                                $("#reminder1_span").html(data.reminder_one);
                                $("#reminder2_span").html(data.reminder_two);
                                $("#milestone_span").html(data.milestone);
                                $("#durations_span").html(data.duration);
                                $("#defaultmilestone_span").html(data.defaultmile);
                                $("#defaultduration_span").html(data.defaultduration);
                                $("#defaultreminderone_span").html(data.defaultr1);
                                $("#defaultremindertwo_span").html(data.defaultr2);
                            } else {
                                alert_float('danger', response.message);
                            }

                        }
                    }
                });


            });

            function new_issue() {
                adddisabledfield();
                triggerInputClass();
                $('.btn_add_test').attr('hidden', true);
                $('#issue').modal('show');
                $('.edit-title').addClass('hide');


            }

            function strtoarray(item) {
                var n = item.toString().indexOf(",");
                if (n === -1) {
                    return item;
                } else {
                    var a = item.split(',');
                    return a;
                }


            }

            function addmilestonesfiled() {
                var wrapper = $(".input_fields_wrap1");
                var data = '<div class="input_fields_wrap_filled"></div></div>';
                wrapper.append(data);
            }

            function adddisabledfield() {
                var wrapper = $(".disabled_wrapper");

                // ajay sir code
                var data = '<div class="milestone-row"><input type="hidden" name="milesid[]" /><div class="form-input-field full-width"><input name="defaultmilestonename" id="defaultmilestonename" type="text"  value="End of Project"><label for="" title="Milestone Name" data-title="Milestone Name"></label><span><p id="defaultmilestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="defaultmilestoneduration" onblur=addreminderforshorttermproject() id="defaultmilestoneduration" type="number" required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="defaultduration_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input name="defaultmilestoner1" type="number" id="defaultmilestoner1" value=""><label for="" title="Reminder 1(Days)" data-title="Reminder 1(Days)"></label> <span> <p id="defaultreminderone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="defaultmilestoner2" type="number" id="defaultmilestoner2" value=""><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="defaultremindertwo_span" style="color:red"></p></span></div><div class="add-milestone-btn mT7">                    <label for="" class="mR0">Add Milestone <span><i class="fa fa-plus-square-o btn-color add_field_button fa-2x"  style="" ></i></span></label></div></div>'
                // ajay sir code ends
                wrapper.append(data);

            }

            function changeStatus(invoker, id, status) {
                let data = {};
                if ($(invoker).is(":checked")) {
                    data = {
                        'id': id,
                        'status': 1
                    }
                } else {
                    data = {
                        'id': id,
                        'status': 0
                    }
                }

                $.ajax({
                    type: "post",
                    url: admin_url + "issues/change_issue_status",
                    data: data,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.success == true) {
                            alert_float("success", response.message);
                            $('.table-issues').DataTable().ajax.reload();
                        }

                    }
                });
            }

            function removeappended(parent) {
                $("." + parent).remove();
                x--;
                // alert(x);
                var durations = $('input[name^=durations]').map(function(idx, duration) {
                    //  if(duration !='')
                    return $(duration).val();
                }).get();
                changedurationonedit();
                var value = findtotaldurationsum(durations);
                $("#defaultmilestoneduration").val(value);
                if (x == 0) {
                    $("#filleddurationvalue0").removeAttr('disabled');
                    $("#filledmilestone0").removeAttr('disabled');
                    $('#filledr10').removeAttr('disabled');
                    $('#filledr20').removeAttr('disabled');


                    $('#defaultmilestonename').removeAttr('disabled');
                    $('#defaultmilestoneduration').removeAttr('disabled');
                    $('#defaultmilestoner1').removeAttr('disabled');
                    $('#defaultmilestoner1').parent('div').removeAttr('hidden');
                    $('#defaultmilestoner2').removeAttr('disabled');
                    $('#defaultmilestoner2').parent('div').removeAttr('hidden');
                    $('#filledr20').closest('div').removeAttr('hidden');
                    $('#filledr10').closest('div').removeAttr('hidden');
                    $('#defaultmilestonename').val("End of Project");
                    $("#filledmilestone0").val("End of Project");
                }

            }

            function removedisabledfield() {
                var wrapper = $(".disabled_wrapper");
                wrapper.empty();
            }

            function postappend(parent) {
                // alert("."+parent);
                x++;
                // alert(x);
                if (x > 0) {
                    $('#filledmilestone0').attr('disabled', 'disabled');
                    $('#filledmilestone0').val("End of Project");
                    $('#filleddurationvalue0').attr('disabled', 'disabled');
                    $('#filledr10').attr('disabled', 'disabled');
                    $('#filledr10').val(0);
                    $('#filledr20').attr('disabled', 'disabled');
                    $('#filledr20').val(0);
                    $('#filledr20').closest('div').attr('hidden', 'hidden');
                    $('#filledr10').closest('div').attr('hidden', 'hidden');
                    var durations = $('input[name^=durations]').map(function(idx, duration) {
                        return $(duration).val();
                    }).get();
                    changedurationonedit();


                }
                $("." + parent).after('<div class="milestone-row appendedfield' + x + '"><input type="hidden" name="milesid[]" /><div class="form-input-field full-width"><input name="milestones[]" id="milestonename' + (x - 1) + '" type="text" ><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span' + (x - 1) + '" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" onchange="changedefaultduration(event);changedurationonedit();"  id="milestoneduration' + (x - 1) + '" type="number" onblur=addreminder(' + (x - 1) + ') required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span' + (x - 1) + '" style="color:red"></p></span></div><div class="form-input-field duration-field r1"><input  name="reminder1[]" type="number" id="milestoner1' + (x - 1) + '" value=""><label for="" title="Reminder 1(Days)" data-title="Reminder 1(Days)"></label> <span> <p id="reminder1_span' + (x - 1) + '" style="color:red"></p></span></div><div class="form-input-field duration-field r2"><input  name="reminder2[]" type="number" id="milestoner2' + (x - 1) + '" value=""><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span' + (x - 1) + '" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-square-o btn-color remove_field fa-2x " onclick=removeappended("appendedfield' + x + '") style="margin-top:6px" ></i><i class="fa fa-plus-square-o btn-color add_field_after_button fa-2x " onclick=postappend("appendedfield' + x + '") style="margin-top:6px;margin-left:3px;" ></i></span></div></div>');
                // alert(x)
            }

            function remove(event, id) {
                var filledwrapper = $(".input_fields_wrap_filled");
                event.preventDefault();
                $.ajax({
                    type: "post",
                    url: admin_url + 'issues/deletemilestone',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        x--;
                        // alert(x)
                        if (response.success == true) {
                            $('.filled_field' + id).remove();
                            if (x == 0) {
                                $("#filleddurationvalue0").removeAttr('disabled');
                                $("#filledmilestone0").removeAttr('disabled');
                                $('#filledr10').removeAttr('disabled');
                                $('#filledr20').removeAttr('disabled');
                                $('#filledr20').closest('div').removeAttr('hidden');
                                $('#filledr10').closest('div').removeAttr('hidden');
                                $("#filleddurationvalue0").val(0);
                                $('#defaultmilestonename').val("End of Project");
                                $("#filledmilestone0").val("End of Project");

                            } else {
                                changedurationonedit();
                            }
                            alert_float("success", response.message);
                        }
                        if (response.success == false) {
                            alert_float("danger", response.message);
                        }
                    }
                });

            }

            function changedurationonedit() {
                var durations = $('input[name^=durations]').map(function(idx, duration) {
                    return $(duration).val();
                }).get();
                // var value =findtotaldurationsum(durations);
                var count = 0
                for (let i = 1; i < durations.length; i++) {
                    if (durations[i] == '') {
                        count = count + 0;
                    } else if (parseInt(durations[i]) < 0) {
                        count = count + 0;
                    } else {
                        count = count + parseInt(durations[i]);
                    }
                }
                // alert(count)
                $("#filleddurationvalue0").val(count);
            }


            function edit_issue(invoker, id) {
                addmilestonesfiled();
                // $('.btn_add_test').removeAttr('hidden');
                $('.btn_add_test').attr('hidden', true);
                var wrapper = $(".input_fields_wrap_filled");
                var add_button = $(".add_field_button");
                $('#additionalhiddenfields').append(hidden_input('id', id));
                //  $('.input_fields_wrap_filled').append('<input type="hidden" value="'+id+'" name="id">');
                //  $('.input_fields_wrap_filled').append('<input type="hidden" value="'+$(invoker).data('name')+'" name="name">');

                $('#additionalhiddenfields').append(hidden_input('name', $(invoker).data('name')));
                $('#issue input[name="issue_name"]').val($(invoker).data('name'));

                var milestones = strtoarray($(invoker).data('milestone'));
                var durations = strtoarray($(invoker).data('duration'));
                var reminder_one = strtoarray($(invoker).data('reminder_one'));
                var reminder_two = strtoarray($(invoker).data('reminder_two'));
                var milestones_id = strtoarray($(invoker).data('milestoneid'));
                if (Array.isArray(milestones)) {
                    x = x + milestones.length - 1;
                    for (let i = 0; i < milestones.length; i++) {
                        var att = "",
                            callback = "",
                            additionaldata = "",
                            callback2 = "";

                        // ajay sir code

                        if (i == 0) {
                            att = 'disabled="disabled"';
                            callback = "onblur=addfilledfieldreminder(" + i + ")";
                            callback2 = "";
                            additionaldata = "";
                            hidden = 'hidden="hidden"';
                            label = '<label for="" class="add-milestone-btn mT7">Add Milestone';
                            labelend = '</label>';
                            style = 'style="margin-left:5px;"';
                        } else if (i > 0) {
                            additionaldata = '<i class="fa fa-minus-square-o remove_filled_field fa-2x btn-color" onclick="remove(event,' + milestones_id[i] + ');" style="margin-top:6px;" ></i>';
                            callback = "onblur=addfilledfieldreminder(" + i + ")";
                            callback2 = "onchange=changedurationonedit()";
                            hidden = "";
                            label = "";
                            labelend = "";
                            style = 'style="margin-top:6px;margin-right:3px;"';
                        }
                        // var data='<div class="milestone-row filled_field'+milestones_id[i]+'"'+' style=""><input type="hidden" name="milesid[]" value="'+milestones_id[i]+'"'+' /><div class="form-input-field"><input name="milestones[]" type="text" '+att+' id="filledmilestone'+i+'" value="'+milestones[i]+'"'+'><label for="" title="Milestone Name" data-title="Milestone Name></label><span> <p id="milestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" '+att+' value="'+durations[i]+'"'+' id="filleddurationvalue'+i+'" "type="number" '+callback+' required=""  '+callback2+'><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input required name="reminder1[]" type="number" '+att+' id="filledr1'+i+'" value="'+reminder_one[i]+'"' + '> <label for="" title="Reminder1(Days)" data-title="Reminder1(Days)"></label> <span> <p id="reminder1_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="reminder2[]" type="number" id="filledr2'+i+'" '+att+' value="'+reminder_two[i]+'"'+ '><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-circle remove_filled_field fa-2x " onclick="remove(event,'+milestones_id[i]+');" style="margin-top:6px;color:#135cd6;" ></i><i class="fa fa-plus-circle add_field_after_button fa-2x " onclick=postappend("filled_field'+milestones_id[i]+'")  style="color:#135cd6;margin-top:6px" ></i></span></div></div>';
                        // var data='<div class="milestone-row filled_field'+milestones_id[i]+'"'+' style=""><input type="hidden" name="milesid[]" value="" /><div class="form-input-field"><input name="milestones[]" type="text" '+att+' id="filledmilestone'+i+'" value="'+milestones[i]+'"'+'><label for="" title="Milestone Name" data-title="Milestone Name></label><span> <p id="milestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" '+att+' value="'+durations[i]+'"'+' id="filleddurationvalue'+i+'" "type="number" '+callback+' required=""  '+callback2+'><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input required name="reminder1[]" type="number" '+att+' id="filledr1'+i+'" value="'+reminder_one[i]+'"' + '> <label for="" title="Reminder1(Days)" data-title="Reminder1(Days)"></label> <span> <p id="reminder1_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="reminder2[]" type="number" id="filledr2'+i+'" '+att+' value="'+reminder_two[i]+'"'+ '><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-circle remove_filled_field fa-2x " onclick="remove(event,'+milestones_id[i]+');" style="margin-top:6px;color:#135cd6;" ></i><i class="fa fa-plus-circle add_field_after_button fa-2x " onclick=postappend("filled_field'+milestones_id[i]+'")  style="color:#135cd6;margin-top:6px" ></i></span></div></div>';
                        var data = '<div class="milestone-row filled_field' + milestones_id[i] + '"' + ' style=""><input type="hidden" name="milesid[]" value="" /><div class="form-input-field full-width"><input name="milestones[]" type="text" ' + att + ' id="filledmilestone' + i + '" value="' + milestones[i] + '"' + '><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" ' + att + ' value="' + durations[i] + '"' + ' id="filleddurationvalue' + i + '" "type="number" ' + callback + ' required=""  ' + callback2 + '><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span" style="color:red"></p></span></div><div ' + hidden + ' class="form-input-field duration-field r1"><input  name="reminder1[]" type="number"  id="filledr1' + i + '" ' + att + ' value="' + reminder_one[i] + '"' + '><label for="" title="Reminder 1(Days)" data-title="Reminder 1(Days)"></label> <span> <p id="reminder1_span" style="color:red"></p></span></div><div ' + hidden + ' class="form-input-field duration-field r2"><input  name="reminder2[]" type="number"  id="filledr2' + i + '" ' + att + ' value="' + reminder_two[i] + '"' + '><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span" style="color:red"></p></span></div><div class="duration-field">' + label + '<span>' + additionaldata + '<i class="fa fa-plus-square-o add_field_after_button btn-color fa-2x " ' + style + 'onclick=postappend("filled_field' + milestones_id[i] + '")   ></i></span>' + labelend + '</div></div>';

                        // ajay sir code end

                        $(wrapper).append(data);


                    }
                    $('#issue').modal('show');
                    $('.add-title').addClass('hide');

                    triggerInputClass();
                } else {
                    var att = "";;
                    var callback = "onblur=addfilledfieldreminder(0)";

                    // ajay sir code
                    // var data='<div class="milestone-row filled_field'+milestones_id+'"'+'style=""><input type="hidden" name="milesid[]" value="'+milestones_id+'"'+' /><div class="form-input-field"><input name="milestones[]" type="text" id="filledmilestone0" value="'+milestones+'"'+'><label for="" title="Milestone Name" data-title="Milestone Name></label><span> <p id="milestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" '+callback+' value="'+durations+'"'+' type="number" id="filleddurationvalue0" required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input required name="reminder1[]" type="number" id="filledr10" value="'+reminder_one+'"' + '> <label for="" title="Reminder1(Days)" data-title="Reminder1(Days)"></label> <span> <p id="reminder1_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="reminder2[]" type="number" id="filledr20" value="'+reminder_two+'"'+ '><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span" style="color:red"></p></span></div><div class="duration-field"><span><i class="fa fa-minus-circle remove_filled_field fa-2x " onclick="remove(event,'+milestones_id+');" style="margin-top:6px;color:#135cd6;" ></i><i class="fa fa-plus-circle add_field_after_button fa-2x " onclick=postappend("filled_field'+milestones_id+'")  style="color:#135cd6;margin-top:6px" ></i></span></div></div>';
                    var data = '<div class="milestone-row filled_field' + milestones_id + '"' + 'style=""><input type="hidden" name="milesid[]"  /><div class="form-input-field full-width"><input name="milestones[]" type="text" id="filledmilestone0" value="' + milestones + '"' + '><label for="" title="Milestone Name" data-title="Milestone Name"></label><span> <p id="milestone_span" style="color:red"></p></span></div><div class="form-input-field duration-field"><input  name="durations[]" ' + callback + ' value="' + durations + '"' + ' type="number" id="filleddurationvalue0" required="" ><label for="" title="Duration(Days)" data-title="Duration(Days)"></label><span> <p id="durations_span" style="color:red"></p></span></div><div class="form-input-field duration-field r1"><input  name="reminder1[]" type="number" id="filledr10" value="' + reminder_one + '"' + '><label for="" title="Reminder 1(Days)" data-title="Reminder 1(Days)"></label> <span> <p id="reminder1_span" style="color:red"></p></span></div><div class="form-input-field duration-field r2"><input  name="reminder2[]" type="number" id="filledr20" value="' + reminder_two + '"' + '><label for="" title="Reminder 2(Days)" data-title="Reminder 2(Days)"></label> <span> <p id="reminder2_span" style="color:red"></p></span></div><div class="add-milestone-btn mT7"><label for="">Add Milestone <span><i class="fa fa-plus-square-o add_field_after_button btn-color fa-2x " onclick=postappend("filled_field' + milestones_id + '")  style="" ></i></span></label></div>';
                    // ajay sir end
                    $(wrapper).append(data);
                    $('.add-title').addClass('hide');
                    $('#issue').modal('show');

                }
                triggerInputClass();

            }


            function triggerInputClass() {
                $("form#issue_form :input").each(function() {
                    if ($(this).val()) {
                        $(this).addClass("label-up");
                    } else {
                        $(this).addClass("labellll-up");
                    }
                })
            }
        </script>
        </body>

        </html>
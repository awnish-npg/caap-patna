<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$area = $GLOBALS['current_user']->area;
init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-header">
                            <a href="#" onclick="new_subregion(); return false;" class="btn btn-custom add-area-admin pull-right display-block">
                                <?php echo _l('new_subregion'); ?>
                            </a>

                            <h1>Manage Municipal Zone<span>Here you can view, add, edit and deactivate Municipal Zones </span></h1>
                            <hr class="hr-panel-heading" />
                        </div>

                        <div class="clearfix"></div>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <?php render_datatable(array(
                                _l('subregion_name'),
                                _l('City/ Corporation'),
                                //_l('area_name'), 
                                _l('status'),
                                _l('options')
                            ), 'departments'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sidebarModal" id="department" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('subregion/subregion')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_subregion'); ?></span>
                    <span class="add-title"><?php echo _l('new_subregion'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="addition"></div>
                        <p class="form-instruction add-title">Fill in the following field(s) to add a Municipal Zone</p>
                        <p class="form-instruction edit-title">Fill in the following field(s) to edit a Municipal Zone</p>
                        <hr class="hr-panel-model" />




                        <div class="form-group" app-field-wrapper="region_id">
                            <div class="form-select-field">

                                <?php
                                $selected = [];
                                echo render_select('region_id', $region, array('id', 'region_name'), '', $selected, array('data-width' => '100%', 'data-none-selected-text' => 'City/ Corporation', 'title' => 'Select City/ Corporation'), array(), 'no-mbot');
                                ?>
                                <label class="select-label">City/ Corporation*</label>
                                <p id="region_id-error" class="text-danger"></p>
                            </div>
                        </div>


                        <?php echo render_input('region_name', 'subregion'); ?>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-custom"><?php echo _l('submit'); ?></button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>

                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php init_tail(); ?>
    <style>
        .has-error .bootstrap-select .dropdown-toggle {
            border-color: #d7d7d7 !important;
        }
    </style>
    <script>
        $(function() {
            var columnDefs = [null, null, {
                "width": "5%"
            }, {
                "width": "5%",
                "className": "dt_center_align"
            }];
            initDataTable('.table-departments', window.location.href, [3], [3], undefined, [0, 'asc'], '', columnDefs);
            appValidateForm($('form'), {
                region_name: {
                    required: true,
                    maxlength: 50,
                    alphanumericspace: false
                },

                region_id: 'required'
            }, manage_subregion);
            $('#department').on('hidden.bs.modal', function(event) {
                $('#addition').html('');
                $('#department input[type="text"]').val('');
                $('.add-title').removeClass('hide');
                $('.edit-title').removeClass('hide');
                $('#department input[name="region_name"]').removeClass('label-up');
                $('.text-danger').css("display", "none");
                $('.selection').css("display", "none");
            });
            /*Function Call to get and populate Regions in select box under Admin Form*/
            getRegionData();

        });

        $("#region_id").on("change", function() {
            if ($('#region_id').val() != '') {
                $('#region_id-error').addClass("hide");
            } else {
                $('#region_id-error').removeClass("hide");
            }
        });

        //    $("form").submit(function(){
        //         if($('#region_id').val() == '' ){
        //             $('#region_id').parents(".form-group.no-mbot").parents(".form-group").append('<p id="region_id-error" class="text-danger">This field is required.</p>');
        //         }
        //     });



        //    $("#region_id").on("change", function(){
        //         $("#region_id-error").remove();
        //         if($('#region_id').val() == '' || $('#region_id').val() == null){
        //             $('#region_id').parents(".form-group.no-mbot").parents(".form-group").append('<p id="region_id-error" class="text-danger">This field is required.</p>')
        //             //$('[app-field-wrapper="region_id"]').append('<p id="region_name-error" class="text-danger">This field is required.</p>');
        //             return false;
        //         }
        //    });

        /*Function to get and populate Region Data*/
        const getRegionData = (selectedRegion = null) => {
            let area = "<?= $area ?>";
            let data = {
                'area_id': area,
                'group_by': false
            }
            $.post(admin_url + 'region/get_region', data).done((res) => {
                res = JSON.parse(res);

                if (res.success == true) {
                    REGION_LIST = {
                        ...res.region_list
                    };
                    let options = "";
                    //let options = `<option value=''>Select City/ Corporation</option>`;
                    for (let region in REGION_LIST) {
                        let regionId = REGION_LIST[region][0].region_id;
                        let regionName = REGION_LIST[region][0].region_name;
                        options += `<option value='${regionId}'>${regionName}</option>`

                    }
                    $("#region_id").html(options);
                    $('#region_id').selectpicker('refresh');
                    if (selectedRegion !== null) {
                        $('#region_id').selectpicker('val', selectedRegion);
                    }
                }
            }).fail(function(data) {
                var error = JSON.parse(data.responseText);
                console.log("Region option ajax error:", error);
            });
        }

        function manage_subregion(form) {
            var data = $(form).serialize();
            var url = form.action;
            // if($('#region_id').val() == '' || $('#region_id').val() == null){
            //     $('#region_id').parents(".form-group.no-mbot").parents(".form-group").append('<p id="region_id-error" class="text-danger">This field is required.</p>')
            //     //$('[app-field-wrapper="region_id"]').append('<p id="region_name-error" class="text-danger">This field is required.</p>');
            //     return false;
            // }
            $("#region_id-error").remove();
            console.log("test 2", $('#region_id').val());

            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                    $('.table-departments').DataTable().ajax.reload();
                    $('#department').modal('hide');
                } else {
                    alert_float('danger', response.message);
                }

            }).fail(function(data) {
                var error = JSON.parse(data.responseText);
                alert_float('danger', error.message);
            });
            return false;
        }

        function new_subregion() {
            $('#department').modal('show');
            $('#area_id').val('');
            $('#region_id').val('');
            $('.edit-title').addClass('hide');
            $("form").trigger('reset');
            $(".form-group").removeClass("has-error");
        }

        function edit_subregion(invoker, id) {
            $('#addition').append(hidden_input('id', id));
            $('#department input[name="region_name"]').val($(invoker).data('name'));
            $('#department input[name="region_name"]').addClass('label-up');
            // $('#department #region_id').selectpicker('val', $(invoker).data('region'));
            getRegionData($(invoker).data('region'));
            $('#department').modal('show');
            $('.add-title').addClass('hide');
            $(".form-group").removeClass("has-error");
        }



        const changeStatus = (invoker, id) => {
            let url = admin_url + "subregion/change_subregion_status";
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
                processing: 'true',
                serverSide: 'true',
                type: "POST",
                url: url,
                data: data,
                success: function(res) {
                    res = JSON.parse(res);
                    console.log(res);
                    if (res.success) {
                        $(this).prop('checked', !$(this).prop('checked'));
                        if (res.check_status) {
                            $(invoker).prop('checked', true)
                        } else if (!res.check_status) {
                            $(invoker).prop('checked', false)
                        }
                        $('.table-departments').DataTable().ajax.reload();
                        alert_float('success', res.message);
                    } else {
                        if (res.check_status) {
                            $(invoker).prop('checked', true)
                        } else if (!res.check_status) {
                            $(invoker).prop('checked', false)
                        }
                        $('.table-departments').DataTable().ajax.reload();
                        alert_float('danger', res.message);
                    }
                }
            })

        }
    </script>
    </body>

    </html>
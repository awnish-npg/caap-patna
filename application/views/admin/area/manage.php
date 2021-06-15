<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s custom-panel1">
                    <div class="panel-body">
                        <div class="panel-header">
                            <a href="#" onclick="new_area(); return false;" class="btn btn-custom add-area-admin pull-right display-block">
                                Add State
                            </a>

                            <h1>Manage States<span>Here you can view, add, edit and deactivate States. </span></h1>
                            <hr class="hr-panel-heading" />
                        </div>
                        <div class="table-responsive">
                        <?php render_datatable(array(
                            _l('name'),
                            _l('City Action Plan'),
                            _l('Logo'),
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
<div class="modal fade sidebarModal" id="department" tabindex="-1" role="dialog" style="width:40%">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('area/area'), array('id' => 'area_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_area'); ?></span>
                    <span class="add-title"><?php echo _l('new_area'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="addition"></div>
                        <p class="form-instruction add-title">Fill in the following fields to add a State</p>
                        <p class="form-instruction edit-title">Fill in the following field to edit a State</p>
                    </div>
                    <hr class="hr-panel-model" />
                </div>

                <div class="form-group">
                    <?php echo render_input('name', 'area_name'); ?>
                </div>
                <div class="form-input-field mB15">
                    <div for="attachment" class="mB5">City Action Plan<span style="font-size:12px;">&nbsp;(Select PDF only | Max size: 5 MB)</span></div>
                    <div>
                    <input type="file" name="file" id="pdf" class="form-control">
                    <label for="file" title="" data-title=""></label>
                  
                    </div>
                </div>
                <div class="form-input-field mB15">
                    <div for="attachment" class="mB5">Logo<span style="font-size:12px;">&nbsp;(Select Image only | Max size: 2 MB | Suggested Dimention: 225px X 150px)</span></div>
                    <div>
                    <input type="file" name="logo" id="img" class="form-control">
                    <label for="file" title="" data-title=""></label>
                  
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-custom">Save</button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>

                </div>
            </div><!-- /.modal-content -->

            <div class="notes mB20">
                <h5>
                    <p class="form-field-notes mL0">Note</p>
                </h5>
                <p>Please contact the developer to add state initials for every new/ edited state in the system. This is to allow action item creation at a state level.</p>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <?php init_tail(); ?>
    <script>
        $(function() {
            var columnDefs = [{
                "width": "30%"
            }, {
                "width": "30%"
            }, {
                "width": "30%"
            }, {
                "width": "5%",
                "className": "dt_status_column"
            }, {
                "width": "5%",
                "className": "dt_center_align"
            }];
            initDataTable('.table-departments', window.location.href, [4], [4], undefined, [0, 'asc'], '', columnDefs);
            appValidateForm($('form'), {
                name: {
                    required: true,
                    maxlength: 50,
                    charsonly: true,
                    noSpace: true,

                },
                // file: {
                //   required: true,
                // },
            }, manage_departments);
            $('#department').on('hidden.bs.modal', function(event) {
                $('#department input[name="name"]').removeClass("label-up");
                $('#addition').html('');
                $('#department input[type="text"]').val('');
                $('.add-title').removeClass('hide');
                $('.edit-title').removeClass('hide');
                $('.text-danger').css("display", "none");
            });
        });

        function manage_departments(form) {

            var formURL = $(form).attr("action");
            var formData = new FormData($(form)[0]);
            var url = form.action;

            $.ajax({
                type: $(form).attr('method'),
                data: formData,
                mimeType: $(form).attr('enctype'),
                contentType: false,
                cache: false,
                processData: false,
                url: formURL

            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                    $('.table-departments').DataTable().ajax.reload();
                    $("#department").trigger('reset');
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

        $('input[name="file"]'). change(function(e){
        var fileName = e. target. files[0]. name;
        var fileSize = e. target. files[0]. size;
        extension = fileName.split('.').pop(); 
            if(fileName != ''){
                $('#pdf-error').css("display", "none");
            }
            if(extension != 'pdf'){
                alert_float('danger', 'Only PDF file type is allowed');
                $('input[name=file]').val('');
                return false;
            }
            if(fileSize > 5242880){
                alert_float('danger', 'File uploaded is greater than 5 MB');
                $('input[name=file]').val('');
                return false;
            }
        });

        $('input[name="logo"]'). change(function(e){
        var fileName = e. target. files[0]. name;
        var fileSize = e. target. files[0]. size;
        extension = fileName.split('.').pop();
        var validExtensions = ['jpg','png','jpeg'];
            if(fileName != ''){
                $('#img-error').css("display", "none");
            }

            if ($.inArray(extension, validExtensions) == -1){
                alert_float('danger', 'Only JPEG or JPG or PNG file type is allowed');
                $('input[name=logo]').val('');
                return false;
        }
           
            if(fileSize > 2097152){
                alert_float('danger', 'File uploaded is greater than 2 MB');
                $('input[name=logo]').val('');
                return false;
            }
        });

        function new_area() {
            $('#department').modal('show');
            $('.edit-title').addClass('hide');
        }

        function edit_area(invoker, id) {
           
            $('#addition').append(hidden_input('id', id));
            $('#department input[name="name"]').val($(invoker).data('name'));
            $('#department input[name="name"]').addClass("label-up");
            //$('#department input[name="email"]').val($(invoker).data('email'));
            $('#department').modal('show');
            $('.add-title').addClass('hide');
            $('.text-danger').css("display", "none");
        }

        const changeStatus = (invoker, id) => {
            
            let url = admin_url + "area/change_area_status";
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
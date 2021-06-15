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
                                Add Exception
                            </a>

                            <h1>Manage Exceptions<span>Here you can view, add, edit and deactivate Exceptions.</span></h1>
                            <hr class="hr-panel-heading" />
                        </div>
                        <?php render_datatable(array(
                            _l('name'),
                            _l('status'),
                            _l('options')
                        ), 'departments'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sidebarModal" id="department" tabindex="-1" role="dialog" style="width:40%">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('exceptions/save')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Edit Exception</span>
                    <span class="add-title">Add Exception</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="addition"></div>
                        <p class="form-instruction add-title">Fill in the following field to add an exception</p>
                        <p class="form-instruction edit-title">Fill in the following field to edit an exception</p>
                    </div>
                    <hr class="hr-panel-model" />
                </div>


                <?php echo render_input('name', 'Exception Name*'); ?>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-custom">Save</button>
                    <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>

                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <?php init_tail(); ?>
    <script>
        $(function() {
            var columnDefs = [{ "width": "90%" },{ "width": "5%" },{ "width": "5%", "className": "dt_center_align" }];
            initDataTable('.table-departments', window.location.href, [2], [2], undefined, [0, 'asc'], '',columnDefs);
            appValidateForm($('form'), {
                name: {
                    required: true,
                    maxlength: 50,
                    charsonly: true,
                    noSpace: true,
                },

            }, manage_exception);
            $('#department').on('hidden.bs.modal', function(event) {
                $('#department input[name="name"]').removeClass("label-up");
                $('#addition').html('');
                $('#department input[type="text"]').val('');
                $('.add-title').removeClass('hide');
                $('.edit-title').removeClass('hide');
                $('.text-danger').css("display","none");
            });
        });

        function manage_exception(form) {

            var data = $(form).serialize();
            var url = form.action;

            $.post(url, data).done(function(response) {
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

        function new_area() {
            $('#department').modal('show');
            $('.edit-title').addClass('hide');
        }

        function edit_area(invoker, id) {
            console.log(invoker);
            $('#addition').append(hidden_input('id', id));
            $('#department input[name="name"]').val($(invoker).data('name'));
            $('#department input[name="name"]').addClass("label-up");
            $('#department input[name="email"]').val($(invoker).data('email'));
            $('#department').modal('show');
            $('.add-title').addClass('hide');
        }

        const changeStatus = (invoker, id) => {

            let url = admin_url + "exceptions/change_exception_status";
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

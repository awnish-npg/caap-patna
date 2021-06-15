<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-header">
                            <a href="#" onclick="new_region(); return false;"
                                class="btn btn-custom add-area-admin pull-right display-block">
                                <?php echo _l('new_region'); ?>
                            </a>

                            <h1>Manage City/ Corporation<span>Here you can view, add, edit and deactivate Cities/ Corporations.</span></h1>
                        </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <div class="table-responsive">
                    <?php render_datatable(array(
                        _l('City/ Corporation'),
                        _l('area_name'),
                        _l('status'),
                        _l('options')
                        ),'departments'); ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sidebarModal" id="department" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('region/region')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_region'); ?></span>
                    <span class="add-title"><?php echo _l('new_region'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="addition"></div>
                        <p class="form-instruction add-title">Here you can create a new City/ Corporation </p>
                        <p class="form-instruction edit-title">Here you can edit the City/ Corporation</p>
                        <hr class="hr-panel-model" />
                        <!-- <div class="form-group">
                            <div class="form-input-field">
                                <input required type="text" autocomplete="off" name="region_name" id="name" autofocus>
                                <label for="region_name" title="Region*" data-title="Region*"></label>
                            </div>
            			</div> -->
                       
                        <?php  echo render_input('region_name','region_name'); ?>        

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
    <script>
        $(function(){
           var columnDefs = [null, null,{ "width": "5%" },{ "width": "5%", "className": "dt_center_align" }];
           initDataTable('.table-departments', window.location.href, [3], [3], undefined, [0, 'asc'],'',columnDefs);
           appValidateForm($('form'),{
           region_name: {
                    required:true,
                    maxlength: 50,
                    alphanumericspace:true
                }
            }, manage_region);
           $('#department').on('hidden.bs.modal', function(event) {
            $('#addition').html('');
            $('#department input[type="text"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
            $('input').removeClass("label-up");
            $('.text-danger').css("display","none");
        });
       });
        function manage_region(form) {
            var data = $(form).serialize();
            var url = form.action;

            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if(response.success == true){
                    alert_float('success',response.message);
                    $('.table-departments').DataTable().ajax.reload();
                    $('#department').modal('hide');
                }else{
                    alert_float('danger', response.message);
                }

            }).fail(function(data){
                var error = JSON.parse(data.responseText);
                alert_float('danger',error.message);
            });
            return false;
        }
        function new_region(){
            $('#department').modal('show');
            $("#region_id").focus();
            //$('#area_id').val('');
            $('.edit-title').addClass('hide');
        }
        function edit_region(invoker,id){
            var area = $(invoker).data('area');
            $('#addition').append(hidden_input('id',id));
            $('#department input[name="region_name"]').val($(invoker).data('name'));
            //$('#area_id').val($(invoker).data('area'));
            $('#department').modal('show');
            $('.add-title').addClass('hide');
            $('input').addClass("label-up");
        }

        
        const changeStatus = (invoker, id) => {
	
		let url = admin_url + "region/change_region_status";
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

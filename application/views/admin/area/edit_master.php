<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s custom-panel1">
                    <div class="panel-body">
                        <div class="panel-header">
                            <h1>Edit City Action Plan<span>Here you can edit city action plan. </span></h1>
                            <hr class="hr-panel-heading" />
                        </div>
                        <div class="row">
                            <?php echo form_open_multipart('admin/masterplan/edit',array('id'=>'open-new-ticket-form')); ?>
                                <div class="col-md-6">
                                    <div class="form-input-field mB15">
                                        <div for="attachment" class="mB5">City Action Plan<span style="font-size:12px;">&nbsp;(Select PDF only | Max size: 5 MB)</span></div>
                                        <div>
                                        <input type="file" name="file" id="pdf" class="form-control">
                                        <label for="attachment" title="" data-title=""></label>
                                        </div>
                                    </div>
                                <button type="submit" class="btn btn-custom btn-primary">Save</button>
                                <button type="reset" class="btn btn-cancel manage-btn mL5" >Reset</button>
                                </div>
                            <?php echo form_close(); ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <?php init_tail(); ?>
    <script>
    $(function() {
            appValidateForm($('form'), {
                file: {
                  required: true,
                },
                logo: {
                  required: true,
                },

            }, manage_departments);
        });


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

    

        function manage_departments(form) {
            
            var formURL = $(form).attr("action");
            var formData = new FormData($(form)[0]);
            var url = form.action;
            $.ajax({
            type:$(form).attr('method'),
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
                    setTimeout(function(){  window.location.reload(); }, 2000);
                    
                } else {
                    alert_float('danger', response.message);
                }

            }).fail(function(data) {
                var error = JSON.parse(data.responseText);
                alert_float('danger', error.message);
            });
            return false;
        }
    </script>
    </body>

    </html>

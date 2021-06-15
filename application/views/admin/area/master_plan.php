<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-header">
                           <h1>City Action Plan<span>Here you can view all area, city action plan list.  </span></h1>
                            <hr class="hr-panel-heading" />
                        </div>

                        <div class="clearfix"></div>
                        <div class="clearfix"></div>
                        <div class="table-scroll">
                        <?php render_datatable(array(
                            _l('area_name'),
                            _l('City Action Plan'),
                          
                            ),'departments'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(function(){
        var columnDefs = [{ "width": "50%" },{ "width": "50%"}];
        initDataTable('.table-departments', window.location.href, [], [], undefined, [0, 'asc'], '',columnDefs );
    });

</script>
</body>
</html>

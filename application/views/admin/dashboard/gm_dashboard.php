<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
// pre($GLOBALS);
$area = $GLOBALS['current_user']->area;
$role = $GLOBALS['current_user']->role;
if (!empty($tableParams['report_date'])) {
   $summ =   _l('historical');
} else{
   $summ =   _l('as_on_date');;
}
?>
<div id="wrapper">
   <div class="content gm-dashboard">
      <div class="row">
         <div class="col-md-12">
            <div class="gm-filter-container">
               <h1 class="pull-left"><span>Summary of India</span>
               <label><?php echo $summ; ?></label>
               </h1>

               <div class="filter-dropdown pull-right">
                  <button class="modify-filter" type="button">Modify Filter
                     <i class="fa fa-filter" aria-hidden="true"></i> </button>
                  <div class="dropdown d-inline-block export-dropdown">
                     <button class="dropdown-toggle" type="button" data-toggle="dropdown">Export
                        <span class="caret"></span></button>
                     <ul class="dropdown-menu">
                        <li><a href="#" id="summary_export"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></li>
                        <li><a class="pdf-download" href="#" title="Download as PDF" id="export_pdf" style="position: static;"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</a></li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="panel_s" id="chart_container">
               <div class="panel-body">
                  <div class="row">
                     <!-- <div class="col-lg-12">
                        <h2 class="text-center" id="total_count_head"></h2>
                     </div> -->
                     <div class="col-md-6">
                        <div id="piechart"></div>
                     </div>
                     <div class="col-md-1"></div>
                     <div class="col-md-3">
                     <h2 class="text-center" id="total_count_head"></h2>
                        <span class="text-center d-block mT10">Date Range: <?php echo $filter_data;?></span>
                        <div id="task_summary"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="gm-table-container" id="table_container">
               <h2 class="pull-left">Details</h2>
               <div class="clearfix"></div>
               <div class="table-scroll">
                  <table class="table dt-table gm-table" id="summary_table">
                     <thead>
                        <th width="70"><?php echo _l('State'); ?></th>
                        <th class="new" width="80"><?php echo _l('New'); ?></th>
                        <th class="escalated" width="100"><?php echo _l('Delayed'); ?></th>
                        <th class="wip" width="120">In Progress</th>
                        <th class="closed" width="80"><?php echo _l('Closed'); ?></th>
                        <th class="rejected" width="90">Rejected</th>
                        <th class="unassigned" width="120">Unassigned</th>
                        <th class="frozen" width="70">Frozen</th>
                        <th class="total-column" width="70"><?php echo _l('Total'); ?></th>
                     </thead>
                     <tbody>
                        <?php foreach ($statuses as $val) {
                           if ($val['name'] != "") {
                        ?>
                        
                              <tr>
                                 <td><a href="<?php echo admin_url("dashboard/index/?area=" . base64_encode($val['areaid'])) ?>"><?php echo (!empty($val['name'])) ? $val['name'] : ''; ?></a></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 1)"><?php echo (!empty($val['new'])) ? $val['new'] : '0'; ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 7)"><?php echo (!empty($val['escalated'])) ? $val['escalated'] : '0';  ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 2)"><?php echo (!empty($val['wip'])) ? $val['wip'] : '0'; ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 3)"><?php echo (!empty($val['close'])) ? $val['close'] : '0';  ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 5)"><?php echo (!empty($val['rejected'])) ? $val['rejected'] : '0';  ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 9)"><?php echo (!empty($val['unassigned'])) ? $val['unassigned'] : '0';  ?></td>
                                 <td onclick="reportFilter(<?php echo $val['areaid'] ?>, 8)"><?php echo (!empty($val['frozen'])) ? $val['frozen'] : '0';  ?></td>
                                 <td class="gm-total" onclick="reportFilter(<?php echo $val['areaid'] ?>)"><?php echo (!empty($val['total'])) ? $val['total'] : '0'; ?></td>
                              </tr>
                        <?php }
                        } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include_once(APPPATH . 'views/admin/dashboard/widgets/filter_popup.php'); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
   $(document).ready(function() {
      var report_from = $('input[name="report-from"]');
      var report_to = $('input[name="report-to"]');
      var date_range = $('#date-range');

      var time = $('[name="report_months"]').val();
      if (time == 'custom') {
         report_to.attr('disabled', false);
         date_range.addClass('fadeIn').removeClass('hide');
         return;
      } else {
         if (!date_range.hasClass('hide')) {
            date_range.removeClass('fadeIn').addClass('hide');
         }
      }
   });

   var report_from = $('input[name="report-from"]');
   var report_to = $('input[name="report-to"]');
   var date_range = $('#date-range');

   $(document).on('change', 'select[name="report_months"]', function() {
      var val = $(this).val();
      report_to.attr('disabled', true);
      report_to.val('');
      report_from.val('');
      if (val == 'custom') {
         date_range.addClass('fadeIn').removeClass('hide');
         return;
      } else {
         if (!date_range.hasClass('hide')) {
            date_range.removeClass('fadeIn').addClass('hide');
         }
      }
   });

   $(document).on('change', 'input[name="report-from"]', function() {
      var val = $(this).val();
      var report_to_val = report_to.val();
      if (val != '') {
         report_to.attr('disabled', false);
         $('#report-to').val(val);
      } else {
         report_to.attr('disabled', true);
      }
   });

   $("form").submit(function() {
      var fromdate = document.getElementById("report-from").value;
      fromdate = fromdate.split("-");
      var fromdate = new Date(fromdate[2], fromdate[1] - 1, fromdate[0]);
      var from_date = fromdate.getTime();

      var todate = document.getElementById("report-to").value;
      todate = todate.split("-");
      var todate = new Date(todate[2], todate[1] - 1, todate[0]);
      var to_date = todate.getTime();

      if (from_date != '' && to_date != '') {
         if (from_date > to_date) {
            alert_float('danger', 'From date should not be greater than To date');
            return false;
         }
      }
   });



   // $(function() {
   //    var report_from = $('input[name="report-from"]');
   //    var report_to = $('input[name="report-to"]');
   //    var date_range = $('#date-range');

   //    var time = $('[name="report_months"]').val();
   //    if (time == 'custom') {
   //       report_to.attr('disabled', false);
   //       date_range.addClass('fadeIn').removeClass('hide');
   //       return;
   //    } else {
   //       if (!date_range.hasClass('hide')) {
   //          date_range.removeClass('fadeIn').addClass('hide');
   //       }
   //    }
   //    $('select[name="report_months"]').on('change', function() {
   //       var val = $(this).val();
   //       //report_to.attr('disabled', true);
   //       report_to.val('');
   //       report_from.val('');
   //       if (val == 'custom') {
   //          date_range.addClass('fadeIn').removeClass('hide');
   //          return;
   //       } else {
   //          if (!date_range.hasClass('hide')) {
   //             date_range.removeClass('fadeIn').addClass('hide');
   //          }
   //       }
   //    });

   //    report_from.on('change', function() {
   //       var val = $(this).val();
   //       var report_to_val = report_to.val();
   //       if (val != '') {
   //          $('#report-to').val(val);
   //          report_to.attr('disabled', false);
   //       } else {
   //          report_to.attr('disabled', true);
   //       }
   //    });
   // });

   google.charts.load('current', {
      'packages': ['corechart']
   });
   google.charts.setOnLoadCallback(drawChart);

   function drawChart() {
      let data = {
         'category': $('[name="category[]"]').val(),
         'duration': $('[name="duration[]"]').val(),
         'report_months': $('[name="report_months"]').val(),
         'report_from': $('[name="report-from"]').val(),
         'report_to': $('[name="report-to"]').val(),
      }
      $.post(admin_url + "dashboard/get_chart_data", data).done((res) => {
         res = JSON.parse(res);
         var data = google.visualization.arrayToDataTable(res.data);
         var options = {
            chartArea: {
               width: 450,
               height: 300
            },
            legend: {
               position: "right",
               alignment: "center"
            },
            slices: {
               0: {
                  color: '#2C77EE'
               },
               1: {
                  color: '#F43653'
               },
               2: {
                  color: '#f5b500'
               },
               3: {
                  color: '#2BB47A'
               },
               4: {
                  color: '#7e007f'
               },
               5: {
                  color: '#5603b5'
               },
               6: {
                  color: '#c96118'
               }
            }
            // slices: {
            //    0: {
            //       color: '#2C77EE'
            //    },
            //    1: {
            //       color: '#F43653'
            //    },
            //    2: {
            //       color: '#2BB47A'
            //    },
            //    3: {
            //       color: '#7e007f'
            //    },
            //    4: {
            //       color: '#c96118'
            //    },
            //    5: {
            //       color: '#5603b5'
            //    },
            //    6: {
            //       color: '#f5b500'
            //    }
            // }
         };
         let chartArea = document.getElementById('piechart');
         var chart = new google.visualization.PieChart(chartArea);
         chart.draw(data, options);
         let sideTable = "<table class='table dt-table'><thead><tr><th>Status</th><th>Count</th></tr></thead>";
         let status = "";
         let total = 0;
         res.data.map(row => {
            if (row[0] !== "Task" && row[0] !== "Total") {
               switch (row[0]) {
                  case "New":
                     status = 1;
                     break;
                  case "Closed":
                     status = 3;
                     break;
                  case "Delayed":
                     status = 7;
                     break;
                  case "In Progress":
                     status = 2;
                     break;
                  case "Escalated":
                     status = 7;
                     break;
                  case "Rejected":
                     status = 5;
                     break;
                  case "Unassigned":
                     status = 9;
                     break;
                  case "Frozen":
                     status = 8;
                     break;
                  default:
                     status = ""
               }
               //  if (row[0] == "Frozen" || row[0] == "Delayed") {
               //     sideTable += `<tr><td>${row[0]}</td><td>${row[1]}</td></tr>`;
               //  } else {
               //    sideTable += `<tr><td>${row[0]}</td><td><a onclick="reportFilter(0, ${status})" href="javascript://">${row[1]}</a></td></tr>`;
               // }
               sideTable += `<tr><td>${row[0]}</td><td><a onclick="reportFilter(0, ${status})" href="javascript://">${row[1]}</a></td></tr>`;
               total += row[1];
            }
         })
         sideTable += `<tr><td class="total-footer">Total</td><td class="total-footer"><a onclick="reportFilter(0)" href="javascript://">${total}</a></td></tr></table>`;
         document.getElementById("total_count_head").innerHTML = `Total Projects: ${total}`;
         document.getElementById("task_summary").innerHTML = sideTable;
      }).fail((err) => {
         err = JSON.parse(err);
         var data = google.visualization.arrayToDataTable([]);

         var options = {
            title: '',
            chartArea: {
               width: 450,
               height: 300
            },
            legend: {
               position: "right",
               alignment: "center",
            },
            slices: {
               0: {
                  color: '#2C77EE'
               },
               1: {
                  color: '#F43653'
               },
               2: {
                  color: '#2BB47A'
               },
               3: {
                  color: '#f5b500'
               }
            }
         };

         var chart = new google.visualization.PieChart(document.getElementById('piechart'));

         chart.draw(data, options);
         console.log(err)
      });
   }

   //$(document).on('changed.bs.select', '[name="duration[]"]', function(e) {
   $(document).on('change', '[name="duration[]"]', function(e) {
      let durations = $(e.currentTarget).val();
      if(durations == ''){
        options = [];
        $('.custom-categories').selectpicker('val', options);
      }
      selectCategories(durations);
   });

   function selectCategories(duration) {
      if (duration.length) {
         $.post(admin_url + "issues/get_duration_issues", {
            "duration": duration
         }).done((res) => {
            res = JSON.parse(res);
             if (res.success == true) {
                let issues = [...res.issues];
                let options = [];
                if (issues.length > 0) {

                issues.map(issue => {
                    issue.map(val => {
                        options.push(val.id);
                    });
                });
                } else {
                options = [];
                }
            $('.custom-categories').selectpicker('val', options);
            }else if (res.success == false) {
                options = [];
                $('.custom-categories').selectpicker('val', options);
                $('.custom-categories .filter-option-inner-inner').text('No Action Items Selected');
            }
            // let issues = [...res.issues];
            // let options = [];
            // if (issues.length > 0) {

            //    issues.map(issue => {
            //       issue.map(val => {
            //          options.push(val.id);
            //       });
            //    });
            // } else {
            //    options = [];
            // }
            // $('.custom-categories').selectpicker('val', options);
         }).fail((err) => console.log(err));

      }
   }


   $(document).ready(function(e) {
      $('.modify-filter').click(function(e) {
         $('#modify_filter_modal').modal('show');
      })
      $('.filter-list .btn-cancel').click(function(e) {
         $('.filter-list').hide();
      })
   })

   $(document).mouseup(function(e) {
      if ($(e.target).closest(".filter-list").length === 0) {}
   });

   function prepare_export_data() {
      let data = {
         'category': $('[name="category[]"]').val(),
         'duration': $('[name="duration[]"]').val(),
         'report_months': $('[name="report_months"]').val(),
         'report-from': $('[name="report-from"]').val(),
         'report-to': $('[name="report-to"]').val(),
      }
      return data;
   }

   $(document).on("click", "#summary_export", function() {

      let data = prepare_export_data();
      $.ajax({
         url: admin_url + "dashboard/download_export",
         data: data,
         complete: function() {},
         success: function() {
            window.location.replace(this.url);
         }
      });
      $(".dropdown-toggle").click();
      return false;
   });

   $(document).on("click", "#export_pdf", function() {
      let data = prepare_export_data();
      $.ajax({
         url: admin_url + "dashboard/create_aeg_pdf",
         data: data,
         complete: function() {
            //alert(this.url);
         },
         success: function() {
            window.location.replace(this.url);
         }
      });
      $(".dropdown-toggle").click();
      return false;
   })


   function reportFilter(area, status = null) {

      let data = {
         'category': $('[name="category[]"]').val(),
         'report_months': $('[name="report_months"]').val(),
         'report_from': $('[name="report-from"]').val(),
         'report_to': $('[name="report-to"]').val(),
         'duration': $('[name="duration[]"]').val(),
         'area': <?php echo $area ?>,
         'role': <?php echo $role ?>,
         'areaid': area,
      }
      
      let csrf_token_name = '';
      if (typeof csrfData !== "undefined") {
         csrf_token_name = csrfData["hash"];
         // data['csrf_token_name'] = csrfData["hash"]
      }
      let formContent = '<form action="<?php echo admin_url('report/index') ?>" method="post">';
      if (status) {
         formContent += '<input type="hidden" name="ticket[]" value="' + status + '" />';
      } else {
         formContent += '<input type="hidden" name="ticket[]" value="1" />';
         formContent += '<input type="hidden" name="ticket[]" value="2" />';
         formContent += '<input type="hidden" name="ticket[]" value="3" />';
         formContent += '<input type="hidden" name="ticket[]" value="5" />';
         formContent += '<input type="hidden" name="ticket[]" value="7" />';
         formContent += '<input type="hidden" name="ticket[]" value="8" />';
         formContent += '<input type="hidden" name="ticket[]" value="9" />';
      }
      if(data.areaid != 0){
         formContent += '<input type="hidden" name="areaid[]" value="' + data.areaid + '" />';
      }
      if(data.duration != 0){
         formContent += '<input type="hidden" name="duration[]" value="' + data.duration + '" />';
      }
      if(data.category != 0){
         formContent += '<input type="hidden" name="category[]" value="' + data.category + '" />';
      }
      //formContent += '<input type="hidden" name="area[]" value="' + data.area + '" />' +
      formContent += '<input type="hidden" name="report_months" value="' + data.report_months + '" />' +
         '<input type="hidden" name="report-from" value="' + data.report_from + '" />' +
         '<input type="hidden" name="report-to" value="' + data.report_to + '" />' +
         '<input type="hidden" name="area" value="' + data.area + '" />' +
         '<input type="hidden" name="role" value="' + data.role + '" />' +
         '<input type="hidden" name="csrf_token_name" value="' + csrf_token_name + '" />' +
         '<input type="hidden" name="dashboard" value="1" />' +
         '</form>';
      let form = $(formContent);
      $('body').append(form);
      $(form).submit();
   }
</script>
</body>

</html>
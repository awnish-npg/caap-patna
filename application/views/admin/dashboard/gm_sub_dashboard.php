<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// pre($GLOBALS['current_user']);
init_head();
if ($GLOBALS['current_user']->role_slug_url == "ae-area") {
    $area = $GLOBALS['current_user']->area;
} else if ($this->input->get("area")) {
    $area = base64_decode($this->input->get("area"));
}
$role = $GLOBALS['current_user']->role;

$historical = _l('historical');
$as_on_date =  _l('as_on_date');

if (!empty($tableParams['report_date'])) {
    $summ =   _l('historical');
} else {
    $summ =   _l('as_on_date');
}

?>
<div id="wrapper">
    <div class="content gm-dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="gm-filter-container">

                    <?php if ($GLOBALS['current_user']->role_slug_url == "ae-global") { ?>
                        <h1 class="pull-left">Summary of <span><a href='<?php echo admin_url() ?>'>India</a><?php echo (isset($tableParams['area_name']) && !empty($tableParams['area_name'])) ? ', ' . $tableParams['area_name'] : "" ?></span>
                            <label class="summ-tag"><?php echo $summ; ?></label>
                        </h1>

                    <?php } else { ?>
                        <h1 class="pull-left">Summary of <span><?php echo (isset($tableParams['area_name']) && !empty($tableParams['area_name'])) ? $tableParams['area_name'] : "" ?></span>
                            <label class="summ-tag"><?php echo $summ; ?></label>
                        </h1>
                    <?php } ?>


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

                <div class="panel_s">
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
                                <span class="text-center d-block mT10" id="filter_duration_display">Date Range: </span>

                                <div id="task_summary"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gm-table-container">
                    <h2 class="pull-left">Details</h2>
                    <div class="dropdown pull-right">


                        <button class="expand-btn" type="button">Expand</button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="table-scroll">
                        <table class="table gm-table gm-sub-table" style="margin-top: 6px;">
                            <thead>
                                <th width="140" class="sortIcon" onclick="sortTable('region_name', this)" data-order="asc">City/Corp.</th>
                                <th width="80" onclick="sortTable('new', this)" data-order="asc" class="new sortIcon"><?php echo _l('New'); ?></th>
                                <th width="100" onclick="sortTable('escalated', this)" data-order="asc" class="escalated sortIcon"><?php echo _l('Delayed'); ?></th>
                                <th width="120" onclick="sortTable('wip', this)" data-order="asc" class="wip sortIcon">In Progress</th>
                                <th width="80" onclick="sortTable('close', this)" data-order="asc" class="closed sortIcon"><?php echo _l('Closed'); ?></th>
                                <th width="100" onclick="sortTable('rejected', this)" data-order="asc" class="rejected sortIcon">Rejected</th>
                                <th width="120" onclick="sortTable('unassigned', this)" data-order="asc" class="unassigned sortIcon">Unassigned</th>
                                <th width="90" onclick="sortTable('frozen', this)" data-order="asc" class="frozen sortIcon">Frozen</th>
                                <th width="70" onclick="sortTable('total', this)" data-order="asc" class="total-column sortIcon"><?php echo _l('Total'); ?></th>
                            </thead>
                            <tbody id="table_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once(APPPATH . 'views/admin/dashboard/widgets/gm_sub_fliter_popup.php'); ?>

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

    $(function() {
        $('.modify-filter').click(function(e) {
            $('#modify_ae_filter_modal').modal('show');
        })
        $('.filter-list .btn-cancel').click(function(e) {
            $('.filter-list').hide();
        })
        // var report_from = $('input[name="report-from"]');
        // var report_to = $('input[name="report-to"]');
        // var date_range = $('#date-range');

        // var time = $('[name="report_months"]').val();
        // if (time == 'custom') {
        //     report_to.attr('disabled', false);
        //     date_range.addClass('fadeIn').removeClass('hide');
        //     return;
        // } else {
        //     if (!date_range.hasClass('hide')) {
        //         date_range.removeClass('fadeIn').addClass('hide');
        //     }
        // }
        // $('select[name="report_months"]').on('change', function() {
        //     var val = $(this).val();
        //     report_to.attr('disabled', true);
        //     report_to.val('');
        //     report_from.val('');
        //     if (val == 'custom') {
        //         date_range.addClass('fadeIn').removeClass('hide');
        //         return;
        //     } else {
        //         if (!date_range.hasClass('hide')) {
        //             date_range.removeClass('fadeIn').addClass('hide');
        //         }
        //     }
        // });

        // report_from.on('change', function() {
        //     var val = $(this).val();
        //     var report_to_val = report_to.val();
        //     if (val != '') {
        //         report_to.attr('disabled', false);
        //     } else {
        //         report_to.attr('disabled', true);
        //     }
        // });



    });
    loadTable();

    function loadTable() {
        //let data = prepare_export_data();
        let data = {
            'category': $('[name="category[]"]').val(),
            'duration': $('[name="duration[]"]').val(),
            'report_months': $('[name="report_months"]').val(),
            'report-from': $('[name="report-from"]').val(),
            'report-to': $('[name="report-to"]').val(),
        }
        data["area"] = "<?php echo $area; ?>";
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
                return;
            }
        }


        $.post(admin_url + "dashboard/load_sub_dashboard", data).done((res) => {
            res = JSON.parse(res);
            if (res.success) {
                window.tableData = [...res.data];
                prepareTable(window.tableData, data["area"]);
                $('.expand-btn').trigger("click");
                $('#modify_ae_filter_modal').modal('hide');
                $("#filter_duration_display").html("Date Range: " + res.filter_data);
                if (res.filter_data == 'Currently') {
                    $(".summ-tag").html('<?php echo $as_on_date ?>');
                } else {
                    $(".summ-tag").html('<?php echo $historical ?>');
                }

            }
        })

        loadChart();

    }

    // function to prepare table using data argument
    function prepareTable(data, area) {
        let table = "";
        data.forEach(status => {
            table += '<tr class="gm-region active">';
            if (status.region_name != "") {
                table += `<td width="120">${status.region_name}</td><td width="80" data-area="${area}" data-status="1" data-region="${status.region_id}" class="filter-row">${status.new}</td><td width="100" data-area="${area}" data-status="7" data-region="${status.region_id}" class="filter-row">${status.escalated}</td><td width="120" data-area="${area}" data-status="2" data-region="${status.region_id}" class="filter-row">${status.wip}</td><td width="80" data-area="${area}" data-status="3" data-region="${status.region_id}" class="filter-row">${status.close}</td><td width="100" data-area="${area}" data-status="5" data-region="${status.region_id}" class="filter-row">${status.rejected}</td><td width="120" data-area="${area}" data-status="9" data-region="${status.region_id}" class="filter-row">${status.unassigned}</td><td width="90" data-area="${area}" data-status="8" data-region="${status.region_id}" class="filter-row">${status.frozen}</td><td width="70" data-area="${area}" data-region="${status.region_id}" class="filter-row">${status.total}</td>`
                if (status.sub_region_status !== undefined && status.sub_region_status.length > 0) {
                    table += `<tr class="row-show"><td colspan="9"><table width="100%">`;
                    status.sub_region_status.forEach(subStatus => {
                        table += `<tr><td width="140">${subStatus.sub_region_name}</td><td width="80" data-area="${area}" data-status="1" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.new}</td><td width="100" data-area="${area}" data-status="7" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.escalated}</td><td width="120" data-area="${area}" data-status="2" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.wip}</td><td width="80" data-area="${area}" data-status="3" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.close}</td>
                        <td width="100" data-area="${area}" data-status="5" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.rejected}</td><td width="120" data-area="${area}" data-status="9" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.unassigned}</td><td width="90" data-area="${area}" data-status="8" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.frozen}</td><td width="70" data-area="${area}" data-region="${status.region_id}" data-subregion="${subStatus.sub_region_id}" class="filter-row">${subStatus.total}</td></tr>`
                    })
                    table += `</table></td>`;
                }
            }
            table += "</tr>"
        })
        document.getElementById("table_body").innerHTML = table;
    }

    // function to sort table on column header click
    function sortTable(param, e) {
        let order = e.dataset.order;
        if (order === "asc") {
            if (param === "region_name") {
                window.tableData.sort((a, b) => (a[param] > b[param]) ? 1 : ((b[param] > a[param]) ? -1 : 0));
            } else
                window.tableData.sort((a, b) => a[param] - b[param]);
            e.dataset.order = "desc";

        } else if (order === "desc") {
            if (param === "region_name") {
                window.tableData.sort((a, b) => (b[param] > a[param]) ? 1 : ((a[param] > b[param]) ? -1 : 0));
            } else
                window.tableData.sort((a, b) => b[param] - a[param]);
            e.dataset.order = "asc";
        }
        // call prepareTable with sorted data
        prepareTable(window.tableData,"<?php echo $area; ?>");
        $('.expand-btn').trigger("click");
    }

    $(document).on('click', '.sortIcon', function(e) {
        $(this).removeClass('sortIcon');
        $(this).siblings('th').addClass('sortIcon');
    })

    $(document).mouseup(function(e) {
        if ($(e.target).closest(".filter-list").length === 0) {
            $(".filter-list").hide();
        }
    });
    loadChart();

    function loadChart() {
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);
    }

    function drawChart() {
        let data = prepare_export_data();
        data["area"] = "<?php echo $area; ?>";
        $.post(admin_url + "dashboard/get_chart_data", data).done((res) => {
            res = JSON.parse(res);
            var data = google.visualization.arrayToDataTable(res.data);
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
            };
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
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
                        case "WIP":
                            status = 2;
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
                    if (row[0] == "Frozen" || row[0] == "Escalated") {
                        // sideTable += `<tr><td>${row[0] == "Escalated" ? "Delayed" : row[0]}</td><td>${row[1]}</td></tr>`;
                        sideTable += `<tr><td>${row[0] == "Escalated" ? "Delayed" : row[0]}</td><td><a data-area="<?php echo $area; ?>" data-status="${status}" class="filter-row" href="javascript://">${row[1]}</a></td></tr>`;
                    } else {
                        //sideTable += `<tr><td>${row[0] == "WIP" ? "In Progress": row[0]}</td><td><a href="${admin_url + "report/index/?area="+btoa(data['area'])+"&status=" + btoa(status)}">${row[1]}</a></td></tr>`;
                        sideTable += `<tr><td>${row[0] == "WIP" ? "In Progress": row[0]}</td><td><a data-area="<?php echo $area; ?>" data-status="${status}" class="filter-row" href="javascript://">${row[1]}</a></td></tr>`;
                    }
                    total += row[1];

                }
            })
            sideTable += `<tr><td class="total-footer">Total</td><td class="total-footer"><a data-area="<?php echo $area; ?>" class="filter-row" href="javascript://">${total}</a></td></tr></table>`;
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
        if (durations == '') {
            options = [];
            $('.custom-categories').selectpicker('val', options);
        }
        selectCategories(durations);
    });

    function selectCategories(duration) {
        if (duration.length) {
            $.post(admin_url + "issues/get_duration_issues", {
                "duration": duration,
                "area": "<?php echo base64_encode($area); ?>"
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
                } else if (res.success == false) {
                    options = [];
                    $('.custom-categories').selectpicker('val', options);
                    $('.custom-categories .filter-option-inner-inner').text('No Action Items Selected');
                }
                // let issues = [...res.issues];
                // let options = [];
                // if (issues.length > 0) {

                //     issues.map(issue => {
                //         issue.map(val => {
                //             options.push(val.id);
                //         });
                //     });
                // } else {
                //     options = [];
                // }
                // $('.custom-categories').selectpicker('val', options);
            }).fail((err) => console.log(err));

        }
    }

    function prepare_export_data() {
        let data = {
            'category': $('[name="category[]"]').val(),
            'duration': $('[name="duration[]"]').val(),
            'report_months': $('[name="report_months"]').val(),
            'report_from': $('[name="report-from"]').val(),
            'report_to': $('[name="report-to"]').val(),
        }
        return data;
    }

    $(document).on("click", "#summary_export", function() {

        let data = prepare_export_data();
        data["area"] = "<?php echo $area; ?>";
        $.ajax({
            url: admin_url + "dashboard/download_export",
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
    });

    $(document).on("click", "#export_pdf", function() {
        let data = prepare_export_data();
        data['area'] = "<?php echo $area ?>";
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

    $(document).on("click", ".gm-region td:first-child", function() {
        if (!$(this).closest('tr').hasClass('active')) {
            $(this).closest('tr').addClass('active');
            $(this).closest('tr').next('tr').addClass('row-show').removeClass('row-hide');
            $('.expand-btn').text('Collapse').addClass('collapse-btn').removeClass('expand-btn');
        } else {
            $(this).closest('tr').removeClass('active');
            $(this).closest('tr').next('tr').removeClass('row-show').addClass('row-hide');
            $('.collapse-btn').text('Expand').addClass('expand-btn').removeClass('collapse-btn');
        }
    })

    $(document).on('click', '.expand-btn', function(e) {
        $('.row-hide').addClass('row-show').removeClass('row-hide');
        $('.gm-region').addClass('active');
        $(this).text('Collapse').addClass('collapse-btn').removeClass('expand-btn');
    })
    $(document).on('click', '.collapse-btn', function(e) {
        $('.row-show').addClass('row-hide').removeClass('row-show');
        $('.gm-region').removeClass('active');
        $(this).text('Expand').addClass('expand-btn').removeClass('collapse-btn');
    })

    $(document).on("click", ".filter-row", function(e) {
        let data = {
            'category': $('[name="category[]"]').val(),
            'report_months': $('[name="report_months"]').val(),
            'report_from': $('[name="report-from"]').val(),
            'report_to': $('[name="report-to"]').val(),
            'duration': $('[name="duration[]"]').val(),
            'areaid': e.target.getAttribute('data-area'),
            'area': <?php echo $area ?>,
            'role': <?php echo $role ?>,
        }
        let status = e.target.getAttribute('data-status');
        let region = e.target.getAttribute('data-region');
        let subRegion = e.target.getAttribute('data-subregion');
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
        if (region)
            formContent += '<input type="hidden" name="region[]" value="' + region + '" />';

        if (subRegion)
            formContent += '<input type="hidden" name="subregion[]" value="' + subRegion + '" />';


        if (data.duration != 0) {
            formContent += '<input type="hidden" name="duration[]" value="' + data.duration + '" />';
        }
        if (data.category != 0) {
            formContent += '<input type="hidden" name="category[]" value="' + data.category + '" />';
        }

        formContent += '<input type="hidden" name="areaid[]" value="' + data.areaid + '" />' +
            '<input type="hidden" name="report_months" value="' + data.report_months + '" />' +
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
    })
</script>
</body>

</html>
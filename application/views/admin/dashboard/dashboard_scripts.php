<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
    $(function() {
        var ProjectsServerParams = {};

        $.each($('._hidden_inputs._filters input'), function() {
            ProjectsServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });

        initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array(5, 'asc'))); ?>);

        init_ajax_search('customer', '#clientid_copy_project.ajax-search');
    });
</script>

<script src="<?php echo base_url('assets/js/inview.js') ?>"></script>
<script>
    // var iosorandroiddevice=false;
    // var locationc="";
    // function showPosition() {
    // 		if(navigator.geolocation) {
    // 			navigator.geolocation.getCurrentPosition(function(position) {
    // 				var positionInfo =  position.coords.latitude + "," + position.coords.longitude ;
    //                 locationc=positionInfo;
    //             });
    // 		} else {
    // 			alert("Sorry, your browser does not support HTML5 geolocation.");
    // 		}
    // }
    // function iOS() {
    //     return [
    //         'iPad Simulator',
    //         'iPhone Simulator',
    //         'iPod Simulator',
    //         'iPad',
    //         'iPhone',
    //         'iPod'
    //     ].includes(navigator.platform)
    // iPad on iOS 13 detection
    // || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
    // }
    // function Android(){
    //     var ua = navigator.userAgent.toLowerCase();
    //     var isAndroid = ua.indexOf("android") > -1;
    //     return isAndroid;
    // }
    // function checkforIOSandAndroid(){
    //     var isIOS=iOS();
    //     var isAndroid=Android();
    //     if(isIOS == true || isAndroid == true){
    //         showPosition();
    //         return true;
    //     }else{
    //         return false;
    //     }   
    // }
    $(document).on('click', '.reject-btn', function(e) {
        if ($(window).width() > 769) {
            var posX = e.pageX - this.offsetLeft;
            var posY = e.pageY - this.offsetLeft;
            $('.modal.assign-modal').css({
                'top': posY,
                'left': posX,
                'right': 'auto',
                'bottom': 'auto',
                'position': 'absolute',
                'margin-top': '-170px',
                'margin-left': '-380px'
            })
            $('body').addClass('action-modal-open');
            $(".modal.assign-modal .modal-body").mCustomScrollbar("update");
        }

    })

    $(document).on('click', '.accept-btn', function(e) {
        if ($(window).width() > 769) {
            var posX = e.pageX - this.offsetLeft;
            var posY = e.pageY - this.offsetLeft;

            $('.modal.assign-modal').css({
                'top': posY,
                'left': posX,
                'right': 'auto',
                'bottom': 'auto',
                'position': 'absolute',
                'margin-top': '-170px',
                'margin-left': '-360px'
            })
            $('body').addClass('action-modal-open');
            $(".modal.assign-modal .modal-body").mCustomScrollbar("update");
        }

    })

    $(document).on('click', '.accept-btn, .reject-btn', function(e) {
        $('.dashboard-row').removeClass('active-cell');
        $(this).closest('.dashboard-row').addClass('active-cell');
    })
    $(document).on('click', '.modal.assign-modal .btn-cancel', function(e) {
        $('.dashboard-row').removeClass('active-cell');
        $('.modal.assign-modal').css('');
        $('body').removeClass('action-modal-open');

    })

    $(document).on('click', '.table-row-group .dashboard-row:last .reject-btn, .table-row-group .dashboard-row:last .accept-btn', function(e) {
        window.scrollTo(0, document.body.scrollHeight);
    })

    $(document).on('click', '.ticket-detail-modal .close', function(e) {
        $('.dashboard-row').removeClass('active-cell');

    })

    $(document).on('show.bs.modal', '#ticketDetailsPopup,#sidebarModal', function() {
        $('.lightgallery').lightGallery();
        // iosorandroiddevice=checkforIOSandAndroid();


    })



    $(document).on('click', '.updateMilestone', function() {
        console.clear();
        // alert(locationc);
        // let res2   =iosdevicevalidation();
        let res = validatetextarea();
        let res1 = validatefiletype();
        if (res > 0 || res1 > 0) {
            return false;
        }
        var form_data = new FormData();
        $('input[type="file"]').each(function(i, item) {

            var files = item.files;
            if (files.item(0) != null) {
                var file = files.item(0);
                // console.log(file)
                form_data.append("file[]", file, file.name);
            }
        });
        form_data.append("comment", $("#comment").val());
        form_data.append("csrf_token_name", "<?= $this->security->get_csrf_hash(); ?>");
        form_data.append("staff_id", <?= $GLOBALS['current_user']->staffid; ?>);
        form_data.append("project_id", $("#project_id").val());
        form_data.append("task_id", $("#task_id").val());
        form_data.append("last_milestone", $('.lastMilestone').val());
        // if(iosorandroiddevice == true){
        //     form_data.append("location_ios", locationc);
        // }else{
        //     form_data.append("location_ios","");
        // }
        $.ajax({
            url: admin_url + "tickets/update_milestone",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function(res) {
                res = JSON.parse(res);
                if (res.success) {
                    alert_float('success', res.message);
                    $('.table-projects').DataTable().ajax.reload();
                    $('#ticketDetailsPopup').modal('hide');
                    $('.action_items_data').trigger('click');
                    $('.upcoming_deadline_data').trigger('click');
                    loadWidgetData();
                } else {
                    alert_float('danger', res.message);
                }
            }
        });
    });
    const validatefiletype = () => {
        var check = 0;
        var validfileExtensions = ["jpg", "jpeg", "png", 'pdf', 'JPEG', 'JPG', 'PNG'];
        $('input[type^="file"]').each(function() {
            var file = $(this).val().split('.').pop();
            if (validfileExtensions.indexOf(file) == -1 && $(this).val() != "") {
                check++;
            }
        });
        return check;
    }
    // const iosdevicevalidation = ()=>{
    //     var count=0;
    //     if(iosorandroiddevice==true && locationc==""){
    //         alert_float('danger','Enable location services of your browser <a href="https://support.google.com/chrome/answer/142065?hl=en" target="_blank">click here for more info.</a>');
    //         count++;                    
    //     }
    //     return count;
    // }
    const validatetextarea = () => {
        var count = 0;
        if ($("#comment").val().trim().length == 0) {
            $('#alertspan').html("The Comment field cannot be empty.");
            count = count + 1
        } else if ($("#comment").val().trim().length > 500) {

            $('#alertspan').html("The Comment field cannot exceed 500 characters in length.");
            count = count + 1
        }
        //  else if (/^[a-zA-Z0-9\d\-.,:?\s\s]*$/.test($("#comment").val().trim()) == false) {
        //     $('#alertspan').html(
        //         "The Comment field may only contain alpha-numeric characters and spaces.");
        //     count = count + 1;
        // }
        else {
            $('#alertspan').html("");
        }
        return count;
    }

    $(document).ready(function() {
        $('#ticketDetailsPopup').scroll(function() {

            var datePicker = $('.datepicker1').datepicker();
            datePicker.datepicker('hide');
            $('.datepicker1').blur();
        })
        if ($(window).width() > 991) {

            $('#ticketDetailsPopup .dashboardModal').mCustomScrollbar({
                callbacks: {
                    onScrollStart: function() {
                        var datePicker = $('.datepicker1').datepicker();
                        datePicker.datepicker('hide');
                        $('.datepicker1').blur();

                    },
                    whileScrolling: function() {
                        var datePicker = $('.datepicker1').datepicker();
                        datePicker.datepicker('hide');
                        $('.datepicker1').blur();

                    }
                }
            });
        }
        if ($(window).width() <= 991) {

        }

        if ($('.dashboard').val() == 'dashboard') {
            loadWidgetData();
            loadActionData();
        }
    });

    $(document).on('click', '.action_items_data', function() {
        let nextPage = 1;
        $('#actionItems').html('');
        loadActionData(nextPage);
    });

    $(document).on('click', '#loadMoreAction', function() {
        let nextPage = isNaN(parseInt($('#pageno').val())) ? 2 : parseInt($('#pageno').val()) + 1;
        loadActionData(nextPage);
    });

    function loadActionData(nextPage) {
        let action_item_list = $('.action_item_list').val();
        $.ajax({
            type: 'POST',
            url: admin_url + 'dashboard/action_items',
            data: {
                pageno: nextPage
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.action_items_cnt == 0 && response.message == '') {
                        $('.no-action-item-data').removeClass('hide');
                        $('.action-items-data').addClass('hide');
                    } else if (response.message != '') {
                        $('.no-action-item-data').addClass('hide');
                        $('.action-items-data').removeClass('hide');

                        $('#actionItems').append(response.message);
                    }

                    $('#pageno').val(nextPage);
                    let listCnt = (nextPage > 0) ? nextPage * action_item_list : action_item_list;
                    if (response.action_items_cnt > listCnt && response.message != '') {
                        // if(response.action_items_cnt > action_item_list && response.message != ''){
                        // if(response.action_items_cnt >= 1 && response.message != ''){
                        $('.load-btn-action-item').removeClass('hide');
                    } else {
                        $('.load-btn-action-item').addClass('hide');
                    }
                } else {
                    $("#loadMoreAction").hide();
                }
                loadWidgetData();
            }
        });
    }

    $(document).on('click', '.upcoming_deadline_data', function() {
        let nextPage = 1;
        $('#upcomingItems').html('');
        loadUpcomingDeadlineData(nextPage);
    });

    $(document).on('click', '#upcomingDeadlineLoader', function() {
        let nextPage = isNaN(parseInt($('#nextpageno').val())) ? 2 : parseInt($('#nextpageno').val()) + 1;
        loadUpcomingDeadlineData(nextPage);
    });

    function loadUpcomingDeadlineData(nextPage) {
        let action_item_list = $('.action_item_list').val();
        $.ajax({
            type: 'POST',
            url: admin_url + 'dashboard/upcoming_deadline',
            data: {
                nextpageno: nextPage
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.upcoming_deadline_data_cnt == 0 && response.message == '') {
                        $('.no-upcoming-deadlines').removeClass('hide');
                        $('.upcoming-deadlines-data').addClass('hide');
                    } else if (response.message != '') {
                        $('.no-upcoming-deadlines').addClass('hide');
                        $('.upcoming-deadlines-data').removeClass('hide');
                        $('#upcomingItems').append(response.message);
                    }

                    $('#nextpageno').val(nextPage);
                    let listCnt = (nextPage > 0) ? nextPage * action_item_list : action_item_list;
                    if (response.upcoming_deadline_data_cnt > listCnt && response.message != '') {
                        // if(response.upcoming_deadline_data_cnt > action_item_list && response.message != ''){
                        // if(response.upcoming_deadline_data_cnt >= 1 && response.message != ''){
                        $('.load-btn-upcoming_deadline').removeClass('hide');
                    } else {
                        $('.load-btn-upcoming_deadline').addClass('hide');
                    }
                } else {
                    $("#upcomingDeadlineLoader").hide();
                }
                loadWidgetData();
            }
        });
    }

    // $(document).on('click', '#recentlyClosedLoader', function() {
    //     let nextPage = isNaN(parseInt($('#nextpageno').val())) ? 2 : parseInt($('#nextpageno').val()) + 1;
    //     $.ajax({
    //         type: 'POST',
    //         url: admin_url + 'dashboard/recently_closed',
    //         data: {
    //             nextpageno: nextPage
    //         },
    //         success: function(response) {
    //             if (response != '') {
    //                 $('#recentlyClosedItems').append(response);
    //                 $('#nextpageno').val(nextPage);
    //             } else {
    //                 $("#recentlyClosedLoader").hide();
    //             }
    //         }
    //     });
    // });

    $(document).on('click', '.recently_closed_data', function() {
        let nextPage = 1;
        $('#recentlyClosedItems').html('');
        loadRecentlyClosedData(nextPage);
    });

    $(document).on('click', '#recentlyClosedLoader', function() {
        let nextPage = parseInt($('#nextpageno').val()) + 1;
        loadRecentlyClosedData(nextPage);
    });

    function loadRecentlyClosedData(nextPage) {
        let action_item_list = $('.action_item_list').val();
        $.ajax({
            type: 'POST',
            url: admin_url + 'dashboard/recently_closed',
            data: {
                nextpageno: nextPage
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.recently_closed_cnt == 0 && response.message == '') {
                        $('.no-recently-closed').removeClass('hide');
                        $('.recently-closed-data').addClass('hide');
                    } else if (response.message != '') {
                        $('.no-recently-closed').addClass('hide');
                        $('.recently-closed-data').removeClass('hide');
                        $('#recentlyClosedItems').append(response.message);
                    }

                    $('#nextpageno').val(nextPage);
                    let listCnt = (nextPage > 0) ? nextPage * action_item_list : action_item_list;
                    if (response.recently_closed_cnt > listCnt && response.message != '') {
                        // if(response.recently_closed_cnt > action_item_list && response.message != ''){
                        // if(response.upcoming_deadline_data_cnt >= 1 && response.message != ''){
                        $('.load-btn-recently-closed').removeClass('hide');
                    } else {
                        $('.load-btn-recently-closed').addClass('hide');
                    }
                } else {
                    $("#recentlyClosedLoader").hide();
                }
                loadWidgetData();
            }
        });
    }

    $(document).on('click', '.evidence_img', function() {
        let projectId = $(this).data('project_id');
        let imgType = $(this).data('img_type');
        $('.evidence-data').html('');
        $.ajax({
            type: 'GET',
            url: admin_url + 'dashboard/evidence_image',
            data: {
                projectId: projectId,
                imgType: imgType
            },
            success: function(response) {
                if (response != '') {
                    $('.evidence-data').html(response);
                } else {
                    $(".evidence-data").html('<p>No Evidence Found</p>');
                }
                $('#sidebarModal').modal('show');
            }
        });
    });

    $(document).on('click', '.ticket_details', function() {
        $(this).closest('.dashboard-row').addClass('active-cell');

        let projectId = $(this).data('project_id');
        let role = $(this).data('role');
        let status = $(this).data('status');
        let report = $(this).data('report');
        $(".panel_s").waitMe("show");
        $('.ticket-data').html('');
        $.ajax({
            type: 'GET',
            url: admin_url + 'tickets/ticket_details',
            data: {
                projectId: projectId
            },
            success: function(response) {
                //console.log("Awnish Page", response);
                var check_login_index = response.indexOf('<body class="login_admin" >');
                if (check_login_index > 0) {
                    $('#ticketDetailsPopup').modal('hide');
                    window.location = '<?php echo admin_url('authentication'); ?>';
                    // return false;
                }
                if (response != '') {
                    $('.ticket-data').html(response);
                } else {
                    $(".ticket-data").html('<p>No Data Found</p>');
                }
                if (role == 'ata' && status == 1 && report != 'report') {
                    $('.project_' + projectId).remove();
                    $('.action_items_data').trigger('click');
                }
                // loadWidgetData();
                if (role == 'ar' && status == 5) {
                    updateDeadlineDatePicker();
                    $('.ar-reassignment-action').selectpicker('refresh');
                }
                if (role == 'ar' && status == 3) {
                    $('.reopenReason').focus();
                }
                if (status == 6 && report != 'report') {
                    $('.action_items_data').trigger('click');
                    $('.upcoming_deadline_data').trigger('click');
                }
                //Highlight background ticket

                loadTicketHistory(projectId);
                $(".panel_s").waitMe("hide");
            }
        });
        $('#ticketDetailsPopup').modal('show');

    });

    function loadTicketHistory(projectId) {
        $.ajax({
            type: 'POST',
            url: admin_url + 'projects/get_project_activity',
            data: {
                projectId: projectId
            },
            success: function(response) {
                if (response != '') {
                    $('#ticket-history').html(response);
                } else {
                    $("#ticket-history").html('<p>No Data Found</p>');
                }
            }
        });
    }

    //Load widget Data
    function loadWidgetData() {
        $.ajax({
            url: admin_url + 'dashboard/dashboard_widget_data',
            type: "GET",
            cache: false,
            success: function(response) {
                let data = $.parseJSON(response);
                $('.escalated_total').html(data.escalated);
                $('.new_total').html(data.new);
                $('.ongoing_total').html(data.ongoing);
                $('.closed_total').html(data.closed);
                $('.total_act').html(data.total_activity);
                $('.action_items_cnt').html(data.action_items);
                $('.upcoming_deadline_cnt').html(data.upcoming_deadline);
                $('.recently_closed_cnt').html(data.recently_closed);

            }
        });
    }

    $(".dlist").find("li a").click(function() {
        $('.active_li').removeClass('active_li');
        $(this).addClass('active_li');
    });

    $(document).on('click', '.accept-btn-popup', function() {
        let projectId = $(this).data('projectid');
        $('#assignTicketPopup').find('.acceptProjectId').val(projectId);
        $('#assignTicketPopup').modal('show');
    });

    $(document).on('click', '.assignTicket', function() {
        let projectId = $('.acceptProjectId').val();
        let staffId = $('.dlist').find("li a.active_li").data('staffid');
        let staffName = $('.dlist').find("li a.active_li").text();
        let ticketDetail = $(this).data('ticketdetail');
        acceptTicket(projectId, staffId, staffName, ticketDetail);
    });

    $(document).on('click', '.assignTDTicket', function() {
        let projectId = $('.ticketPopupId').val();
        let staffId = $('.assignTDList').find("option:selected").val();
        let staffName = $.trim($('.assignTDList').find("option:selected").text());
        let ticketDetail = $(this).data('ticketdetail');
        acceptTicket(projectId, staffId, staffName, ticketDetail);
    });

    function acceptTicket(projectId, staffId, staffName, ticketDetail) {
        if (typeof staffId === "undefined" || staffId == '') {
            alert_float('danger', 'Please select user to assign project.');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: admin_url + 'tickets/assign_ticket',
            cache: false,
            data: {
                staffId: staffId,
                staffName: staffName,
                projectId: projectId
            },
            success: function(response) {
                let data = $.parseJSON(response);
                if (data.success) {
                    alert_float('success', data.message);
                    if (ticketDetail == 'ticketDetail') {
                        $('#ticketDetailsPopup').modal('hide');
                    } else {
                        $('#assignTicketPopup').modal('hide');
                    }
                    $('.project_' + projectId).remove();
                    $('.action_items_data').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('#ticketDetailsPopup').modal('hide');
                    $('#assignTicketPopup').modal('hide');
                }
            }
        });
    }

    $(document).on('click', '.reject-btn-popup', function() {
        let projectId = $(this).data('projectid');
        $('#rejectTicketPopup').find('.rejectProjectId').val(projectId);
        $('#rejectTicketPopup').modal('show');
    });

    $(document).on('click', '.rejectTicketList', function() {
        let exceptionId = $(this).data('exceptionid');
        let exceptionName = $.trim($(this).text());
        if (exceptionName.toLowerCase() == 'others') {
            $('.otherReason').removeClass('hide');
            $(".otherException").focus();
        } else {
            $('.otherReason').addClass('hide');
        }
    });

    $(document).on('click', '.rejectTicket', function() {
        let exceptionId = $('.dlist').find("li a.active_li").data('exceptionid');
        let projectId = $('.rejectProjectId').val();
        let exceptionName = $.trim($('.dlist').find("li a.active_li").text());
        let ticketDetail = $(this).data('ticketdetail');

        if (typeof exceptionId === "undefined" || exceptionId == '') {
            alert_float('danger', 'Please give reason to reject this project.');
            return false;
        }

        if (exceptionName.toLowerCase() == 'others') {
            let otherReason = $.trim($('#ticket_reject_form').find('.otherException').val());
            if (otherReason == '') {
                $('#ticket_reject_form').find('.otherException').css('border-color', 'red');
                alert_float('danger', 'Please give reason to reject this project.');
                return false;
            } else {
                exceptionName = otherReason;
            }
        }

        rejectTicket(projectId, exceptionId, exceptionName, ticketDetail);
    });

    $(document).on('change', '.rejectTDList', function() {
        let exceptionId = $(this).find("option:selected").val();
        let exceptionName = $.trim($(this).find("option:selected").text());
        if (exceptionName.toLowerCase() == 'others') {
            $('.otherTDReason').removeClass('hide');
        } else {
            $('.otherTDReason').addClass('hide');
        }
    });

    $(document).on('click', '.rejectTDTicket', function() {
        let projectId = $('.ticketPopupId').val();
        let exceptionId = $('.rejectTDList').find("option:selected").val();
        let exceptionName = $.trim($('.rejectTDList').find("option:selected").text());
        let ticketDetail = $(this).data('ticketdetail');

        if (typeof exceptionId === "undefined" || exceptionId == '') {
            alert_float('danger', 'Please give reason to reject this project.');
            return false;
        }

        if (exceptionName.toLowerCase() == 'others') {
            let otherReason = $.trim($('.otherTDException').val());
            if (otherReason == '') {
                alert_float('danger', 'Please give reason to reject this project.');
                return false;
            } else {
                exceptionName = otherReason;
            }
        }

        rejectTicket(projectId, exceptionId, exceptionName, ticketDetail);
    });

    function rejectTicket(projectId, exceptionId, exceptionName, ticketDetail) {
        if (typeof exceptionId === "undefined" || exceptionId == '') {
            alert_float('danger', 'Please give reason to reject this project.');
            return false;
        }

        if (projectId == '') {
            alert_float('danger', 'Something went wrong. Please try again.');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: admin_url + 'tickets/reject_ticket',
            cache: false,
            data: {
                projectId: projectId,
                exceptionId: exceptionId,
                exception: exceptionName
            },
            success: function(response) {
                let data = $.parseJSON(response);
                if (data.success) {
                    alert_float('success', data.message);
                    $('#ticketDetailsPopup').modal('hide');
                    $('#rejectTicketPopup').modal('hide');
                    $('.project_' + projectId).remove();
                    $('.action_items_data').trigger('click');
                } else {
                    alert_float('danger', data.message);
                }
                // if (ticketDetail != 'ticketDetail') {
                //     if (exception == 'Others') {
                //         $('#rejectTicketPopup').modal('hide');
                //     }
                // } else {
                //     $('#ticketDetailsPopup').modal('hide');
                // }
                // loadWidgetData();
            }
        });
    }

    $(document).on('click', '.submitException', function() {
        let exceptionId = $('.exceptionId').val();
        let projectId = $('.ticketPopupId').val();
        let exceptionName = $('.otherException').val();
        let ticketDetail = $(this).data('ticketdetail');

        if ($.trim(exceptionName) != '') {
            rejectTicket(projectId, exceptionId, exceptionName, ticketDetail);
        } else {
            $('.otherException').css('border-color', 'red');
            alert_float('danger', 'Please give reason to reject this project.');
            // alert('Please write Rejection reason.');
            return false;
        }
    });

    $(document).on('click', '.reopen-ticket', function() {
        $('.reopen-ticket').attr('disabled', true);
        $('.reopenTicketComment').removeClass('hide');
    });

    $(document).on('click', '.reopenTicket', function() {
        let projectId = $('.ticketPopupId').val();
        let reopenReason = $('.reopenReason').val();
        let ticketDetail = $(this).data('ticketdetail');

        if ($.trim(reopenReason) != '') {
            if (confirm('Are you sure you want to reopen this project?')) {
                $('.reopenTicket').attr('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: admin_url + 'tickets/reopen_ticket',
                    cache: false,
                    data: {
                        projectId: projectId,
                        reopenReason: reopenReason
                    },
                    success: function(response) {
                        let data = $.parseJSON(response);
                        if (data.success) {
                            alert_float('success', data.message);
                        } else {
                            alert_float('danger', data.message);
                        }
                        if (ticketDetail != 'ticketDetail') {
                            $('#reopenTicketPopup').modal('hide');
                        } else {
                            $('#ticketDetailsPopup').modal('hide');
                        }
                        $('.project_' + projectId).remove();
                        $('.action_items_data').trigger('click');
                        $('.recently_closed_data').trigger('click');
                        $('.upcoming_deadline_data').trigger('click');
                    }
                });
            }
        } else {
            $('.reopenReason').css('border-color', 'red');
            alert_float('danger', 'Please give reason for reopening this project.');
            return false;
        }
    });

    $(document).on('click', '.close-ticket', function() {
        let projectId = $('.ticketPopupId').val();
        let ticketDetail = $(this).data('ticketdetail');

        $.ajax({
            type: 'POST',
            url: admin_url + 'tickets/close_ticket',
            cache: false,
            data: {
                projectId: projectId,
            },
            success: function(response) {
                let data = $.parseJSON(response);
                if (data.success) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                }
                if (ticketDetail != 'ticketDetail') {
                    $('#reopenTicketPopup').modal('hide');
                } else {
                    $('#ticketDetailsPopup').modal('hide');
                }
                $('.project_' + projectId).remove();
                $('.action_items_data').trigger('click');
            }
        });

    });

    $(document).on('click', '.evidence_loc', function() {
        let projectId = $(this).data('project_id');
        let fileId = $(this).data('file_id');
        $.ajax({
            type: 'GET',
            url: admin_url + 'dashboard/evidence_location',
            data: {
                projectId: projectId,
                fileId: fileId
            },
            success: function(response) {
                if (response != '') {
                    $('.evidence-data').html(response);
                } else {
                    $(".evidence-data").html('<p>No Location Found</p>');
                }
                $('#ticketDetailsPopup').modal('hide');
                $('#sidebarModal').modal('show');
            }
        });
    });

    $(document).on('change', '.ar-action', function() {
        let val = $(this).val();

        $('.ar-close-ticket').addClass('hide');
        $('.ar-extend-deadline').addClass('hide');
        $('.ar-sub-ticket').addClass('hide');

        if (val == 1) {
            $('.ar-close-ticket').removeClass('hide');
        } else if (val == 2) {
            setReassignmentDueDate()
            $('.ar-extend-deadline').removeClass('hide');
            $('.ar-reassignment-action').selectpicker('refresh');
        } else if (val == 3) {
            if (confirm('Are you sure you want to create multiple projects?')) {
                setSubTicketDueDate();
                $('.ar-sub-ticket').removeClass('hide');
                $('.ar-subtask-action').selectpicker('refresh');
                $('.subtaskfiled').selectpicker('refresh');

            } else {
                $('.ar-action').val('');
            }
        }
    });

    function updateDeadlineDatePicker() {
        $(".datepicker1").datepicker({
            // dateFormat: "yy-mm-dd"
            dateFormat: "dd-M-yy"
        });
    }

    function setReassignmentDueDate() {
        let milestoneCnt = $('.milestone-cnt').val();
        for (let i = 1; i <= milestoneCnt; i++) {
            let day = parseInt($('.reassignment_days_' + i).val());
            if (i == 1) {
                let dueDate = new Date($('.reassignment_start_date_' + i).text());
                dueDate.setDate(dueDate.getDate() + day)
                $('#reassignment_due_date_' + i).datepicker().datepicker('setDate', dueDate);
            } else {
                let j = i - 1;
                let lastDueDate = $('#reassignment_due_date_' + j).datepicker('getDate');
                let newStartDate = new Date(lastDueDate);
                newStartDate.setDate(lastDueDate.getDate() + 1);
                newStartDate = $.datepicker.formatDate('dd-M-yy', newStartDate);
                $('.reassignment_start_date_' + i).text(newStartDate);

                let dueDate = new Date($('.reassignment_start_date_' + i).text());
                dueDate.setDate(dueDate.getDate() + day)
                $('#reassignment_due_date_' + i).datepicker().datepicker('setDate', dueDate);
            }

        }
    }

    function setSubTicketDueDate() {
        let milestoneCnt = $('.milestone-cnt').val();
        let day = 0;
        for (let i = 1; i <= milestoneCnt; i++) {
            day = day + parseInt($('.subtask_days_' + i).val());
            let dueDate = new Date($('.subtask_start_date_' + i).text());
            dueDate.setDate(dueDate.getDate() + day)
            $('#subtask_due_date_' + i).datepicker().datepicker('setDate', dueDate);
        }
    }

    $(document).on('change', 'select[name="ar-subtask-action"]', function() {
        // let userID = $(this).val();
        let userID = $('select[name="ar-subtask-action"]').val();
        let userName = $('select[name="ar-subtask-action"]').find('option:selected').text();
        let milestoneCnt = $('.milestone-cnt').val();
        for (let i = 1; i <= milestoneCnt; i++) {
            // $(".subtask_at_" + i).val(userID);
            $('select[name="subtask_at_' + i + '"]').val(userID);
            $(".subtask_at_" + i).find('.filter-option-inner-inner').html('');
            $(".subtask_at_" + i).find('.filter-option-inner-inner').html(userName);
        }
    });

    $(document).on('change', '.reassignment_date', function() {
        let milestoneCnt = $('.milestone-cnt').val();
        let milestone = $(this).data('milestone');
        let j = milestone + 1;

        for (j; j <= milestoneCnt; j++) {
            let i = j - 1;
            let lastDueDate = $('#reassignment_due_date_' + i).datepicker('getDate');
            if (lastDueDate !== null) {
                let day = parseInt($('.reassignment_days_' + j).val());
                let newStartDate = new Date(lastDueDate);
                newStartDate.setDate(lastDueDate.getDate() + 1);
                newStartDate = $.datepicker.formatDate('dd-M-yy', newStartDate);
                $('.reassignment_start_date_' + j).text(newStartDate);

                let dueDate = new Date($('.reassignment_start_date_' + j).text());
                dueDate.setDate(dueDate.getDate() + day)
                $('#reassignment_due_date_' + j).datepicker().datepicker('setDate', dueDate);
            }
        }

    });

    $(document).on('click', '#extend-deadline-submit', function() {
        let milestoneCnt = $('.milestone-cnt').val();
        // let assignAt = $('.ar-reassignment-action').val();
        let assignAt = $('[name="ar-reassignment-action"]').val();
        let error = false;
        let dateErr = [];
        let extendDeadlineData = [];
        for (let i = 1; i <= milestoneCnt; i++) {
            // let task_days = parseInt($('.reassignment_days_'+ i).val());
            let task_id = parseInt($('.reassignment_milestoneid_' + i).val());
            // let startDate = $.datepicker.formatDate('dd-mm-yy', new Date($('.reassignment_start_date_'+ i).text()));
            // let dueDate = $.datepicker.formatDate('dd-mm-yy', $('#reassignment_due_date_' + i).datepicker('getDate'));
            let startDate = new Date($('.reassignment_start_date_' + i).text());
            let dueDate = $('#reassignment_due_date_' + i).datepicker('getDate');

            // let reminderOne_days = parseInt($('.reassignment_reminderone_days_'+ i).val());
            // let reminderTwo_days = parseInt($('.reassignment_remindertwo_days_'+ i).val());
            extendDeadlineData.push({
                task_id: task_id,
                startDate: $.datepicker.formatDate('dd-M-yy', startDate),
                dueDate: $.datepicker.formatDate('dd-M-yy', dueDate),
            });

            if (dueDate == '' || dueDate < startDate) {
                error = true;
                dateErr.push(i);
            }
        }
        // console.log(extendDeadlineData);
        // alert(assignAt);
        // return false;
        if (assignAt == '') {
            $('.ar-reassignment-action').css('border-color', 'red');
            alert_float('danger', 'Please select Project Leader');
            return false;
        }
        if (error) {
            $.each(dateErr, function(index, value) {
                $('#reassignment_due_date_' + value).css('border-color', 'red');
            });
            alert_float('danger', 'Due Date should be greater than Start Date');
            return false;
        } else {
            //Extend Deadline and Re-assignment
            $('.datepicker, .ar-reassignment-action').css('border-color', '');

            let projectId = $('.ticketPopupId').val();
            $.ajax({
                type: 'POST',
                url: admin_url + 'tickets/extend_deadline',
                cache: false,
                data: {
                    projectId: projectId,
                    assignAt: assignAt,
                    extendDeadlineData: extendDeadlineData,
                    milestoneCnt: milestoneCnt
                },
                success: function(response) {
                    let data = $.parseJSON(response);
                    if (data.success) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                    $('#ticketDetailsPopup').modal('hide');
                    $('.project_' + projectId).remove();
                    // loadWidgetData();
                    $('.action_items_data').trigger('click');
                }
            });
        }
    });

    $(document).on('click', '.arCloseTicket', function() {
        let projectId = $('.ticketPopupId').val();
        let closeReason = $.trim($('.closeReason').val());

        if (closeReason == '') {
            $('.closeReason').css('border-color', 'red');
            alert_float('danger', 'Please give reason to close this project.');
            return false;
        } else {
            $('.closeReason').css('border-color', '');
        }
        $('.arCloseTicket').attr('disabled', true);

        $.ajax({
            type: 'POST',
            url: admin_url + 'tickets/close_ticket',
            cache: false,
            data: {
                projectId: projectId,
                closeReason: closeReason
            },
            success: function(response) {
                let data = $.parseJSON(response);
                if (data.success) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                }
                $('#ticketDetailsPopup').modal('hide');
                $('.project_' + projectId).remove();
                $('.action_items_data').trigger('click');
            }
        });
    });

    $(document).on('click', '#subticket-submit', function() {
        let milestoneCnt = $('.milestone-cnt').val();
        // let assignAt = $('.ar-subtask-action').val();
        let assignAt = $('select[name="ar-subtask-action"').val();
        let error = false;
        let dateErr = [];
        let extendDeadlineData = [];
        for (let i = 1; i <= milestoneCnt; i++) {
            // let task_days = parseInt($('.subtask_days_'+ i).val());
            let task_id = parseInt($('.subtask_milestoneid_' + i).val());
            let startDate = $.datepicker.formatDate('yy-mm-dd', new Date($('.subtask_start_date_' + i).text()));
            let dueDate = $.datepicker.formatDate('yy-mm-dd', $('#subtask_due_date_' + i).datepicker('getDate'));

            // let startDate = new Date($('.subtask_start_date_'+ i).text());
            // let dueDate = $('#subtask_due_date_' + i).datepicker('getDate');

            // let reminderOne_days = parseInt($('.subtask_reminderone_days_'+ i).val());
            // let reminderTwo_days = parseInt($('.subtask_remindertwo_days_'+ i).val());
            let subtask_name = $.trim($('.subtask_name_' + i).text());

            // let assignSubAt = $('.subtask_at_' + i).val();
            let assignSubAt = $('[name="subtask_at_' + i + '"]').val();

            if (assignSubAt == '') {
                $('.subtask_at_' + i).css('border-color', 'red');
                alert_float('danger', 'Please select Project Leader');
                return false;
            } else {
                $('.subtask_at_' + i).css('border-color', '');
            }

            extendDeadlineData.push({
                task_id: task_id,
                startDate: startDate,
                dueDate: dueDate,
                // startDate : $.datepicker.formatDate('dd-M-yy', startDate), 
                // dueDate :  $.datepicker.formatDate('dd-M-yy', dueDate),
                // task_days : task_days,
                // reminderOne_days :reminderOne_days,
                // reminderTwo_days : reminderTwo_days,
                assignSubAt: assignSubAt,
                subtask_name: subtask_name
            });

            if (dueDate == '' || dueDate < startDate) {
                error = true;
                dateErr.push(i);
            }
        }
        // console.log(extendDeadlineData);
        if (assignAt == '') {
            $('.ar-subtask-action').css('border-color', 'red');
            alert_float('danger', 'Please select Project Leader');
            return false;
        }
        if (error) {
            $.each(dateErr, function(index, value) {
                $('#subtask_due_date_' + value).css('border-color', 'red');
            });
            alert_float('danger', 'Due Date should be greater than Start Date');
            return false;
        } else {
            $('.datepicker,.ar-subtask-action').css('border-color', '');
            let projectId = $('.ticketPopupId').val();
            $.ajax({
                type: 'POST',
                url: admin_url + 'tickets/subtickets',
                cache: false,
                data: {
                    projectId: projectId,
                    assignAt: assignAt,
                    extendDeadlineData: extendDeadlineData,
                    milestoneCnt: milestoneCnt
                },
                success: function(response) {
                    let data = $.parseJSON(response);
                    if (data.success) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                    $('#ticketDetailsPopup').modal('hide');
                    $('.project_' + projectId).remove();
                    $('.action_items_data').trigger('click');
                }
            });
        }
    });

    // function printDiv() {
    //     var divToPrint = document.getElementById('ticketDetailsPopup');
    //     var newWin = window.open('', 'Print-Window');
    //     newWin.document.write('<html><body onload="window.print()"><link rel="stylesheet" type="text/css" id="reset-css" href="< ?= base_url('/assets/css/reset.min.css'); ?>"><link rel="stylesheet" type="text/css" id="vendor-css" href="< ?= base_url('/assets/builds/vendor-admin.css?v=2.4.4'); ?>"><link rel="stylesheet" type="text/css" id="waitme-css" href="< ?= base_url('/assets/plugins/waitMe/waitMe.css?v=2.4.4'); ?>"><link rel="stylesheet" type="text/css" id="app-css" href="< ?= base_url('/assets/css/style.css?v=1595865070'); ?>"><link rel="stylesheet" type="text/css" id="custom-css" href="< ?= base_url('/assets/css/custom.css?v=1595865070'); ?>"><header style="text-align:center; margin-top: 20px"><img src="< ?php echo base_url('assets/images/header-logo.jpg') ?>" alt="" style="margin-right:10px"><img src="< ?php echo base_url('assets/images/dpcc-logo.jpg') ?>" alt=""><h3 style="margin-bottom: 0; padding-bottom:0">Air Pollution Dashboard</h3></header>' + divToPrint.innerHTML + '</body></html>');

    //     newWin.document.close();
    //     setTimeout(function() {
    //         newWin.close();
    //     }, 1000);
    // }

    $(document).on('click', '#reassign-ticket', function() {
        // let assignAt = $('.ar-reassignment-action').val();
        let assignAt = $('[name="ar-reassignment-action"]').val();
        if (assignAt == '') {
            $('.ar-reassignment-action').css('border-color', 'red');
            alert_float('danger', 'Please select Project Leader');
            return false;
        }

        let projectId = $('.ticketPopupId').val();
        $.ajax({
            type: 'POST',
            url: admin_url + 'tickets/reassignment',
            cache: false,
            data: {
                projectId: projectId,
                assignAt: assignAt
            },
            success: function(response) {
                let data = $.parseJSON(response);
                if (data.success) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                }
                $('#ticketDetailsPopup').modal('hide');
                $('.project_' + projectId).remove();
                $('.action_items_data').trigger('click');
            }
        });
    });

    function reportFilter(status) {
        let csrf_token_name = '';
        if (typeof csrfData !== "undefined") {
            csrf_token_name = csrfData["hash"];
        }
        let userRole = $('.userRole').val();
        let userFilter = '';
        if (userRole == 'at' || userRole == 'ata') {
            let region = $('.uregion').val();
            let sub_region = $('.usub_region').val();
            let staffid = $('.ustaffid').val();

            userFilter = '<input type="hidden" name="region[]" value="' + region + '" />' +
                '<input type="hidden" name="subregion[]" value="' + sub_region + '" />' +
                '<input type="hidden" name="action_taker[]" value="' + staffid + '" />';
        } else if (userRole == 'ar') {
            let staffid = $('.ustaffid').val();

            userFilter = '<input type="hidden" name="action_reviewer[]" value="' + staffid + '" />';
        }
        // $.redirect('/admin/report/index', {'ticket[]': status});
        // onclick="window.open('/admin/report/index?ticket[]=7','_self')"
        let form = $('<form action="<?php echo admin_url('report/index') ?>" method="post">' +
            userFilter +
            '<input type="hidden" name="ticket[]" value="' + status + '" />' +
            '<input type="hidden" name="csrf_token_name" value="' + csrf_token_name + '" />' +
            '</form>');
        $('body').append(form);
        $(form).submit();
    }

    $(document).on('click', '#ticket_detail_pdf', function() {
        let projectId = $('.ticketPopupId').val();
        $.ajax({
            // type: 'POST',
            url: admin_url + "tickets/ticket_detail_pdf",
            data: {
                projectId: projectId,
            },
            complete: function() {
                //alert(this.url);
            },
            success: function() {
                // window.location.replace(this.url);
                window.open(this.url, '_blank');
            }
        });
        return false;
    });
</script>
<script src="<?php echo base_url('assets/js/rocket-loader.min.js') ?>"></script>
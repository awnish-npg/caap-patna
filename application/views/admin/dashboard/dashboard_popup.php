<?php defined('BASEPATH') or exit('No direct script access allowed');

$userId = $GLOBALS['current_user']->staffid;
$userName = $GLOBALS['current_user']->full_name;
$assistantList = '<li><a href="javascript:void(0);" class="" data-staffid="' . $userId . '">' . $userName . ' (Self)</a></li>';
if (!empty($assistantDetails))
    foreach ($assistantDetails as $assistant) {
        $name = $assistant['full_name'].'('.$assistant['organisation'].')';
        $assistantList .= '<li><a href="javascript:void(0);" class="" data-staffid="' . $assistant['staffid'] . '">' . $name . '</a></li>';
    }

$exceptionList = '';
if (!empty($exceptionDetails))
    foreach ($exceptionDetails as $exception) {
        $exceptionList .= '<li><a href="javascript:void(0);" class="rejectTicketList" data-exceptionid="' . $exception['id'] . '">' . $exception['name'] . '</a></li>';
    }
?>

<!-- Image Evidence Popup -->
<div class="modal sidebarModal fade ticket-detail-modal w70P" id="sidebarModal">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form autocomplete="off" action="javascript:void(0)" id="evidence_popup_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 evidence-data"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>

<!-- Ticket Details Popup -->
<div class="modal  sidebarModal fade ticket-detail-modal w70P mB0" id="ticketDetailsPopup">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
    <div class="dashboardModal mCustomScrollbar" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form autocomplete="off" action="javascript:void(0)" id="ticket_detail_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-header"></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 ticket-data"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>

<!-- Assign Ticket Modal -->
<div class="modal fade assign-modal" id="assignTicketPopup" style="">
    <div class="dashboardModal" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 350px;">
            <form autocomplete="off" action="javascript:void(0)" id="ticket_assign_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-header p-0">
                        <div class="panel panel-default sub-ticket-panel mB0 border-0">
                            <div class="panel-heading accept">
                                Assign To
                            </div>
                        </div>

                    </div>
                    <div class="modal-body mCustomScrollbar">
                        <input type="hidden" name="acceptProjectId" class="acceptProjectId" value="" />
                        <ul class="dlist">
                            <?php echo $assistantList; ?>
                        </ul>
                    </div>
                            <div class="modal-footer">
                            <div class="btn-container">
                                <button type="button" class="btn btn-custom assignTicket">Assign</button>
                                <button type="submit" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                            </div>
                    </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>

<!-- Reject Ticket Modal -->
<div class="modal fade assign-modal" id="rejectTicketPopup" style="">
    <div class="dashboardModal" id="" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 350px;">
            <form autocomplete="off" action="javascript:void(0)" id="ticket_reject_form" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-content">
                    <div class="modal-header p-0">
                        <div class="panel panel-default sub-ticket-panel mB0 border-0">
                            <div class="panel-heading reject">
                            <?php if($GLOBALS['current_user']->role_slug_url =='at') { 
                                    echo "Refer Project"; 
                                }else{
                                    echo "Reject Project";
                                }?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body mCustomScrollbar">
                        <input type="hidden" name="rejectProjectId" class="rejectProjectId" value="" />
                        <ul class="dlist">
                            <?php echo $exceptionList; ?>
                        </ul>
                        <div class="form-group otherReason hide p15 mB0">
                            <textarea name="otherException" class="form-textarea otherException"></textarea>
                            <label title="Reason" data-title="Reason"></label>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <div class="btn-container">
                            <button type="button" class="btn-custom rejectTicket">
                            <?php if($GLOBALS['current_user']->role_slug_url =='at') { 
                                    echo "Refer"; 
                                }else{
                                    echo "Reject";
                                }?>
                            </button>
                            <button type="submit" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                        </div>
                        </div>
                </div><!-- /.modal-content -->
            </form>
        </div><!-- /.modal-dialog -->
    </div>
</div>
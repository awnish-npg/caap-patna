<table class="table dt-table scroll-responsive table-reportmng table-fixed" data-order-col="7" id="report-mg">
							<thead>
                            <tr> 
								<th width= "80"><?php echo _l('Project ID'); ?></th>
                                <th width= "130"><?php echo _l('Action Items'); ?></th>
                                <th width= "120"><?php echo _l('Status'); ?></th>       
                                <th width= "130"><?php echo _l('Assigned To'); ?></th>                              
                                <th width= "80"><?php echo _l('Contact'); ?></th>
                                <th width= "80"><?php echo _l('Due Date'); ?></th>
                                <th width= "100"><?php echo _l('Municipal Zone'); ?></th>
                                <th width= "100"><?php echo _l('Landmark'); ?></th>
								<th width= "120"><?php echo _l('City/ Corporation '); ?></th>
                                <th width= "80"><?php echo _l('State'); ?></th>
                                <th width= "80" id="raised"><?php echo _l('Raised On'); ?></th>
                                <th width= "210"><?php echo _l('Raised Comment'); ?></th>  
                                <th width= "120"><?php echo _l('Raised Evidence'); ?></th>
                                <th width= "120"><?php echo _l('Raised Location'); ?></th>
                                <th width= "120"><?php echo _l('Latest Comment'); ?></th>
                                <th width= "120"><?php echo _l('Latest Evidence'); ?></th>
                                <th width= "120"><?php echo _l('Latest Location'); ?></th>
                                <th width= "100"><?php echo _l('Role'); ?></th>
                                <th width= "120"><?php echo _l('Email ID'); ?></th>
                                <!-- <th width= "80"><?php //echo _l('Type'); ?></th>           -->
                                <th width= "120"><?php echo _l('Milestone'); ?></th>
                                <th width= "120"><?php echo _l('Closed On'); ?></th>
                                <th width= "120"><?php echo _l('Raised By'); ?></th>
                                <th width= "100"><?php echo _l('Raised Name'); ?></th>
                                <th width= "100"><?php echo _l('Raised Contact'); ?></th>
                                <th width= "120"><?php echo _l('Raised Email ID'); ?></th>
                         
                                <th width= "80" class="not-export"><?php echo _l('Evidence'); ?></th> 
                                
                                </tr>  
							</thead>
							<tbody>
                                <?php  //pre($projects); 
                                foreach($projects as $val){ 
                                $project_id = $val['id'];
                                 $ticketDetails = $this->report_model->get_project_details( $project_id);
                                // $assignedUser = !empty($ticketDetails->assigned_user_id) ? $ticketDetails->assigned_user_id : '';
                                //print_r($ticketDetails); exit;
                                 $assignedUser = getProjectAssignedUser($project_id);
                                 $assignedUserDetails = $this->staff_model->get_userDetails($assignedUser);
                                 $tasks = $this->projects_model->get_task_details( $project_id);
                                 $milestone = $this->report_model->get_current_milestone( $project_id);
                                 $milestone = $milestone[0]; 
                                 $milestone_name = '';
                                //  if(!empty($milestone['task_name']) && $val['status'] != 3 && $val['frozen'] == 0){
                                if(!empty($milestone['task_name']) && in_array($val['status'],[2,4,6]) && $val['frozen'] == 0){
                                     $milestone_name = $milestone['task_name'];
                                 }

                                 if($val['status'] == 3){
                                    $milestone_name = 'NA';
                                 }

                                // pre($milestone);
                                 $taskId = (!empty($milestone['task_id'])) ? $milestone['task_id'] : '';
                                 $latestImages = array();
                                 $latest_image ='';
                                 $latest_location = '';
                                 $resolved_evidence = '';
                                 $resolved_location = '';
                                 if(!empty($taskId)){
                                    $latestImages = $this->dashboard_model->get_evidence_image($project_id, $taskId);
                                    if(!empty($latestImages[0]['file_name']) && in_array( $val['status'], [ 2, 4, 6, 3]) ){
                                        $latest_image = base_url('uploads/tasks/' . $taskId . '/') . $latestImages[0]['file_name'];

                                        $resolved_evidence =  '<a href="'. $latest_image .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View </a>';
                                    
                                        $resolved_location = 'NA';
                                        if(!empty($latestImages[0]['latitude']) && $latestImages[0]['latitude'] != 0 && !empty($latestImages[0]['longitude']) &&  $latestImages[0]['longitude'] != 0){
                                            $latest_location = 'https://maps.google.com/maps?q=' . $latestImages[0]['latitude'] . ',' . $latestImages[0]['longitude'] . '"';

                                            $resolved_location = '<a href="'. $latest_location .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>';
                                        }
                                    }
                                }

                                 $projectNotes = project_latest_notes($project_id);
                                 $projectNote_content = !empty($projectNotes->content) ? $projectNotes->content : '';
                                 //2, 4, 6, 
                                 if(!empty($projectNote_content) && $val['frozen'] == 0 && (in_array( $val['status'], [3,5]) || !empty($val['sub_ticket_id']) || is_project_reopened($project_id))){
                                    $resolved_evidence = !empty($resolved_evidence)?$resolved_evidence:'NA';
                                    $resolved_location = !empty($resolved_location)?$resolved_location:'NA';
                                 }
                                    
                                 $evidence = $this->report_model->get_evidence($project_id);
                                 $location = $this->report_model->get_location($project_id);
                                 $ticket_id = !empty( $val['sub_id']) ?  $val['sub_id'] :  $project_id;   
                                 $img_type = '';
                                 if($val['status'] == 3){
                                    $img_type = "closed";
                                 }else{
                                    $img_type = "original";
                                 }
                                
                                 if(!empty( $assignedUserDetails->full_name) && !empty( $assignedUserDetails->organisation)){
                                     $assign = $assignedUserDetails->full_name  ." (".  $assignedUserDetails->organisation.")";
                                 }else{
                                    $assign = '';
                                 }
                                 $status = (!empty( $ticketDetails->status_name)) ?  $ticketDetails->status_name : '';
                                 $assign_email = (!empty( $assignedUserDetails->email)) ?  $assignedUserDetails->email : '';
                                 

                                $raised_name = "";
                                $raised_email = "";
                                $raised_phone = "";
                                $user_type = "";
                                if(!empty( $val['user_type']) && $val['user_type'] == 'Call-Center'){
                                    $user_type = "Call Center Executive";
                                    $raised_name = (!empty( $val['rname'])) ?  $val['rname'] : '';
                                    $raised_email = (!empty( $val['remail'])) ?  $val['remail'] : '';
                                    $raised_phone = (!empty( $val['rphonenumber'])) ?  $val['rphonenumber'] : '';
                                }else if(!empty( $val['user_type']) && $val['user_type'] == 'Surveyor'){
                                   $user_type = "Surveyor";
                                   $raised_name = (!empty( $val['firstname'])) ?  $val['firstname'] : '';
                                   $raised_email = (!empty( $val['email'])) ?  $val['email'] : '';
                                   $raised_phone = (!empty( $val['phonenumber'])) ?  $val['phonenumber'] : '';
                                }


                                $status_tag = '';
                                $statusname = '';
                               
                                if($val['status'] == 9 && $val['is_assigned'] == 0 && $val['frozen'] == 0 ){
                                    $status_tag = 'Unassigned';
                                }else  if (in_array( $val['status'], [ 2, 4, 6])  && $val['action_date'] >= date('Y-m-d') && $val['frozen'] == 0  ) {
                                    $status_tag = 'In Progress';
                                }else if ((in_array( $val['status'], [ 2, 4, 6]) && $val['action_date'] < date('Y-m-d') && $val['frozen'] == 0 ) || ($val['status'] == 1 && $val['action_date'] < date('Y-m-d') && $val['frozen'] == 0 )) {
                                    $status_tag = 'Delayed';
                                }else if( $val['status'] == 5 && $val['frozen'] == 0 ){
                                    $status_tag = 'Rejected';
                                }else if( $val['status'] == 1 && $val['frozen'] == 0 ){
                                    $status_tag = 'New';
                                }else if( $val['status'] == 3 && $val['frozen'] == 0 ){
                                    $status_tag = 'Closed';
                                } else if($val['frozen'] == 1){
                                    $status_tag = 'Frozen';
                                }

                                
             
                                if(!empty($status_tag)){
                                    
                                    //  
                                    if ($status_tag == "Delayed" && $ticketDetails->project_status == 1 ) {
                                        $statusname = 'Unaccepted';
                                    }else if ( $status_tag == "Delayed" &&  ($ticketDetails->project_status == 2 ||  $ticketDetails->project_status == 6 || $ticketDetails->project_status == 4) ) {
                                        $statusname = 'Overdue';
                                    }else if ($status_tag == 'In Progress'   && $val['reassigned'] == 1 ) {
                                        $statusname = 'Reassigned';
                                    }else if($status_tag == 'In Progress' && $ticketDetails->project_status == '2'){
                                        $statusname = '';
                                    }else if( $status_tag == 'In Progress' && $ticketDetails->project_status == '4'){
                                        $statusname = '';
                                    }else if( $status_tag == 'Rejected' && $ticketDetails->project_status == '5'){
                                        $statusname = '';
                                    }else if( $status_tag == 'In Progress' && $ticketDetails->project_status == '6'){
                                        $statusname = 'Reopened';
                                    }
                                }
  
                                if(!empty($statusname)){
                                    $status_name = $status_tag."-".$statusname;
                                }else{
                                    $status_name = $status_tag;
                                }

                                ?>
								<tr>
         
                                    <td><div class="dashboard-cell w20P action-item">
                                    <p class="ticket_details" data-project_id="<?php echo $project_id ?>" data-role="<?php echo $GLOBALS['current_user']->role_slug_url  ?>" data-status="<?php echo $val['status'] ?>" data-report="report"><strong><?php echo $ticket_id ?></strong></p>
                                    </div></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['name'])) ?  strip_tags($val['name']) : '' ?>"><?php echo (!empty( $val['name'])) ?  mb_strimwidth($val['name'], 0, 30, '...') : '' ?></p></td>
                                    <td><?php echo $status_name ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($assign); ?>"><?php echo (!empty( $assign)) ?  mb_strimwidth($assign, 0, 25, '...') : '' ?></p></td>
                                    <td><?php echo (!empty( $assignedUserDetails->phonenumber)) ?  $assignedUserDetails->phonenumber : '' ?></td>
                                    <td><?php echo (!empty( $val['deadline'])) ? '<span class="hide">'.date('Ymd',strtotime($val['deadline'])).'</span>'.date('d-m-Y',strtotime($val['deadline'])) : '' ?></td>
                                    <td><?php echo (!empty( $ticketDetails->sub_region_name)) ?  $ticketDetails->sub_region_name : '' ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['landmark'])) ?  strip_tags($val['landmark']) : '' ?>"><?php echo (!empty( $val['landmark'])) ?  mb_strimwidth($val['landmark'], 0, 30, '...') : '' ?></p></td>
                                    <td><?php echo (!empty( $ticketDetails->region_name)) ?  $ticketDetails->region_name : 'NA' ?></td>
                                    <td><?php echo (!empty( $ticketDetails->area_name)) ?  $ticketDetails->area_name : '' ?></td>
                                    <td><?php echo _d($val['project_created']) ?></td>
                                    
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $val['description'])) ?  strip_tags($val['description']) : '' ?>"><?php echo (!empty( $val['description'])) ?  mb_strimwidth($val['description'], 0, 100, '...') : '' ?></p></td>
                                    
                                   
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo (!empty( $evidence[0])) ?  '<a href="'. $evidence[0] .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View  </a>' : 'NA' ?></div></td>
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo (!empty( $location[0])) ? '<a href="'. $location[0] .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>' : 'NA'  ?></div></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo (!empty( $projectNote_content)) ?  strip_tags($projectNote_content) : '' ?>"><?php echo (!empty( $projectNote_content)) ?  mb_strimwidth($projectNote_content, 0, 50, '...') : '' ?></p></td>

                                    <!-- <td><div class="d-flex justify-content-center align-flex-end">< ?php echo (!empty( $latest_image)) ?  '<a href="'. $latest_image .'" target="_blank" class="report-location" ><i class="fa fa-eye" aria-hidden="true"></i> View </a>' : 'NA' ?></div></td>
                                   
                                    <td><div class="d-flex justify-content-center align-flex-end">< ?php echo (!empty( $latest_location)) ? '<a href="'. $latest_location .'" target="_blank" class="report-location"><i class="fa fa-map-marker" aria-hidden="true"></i> View </a>' : 'NA'  ?></div></td> -->

                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo $resolved_evidence; ?></div></td>
                                   
                                    <td><div class="d-flex justify-content-center align-flex-end"><?php echo $resolved_location;  ?></div></td>

                                   
                                    <td><?php echo (!empty( $assignedUserDetails->role_name)) ?  $assignedUserDetails->role_name : '' ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($assign_email); ?>"><?php echo  mb_strimwidth($assign_email, 0, 25, '...') ?></p></td>
                                    <!-- <td><?php //echo (!empty( $milestone['tag'])) ?  $milestone['tag'] : '' ?></td> -->
                                    <td><?php echo (!empty( $milestone_name)) ?  $milestone_name : '' ?></td>
                                    <td><?php echo (!empty( $val['date_finished'] && $val['date_finished'] != '0000-00-00 00:00:00')) ?  date("d-m-Y", strtotime($val['date_finished'])) : '' ?></td>
                                    <td><?php echo $user_type ?></td>  
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo $raised_name  ?>"><?php echo  mb_strimwidth($raised_name , 0, 25, '...')  ?></p></td>
                                    <td><?php echo $raised_phone ?></td>
                                    <td><p class="ellipsis" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($raised_email); ?>"><?php echo  mb_strimwidth($raised_email, 0, 25, '...') ?></p></td>
                                    
                                     <td><div class="d-flex justify-content-center align-flex-end">
                                        <a class="evidence_img evidence report-location" data-project_id="<?php echo $project_id ?>" data-img_type="<?php echo $img_type; ?>">
                                        <i class="fa fa-eye" aria-hidden="true"></i> View 
                                           </a>  </div>
                                    </td> 
                                   
								</tr>
								<?php } ?>
							</tbody>
						</table>
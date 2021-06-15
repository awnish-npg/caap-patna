<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    // db_prefix().'issue_categories.id',
    'issue_name',
    // 'staff_id',
    'milestone_name',
    'days',
    'reminder_one',
    'reminder_two',
    //db_prefix().'issue_categories.is_active',
    ];
if (has_permission('categories', '', 'edit')) {
  array_push($aColumns, db_prefix().'issue_categories.is_active');
}
$sIndexColumn = 'id';

$sTable       = db_prefix().'issue_categories';
$join = [
    'JOIN ' . db_prefix() . 'issue_milestones ON ' . db_prefix() . 'issue_categories.id = ' . db_prefix() . 'issue_milestones.issue_id',
];
// $where        = ['AND '.db_prefix().'issue_categories.is_active = 1','AND '.db_prefix().'issue_milestones.is_active = 1'];
$where        = ['AND '.db_prefix().'issue_milestones.is_active = 1'];
// $where=[];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, [db_prefix().'issue_categories.id'],'');
$output  = $result['output'];
$rResult = $result['rResult'];
// print_r($result);

$temp=array();
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'issue_name') {

            if(in_array($aRow['id'],$temp)==false){
                
                // $_data = '<a href="#" onclick="edit_issue(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['issue_name'] .'"data-milestoneid="' . get_milestone_of_issue($aRow['id'],'id') . '"data-reminder_one="' . get_milestone_of_issue($aRow['id'],'reminder_one') .'"data-reminder_two="' . get_milestone_of_issue($aRow['id'],'reminder_two') .'"data-milestone="' . get_milestone_of_issue($aRow['id'],'milestone_name') . '"data-duration="' . get_milestone_of_issue($aRow['id'],'days')  . '">' . $_data . '</a>';       
                $_data =  $aRow['issue_name'] ;

            }
                else{
                    $_data ="";
                }       
          }

         if ($aColumns[$i] == 'issue_categories.is_active') {
             if(in_array($aRow['id'],$temp)==false){

                  $checked = '';
                  if ($aRow['issue_categories.is_active'] == 1) {
                      $checked = 'checked';
                  }
                  $_data = "";
                  if (has_permission('categories', '', 'edit')) {
                    $_data = '<div class="onoffswitch">
                    <input type="checkbox"  onclick="changeStatus(this,' . $aRow['id'] .')" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" data-status="' . $aRow['issue_categories.is_active'] . '" ' . $checked . '>
                    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                     </div>';
                      $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('active') : _l('inactive')) . '</span>';
                    }

                  // For exporting

              }else{
               $_data="";
              }
        }

        // print_r($_data);
         $row[] = $_data;

    }

    if(in_array($aRow['id'],$temp)==false){
        array_push($temp,$aRow['id']);
        if (has_permission('categories', '', 'edit')) {
            $options = icon_btn('javascript:void(0)', 'pencil-square-o', 'btn-default edit-category-btn', [
            'onclick' => 'edit_issue(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['issue_name'] .'"data-milestoneid="' . get_milestone_of_issue($aRow['id'],'id') . '"data-reminder_one="' . get_milestone_of_issue($aRow['id'],'reminder_one') .'"data-reminder_two="' . get_milestone_of_issue($aRow['id'],'reminder_two') .'"data-milestone="' . get_milestone_of_issue($aRow['id'],'milestone_name') . '"data-duration="' . get_milestone_of_issue($aRow['id'],'days')  . '"'
            ]);
        }else{
            if(!check($aRow['id'])){
                $checked = 'checked';
                $disable='disabled';
            }
            else{
                $checked="";
                $disable="";
            }
          $options = '<div class="form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox"  name="status" value="1" id="import_status'.$aRow['id'].'" onclick="addtomanage('.$aRow['id'].')" '.$checked.' '.$disable.' >
                            <label for="area_status"></label>
                        </div>
                    </div>';
        }
    }
    else{
        $options="";
    }

    // $row[] = $options .= icon_btn('issues/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // $row[] = icon_btn('issues/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $row[] = $options ;

    $output['aaData'][] = $row;
}

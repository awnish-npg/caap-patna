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
$where        = ['AND '.db_prefix().'issue_milestones.is_active = 1','AND '.db_prefix().'issue_milestones.parent_issue_id = 0','AND '.db_prefix().'issue_milestones.area_id = 0'];
// $where=[];

$result  = data_tables_init_category($aColumns, $sIndexColumn, $sTable,$join, $where, [db_prefix().'issue_categories.id'],'',[],',issue_milestones.id ASC');
$output  = $result['output'];
$rResult = $result['rResult'];
$output['iTotalDisplayRecords'] = $output['iTotalRecords'];
// print_r($aColumns);

$temp=array();
$j = 0;
foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = ucfirst($aRow[$aColumns[$i]]);

        if ($aColumns[$i] == 'issue_name') {

            if(in_array($aRow['id'],$temp)==false){

                // $_data = '<a href="#" onclick="edit_issue(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['issue_name'] .'"data-milestoneid="' . get_milestone_of_issue($aRow['id'],'id') . '"data-reminder_one="' . get_milestone_of_issue($aRow['id'],'reminder_one') .'"data-reminder_two="' . get_milestone_of_issue($aRow['id'],'reminder_two') .'"data-milestone="' . get_milestone_of_issue($aRow['id'],'milestone_name') . '"data-duration="' . get_milestone_of_issue($aRow['id'],'days')  . '">' . $_data . '</a>';

                if(isset($rResult[$j+1]) && $rResult[$j+1]['id'] == $aRow['id'])
                  $_data =  '<a href="javascript://" class="btn btn-default btn-icon btn-color show_milestone plus" id="c_' . $aRow['id']. '"><i class="fa fa-plus-square-o"></i></a>&nbsp;<span class="ellipsis" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.ucfirst($aRow['issue_name']).'">'.ucfirst($aRow['issue_name']).'</span>';
                else{
                  $_data =  '<a href="javascript://" class="btn btn-default btn-icon btn-color no_milestone" id="c_' . $aRow['id']. '"></a>&nbsp;<span class="ellipsis" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.ucfirst($aRow['issue_name']).'">'.ucfirst($aRow['issue_name']).'</span>';
                }

                $row["DT_RowClass"] = "category c_" . $aRow['id']  ;
            }
                else{
                    $_data ="";
                    $row["DT_RowClass"] = "milestone c_" . $aRow['id'] ;
                }
          }
         if ($aColumns[$i] == 'reminder_one') {
            if( $aRow['reminder_one'] == 0) $_data = '';
         }
         if ($aColumns[$i] == 'reminder_two') {
            if( $aRow['reminder_two'] == 0) $_data = '';
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
                    <input type="checkbox"  onclick="changeStatus(this,' . $aRow['id'] .')" name="onoffswitch" class="onoffswitch-checkbox" id="d_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" data-status="' . $aRow['issue_categories.is_active'] . '" ' . $checked . '>
                    <label class="onoffswitch-label" for="d_' . $aRow['id'] . '"></label>
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
    $j++;
    if(in_array($aRow['id'],$temp)==false){
        array_push($temp,$aRow['id']);
        if (has_permission('categories', '', 'edit')) {
            $options = icon_btn('javascript:void(0)', 'pencil-square-o btn-color', 'btn-default edit-category-btn', [
            'onclick' => 'edit_issue(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['issue_name'] .'"data-milestoneid="' . get_milestone_of_issue($aRow['id'],'id') . '"data-reminder_one="' . get_milestone_of_issue($aRow['id'],'reminder_one') .'"data-reminder_two="' . get_milestone_of_issue($aRow['id'],'reminder_two') .'"data-milestone="' . get_milestone_of_issue($aRow['id'],'milestone_name') . '"data-duration="' . get_milestone_of_issue($aRow['id'],'days')  . '"'
            ]);
        }
        else{
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
        //$row["DT_RowClass"] = "category" ;
        $row["DT_RowAttr"] = "category" ;
    }
    else{
        $options="";

        $row["DT_RowAttr"] = "milestone" ;
    }

    // $row[] = $options .= icon_btn('issues/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // $row[] = icon_btn('issues/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $row[] = $options ;


    $output['aaData'][] = $row;

}





/*


die(); // logic 2

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
                $_data =  '<b style="font-weight:1000;">'.$aRow['issue_name'].'</b>';

            }
                else{
                    $_data ="";
                }
          }

          if ($aColumns[$i] == 'milestone_name') {

            if(in_array($aRow['id'],$temp)==false){
                $data=get_milestone_of_issue($aRow['id'],'milestone_name');
                $milestonenamearray=explode(',',$data);
                $milestonenamearray[0]='<b style="font-weight:1000;">'.$milestonenamearray[0].'</b>';
                $milestones=implode('<br>',$milestonenamearray);
                $_data=$milestones;
            }
                else{
                    $_data ="";
                }
          }

          if ($aColumns[$i] == 'days') {

            if(in_array($aRow['id'],$temp)==false){
                $data=get_milestone_of_issue($aRow['id'],'days');
                $milestonesdurationarray=explode(',',$data);
                $milestonesdurationarray[0]='<b style="font-weight:1000;">'.$milestonesdurationarray[0].'</b>';
                $durations=implode('<br>',$milestonesdurationarray);
                $_data=$durations;
            }
                else{
                    $_data ="";
                }
          }

          if ($aColumns[$i] == 'reminder_one') {

            if(in_array($aRow['id'],$temp)==false){
                $data=get_milestone_of_issue($aRow['id'],'reminder_one');
                $milestonesr1array=explode(',',$data);
                if(count($milestonesr1array)==1){
                    $milestonesr1array[0]='<b style="font-weight:1000;">'.$milestonesr1array[0].'</b>';
                }
                else{
                    $milestonesr1array[0]="";
                }
                $mr1=implode('<br>',$milestonesr1array);
                $_data=$mr1;
            }
                else{
                    $_data ="";
                }
          }
          if ($aColumns[$i] == 'reminder_two') {

            if(in_array($aRow['id'],$temp)==false){
                $data=get_milestone_of_issue($aRow['id'],'reminder_two');
                $milestonesr2array=explode(',',$data);
                if(count($milestonesr2array)==1){
                    $milestonesr2array[0]='<b style="font-weight:1000;">'.$milestonesr2array[0].'</b>';
                }
                else{
                $milestonesr2array[0]="";
                }
                $mr2=implode('<br>',$milestonesr2array);
                $_data=$mr2;
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
    $totalcount=calcuatetotaldurationofcategory($aRow['id'],'days');
    if(in_array($aRow['id'],$temp)==false){
        // array_push($temp,$aRow['id']);
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
    $row["DT_RowClass"] = "gradeA" ;
    $row["DT_RowAttr"] = "attre1" ;

//     if(in_array($aRow['id'],$temp)==false){
        // array_push($temp,$aRow['id']);
//     for ($i = 0; $i < count($aColumns); $i++){
//         if($aColumns[$i] == 'issue_name'){
//             $row1[]='<b style="font-weight:1000;">Project Closure</b>';
//         }
//         else{
//         $row1[]="";
//         }
//     }
//     $row1[]=["",""];
// }

// if(in_array($aRow['id'],$temp)==false){
//     $output['aaData'][] = $row1;
//     array_push($temp,$aRow['id']);
// }
  if(in_array($aRow['id'],$temp)==false){
      array_push($temp,$aRow['id']);
      $output['aaData'][] = $row;
  }

}
*/

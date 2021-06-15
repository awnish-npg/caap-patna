<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $aColumns = [
        'name',
        'status',
        ];
    $sIndexColumn = 'id';
    $sTable       = db_prefix().'exception';
    $where        = ['AND is_deleted = 0 AND name != "Others"'];
    //$where        = ['AND is_deleted = 0'];
    $join         = [];  
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['is_deleted','id']);
    $output  = $result['output'];
    $rResult = $result['rResult'];



    foreach ($rResult as $aRow) {
    
            $row = [];
            for ($i = 0; $i < count($aColumns); $i++) 
            {
                $_data = $aRow[$aColumns[$i]];

                if ($aColumns[$i] == 'status') {
// check by umair
                    if($aRow['name']=='Others'){
                        $_data="";    
                    }
                    else{
                    $checked = '';
                    if ($aRow['status'] == 1) {
                        $checked = 'checked';
                    }

                    $_data = '<div class="onoffswitch">
                        <input type="checkbox"   onclick="changeStatus(this,' . $aRow['id'] .')" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" data-status="' . $aRow['status'] . '" ' . $checked . '>
                        <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                    </div>';

                    // For exporting
                    $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('active') : _l('inactive')) . '</span>';
                }
                }

                $row[] = $_data;        
            }
            // check by umair
            if($aRow['name']=='Others'){
                $options='';
            }else{
            $options = icon_btn('area/area/' . $aRow['id'], 'pencil-square-o', 'btn-default', [
                'onclick' => 'edit_area(this,' . $aRow['id'] . '); return false', 'data-name' => $aRow['name'],  'data-status' => $aRow['status'], 'data-areaid' => $aRow['id'],
                ]);
            }
            $row[] = $options; //.= icon_btn('area/delete/' . $aRow['areaid'], 'remove', 'btn-danger _delete');
                
            $output['aaData'][] = $row;
   
    }

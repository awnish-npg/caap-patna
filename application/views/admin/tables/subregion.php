<?php

    defined('BASEPATH') or exit('No direct script access allowed');

    $aColumns = [
        db_prefix() . 'sub_region.region_name',
        db_prefix() . 'region.region_name',
        //db_prefix() . 'area.name',
        db_prefix() . 'sub_region.status',
        ];
    $sIndexColumn = 'id';
    $sTable       = db_prefix().'sub_region';
    //$where        = ['AND '.db_prefix() .'sub_region.is_deleted = 0 And ' . db_prefix() . 'sub_region.created_by =  '.$_SESSION['staff_user_id']];
    $where        = ['AND '.db_prefix() .'region.is_deleted = 0 AND ' . db_prefix() . 'area.areaid = '. $GLOBALS['current_user']->area];
    $join         = ['LEFT JOIN ' . db_prefix() . 'area ON ' . db_prefix() . 'area.areaid = ' . db_prefix() . 'sub_region.area_id LEFT JOIN ' . db_prefix() . 'region ON ' . db_prefix() . 'region.id = ' . db_prefix() . 'sub_region.region_id'];  

    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,  $join, $where, [db_prefix() . 'sub_region.id',db_prefix() . 'sub_region.area_id',db_prefix() . 'sub_region.region_id']);
    $output  = $result['output'];
    $rResult = $result['rResult'];

    foreach ($rResult as $aRow) {
        $row = [];
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
         
            if ($aColumns[$i] == 'sub_region.status') {
                            
                $checked = '';
                if ($aRow[db_prefix() . 'sub_region.status'] == 1) {
                    $checked = 'checked';
                }

                $_data = '<div class="onoffswitch">
                    <input type="checkbox"  onclick="changeStatus(this,' . $aRow['id'] .')"  name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" data-status="' . $aRow[db_prefix() . 'sub_region.status'] . '" ' . $checked . '>
                    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                // For exporting
                $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('active') : _l('inactive')) . '</span>';

            }

            $row[] = $_data;  
        }

        $options = icon_btn('subregion/subregion/' . $aRow['id'], 'pencil-square-o', 'btn-default', [
            'onclick' => 'edit_subregion(this,' . $aRow['id'] . '); return false', 'data-name' => $aRow[db_prefix() . 'sub_region.region_name'], 'data-area' => $aRow['area_id'], 'data-region' => $aRow['region_id'],'data-id' => $aRow['id'], 
            ]);
        $row[] = $options; //.= icon_btn('subregion/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');

        $output['aaData'][] = $row;
    }

<?php

    defined('BASEPATH') or exit('No direct script access allowed');
   
    $aColumns = [   
        'region_name',
        db_prefix() . 'area.name',
        db_prefix() . 'region.status',
        ];
    $sIndexColumn = 'id';
    $sTable       = db_prefix().'region';
    //$where        = ['AND '.db_prefix() .'region.is_deleted = 0 And ' . db_prefix() . 'region.created_by =  '.$_SESSION['staff_user_id']];
    $where        = ['AND '.db_prefix() .'region.is_deleted = 0 AND ' . db_prefix() . 'area.areaid = '. $GLOBALS['current_user']->area];
    $join         = ['LEFT JOIN ' . db_prefix() . 'area ON ' . db_prefix() . 'area.areaid = ' . db_prefix() . 'region.area_id' ]; 
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable,  $join, $where, ['id', 'name', 'area_id']);
    $output  = $result['output'];
    $rResult = $result['rResult'];

    foreach ($rResult as $aRow) {
        $row = [];
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];

            if ($aColumns[$i] == 'region.status') {
                        
                $checked = '';
                if ($aRow[db_prefix() . 'region.status'] == 1) {
                    $checked = 'checked';
                }

                $_data = '<div class="onoffswitch">
                    <input type="checkbox"  onclick="changeStatus(this,' . $aRow['id'] .')"  name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" data-status="' . $aRow[db_prefix() . 'region.status'] . '" ' . $checked . '>
                    <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                // For exporting
                $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('active') : _l('inactive')) . '</span>';
            
            }

            $row[] = $_data;
        
        }

        $options = icon_btn('region/region/' . $aRow['id'], 'pencil-square-o', 'btn-default', [
            'onclick' => 'edit_region(this,' . $aRow['id'] . '); return false', 'data-name' => $aRow['region_name'], 'data-area' => $aRow['area_id'], 'data-id' => $aRow['id'], 
            ]);
        $row[] = $options; //.= icon_btn('region/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');

        $output['aaData'][] = $row;
    }

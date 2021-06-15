<?php

defined('BASEPATH') or exit('No direct script access allowed');
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', 360);
@ini_set('post_max_size', '64M');
@ini_set('upload_max_filesize', '64M');
class Masterplan extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_model');

    }
   
    public function index()
    {
       
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name',
                'file',
                ];
            $sIndexColumn = 'areaid';
            $sTable       = db_prefix().'area';
            $where        = ['AND is_deleted = 0'];
            $join         = [];  
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['is_deleted','areaid']);
            $output  = $result['output'];
            $rResult = $result['rResult'];
 
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'file') {
                        if($aRow['file'] != NULL){
                            $_data = '<div class="">
                            <p class="evidence_img" data-area_id="' . $aRow['areaid'] .'">
                                <a href="'. base_url('uploads/area/'.$aRow['file']) .'" target="_blank" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> '.$aRow['file'].'</a>
                                <span></span></p>        
                        </div>';
    
                         // For exporting
                         $_data .= '<span class="hide">'.$aRow['file'] .'</span>';
                        }
                    } 
                    $row[] = $_data;  
                }

                $output['aaData'][] = $row;
            }
            echo json_encode($output);
                die();
             
        }
        $data['title']                = 'City Action Plan';
        $this->load->view('admin/area/master_plan', $data);

    }

    public function edit()
    {
       
        $area = $GLOBALS['current_user']->area;
        
            if(!empty($_FILES)){
                if(isset($_FILES["file"]['name'])){
                $name = str_replace([' ', '.'], '_', basename($_FILES["file"]['name'], ".pdf"));
                $file = time().'-'.$name.".pdf";   
                $config = array(
                    'upload_path'   =>  FCPATH . 'uploads/area/',
                    'allowed_types' => "pdf",
                    'overwrite'     => TRUE,
                    'file_name'     => $file,
                    'max_size'      => '5000',
                    );
                    $this->load->library('upload', $config);
                    
                    if (!$this->upload->do_upload('file')) {
                        $error = array('error' => $this->upload->display_errors());
                        if($error['error'] == '<p>The filetype you are attempting to upload is not allowed.</p>'){
                 
                            $err = "Only PDF file type is allowed";
                        }else if($error['error'] == '<p>The file you are attempting to upload is larger than the permitted size.</p>'){
                         
                            $err = "File uploaded is greater than 5 MB";
                        }else{
                            $err = "Something went wrong.";
                        }
                        
                        echo json_encode([
                            'success'              => false,
                            'message'              => $err,
                        ]);
                    }else{
                        
                            $data['file'] = $file;
                            $this->db->where('areaid', $area);
                            $this->db->update(db_prefix() . 'area', $data);
                            echo json_encode([
                                'success'              => true,
                                'message'              => 'City Action Plan updated successfully',
                            ]);
                    }  
                }
               
            }else{
                $data['title']                = 'Edit City Action Plan';
                $this->load->view('admin/area/edit_master', $data);
            }

    }

}

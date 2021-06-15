<?php

defined('BASEPATH') or exit('No direct script access allowed');

class New_ticket extends App_mail_template
{
    protected $for = 'staff';

    protected $staff_email;

    protected $original_password;

    protected $staffid;

    public $slug = 'new-ticket';

    public $rel_type = 'staff';

    public function __construct($staff_email, $staffid, $original_password,$id,$area,$region,$subregion,$category,$landmark,$deadline)
    {
        parent::__construct();
        $location=get_ticket_doc_loc($id);
        $file=get_ticket_image($id);
        $path=base_url('uploads/projects').'/'.$id.'/'.$file;
        $latitude=$location['latitude'];
        $longitude=$location['longitude'];
        $this->staff_email       = $staff_email;
        $this->staffid           = $staffid;
        $this->original_password = $original_password;
        $projectId = getSubTicketName($id);
        // $this->id = $id;
        $this->id = $projectId;
        $this->area = $area;
        $this->region = $region;
        $this->subregion = $subregion;
        $this->category = $category;
        $this->landmark = $landmark;
        $this->deadline = $deadline;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->path = $path;
        if($latitude==0 and $longitude==0){
            $this->latitude="NA";
        }else{
            $this->latitude='Click <a href="https://maps.google.com/?q='.$latitude.','.$longitude.'" target="_blank">here </a>';
        }
    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->staffid)
        ->set_merge_fields('staff_merge_fields', $this->staffid, $this->original_password,$this->id,$this->area,$this->region,$this->subregion,$this->category, $this->landmark, $this->deadline,
        $this->latitude,$this->longitude,$this->path);
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_reopened extends App_mail_template
{
    protected $for = 'staff';

    protected $staff_email;

    protected $original_password;

    protected $staffid;

    public $slug = 'ticket-re-opened';

    public $rel_type = 'staff';

    public function __construct($staff_email, $staffid, $original_password,$id,$area,$region,$subregion,$category,$landmark,$deadline,$openername
    ,$org,$desg,$latestcomment,$reopenedcomment)
    {
        parent::__construct();
        // $location=get_ticket_image_loc($id);
        $fileData = get_milestone_ticket_image($id);
        $path = "No file";
        if(!empty($fileData)){
            $taskId = $fileData['milestone'];
            $file = $fileData['file_name'];
            $path=base_url('uploads/tasks').'/'.$taskId.'/'.$file;
        }

        // $latitude=$location['latitude'];
        // $longitude=$location['longitude'];
        $projectId = getSubTicketName($id);
        $this->staff_email       = $staff_email;
        $this->staffid           = $staffid;
        $this->original_password = $original_password;
        // $this->id = $id;
        $this->id = $projectId;
        $this->area = $area;
        $this->region = $region;
        $this->subregion = $subregion;
        $this->category = $category;
        $this->landmark = $landmark;
        $this->deadline = $deadline;
        // $this->latitude = $latitude;
        // $this->longitude = $longitude;
        $this->path = $path;
        $this->openername=$openername;
        $this->org=$org;
        $this->desg=$desg;
        $this->latestcomment=$latestcomment;
        $this->reopenedcomment=$reopenedcomment;

    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->staffid)
        ->set_merge_fields('staff_merge_fields', $this->staffid, $this->original_password,$this->id,$this->area,$this->region,$this->subregion,$this->category, $this->landmark, $this->deadline,
        '','',$this->path,$this->openername,$this->org,$this->desg,$this->latestcomment,$this->reopenedcomment);
    }
}

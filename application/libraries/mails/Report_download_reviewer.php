<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_download_reviewer extends App_mail_template
{
    protected $for = 'staff';

    protected $staff_email;

    protected $original_password;

    protected $id;
    protected $area;
    protected $region;
    protected $subregion;
    protected $category;
    protected $landmark;
    protected $deadline;
    protected $latitude;
    protected $longitude;

    protected $staffid;

    public $slug = 'report-download-ar';

    public $rel_type = 'staff';

    public function __construct($staff_email, $staffid, $original_password,$id,$area,$region,$subregion,$category,$landmark,$deadline,$latitude,$longitude)
    {
        parent::__construct();
        $this->staff_email       = $staff_email;
        $this->staffid           = $staffid;
        $this->original_password = $original_password;
        $this->id = $id;
        $this->area = $area;
        $this->region = $region;
        $this->subregion = $subregion;
        $this->category = $category;
        $this->landmark = $landmark;
        $this->deadline = $deadline;
        $this->latitude = $latitude;
        $this->longitude = $longitude;

    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->staffid)
        ->set_merge_fields('staff_merge_fields', $this->staffid, $this->original_password, $this->id, $this->area, $this->region, $this->subregion, $this->category, $this->landmark, $this->deadline, $this->latitude, $this->longitude);
    }
}

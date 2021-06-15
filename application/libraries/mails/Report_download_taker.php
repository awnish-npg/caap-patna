<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_download_taker extends App_mail_template
{
    protected $for = 'staff';

    protected $staff_email;

    protected $original_password;

    protected $id;

    protected $staffid;

    public $slug = 'download-report';

    public $rel_type = 'staff';

    public function __construct($staff_email, $staffid, $original_password,$id)
    {
        parent::__construct();
        $this->staff_email       = $staff_email;
        $this->staffid           = $staffid;
        $this->original_password = $original_password;
        $this->id = $id;

    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->staffid)
        ->set_merge_fields('staff_merge_fields', $this->staffid, $this->original_password, $this->id);
    }
}

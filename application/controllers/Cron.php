<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{
    public function index($key = '')
    {
        update_option('cron_has_run_from_cli', 1);

        if (defined('APP_CRON_KEY') && (APP_CRON_KEY != $key)) {
            header('HTTP/1.0 401 Unauthorized');
            die('Passed cron job key is not correct. The cron job key should be the same like the one defined in APP_CRON_KEY constant.');
        }

        $last_cron_run                  = get_option('last_cron_run');
        $seconds = hooks()->apply_filters('cron_functions_execute_seconds', 300);

        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->load->model('cron_model');
            $this->cron_model->run();
            $this->update_delayed_projects();
        }
    }

    public function update_delayed_projects(){
        $this->load->model('projects_model');
        $delayedProjects = $this->projects_model->getDelayedProjects();
        
        //check if any delayed project updated his state
        $delayedHistoryProjects = $this->projects_model->getDelayedFrozenHistoryProjects('project_delayed',7);
        
        $delayedStatusProjects = array_diff($delayedProjects,$delayedHistoryProjects);
        $updatedStatusProjects = array_diff($delayedHistoryProjects,$delayedProjects);
        
        if(!empty($updatedStatusProjects)){
            //update projects with no more delayed
            $this->projects_model->updateDelayedHistoryProjects($updatedStatusProjects,'project_delayed',7);
        }
        
        //update project_activity table with delayed projects
        if(!empty($delayedStatusProjects)){
            $this->projects_model->addDelayedFrozenProjects($delayedStatusProjects,'project_delayed',7);
        }
    }
}

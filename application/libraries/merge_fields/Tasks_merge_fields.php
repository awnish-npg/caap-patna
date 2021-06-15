<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasks_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
                [
                    'name'      => 'Staff/Contact who take action on task',
                    'key'       => '{task_user_take_action}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Link',
                    'key'       => '{task_link}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Comment Link',
                    'key'       => '{comment_link}',
                    'available' => [
                    ],
                    'templates' => [
                        'task-commented',
                        'task-commented-to-contacts',
                    ],
                ],
                [
                    'name'      => 'Task Name',
                    'key'       => '{task_name}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Description',
                    'key'       => '{task_description}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Status',
                    'key'       => '{task_status}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Comment',
                    'key'       => '{task_comment}',
                    'available' => [

                    ],
                    'templates' => [
                        'task-commented',
                        'task-commented-to-contacts',
                    ],
                ],
                [
                    'name'      => 'Task Priority',
                    'key'       => '{task_priority}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Start Date',
                    'key'       => '{task_startdate}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Task Due Date',
                    'key'       => '{task_duedate}',
                    'available' => [
                        'tasks',
                    ],
                ],
                [
                    'name'      => 'Related to',
                    'key'       => '{task_related}',
                    'available' => [
                        'tasks',
                    ],
                ],
            ];
    }

    /**
     * Merge fields for tasks
     * @param  mixed  $task_id         task id
     * @param  boolean $client_template is client template or staff template
     * @return array
     */
    public function format($task_id, $client_template = false)
    {
        $fields = [];

        $this->ci->db->where('id', $task_id);
        $task = $this->ci->db->get(db_prefix().'tasks')->row();

        if (!$task) {
            return $fields;
        }

        // Client templateonly passed when sending to tasks related to project and sending email template to contacts
        // Passed from tasks_model  _send_task_responsible_users_notification function
        if ($client_template == false) {
            $fields['{task_link}'] = admin_url('tasks/view/' . $task_id);
        } else {
            $fields['{task_link}'] = site_url('clients/project/' . $task->rel_id . '?group=project_tasks&taskid=' . $task_id);
        }

        if (is_client_logged_in()) {
            $fields['{task_user_take_action}'] = get_contact_full_name(get_contact_user_id());
        } else {
            $fields['{task_user_take_action}'] = get_staff_full_name(get_staff_user_id());
        }

        $fields['{task_comment}'] = '';
        $fields['{task_related}'] = '';
        $fields['{project_name}'] = '';

        if ($task->rel_type == 'project') {
            $this->ci->db->select('name, clientid , area_id ,region_id ,subregion_id ,issue_id , id ,landmark,deadline');
            $this->ci->db->from(db_prefix().'projects');
            $this->ci->db->where('id', $task->rel_id);
            $project = $this->ci->db->get()->row();
            if ($project) {
                $fields['{project_name}'] = $project->name;
                // print_r($project);
                // die;
                $fields['{id}'] = $project->id;
                $fields['{area}'] = getitemname(convertint($project->area_id),'area');
                $fields['{region}'] = getitemname(convertint($project->region_id),'region');
                $fields['{subregion}'] = getitemname(convertint($project->subregion_id),'subregion');
                $fields['{category}'] = getitemname(convertint($project->issue_id),'category');
                $fields['{landmark}'] = $project->landmark;
                $fields['{deadline}'] = $project->deadline;
                $projectNotes = project_latest_notes($project->id, 4);
                $latestComment = !empty($projectNotes->content) ? $projectNotes->content : 'NA';
                $fields['{resolver_last_comment}'] = $latestComment;
                 // Task Evidence
                $fileData = get_milestone_ticket_image($project->id);
                $path = "No file";
                if(!empty($fileData) and $fileData['milestone'] != 0){
                    $taskId = $fileData['milestone'];
                    $file = $fileData['file_name'];
                    $path=base_url('uploads/tasks').'/'.$taskId.'/'.$file;
                }

                if(empty($taskId)){
                    $eve="NA";
                }else{
                    $eve='Click <a href="'.$path.'" target="_blank">here </a>';
                }
                $fields['{resolver_evidence}'] = $eve;
                // End
                // project Evidence
                $file=get_ticket_image($project->id);
                $filepath=base_url('uploads/projects').'/'.$project->id.'/'.$file;
                $fields['{filepath}'] = $filepath;
                // End
                $location=get_ticket_doc_loc($project->id);
                $latitude=$location['latitude'];
                $longitude=$location['longitude'];
                if($latitude==0 and $longitude==0){
                    $coor="NA";
                }else{
                    $coor='Click <a href="https://maps.google.com/?q='.$latitude.','.$longitude.'" target="_blank">here </a>';
                }
                $fields['{location}'] = $coor;


            }
        }

       

        $fields['{task_name}']        = $task->name;
        $fields['{task_description}'] = $task->description;

        $languageChanged = false;

        // The tasks status may not be translated if the client language is not loaded
        if (!is_client_logged_in()
        && $task->rel_type == 'project'
        && $project
        && isset($GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS'])
        && !$GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS']->get_staff_id() // email to client
    ) {
            load_client_language($project->clientid);
            $languageChanged = true;
        } else {
            if (isset($GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS'])) {
                $sending_to_staff_id = $GLOBALS['SENDING_EMAIL_TEMPLATE_CLASS']->get_staff_id();
                if ($sending_to_staff_id) {
                    load_admin_language($sending_to_staff_id);
                    $languageChanged = true;
                }
            }
        }

        $fields['{task_status}']   = format_task_status($task->status, false, true);
        $fields['{task_priority}'] = task_priority($task->priority);

        $custom_fields = get_custom_fields('tasks');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($task_id, $field['id'], 'tasks');
        }

        if (!is_client_logged_in() && $languageChanged) {
            load_admin_language();
        } elseif (is_client_logged_in() && $languageChanged) {
            load_client_language();
        }

        $fields['{task_startdate}'] = _d($task->startdate);
        $fields['{task_duedate}']   = _d($task->duedate);
        $fields['{comment_link}']   = '';

        $this->ci->db->where('taskid', $task_id);
        $this->ci->db->limit(1);
        $this->ci->db->order_by('dateadded', 'desc');
        $comment = $this->ci->db->get(db_prefix().'task_comments')->row();

        if ($comment) {
            $fields['{task_comment}'] = $comment->content;
            $fields['{comment_link}'] = $fields['{task_link}'] . '#comment_' . $comment->id;
        }

        if($task->deadline_notified == 0){
            $fields['{reminder_count}'] = 'First';
            $fields['{reminder_count_lower}'] = 'first';
        }
        if($task->deadline_notified == 1){
            $fields['{reminder_count}'] = 'Second';
            $fields['{reminder_count_lower}'] = 'second';
        }
        $fields['{project_id}'] = $task->rel_id;


        return hooks()->apply_filters('task_merge_fields', $fields, [
        'id'              => $task_id,
        'task'            => $task,
        'client_template' => $client_template,
     ]);
    }
}

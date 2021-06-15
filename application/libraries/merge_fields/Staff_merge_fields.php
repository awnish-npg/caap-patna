<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
        [
                    'name'      => 'Staff Firstname',
                    'key'       => '{staff_firstname}',
                    'available' => [
                        'staff',
                        'gdpr',
                    ],
                    'templates' => [
                        'task-status-change-to-staff',
                        'task-commented',
                        'task-deadline-notification',
                        'task-added-attachment',
                        'task-added-as-follower',
                        'task-assigned',
                        'new-project-discussion-created-to-staff',
                        'new-project-file-uploaded-to-staff',
                        'new-project-discussion-comment-to-staff',
                        'staff-added-as-project-member',
                        'contract-expiration-to-staff',
                        'contract-signed-to-staff',
                        'contract-comment-to-admin',
                    ],
                ],
                [
                    'name'      => 'Staff Lastname',
                    'key'       => '{staff_lastname}',
                    'available' => [
                        'staff',
                        'gdpr',
                    ],
                    'templates' => [
                        'task-status-change-to-staff',
                        'task-commented',
                        'task-deadline-notification',
                        'task-added-attachment',
                        'task-added-as-follower',
                        'task-assigned',
                        'new-project-discussion-created-to-staff',
                        'new-project-file-uploaded-to-staff',
                        'new-project-discussion-comment-to-staff',
                        'staff-added-as-project-member',
                        'contract-expiration-to-staff',
                        'contract-signed-to-staff',
                        'contract-comment-to-admin',
                    ],
                ],
                [
                    'name'      => 'Staff Email',
                    'key'       => '{staff_email}',
                    'available' => [
                        'staff',
                    ],
                    'templates' => [
                        'new-project-discussion-created-to-staff',
                        'new-project-file-uploaded-to-staff',
                        'new-project-discussion-comment-to-staff',
                        'staff-added-as-project-member',
                    ],
                ],
                [
                    'name'      => 'Staff Date Created',
                    'key'       => '{staff_datecreated}',
                    'available' => [
                        'staff',
                    ],
                ],
                [
                    'name'      => 'Reset Password Url',
                    'key'       => '{reset_password_url}',
                    'available' => [
                    ],
                    'templates' => [
                        'staff-forgot-password',
                    ],
                ],
                [
                    'name'      => 'Reminder Text',
                    'key'       => '{staff_reminder_description}',
                    'available' => [

                    ],
                    'templates' => [
                        'reminder-email-staff',
                    ],
                ],
                [
                    'name'      => 'Reminder Date',
                    'key'       => '{staff_reminder_date}',
                    'available' => [

                    ],
                    'templates' => [
                        'reminder-email-staff',
                    ],
                ],
                [
                    'name'      => 'Reminder Relation Name',
                    'key'       => '{staff_reminder_relation_name}',
                    'available' => [

                    ],
                    'templates' => [
                        'reminder-email-staff',
                    ],
                ],
                [
                    'name'      => 'Reminder Relation Link',
                    'key'       => '{staff_reminder_relation_link}',
                    'available' => [

                    ],
                    'templates' => [
                        'reminder-email-staff',
                    ],
                ],
                [
                    'name'      => 'Two Factor Authentication Code',
                    'key'       => '{two_factor_auth_code}',
                    'available' => [
                    ],
                    'templates' => [
                        'two-factor-authentication',
                    ],
                ],
                [
                    'name'      => 'Password',
                    'key'       => '{password}',
                    'available' => [
                    ],
                    'templates' => [
                        'new-staff-created',
                    ],
                ],
            ];
    }

    /**
    * Merge field for staff members
    * @param  mixed $staff_id staff id
    * @param  string $password password is used only when sending welcome email, 1 time
    * @return array
    */
    public function format($staff_id, $password = '',$id= '',$area='',$region='',$subregion='',$category='',$landmark="",$deadline="",$latitude="",$longitude="",$path="",$openername="",
    $org='',$desg='',$latestcomment='',$reopenedcomment='', $reminder_count = '')
    {
        $fields = [];

        $this->ci->db->where('staffid', $staff_id);
        $staff = $this->ci->db->get(db_prefix().'staff')->row();
       
        $role = $this->ci->db->where('roleid', $staff->role)->get(db_prefix().'roles')->row();

        $fields['{password}']          = '';
        $fields['{staff_firstname}']   = '';
        $fields['{staff_lastname}']    = '';
        $fields['{staff_email}']       = '';
        $fields['{staff_datecreated}'] = '';
        $fields['{user_type}'] = '';
        $fields['{id}'] = '';
        $fields['{area}'] = '';
        $fields['{region}'] = '';
        $fields['{subregion}'] = '';
        $fields['{category}'] = '';
        $fields['{landmark}'] = '';
        $fields['{deadline}'] = '';
        $fields['{latitude}'] = '';
        $fields['{longitude}'] = '';
        $fields['{filepath}'] = '';
        $fields['{opener_name}']='';
        $fields['{desg}']='';
        $fields['{org}']='';
        $fields['{latestcomment}']='';
        $fields['{reopenedcomment}']='';

        $fields['{reminder_count}']='';

        
        if (!$staff) {
            return $fields;
        }

        if ($password != '') {
            $fields['{password}'] = htmlentities($password);
        }
        if ($id != '') {
            $fields['{id}'] = $id;
        }
        if ($area != '') {
            $fields['{area}'] = htmlentities($area);
        }
        if ($region != '') {
            $fields['{region}'] = htmlentities($region);
        }
        if ($subregion != '') {
            $fields['{subregion}'] = htmlentities($subregion);
        }
        if ($category != '') {
            $fields['{category}'] = htmlentities($category);
        }
        if ($landmark != '') {
            $fields['{landmark}'] = htmlentities($landmark);
        }
        if ($deadline != '') {
            $fields['{deadline}'] = htmlentities($deadline);
        }
        if ($latitude != '') {
            $fields['{latitude}'] = $latitude;
        }
        if ($longitude != '') {
            $fields['{longitude}'] = htmlentities($longitude);
        }
        if ($path != '') {
            $fields['{filepath}'] = htmlentities($path);
        }
        if ($openername != '') {
            $fields['{opener_name}'] = trim(htmlentities($openername));
        }
        if ($org != '') {
            $fields['{org}'] = htmlentities($org);
        }
        if ($desg != '') {
            $fields['{desg}'] = htmlentities($desg);
        }
        if ($latestcomment != '') {
            $fields['{latestcomment}'] = htmlentities($latestcomment);
        }
        if ($reopenedcomment != '') {
            $fields['{reopenedcomment}'] = htmlentities($reopenedcomment);
        }

        if ($reminder_count != '') {
            $fields['{reminder_count}'] = htmlentities($reminder_count);
        }

        if ($staff->two_factor_auth_code) {
            $fields['{two_factor_auth_code}'] = $staff->two_factor_auth_code;
        }

        $fields['{staff_firstname}']   = $staff->firstname;
        $fields['{staff_lastname}']    = $staff->lastname;
        $fields['{staff_email}']       = $staff->email;
        $fields['{staff_datecreated}'] = $staff->datecreated;
        $fields['{user_type}']         = $role->name;

      

        $custom_fields = get_custom_fields('staff');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($staff_id, $field['id'], 'staff');
        }

        return hooks()->apply_filters('staff_merge_fields', $fields, [
        'id'    => $staff_id,
        'staff' => $staff,
     ]);
    }

    /**
     * Merge fields for staff reminders
     * @param  object $reminder reminder from database
     * @return array
     */
    public function reminder($reminder)
    {
        $reminder = (object) $reminder;

        $rel_data   = get_relation_data($reminder->rel_type, $reminder->rel_id);
        $rel_values = get_relation_values($rel_data, $reminder->rel_type);

        $fields['{staff_reminder_description}']   = $reminder->description;
        $fields['{staff_reminder_date}']          = _dt($reminder->date);
        $fields['{staff_reminder_relation_name}'] = $rel_values['name'];
        $fields['{staff_reminder_relation_link}'] = $rel_values['link'];

        return hooks()->apply_filters('staff_reminder_merge_fields', $fields, [
            'reminder' => $reminder,
        ]);
    }

    public function password($data, $type)
    {
        $fields['{reset_password_url}'] = '';
        $fields['{set_password_url}']   = '';

        if ($type == 'forgot') {
            $fields['{reset_password_url}'] = admin_url('authentication/reset_password/1/' . $data['userid'] . '/' . $data['new_pass_key']);
        }

        return $fields;
    }
}

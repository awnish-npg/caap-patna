<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_model extends App_Model
{
    public function delete($id, $transfer_data_to)
    {
        if (!is_numeric($transfer_data_to)) {
            return false;
        }

        if ($id == $transfer_data_to) {
            return false;
        }

        hooks()->do_action('before_delete_staff_member', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        $name           = get_staff_full_name($id);
        $transferred_to = get_staff_full_name($transfer_data_to);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'estimates', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('sale_agent', $id);
        $this->db->update(db_prefix() . 'invoices', [
            'sale_agent' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'expenses', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'notes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'newsfeed_post_comments', [
            'userid' => $transfer_data_to,
        ]);

        $this->db->where('creator', $id);
        $this->db->update(db_prefix() . 'newsfeed_posts', [
            'creator' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'projectdiscussions', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'projects', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'creditnotes', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'credits', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'proposal_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'proposals', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'task_comments', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->where('is_added_from_contact', 0);
        $this->db->update(db_prefix() . 'tasks', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'files', [
            'staffid' => $transfer_data_to,
        ]);

        $this->db->where('renewed_by_staff_id', $id);
        $this->db->update(db_prefix() . 'contract_renewals', [
            'renewed_by_staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('finished_from', $id);
        $this->db->update(db_prefix() . 'task_checklist_items', [
            'finished_from' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'ticket_replies', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('admin', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'admin' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'leads', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'leads', [
            'assigned' => $transfer_data_to,
        ]);

        $this->db->where('staff_id', $id);
        $this->db->update(db_prefix() . 'taskstimers', [
            'staff_id' => $transfer_data_to,
        ]);

        $this->db->where('addedfrom', $id);
        $this->db->update(db_prefix() . 'contracts', [
            'addedfrom' => $transfer_data_to,
        ]);

        $this->db->where('assigned_from', $id);
        $this->db->where('is_assigned_from_contact', 0);
        $this->db->update(db_prefix() . 'task_assigned', [
            'assigned_from' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'leads_email_integration', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('responsible', $id);
        $this->db->update(db_prefix() . 'web_to_lead', [
            'responsible' => $transfer_data_to,
        ]);

        $this->db->where('created_from', $id);
        $this->db->update(db_prefix() . 'subscriptions', [
            'created_from' => $transfer_data_to,
        ]);

        $this->db->where('notify_type', 'specific_staff');
        $web_to_lead = $this->db->get(db_prefix() . 'web_to_lead')->result_array();

        foreach ($web_to_lead as $form) {
            if (!empty($form['notify_ids'])) {
                $staff = unserialize($form['notify_ids']);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', $form['id']);
                            $this->db->update(db_prefix() . 'web_to_lead', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('id', 1);
        $leads_email_integration = $this->db->get(db_prefix() . 'leads_email_integration')->row();

        if ($leads_email_integration->notify_type == 'specific_staff') {
            if (!empty($leads_email_integration->notify_ids)) {
                $staff = unserialize($leads_email_integration->notify_ids);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', 1);
                            $this->db->update(db_prefix() . 'leads_email_integration', [
                                'notify_ids' => $staff,
                            ]);
                        }
                    }
                }
            }
        }

        $this->db->where('assigned', $id);
        $this->db->update(db_prefix() . 'tickets', [
            'assigned' => 0,
        ]);

        $this->db->where('staff', 1);
        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'dismissed_announcements');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_comment_likes');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'newsfeed_post_likes');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'customer_admins');

        $this->db->where('fieldto', 'staff');
        $this->db->where('relid', $id);
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'events');

        $this->db->where('touserid', $id);
        $this->db->delete(db_prefix() . 'notifications');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'user_meta');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_members');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'project_notes');

        $this->db->where('creator', $id);
        $this->db->or_where('staff', $id);
        $this->db->delete(db_prefix() . 'reminders');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff_departments');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'todos');

        $this->db->where('staff', 1);
        $this->db->where('user_id', $id);
        $this->db->delete(db_prefix() . 'user_auto_login');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'staff_permissions');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_assigned');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'task_followers');

        $this->db->where('staff_id', $id);
        $this->db->delete(db_prefix() . 'pinned_projects');

        $this->db->where('staffid', $id);
        $this->db->delete(db_prefix() . 'staff');
        log_activity('Staff Member Deleted [Name: ' . $name . ', Data Transferred To: ' . $transferred_to . ']');

        hooks()->do_action('staff_member_deleted', [
            'id'               => $id,
            'transfer_data_to' => $transfer_data_to,
        ]);

        return true;
    }

    /**
     * Get staff member/s
     * @param  mixed $id Optional - staff id
     * @param  mixed $where where in query
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $where = [])
    {
        $select_str = '*,CONCAT(firstname," ",lastname) as full_name';

        // Used to prevent multiple queries on logged in staff to check the total unread notifications in core/AdminController.php
        if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
            $select_str .= ',(SELECT COUNT(*) FROM ' . db_prefix() . 'notifications WHERE touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (SELECT COUNT(*) FROM ' . db_prefix() . 'todos WHERE finished=0 AND staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
        }

        $this->db->select($select_str);
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('staffid', $id);
            $staff = $this->db->get(db_prefix() . 'staff')->row();

            if ($staff) {
                $staff->permissions = $this->get_staff_permissions($id);
            }

            return $staff;
        }
        $this->db->order_by('firstname', 'desc');

        return $this->db->get(db_prefix() . 'staff')->result_array();
    }

    /**
     * Get staff permissions
     * @param  mixed $id staff id
     * @return array
     */
    public function get_staff_permissions($id)
    {
        // Fix for version 2.3.1 tables upgrade
        if (defined('DOING_DATABASE_UPGRADE')) {
            return [];
        }

        $permissions = $this->app_object_cache->get('staff-' . $id . '-permissions');

        if (!$permissions && !is_array($permissions)) {
            $this->db->where('staff_id', $id);
            $permissions = $this->db->get('staff_permissions')->result_array();

            $this->app_object_cache->add('staff-' . $id . '-permissions', $permissions);
        }

        return $permissions;
    }

    /**
     * Add new staff member
     * @param array $data staff $_POST data
     */
    public function add($data)
    {
        // if (isset($data['fakeusernameremembered'])) {
        //     unset($data['fakeusernameremembered']);
        // }
        // if (isset($data['fakepasswordremembered'])) {
        //     unset($data['fakepasswordremembered']);
        // }
        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get(db_prefix() . 'staff')->row();
        if ($email) {
            $message = _l('email_exists');
            return [
                'success' => false,
                'email_exist_as_staff' => true,
                'message'              => $message,
            ];
        }
        // First check for all cases if the phone number exists.
        
        // removing phone validation 
        // $this->db->where('phonenumber', $data['phonenumber']);
        // $phonenumber = $this->db->get(db_prefix() . 'staff')->row();
        // if ($phonenumber) {
        //     $message = _l('phonenumber_exists');
        //     return [
        //         'success' => false,
        //         'phonenumber_exists_as_staff' => true,
        //         'message'              => $message,
        //     ];
        // }
        // end
        $data['admin'] = 0;
        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            }
        }

        $send_welcome_email = true;
        $original_password  = $data['password'];
        if (!isset($data['send_welcome_email'])) {
            $send_welcome_email = false;
        } else {
            unset($data['send_welcome_email']);
        }

        $data['password']        = app_hash_password($data['password']);
        $data['datecreated']     = date('Y-m-d H:i:s');
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permissions = [];
        // if (isset($data['permissions'])) {
        //     $permissions = $data['permissions'];
        //     unset($data['permissions']);
        // }

        $role_data = $this->roles_model->get($data['role']);
        if (!empty($role_data->permissions)) {
            $permissions = $role_data->permissions;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if ($data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $this->db->insert(db_prefix() . 'staff', $data);
        $staffid = $this->db->insert_id();
        if ($staffid) {
            //$slug = $data['firstname'] . ' ' . $data['lastname'];
            $slug = $data['firstname'];

            if ($slug == ' ') {
                $slug = 'unknown-' . $staffid;
            }

            if ($send_welcome_email == true) {
                send_mail_template('staff_created', $data['email'], $staffid, $original_password);
            }

            $this->db->where('staffid', $staffid);
            $this->db->update(db_prefix() . 'staff', [
                'media_path_slug' => slug_it($slug),
            ]);

            if (isset($custom_fields)) {
                handle_custom_fields_post($staffid, $custom_fields);
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert(db_prefix() . 'staff_departments', [
                        'staffid'      => $staffid,
                        'departmentid' => $department,
                    ]);
                }
            }

            // Delete all staff permission if is admin we dont need permissions stored in database (in case admin check some permissions)
            $this->update_permissions($data['admin'] == 1 ? [] : $permissions, $staffid);

            log_activity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ']');

            // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from(db_prefix() . 'announcements');
            $this->db->where('showtostaff', 1);
            $announcements = $this->db->get()->result_array();
            foreach ($announcements as $announcement) {
                $this->db->insert(db_prefix() . 'dismissed_announcements', [
                    'announcementid' => $announcement['announcementid'],
                    'staff'          => 1,
                    'userid'         => $staffid,
                ]);
            }
            hooks()->do_action('staff_member_created', $staffid);

            return $staffid;
        }

        return false;
    }

    /**
     * Update staff member info
     * @param  array $data staff data
     * @param  mixed $id   staff id
     * @return boolean
     */
    public function update($data, $id)
    {
        // if (isset($data['fakeusernameremembered'])) {
        //     unset($data['fakeusernameremembered']);
        // }
        // if (isset($data['fakepasswordremembered'])) {
        //     unset($data['fakepasswordremembered']);
        // }

        $data = hooks()->apply_filters('before_update_staff_member', $data, $id);

        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            } else {
                if ($id != get_staff_user_id()) {
                    if ($id == 1) {
                        return [
                            'cant_remove_main_admin' => true,
                        ];
                    }
                } else {
                    return [
                        'cant_remove_yourself_from_admin' => true,
                    ];
                }
                $data['admin'] = 0;
            }
        }


        // check if email id was changed
        $send_welcome_email = false;
        $member = $this->staff_model->get_member(['staffid' => $data['id']])[0];
        if ($member['email'] != $data['email']) {
            $send_welcome_email = true;
            $password = random_password();
            $data["password"] = app_hash_password($password);
        }

        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $this->db->where('staffid !=', $data['id']);
        $email = $this->db->get(db_prefix() . 'staff')->row();
        if ($email) {
            $message = _l('email_exists');
            return [
                'success' => false,
                'email_exist_as_staff' => true,
                'message'              => $message,
            ];
        }

        // First check for all cases if the phone number exists.
        
        // $this->db->where('phonenumber', $data['phonenumber']);
        // $this->db->where('staffid !=', $data['id']);
        // $phonenumber = $this->db->get(db_prefix() . 'staff')->row();
        // if ($phonenumber) {
        //     $message = _l('phonenumber_exists');
        //     return [
        //         'success' => false,
        //         'phonenumber_exists_as_staff' => true,
        //         'message'              => $message,
        //     ];
        // }


        $affectedRows = 0;
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        $permissions = [];
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        if (isset($data['is_not_staff'])) {
            $data['is_not_staff'] = 1;
        } else {
            $data['is_not_staff'] = 0;
        }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        // $this->load->model('departments_model');
        // $staff_departments = $this->departments_model->get_staff_departments($id);
        // if (sizeof($staff_departments) > 0) {
        //     if (!isset($data['departments'])) {
        //         $this->db->where('staffid', $id);
        //         $this->db->delete(db_prefix() . 'staff_departments');
        //     } else {
        //         foreach ($staff_departments as $staff_department) {
        //             if (isset($departments)) {
        //                 if (!in_array($staff_department['departmentid'], $departments)) {
        //                     $this->db->where('staffid', $id);
        //                     $this->db->where('departmentid', $staff_department['departmentid']);
        //                     $this->db->delete(db_prefix() . 'staff_departments');
        //                     if ($this->db->affected_rows() > 0) {
        //                         $affectedRows++;
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     if (isset($departments)) {
        //         foreach ($departments as $department) {
        //             $this->db->where('staffid', $id);
        //             $this->db->where('departmentid', $department);
        //             $_exists = $this->db->get(db_prefix() . 'staff_departments')->row();
        //             if (!$_exists) {
        //                 $this->db->insert(db_prefix() . 'staff_departments', [
        //                     'staffid'      => $id,
        //                     'departmentid' => $department,
        //                 ]);
        //                 if ($this->db->affected_rows() > 0) {
        //                     $affectedRows++;
        //                 }
        //             }
        //         }
        //     }
        // } else {
        //     if (isset($departments)) {
        //         foreach ($departments as $department) {
        //             $this->db->insert(db_prefix() . 'staff_departments', [
        //                 'staffid'      => $id,
        //                 'departmentid' => $department,
        //             ]);
        //             if ($this->db->affected_rows() > 0) {
        //                 $affectedRows++;
        //             }
        //         }
        //     }
        // }

        unset($data['id']);
        //$data['password'] = app_hash_password($data['password']);
        //unset($data['password']);
        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($send_welcome_email)
                send_mail_template('staff_created', $data['email'], $id, $password);
        }

        $role_data = $this->roles_model->get($data['role']);
        if (!empty($role_data->permissions)) {
            $permissions = $role_data->permissions;
        }

        if ($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $permissions), $id)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            hooks()->do_action('staff_member_updated', $id);
            log_activity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ']');

            return true;
        }

        return false;
    }

    public function update_permissions($permissions, $id)
    {
        $this->db->where('staff_id', $id);
        $this->db->delete('staff_permissions');

        $is_staff_member = is_staff_member($id);

        foreach ($permissions as $feature => $capabilities) {
            foreach ($capabilities as $capability) {

                // Maybe do this via hook.
                if ($feature == 'leads' && !$is_staff_member) {
                    continue;
                }

                $this->db->insert('staff_permissions', ['staff_id' => $id, 'feature' => $feature, 'capability' => $capability]);
            }
        }

        return true;
    }

    public function update_profile($data, $id)
    {
        $data = hooks()->apply_filters('before_staff_update_profile', $data, $id);

        // if (empty($data['password'])) {
        //     unset($data['password']);
        // } else {
        //     $data['password']             = app_hash_password($data['password']);
        //     $data['last_password_change'] = date('Y-m-d H:i:s');
        // }

        // if (isset($data['two_factor_auth_enabled'])) {
        //     $data['two_factor_auth_enabled'] = 1;
        // } else {
        //     $data['two_factor_auth_enabled'] = 0;
        // }



        // First check for all cases if the phone number exists.
        $this->db->where('phonenumber', $data['phonenumber']);
        $this->db->where('staffid !=', $id);
        $phonenumber = $this->db->get(db_prefix() . 'staff')->row();
        if ($phonenumber) {
            $message = _l('phonenumber_exists');
            return [
                'success' => false,
                'phonenumber_exists_as_staff' => true,
                'message'              => $message,
            ];
        }

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', $data);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('staff_member_profile_updated', $id);
            log_activity('Staff Profile Updated [Staff: ' . get_staff_full_name($id) . ']');
            return [
                'success' => true,
                'message' => _l('staff_profile_updated'),
            ];
        }

        return false;
    }

    /**
     * Change staff passwordn
     * @param  mixed $data   password data
     * @param  mixed $userid staff id
     * @return mixed
     */
    public function change_password($data, $userid)
    {
        $data = hooks()->apply_filters('before_staff_change_password', $data, $userid);

        $member = $this->get($userid);
        // CHeck if member is active
        if ($member->active == 0) {
            return [
                [
                    'memberinactive' => true,
                ],
            ];
        }

        // Check new old password
        if (!app_hasher()->CheckPassword($data['oldpassword'], $member->password)) {
            return [
                [
                    'passwordnotmatch' => true,
                ],
            ];
        }

        $data['newpasswordr'] = app_hash_password($data['newpasswordr']);

        $this->db->where('staffid', $userid);
        $this->db->update(db_prefix() . 'staff', [
            'password'             => $data['newpasswordr'],
            'last_password_change' => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('Staff Password Changed [' . $userid . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id     staff id
     * @param  mixed $status status(0/1)
     */
    public function change_staff_status($id, $status)
    {
        $status = hooks()->apply_filters('before_staff_status_change', $status, $id);

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', [
            'active' => $status,
        ]);

        log_activity('Staff Status Changed [StaffID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function get_logged_time_data($id = '', $filter_data = [])
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }
        $result['timesheets'] = [];
        $result['total']      = [];
        $result['this_month'] = [];

        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t 23:59:59');

        $result['last_month'] = [];
        $first_day_last_month = date('Y-m-01', strtotime('-1 MONTH')); // hard-coded '01' for first day
        $last_day_last_month  = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));

        $result['this_week'] = [];
        $first_day_this_week = date('Y-m-d', strtotime('monday this week'));
        $last_day_this_week  = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $result['last_week'] = [];

        $first_day_last_week = date('Y-m-d', strtotime('monday last week'));
        $last_day_last_week  = date('Y-m-d 23:59:59', strtotime('sunday last week'));

        $this->db->select('task_id,start_time,end_time,staff_id,' . db_prefix() . 'taskstimers.hourly_rate,name,' . db_prefix() . 'taskstimers.id,rel_id,rel_type, billed');
        $this->db->where('staff_id', $id);
        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'taskstimers.task_id', 'left');
        $timers           = $this->db->get(db_prefix() . 'taskstimers')->result_array();
        $_end_time_static = time();

        $filter_period = false;
        if (isset($filter_data['period-from']) && $filter_data['period-from'] != '' && isset($filter_data['period-to']) && $filter_data['period-to'] != '') {
            $filter_period = true;
            $from          = to_sql_date($filter_data['period-from']);
            $from          = date('Y-m-d', strtotime($from));
            $to            = to_sql_date($filter_data['period-to']);
            $to            = date('Y-m-d', strtotime($to));
        }

        foreach ($timers as $timer) {
            $start_date = strftime('%Y-%m-%d', $timer['start_time']);

            $end_time    = $timer['end_time'];
            $notFinished = false;
            if ($timer['end_time'] == null) {
                $end_time    = $_end_time_static;
                $notFinished = true;
            }

            $total = $end_time - $timer['start_time'];

            $result['total'][]     = $total;
            $timer['total']        = $total;
            $timer['end_time']     = $end_time;
            $timer['not_finished'] = $notFinished;

            if ($start_date >= $first_day_this_month && $start_date <= $last_day_this_month) {
                $result['this_month'][] = $total;
                if (isset($filter_data['this_month']) && $filter_data['this_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_month && $start_date <= $last_day_last_month) {
                $result['last_month'][] = $total;
                if (isset($filter_data['last_month']) && $filter_data['last_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_this_week && $start_date <= $last_day_this_week) {
                $result['this_week'][] = $total;
                if (isset($filter_data['this_week']) && $filter_data['this_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_week && $start_date <= $last_day_last_week) {
                $result['last_week'][] = $total;
                if (isset($filter_data['last_week']) && $filter_data['last_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }

            if ($filter_period == true) {
                if ($start_date >= $from && $start_date <= $to) {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
        }
        $result['total']      = array_sum($result['total']);
        $result['this_month'] = array_sum($result['this_month']);
        $result['last_month'] = array_sum($result['last_month']);
        $result['this_week']  = array_sum($result['this_week']);
        $result['last_week']  = array_sum($result['last_week']);

        return $result;
    }

    public function get_member($data)
    {
        $this->db->where($data);
        return $this->db->get('staff')->result_array();
    }

    public function save($data)
    {
        $original_password = $data['password'];
        $data['password'] = app_hash_password($data['password']);
        $this->db->insert('staff', $data);
        $staff_id = $this->db->insert_id();
        send_mail_template('staff_created', $data['email'], $staff_id, $original_password);
        return $staff_id;
    }

    public function update_staff($data, $id)
    {
        $send_welcome_email = false;
        if (!isset($data['send_welcome_email'])) {
            $send_welcome_email = false;
        } else {
            $send_welcome_email = true;
            $password = random_password();
            $data["password"] = app_hash_password($password);
            unset($data['send_welcome_email']);
        }
        $this->db->where('staffid', $id);
        $this->db->update('staff', $data);

        if ($this->db->affected_rows() > 0) {
            if ($send_welcome_email)
                send_mail_template('staff_created', $data['email'], $id, $password);
            return true;
        }
        return false;
    }

    public function save_staff_region($data, $is_batched = true)
    {
        if (!$is_batched)
            $this->db->insert('staff_region', $data);
        else
            $this->db->insert_batch('staff_region', $data);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }


    public function delete_staff_data($staff_id, $table)
    {
        $this->db->where("staff_id", $staff_id);
        $this->db->delete($table);
    }

    public function get_area_reviewers($area_id)
    {
        $this->db->select('s.firstname as name, s.staffid, s.organisation');
        $this->db->from('roles r');
        $this->db->join('staff s', 's.role = r.roleid AND r.slug_url = "ar" AND s.area = ' . $area_id);
        $this->db->where("s.active = 1");
        $result = $this->db->get()->result_array();
        if (count($result) > 0)
            return $result;
        return false;
    }

    public function get_action_taker($data)
    {
        $this->db->select('s.firstname as name,CONCAT(s.firstname," ",s.lastname) as full_name, s.staffid, s.organisation,s.role');
        $this->db->from('roles r');
        $this->db->join('staff s', 's.role = r.roleid AND r.slug_url = "at" AND s.area = ' . $data['area_id']);
        if (!empty($data['region']) && !empty($data['sub_region'])) {
            $this->db->join('staff_region sr', 's.staffid = sr.staff_id  AND sr.region = ' . $data['region'] . ' AND sr.sub_region =  ' . $data['sub_region']);
        }
        $this->db->where("s.active", 1);
        // $this->db->get()->result_array();
        // echo $this->db->last_query(); die;
        $result = $this->db->get()->result_array();

        if (count($result) > 0)
            return $result;
        return false;
    }

    public function save_action_taker_category($data)
    {
        $this->db->insert_batch('staff_issues', $data);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    public function remove_staff_issue($id){
        $this->db->delete('staff_issues', array('staff_id' => $id));
    }
    public function save_action_taker_reviewer($data)
    {
        $this->db->insert('staff_assistance', $data);
        if ($this->db->affected_rows()) {
            return true;
        }
        return false;
    }

    public function get_ata($id)
    {
        $select_str = 's.staffid,s.firstname,s.email,s.organisation,s.phonenumber,CONCAT(firstname," ",lastname) as full_name,sa.assistant_id,sr.region,sr.sub_region';

        $this->db->select($select_str);
        $this->db->from('staff s');
        $this->db->join('staff_assistance sa', 's.staffid = sa.staff_id ', 'left');
        $this->db->join('staff_region sr', 's.staffid = sr.staff_id ', 'left');
        $this->db->where('staffid', $id);
        $result = $this->db->get()->row();
        return $result;
    }

    public function get_action_reviewer_takers($id)
    {
        $this->db->select("s.staffid as staff_id, s.firstname as at_name, s.organisation, s.phonenumber, s.email, r.region_name as region_name, srg.region_name as sub_region_name");
        $this->db->from("staff_assistance as sa");
        $this->db->join("staff as s", "s.staffid = sa.staff_id", "left");
        $this->db->join("staff_region as sr", "sr.staff_id = sa.staff_id", "left");
        $this->db->join("region as r", "r.id = sr.region", "left");
        $this->db->join("sub_region as srg", "srg.id = sr.sub_region", "left");
        $this->db->where("sa.assistant_id", $id);
        $this->db->where("s.active = '1'");
        $this->db->order_by('sa.created_at', 'DESC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_action_reviewer_action_takers($id)
    {
        $this->db->select("s.staffid as staff_id, s.firstname as at_name, s.organisation, s.phonenumber, s.email, r.region_name as region_name, srg.region_name as sub_region_name");
        $this->db->from("staff_assistance as sa");
        $this->db->join("staff as s", "s.staffid = sa.staff_id", "left");
        $this->db->join("staff_region as sr", "sr.staff_id = sa.staff_id", "left");
        $this->db->join("region as r", "r.id = sr.region", "left");
        $this->db->join("sub_region as srg", "srg.id = sr.sub_region", "left");
        $this->db->where("sa.assistant_id", $id);
        // $this->db->where("s.active = '1'");
        $this->db->order_by('sa.created_at', 'DESC');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_staff_assistance($id)
    {
        $select_str = 's.staffid,s.firstname,s.email,s.organisation,s.phonenumber,CONCAT(firstname," ",lastname) as full_name,sa.assistant_id,sr.region,sr.sub_region';

        $this->db->select($select_str);
        $this->db->from('staff_assistance sa');
        $this->db->join('staff s', 's.staffid = sa.staff_id', 'left');
        $this->db->join('staff_region sr', 's.staffid = sr.staff_id ', 'left');
        $this->db->where('sa.assistant_id', $id);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_userDetails($staffid)
    {
        $select_str = 's.staffid,s.firstname,s.email,s.organisation,s.phonenumber,CONCAT(firstname," ",lastname) as full_name,s.area,GROUP_CONCAT(sr.region) as region ,GROUP_CONCAT(sr.sub_region) as sub_region,s.role,r.name as role_name';

        $this->db->select($select_str);
        $this->db->from('staff s');
        $this->db->join('staff_region sr', 's.staffid = sr.staff_id ', 'left');
        $this->db->join('roles r', 's.role = r.roleid ', 'left');
        $this->db->where('s.staffid', $staffid);
        $this->db->group_by('s.staffid');
        $result = $this->db->get()->row();
        return $result;
    }

    public function get_reporting_person($id)
    {
        $select_str = 's.staffid,s.firstname,s.email,s.organisation,s.phonenumber,CONCAT(firstname," ",lastname) as full_name,sa.assistant_id,sr.region,sr.sub_region';

        $this->db->select($select_str);
        $this->db->from('staff s');
        $this->db->join('staff_assistance sa', 's.staffid = sa.assistant_id ', 'left');
        $this->db->join('staff_region sr', 's.staffid = sr.staff_id ', 'left');
        $this->db->where('sa.staff_id', $id);
        $result = $this->db->get()->row();
        return $result;
    }

    public function get_area_staff($area_id)
    {
        $this->db->select("str.staff_id");
        $this->db->from("region as r");
        $this->db->join("staff_region as str", "str.region = r.id", "LEFT");
        $this->db->where("r.area_id", $area_id);
        $this->db->group_by("str.staff_id");
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_region_staff($condition)
    {
        $this->db->select("sr.staff_id, r.slug_url");
        $this->db->from("staff_region as sr");
        $this->db->join("staff as s", "sr.staff_id = s.staffid", "LEFT");
        $this->db->join("roles as r", "r.roleid = s.role", "LEFT");
        $this->db->where($condition);
        $this->db->group_by("staff_id");
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function update_status_by_area($area_id, $status)
    {
        $this->db->where('area', $area_id);
        $this->db->update(db_prefix() . 'staff', [
            'active' => $status,
        ]);
    }

    public function deactivate_staff($staff_data, $status)
    {
        foreach ($staff_data as $staff) {

            if ($staff["slug_url"] != "ae-area") {
                $this->db->where("staffid", $staff["staff_id"]);
                $this->db->update("staff", ["active" => $status]);
            }

            if ($staff["slug_url"] == "at") {
                // get associated AR id
                $this->db->select("assistant_id");
                $this->db->from("staff_assistance");
                $this->db->where("staff_id", $staff["staff_id"]);
                $reviewer = $this->db->get()->row();

                // get all ATs for this AR
                if ($reviewer) {

                    $this->db->select("staff_id");
                    $this->db->from("staff_assistance");
                    $this->db->where("assistant_id", $reviewer->assistant_id);
                    $all_ATs = $this->db->get()->result_array();

                    // get all deactivated ATs for this AR
                    if (count($all_ATs) > 0) {
                        $active_AT_count = 0;
                        foreach ($all_ATs as $at) {
                            $this->db->select("active");
                            $this->db->from("staff");
                            $this->db->where("staffid", $at["staff_id"]);
                            if ($this->db->get()->row()->active == $status)
                                $active_AT_count++;
                        }

                        // deactivate AR if all ATs are deactivate
                        if ($active_AT_count == count($all_ATs)) {
                            $this->db->where(["staffid" =>  $reviewer->assistant_id]);
                            $this->db->update("staff", ["active" => $status]);
                        }
                    }
                }
            } else if ($staff["slug_url"] == "ae-area") {

                // get AR regions
                $this->db->select("region");
                $this->db->from("staff_region");
                $this->db->where("staff_id", $staff["staff_id"]);
                $ae_regions = $this->db->get()->result_array();

                // get all deactivated regions for this AE
                if (count($ae_regions) > 0) {
                    $active_ae_regions = 0;
                    foreach ($ae_regions as $region) {
                        $this->db->select("status");
                        $this->db->from("region");
                        $this->db->where("id", $region["region"]);
                        if ($this->db->get()->row()->status == $status) {
                            $active_ae_regions++;
                        }
                    }

                    if ($active_ae_regions == count($ae_regions)) {
                        $this->db->where("staffid", $staff["staff_id"]);
                        $this->db->update("staff", ["active" => $status]);
                    }
                }
            }
        }
    }

    public function check_staff_projects_status($staff_id)
    {
        // $this->db->select("p.id");
        $this->db->from("project_members as pm");
        $this->db->join("projects as p", "pm.project_id = p.id AND p.status NOT IN (3,5)", "RIGHT");
        $this->db->where("pm.staff_id", $staff_id);
        $this->db->where("pm.active", 1);
        // $result = $this->db->get()->result_array();
        $result = $this->db->count_all_results();
        // if (!empty($result)) {
        //     return true;
        // }
        // return false;
        return ($result > 0) ? true : false;
    }

    public function check_staff_region_status($staff_id, $condition)
    {
        $this->db->select("sr.staff_id, sr.region");
        $this->db->from("staff_region sr");
        $this->db->join("region r", "r.id = sr.region", "LEFT");
        $this->db->where("sr.staff_id", $staff_id);
        $this->db->where($condition);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function check_staff_subregion_status($staff_id, $condition)
    {
        $this->db->select("sr.staff_id, sr.sub_region");
        $this->db->from("staff_region sr");
        $this->db->join("sub_region r", "r.id = sr.sub_region", "LEFT");
        $this->db->where("sr.staff_id", $staff_id);
        $this->db->where($condition);
        $result = $this->db->get()->result_array();
        return $result;
    }
    

    public function get_active_ats($id)
    {
        $this->db->from('staff_assistance sa');
        $this->db->join('staff s', 's.staffid = sa.staff_id', 'left');
        $this->db->join('staff_region sr', 's.staffid = sr.staff_id ', 'left');
        $this->db->where('sa.assistant_id', $id);
        $this->db->where("s.active = '1'");
        // $result = $this->db->get()->result_array();
        $result = $this->db->count_all_results();

        return ($result > 0) ? true : false;
    }
}

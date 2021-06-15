<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('clients_authentication_constructor', $this);
    }

    public function index()
    {
        //$this->login();
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }
        $this->admin();
    }

    // Added for backward compatibilies
    public function admin()
    {
        redirect(admin_url('authentication'));
    }

    public function login()
    {
        // redirect(admin_url('authentication'));
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }
        if (is_client_logged_in()) {
            redirect(site_url('clients/open_ticket'));
        }

        $this->form_validation->set_rules('password', _l('clients_login_password'), 'required');
        $this->form_validation->set_rules('email', _l('clients_login_email'), 'trim|required|valid_email');

        if (get_option('use_recaptcha_customers_area') == 1
            && get_option('recaptcha_secret_key') != ''
            && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }
        if ($this->form_validation->run() !== false) {
            $this->load->model('Authentication_model');
            $res=$this->Authentication_model->checkforstaff($this->input->post('email'));
            if($res==true){
                set_alert('danger', _l('Admin Cannot Login Here'));
                redirect(site_url('authentication/login'));
            }
            $success = $this->Authentication_model->login(
                $this->input->post('email'),
                $this->input->post('password', false),
                $this->input->post('remember'),
                false
            );

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', _l('inactive_account'));
                redirect(site_url('authentication/login'));
            } elseif ($success == false) {
                set_alert('danger', _l('client_invalid_username_or_password'));
                redirect(site_url('authentication/login'));
            }

            $this->load->model('announcements_model');
            $this->announcements_model->set_announcements_as_read_except_last_one(get_contact_user_id());

            hooks()->do_action('after_contact_login');

            // maybe_redirect_to_previous_url();
            redirect(site_url('clients/open_ticket'));
        }
        if (get_option('allow_registration') == 1) {
            $data['title'] = _l('clients_login_heading_register');
        } else {
            $data['title'] = _l('clients_login_heading_no_register');
        }
        $data['bodyclass'] = 'customers_login';

        $this->data($data);
        $this->view('login');
        $this->layout();
    }
//
public function check_admin($str){
    $res=checkadmin($str);
    if($res==true){
        $this->form_validation->set_message('check_admin', 'Admins can not register as Surveyors.');
        return FALSE;
    }
    return true;
}
public function registerccr()
{  
    if (get_option('allow_registration') != 1 || is_client_logged_in()) {
        redirect(site_url());
    }
    $this->session->set_flashdata('type','ccr');
    // if (get_option('company_is_required') == 1) {
    //     $this->form_validation->set_rules('company', _l('client_company'), 'required');
    // }

    $this->form_validation->set_rules('company', _l('company_name'), 'required|max_length[125]');
    $this->form_validation->set_rules('firstname', _l('Name'), 'required|alpha_numeric_spaces|max_length[50]');
    // $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required|alpha');
    $this->form_validation->set_rules('email', _l('client_email'), 'trim|required|max_length[50]|valid_email|is_unique[' . db_prefix() . 'contacts.email]|valid_email|callback_check_admin');
    $this->form_validation->set_rules('password', _l('clients_register_password'), 'required|callback_valid_password|max_length[17]|min_length[8]');
    $this->form_validation->set_rules('passwordr', _l('clients_register_password_confirm'), 'required|matches[password]');
    $this->form_validation->set_rules('contact_phonenumber', _l('clients_contact_number'), 'required|max_length[12]|min_length[10]');

    if (get_option('use_recaptcha_customers_area') == 1
        && get_option('recaptcha_secret_key') != ''
        && get_option('recaptcha_site_key') != '') {
        $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
    }

    $custom_fields = get_custom_fields('customers', [
        'show_on_client_portal' => 1,
        'required'              => 1,
    ]);

    $custom_fields_contacts = get_custom_fields('contacts', [
        'show_on_client_portal' => 1,
        'required'                 => 1,
    ]);

    foreach ($custom_fields as $field) {
        $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
        if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
            $field_name .= '[]';
        }
        $this->form_validation->set_rules($field_name, $field['name'], 'required');
    }
    foreach ($custom_fields_contacts as $field) {
        $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
        if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
            $field_name .= '[]';
        }
        $this->form_validation->set_rules($field_name, $field['name'], 'required');
    }
    if ($this->input->post()) {
        if ($this->form_validation->run() !== false) {
            $data = $this->input->post();

            define('CONTACT_REGISTERING', true);

            $clientid = $this->clients_model->add([

                  'firstname'           => $data['firstname'],
                //   'lastname'            => $data['lastname'],
                  'email'               => $data['email'],
                  'company'             => $data['company'],
                  'contact_phonenumber' => $data['contact_phonenumber'] ,
                  'password'            => $data['passwordr'],
                  'is_cc'               =>1,
                  'custom_fields'       => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
            ], true);

            if ($clientid) {
                hooks()->do_action('after_client_register', $clientid);

                if (get_option('customers_register_require_confirmation') == '1') {
                    send_customer_registered_email_to_administrators($clientid);

                    $this->clients_model->require_confirmation($clientid);
                    set_alert('success', _l('customer_register_account_confirmation_approval_notice'));
                    redirect(site_url('authentication/login'));
                }

                $this->load->model('authentication_model');

                $logged_in = $this->authentication_model->login(
                    $this->input->post('email'),
                    $this->input->post('password', false),
                    false,
                    false
                );

                $redUrl = site_url('clients/open_ticket');

                if ($logged_in) {
                    hooks()->do_action('after_client_register_logged_in', $clientid);
                    set_alert('success', _l('clients_successfully_registered'));
                    // 
                    // $ch  =  curl_init();
                    // $timeout  =  30; 
                    // $message=$this->load->view('sms_templates/onboard',[],true);
                    // $url=SMS_API_URL.'&number='.$data['contact_phonenumber'].'&text='.urlencode($message).'&route=05';
                    // curl_setopt ($ch,CURLOPT_URL, $url);
                    // curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
                    // curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                    // curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
                    // $response = curl_exec($ch) ;
                    // curl_close($ch) ;
                    //                     
                } else {
                    set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                    $redUrl = site_url('authentication/login');
                }

                send_customer_registered_email_to_administrators($clientid);
                redirect($redUrl);
            }
        }

    }

    $data['title']     = _l('clients_register_heading');
    $data['bodyclass'] = 'register';
    $this->data($data);
    $this->view('register');
    $this->layout();
}


// 
public function valid_password($password)
{   
    $password = trim($password);
    $regex_lowercase = '/[a-z]/';
    $regex_uppercase = '/[A-Z]/';
    $regex_number = '/[0-9]/';
    $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
    if (empty($password))
    {
        $this->form_validation->set_message('valid_password', 'The {field} field is required.');
        return FALSE;
    }
    if (preg_match_all($regex_lowercase, $password) < 1)
    {
        $this->form_validation->set_message('valid_password', 'The {field} field must be at least one lowercase letter.');
        return FALSE;
    }
    if (preg_match_all($regex_uppercase, $password) < 1)
    {
        $this->form_validation->set_message('valid_password', 'The {field} field must be at least one uppercase letter.');
        return FALSE;
    }
    if (preg_match_all($regex_number, $password) < 1)
    {
        $this->form_validation->set_message('valid_password', 'The {field} field must have at least one number.');
        return FALSE;
    }
    if (preg_match_all($regex_special, $password) < 1)
    {
        $this->form_validation->set_message('valid_password', 'The {field} field must have at least one special character.');
        return FALSE;
    }
    return TRUE;

}
    public function register()
    {
        if (get_option('allow_registration') != 1 || is_client_logged_in()) {
            redirect(site_url());
        }

        // if (get_option('company_is_required') == 1) {
        //     $this->form_validation->set_rules('company', _l('client_company'), 'required');
        // }
        $this->form_validation->set_rules('firstname', _l('Name'), 'required|alpha_numeric_spaces|max_length[50]');
        $this->form_validation->set_rules('company', _l('company_name'), 'required|max_length[125]');
        // $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required|alpha');
        $this->form_validation->set_rules('email', _l('client_email'), 'trim|required|max_length[50]|valid_email|is_unique[' . db_prefix() . 'contacts.email]|valid_email|callback_check_admin');
        $this->form_validation->set_rules('password', _l('clients_register_password'), 'required|callback_valid_password|max_length[17]|min_length[8]');
        $this->form_validation->set_rules('passwordr', _l('clients_register_password_confirm'), 'required|matches[password]');
        $this->form_validation->set_rules('contact_phonenumber', _l('clients_contact_number'), 'required|max_length[12]|min_length[10]');
        $this->form_validation->set_message('callback_password_check', 'callback_password_check');

        if (get_option('use_recaptcha_customers_area') == 1
            && get_option('recaptcha_secret_key') != ''
            && get_option('recaptcha_site_key') != '') {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }

        $custom_fields = get_custom_fields('customers', [
            'show_on_client_portal' => 1,
            'required'              => 1,
        ]);

        $custom_fields_contacts = get_custom_fields('contacts', [
            'show_on_client_portal' => 1,
            'required'                 => 1,
        ]);

        foreach ($custom_fields as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        foreach ($custom_fields_contacts as $field) {
            $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                $field_name .= '[]';
            }
            $this->form_validation->set_rules($field_name, $field['name'], 'required');
        }
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                define('CONTACT_REGISTERING', true);

                $clientid = $this->clients_model->add([

                      'firstname'           => $data['firstname'],
                    //   'lastname'            => $data['lastname'],
                      'email'               => $data['email'],
                      'company'             => $data['company'],
                      'contact_phonenumber' => $data['contact_phonenumber'] ,
                      'password'            => $data['passwordr'],
                      'is_cc'               =>0,
                      'custom_fields'       => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
                ], true);

                if ($clientid) {
                    hooks()->do_action('after_client_register', $clientid);

                    if (get_option('customers_register_require_confirmation') == '1') {
                        send_customer_registered_email_to_administrators($clientid);

                        $this->clients_model->require_confirmation($clientid);
                        set_alert('success', _l('customer_register_account_confirmation_approval_notice'));
                        redirect(site_url('authentication/login'));
                    }

                    $this->load->model('authentication_model');

                    $logged_in = $this->authentication_model->login(
                        $this->input->post('email'),
                        $this->input->post('password', false),
                        false,
                        false
                    );

                    $redUrl = site_url('clients/open_ticket');

                    if ($logged_in) {
                        hooks()->do_action('after_client_register_logged_in', $clientid);
                        set_alert('success', _l('clients_successfully_registered'));
                        // 
                        // $ch  =  curl_init();
                        // $timeout  =  30; 
                        // $message=$this->load->view('sms_templates/onboard',[],true);
                        // $url=SMS_API_URL.'&number='.$data['contact_phonenumber'].'&text='.urlencode($message).'&route=05';
                        // curl_setopt ($ch,CURLOPT_URL, $url);
                        // curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);
                        // curl_setopt ($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
                        // curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT, $timeout) ;
                        // $response = curl_exec($ch) ;
                        // curl_close($ch);
                        // 
                    } else {
                        set_alert('warning', _l('clients_account_created_but_not_logged_in'));
                        $redUrl = site_url('authentication/login');
                    }

                    send_customer_registered_email_to_administrators($clientid);
                    redirect($redUrl);
                }
            }

        }

        $data['title']     = _l('clients_register_heading');
        $data['bodyclass'] = 'register';
        $this->data($data);
        $this->view('register');
        $this->layout();
    }

    public function forgot_password()
    {
        if (is_client_logged_in()) {
            redirect(site_url());
        }

        $this->form_validation->set_rules(
            'email',
            _l('customer_forgot_password_email'),
            'trim|required|valid_email|callback_contact_email_exists'
        );

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $this->load->model('Authentication_model');
                $res=$this->Authentication_model->checkforstaff($this->input->post('email'));
                if($res==true){
                    set_alert('danger', _l('Admins can not reset password from here'));
                    redirect(site_url('authentication/login'));
                }
                $success = $this->Authentication_model->forgot_password_surveyor($this->input->post('email'));
                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('inactive_account'));
                } elseif ($success == true) {
                    set_alert('success', _l('check_email_for_resetting_password'));
                } else {
                    set_alert('danger', _l('error_setting_new_password_key'));
                }
                redirect(site_url('authentication/forgot_password'));
            }
        }
        $data['title'] = _l('customer_forgot_password');
        $this->data($data);
        $this->view('forgot_password');

        $this->layout();
    }

    public function reset_password($staff, $userid, $new_pass_key)
    {
        $this->load->model('Authentication_model');
        if (!$this->Authentication_model->can_reset_password($staff, $userid, $new_pass_key)) {
            set_alert('danger', _l('password_reset_key_expired'));
            redirect(site_url('authentication/login'));
        }

        $this->form_validation->set_rules('password', _l('customer_reset_password'), 'required|callback_valid_password');
        $this->form_validation->set_rules('passwordr', _l('customer_reset_password_repeat'), 'required|matches[password]');
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                hooks()->do_action('before_user_reset_password', [
                    'staff'  => $staff,
                    'userid' => $userid,
                ]);
                $success = $this->Authentication_model->reset_password(
                        0,
                        $userid,
                        $new_pass_key,
                        $this->input->post('passwordr', false)
                );
                if (is_array($success) && $success['expired'] == true) {
                    set_alert('danger', _l('password_reset_key_expired'));
                } elseif ($success == true) {
                    hooks()->do_action('after_user_reset_password', [
                        'staff'  => $staff,
                        'userid' => $userid,
                    ]);
                    set_alert('success', _l('password_reset_message'));
                } else {
                    set_alert('danger', _l('password_reset_message_fail'));
                }
                redirect(site_url('authentication/login'));
            }
        }
        $data['title'] = _l('admin_auth_reset_password_heading');
        $this->data($data);
        $this->view('reset_password');
        $this->layout();
    }

    public function logout()
    {
        $this->load->model('authentication_model');
        $this->authentication_model->logout(false);
        hooks()->do_action('after_client_logout');
        redirect(site_url('authentication/login'));
    }

    public function contact_email_exists($email = '')
    {
        $this->db->where('email', $email);
        $total_rows = $this->db->count_all_results(db_prefix() . 'contacts');

        if ($total_rows == 0) {
            $this->form_validation->set_message('contact_email_exists', _l('auth_reset_pass_email_not_found'));

            return false;
        }

        return true;
    }

    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }
}

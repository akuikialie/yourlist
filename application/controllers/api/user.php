<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once ( APPPATH . '/libraries/REST_Controller.php');

class user extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_m');
        $this->load->model('user_m');
    }

    public function user_profile_get()
    {
        $username = $this->get('username');
        
        if (!$this->input->server('HTTP_X_API_KEY')) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Key'));
            return;
        }

        $key = $this->input->server('HTTP_X_API_KEY');

        if (empty($username)) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }

        $checkKey = $this->auth_m->check_key($key);

        if ($checkKey) {
            $getUser = $this->user_m->get_user($username);
//            $getUser = $this->user_m->get_single('tbl_user', 'email', $username);
            if ($getUser) {
                unset($getUser->password);

                $this->response(array('status' => 1, 'user' => $getUser));
            } else {
                $this->response(array('status' => 0, 'error' => "Your Username isn't Valid!"));
            }
        } else {
            $this->response(array('status' => 0, 'error' => "Your Key isn't Valid!"));
        }
    }

}

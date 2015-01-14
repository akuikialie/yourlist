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

    /**
     * Check Input User Key GET
     * 
     * @return key string
     */
    private function _checkInputGetKey()
    {
        $key = $this->get('key');
        
        if (empty($key) || !isset($key)) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Key'));
        }
        
        $checkKeyValid = $this->user_m->checkKey($key); 
        
        if (!$checkKeyValid) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Key'));
        }
        return $key;
        /*if (!$this->input->server('HTTP_X_API_KEY')) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Key'));
            return;
        }
        return $this->input->server('HTTP_X_API_KEY');*/
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


    public function get_all_manager_get()
    {
        /** Check Input User Key **/
        $userKey = $this->_checkInputGetKey();

        $user = $this->user_m->getUserByKey($userKey);

        $getUser = $this->user_m->get_datas('tbl_user', 2, 'id_level');

        if ($getUser) {
            unset($getUser->password);

            $this->response(array('status' => 1, 'user' => $getUser));
        } else {
            $this->response(array('status' => 0, 'error' => "Unsuccessfull Get Data!!"));
        }
    }

}

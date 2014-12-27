<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once ( APPPATH . '/libraries/REST_Controller.php');

class auth extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_m');
        $this->load->model('user_m');
    }

    public function login_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');
    
        if (empty($username) || !isset($username) || $username == " ") {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }

        if (empty($password) || !isset($password) || $password == " ") {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }
 
        /** Check Username is in already in Database **/
        if ($this->_checkUsername($username)) {
            /** Check Username & Password in Database **/
            $checkLogin = $this->auth_m->login($username, $password);
            if ($checkLogin) {
                /** Get User Data **/
                $dataUser = $this->user_m->getUser($username);

                $this->response(array('status' => 1, 'data' => $dataUser));
            } else {
                $this->response(array('status' => 0, 'error' => 'Your Account is not Valid'));
            }
        } else {
            $this->response(array('status' => 0, 'error' => 'Your Account is not Valid'));
        }
    }
    
    public function register_post()
    {
        $username        = $this->post('username');
        $password        = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $email           = $this->post('email');
        $phone           = $this->post('phone');
        $creator         = $this->post('creator');

        if (empty($username) || !isset($username) || $username == " ") {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }

        if (empty($password) || !isset($password) || $password == " ") {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }

        if (empty($confirmPassword) || !isset($confirmPassword) || $confirmPassword == " ") {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
        }
        
        if ($password !== $confirmPassword) {
            $this->response(array('status' => 0, 'error' => 'Please Fill Your Password Correctly'));
        }
        
        /** Check Username is in already in Database **/
        if ($this->_checkUsername($username)) {
            $this->response(array('status' => 0, 'error' => 'Your Account is not Valid'));
        } else {
            $userData = array(
                'username'  => $username,
                'password'  => md5(md5($password)),
                'email'     => !empty($email) ? $email : null,
                'phone'     => !empty($phone) ? $phone : null,
                'id_level'  => 3,
                'id_status' => 1,
                'activate'  => 1,
                'creator'   => !empty($creator) ? (int) $creator : 1,
                'created'   => date('Y-m-d H:i:s'),
            );
            
            /** Insert Data User into Database **/
            $userId = $this->user_m->insert('tbl_user', $userData); 
            /** Generate User Key **/
            $userKey = $this->_generateKey();
            /** Save User Key **/
            $keyId = $this->_insertKey($userKey, array('level' => 1, 'ignore_limits' => 1));
            /** Update User Id Key **/
            $this->user_m->update('tbl_user', 'id_user', $userId, array('id_key' => $keyId));
            
            $this->response(array('status' => 1, 'message' => 'Register Success'));
        }
        $this->response(array('status' => 0, 'message' => 'Register Failed'));
    }
    
    /**
     * Check Username
     * @param String
     * @return boolean
     */
    private function _checkUsername($username)
    {
        $checkUsername = $this->user_m->checkUsername($username);
        return $checkUsername;   
    }
    
    /**
     * Generate Key
     *
     * @return string
     */
    private function _generateKey()
    {
        $this->load->helper('security');

        do
        {
            $salt = do_hash(time().mt_rand());
            $new_key = substr($salt, 0, config_item('rest_key_length'));
        }

        while ($this->_key_exists($new_key));

        return $new_key;
    }
    
    /**
     * Check Key
     * @param key string
     * @return boolean
     */
    private function _key_exists($key)
    {
        return $this->user_m->checkKey($key);
    }
    
    /*
     * Insert Key
     * @param key string , data array
     * @return id_key integer
     */
    private function _insertKey($key, $data)
    {   
        return $this->user_m->insertKey($key, $data);
    }

}

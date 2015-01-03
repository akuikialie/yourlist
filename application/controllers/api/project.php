<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once ( APPPATH . '/libraries/REST_Controller.php');

class project extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
        $this->load->model('project_m');
    }
    
    /**
     * Check Input User Key
     * 
     * @return key string
     */
    private function _checkInputKey()
    {
        $key = $this->post('key');
        
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
    
    public function create_project_post()
    {
        /** Check Input User Key **/
        $userKey = $this->_checkInputKey();

        $user = $this->user_m->getUserByKey($userKey);
        
        $projectCode         = $this->post('kode_project');
        $projectName         = $this->post('project_name');
        $projectOwner        = $this->post('project_author');
        $projectPlatform     = $this->post('project_platform');
        $projectWorkingTime  = $this->post('working_time');
        $projectDescription  = $this->post('description');
	
    	if (!isset($projectCode) || empty($projectCode)) {
                $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
    	}

    	$checkProjectCode = $this->project_m->checkProjectCode($projectCode);
    	if ($checkProjectCode) {
                $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter Correctly'));
    	}
    	$projectData = array(
    	    'kode_project'     => $projectCode,
    	    'project_name'     => $projectName,
    	    'project_platform' => $projectPlatform,
    	    'project_author'   => $projectOwner,
    	    'working_time'     => $projectWorkingTime,
    	    'description'      => $projectDescription,
            'creator'          => (int) $user->id_user,
            'created'          => date('Y-m-d H:i:s'),
    	);

	/** Insert Data Project into Database **/
        $projectId = $this->project_m->insert('tbl_project', $projectData); 
	
    	if ($projectId) {
                $this->response(array('status' => 1, 'message' => 'New Project has been Add'));
    	} else {
                $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter Correctly'));
    	}
    }

}

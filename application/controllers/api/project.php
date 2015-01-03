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

    public function delete_project_get()
    {
    	echo "delete";
    }


    public function update_project_get()
    {
    	echo "update";
    }

    public function get_single_project_get()
    {
        /** Check Input User Key **/
        $userKey = $this->_checkInputGetKey();

        $user = $this->user_m->getUserByKey($userKey);
		
        $projectCode         = $this->get('kode_project');

	if (!isset($projectCode) || empty($projectCode)) {
	    $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter'));
	}
	
	$checkProjectCode = $this->project_m->checkProjectCode($projectCode);
    	if (!$checkProjectCode) {
                $this->response(array('status' => 0, 'error' => 'Please Fill Your Parameter Correctly'));
    	}
	$dataProjectSingle = $this->project_m->getProjectSingle($projectCode);

        $this->response(array('status' => 1, 'data' => $dataProjectSingle));
 
    }

    public function get_projects_get()
    {
        /** Check Input User Key **/
        $userKey = $this->_checkInputGetKey();

        $user = $this->user_m->getUserByKey($userKey);
		
        $getLimit         = $this->get('limit');
        $getOffset        = $this->get('offset');

	if (!isset($getLimit) || empty($getLimit)) {
	    $limit = 5;
	} else {
	    $limit = $getLimit;
	}

	if (!isset($getOffset) || empty($getOffset)) {
	    $offset = 0;
	} else {
	    $offset = $getOffset;
	}

	$dataProject = $this->project_m->getProjects($user->id_user, $limit, $offset);
        $this->response(array('status' => 1, 'data' => $dataProject));
    }

}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_m extends MY_Model {

	public $variable;
    private $tableName = "tbl_project";

	public function __construct()
	{
		parent::__construct();
		
	}
    
    function checkProjectCode($projectCode) 
    {
		$sql = "SELECT * FROM " . $this->tableName . " WHERE kode_project = '". $this->db->escape_str($projectCode) . "' ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getProjects($idUser, $limit, $offset)
    {
    	$sql="select * from " . $this->tableName . " where creator = " . $idUser. " limit " . (int) $offset . " , " . (int) $limit;
        $q = $this->db->query($sql);
        $data = $q->result_object();
        return $data;
    }

    function getProjectsByPic($idUser, $limit, $offset)
    {
        $sql="select * from " . $this->tableName . " where pic = " . $idUser. " limit " . (int) $offset . " , " . (int) $limit;
        $q = $this->db->query($sql);
        $data = $q->result_object();
        return $data;
    }

    function getProjectSingle($projectCode)
    {
    	$sql="select * from " . $this->tableName . " where kode_project = '" . $this->db->escape_str($projectCode) . "'";
        $q = $this->db->query($sql);
        $data = $q->row();
        return $data;
    }
 

    function checkKey($key)
    {
        return $this->db->where(config_item('rest_key_column'), $key)->count_all_results(config_item('rest_keys_table')) > 0;
    }
        
    function insertKey($key, $data)
    {
        $data[config_item('rest_key_column')] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();

        $this->db->set($data)->insert(config_item('rest_keys_table'));

        return $this->db->insert_id();
    }
    
    function getUser($username, $showPassword = false){
        $sql="select a.*, k.key from " . $this->tableName . " a left join tbl_keys k on a.id_key = k.id where (a.username = '". $this->db->escape_str($username) ."' or a.email = '" . $this->db->escape_str($username) . "')";
        $q = $this->db->query($sql);
        $data = $q->row();
        if (!$showPassword) {
            unset($data->password);
        }
        $q->free_result();
        return $data;
    }

}

/* End of file  */
/* Location: ./application/models/ */

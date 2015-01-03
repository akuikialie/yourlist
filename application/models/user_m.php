<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_m extends MY_Model {

	public $variable;
    private $tableName = "tbl_user";

	public function __construct()
	{
		parent::__construct();
		
	}
    
    function checkUsername($username) 
    {
		$sql = "SELECT * FROM " . $this->tableName . " WHERE username='". $this->db->escape_str($username) . "' ";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
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

    function getUserByKey($key, $showPassword = false){
        $sql="select a.* from " . $this->tableName . " a left join tbl_keys k on a.id_key = k.id where k.key = '". $this->db->escape_str($key) ."'";
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

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_m extends MY_Model {

	public $variable;

	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->helper('date');
		session_start();
	}

	function login($username = '', $password = ''){
		$passwordEncrypt = md5(md5($password));
		$sql = "select * from tbl_user where (username = '" . $this->db->escape_str($username) . "' or email = '" . $this->db->escape_str($username) . "') and activate = 1 AND id_status = 1 AND deleted != 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() === 1){
            $user = $query->row();
            if ($user->password === $passwordEncrypt) {
                return true;
            } else {
                return false;
            }
		} else {
			return false;
		}
	}

    function getUser($username, $showPassword = false){
        $sql="select * from tbl_user a
                where a.usernam = '". $this->db->escape_str($username) ."'";
        return $sql; exit();
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
    	return $data;
    }

    function get_propinsi($id=''){
    	$this->db->where('id_propinsi', (int)$id);
    	$q=$this->db->get('tbl_propinsi');
    	$data=$q->row();
    	$q->free_result();
    	return $data->nama_propinsi;
    }

    function get_kabupaten($id=''){
    	$this->db->where('id_kabupaten', (int)$id);
    	$q=$this->db->get('tbl_kabupaten');
    	$data=$q->row();
    	$q->free_result();
    	return $data->nama_kabupaten;
    }

    function get_kecamatan($id=''){
    	$this->db->where('id_kecamatan', (int)$id);
    	$q=$this->db->get('tbl_kecamatan');
    	$data=$q->row();
    	$q->free_result();
    	return $data->nama_kecamatan;
    }

    function getCity()
    {
        // $nama = strtoupper($this->input->post('query'));
        $sql = "SELECT nama_kabupaten FROM tbl_kabupaten";
        $q = $this->db->query($sql);
        $data = $q->result_array();
        $output = array();
        foreach ($data as $key => $value) {
            $output[] = $value['nama_kabupaten'];
        }
        echo json_encode($output);
    }

    function getUserNameById($id)
    {
        $sql = "SELECT username as nama from tbl_user
            WHERE id_user = '". (int)$id . "'";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->nama;
    }
    
    function kata($kata, $limit){
        $kalimat = "";
        $potong = explode(" ", $kata);
        for($i=0; $i<=$limit; $i++){
        if(isset($potong[$i])){
        $kalimat = $kalimat.$potong[$i]." ";
        }
        }
        $hasil = "$kalimat...";
        return $hasil;
    }
    
    function check_key($key)
    {
        $sql = "SELECT * FROM `tbl_partner` WHERE `key` = '".$key."'";
        $query = $this->db->query($sql);
        if ($query->num_rows() === 1) {
            $key = $query->row();
            return true;
        } else {
            return false;
        }
    }

}

/* End of file  */
/* Location: ./application/models/ */

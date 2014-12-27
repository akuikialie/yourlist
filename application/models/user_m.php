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





    /** Code lama **/
	function cek_username($id='', $username=''){
		$sql="SELECT COUNT(*) AS jumlah FROM tbl_user WHERE id_user!='".(int)$id."' AND username='". $this->db->escape_str($username) . "' ";
		$q=$this->db->query($sql);
		$data = $q->row();
		$q->free_result();
		return $data->jumlah;
	}

	function cek_password($id='', $pass=''){
		$sql="SELECT COUNT(*) AS jumlah FROM tbl_user WHERE id_user='". (int)$id."' AND password='". $this->db->escape_str($pass) ."' ";
		$q=$this->db->query($sql);
		$data = $q->row();
		$q->free_result();
		return $data->jumlah;
	}

	function view_all_admin($limit='', $offset=''){
		$sql=	"select a.*, b.nama_level, c.nama_kabupaten, d.nama_status, e.nama_group
				from tbl_user a
				left join tbl_level b on a.id_level=b.id_level
				left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
				left join tbl_status d on a.id_status=d.id_status
				left join tbl_group e on a.id_group=e.id_group
				where a.id_level!=6 and a.id_level!=7 and a.deleted=0
				order by a.id_user asc limit ".(int)$offset.",".(int)$limit."";
		$q=$this->db->query($sql);
		$data = $q->result();
		$q->free_result();
		return $data;
	}
        
        function view_all_member($limit='', $offset=''){
		$sql=	"select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi
				from tbl_user a
				left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
				left join tbl_status d on a.id_status=d.id_status
				left join tbl_group e on a.id_group=e.id_group
                                left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                                left join tbl_propinsi g on a.id_propinsi=g.id_propinsi
				where a.id_level IN (6,7)
				order by a.date_added desc limit ".(int)$offset.",".(int)$limit."";
		$q=$this->db->query($sql);
		$data = $q->result();
		$q->free_result();
		return $data;
	}
        
	function view_all_merchant($limit='', $offset=''){
		$sql=	"select m.*,m.date_added as 'merchant_register_date',m.date_request as 'merchant_request_date',"
                . " m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi"
                . " from tbl_store m left join tbl_user u on m.id_user=u.id_user"
                . " left join tbl_kecamatan as kc on m.id_kecamatan=kc.id_kecamatan"
                . " left join tbl_kabupaten as kb on m.id_kabupaten=kb.id_kabupaten"
                . " left join tbl_propinsi as pr on m.id_propinsi=pr.id_propinsi"
                . " where u.deleted=0 order by m.date_added DESC limit ".(int)$offset.",".(int)$limit."";
		$q=$this->db->query($sql);
		$data = $q->result();
		$q->free_result();
		return $data;
	}
        
    function view_all_merchant_by_status($status = '', $limit='', $offset=''){
		$sql="select m.*,m.date_added as 'merchant_register_date', m.date_request as 'merchant_request_date',"
                . " m.date_modified as 'merchant_modified_date', jo.display_name as nama_kota_jne,"
                . " m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi"
                . " from tbl_store m left join tbl_user u on (m.id_user=u.id_user)"
                . " left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)"
                . " left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)"
                . " left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)"
                . " left join jne_origin jo on (jo.id = m.id_jne_origin)"
                . " where u.deleted=0 and m.store_status='" . $this->db->escape_str($status) . "'";
                
                if($this->session->userdata('admin_session')->id_level == 8){
                    $sql .= " AND m.agregator = ". (int)$this->session->userdata('admin_session')->id_user;
                }
                if($status == 'pending'){
                    $sql .= " order by m.date_request DESC";
                }elseif ($status == 'approve') {
                    $sql .= " order by m.date_verified DESC";
                }elseif ($status == 'block') {
                    $sql .= " order by m.date_unverified DESC";
                }
                $sql .= "  limit ".(int)$offset.",".(int)$limit."";
		$q=$this->db->query($sql);
		$data = $q->result();
		$q->free_result();
		return $data;
	}
        
    function get_merchant_by_search($by_search = '', $by_mail = '', $by_from = '', $by_to = '', $by_status = '', $by_sales = '', $by_location = '', $indoloka = '', $limit = '', $offset = '') {
        $query = "select m.*,m.date_added as 'merchant_register_date', m.date_request as 'merchant_request_date', m.date_modified as 'merchant_modified_date',
                m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi, jo.display_name as nama_kota_jne
                from tbl_store m left join tbl_user u on (m.id_user=u.id_user)
                left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)
                left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)
                left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)
                left join jne_origin jo on (jo.id = m.id_jne_origin)
                where u.deleted=0";

        if($this->session->userdata('admin_session')->id_level == 8){
            $query .= " AND m.agregator = ". (int)$this->session->userdata('admin_session')->id_user;
        }
        
        $conditions = array();
        if ($by_search != "") {
            $conditions[] = "m.nama_store LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
        }
        if($by_mail !="") {
          $conditions[] = "m.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
        }
        if($indoloka !="") {
          $conditions[] = "m.merchant_indoloka = '" . $this->db->escape_str($indoloka) . "'";
        }
        if ($by_from != "" && $by_to != "") {
            $from = strftime("%Y-%m-%d", strtotime($by_from));
            $to = strftime("%Y-%m-%d", strtotime($by_to));
            if($by_status == "approve"){
                $conditions[] = "(DATE(m.date_verified) BETWEEN '$from' AND '$to')";
            }else if($by_status == "pending"){
                $conditions[] = "(DATE(m.date_request) BETWEEN '$from' AND '$to')";
            }else if($by_status == "block"){
                $conditions[] = "(DATE(m.date_unverified) BETWEEN '$from' AND '$to')";
            }else{
                $conditions[] = "(DATE(m.date_added) BETWEEN '$from' AND '$to')";
            }
        }
        if ($by_status != "") {
            $conditions[] = "m.store_status='" . $this->db->escape_str($by_status) . "'";
        }
        if ($by_sales != "") {
            $conditions[] = "m.id_sales='" . (int)$by_sales . "'";
        }
        if ($by_location != "") {
            $conditions[] = "kb.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
        }

        //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

        $sql = $query;
        if (count($conditions) > 0) {
            $sql .= " AND " . implode(' AND ', $conditions) . "";
        }
        if($by_status == 'pending'){
            $sql .= " order by m.date_request DESC";
        }elseif ($by_status == 'approve') {
            $sql .= " order by m.date_verified DESC";
        }elseif ($by_status == 'block') {
            $sql .= " order by m.date_unverified DESC";
        }
        $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit . "";

        $q = $this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }
    
    function get_merchant_export($by_search = '', $by_mail = '', $by_from = '', $by_to = '', $by_status = '', $by_sales = '', $by_location = '', $indoloka = '', $limit = '', $offset = '') {
        $query = "select m.*,m.date_added as 'merchant_register_date', m.date_request as 'merchant_request_date', m.date_modified as 'merchant_modified_date',
                m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi, jo.display_name as nama_kota_jne
                from tbl_store m left join tbl_user u on (m.id_user=u.id_user)
                left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)
                left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)
                left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)
                left join jne_origin jo on (jo.id = m.id_jne_origin)
                where u.deleted=0";

        if($this->session->userdata('admin_session')->id_level == 8){
            $query .= " AND m.agregator = ". (int)$this->session->userdata('admin_session')->id_user;
        }
        
        $conditions = array();
        if ($by_search != "") {
            $conditions[] = "m.nama_store LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
        }
        if($by_mail !="") {
          $conditions[] = "m.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
        }
        if($indoloka !="") {
          $conditions[] = "m.merchant_indoloka = '" . $this->db->escape_str($indoloka) . "'";
        }
        if ($by_from != "" && $by_to != "") {
            $from = strftime("%Y-%m-%d", strtotime($by_from));
            $to = strftime("%Y-%m-%d", strtotime($by_to));
            if($by_status == "approve"){
                $conditions[] = "(DATE(m.date_verified) BETWEEN '$from' AND '$to')";
            }else if($by_status == "pending"){
                $conditions[] = "(DATE(m.date_request) BETWEEN '$from' AND '$to')";
            }else if($by_status == "block"){
                $conditions[] = "(DATE(m.date_unverified) BETWEEN '$from' AND '$to')";
            }else{
                $conditions[] = "(DATE(m.date_added) BETWEEN '$from' AND '$to')";
            }
        }
        if ($by_status != "") {
            $conditions[] = "m.store_status='" . $this->db->escape_str($by_status) . "'";
        }
        if ($by_sales != "") {
            $conditions[] = "m.id_sales='" . (int)$by_sales . "'";
        }
        if ($by_location != "") {
            $conditions[] = "kb.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
        }

        //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

        $sql = $query;
        if (count($conditions) > 0) {
            $sql .= " AND " . implode(' AND ', $conditions) . "";
        }
        if($by_status == 'pending'){
            $sql .= " order by m.date_request DESC";
        }elseif ($by_status == 'approve') {
            $sql .= " order by m.date_verified DESC";
        }elseif ($by_status == 'block') {
            $sql .= " order by m.date_unverified DESC";
        }

        $q = $this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }

    function get_member_by_search($by_search='',$by_mail='', $by_from='', $by_to='',$by_status='', $by_location='', $limit = '', $offset = '')
        {
            $query = "a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi
                        from tbl_user a
                        left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                        left join tbl_status d on a.id_status=d.id_status
                        left join tbl_group e on a.id_group=e.id_group
                        left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                        left join tbl_propinsi g on a.id_propinsi=g.id_propinsi
                        where a.id_level IN (6,7) and a.deleted=0";

            $conditions = array();
            if($by_search !="") {
              $conditions[] = "a.username LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
            }
            if($by_from !="" && $by_to !="") {
                $from = strftime("%Y-%m-%d",strtotime($by_from));
                $to = strftime("%Y-%m-%d",strtotime($by_to));
                $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
            }
            if($by_mail !="") {
              $conditions[] = "a.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
            }
            if($by_status !="") {
                $conditions[] = "d.id_status='" . (int)$by_status . "'";
              }
            if($by_location !="") {
              $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
            }

            //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

            $sql = $query;
            if (count($conditions) > 0) {
              $sql .= " AND " . implode(' AND ', $conditions) . "";
            }
            $sql .= " group by a.id_user desc LIMIT " . (int)$offset . ", " . (int)$limit . "";
            // var_dump($sql);exit;
            $q = $this->db->query($sql);
            $data = $q->result();
            $q->free_result();
            return $data;
        }
        
        function get_member_by_search_m($by_search='',$by_mail='', $by_from='', $by_to='',$by_status='', $by_level='', $by_jenkel = '', $by_um = '', $by_location='', $limit = '', $offset = '')
        {
            $query = "select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi
                        from tbl_user a
                        left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                        left join tbl_status d on a.id_status=d.id_status
                        left join tbl_group e on a.id_group=e.id_group
                        left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                        left join tbl_propinsi g on a.id_propinsi=g.id_propinsi
                        where a.deleted = 0 ";

            $conditions = array();
            if($by_search !="") {
              $conditions[] = " CONCAT(a.firstname, ' ', a.lastname) LIKE '%". $this->db->escape_like_str($by_search) ."%'";
            }
            if($by_from !="" && $by_to !="") {
                $from = strftime("%Y-%m-%d",strtotime($by_from));
                $to = strftime("%Y-%m-%d",strtotime($by_to));
                $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
            }
            if($by_mail !="") {
              $conditions[] = "a.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
            }
            if($by_status !="") {
                $conditions[] = "a.id_status = '" . (int)$by_status . "'";
              }
            if($by_level !="") {
                $conditions[] = "a.id_level='" . (int)$by_level . "'";
              }else{
                $conditions[] = "a.id_level IN (6,7)";
              }
            if($by_jenkel !="" AND $by_jenkel !="-") {
                $conditions[] = "a.gender='" . $this->db->escape_str($by_jenkel) . "'";
            }elseif($by_jenkel =="-"){
                $conditions[] = "a.gender NOT IN ('man','woman')";
            }
            if($by_um !=""){
                if($by_um == 22){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=22 ";
                }elseif($by_um == 21){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=18 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=21";
                }elseif($by_um == 17){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=13 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=17";
                }elseif($by_um == 13){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=6 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=12";
                }else{
                  $conditions[] = " a.birthdate IS NULL";
                }
                
            }
            if($by_location !="") {
              $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_str($by_location) . "%'";
            }

            //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

            $sql = $query;
            if (count($conditions) > 0) {
              $sql .= " AND " . implode(' AND ', $conditions) . "";
            }
            $sql .= " order by a.date_added desc LIMIT " . (int)$offset . ", " . (int)$limit . "";
            // var_dump($sql);exit;
            $q = $this->db->query($sql);
            $data = $q->result();
            $q->free_result();
            return $data;
        }
        function get_member_export_m($by_search='',$by_mail='', $by_from='', $by_to='',$by_status='', $by_level='', $by_jenkel = '', $by_location='', $limit = '', $offset = '')
        {
            $query = "select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi
                        from tbl_user a
                        left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                        left join tbl_status d on a.id_status=d.id_status
                        left join tbl_group e on a.id_group=e.id_group
                        left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                        left join tbl_propinsi g on a.id_propinsi=g.id_propinsi";

            $conditions = array();
            if($by_search !="") {
              $conditions[] = " CONCAT(a.firstname, ' ', a.lastname) LIKE '%". $this->db->escape_like_str($by_search) ."%'";
            }
            if($by_from !="" && $by_to !="") {
                $from = strftime("%Y-%m-%d",strtotime($by_from));
                $to = strftime("%Y-%m-%d",strtotime($by_to));
                $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
            }
            if($by_mail !="") {
              $conditions[] = "a.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
            }
            if($by_status !="") {
                $conditions[] = "a.id_status = '" . (int)$by_status . "'";
              }
            if($by_level !="") {
                $conditions[] = "a.id_level='" . (int)$by_level . "'";
              }else{
                $conditions[] = "a.id_level IN (6,7)";
              }
            if($by_jenkel !="") {
                $conditions[] = "a.gender='" . $this->db->escape_str($by_jenkel) . "'";
            }
            if($by_location !="") {
              $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_str($by_location) . "%'";
            }

            //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

            $sql = $query;
            if (count($conditions) > 0) {
              $sql .= " WHERE " . implode(' AND ', $conditions) . "";
            }
            $sql .= " group by a.date_added order by a.date_added DESC";
            // var_dump($sql);exit;
            $q = $this->db->query($sql);
            $data = $q->result();
            $q->free_result();
            return $data;
        }
        
        function count_all_merchant() {
        $sql = "select count(*) as jumlah from (select m.*,m.date_added as 'merchant_register_date', m.date_modified as 'merchant_modified_date',"
                . " m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi"
                . " from tbl_store m left join tbl_user u on (m.id_user=u.id_user)"
                . " left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)"
                . " left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)"
                . " left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)"
                . " where u.deleted=0 order by m.date_added DESC) as x";

        $query = $this->db->query($sql);
        $data = $query->row();
        $query->free_result();
        return $data->jumlah;
    }
    
    function count_all_merchant_by_status($status='') {
        $sql = "select count(*) as jumlah from (select m.*,m.date_added as 'merchant_register_date', m.date_modified as 'merchant_modified_date',"
                . " m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi"
                . " from tbl_store m left join tbl_user u on (m.id_user=u.id_user)"
                . " left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)"
                . " left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)"
                . " left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)"
                . " where u.deleted=0";
        
        if($this->session->userdata('admin_session')->id_level == 8){
            $sql .= " AND m.agregator = ". (int)$this->session->userdata('admin_session')->id_user;
        }
        
            $sql .= " and m.store_status='" . $this->db->escape_str($status) . "' order by m.date_added DESC) as x";
        
        $query = $this->db->query($sql);
        $data = $query->row();
        $query->free_result();
        return $data->jumlah;
    }

    function count_all_member(){
            $sql = "select count(*) as jumlah from tbl_user WHERE id_level IN (6,7) AND deleted=0 AND id_status !=5 ";
            $q = $this->db->query($sql);
            $data = $q->row();
            $q->free_result();
            return $data->jumlah;
        }
        
    function count_all_merchant_search($by_search = '', $by_mail = '', $by_from = '', $by_to = '', $by_status = '', $by_sales = '', $by_location = '', $indoloka = '', $limit = '', $offset = '') {
        $query = "select count(*) as jumlah from"
                . "(select m.*,m.date_added as 'merchant_register_date', m.date_modified as 'merchant_modified_date',"
                . " m.telpon as 'merchant_telp', kb.nama_kabupaten, kc.nama_kecamatan, pr.nama_propinsi"
                . " from tbl_store m left join tbl_user u on (m.id_user=u.id_user)"
                . " left join tbl_kecamatan kc on (m.id_kecamatan=kc.id_kecamatan)"
                . " left join tbl_kabupaten kb on (m.id_kabupaten=kb.id_kabupaten)"
                . " left join tbl_propinsi pr on (m.id_propinsi=pr.id_propinsi)"
                . " where u.deleted=0";
        
        if($this->session->userdata('admin_session')->id_level == 8){
            $query .= " AND m.agregator = ". (int)$this->session->userdata('admin_session')->id_user;
        }
        
        $conditions = array();
        if ($by_search != "") {
            $conditions[] = "m.nama_store LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
        }
        if ($by_from != "" && $by_to != "") {
            $from = strftime("%Y-%m-%d", strtotime($by_from));
            $to = strftime("%Y-%m-%d", strtotime($by_to));
            if($by_status == "approve"){
                $conditions[] = "(DATE(m.date_added) BETWEEN '$from' AND '$to')";
            }else if($by_status == "pending"){
                $conditions[] = "(DATE(m.date_verified) BETWEEN '$from' AND '$to')";
            }else if($by_status == "block"){
                $conditions[] = "(DATE(m.date_unverified) BETWEEN '$from' AND '$to')";
            }else{
                $conditions[] = "(DATE(m.date_added) BETWEEN '$from' AND '$to')";
            }
        }
        if($by_mail !="") {
          $conditions[] = "m.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
        }
        if($indoloka !="") {
          $conditions[] = "m.merchant_indoloka = '" . $this->db->escape_str($indoloka) . "'";
        }
        if ($by_status != "") {
            $conditions[] = "m.store_status='" . $this->db->escape_str($by_status) . "'";
        }
        if ($by_sales != "") {
            $conditions[] = "m.id_sales='" . (int)$by_sales . "'";
        }        
        if ($by_location != "") {
            $conditions[] = "kb.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
        }

        //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

        $sql = $query;
        if (count($conditions) > 0) {
            $sql .= " AND " . implode(' AND ', $conditions) . "";
        }
        $sql .= " group by m.id_store) as x";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->jumlah;
    }

    function count_all_member_search($by_search='',$by_mail='', $by_from='', $by_to='', $by_status='', $by_location='')
        {
            $query = "select count(*) as jumlah from (select a.*, YEAR(CURDATE()) - YEAR(a.birthdate) AS usia, c.nama_kabupaten, d.nama_status, e.nama_group
                from tbl_user a
                left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                left join tbl_status d on a.id_status=d.id_status
                left join tbl_group e on a.id_group=e.id_group
                where a.id_level IN (6,7) and a.deleted=0";

            $conditions = array();
            if($by_search !="") {
              $conditions[] = "a.username LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
            }
            if($by_from !="" && $by_to !="") {
                $from = strftime("%Y-%m-%d",strtotime($by_from));
                $to = strftime("%Y-%m-%d",strtotime($by_to));
                $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
            }
            if($by_status !="") {
                $conditions[] = "d.id_status='" . (int)$by_status . "'";
              }
            if($by_location !="") {
              $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
            }

            //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

            $sql = $query;
            if (count($conditions) > 0) {
              $sql .= " AND " . implode(' AND ', $conditions) . "";
            }
            $sql .= " group by a.id_user) as x";
            $q = $this->db->query($sql);
            $data = $q->row();
            $q->free_result();
            return $data->jumlah;
        }
        
        function count_all_member_search_m($by_search='',$by_mail='', $by_from='', $by_to='', $by_status='', $by_level='', $by_jenkel = '', $by_um='', $by_location='')
        {
            $query = "select count(*) as jumlah from (select a.*, c.nama_kabupaten, d.nama_status, e.nama_group
                from tbl_user a
                left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                left join tbl_status d on a.id_status=d.id_status
                left join tbl_group e on a.id_group=e.id_group
                left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                left join tbl_propinsi g on a.id_propinsi=g.id_propinsi
                where a.deleted=0";

            $conditions = array();
            if($by_search !="") {
              $conditions[] = "a.username LIKE '%" . $this->db->escape_like_str($by_search) . "%'";
            }
            if($by_from !="" && $by_to !="") {
                $from = strftime("%Y-%m-%d",strtotime($by_from));
                $to = strftime("%Y-%m-%d",strtotime($by_to));
                $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
            }
            if($by_status !="") {
                $conditions[] = "a.id_status = '" . $this->db->escape_str($by_status) . "'";
              }
            if($by_level !="") {
                $conditions[] = "a.id_level='" . (int)$by_level . "'";
              }else{
                $conditions[] = "a.id_level IN (6,7)";
              }
            if($by_jenkel !="" AND $by_jenkel !="-") {
                $conditions[] = "a.gender='" . $this->db->escape_str($by_jenkel) . "'";
              }elseif($by_jenkel =="-"){
                $conditions[] = "a.gender NOT IN ('man','woman')";
              }
            if($by_um !=""){
                if($by_um == 22){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=22 ";
                }elseif($by_um == 21){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=18 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=21";
                }elseif($by_um == 17){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=13 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=17";
                }elseif($by_um == 13){
                  $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=6 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=12";
                }else{
                  $conditions[] = " a.birthdate IS NULL";
                }
            }  
            if($by_location !="") {
              $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_like_str($by_location) . "%'";
            }                        

            //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

            $sql = $query;
            if (count($conditions) > 0) {
              $sql .= " AND " . implode(' AND ', $conditions) . "";
            }
            $sql .= " group by a.id_user) as x";
            $q = $this->db->query($sql);
            $data = $q->row();
            $q->free_result();
            return $data->jumlah;
        }

	function get_username($key){
		$sql="select max(username) as id from tbl_user where (select left(username, 3))='". $this->db->escape_str($key) ."'";
		$q=$this->db->query($sql);
		$data = $q->row();
		$q->free_result();
		if($data->id!=null){
			$id_anyar = str_replace($key, '', $data->id);
			$id_anyar += 1000; $id_anyar++;
			$out = $key.substr($id_anyar, 1,3);	
		} else {
			$id_anyar = 1000; $id_anyar++;
			$out=$key.substr($id_anyar, 1,3);
		}
		return $out;	
	}

	function get_user_login($_username, $_password) {
        $sql = "select * from tbl_user 
            where id_level IN(1, 2, 3, 8, 4, 5) AND (username='" . $this->db->escape_str($_username) . "' or email ='". $this->db->escape_str($_username) ."') 
            AND password = '" . $this->db->escape_str($_password) . "' AND deleted != 1";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->row();
        }
    }

    function get_email_user($email){
    	$sql = "select * from tbl_user where email ='". $this->db->escape_str($email) ."'";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->row();
        }
    }

    function get_image($id_user=''){
    	$sql = "select image from tbl_user where id_user ='". (int)$id_user."'";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->image;
    }
    
    function delete_love($id_produk, $id_user){		
        $this->db->where('md5(id_produk)', $id_produk);
        $this->db->where('md5(id_user)', $id_user);
        $this->db->delete('tbl_love');
        return $this->db->affected_rows();
    }
    
    /* Check duplicate email */
    function duplicate_email($email)
    {
        $sql = "select email from tbl_user where email='". $this->db->escape_str($email) ."'";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
     function get_status(){
    	$sql = "select * from tbl_status";
        $q=$this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }
    
    function get_user($email)
    {
        $sql = "select u.id_user, u.username as name, email, active, firstname, lastname, " .
                "image, alamat, bio, telpon, hp, gender, " .
                "p.nama_propinsi, k.nama_kabupaten, kc.nama_kecamatan , date_added " .
                "from tbl_user u left join tbl_kabupaten k on u.id_kabupaten=k.id_kabupaten " .
                "left join tbl_propinsi p on u.id_propinsi=p.id_propinsi " .
                "left join tbl_kecamatan kc on u.id_kecamatan = kc.id_kecamatan where u.email = '" . $this->db->escape_str($email) . "';";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data;
    }
    
    function get_member_only($term)
    {
        $where = "firstname like '%".$this->db->escape_like_str($term)."%' or lastname like '%".$this->db->escape_like_str($term)."%'";
        $sql = "select id_user, concat(firstname, ' ', lastname) as member ";
        $sql .= "from tbl_user where id_level = '6' and active = 1 and (".$where.")";
        $q = $this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }
    
    function get_kabupaten_by_propinsi($id_propinsi, $nama_kabupaten)
    {
        $sql = "select id_kabupaten from tbl_kabupaten where nama_kabupaten = '" . $this->db->escape_str($nama_kabupaten) . "' and id_propinsi = " . (int) $id_propinsi . ";";
        $query = $this->db->query($sql);

        if ($data = $query->num_rows() > 0) {
            return $data = $query->row_object();
        } else {
            return FALSE;
        }
    }

    function get_kecamatan_by_kabupaten($id_kabupaten, $nama_kecamatan)
    {
        $sql = "select id_kecamatan from tbl_kecamatan where nama_kecamatan = '" . $this->db->escape_str($nama_kecamatan) . "' and id_kabupaten = " . (int) $id_kabupaten . ";";
        $query = $this->db->query($sql);

        if ($data = $query->num_rows() > 0) {
            return $data = $query->row_object();
        } else {
            return FALSE;
        }
    }

    function get_user_voucher($id_user = '', $limit = '', $offset = '')
    {
        $sql = "select * from tbl_voucher where id_user='" . (int) $id_user . "' order by id_voucher desc limit " . (int) $offset . "," . (int) $limit . "";
        $q = $this->db->query($sql);
        $data = $q->result_array();
        $q->free_result();
        return $data;
    }
    
    function count_user_voucher($id_user = '')
    {
        $sql = "select * from tbl_voucher where id_user='" . (int) $id_user . "' order by id_voucher desc;";
        $q = $this->db->query($sql);
        $data = $q->num_rows();
        $q->free_result();
        return $data;
    }
    
    function cek_id_user($id_user = '')
    {
        $sql = "select * from tbl_user where id_user = ".(int) $id_user;
        $q = $this->db->query($sql);
        $data = $q->num_rows();
        $q->free_result();
        return $data;
    }
    
    function get_all_point($id_user, $sortBy = 'tanggal', $sort = "ASC")
    {
        $sql = "select * from tbl_point_reward where id_user = '" . (int) $id_user . "' and void = 0 order by '" . $this->db->escape_str($sortBy) . "' " .$this->db->escape_str($sort) . ";";
        $q = $this->db->query($sql);
        $data = $q->result_object();
        $q->free_result();
        return $data;
    }

    function count_point($id_user, $sortBy = 'tanggal', $sort = "ASC")
    {
        $sql = "select sum(debet) as total_debet, sum(kredit) as total_kredit, sum(kredit) - sum(debet) as saldo_point from tbl_point_reward where id_user = '". (int) $id_user ."' and void = 0 order by '" . $this->db->escape_str($sortBy) . "' " .$this->db->escape_str($sort) . ";";

        $q = $this->db->query($sql);
        $data = $q->row_object();
        $q->free_result();
        return $data;
    }

    function view_all_member_with_point($limit='', $offset=''){
        $sql=   "select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi, 
                sum(pnt.debet) as total_debet, sum(pnt.kredit) as total_kredit, sum(pnt.kredit) - sum(pnt.debet) as saldo_point 
                from tbl_user a
                left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                left join tbl_status d on a.id_status=d.id_status
                left join tbl_group e on a.id_group=e.id_group
                                left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                                left join tbl_propinsi g on a.id_propinsi=g.id_propinsi
                                left join tbl_point_reward pnt on a.id_user = pnt.id_user
                where a.id_level IN (6,7) 
                group by a.id_user order by a.date_added desc  limit ".(int)$offset.",".(int)$limit."";
        $q=$this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }

    function get_member_by_search_m_with_point($by_search='',$by_mail='', $by_from='', $by_to='',$by_status='', $by_level='', $by_jenkel = '', $by_um = '', $by_location='', $by_minimal_saldo=0, $limit = '', $offset = '')
    {
        $query = "select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi,
                sum(pnt.debet) as total_debet, sum(pnt.kredit) as total_kredit, sum(pnt.kredit) - sum(pnt.debet) as saldo_point
                    from tbl_user a
                    left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                    left join tbl_status d on a.id_status=d.id_status
                    left join tbl_group e on a.id_group=e.id_group
                    left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                    left join tbl_propinsi g on a.id_propinsi=g.id_propinsi 
                    left join tbl_point_reward pnt on a.id_user = pnt.id_user 
                    where a.deleted = 0 ";

        $conditions = array();
        if($by_search !="") {
          $conditions[] = " CONCAT(a.firstname, ' ', a.lastname) LIKE '%". $this->db->escape_like_str($by_search) ."%'";
        }
        if($by_from !="" && $by_to !="") {
            $from = strftime("%Y-%m-%d",strtotime($by_from));
            $to = strftime("%Y-%m-%d",strtotime($by_to));
            $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
        }
        if($by_mail !="") {
          $conditions[] = "a.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
        }
        if($by_status !="") {
            $conditions[] = "a.id_status = '" . (int)$by_status . "'";
          }
        if($by_level !="") {
            $conditions[] = "a.id_level='" . (int)$by_level . "'";
          }else{
            $conditions[] = "a.id_level IN (6,7)";
          }
        if($by_jenkel !="" AND $by_jenkel !="-") {
            $conditions[] = "a.gender='" . $this->db->escape_str($by_jenkel) . "'";
        }elseif($by_jenkel =="-"){
            $conditions[] = "a.gender NOT IN ('man','woman')";
        }
        if($by_um !=""){
            if($by_um == 22){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=22 ";
            }elseif($by_um == 21){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=18 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=21";
            }elseif($by_um == 17){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=13 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=17";
            }elseif($by_um == 13){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=6 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=12";
            }else{
              $conditions[] = " a.birthdate IS NULL";
            }
            
        }
        if($by_location !="") {
          $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_str($by_location) . "%'";
        }
        
        $sqlFindMinimalSaldo = "";
        if($by_minimal_saldo !=0) {
          $sqlFindMinimalSaldo = " having saldo_point >= " . (int) $by_minimal_saldo . "";
        }

        //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

        $sql = $query;
        if (count($conditions) > 0) {
          $sql .= " AND " . implode(' AND ', $conditions) . "";
        }
        $sql .= " group by a.id_user " . $sqlFindMinimalSaldo . " order by a.date_added desc LIMIT " . (int)$offset . ", " . (int)$limit . "";
        // var_dump($sql);exit;
        $q = $this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }

    function count_all_member_search_m_with_point($by_search='',$by_mail='', $by_from='', $by_to='', $by_status='', $by_level='', $by_jenkel = '', $by_um='', $by_location='', $by_minimal_saldo=0)
    {
        $query = "select count(*) as jumlah from (select a.*, c.nama_kabupaten, d.nama_status, e.nama_group, f.nama_kecamatan, g.nama_propinsi,
                sum(pnt.debet) as total_debet, sum(pnt.kredit) as total_kredit, sum(pnt.kredit) - sum(pnt.debet) as saldo_point
                    from tbl_user a
                    left join tbl_kabupaten c on a.id_kabupaten=c.id_kabupaten
                    left join tbl_status d on a.id_status=d.id_status
                    left join tbl_group e on a.id_group=e.id_group
                    left join tbl_kecamatan f on a.id_kecamatan=f.id_kecamatan
                    left join tbl_propinsi g on a.id_propinsi=g.id_propinsi 
                    left join tbl_point_reward pnt on a.id_user = pnt.id_user 
                    where a.deleted = 0 ";

         $conditions = array();
        if($by_search !="") {
          $conditions[] = " CONCAT(a.firstname, ' ', a.lastname) LIKE '%". $this->db->escape_like_str($by_search) ."%'";
        }
        if($by_from !="" && $by_to !="") {
            $from = strftime("%Y-%m-%d",strtotime($by_from));
            $to = strftime("%Y-%m-%d",strtotime($by_to));
            $conditions[] = "(DATE(a.date_added) BETWEEN '$from' AND '$to')";
        }
        if($by_mail !="") {
          $conditions[] = "a.email LIKE '%" . $this->db->escape_like_str($by_mail) . "%'";
        }
        if($by_status !="") {
            $conditions[] = "a.id_status = '" . (int)$by_status . "'";
          }
        if($by_level !="") {
            $conditions[] = "a.id_level='" . (int)$by_level . "'";
          }else{
            $conditions[] = "a.id_level IN (6,7)";
          }
        if($by_jenkel !="" AND $by_jenkel !="-") {
            $conditions[] = "a.gender='" . $this->db->escape_str($by_jenkel) . "'";
        }elseif($by_jenkel =="-"){
            $conditions[] = "a.gender NOT IN ('man','woman')";
        }
        if($by_um !=""){
            if($by_um == 22){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=22 ";
            }elseif($by_um == 21){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=18 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=21";
            }elseif($by_um == 17){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=13 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=17";
            }elseif($by_um == 13){
              $conditions[] = " YEAR(CURDATE()) - YEAR(a.birthdate) >=6 AND  YEAR(CURDATE()) - YEAR(a.birthdate) <=12";
            }else{
              $conditions[] = " a.birthdate IS NULL";
            }
            
        }
        if($by_location !="") {
          $conditions[] = "c.nama_kabupaten LIKE '%" . $this->db->escape_str($by_location) . "%'";
        }

        $sqlFindMinimalSaldo = "";
        if($by_minimal_saldo !=0) {
          $sqlFindMinimalSaldo = " having saldo_point >= " . (int) $by_minimal_saldo . "";
        }

        //(DATE(date) BETWEEN '".date('Y-m-d', strtotime("-1 month"))."' AND '".date('Y-m-d')."')

        $sql = $query;
        if (count($conditions) > 0) {
          $sql .= " AND " . implode(' AND ', $conditions) . "";
        }
        $sql .= " group by a.id_user " . $sqlFindMinimalSaldo . " order by a.date_added desc) as x";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->jumlah;
    }

    function count_total_point()
    {
        $sql = "select sum(debet) as total_debet, sum(kredit) as total_kredit, sum(kredit) - sum(debet) as saldo_point from tbl_point_reward where void = 0;";

        $q = $this->db->query($sql);
        $data = $q->row_object();
        $q->free_result();
        return $data;
    }
    
    function check_point($point, $id_user) {
        $sql = "select sum(kredit) - sum(debet) as saldo_point from tbl_point_reward where id_user = " . (int) $id_user . " and void = 0;";
        
        $q = $this->db->query($sql);
        $data = $q->row_object();
        
        if ($data->saldo_point > 0) {
            if ((int) $data->saldo_point >= (int) $point) {
                $out = array (
                    'status' => 1,
                );
            } else {
                $out = array (
                    'status' => 0,
                    'error' => "Maaf, Point yang anda miliki tidak mencukupi.",
                );
            }
        } else {
            $out = array (
                'status' => 0,
                'error' => "Maaf, Anda belum mempunyai point rewards.",
            );
        }
        return (object) $out;
    }
}

/* End of file  */
/* Location: ./application/models/ */

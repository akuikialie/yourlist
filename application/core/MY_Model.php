<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }
	//====================================================
    function get_data_join($table1, $table2, $field ){
        $this->db->join($table2, $table2.'.'.$field.' = '.$table1.'.'.$field);
        $q = $this->db->get($table1);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function count_all_data($table=''){
        $sql = "select count(*) as jumlah from ".$table;
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->jumlah;
    }

    function count_all_data_where($table='', $field='', $key=''){
        $sql = "select count(*) as jumlah from ".$table." WHERE ".$field."='".$key."'";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->jumlah;
    }

    function get_data_join2($table1, $table2, $table3, $field1, $field2 ){
        $this->db->join($table2, $table2.'.'.$field1.' = '.$table1.'.'.$field1);
        $this->db->join($table3, $table3.'.'.$field2.' = '.$table1.'.'.$field2);
        $q = $this->db->get($table1);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function get_data_join3($table1, $table2, $table3, $table4, $field1, $field2, $field3 ){
        $this->db->join($table2, $table2.'.'.$field1.' = '.$table1.'.'.$field1);
        $this->db->join($table3, $table3.'.'.$field2.' = '.$table1.'.'.$field2);
        $this->db->join($table4, $table4.'.'.$field3.' = '.$table1.'.'.$field3);
        $q = $this->db->get($table1);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function get_data_join_where($table, $table2, $fieldjoin, $field, $key){
        $this->db->where($table.'.'.$field, $key);
        $this->db->from($table);
        $this->db->join($table2, $table2.'.'.$fieldjoin.' = '.$table.'.'.$fieldjoin);
        $q = $this->db->get();
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function get_data_join_where2($table, $table2, $fieldjoin, $field1, $key1, $field2, $key2){
        $this->db->where($table.'.'.$field1, $key1);
        $this->db->where($table.'.'.$field2, $key2);
        $this->db->from($table);
        $this->db->join($table2, $table2.'.'.$fieldjoin.' = '.$table.'.'.$fieldjoin);
        $q = $this->db->get();
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }
    
	function cek($tabel, $field1, $field2, $param1, $param2){
		$sql = "SELECT COUNT(*) AS jumlah FROM ".$tabel;
		$sql .= " WHERE ".$field1."='".$param1."' AND  ".$field2."='".$param2."'";
		$q = $this->db->query($sql);
		$data = $q->row();
		$q->free_result();
		return $data->jumlah;
	}

    function cek2($tabel, $field1, $param1, $field2, $param2){
        $sql = "SELECT COUNT(*) AS jumlah FROM ".$tabel;
        $sql .= " WHERE ".$field1."='".$param1."' AND ".$field2."='".$param2."'";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->jumlah;
    }
    function get_join($tabel1, $tabel2, $field, $key){
        $sql = "SELECT * FROM ".$tabel1." WHERE ".$tabel1.".".$field."=".$key." LEFT JOIN ".$tabel2." 
                ON ".$tabel1.".".$field."=".$tabel2.".".$field."";       
        $q = $this->db->query($sql);
        $data = $q->result();
        $q->free_result();
        return $data;
    }
    function sum($tabel, $field){
        $sql = "SELECT SUM({$field}) AS total FROM {$tabel}";
        $q = $this->db->query($sql);
        $data = $q->row();
        $q->free_result();
        return $data->total;
    }
	function update_s($tabel, $field1, $field2, $param1, $param2){
		$this->db->where($field1, $param1);
		$this->db->where($field2, $param2);
        $this->db->update($tabel, $data);
        return $this->db->affected_rows();
	}
	//=====================================================
	
    function count_data($tabel, $filter = NULL)
    {
        $fields = $this->db->list_fields($tabel);

        if ($this->session->userdata('filter'))
            $filter = $this->session->userdata('filter');

        $iterasi = 1;
        $num = count($fields);
        $where = "";
        foreach ($fields as $field)
        {
            if ($iterasi == 1)
            {
                $where .= "(" . $field . " LIKE '%" . $filter . "%' ";
            }
            else if ($iterasi == $num)
            {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%') ";
            }
            else
            {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%' ";
            }

            $iterasi++;
        }
        $this->db->where($where);

        $this->db->from($tabel);
        return $this->db->count_all_results();
    }

    function get_data($tabel, $limit = NULL, $offset = NULL, $filter = NULL)
    {
        $fields = $this->db->list_fields($tabel);

        if ($this->session->userdata('filter'))
            $filter = $this->session->userdata('filter');

        $iterasi = 1;
        $num = count($fields);
        $where = "";
        foreach ($fields as $field)
        {
            if ($iterasi == 1)
            {
                $where .= "(" . $field . " LIKE '%" . $filter . "%' ";
            }
            else if ($iterasi == $num)
            {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%') ";
            }
            else
            {
                $where .= "OR " . $field . " LIKE '%" . $filter . "%' ";
            }

            $iterasi++;
        }
        $this->db->where($where);

        $this->db->limit($limit, $offset);
        $query = $this->db->get($tabel);
        $data = $query->result_array();

        return $data;
    }

    function get_data_offset($table, $limit, $offset, $field, $order){
        $this->db->limit($limit, $offset);
        $this->db->order_by($field, $order);
        $q=$this->db->get($table);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function get_datae($tabel, $filter, $field)
    {
        $sql = $this->db->query('select * from ' . $tabel . ' where '.$field.' like "%' . $filter . '%" order by trx_id asc');

        return $sql->result_array(); 
    }

    function get_all_data($tabel){
        $q = $this->db->get($tabel);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }
    
	function get_all_data_order($tabel, $field, $order)
    {
		$this->db->order_by($field, $order);
        $q = $this->db->get($tabel);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }
    
    function get_all_data_by_order($tabel, $field, $order)
    {
        $this->db->order_by($field, $order);
        $q = $this->db->get($tabel);
        // $data = array();
        // $data =$q->result();
        return $q->result_array();
        // return $data;
    }

    function get_single($tabel, $field, $key){
        $this->db->where($field, $key);
        $q = $this->db->get($tabel);
        $data = array();
        $data =$q->row();
        $q->free_result();
        return $data;
    }

    function get_datas($tabel, $id, $field)
    {
        $data = array();

        $query = $this->db->get_where($tabel, array($field => $id));
        $data = $query->result_array();

        return $data;
    }

    function get_data_where($table, $field, $id, $order_by = '', $direction_order ='', $numofrec = ''){
        $this->db->where($field, $id);
		if(!empty($order_by))
			$this->db->order_by($order_by, $direction_order);
		
		if(empty($numofrec))
			$q = $this->db->get($table);
		else
			$q = $this->db->get($table, $numofrec);
        
		$data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function get_data_where2($table, $field1, $key1, $field2, $key2){
        $this->db->where($field1, $key1);
        $this->db->where($field2, $key2);
        $q = $this->db->get($table);
        $data = array();
        $data =$q->result();
        $q->free_result();
        return $data;
    }

    function insert($tabel, $data)
    {
        $this->db->insert($tabel, $data); 
        return $this->db->insert_id();
    }

    function update($tabel, $field, $id, $data)
    {
        $this->db->where($field, $id);
        $this->db->update($tabel, $data);
        return $this->db->affected_rows();
    }

    function update2($tabel, $field1, $id1, $field2, $id2, $data)
    {
        $this->db->where($field1, $id1);
        $this->db->where($field2, $id2);
        $this->db->update($tabel, $data);
        return $this->db->affected_rows();
    }

    function delete($tabel, $field, $id)
    {
        $this->db->where($field, $id);
        $this->db->delete($tabel);
        return $this->db->affected_rows();
    }

    function delete2($tabel, $field1, $field2, $id1, $id2)
    {
        $this->db->where($field1, $id1);
        $this->db->where($field2, $id2);
        $this->db->delete($tabel);
        return $this->db->affected_rows();
    }       

    function page($jml='', $perhalaman='' ,$hal=''){
            // jumlah data yang akan ditampilkan per halaman        
        $dataPerhalaman = $perhalaman;
        $showhalaman = 0;
        $nohalaman = 0;
        // apabila $_GET['halaman'] sudah didefinisikan, gunakan nomor halaman tersebut, 
        // sedangkan apabila belum, nomor halamannya 1.
        if($hal==''){
            $nohalaman = 1;
        }else{ 
            $nohalaman = $hal;      
        }
        
        $jumData = $jml;

        // menentukan jumlah halaman yang muncul berdasarkan jumlah semua data
        $jumhalaman = ceil($jumData/$dataPerhalaman);
        
        $output = '<ul class="pagination pull-right">';
        // menampilkan link previous
        if ($nohalaman > 1){

            $params = $_GET;
            $params['hal'] = $nohalaman-1;

            $query = http_build_query($params);        
            $output .= '<li><a class="round-icon" href="?'.$query.'" data-toggle="tooltip" data-title="Previous Page">&laquo;</a></li>';
        } else {        
            $output .= '<li><a class="round-icon" href="#" data-toggle="tooltip" data-title="Previous Page">&laquo;</a></li>';
        }

        

        // memunculkan nomor halaman dan linknya
        for($halaman = 1; $halaman <= $jumhalaman; $halaman++)
        {
            $params = $_GET;
            $params['hal'] = $halaman;

            $query = http_build_query($params);
                 if ((($halaman >= $nohalaman - 2) && ($halaman <= $nohalaman + 2)) || ($halaman == 1) || ($halaman == $jumhalaman)) 
                 {   
                    if (($showhalaman == 1) && ($halaman != 2)){  $output .= "<li><a>...</a></li>";} 
                    if (($showhalaman != ($jumhalaman - 1)) && ($halaman == $jumhalaman)){  $output .= "<li><a>...</a></li>";}
                    if ($halaman == $nohalaman){                    
                        $output .= '<li><a href="" style="text-decoration:underline">'.$halaman.'</a></li>';
                    }else{ 
                        $output .= '<li><a href="?'.$query.'">'.$halaman.'</a></li>';
                    }
                    $showhalaman = $halaman;          
                 }
        }

        // menampilkan link next
        if ($nohalaman < $jumhalaman){ 

            $params = $_GET;
            $params['hal'] = $nohalaman+1;

            $query = http_build_query($params);
            $output .= '<li><a class="round-icon" href="?'.($query).'" data-toggle="tooltip" data-title="Next Page">&raquo;</a></li>';
        } 

        $output.='</ul>';
        return $output;
    }

}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */
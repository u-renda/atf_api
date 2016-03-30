<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_flag_model extends CI_Model {

    var $table = 'product_flag';
    var $table_id = 'id_product_flag';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_product_flag']) == TRUE)
        {
            $where += array('id_product_flag' => $param['id_product_flag']);
        }
        
        $this->db->select('id_product_flag, '.$this->table.'.id_product, '.$this->table.'.name,
						  '.$this->table.'.status, start_date, end_date,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date,
						  product.name as product_name, photo, price, matched, url');
        $this->db->from($this->table);
        $this->db->join('product', $this->table.'.id_product = product.id_product');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_product']) == TRUE)
        {
            $where += array('id_product' => $param['id_product']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_product_flag, id_product, name, status, start_date, end_date,
						  created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        if (isset($param['id_product']) == TRUE)
        {
            $where += array('id_product' => $param['id_product']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select($this->table_id);
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}
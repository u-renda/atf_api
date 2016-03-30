<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_subcategory_model extends CI_Model {

    var $table = 'product_subcategory';
    var $table_id = 'id_product_subcategory';
    
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
        if (isset($param['id_product_subcategory']) == TRUE)
        {
            $where += array('id_product_subcategory' => $param['id_product_subcategory']);
        }
        
        $this->db->select('id_product_subcategory, '.$this->table.'.id_product_category,
						  '.$this->table.'.name, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date,
						  product_category.name as product_category_name');
        $this->db->from($this->table);
        $this->db->join('product_category', $this->table.'.id_product_category = product_category.id_product_category');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_product_category']) == TRUE)
        {
            $where += array('id_product_category' => $param['id_product_category']);
        }
        
        $this->db->select('id_product_subcategory, id_product_category, name, created_date,
						  updated_date');
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
        if (isset($param['id_product_category']) == TRUE)
        {
            $where += array('id_product_category' => $param['id_product_category']);
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
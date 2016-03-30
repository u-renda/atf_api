<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    var $table = 'product';
    var $table_id = 'id_product';
    
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
        if (isset($param['id_product']) == TRUE)
        {
            $where += array('id_product' => $param['id_product']);
        }
        
        $this->db->select('id_product, id_product_subcategory, '.$this->table.'.id_movie,
						  '.$this->table.'.id_product_brand, '.$this->table.'.name,
						  '.$this->table.'.photo, price, matched, url, '.$this->table.'.status,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date, title,
						  movie.photo as movie_photo, product_brand.name as product_brand_name');
        $this->db->from($this->table);
        $this->db->join('movie', $this->table.'.id_movie = movie.id_movie');
        $this->db->join('product_brand', $this->table.'.id_product_brand = product_brand.id_product_brand');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_product, '.$this->table.'.id_product_subcategory,
						  '.$this->table.'.id_movie, '.$this->table.'.id_product_brand,
						  '.$this->table.'.name, '.$this->table.'.photo, price, matched, url,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, title, movie.photo as movie_photo,
						  product_brand.name as product_brand_name,
						  product_subcategory.name as product_subcategory_name');
        $this->db->from($this->table);
        $this->db->join('movie', $this->table.'.id_movie = movie.id_movie');
        $this->db->join('product_brand', $this->table.'.id_product_brand= product_brand.id_product_brand');
        $this->db->join('product_subcategory', $this->table.'.id_product_subcategory= product_subcategory.id_product_subcategory');
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
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
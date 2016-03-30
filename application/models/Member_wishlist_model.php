<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_wishlist_model extends CI_Model {

    var $table = 'member_wishlist';
    var $table_id = 'id_member_wishlist';
    
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
        if (isset($param['id_member_wishlist']) == TRUE)
        {
            $where += array('id_member_wishlist' => $param['id_member_wishlist']);
        }
        
        $this->db->select('id_member_wishlist, '.$this->table.'.id_member,
						  '.$this->table.'.id_product, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, member.name as member_name, email, gender,
						  birthday, product.name as product_name, photo, price, matched, url');
        $this->db->from($this->table);
        $this->db->join('member', $this->table.'.id_member = member.id_member');
        $this->db->join('product', $this->table.'.id_product = product.id_product');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        
        $this->db->select('id_member_wishlist, id_member, id_product, created_date, updated_date');
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
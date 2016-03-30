<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Movie_cast_model extends CI_Model {

    var $table = 'movie_cast';
    var $table_id = 'id_movie_cast';
    
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
        if (isset($param['id_movie_cast']) == TRUE)
        {
            $where += array('id_movie_cast' => $param['id_movie_cast']);
        }
        
        $this->db->select('id_movie_cast, '.$this->table.'.id_movie, actor, cast,
						  '.$this->table.'.photo, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, title, movie.photo as movie_photo,
						  status');
        $this->db->from($this->table);
        $this->db->join('movie', $this->table.'.id_movie = movie.id_movie');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_movie']) == TRUE)
        {
            $where += array($this->table.'.id_movie' => $param['id_movie']);
        }
        
        $this->db->select('id_movie_cast, '.$this->table.'.id_movie, actor, cast,
						  '.$this->table.'.photo, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, title, movie.photo as movie_photo');
        $this->db->from($this->table);
        $this->db->join('movie', $this->table.'.id_movie = movie.id_movie');
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        if (isset($param['id_movie']) == TRUE)
        {
            $where += array('id_movie' => $param['id_movie']);
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
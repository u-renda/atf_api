<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Movie_cast extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('movie_cast_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_movie = filter($this->post('id_movie'));
		$actor = filter(trim($this->post('actor')));
		$cast = filter(trim(strtolower($this->post('cast'))));
		$photo = filter(trim($this->post('photo')));
		
		$data = array();
		if ($id_movie == FALSE)
		{
			$data['id_movie'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($actor == FALSE)
		{
			$data['actor'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($cast == FALSE)
		{
			$data['cast'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($photo == FALSE)
		{
			$data['photo'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_movie'] = $id_movie;
			$param['actor'] = $actor;
			$param['cast'] = $cast;
			$param['photo'] = $photo;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				$data['create'] = 'success';
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['create'] = 'failed';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function delete_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
        $id_movie_cast = filter($this->post('id_movie_cast'));
        
		$data = array();
        if ($id_movie_cast == FALSE)
		{
			$data['id_movie_cast'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_movie_cast' => $id_movie_cast));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_movie_cast);
				
				if ($delete > 0)
				{
					$data['delete'] = 'success';
					$validation = "ok";
					$code = 200;
				}
				else
				{
					$data['delete'] = 'failed';
					$validation = "error";
					$code = 400;
				}
			}
			else
			{
				$data['id_movie_cast'] = 'not found';
				$validation = "error";
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function info_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_movie_cast = filter($this->get('id_movie_cast'));
		
		$data = array();
		if ($id_movie_cast == FALSE)
		{
			$data['id_movie_cast'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_movie_cast != '')
			{
				$param['id_movie_cast'] = $id_movie_cast;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_movie_cast' => $row->id_movie_cast,
					'actor' => $row->actor,
					'cast' => $row->cast,
					'photo' => $row->photo,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'movie' => array(
						'id_movie' => $row->id_movie,
						'title' => $row->title,
						'photo' => $row->movie_photo,
						'status' => intval($row->status)
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_movie_cast'] = 'not found';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function lists_get()
	{
		$this->benchmark->mark('code_start');
		
		$offset = filter(trim(intval($this->get('offset'))));
		$limit = filter(trim(intval($this->get('limit'))));
		$order = filter(trim(strtolower($this->get('order'))));
		$sort = filter(trim(strtolower($this->get('sort'))));
		$id_movie = filter($this->get('id_movie'));
		
		if ($limit == TRUE && $limit < 20)
		{
			$limit = $limit;
		}
		elseif ($limit == TRUE && in_array($this->rest->key, $this->config->item('allow_api_key')))
		{
			$limit = $limit;
		}
		else
		{
			$limit = 20;
		}
		
		if ($offset == TRUE)
		{
			$offset = $offset;
		}
		else
		{
			$offset = 0;
		}
		
		if (in_array($order, $this->config->item('default_movie_cast_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'created_date';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'desc';
		}
		
		$param = array();
		$param2 = array();
		if ($id_movie == TRUE)
		{
			$param['id_movie'] = $id_movie;
			$param2['id_movie'] = $id_movie;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->the_model->lists($param);
		$total = $this->the_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_movie_cast' => $row->id_movie_cast,
					'actor' => $row->actor,
					'cast' => $row->cast,
					'photo' => $row->photo,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'movie' => array(
						'id_movie' => $row->id_movie,
						'title' => $row->title,
						'photo' => $row->movie_photo
					)
				);
			}
		}

		$rv = array();
		$rv['message'] = 'ok';
		$rv['code'] = 200;
		$rv['limit'] = intval($limit);
		$rv['offset'] = intval($offset);
		$rv['total'] = intval($total);
		$rv['count'] = count($data);
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $rv['code']);
	}
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_movie_cast = filter($this->post('id_movie_cast'));
		$id_movie = filter($this->post('id_movie'));
		$actor = filter(trim($this->post('actor')));
		$cast = filter(trim(strtolower($this->post('cast'))));
		$photo = filter(trim($this->post('photo')));
		
		$data = array();
		if ($id_movie_cast == FALSE)
		{
			$data['id_movie_cast'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_movie_cast' => $id_movie_cast));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_movie == TRUE)
				{
					$param['id_movie'] = $id_movie;
				}
				
				if ($cast == TRUE)
				{
					$param['cast'] = $cast;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($actor == TRUE)
				{
					$param['actor'] = $actor;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_movie_cast, $param);
					
					if ($update > 0)
					{
						$data['update'] = 'success';
						$validation = 'ok';
						$code = 200;
					}
				}
				else
				{
					$data['update'] = 'failed';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['id_movie_cast'] = 'not found';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
}

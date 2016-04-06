<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Product extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('movie_cast_model');
		$this->load->model('product_model', 'the_model');
		$this->load->model('product_subcategory_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_movie_cast = filter($this->post('id_movie_cast'));
		$id_product_subcategory = filter($this->post('id_product_subcategory'));
		$id_product_brand = filter($this->post('id_product_brand'));
		$name = filter(trim($this->post('name')));
		$photo = filter(trim($this->post('photo')));
		$price = filter(trim(intval($this->post('price'))));
		$matched = filter(trim(intval($this->post('matched'))));
		$url = filter(trim($this->post('url')));
		$status = filter(trim(intval($this->post('status'))));
		
		$data = array();
		if ($id_movie_cast == FALSE)
		{
			$data['id_movie_cast'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($id_product_brand == FALSE)
		{
			$data['id_product_brand'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($id_product_subcategory == FALSE)
		{
			$data['id_product_subcategory'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($photo == FALSE)
		{
			$data['photo'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($price == FALSE)
		{
			$data['price'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($url == FALSE)
		{
			$data['url'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			if ($matched == FALSE)
			{
				$matched = 0;
			}
			
			if ($status == FALSE)
			{
				$status = 1;
			}
			
			$param = array();
			$param['id_movie_cast'] = $id_movie_cast;
			$param['id_product_brand'] = $id_product_brand;
			$param['id_product_subcategory'] = $id_product_subcategory;
			$param['name'] = $name;
			$param['photo'] = $photo;
			$param['price'] = $price;
			$param['matched'] = $matched;
			$param['url'] = $url;
			$param['status'] = $status;
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
		
        $id_product = filter($this->post('id_product'));
        
		$data = array();
        if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_product' => $id_product));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_product);
				
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
				$data['id_product'] = 'not found';
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
		
		$id_product = filter($this->get('id_product'));
		
		$data = array();
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_product != '')
			{
				$param['id_product'] = $id_product;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$get_category = $this->product_subcategory_model->info(array('id_product_subcategory' => $row->id_product_subcategory));
				$get_movie = $this->movie_cast_model->info(array('id_movie_cast' => $row->id_movie_cast));
				
				if ($get_category->num_rows() > 0)
				{
					$category = $get_category->row();
				}
				
				if ($get_movie->num_rows() > 0)
				{
					$movie = $get_movie->row();
				}
				
				$data = array(
					'id_product' => $row->id_product,
					'name' => $row->name,
					'photo' => $row->photo,
					'price' => intval($row->price),
					'matched' => intval($row->matched),
					'url' => $row->url,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'movie' => array(
						'id_movie' => $movie->id_movie,
						'title' => $movie->title
					),
					'movie_cast' => array(
						'id_movie_cast' => $row->id_movie_cast,
						'actor' => $row->movie_cast_actor,
						'cast' => $row->movie_cast_cast,
						'photo' => $row->movie_cast_photo
					),
					'brand' => array(
						'id_product_brand' => $row->id_product_brand,
						'name' => $row->product_brand_name
					),
					'category' => array(
						'id_product_category' => $category->id_product_category,
						'name' => $category->product_category_name
					),
					'subcategory' => array(
						'id_product_subcategory' => $row->id_product_subcategory,
						'name' => $row->product_subcategory_name
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_product'] = 'not found';
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
		$status = filter(trim(intval($this->get('status'))));
		
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
		
		if (in_array($order, $this->config->item('default_product_order')) && ($order == TRUE))
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
		
		if (in_array($status, $this->config->item('default_product_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($status == TRUE)
		{
			$param['status'] = $status;
			$param2['status'] = $status;
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
				$get_category = $this->product_subcategory_model->info(array('id_product_subcategory' => $row->id_product_subcategory));
				$get_movie = $this->movie_cast_model->info(array('id_movie_cast' => $row->id_movie_cast));
				
				if ($get_category->num_rows() > 0)
				{
					$category = $get_category->row();
				}
				
				if ($get_movie->num_rows() > 0)
				{
					$movie = $get_movie->row();
				}
				
				$data[] = array(
					'id_product' => $row->id_product,
					'name' => $row->name,
					'photo' => $row->photo,
					'price' => intval($row->price),
					'matched' => intval($row->matched),
					'url' => $row->url,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'movie' => array(
						'id_movie' => $movie->id_movie,
						'title' => $movie->title
					),
					'movie_cast' => array(
						'id_movie_cast' => $row->id_movie_cast,
						'actor' => $row->movie_cast_actor,
						'cast' => $row->movie_cast_cast
					),
					'brand' => array(
						'id_product_brand' => $row->id_product_brand,
						'name' => $row->product_brand_name
					),
					'category' => array(
						'id_product_category' => $category->id_product_category,
						'name' => $category->product_category_name
					),
					'subcategory' => array(
						'id_product_subcategory' => $row->id_product_subcategory,
						'name' => $row->product_subcategory_name
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
		
		$id_product = filter($this->post('id_product'));
		$id_movie_cast = filter($this->post('id_movie_cast'));
		$id_product_subcategory = filter($this->post('id_product_subcategory'));
		$id_product_brand = filter($this->post('id_product_brand'));
		$name = filter(trim($this->post('name')));
		$photo = filter(trim($this->post('photo')));
		$price = filter(trim(intval($this->post('price'))));
		$matched = filter(trim(intval($this->post('matched'))));
		$url = filter(trim($this->post('url')));
		$status = filter(trim(intval($this->post('status'))));
		
		$data = array();
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_product' => $id_product));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_movie_cast == TRUE)
				{
					$param['id_movie_cast'] = $id_movie_cast;
				}
				
				if ($id_product_subcategory == TRUE)
				{
					$param['id_product_subcategory'] = $id_product_subcategory;
				}
				
				if ($id_product_brand == TRUE)
				{
					$param['id_product_brand'] = $id_product_brand;
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if (isset($price) == TRUE)
				{
					$param['price'] = $price;
				}
				
				if (isset($matched) == TRUE)
				{
					$param['matched'] = $matched;
				}
				
				if ($url == TRUE)
				{
					$param['url'] = $url;
				}
				
				if (isset($status) == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_product, $param);
					
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
				$data['id_product'] = 'not found';
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

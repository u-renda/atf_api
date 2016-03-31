<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member_wishlist extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('member_wishlist_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$id_product = filter($this->post('id_product'));
		
		$data = array();
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_member'] = $id_member;
			$param['id_product'] = $id_product;
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
		
        $id_member_wishlist = filter($this->post('id_member_wishlist'));
        
		$data = array();
        if ($id_member_wishlist == FALSE)
		{
			$data['id_member_wishlist'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_member_wishlist' => $id_member_wishlist));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_member_wishlist);
				
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
				$data['id_member_wishlist'] = 'not found';
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
		
		$id_member_wishlist = filter($this->get('id_member_wishlist'));
		
		$data = array();
		if ($id_member_wishlist == FALSE)
		{
			$data['id_member_wishlist'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member_wishlist != '')
			{
				$param['id_member_wishlist'] = $id_member_wishlist;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_member_wishlist' => $row->id_member_wishlist,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->member_name,
						'email' => $row->email,
						'gender' => intval($row->gender),
						'birthday' => $row->birthday
					),
					'product' => array(
						'id_product' => $row->id_product,
						'name' => $row->product_name,
						'photo' => $row->photo,
						'price' => intval($row->price),
						'matched' => intval($row->matched),
						'url' => $row->url
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member_wishlist'] = 'not found';
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
		$id_member = filter($this->get('id_member'));
		$id_product = filter($this->get('id_product'));
		
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
		
		if (in_array($order, $this->config->item('default_member_wishlist_order')) && ($order == TRUE))
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
		if ($id_member == TRUE)
		{
			$param['id_member'] = $id_member;
			$param2['id_member'] = $id_member;
		}
		if ($id_product == TRUE)
		{
			$param['id_product'] = $id_product;
			$param2['id_product'] = $id_product;
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
					'id_member_wishlist' => $row->id_member_wishlist,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->member_name
					),
					'product' => array(
						'id_product' => $row->id_product,
						'name' => $row->product_name
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
		
		$id_member_wishlist = filter($this->post('id_member_wishlist'));
		$id_member = filter($this->post('id_member'));
		$id_product = filter($this->post('id_product'));
		
		$data = array();
		if ($id_member_wishlist == FALSE)
		{
			$data['id_member_wishlist'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_member_wishlist' => $id_member_wishlist));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_member == TRUE)
				{
					$param['id_member'] = $id_member;
				}
				
				if ($id_product == TRUE)
				{
					$param['id_product'] = $id_product;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_member_wishlist, $param);
					
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
				$data['id_member_wishlist'] = 'not found';
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

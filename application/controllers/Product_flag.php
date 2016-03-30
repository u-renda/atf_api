<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Product_flag extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('product_flag_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_product = filter($this->post('id_product'));
		$name = filter(trim(strtolower($this->post('name'))));
		$status = filter(trim(intval($this->post('status'))));
		$start_date = filter($this->post('start_date'));
		$end_date = filter($this->post('end_date'));
		
		$data = array();
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($start_date == FALSE)
		{
			$data['start_date'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($end_date == FALSE)
		{
			$data['end_date'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_product_flag_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			if ($status == FALSE)
			{
				$status = 1;
			}
			
			$param = array();
			$param['id_product'] = $id_product;
			$param['name'] = $name;
			$param['status'] = $status;
			$param['start_date'] = $start_date;
			$param['end_date'] = $end_date;
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
		
        $id_product_flag = filter($this->post('id_product_flag'));
        
		$data = array();
        if ($id_product_flag == FALSE)
		{
			$data['id_product_flag'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_product_flag' => $id_product_flag));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_product_flag);
				
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
				$data['id_product_flag'] = 'not found';
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
		
		$id_product_flag = filter($this->get('id_product_flag'));
		
		$data = array();
		if ($id_product_flag == FALSE)
		{
			$data['id_product_flag'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_product_flag != '')
			{
				$param['id_product_flag'] = $id_product_flag;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_product_flag' => $row->id_product_flag,
					'name' => $row->name,
					'status' => intval($row->status),
					'start_date' => $row->start_date,
					'end_date' => $row->end_date,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
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
				$data['id_product_flag'] = 'not found';
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
		$name = filter(trim(strtolower($this->get('name'))));
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
		
		if (in_array($order, $this->config->item('default_product_flag_order')) && ($order == TRUE))
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
		
		if (in_array($status, $this->config->item('default_product_flag_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($name == TRUE)
		{
			$param['name'] = $name;
			$param2['name'] = $name;
		}
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
				$data[] = array(
					'id_product_flag' => $row->id_product_flag,
					'id_product' => $row->id_product,
					'name' => $row->name,
					'status' => intval($row->status),
					'start_date' => $row->start_date,
					'end_date' => $row->end_date,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
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
		
		$id_product_flag = filter($this->post('id_product_flag'));
		$id_product = filter($this->post('id_product'));
		$name = filter(trim(strtolower($this->post('name'))));
		$status = filter(trim(intval($this->post('status'))));
		$start_date = filter($this->post('start_date'));
		$end_date = filter($this->post('end_date'));
		
		$data = array();
		if ($id_product_flag == FALSE)
		{
			$data['id_product_flag'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_product_flag' => $id_product_flag));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_product == TRUE)
				{
					$param['id_product'] = $id_product;
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($start_date == TRUE)
				{
					$param['start_date'] = $start_date;
				}
				
				if ($end_date == TRUE)
				{
					$param['end_date'] = $end_date;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_product_flag, $param);
					
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
				$data['id_product_flag'] = 'not found';
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

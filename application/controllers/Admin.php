<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Admin extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('admin_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$photo = filter(trim($this->post('photo')));
		$admin_role = filter(trim(intval($this->post('admin_role'))));
		$status = filter(trim(intval($this->post('status'))));
		
		$data = array();
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($username == FALSE)
		{
			$data['username'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($password == FALSE)
		{
			$data['password'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($admin_role == FALSE)
		{
			$data['admin_role'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_admin_name($name) == FALSE && $name == TRUE)
		{
			$data['name'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_admin_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($admin_role, $this->config->item('default_admin_role')) == FALSE && $admin_role == TRUE)
		{
			$data['admin_role'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			if ($photo == FALSE)
			{
				$photo = '-';
			}
			
			if ($status == FALSE)
			{
				$status = 1;
			}
			
			$param = array();
			$param['name'] = $name;
			$param['email'] = $email;
			$param['username'] = $username;
			$param['password'] = md5($password);
			$param['photo'] = $photo;
			$param['admin_role'] = $admin_role;
			$param['status'] = $status;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				// bisa tambahin pengiriman email ke admin
				
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
		
        $id_admin = filter($this->post('id_admin'));
        
		$data = array();
        if ($id_admin == FALSE)
		{
			$data['id_admin'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_admin' => $id_admin));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_admin);
				
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
				$data['id_admin'] = 'not found';
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
		
		$id_admin = filter($this->get('id_admin'));
		$username = filter(trim(strtolower($this->get('username'))));
		$email = filter(trim(strtolower($this->get('email'))));
		$name = filter(trim(strtolower($this->get('name'))));
		
		$data = array();
		if ($id_admin == FALSE && $username == FALSE && $email == FALSE && $name == FALSE)
		{
			$data['id_admin'] = 'required (username/email/name)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_admin != '')
			{
				$param['id_admin'] = $id_admin;
			}
			elseif ($username != '')
			{
				$param['username'] = $username;
			}
			elseif ($name != '')
			{
				$param['name'] = $name;
			}
			else
			{
				$param['email'] = $email;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_admin' => $row->id_admin,
					'admin_role' => intval($row->admin_role),
					'email' => $row->email,
					'name' => $row->name,
					'photo' => $row->photo,
					'status' => intval($row->status),
					'username' => $row->username,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_admin'] = 'not found';
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
		$admin_role = filter(trim(intval($this->get('admin_role'))));
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
		
		if (in_array($order, $this->config->item('default_admin_order')) && ($order == TRUE))
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
		
		if (in_array($admin_role, $this->config->item('default_admin_role')) && ($admin_role == TRUE))
		{
			$admin_role = $admin_role;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($admin_role == TRUE)
		{
			$param['admin_role'] = $admin_role;
			$param2['admin_role'] = $admin_role;
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
					'id_admin' => $row->id_admin,
					'admin_role' => intval($row->admin_role),
					'email' => $row->email,
					'name' => $row->name,
					'photo' => $row->photo,
					'status' => intval($row->status),
					'username' => $row->username,
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
		
		$id_admin = filter($this->post('id_admin'));
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$photo = filter(trim($this->post('photo')));
		$admin_role = filter(trim(intval($this->post('admin_role'))));
		$status = filter(trim(intval($this->post('admin_role'))));
		
		$data = array();
		if ($id_admin == FALSE)
		{
			$data['id_admin'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($admin_role, $this->config->item('default_admin_role')) == FALSE && $admin_role == TRUE)
		{
			$data['admin_role'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_admin' => $id_admin));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($username == TRUE)
				{
					$param['username'] = $username;
				}
				
				if ($password == TRUE)
				{
					$param['password'] = md5($password);
					
					// bisa tambahin kirim email karena ganti password
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($email == TRUE)
				{
					$param['email'] = $email;
					
					// bisa tambahin kirim email konfirmasi karena ganti email
				}
				
				if ($admin_role == TRUE)
				{
					$param['admin_role'] = $admin_role;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_admin, $param);
					
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
				$data['id_admin'] = 'not found';
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
	
	// Dipakai untuk login karena butuh username & password (required) 
	function valid_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		
		$data = array();
		if ($username == FALSE)
		{
			$data['username'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($password == FALSE)
		{
			$data['password'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('username' => $username));
			
			if ($query->num_rows() > 0)
			{
				$check_pass = $query->row()->password;
				$pass = md5($password);
				
				if ($check_pass == $pass)
				{
					$data['valid'] = 'yes!';
					$validation = 'ok';
					$code = 200;
				}
				else
				{
					$data['valid'] = 'no!';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['username'] = 'not found';
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

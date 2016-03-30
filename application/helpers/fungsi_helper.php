<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('check_admin_email'))
{
    function check_admin_email($email)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model');
        
		$query = $CI->admin_model->info(array('email' => $email));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_admin_name'))
{
    function check_admin_name($name)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model');
        
		$query = $CI->admin_model->info(array('name' => $name));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_email'))
{
    function check_member_email($email)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('email' => $email));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_name'))
{
    function check_member_name($name)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('name' => $name));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('filter'))
{
    function filter($param)
    {
        $CI =& get_instance();

        $result = $CI->db->escape_str($param);
        return $result;
    }
}

if ( ! function_exists('valid_email'))
{
	function valid_email($email)
	{
		if ( !preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $email) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
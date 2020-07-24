<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/****************************************************************
	GRCentral v0.2
	File:			application\libraries\Grcentral.php
	Description:	Small but necessary functions that are used in different system controllers.
	
	2020 (c) Copyright GRCentral
	Get this on Github: http://github.com/lumian/grcentral
****************************************************************/

class Grcentral {
	
	var $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	// Function for checking whether the user is authorized
	public function is_user()
	{
		$logged_in = $this->CI->session->userdata('logged_in');
		
		if ($logged_in == $this->CI->config->item('login', 'auth'))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	// Function for determining whether settings should be applied.
	public function check_cfg_need_apply()
	{
		$this->CI->load->model('settings_model');
		$need_apply_db = $this->CI->settings_model->syssettings_get('cfg_need_apply');
		
		if ($need_apply_db == 'on')
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	// Function for setting the "need apply" flag
	public function set_cfg_need_apply()
	{
		$this->CI->load->model('settings_model');
		$need_apply_db = $this->CI->settings_model->syssettings_update(array('cfg_need_apply' => 'on'));
		
		return TRUE;
	}
	
	// Function for giving files to devices.
	public function forcedownload($file_name=NULL, $file_path=NULL)
	{
		if (!is_null($file_name) AND !is_null($file_path) AND file_exists($file_path))
		{
			$this->CI->load->helper('download');
			$data = file_get_contents($file_path);
			force_download($file_name, $data);
		}
		else
		{
			show_404();
		}
	}
}

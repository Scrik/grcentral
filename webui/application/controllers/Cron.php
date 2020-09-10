<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/****************************************************************
	GRCentral v0.2
	File:			application\controllers\Cron.php
	Description:	Cron jabs for GRCentral.
	
	2020 (c) Copyright GRCentral
	Get this on Github: http://github.com/lumian/grcentral
****************************************************************/

class Cron extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		// Loading models:
		$this->load->model('settings_model');
		$this->load->model('devices_model');
	}
	
	public function webcron($query=NULL)
	{
		
		if (!$this->grcentral->is_user())
		{
			redirect(index_page());
		}
		$result = FALSE;
		
		if (!is_null($query))
		{
			$json_data['query'] = $query;
			if ($query == 'gencfg')
			{
				$result = $this->generate_cfg();
			}
			else
			{
				show_404();
			}
			
			if ($result == TRUE)
			{
				$json_data['result'] = 'success';
				
			}
			else
			{
				$json_data['result'] = 'error';
			}
			echo json_encode($json_data);
		}
		else
		{
			show_404();
		}
	}
	
	public function clicron($type=NULL)
	{
		$result = FALSE;
		
		if ($this->input->is_cli_request())
		{
			if ($type == 'gencfg')
			{
				$result = $this->generate_cfg();
			}
			else
			{
				echo "Error: Type not found";
			}
		}
		else
		{
			echo "Error: Only CLI requests are allowed.";
		}
		if ($result == TRUE)
		{
			echo "Task completed: ".$type.PHP_EOL;
		}
		else
		{
			echo "Error";
		}
	}
	
	//
	// Service functions
	//
	private function generate_cfg()
	{
		$this->settings_model->syssettings_update(array('cfg_need_apply' => 'off'));
		$phones_list = $this->devices_model->getlist();
		$params_list = $this->settings_model->params_getlist();
		$models_list = $this->settings_model->models_getlist(array('group_data'=>TRUE));
		$servers_list = $this->settings_model->servers_getlist();
		
		if ($phones_list != FALSE AND $params_list != FALSE AND $models_list != FALSE AND $servers_list != FALSE)
		{
			$xml_path = $this->config->item('storage_path', 'grcentral').'cfg';
			$this->_clean_dir($xml_path);
			
			$xml_data = FALSE;
			
			foreach ($phones_list as $phone)
			{
				if ($models_list[$phone['model_id']]['params_group_id'] != '0' AND $phone['status_active'] == '1')
				{
					$params_id = $models_list[$phone['model_id']]['params_group_id'];
					$params_array_src = json_decode($params_list[$params_id]['params_json_data'], TRUE);
					$params_array = array();
					
					foreach($params_array_src as $param_string)
					{
						if (mb_stripos($param_string, "=") != FALSE)
						{
							$string_array = explode("=", $param_string);
							$key = trim($string_array[0]);
							$param = trim($string_array[1]);
							$params_array[$key] = $param;
						}
					}
					
					if (isset($params_array['P2']))
					{
						// Update admin password in DB for CTI
						$this->devices_model->edit($phone['id'], array('admin_password' => $params_array['P2']));
					}
					else
					{
						// Clear admin password in DB.
						$this->devices_model->edit($phone['id'], array('admin_password' => ''));
					}
					
					$accounts_array = json_decode($phone['accounts_data'], TRUE);
					
					if ($accounts_array != NULL)
					{
						foreach($accounts_array as $acc_num=>$acc_info)
						{
							if ($acc_num == '1')
							{
								
								$params_array['P271']	= $acc_info['active'];								// Account Active
								$params_array['P270']	= $acc_info['name'];								// Account Name
								$params_array['P47']	= $servers_list[$acc_info['voipsrv1']]['server'];	// SIP Server
								$params_array['P2312']	= $servers_list[$acc_info['voipsrv2']]['server'];	// Secondary SIP Server
								$params_array['P35']	= $acc_info['userid'];								// SIP User ID
								$params_array['P36']	= $acc_info['authid'];								// Authenticate ID
								$params_array['P34']	= $acc_info['password'];							// Authenticate Password
								$params_array['P3']		= $acc_info['name'];								// Name
								$params_array['P2380']	= '1';												// Account Display
								if (!is_null($servers_list[$acc_info['voipsrv1']]['voicemail_number']))
								{
									$params_array['P33']	= $servers_list[$acc_info['voipsrv1']]['voicemail_number']; // Voice Mail Access Number
								}
							}
							if ($acc_num == '2')
							{
								$params_array['P401'] 	= $acc_info['active'];
								$params_array['P417']	= $acc_info['name'];
								$params_array['P402']	= $servers_list[$acc_info['voipsrv1']]['server'];
								$params_array['P2412']	= $servers_list[$acc_info['voipsrv2']]['server'];
								$params_array['P404']	= $acc_info['userid'];
								$params_array['P405']	= $acc_info['authid'];
								$params_array['P406']	= $acc_info['password'];
								$params_array['P407']	= $acc_info['name'];
								$params_array['P2480']	= '1';
								if (!is_null($servers_list[$acc_info['voipsrv1']]['voicemail_number']))
								{
									$params_array['P426']	= $servers_list[$acc_info['voipsrv1']]['voicemail_number'];
								}
							}
							if ($acc_num == '3')
							{
								$params_array['P501'] 	= $acc_info['active'];
								$params_array['P517']	= $acc_info['name'];
								$params_array['P502']	= $servers_list[$acc_info['voipsrv1']]['server'];
								$params_array['P2512']	= $servers_list[$acc_info['voipsrv2']]['server'];
								$params_array['P504']	= $acc_info['userid'];
								$params_array['P505']	= $acc_info['authid'];
								$params_array['P506']	= $acc_info['password'];
								$params_array['P507']	= $acc_info['name'];
								$params_array['P2580']	= '1';
								if (!is_null($servers_list[$acc_info['voipsrv1']]['voicemail_number']))
								{
									$params_array['P526']	= $servers_list[$acc_info['voipsrv1']]['voicemail_number'];
								}
							}
							if ($acc_num == '4')
							{
								$params_array['P601'] 	= $acc_info['active'];
								$params_array['P617']	= $acc_info['name'];
								$params_array['P602']	= $servers_list[$acc_info['voipsrv1']]['server'];
								$params_array['P2612']	= $servers_list[$acc_info['voipsrv2']]['server'];
								$params_array['P604']	= $acc_info['userid'];
								$params_array['P605']	= $acc_info['authid'];
								$params_array['P606']	= $acc_info['password'];
								$params_array['P607']	= $acc_info['name'];
								$params_array['P2680']	= '1';
								if (!is_null($servers_list[$acc_info['voipsrv1']]['voicemail_number']))
								{
									$params_array['P626']	= $servers_list[$acc_info['voipsrv1']]['voicemail_number'];
								}
							}
						}
					}
					
					$xml_data[] = array(
						'mac'				=> $phone['mac_addr'],
						'params'			=> $params_array
					);
				}
			}
			
			if (is_array($xml_data))
			{
				foreach($xml_data as $xml)
				{
					$put_data = '<?xml version="1.0" encoding="UTF-8" ?>'.PHP_EOL;
					$put_data .= '<gs_provision version="1">'.PHP_EOL;
					$put_data .= '	<mac>'.$xml['mac'].'</mac>'.PHP_EOL;
					$put_data .= '	<config version="1">'.PHP_EOL;
					
					foreach($xml['params'] as $key=>$value)
					{
						$put_data .= '		<'.$key.'>'.$value.'</'.$key.'>'.PHP_EOL;
					}
					
					$put_data .= '	</config>'.PHP_EOL;
					$put_data .= '</gs_provision>'.PHP_EOL;
					
					$xml_file = $xml_path.'/cfg'.$xml['mac'].'.xml';
					file_put_contents($xml_file, $put_data);
					chmod($xml_file, 0666);
				}
				return TRUE;
			}
		}
		return FALSE;
	}
	
	private function _clean_dir($dir) {
		$files_list = glob($dir."/*");
		if (count($files_list) > 0)
		{
			foreach ($files_list as $file)
			{      
				if (file_exists($file))
				{
					unlink($file);
				}   
			}
		}
	}
}

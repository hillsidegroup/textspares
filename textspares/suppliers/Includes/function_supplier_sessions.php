<?php
if(!defined('IN_TEXTSPARES')) exit;

/*	$session->userdata['auth'] = intUserType
1 	Super Administrator	Backend
2 	Administrator		Backend
3 	Publisher			Backend
4 	Editor 				Backend
5 	All Backend 		Backend
6 	Supplier 			Frontend
7 	Registered User 	Frontend
*/

/* $this->userdata['account'] = intStatusCode;
1	Active
2	Blocked
3	Suspended
4	Inactive
*/

$errors = array();

class session {

	var $timeout = 3600; //1800 = 30 min
	var $userdata = array();
	var $loggedin = false;
	var $messages = '';

	function login()
	{
		global $db;

		session_set_cookie_params ( $this->timeout, '/', $_SERVER['HTTP_HOST'], false, true );
		session_start();
		$ssid = session_id();

		$user = $db->get_row('SELECT `company_user_id`,`login_time`,`session` FROM `supplier_company_users` WHERE `strUsername` = \''.$db->mysql_prep($_POST['_user']).'\' AND `strPassword` = \''.md5($_POST['_pswd']).'\'');

		if($user && $user->company_user_id > 0)
		{
			if( (time() - $user->login_time) > $this->timeout )
			{
				$db->query('UPDATE `supplier_company_users` SET `key` = NULL, `session` = NULL, `ip` = \''.$_SERVER['REMOTE_ADDR'].'\' WHERE `company_user_id` = \''.$user->company_user_id.'\'');
				$user->session = NULL;
				$user->key = NULL;
			}
		
			//if($user->session == NULL)
			//{
				$key = md5($_SERVER['HTTP_USER_AGENT'].$ssid.$user->company_user_id);
				$update = $db->query('UPDATE `supplier_company_users` SET `key` = \''.$key.'\', `session` = \''.$ssid.'\', `login_time` = \''.time().'\' WHERE `company_user_id` = \''.$user->company_user_id.'\'');

				if($update) {
					return true;
				} else {
					$this->logout();
				}
			//} else {
			//	$this->messages = 'Another user is already logged in with this account.';
			//}
		} else {
			$this->messages = 'Invalid Login Details.';
		}
		return false;
	}

	function check() 
	{
		global $db;

		if(isset($_COOKIE['PHPSESSID']) && strlen($_COOKIE['PHPSESSID']) > 16)
		{
			$start = false;

			$user = $db->get_row('SELECT 
				`inf`.`company_user_id`,
				`inf`.`strName`,
				`inf`.`strEmail`,
				`inf`.`intUserType`,
				`inf`.`dtmRegisterDate`,
				`inf`.`login_time`,
				`inf`.`intStatusCode`,
				`inf`.`dtmActivation`,
				`inf`.`key`,
				`inf`.`company_id`,
				`com`.`c_vat`,
				`com`.`c_waste`,
				`com`.`c_postcode`,
				`com`.`c_activation`,
				`com`.`c_admin_id`
				FROM `supplier_company_users` AS `inf`
				JOIN `supplier_company` AS `com` ON `com`.`company_id` = `inf`.`company_id`
				WHERE `inf`.`session` = \''.$_COOKIE['PHPSESSID'].'\'');
			if(!empty($user->company_user_id) && $user->company_user_id > 0 && $user->key != NULL)
			{
				if(md5($_SERVER['HTTP_USER_AGENT'].$_COOKIE['PHPSESSID'].$user->company_user_id) == $user->key)
				{
					if( (time() - $user->login_time) > $this->timeout )
					{
						$this->userdata['id'] = $user->company_user_id;
						$this->logout();
					}
					else
					{
						$this->userdata['id'] = $user->company_user_id;
						
						// Account Status (Active/Banned etc)
						$this->userdata['account'] = $user->intStatusCode;
						// Company Registration Activation Status
						$this->userdata['company_active'] = $user->c_activation;
						// User Registration Activation Status
						$this->userdata['activated'] = ($user->c_activation > 0) ? $user->dtmActivation : 0;
						// User Access Authorization Level
						$this->userdata['auth'] = $user->intUserType;
						$this->userdata['registered'] = $user->dtmRegisterDate;
						$this->userdata['lastlogin'] = $user->login_time;
						
						$this->userdata['companyid'] = $user->company_id;
						$this->userdata['name'] = $user->strName;
						$this->userdata['email'] = $user->strEmail;
						$this->userdata['vat'] = $user->c_vat;
						$this->userdata['waste'] = $user->c_waste;
						$this->userdata['postcode'] = $user->c_postcode;
						$this->userdata['company_admin'] = ($user->company_user_id == $user->c_admin_id) ? true : false;
						$this->loggedin = true;

						session_id($_COOKIE['PHPSESSID']);
						$start = true;
					}
				} else {
					$this->logout();
				}
			}
			if($start) {
				session_set_cookie_params ( $this->timeout, '/', $_SERVER['HTTP_HOST'], false, true );
				@session_start();
				$db->query('UPDATE `supplier_company_users` SET `login_time` = \''.time().'\' WHERE `company_user_id` = \''.$user->company_user_id.'\'');
				return true;
			}
		} else {
			unset($this->userdata);
		}
		return false;
	}
	
	function access($level)
	{
		$level = (int) $level;
		if(!empty($this->userdata['auth']) && $this->userdata['auth'] > 0 && $this->loggedin == true)
		{
			if($this->userdata['account'] == 2)
			{
				$this->messages = 'Your account has been blocked.';
				return false;
			}
			else
			if($this->userdata['account'] == 3)
			{
				$this->messages = 'Your account has been suspended.';
				return false;
			}
			else
			if($this->userdata['account'] == 4)
			{
				$this->messages = 'Your account has become inactive, Please contact an admin to have your account reactivated.';
				return false;
			}
			else
			{
				if($this->userdata['activated'] > 0)
				{
					if(is_int($level))
					{
						if($this->userdata['auth'] <= $level) {
							return true;
						}
						else
						{
							$this->messages = 'You are not authorised to access this area.';
							return false;
						}
					}
					else
					{
						$this->messages = 'There was a problem with Authorization, Please contant an admin if the problem persists.';
						return false;
					}
				} else {
					if($this->userdata['company_active'] > 0)
					{
						$this->messages = 'Your account has not been activated, Please Contact your company manager responsible for your account.';
						return false;
					}
					else
					{
						$this->messages = 'Sorry, Your company account has not yet been Activated by TextSpares.';
						return false;
					}
				}
			}
		}
		else
		{
			//$this->messages = 'Please Login or Register an Account.';
			return false;
		}
		return false;
	}
	
	function logout() {
	
		global $db;
	
		$sql = 'UPDATE `supplier_company_users` SET `login_time` = \''.time().'\', `key` = NULL, `session` = NULL WHERE `company_user_id` = \''.$this->userdata['id'].'\'';
		$update = $db->query($sql);
		if($update)
		{
			@session_destroy();
			$this->loggedin = false;
			$this->userdata = false;
			unset($_COOKIE['PHPSESSID']);
			
			header('Location:/suppliers/?_action=login');
		}
	}
}

?>
<?php
if(!defined('IN_TEXTSPARES')) exit;

class session {

	var $timeout = 1800; //1800 = 30 min
	var $userdata = array();
	var $loginkey = false;
	var $loggedin = false;
	var $messages = false;

	function __construct()
	{
		$this->loginkey = (isset($_REQUEST['k'])) ? $_REQUEST['k'] : false;

		if($this->loginkey)
		{
			list($un,$pw) = explode('-',$this->loginkey);
			if(strlen($un) > 10 && strlen($pw) > 10)
			{
				$this->login($un,$pw);
			} else {
				$this->loginkey = false;
				$this->messages = 'Invalid Login Key!';
			}
		}
	}

	function login($un = false,$pw = false)
	{
		global $db;

		session_set_cookie_params ( $this->timeout, '/', $_SERVER['HTTP_HOST'], false, true );
		session_start();
		$ssid = session_id();
		
		if(validEmail($_POST['_username']))
		{
			$username = '`cust_email` = \''.$db->mysql_prep($_POST['_username']).'\'';
		} else {
			$username = '`cust_username` = \''.$db->mysql_prep($_POST['_username']).'\'';
		}

		if($this->loginkey)
		{
			$user = $db->get_row('SELECT `customer_id`,`cust_email`,`login_time`,`session` FROM `customer_information` WHERE `cust_password` = \''.$db->mysql_prep($pw).'\'');
			if(md5($user->cust_email) != $un) $user = false;
		} else {
			$user = $db->get_row('SELECT `customer_id`,`login_time`,`session` FROM `customer_information` WHERE '.$username.' AND `cust_password` = \''.md5($_POST['_password']).'\'');
		}

		if($user && $user->customer_id > 0)
		{
			if( (time() - $user->login_time) > $this->timeout )
			{
				$db->query('UPDATE `customer_information` SET `key` = NULL, `session` = NULL WHERE `customer_id` = \''.$user->customer_id.'\'');
				$user->session = NULL;
				$user->key = NULL;
			}
		
			if($user->session == NULL)
			{
				// -- Modify this key to add consistancy attributes [[key]]
				$key = md5($_SERVER['HTTP_USER_AGENT'].$ssid.$user->customer_id);
				
				$update = $db->query('UPDATE `customer_information` SET `key` = \''.$key.'\', `session` = \''.$ssid.'\', `login_time` = '.time().' WHERE `customer_id` = \''.$user->customer_id.'\'');

				if($update) {
					return true;
				} else {
					$this->logout();
				}
			} else {
				$this->messages = 'Already Logged IN.';
			}
		} else {
			$this->messages = 'Invalid login details. You may use your username or e-mail address to login';
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
				`customer_id`,
				`cust_name`,
				`cust_phone`,
				`cust_email`,
				`recieve_sms`,
				`recieve_emails`,
				`account_status`,
				`login_time`,
				`key`
				FROM `customer_information`
				WHERE `session` = \''.$_COOKIE['PHPSESSID'].'\'');
			if($user && $user->key != NULL)
			{
				// -- Modify this key to add consistancy attributes [[key]]
				if(md5($_SERVER['HTTP_USER_AGENT'].$_COOKIE['PHPSESSID'].$user->customer_id) == $user->key)
				{
					if( (time() - $user->login_time) > $this->timeout )
					{
						$this->userdata['id'] = $user->customer_id;
						$this->logout();
					}
					else
					{
						$this->userdata['id'] = $user->customer_id;
						$this->userdata['name'] = $user->cust_name;
						$this->userdata['phone'] = $user->cust_phone;
						$this->userdata['email'] = $user->cust_email;
						$this->userdata['lastlogin'] = $user->login_time;
						$this->userdata['recieve_sms'] = $user->recieve_sms;
						$this->userdata['recieve_emails'] = $user->recieve_emails;
						$this->userdata['auth'] = $user->account_status;
						$this->userdata['level'] = $user->account_status;
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
				session_start();
				$db->query('UPDATE `customer_information` SET `login_time` = '.time().' WHERE `customer_id` = \''.$user->customer_id.'\'');
				return true;
			}
		} else {
			unset($this->userdata);
		}
		return false;
	}
	
	function access($level)
	{
		if($this->userdata['level'] > 0)
		{
			if($this->userdata['auth'] == 2)
			{
				$this->messages = 'Your account has been blocked.';
				return false;
			}
			else
			if($this->userdata['auth'] == 3)
			{
				$this->messages = 'Your account has been suspended.';
				return false;
			}
			else
			if($this->userdata['auth'] == 4)
			{
				$this->messages = 'Your account has become inactive, Please contact an admin to have your account reactivated.';
				return false;
			}
			else
			{
				if(is_numeric($level))
				{
					if($this->userdata['level'] == $level) {
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
			}
		}
		else
		{
			$this->messages = 'Your account is awaiting activation.';
			return false;
		}
	
	}
	
	function logout() {
	
		global $db;
	
		$update = $db->query('UPDATE `customer_information` SET `login_time` = '.time().', `key` = NULL, `session` = NULL WHERE `customer_id` = \''.$this->userdata['id'].'\'');
		
		@session_destroy();
		$this->loggedin = false;
		unset($this->userdata, $_COOKIE['PHPSESSID']);
		
		header('Location:/');
		
	}

}

?>
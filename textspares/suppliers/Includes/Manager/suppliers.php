<?php

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

if(!defined('IN_TEXTSPARES') || $session->access(2) != true) { exit; }

$error = array();

//-------------
// DISPLAY DATA
//-------------
switch ($subaction) {

//---------------
//DEFAULT DISPLAY
//---------------

	default:
	
		include('Includes/pager.inc.php');

		$uitest = false;
		$errors = array();
		
		//-----------------
		// PROCESS REQUESTS
		
		$company_id = (!empty($_REQUEST['CID'])) ? (int) $_REQUEST['CID'] : 0;
		
		$code = (!empty($_REQUEST['CODE'])) ? $_REQUEST['CODE']  : false;
		switch($code)
		{
			//Deactivate Company
			case '00':
			//Activate Company
			case '01':
				$active = ($code == '00') ? 0 : time();
				if(is_int($company_id) && $company_id > 0)
				{
					$sql = 'UPDATE `supplier_company` SET `c_activation` = '.$active.' WHERE `company_id` = '.$company_id;
					$db->query($sql);
					
					// When Company is being Activated:
					if($code == '01')
					{
						$sql = 'SELECT `c`.`c_admin_id`,`u`.`strName`,`u`.`strEmail` FROM `supplier_company` AS `c` JOIN `supplier_company_users` AS `u` ON `u`.`company_user_id` = `c`.`c_admin_id` WHERE `c`.`company_id` = '.$company_id;
						$user = $db->get_row($sql);

						if($user->c_admin_id > 0)
						{
							$sql = 'UPDATE `supplier_company_users` SET `dtmActivation` = '.time().' WHERE `company_user_id` = '.$user->c_admin_id;
							$db->query($sql);
							
							$mail = 'Dear '.$user->strName."\n\n".'Your Company Account has now been activated.'."\n".'You now may <a href="'.ROOT.'">Login</a> and start using TextSpares'."\n\n".'Thank you'."\n\n".'TextSpares Management Team'."\n\n";
							send_email($user->strEmail,'support@textspares.co.uk','TextSpares.co.uk - Company Account Activated',$mail);
						}
					}
				}
			break;
			
			case '02':
				if(isset($_POST['send_user_update']))
				{
					$company_user = (int) ereg_replace('[^0-9]','',$_POST['id']);
					$access_level = (int) ereg_replace('[^0-9]','',$_POST['access_level']);
					$status_level = (int) ereg_replace('[^0-9]','',$_POST['account_status']);
					
					if($company_user > 0 & $access_level > 0 & $status_level > 0)
					{
						$sql = 'UPDATE `supplier_company_users` SET `intUserType` = \''.$access_level.'\', `intStatusCode` = \''.$status_level.'\' WHERE `company_user_id` = \''.$company_user.'\' LIMIT 1';
						$db->query($sql);
					}
				}
			break;
		}
		
		// PROCESS REQUESTS
		//-----------------

		//-----------------------
		// GET  LIST OF COMPANIES
		$sql = 'SELECT
				`c`.`company_id`,`c`.`last_payment`,`c`.`c_name`,`c`.`c_phone`,`c`.`c_sales`,`c`.`c_mobile`,`c`.`c_country`,`c`.`c_vat`,`c`.`c_waste`,`c`.`c_activation`,
				(SELECT COUNT(*) FROM `supplier_quotes` WHERE `company_id` = `c`.`company_id`) AS `quoted`
				FROM `supplier_company` AS `c` WHERE 1 ORDER BY `c`.`company_id` DESC';
		$company = mysql_query($sql);
		if(!$company) 
		{
			$error[] = '<b>MYSQL ERROR</b><br/>'.mysql_error().'<br/><br/><i>'.$sql.'</i><br/><br/>Line: '.__LINE__.' in ' . $_SERVER['SCRIPT_NAME'];
		} else {
			if(mysql_num_rows($company) == 0) {
				$company = false;
			}
		}
		// ^ GET  LIST OF COMPANIES
		//-------------------------

		// PRINT ANY ERRORS
		if(count($error) > 0) {
			foreach($error AS $report) {
				echo '<p class="error">'.$report.'</p>';
			}
		} else {
		// OUTPUT CONTENT
		?>
		<div id="latest_requests" style="border:none;">
			<h1 style="width:100%;"><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?php echo ROOT; ?>Images/quotes.png">Management &gt; Suppliers Accounts</h1>
			<br clear="all" />
			<script type="text/javascript">
			$(document).ready(function()
			{
			});
			</script>
			
			<div class="request_tbl ui-corner-all">
				<table>
					<tbody>
						<tr>
							<th width="50">ID</th>
							<th width="*">Company Name / Quotes</th>
							<th width="500">Company Info</th>
							<th width="100">Activation</th>
						</tr>
<?php
				if($company)
				{
					while($print = mysql_fetch_object($company))
					{
						$activation = ($print->c_activation > 0) ? (($print->company_id == 1) ? '' : '<a href="'.ROOT.SCRIPT.'?_action=suppliers&amp;CODE=00&amp;CID='.$print->company_id.'">Deactivate</a>') : '<a href="'.ROOT.SCRIPT.'?_action=suppliers&amp;CODE=01&amp;CID='.$print->company_id.'">Activate</a>';
					
						echo '<tr class="'.(($print->c_activation > 0) ? 'quote_blue' : ' quote_red').'"><td>'.$print->company_id.'</td><td>'.$print->c_name.' / <b>'.$print->quoted.'</b></td><td>P: '.$print->c_phone.' | M: '.$print->c_mobile.' | S: '.$print->c_sales.'</td><td style="text-align:center;">'.$activation.'</td></tr>';
						echo '<tr style="background-color:#ccc;font-weight:bold;padding:0px;margin:0px;"><td></td><td colspan="3"><table cellpadding="0" cellspacing="0" border="0"><tr><td width="200">Name</td><td width="150">Position</td><td width="200">Username</td><td>Access Level</td><td>Account</td><td></td></tr></table></td></tr>';
						$sql = 'SELECT `company_user_id`,`strName`,`strPosition`,`strUsername`,`strEmail`,`intUserType` AS `account_access` ,`intStatusCode` AS `account_status` FROM `supplier_company_users` WHERE `company_id` = \''.$print->company_id.'\' ORDER BY `strName` DESC';
						$users = $db->get_results($sql);
						
						if($users)
						{
							foreach($users AS $user)
							{
								if(is_array($settings['account_access_levels']))
								{
									$access = '';
									foreach($settings['account_access_levels'] AS $level => $text)
									{
										$selected = ($level == $user->account_access) ? ' selected="selected"' : '';
										$style = ($settings['account_access_areas'][$level] == 'Backend') ? ' style="background-color:red;color:white;"' : '';
										$access .= '<option value="'.$level.'"'.$selected.$style.'>'.$text.'</option>';
									}
								} else {
									$access = '<option value="">Internal Error</option>';
								}
								
								if(is_array($settings['account_status_levels']))
								{
									$status = '';
									foreach($settings['account_status_levels'] AS $level => $text)
									{
										$selected = ($level == $user->account_status) ? ' selected="selected"' : '';
										$status .= '<option value="'.$level.'"'.$selected.'>'.$text.'</option>';
									}
								} else {
									$status = '<option value="">Internal Error</option>';
								}
								$exclude_users = array(1,3);
								echo '<tr><td style="text-align:right;"><b>&gt;</b></td><td colspan="3"><form name="update_user" action="'.ROOT.SCRIPT.'?_action=suppliers&amp;CODE=02" method="post"><table><tr><td width="200">'.$user->strName.'</td><td width="150">'.$user->strPosition.'</td><td width="200">'.$user->strUsername.'</td><td><select name="access_level">'.$access.'</select></td><td><select name="account_status">'.$status.'</select></td><td><input type="hidden" name="id" value="'.$user->company_user_id.'" /><input type="submit" name="send_user_update" value="Update" '.(in_array($user->company_user_id,$exclude_users) ? 'disabled="disabled"' : '').' /></td></tr><tr><td colspan="5">'.$user->strEmail.'</td></tr></table></form></td></tr>';
							}
						} else {
							echo '<tr><td colspan="3">No users found for this company.</td></tr>';
						}
					}
					mysql_free_result($company);
				}
?>
					</tbody>
				</table>
			</div>
		</div>

<?php
			$pager = new Pager();
			//$pager->page_size = 25;
			$pager->display_rpp = true;
		}
	break;
}
?>
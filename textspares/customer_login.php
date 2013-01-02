<?php
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();

$timeout = 7200;

if(!empty($msg))
{
	$msgtxt = $msg;
} else {
	$msgtxt = 'Sign In To TextSpares';
}

$login = false;
$message = '';
if(isset($_POST['customer_reset']))
{

	if(!preg_match("/^[0-9a-zA-Z_]$/", $_POST['_password']) === 0)
	{
		$message = 'Password must contain letters and digits only.';
	} else {
		if(strlen($_POST['_password']) > 5)
		{
			if($_POST['_password'] == $_POST['_matched']) {
				$cust = $db->get_row('SELECT `customer_id`,`stamp` FROM `customer_reset` WHERE `key` = \''.md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']).'\' LIMIT 1');
				if($cust->customer_id > 0 && (time() - $cust->stamp) < $timeout) {
					$db->query('DELETE FROM `customer_reset` WHERE `customer_id` = \''.$cust->customer_id.'\'');
					$result = $db->query('UPDATE `customer_information` SET `cust_password` = \''.md5($db->mysql_prep($_POST['_password'])).'\' WHERE `customer_id` = \''.$cust->customer_id.'\'');
					if($result) {
						$_REQUEST['rs'] = NULL;
						$message = 'Your Password has been reset, You can now login!';
						$login = true;
					} else {
						$message =  'There was a problem with the database.';
					}
				} else {
					$message = 'Sorry, The link you used is invalid or has expired!';
				}
			} else {
				$message = 'Your password differs from the other field.';
			}
		} else {
			$message = 'The password you entered was too short. 6 Characters or longer.';
		}
	}
	$db->query('DELETE FROM `customer_reset` WHERE ('.time().' - `stamp`) > '.$timeout.'');
}
if(isset($_REQUEST['rs']) && strlen($_REQUEST['rs']) > 26 && $login == false)
{
	$cust = $db->get_row('SELECT `customer_id`,`stamp` FROM `customer_reset` WHERE `key` = \''.md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']).'\' LIMIT 1');
	
	if($cust->customer_id > 0 && (time() - $cust->stamp) < $timeout) {
		$msgtxt = 'Enter NEW Password';
		$form = '
			<form name="reset" method="POST" action="'.BASE.'customer_login.php">
			<table cellpadding="2" cellspacing="2" width="100%" border="0" >
			<tr><td class="formtext">Enter New Password:</td><td><input type="password" name="_password" class="formtxtbox">
			<tr><td class="formtext">Retype New Password:</td><td><input type="password" name="_matched" class="formtxtbox">
			<tr><td>&nbsp;</td><td><input type="hidden" name="rs" value="'.$_REQUEST['rs'].'" /><input type="submit" name="customer_reset" value="Update Password"></td></tr>	
			</table>
			</form>
		';
	} else {
		//$db->query('DELETE FROM `customer_reset` WHERE ('.time().' - `stamp`) > '.$timeout.'');
		$form = 'Sorry, This link is invalid or has expired.';
	}
}
else
{
$form = '
	<form name="logfrm" method="post" action="'.BASE.'customer_login.php">
	<table cellpadding="2" cellspacing="2" width="100%" border="0" >
	<tr><td class="formtext" align="right" width="200">Username or E-Mail Address:</td><td><input type="text" name="_username" class="formtxtbox">
	<tr><td class="formtext" align="right">Password:</td><td><input type="password" name="_password" class="formtxtbox">
	<tr><td>&nbsp;</td><td><input type="hidden" name="_login" value="true" /><input type="submit" name="customer_login" value="Login"></td></tr>	
	</table>
	</form>
';
}
?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="116" colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%" height="80" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong><br />
                  We have wide range of new and used car parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1"><?=$msgtxt?></td></tr>
            	</table>
            </td></tr>
            <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
				<?=(($message) ? '<tr><td><p class="errText">'.$message.'</p></td></tr>' : '');?>
              		<tr><td>
					<?=$form;?>
              	</td></tr>
              </table>
               <tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
		</td></tr>
		</table>
	</td></tr>
</table> 

<?php bottom(); ?>
<?php
session_start();
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();

if(!empty($_POST['email']))
{
	$bymail = false;
	if(validEmail($_POST['email'])) {
		$bymail = true;
		$sql = 'SELECT `customer_id`,`cust_name`,`cust_username` FROM `customer_information` WHERE `cust_email` = \''.$db->mysql_prep($_POST['email']).'\' LIMIT 1';
		$data = $db->get_row($sql);
	} else {
		$sql = 'SELECT `customer_id`,`cust_name`,`cust_username` FROM `customer_information` WHERE `cust_username` = \''.$db->mysql_prep($_POST['email']).'\' LIMIT 1';
		$data = $db->get_row($sql);
	}
	$clientname = $data->cust_name;
	$username = $data->cust_username;
	$password = '';
	if($data->customer_id > 0)
	{
		if(!empty($_POST['reset']) && $_POST['reset'] == 'password')
		{
			$key = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
			$password  = '<tr><td style="font-size:14px;"><b>Reset Password:</b> <a href="'.BASE.'customer_login.php?rs='.$key.'" >Reset my password.</a> - This link is only valid for 2 hours.<br/><br/><b>Please make sure you use the same web browser to reset your password.</b><br/>Resetting your password will fail if you use request a new password <br/>with one web browser and try to use the reset link in another.</td></tr>';
			$result = $db->query('INSERT INTO `customer_reset`(`stamp`,`key`,`customer_id`) VALUES(\''.time().'\',\''.$key.'\',\''.$data->customer_id.'\')');
			if(!$result) $db-query('UPDATE `customer_reset` SET `stamp` = \''.time().'\', `key` = \''.$key.'\' WHERE `customer_id` = \''.$data->customer_id.'\' ');
		}
		else
		{
			$password = '<tr><td style="font-size:14px;"><b>Your Password:</b> You did not request a password reset.</td></tr>';
		}
$message = <<<EMAIL
<html>
<head>
<style>
td{ font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
color:#464646;
}
</style>
</head>
<body>
<table cellpadding="1" cellspacing="1" width="100%">
	<tr><td><img src="http://www.textspares.co.uk/images/head.jpg"></td></tr>
	<tr><td style="font-size:14px;"><br/><br/><b>Here is your <u>Username</u>:</b> {$username} &nbsp; (Please note that this is not a password)</td></tr>
	{$password}
</body>
</html>
EMAIL;

		if($bymail)
		{
			if(mail($_POST['email'],'Account Info',$message,'From:accounts@textspares.co.uk'."\n".'Content-type: text/html'))
			{
				echo 'An e-mail has been sent to you with your details.<br/>Please remember to check you Mailbox Spam Folder!';
			}
			else
			{
				echo 'There was a problem with the mail server. Please contact <b>accounts@textspares.co.uk</b>';
			}
		} else {
			echo '
			<table cellpadding="1" cellspacing="1" width="100%">
				<tr><td style="font-size:14px;"><b>Here is your <u>Username</u>:</b> '.$username.'</td></tr>
				'.$password.'
			</table>
			';
		}
	}
	else
	{
		echo 'Sorry, We do not have this e-mail address or Username in our records.';
	}
} else {
	if(isset($_POST['email']))
	{
		echo '<p><b>Invalid E-Mail Address</b></p>';
	}
	?>
	<form action="" method="POST">
	<b>Enter your Username or E-mail Address:</b><br/><br/>
	 &nbsp; <input type="text" name="email" value="<?=(!empty($_POST['email']) ? $_POST['email'] : '');?>" size="32" /><br/><br/>
	I want to reset my password: <input type="checkbox" name="reset" value="password" checked /><br/><br/>
	<input type="submit" name="retrieve" value="Recover Login Details" style="font-size:14px;" />
	</form>
<?php 
} 
bottom();
?>
<?php

if(!defined('IN_TEXTSPARES')) { exit; }

/**
* Module:		Forgot Password
* Version:		v0.99
* File:			forgot_pswd.php
* Copyright:	Copyright 2006 - 2010 e-Orchards Ltd.
* License:		see "Application License" details.

Module Info
--------------------------------------------------------------|
Resets user's password


Funtion List
--------------------------------------------------------------|

Module Specific
***************
createRandomPassword()		- Creates new random password for user account
showForm()					- displays the email form
sendMail()					- Calls the random password generatot and sends the email notification to the user.



Revision Info
--------------------------------------------------------------|
v0.5-v0.98 		| General beta development/improvements
v0.98-v0.98.1	| Module Info boxes implemented
v0.98.1-v0.99	| URL rewrite support


**********************************************************************************/
/****** Module Code *******/

include_once "Includes/validation.inc.php";

$vdata = new Validator();
$vdata->checks["text"] = ".{1,}";
$vdata->checks["dname"] = "^.{1,64}$";
$vdata->checks["select"] = "/[^invalid]/";
$vdata->checks["number"] = "^[0-9][0-9]{0,}$";
$vdata->checks["email"] = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
$vdata->checks["phone"] = "^[0][0-9[:space:]\-]{10,13}$";
$vdata->checks["pswd"] = "^[a-z]{1}[_a-z0-9]{7,24}";
$vdata->checks["user"] = "^[a-z0-9]+[_a-zA-Z0-9-]{5,24}$";
$vdata->checks["pcode"] = "(((^[BEGLMNS][1-9]\d?)|(^W[2-9])|(^(A[BL]|B[ABDHLNRST]|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]|F[KY]|G[LUY]|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]|M[EKL]|N[EGNPRW]|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKL-PRSTWY]|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)\d\d?)|(^W1[A-HJKSTUW0-9])|(((^WC[1-2])|(^EC[1-4])|(^SW1))[ABEHMNPRVWXY]))(\s*)?([0-9][ABD-HJLNP-UW-Z]{2}))$|(^GIR\s?0AA$)";

$vdata->newuserchecks = false;

function createRandomPassword() {

    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

function showForm($vdata)
{
	?>
     <script type="text/javascript">
			$(function()
			{
				//hover states on the static widgets
				$('#registration_form a').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);
				
				$('#pswd_reset_btn').click(function(e){
					e.preventDefault();							  
					$("form[name=_forgot_pswd]").submit();
				});
			});
			</script>
	<div id="registration_form">
		<form name="_forgot_pswd" action="<?=ROOT;?>?_action=password&_subaction=validate" method="post">
		<table class="newuser_tbl">
			<tr>
				<th>Please Enter your email address.</th>
			</tr>
			<tr>
				<td><input type="text" name="_email" class="ui-corner-all" style="width:250px;" value="" maxlength="165" /></td>
			</tr>
			<tr>
				<td colspan="2"><br />
                <p style="margin: 15px 0pt 15px 8px;"><a href="#" id="pswd_reset_btn" class="ui-state-default ui-button ui-corner-all"><span class="ui-icon ui-icon-refresh"></span>Reset Password</a></p>
                </td>
			</tr>
		</table>
		</form>
	</div>
	<br />
	<?php		
}

function sendMail($vdata)
{
	global $db;

	$vdata->newuserchecks = false;
	$vdata->validate($_POST['_email'],"email");

	if($vdata->form_error==false)
	{
		if($userdata = $db->get_row("SELECT `strUsername`,`strEmail`,`company_user_id` FROM `supplier_company_users` WHERE `strEmail` = '".$db->mysql_prep($_POST['_email'])."'") )
		{
			$user = $userdata->strUsername;
			$sendto = $userdata->strEmail;
			$newpswd = createRandomPassword();
			$pswd = md5($newpswd);
			//print($newpswd);

			$message = "User Details\n-----------------------\n\nUsername: $user\n\nYour password has been reset to the following:\nPassword: $newpswd\n\n\nDetails requested at ".date("m:h d-m-y")." by ".$_SERVER['REMOTE_ADDR'];
			$from = "no_reply@".$_SERVER['HTTP_HOST'];

			if(!mail( $sendto, "User Details Reminder",$message, "From: $from" ))
			{
				print("<div class='error_box'><p>There was an error sending the email. Please contact an administrator.</p></div>");
			} else {
				$sql = 'UPDATE `supplier_company_users` SET `strPassword` = \''.$pswd.'\' WHERE `company_user_id` = \''.$userdata->company_user_id.'\'';
				$reset = $db->query($sql);
				print("<div class='valid_box'><p>Your password has been reset and notification has been sent your email address.</p></div>");
				//print("<pre>".$_SERVER['HTTP_HOST']."</pre>");
			}
		} else {
			print("<div class='error_box'><p>The email address was not found in the database.</p></div>");
			showForm($vdata);
		}
	} else {
		print("<div class='error_box'><p>Please enter a valid email address.</p></div>");
		showForm($vdata);
	}
	//print("<pre>$message</pre>");
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////	
?>
	<h1>User Password Reset</h1>
<?php		 

if( isset($_GET['_subaction']) && $_GET['_subaction']=="validate" )
	{
	sendMail($vdata);	
	}
else {
	showForm($vdata);
	}
?>
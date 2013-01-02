<?php

if(!defined('IN_TEXTSPARES')) { exit; }

/**
* Module:		Registration Form
* Version:		v0.99
* File:			registraton.php
* Copyright:	Copyright 2006 - 2010 e-Orchards Ltd.
* License:		see "Application License" details.

Module Info
--------------------------------------------------------------|
Allows users to register on the site


Funtion List
--------------------------------------------------------------|

Module Specific
***************
display_form()				- Displays the registration form
vf()						- Returns if a field requires validation. Set by the modules configuration options.
sf()						- Returns if a field should be displayed. Set by the modules configuration options.
rtn_vcode()					- Returns the validation code for the registration form.
process_form()				- Processes the form.

Generic
**************
rtn_select()				- Takes an input variable as a field within the user table to be returned relating to the input user id.
rtn_data()					- Returns POST or $db data accordingly



Revision Info
--------------------------------------------------------------|
v0.5-v0.98 		| General beta development/improvements
v0.98-v0.98.1	| Module Info boxes implemented
v0.98.1-v0.99	| URL rewrite support


**********************************************************************************/
/****** Module Code *******/
include_once "Includes/validation.inc.php";

$vdata = new Validator();
$vdata->checks["text"] = "";
$vdata->checks["dname"] = "/^.{1,64}$/";
$vdata->checks["select"] = "/[^invalid]/";
$vdata->checks["number"] = "/^[0-9][0-9]{0,}$/";
$vdata->checks["vcode"] = "/^[0-9][0-9]{5}$/";
$vdata->checks["email"] = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
$vdata->checks["phone"] = "/^[0][0-9[:space:]\-]{10,13}$/";
$vdata->checks["pswd"] = "/^[a-zA-Z0-9]{1}[_a-zA-Z0-9]{7,24}/";
$vdata->checks["user"] = "/^[a-zA-Z0-9]+[_a-zA-Z0-9]{5,24}$/";
$vdata->checks["pcode"] = "/(((^[BEGLMNS][1-9]\d?)|(^W[2-9])|(^(A[BL]|B[ABDHLNRST]|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]|F[KY]|G[LUY]|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]|M[EKL]|N[EGNPRW]|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKL-PRSTWY]|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)\d\d?)|(^W1[A-HJKSTUW0-9])|(((^WC[1-2])|(^EC[1-4])|(^SW1))[ABEHMNPRVWXY]))(\s*)?([0-9][ABD-HJLNP-UW-Z]{2}))$|(^GIR\s?0AA$)/";

$_REQUEST['_subaction'] = (!empty($_REQUEST['_subaction'])) ? $_REQUEST['_subaction'] : false;

function display_form($vdata)
{
	if($_REQUEST['_subaction'])
	{	
		$url = $_SERVER['REQUEST_URI'];
	} else {
		$url = $_SERVER['REQUEST_URI'].'&_subaction=validate';
	}
	?>
	<script type="text/javascript">
	$(function(){
			//hover states on the static widgets
			$('#registration_form a').hover(
				function() { $(this).addClass('ui-state-hover'); }, 
				function() { $(this).removeClass('ui-state-hover'); }
			);
			$('#register_btn').click(function(e){
				e.preventDefault();							  
				$("form[name=_newUserForm]").submit();
			});
		});
	</script>
	<h1>Account Registration</h1>
	<p><strong style="color:red;">Registration Notice:</strong><ul>This form is for registering a company only.<br/>If your company is already registered then please ask the manager to create a login account for you in the company profile.<br/><br/>Thank You.</ul></p>
	<form name="_newUserForm" action="<?=$url;?>" method="post">
	<?php
	if($vdata->form_error == true){ ?>
	<div class="ui-state-error ui-corner-all m5">
	<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>There was a problem with your form. Please amend highlighted fields accordingly.</p>
	<?php 
	if($vdata->duplicate_user == true) { echo '<p>*The username you supplied is already present on the database.</p>'; }
	if($vdata->duplicate_email == true) { echo '<p>*The email you supplied is already used by another account.</p>'; }
	?>
	</div>
	<?php 
	}
	$_username = (!empty($_POST['_username'])) ? $_POST['_username'] : '';
	$_email = (!empty($_POST['_email'])) ? $_POST['_email'] : '';
	$_password = (!empty($_POST['_password'])) ? $_POST['_password'] : '';
	$_vpassword = (!empty($_POST['_passwordVerify'])) ? $_POST['_passwordVerify'] : '';

	$_title = (!empty($_POST['_title'])) ? $_POST['_title'] : '';
	$_forename = (!empty($_POST['_forename'])) ? $_POST['_forename'] : '';
	$_surname = (!empty($_POST['_surname'])) ? $_POST['_surname'] : '';
	$_company = (!empty($_POST['_company'])) ? $_POST['_company'] : '';
	$_position = (!empty($_POST['_position'])) ? $_POST['_position'] : '';
	$_address1 = (!empty($_POST['_address1'])) ? $_POST['_address1'] : '';
	$_address2 = (!empty($_POST['_address2'])) ? $_POST['_address2'] : '';
	$_city = (!empty($_POST['_city'])) ? $_POST['_city'] : '';
	$_county = (!empty($_POST['_county'])) ? $_POST['_county'] : '';
	$_country = (!empty($_POST['_country'])) ? $_POST['_country'] : false;
	$_postCode = (!empty($_POST['_postCode'])) ? $_POST['_postCode'] : '';
	$_telephone = (!empty($_POST['_telephone'])) ? $_POST['_telephone'] : '';
	$_mobile = (!empty($_POST['_mobile'])) ? $_POST['_mobile'] : '';
	$_vat = (!empty($_POST['_vat'])) ? $_POST['_vat'] : '';
	$_waste = (!empty($_POST['_waste'])) ? $_POST['_waste'] : '';
	$_info = (!empty($_POST['_info'])) ? $_POST['_info'] : '';
	$_vcode = (!empty($_POST['_vcode'])) ? $_POST['_vcode'] : '';
	$_vimg = (!empty($_POST['_vimg'])) ? $_POST['_vimg'] : '';
	//$_ = (!empty($_POST[''])) ? $_POST[''] : '';
	?>
	<div id="registration_form">
	<p class="usernote">Fields marked with (*) are required.</p>
	
		<div class="reg_grp ui-corner-all m10">
			<h2>User Details</h2>
			<table class="newuser_tbl">
				<tr>
					<th<?php $vdata->validate($_username,"user","display"); ?> class="colwidth">*Username:</th>
					<td><input class="ui-corner-all" type="text" size="25" name="_username" value="<?=$_username;?>" maxlength="25" onkeyup="document.getElementsByName('_displayName')[0].value=this.value;" /></td>
					<td class="usernote">Usernames must be 6-25 alphanumeric chatacters.</td>
				</tr>
			   
				<tr>
					<th<?php $vdata->validate($_email,"email","display"); ?>>*Email:</th>
					<td><input class="ui-corner-all" type="text" size="48" name="_email" value="<?=$_email;?>" maxlength="165" /></td>
					<td class="usernote">Valid emails will only be accepted.</td>
				</tr>
				<tr>	
					<th<?php $vdata->validate($_password,"pswd","display"); ?>>*New Password:</th>
					<td><input class="ui-corner-all" type="password" size="32" name="_password" value="<?=$_password;?>" maxlength="25" /></td>
					<td class="usernote">Passwords must be 8-25 alphanumeric characters.</td>
				</tr>
				<tr>
					<th<?php $vdata->validate($_vpassword,"pswd","display",$_password,1); ?>>*Verify Password:</th>
					<td><input class="ui-corner-all" type="password" size="32" name="_passwordVerify" value="<?=$_vpassword;?>" maxlength="25" /></td>
					<td></td>
				</tr>
		   </table>
		</div>
	   
		<div class="reg_grp ui-corner-all m10 left w50">
			<h2>Supplier Information</h2>       
			<table class="newuser_tbl">    
				<?php if(sf("_title",$vdata)){ $req=""; ?>
				<tr>
					<th<?php if(vf("_title",$vdata)){ $vdata->validate($_title,"text","display"); $req = "*"; }?> class="colwidth"><?php echo $req; ?>Title:</th>
					<td><select name="_title"  class="ui-corner-all box_select">
							<option<?php rtn_select(rtn_data($_title,''),"Mr","Mr"); ?></option>
							<option<?php rtn_select(rtn_data($_title,''),"Mrs","Mrs"); ?></option>
							<option<?php rtn_select(rtn_data($_title,''),"Miss","Miss"); ?></option>
							<option<?php rtn_select(rtn_data($_title,''),"Ms","Ms"); ?></option>
							<option<?php rtn_select(rtn_data($_title,''),"Dr","Dr"); ?></option>
					</select></td>
					<td></td>
				</tr>
				<?php } if(sf("_forename",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_forename",$vdata)){ $vdata->validate($_forename,"text","display"); $req = "*";  }?>><?php echo $req; ?>Forename:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_forename" value="<?=$_forename;?>" maxlength="50" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_surname",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_surname",$vdata)){ $vdata->validate($_surname,"text","display"); $req = "*";  }?>><?php echo $req; ?>Surname:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_surname" value="<?=$_surname;?>" maxlength="50" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_company",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_company",$vdata)){ $vdata->validate($_company,"text","display"); $req = "*";  }?>><?php echo $req; ?>Company:</th>
					<td><input class="ui-corner-all" type="text" size="48" name="_company" value="<?=$_company;?>" maxlength="50" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_position",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_position",$vdata)){ $vdata->validate($_position,"text","display"); $req = "*";  }?>><?php echo $req; ?>Position:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_position" value="<?=$_position;?>" maxlength="50" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_address1",$vdata)){  $req=""; ?>
				<tr>	
					<th<?php if(vf("_address1",$vdata)){ $vdata->validate($_address1,"text","display"); $req = "*";  }?>><?php echo $req; ?>Address Line1:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_address1" value="<?=$_address1;?>" maxlength="100" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_address2",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_address2",$vdata)){ $vdata->validate($_address2,"text","display"); $req = "*";  }?>><?php echo $req; ?>Address Line2:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_address2" value="<?=$_address2;?>" maxlength="100" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_city",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_city",$vdata)){ $vdata->validate($_city,"text","display"); $req = "*";  }?>><?php echo $req; ?>City:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_city" value="<?=$_city;?>" maxlength="50" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_county",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_county",$vdata)){ $vdata->validate($_county,"text","display"); $req = "*";  }?>><?php echo $req; ?>County:</th>
					<td><input class="ui-corner-all" type="text" size="32" name="_county" value="<?=$_county;?>" maxlength="100" /></td>
					<td></td>
				</tr>
				<?php } ?>
				<tr>
					<th>Country:</th>
					<td><select name="_country"><?=country_options($_country);?></select></td>
					<td></td>
				</tr>
				
				<?php if(sf("_postCode",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_postCode",$vdata)){ $vdata->validate($_postCode,"text","display"); $req = "*";  }?>><?php echo $req; ?>Post Code:</th>
					<td><input class="ui-corner-all" type="text" size="12" name="_postCode" value="<?=$_postCode;?>" maxlength="100" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_telephone",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_telephone",$vdata)){ $vdata->validate($_telephone,"phone","display"); $req = "*";  }?>><?php echo $req; ?>Telephone:</th>
					<td><input class="ui-corner-all" type="text" name="_telephone" value="<?=$_telephone;?>" maxlength="15" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_mobile",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_mobile",$vdata)){ $vdata->validate($_mobile,"phone","display"); $req = "*";  }?>><?php echo $req; ?>Mobile:</th>
					<td><input class="ui-corner-all" type="text" name="_mobile" value="<?=$_mobile;?>" maxlength="15" /></td>
					<td></td>
				</tr>
				<?php } if(sf("_vat",$vdata)){ $req=""; ?>
				<tr>
					<th<?php if(vf("_vat",$vdata)){ $vdata->validate($_vat,"text","display"); $req = "*"; }?> class="colwidth"><?php echo $req; ?>VAT Registered?:</th>
					<td><select name="_vat" class="ui-corner-all box_select">
							<option<?=rtn_select(rtn_data($_vat,''),"Yes","Yes");?></option>
							<option<?=rtn_select(rtn_data($_vat,''),"No","No");?></option>
					</select></td>
					<td></td>
				</tr>
				<?php } if(sf("_waste",$vdata)){ $req=""; ?>
				<tr>
					<th<?php if(vf("_waste",$vdata)){ $vdata->validate($_waste,"text","display"); $req = "*"; }?> class="colwidth"><?php echo $req; ?>Waste Management License?:</th>
					<td><select name="_waste" class="ui-corner-all box_select">
							<option<?=rtn_select(rtn_data($_waste,''),"Yes","Yes");?></option>
							<option<?=rtn_select(rtn_data($_waste,''),"No","No");?></option>
					</select></td>
					<td></td>
				</tr>
				<?php } if(sf("_info",$vdata)){  $req=""; ?>
				<tr>
					<th<?php if(vf("_info",$vdata)){ $vdata->validate($_info,"text","display"); $req = "*";  }?>><?php echo $req; ?>Company Description:</th>
					<td><textarea class="ui-corner-all" name="_info"><?=$_info;?></textarea></td>
					<td></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</table>
		</div>
		<div class="reg_grp ui-corner-all m10 right w45">
			<h2>Account Validation</h2>
			<table class="newuser_tbl">
				<tr>
					<th<?=$vdata->validate($_vcode,"vcode","display",rtn_vcode($_vimg,$vdata),1);?>>*Validate:</th>
					<?php 
					$vimg = $vdata->get_row("SELECT * FROM `tblValidationCodes` ORDER BY RAND() LIMIT 0,1");
					echo '<input class="ui-corner-all" type="hidden" name="_vimg" value="'.$vimg->intVID.'" />';
					?>
					<td>
						<img src="<?php echo ROOT; ?>Images/Validation/<?php echo $vimg->strImage; ?>" alt="Validation Image" style="display:block;" />
						<input class="ui-corner-all" type="text" name="_vcode" value="" maxlength="6" />
					</td>
					<td class="usernote"></td>
				</tr>    
				<tr>
					<td colspan="3"><p>Please enter the code as shown</p></td>
				</tr>
				<!--<tr>
					<td colspan="3"><input class="ui-corner-all" type="submit" value="Register" /></td>
				</tr>-->
			</table>
		</div>
		<div class="reg_grp ui-corner-all m10 right w45">
			<h2>Registration</h2>
			<p>Upon registering your account, your details will be sent to our Admin team for verification.</p>
			<p>During this period your account will have limited access to the supplier features on the website.</p>
			<p>Verification should take no longer than 24-48 hours.</p>
			<br />
			<p style="margin: 15px 0pt 15px 8px;"><a href="#" id="register_btn" class="ui-state-default ui-button ui-corner-all"><span class="ui-icon ui-icon-circle-arrow-e"></span>Register</a></p>
		</div>    
	</div>

</form>
<?php
	
}
	

function process_form($class)
{	
	$class->validate($_POST['_username'],"user");
	$class->validate($_POST['_email'],"email");
	$class->validate($_POST['_password'],"pswd");
	$class->validate($_POST['_passwordVerify'],"pswd",$_POST['_password'],1);
	$class->validate($_POST["_vcode"],"vcode","process",rtn_vcode($_POST['_vimg'],$class),1);
	
	
	$cf = $class->get_results("SELECT * FROM `tbl_mod_Registration` WHERE 1");
	foreach($cf as $cf)
	{
		if($cf->intDisplay==1 & $cf->intValidate)
		{
			$class->validate($_POST[$cf->strFieldName],$cf->strVType);
		}
	}
			
	
	if($class->form_error == false)
	{
		$u_username = $class->mysql_prep($_POST['_username']);
		$u_name = $class->mysql_prep($_POST['_forename'].' '.$_POST['_surname']);
		$u_email = $class->mysql_prep($_POST['_email']);
		$u_password = $class->mysql_prep(md5($_POST['_password']));
		$u_type = 6;
		$u_status = 1;
		
		$u7 = $class->mysql_prep($_POST['_title']);
		$u10 = $class->mysql_prep($_POST['_address1']);
		$u11 = $class->mysql_prep($_POST['_address2']);
		$u12 = $class->mysql_prep($_POST['_city']);
		$u13 = $class->mysql_prep($_POST['_county']);
		$u14 = $class->mysql_prep($_POST['_postCode']);
		$u15 = $class->mysql_prep($_POST['_telephone']);
		$u16 = $class->mysql_prep($_POST['_mobile']);
		$u17 = $class->mysql_prep($_POST['_company']);
		$u18 = $class->mysql_prep($_POST['_vat']);
		$u19 = $class->mysql_prep($_POST['_waste']);
		$u20 = $class->mysql_prep($_POST['_info']);
		$u21 = $class->mysql_prep($_POST['_position']);
		
		$class->query('INSERT INTO `supplier_company`(`c_addr1`,`c_addr2`,`c_city`,`c_county`,`c_postcode`,`c_phone`,`c_mobile`,`c_name`,`c_vat`,`c_waste`,`c_info`)				
					VALUES(\''.$u10.'\',\''.$u11.'\',\''.$u12.'\',\''.$u13.'\',\''.$u14.'\',\''.$u15.'\',\''.$u16.'\',\''.$u17.'\',\''.$u18.'\',\''.$u19.'\',\''.$u20.'\')');
		$comp_id = mysql_insert_id();
		
		if($comp_id > 0)
		{
			$sql = 'INSERT INTO `supplier_company_users`(`company_id`,`strUsername`,`strTitle`,`strName`,`strEmail`,`strPassword`,`intUserType`,`intStatusCode`,`dtmRegisterDate`,`strPosition`) 
					VALUES(\''.$comp_id.'\',\''.$u_username.'\',\''.$u7.'\',\''.$u_name.'\',\''.$u_email.'\',\''.$u_password.'\',\''.$u_type.'\',\''.$u_status.'\',NOW(),\''.$u21.'\')';
			$class->query($sql);
			
			$admin_id = mysql_insert_id();
			
			if($admin_id > 0)
			{
				$sql = 'UPDATE `supplier_company` SET `c_admin_id` = '.$admin_id.' WHERE `company_id` = '.$comp_id;
				$class->query($sql);
			}

			$email_data = 'Dear Admin'."\n";
			$email_data .= 'A new supplier has registered on Textspares.co.uk'."\n\n";
			$email_data .= '<b>Summary : </b>'."\n\n";
			$email_data .= "\n".'Company Name: '.$u17;
			$email_data .= "\n".'Username: '.$u_username;
			$email_data .= "\n".'Name: '.$u_name;
			$email_data .= "\n".'Email: '.$u_email;
			$email_data .= "\n".'Tel: '.$u15."\n\n";
			$email_data .= 'To Activate this account goto the Managers Panel here: <a href="'.ROOT.'">'.ROOT.'</a>'."\n\n";

			send_email('support@textspares.co.uk','noreply@textspares.co.uk','TextSpares.co.uk - New Supplier Registration',$email_data);
		}
		?>	
		<h1>Register</h1>
		<div class="valid_box"><p>Registration Complete</p></div>
		<p>Thanks for registering. Your can now login but your account is awaiting activation.</p>
		<p><a href="<?php echo ROOT; ?>">Click here to login.</a></p>
	<?php	
		} else {
		display_form($class);
		}
	
}

if($_REQUEST['_subaction'] == 'validate') { process_form($vdata); } else { display_form($vdata); }
	
?>

	

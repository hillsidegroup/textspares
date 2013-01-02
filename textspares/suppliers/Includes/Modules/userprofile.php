<?php

if(!defined('IN_TEXTSPARES')) { exit; }

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
	global $session,$db;

	$sql = 'SELECT *
		FROM `supplier_company_users` AS `u`
		JOIN `supplier_company` AS `com` ON `com`.`company_id` = `u`.`company_id`
		WHERE `u`.`company_user_id` = '.$session->userdata['id'];
	
	//print($sql);
	$user_data = $db->get_row($sql);

	$username = $user_data->strUsername;
	$dname = $user_data->strName;
	$email = $user_data->strEmail;
	$title = $user_data->strTitle;
	$sname = $user_data->strSurname;
	$fname = $user_data->strForename;
	$cpny = $user_data->c_name;
	$add1 = $user_data->c_addr1;
	$add2 = $user_data->c_addr2;
	$city = $user_data->c_city;
	$cnty = $user_data->c_county;
	$pcode = $user_data->c_postcode;
	$phone = $user_data->c_phone;
	$mobile = $user_data->c_mobile;
	$vat = $user_data->c_vat;
	$waste = $user_data->c_waste;
	$info = $user_data->c_info;
	$posi = $user_data->strPosition;

	$url = ROOT."?_action=profile&_page=validate&_subaction=edit";
?>
	<h1>Edit Account</h1>	
<?php
		if($vdata->form_error==true){ ?>
        <div class="ui-state-error ui-corner-all m5">
			<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>There was a problem with your form. Please amend highlighted fields accordingly.</p>
			<?php 
			if($vdata->duplicate_user==true) {?> <p>*The username you supplied is already present on the database.</p> <?php }
			if($vdata->duplicate_email==true) {?> <p>*The email you supplied is already used by another account.</p> <?php } ?>
		</div>
		<?php 
		} 
?>	
	<script type="text/javascript">
	$(document).ready(function()
	{
		//hover states on the static widgets
		$('#registration_form a').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);
		$('#update_btn').click(function(e){
			e.preventDefault();							  
			$("form[name=_editUserForm]").submit();
		});
	});
	</script>
	<form name="_editUserForm" action="<?php echo $url; ?>" method="post">
	<div id="registration_form">
	<p class="usernote">Fields marked with (*) are required.</p>
	<input type="hidden" name="_username" value="<?php echo rtn_data($_POST['_username'],$username); ?>" maxlength="25" />

   	<div class="reg_grp ui-corner-all m10">
    	<h2>User Details</h2>
        <table class="newuser_tbl">
            <tr>
                <th class="colwidth">*Username:</th>
                <td><input class="ui-corner-all"  type="text" name="_usernameDisplay" value="<?php echo rtn_data($_POST['_username'],$username); ?>" maxlength="25" disabled="disabled" /></td>
                <td class="usernote">Usernames cannot be modified after registration.</td>
            </tr>
            <tr>
                <th<?php $vdata->validate($_POST["_email"],"email","display"); ?>>*Email:</th>
                <td><input class="ui-corner-all"  type="text" name="_email" value="<?php echo rtn_data($_POST['_email'],$email); ?>" maxlength="165" /></td>
                <td class="usernote">Valid emails will only be accepted.</td>
            </tr>
            <tr>	
                <th<?php if($_POST["_password"]!=""){ $vdata->validate($_POST["_password"],"pswd","display"); } ?>>*New Password:</th>
                <td><input class="ui-corner-all"  type="password" name="_password" value="<?php echo rtn_data($_POST['_password'],''); ?>" maxlength="25" /></td>
                <td class="usernote">Passwords must be 8-25 alphanumeric characters</td>
            </tr>
            <tr>
                <th<?php if($_POST["_password"]!=""){ $vdata->validate($_POST["_passwordVerify"],"pswd","display",$_POST['_password'],1); } ?>>*Verify Password:</th>
                <td><input class="ui-corner-all"  type="password" name="_passwordVerify" value="<?php echo rtn_data($_POST['_passwordVerify'],''); ?>" maxlength="25" /></td>
                <td></td>
            </tr>
		</table>
    </div>
	<div class="reg_grp ui-corner-all m10 left w50">
		<h2>Supplier Information</h2>    
        <table class="newuser_tbl">    
            <?php if(sf("_title",$vdata)){ $req=""; ?>
            <tr>
                <th class="colwidth" <?php if(vf("_title",$vdata)){ $vdata->validate($_POST["_title"],"text","display"); $req = "*"; }?>><?php echo $req; ?>Title:</th>
                <td><select class="ui-corner-all"  name="_title" class="box_select">
                        <option<?php rtn_select(rtn_data($_POST['_title'],$title),"Mr","Mr"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_title'],$title),"Mrs","Mrs"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_title'],$title),"Miss","Miss"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_title'],$title),"Ms","Ms"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_title'],$title),"Dr","Dr"); ?></option>
                </select></td>
                <td></td>
            </tr>
            <?php } if(sf("_forename",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_forename",$vdata)){ $vdata->validate($_POST["_forename"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Forename:</th>
                <td><input class="ui-corner-all"  type="text" name="_forename" value="<?php echo rtn_data($_POST['_forename'],$fname); ?>" maxlength="50" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_surname",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_surname",$vdata)){ $vdata->validate($_POST["_surname"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Surname:</th>
                <td><input class="ui-corner-all"  type="text" name="_surname" value="<?php echo rtn_data($_POST['_surname'],$sname); ?>" maxlength="50" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_company",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_company",$vdata)){ $vdata->validate($_POST["_company"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Company:</th>
                <td><input class="ui-corner-all" type="text" name="_company" value="<?php echo rtn_data($_POST['_company'],$cpny); ?>" maxlength="50" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_position",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_position",$vdata)){ $vdata->validate($_POST["_position"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Position:</th>
                <td><input class="ui-corner-all" type="text" name="_position" value="<?php echo rtn_data($_POST['_position'],$posi); ?>" maxlength="50" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_address1",$vdata)){  $req=""; ?>
            <tr>	
                <th<?php if(vf("_address1",$vdata)){ $vdata->validate($_POST["_address1"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Address Line1:</th>
                <td><input class="ui-corner-all"  type="text" name="_address1" value="<?php echo rtn_data($_POST['_address1'],$add1); ?>" maxlength="100" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_address2",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_address2",$vdata)){ $vdata->validate($_POST["_address2"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Address Line2:</th>
                <td><input class="ui-corner-all"  type="text" name="_address2" value="<?php echo rtn_data($_POST['_address2'],$add2); ?>" maxlength="100" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_city",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_city",$vdata)){ $vdata->validate($_POST["_city"],"text","display"); $req = "*";  }?>><?php echo $req; ?>City:</th>
                <td><input class="ui-corner-all"  type="text" name="_city" value="<?php echo rtn_data($_POST['_city'],$city); ?>" maxlength="50" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_county",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_county",$vdata)){ $vdata->validate($_POST["_county"],"text","display"); $req = "*";  }?>><?php echo $req; ?>County:</th>
                <td><input class="ui-corner-all"  type="text" name="_county" value="<?php echo rtn_data($_POST['_county'],$cnty); ?>" maxlength="100" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_postCode",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_postCode",$vdata)){ $vdata->validate($_POST["_postCode"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Post Code:</th>
                <td><input class="ui-corner-all"  type="text" name="_postCode" value="<?php echo rtn_data($_POST['_postCode'],$pcode); ?>" maxlength="100" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_telephone",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_telephone",$vdata)){ $vdata->validate($_POST["_telephone"],"phone","display"); $req = "*";  }?>><?php echo $req; ?>Telephone:</th>
                <td><input class="ui-corner-all"  type="text" name="_telephone" value="<?php echo rtn_data($_POST['_telephone'],$phone); ?>" maxlength="15" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_mobile",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_mobile",$vdata)){ $vdata->validate($_POST["_mobile"],"phone","display"); $req = "*";  }?>><?php echo $req; ?>Mobile:</th>
                <td><input class="ui-corner-all"  type="text" name="_mobile" value="<?php echo rtn_data($_POST['_mobile'],$mobile); ?>" maxlength="15" /></td>
                <td></td>
            </tr>
            <?php } if(sf("_vat",$vdata)){ $req=""; ?>
            <tr>
                <th<?php if(vf("_vat",$vdata)){ $vdata->validate($_POST["_vat"],"text","display"); $req = "*"; }?> class="colwidth"><?php echo $req; ?>VAT Registered?:</th>
                <td><select name="_vat" class="ui-corner-all box_select">
                        <option value="">Please Select</option>
                        <option<?php rtn_select(rtn_data($_POST['_vat'],$vat),"Yes","Yes"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_vat'],$vat),"No","No"); ?></option>
                </select></td>
                <td></td>
            </tr>
            <?php } if(sf("_waste",$vdata)){ $req=""; ?>
            <tr>
                <th<?php if(vf("_waste",$vdata)){ $vdata->validate($_POST["_waste"],"text","display"); $req = "*"; }?> class="colwidth"><?php echo $req; ?>Waste Management License?:</th>
                <td><select name="_waste" class="ui-corner-all box_select">
                        <option value="">Please Select</option>
                        <option<?php rtn_select(rtn_data($_POST['_waste'],$waste),"Yes","Yes"); ?></option>
                        <option<?php rtn_select(rtn_data($_POST['_waste'],$waste),"No","No"); ?></option>
                </select></td>
                <td></td>
            </tr>
            <?php } if(sf("_info",$vdata)){  $req=""; ?>
            <tr>
                <th<?php if(vf("_info",$vdata)){ $vdata->validate($_POST["_info"],"text","display"); $req = "*";  }?>><?php echo $req; ?>Additional Info:</th>
                <td><textarea class="ui-corner-all" type="text" name="_info"><?php echo rtn_data($_POST['_info'],$info); ?></textarea></td>
                <td></td>
            </tr>
            <?php } ?>
        </table>
	</div>	
    <div class="clearer"><p style="margin: 15px 0pt 15px 8px;"><a href="#" id="update_btn" class="ui-state-default ui-button ui-corner-all"><span class="ui-icon ui-icon-pencil"></span>Update Account</a></p></div>
	</div>
</form>
<?php

}

if(!@function_exists(check_display)){
function check_display($id,$field,$value,$db)
{
	$output = false;
	$sql = "SELECT intDisplay FROM tbl_mod_Registration WHERE intRID = $id";
	$display = $db->get_var($sql);
	//print("<p>Display: >".$display."< $sql</p>");
	if($display==1)
	{
		$output = "$field='$value',";
	}
	return $output;
	}
}

function process_form($class)
{	
	global $session;
	//print(TEST);
	$class->validate($_POST['_email'],"email");
	
	//$class->debug();
	/*exit();	
	*/
	//print(TEST);
	if($_POST['_password']!="")
	{
		$class->validate($_POST['_passwordVerify'],"pswd",$_POST['_password'],1);
	}

	$cf = $class->get_results("SELECT * FROM tbl_mod_Registration");
	foreach($cf as $cf)
	{
		if($cf->intDisplay==1&$cf->intValidate)
		{
			$class->validate($_POST[$cf->strFieldName],$cf->strVType);
		}
	}
	
	if($class->form_error==false)
	{
		$u3 = $class->mysql_prep($_POST['_email']);
		if($_POST['_password']!="")
		{
			$u4 = md5($class->mysql_prep($_POST['_password']));
			$sq_f = ",strPassword='$u4'";
		}
		else
		{
			$sq_f = "";
			
		}

		$sql = "UPDATE `supplier_company_users` SET
						strEmail='$u3'
						".$sq_f."									
				WHERE company_user_id = ".$session->userdata['id'];
				
		$class->query($sql);
		//$class->debug();
		//$user_id = mysql_insert_id();
																
		$u7 = $class->mysql_prep($_POST['_title']);
		$u8 = $class->mysql_prep($_POST['_forename']);
		$u9 = $class->mysql_prep($_POST['_surname']);
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
			
		$dCount = $class->get_row("SELECT COUNT(*) AS Total FROM supplier_company_users WHERE company_user_id = ".$session->userdata['id']);
		//$class->debug();
		$dCount = $dCount->Total;
		
		if($dCount==0)
		{
			$sql2 = "INSERT INTO supplier_company_users
						(company_user_id,strTitle,strForename,strSurname,strAddress1,strAddress2,strCity,strCounty,strPostCode,strTelephone,strMobile,strCompany,strVat,strWaste,strInfo,strPosition)				
						VALUES
						(".$session->userdata['id'].",'$u7','$u8','$u9','$u10','$u11','$u12','$u13','$u14','$u15','$u16','$u17','$u18','$u19','$u20','$u21')";
			$class->query($sql2);
			$class->debug();
		} else {
			$false_count = 0;
			
			$sql2 = "UPDATE supplier_company_users SET ";
			if( !check_display(1,"strTitle",$u7,$class) ){ $false_count += 1; } else { $sql2 .= check_display(1,"strTitle",$u7,$class); }
			if( !check_display(2,"strTitle",$u7,$class) ){ $false_count += 1; } else { $sql2 .= check_display(2,"strForename",$u8,$class); }
			if( !check_display(3,"strForename",$u8,$class) ){ $false_count += 1; } else { $sql2 .= check_display(3,"strSurname",$u9,$class); }
			if( !check_display(4,"strSurname",$u9,$class) ){ $false_count += 1; } else { $sql2 .= check_display(4,"strAddress1",$u10,$class); }
			if( !check_display(5,"strAddress1",$u10,$class) ){ $false_count += 1; } else { $sql2 .= check_display(5,"strAddress2",$u11,$class); }
			if( !check_display(6,"strAddress2",$u11,$class) ){ $false_count += 1; } else { $sql2 .= check_display(6,"strCity",$u12,$class); }
			if( !check_display(7,"strCity",$u12,$class) ){ $false_count += 1; } else { $sql2 .= check_display(7,"strCounty",$u13,$class); }
			if( !check_display(8,"strCounty",$u13,$class) ){ $false_count += 1; } else { $sql2 .= check_display(8,"strPostCode",$u14,$class); }
			if( !check_display(9,"strPostCode",$u14,$class) ){ $false_count += 1; } else { $sql2 .= check_display(9,"strTelephone",$u15,$class); }
			if( !check_display(10,"strTelephone",$u15,$class) ){ $false_count += 1; } else { $sql2 .= check_display(10,"strMobile",$u16,$class); }
			if( !check_display(11,"strCompany",$u16,$class) ){ $false_count += 1; } else { $sql2 .= check_display(11,"strCompany",$u17,$class); }
			if( !check_display(12,"strVat",$u17,$class) ){ $false_count += 1; } else { $sql2 .= check_display(12,"strVat",$u18,$class); }
			if( !check_display(13,"strWaste",$u18,$class) ){ $false_count += 1; } else { $sql2 .= check_display(13,"strWaste",$u19,$class); }
			if( !check_display(14,"strInfo",$u19,$class) ){ $false_count += 1; } else { $sql2 .= check_display(15,"strInfo",$u20,$class); }
			if( !check_display(15,"strPosition",$u20,$class) ){ $false_count += 1; } else { $sql2 .= check_display(15,"strPosition",$u21,$class); }

			$sql2 = substr($sql2,0,strlen($sql2)-1);
			$sql2 .= " WHERE company_user_id = ".$session->userdata['id'];
			
			//print($sql2);
			if(substr_count($sql2,"SET WHERE")!=1){
				$class->query($sql2);
				//$class->debug();
				} 
			}
		
		//print($sql."<br>");
		//print($sql2);
		?>
        <div class="ui-widget m10">
			<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
				<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-check"></span>Account Updated!</p>
			</div>
		</div>	
        <?php 
		//include_once "Includes/Modules/control-panel.php";
		//return;
		displayProfile($class); 
	}
}

if(!@function_exists(view_details)){
function view_details($vdata)
{
	global $session,$settings,$db;
	
	if($session->userdata['company_admin'] == true && isset($_POST['do']) && $_POST['do'] == 'add_new_user')
	{
		$u_type = 6;
		$u_status = 1;

		$_username = (!empty($_POST['_username'])) ? $_POST['_username'] : '';
		$_email = (!empty($_POST['_email'])) ? $_POST['_email'] : '';
		$_password = (!empty($_POST['_password'])) ? $_POST['_password'] : '';
		$_vpassword = (!empty($_POST['_passwordVerify'])) ? $_POST['_passwordVerify'] : '';

		$_title = (!empty($_POST['_title'])) ? $_POST['_title'] : '';
		$_name = (!empty($_POST['_name'])) ? $_POST['_name'] : '';
		$_position = (!empty($_POST['_position'])) ? $_POST['_position'] : '';
		
		$vdata->validate($_username,"user");
		$vdata->validate($_email,"email");
		$vdata->validate($_password,"pswd");
		$vdata->validate($_vpassword,"pswd",$_password,1);
		
		$vdata->validate($_title,"dname");
		$vdata->validate($_name,"dname");
		$vdata->validate($_position,"dname");
		
		if($vdata->form_error == false)
		{
			$_username = $db->mysql_prep($_username);
			$_email = $db->mysql_prep($_email);
			$_password = $db->mysql_prep($_password);
			$_vpassword = $db->mysql_prep($_vpassword);

			$_title = $db->mysql_prep($_title);
			$_name = $db->mysql_prep($_name);
			$_position = $db->mysql_prep($_position);
			
			$sql = 'INSERT INTO `supplier_company_users`(`company_id`,`strUsername`,`strTitle`,`strName`,`strEmail`,`strPassword`,`intUserType`,`intStatusCode`,`dtmRegisterDate`,`strPosition`) 
					VALUES(\''.$session->userdata['companyid'].'\',\''.$_username.'\',\''.$_title.'\',\''.$_name.'\',\''.$_email.'\',\''.$_password.'\',\''.$u_type.'\',\''.$u_status.'\',NOW(),\''.$_position.'\')';
			if(!$db->query($sql))
			{
				display_alert_message('Sorry, There was an internal error when processing the request.');
			}
		} else {
			display_alert_message('Sorry, There where some errors when processing your request.');
		}
	}

	$sql = "SELECT *
		FROM supplier_company_users 
			JOIN supplier_company
			ON supplier_company_users.company_id = supplier_company.company_id
			INNER JOIN tblUserTypes 
			ON supplier_company_users.intUserType = tblUserTypes.intUTID
			INNER JOIN tblUserStatusCode
			ON supplier_company_users.intStatusCode = tblUserStatusCode.intUSCID
		WHERE supplier_company_users.company_user_id = ".$session->userdata['id'];
	
	//print($sql);
	$user_data = $db->get_row($sql);
	
	$username = $user_data->strUsername;
	$dname = $user_data->strName;
	$pswd = $user_data->strPassword;
	$email = $user_data->strEmail;
	$title = $user_data->strTitle;
	$name = $user_data->strName;
	$cpny = $user_data->c_name;
	$add1 = $user_data->c_addr1;
	$add2 = $user_data->c_addr2;
	$city = $user_data->c_city;
	$cnty = $user_data->c_county;
	$pcode = $user_data->c_postcode;
	$phone = $user_data->c_phone;
	$mobile = $user_data->c_mobile;
	$vat = $user_data->c_vat;
	$waste = $user_data->c_waste;
	$info = $user_data->c_info;
	$posi = $user_data->strPosition;
	$c_admin = $user_data->c_admin_id;
	
	$group = $user_data->strUserType;
	$status = $user_data->strStatusCode;
	$lastlog = date("D jS M y, G:i",((!empty($_SESSION['_lastLogin'])) ? strtotime($_SESSION['_lastLogin']) : time()));
	$activation = $user_data->dtmActivation == 0 ? false : $user_data->dtmActivation;
	?>
	<h1><img src="<?=ROOT;?>Images/account.png" alt="Account" style="vertical-align:middle;" />Account Details</h1>
	<div id="registration_form">

   	<div class="reg_grp ui-corner-all m10 w50 left">
    	<h2>User Details</h2>
        <table class="newuser_tbl">
            <tr>
                <th class="colwidth">Username:</th>
                <td><p><?=$username;?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Email:</th>
                <td><p><?=$email;?></p></td>
            </tr>
            <tr>
                <th>Usergroup:</th>
                <td><p><?=$group;?></p></td>
            </tr>
            <tr>
                <th>Account Status:</th>
                <td><p><?=$status;?></p></td>
            </tr>
            <tr>
                <th>Registration Status:</th>
                <td><p><?=(($activation == '') ? 'Unverified' : 'Verified on: '.date("D jS M y, G:i",strtotime($activation)));?></p></td>
            </tr>
            <tr>
                <th>Last Login:</th>
                <td><p><?=$lastlog;?></p></td>
            </tr>
		</table>
    </div>
    
    <div class="reg_grp ui-corner-all m10 w45 right">
    	<h2>Updating your Account</h2>
        <p>If your looking to update your account details please contact our admin team</p>
        <p>Your details will be verified before your account is updated.</p>
    </div>

<?php
if($session->userdata['id'] == $c_admin)
{
	$_username = (!empty($_POST['_username'])) ? $_POST['_username'] : '';
	$_email = (!empty($_POST['_email'])) ? $_POST['_email'] : '';
	$_password = (!empty($_POST['_password'])) ? $_POST['_password'] : '';
	$_vpassword = (!empty($_POST['_passwordVerify'])) ? $_POST['_passwordVerify'] : '';
	
	$_title = (!empty($_POST['_title'])) ? $_POST['_title'] : '';
	$_name = (!empty($_POST['_name'])) ? $_POST['_name'] : '';
	$_position = (!empty($_POST['_position'])) ? $_POST['_position'] : '';
	
	if($session->userdata['company_admin'] == true)
	{
?>
    <div class="reg_grp ui-corner-all m10 w45 right">
    	<h2>Manage Company User Accounts</h2>
        <p>
			<table width="100%">
<?php
	$user_sql = 'SELECT `company_user_id`,`strName`,`strPosition`,`dtmActivation`,`intStatusCode` FROM `supplier_company_users` WHERE `company_id` = \''.$session->userdata['companyid'].'\' ORDER BY `strName` DESC';
	if($users = $db->get_results($user_sql))
	{
		foreach($users AS $user)
		{
			echo '<tr><td>'.$user->strName.'</td><td>'.$user->strPosition.'</td><td>'.$settings['account_status_levels'][$user->intStatusCode].'</td></tr>';
		}
	}
	else
	{
		echo '<tr><td>There are no addition user accoutns.</td></tr>';
	}
?>
			</table>
		</p>
		<h2>Create New User Account</h2>
		<p>
			<form id="add_new_user" name="add_new_user" action="<?=ROOT;?>?_action=profile&amp;_page=viewprofile" method="POST" />
			<table width="100%">
				<tr><th>Title</th><td><select name="_title"  class="ui-corner-all box_select">
					<option<?=rtn_select(rtn_data($_title,''),"Mr","Mr");?></option>
					<option<?=rtn_select(rtn_data($_title,''),"Mrs","Mrs");?></option>
					<option<?=rtn_select(rtn_data($_title,''),"Miss","Miss");?></option>
					<option<?=rtn_select(rtn_data($_title,''),"Ms","Ms");?></option>
					<option<?=rtn_select(rtn_data($_title,''),"Dr","Dr");?></option>
				</select></td></tr>
				<tr><th>Name:</th><td><input class="ui-corner-all" type="text" size="32" name="_name" value="<?=$_name;?>" maxlength="50" /></td></tr>
				<tr><th>Position:</th><td><input class="ui-corner-all" type="text" size="32" name="_position" value="<?=$_position;?>" maxlength="50" /></td></tr>
				<tr><th>Username:</th><td><input class="ui-corner-all" type="text" size="25" name="_username" value="<?=$_username;?>" maxlength="25" onkeyup="document.getElementsByName('_displayName')[0].value=this.value;" /></td></tr>
				<tr><th>Password:</th><td><input class="ui-corner-all" type="password" size="32" name="_password" value="<?=$_password;?>" maxlength="25" /></td></tr>
				<tr><th>Confirm Password:</th><td><input class="ui-corner-all" type="password" size="32" name="_passwordVerify" value="<?=$_vpassword;?>" maxlength="25" /></td></tr>
				<tr><th>E-Mail Address:</th><td><input class="ui-corner-all" type="text" size="48" name="_email" value="<?=$_email;?>" maxlength="165" /></td></tr>
				<tr><td colspan="2"></td></tr>
			</table>
			<input type="hidden" name="do" value="add_new_user" />
			<div class="clearer">
				<p><input type="submit" name="submit" value="Add New User" /></p>
			</div>
			</form>
		</p>
    </div>
<?php }} ?>

	<div class="reg_grp ui-corner-all m10 left w50">
		<h2>Supplier Information</h2>    
        <table class="newuser_tbl">    
            <tr>
                <th class="colwidth">Title:</th>
                <td><p><?=$title;?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Name:</th>
                <td><p><?=$name;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Company:</th>
                <td><p><?=$cpny;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Position:</th>
                <td><p><?=$posi;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Address Line 1:</th>
                <td><p><?=$add1;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Address Line 2:</th>
                <td><p><?=$add2;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">City:</th>
                <td><p><?=$city;?></p></td>
            </tr>
            <tr>
                <th class="colwidth">County:</th>
                <td><p><?=$cnty;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Post Code:</th>
                <td><p><?=$pcode;?></p></td>
            </tr>
             <tr>
                <th class="colwidth">Telephone:</th>
                <td><p><?=$phone;?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Mobile:</th>
                <td><p><?=$mobile;?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Vat Registered:</th>
                <td><p><?=(($vat == 1) ? 'Yes' : 'No');?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Waste Management License:</th>
                <td><p><?=(($waste == 1) ? 'Yes' : 'No');?></p></td>
            </tr>
            <tr>
                <th class="colwidth">Additional Info:</th>
                <td><p><?=$info;?></p></td>
            </tr>
        </table>
	</div>	
   	<div class="clearer">
     <p><a href="<?=ROOT;?>" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-triangle-w"></span>Back to Control Panel</a></p>
    </div>
    </div>
    
    <?php
	}
}

if( isset($_GET['_page']) )
{
	//if($_GET['_page']=="validate"){ /* process_form($vdata); */ }
	//elseif($_GET['_page']=="editprofile") { /* display_form($vdata); */ }
	//elseif($_GET['_page']=="viewprofile") { view_details($vdata); }
	switch($_GET['_page'])
	{
		default:
		case 'viewprofile':
			view_details($vdata);
		break;
	}
}	
else
{
	$sql = "SELECT * FROM supplier_company_users 
			INNER JOIN tblUserTypes 
			ON supplier_company_users.intUserType = tblUserTypes.intUTID
			INNER JOIN tblUserStatusCode
			ON supplier_company_users.intStatusCode = tblUserStatusCode.intUSCID
			WHERE company_user_id = ".$session->userdata['id'];
	//print($sql);
	$userdata = $db->get_row($sql);
	
	$username = $userdata->strUsername;
	$email = $userdata->strEmail;
	$group = $userdata->strUserType;
	$status = $userdata->strStatusCode;
	$lastlog = date("D jS M y, G:i",((!empty($_SESSION['_lastLogin'])) ? strtotime($_SESSION['_lastLogin']) : time()));
	$activation = $userdata->dtmActivation == 0 ? false : $userdata->dtmActivation;
	?>
	<div id="profile_summary">
	<h1><img src="<?=ROOT;?>Images/account.png" alt="Account" style="vertical-align:middle;" /> Account Summary</h1>
	<table id="user_summary">
		<tr>
			<th>Username:</th>
			<td><?=$username;?></td>
		</tr>
		<tr>
			<th>Email:</th>
			<td><?=$email;?></td>
		</tr>
		<tr>
			<th>Usergroup:</th>
			<td><?=$group;?></td>
		</tr>
		<tr>
			<th>Account Status:</th>
			<td><?=$status;?></td>
		</tr>
        <tr>
			<th>Registration Status:</th>
			<td><?php if($activation == 0){ echo "Unverified"; } else { echo "Verified on: ".date("D jS M y, G:i",$activation); } ?></td>
		</tr>
		<tr>
			<th>Last Login:</th>
			<td><?=$lastlog;?></td>
		</tr>
	</table>
    <p><a class="ui-state-default ui-corner-all" href="<?php echo ROOT; ?>?_action=profile&_page=viewprofile"><span class="ui-icon ui-icon-person"></span>View Account Info</a></p>
	
	<br />
	<br />
	</div>
	<?php
}
?>
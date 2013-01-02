<?php
define('IN_TEXTSPARES',true);
$nosidebar = true;
include_once('include/common.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

function send_auto_quote($company_user_id,$vehicle_id,$list)
{
	global $session, $db, $settings;
	
	if($settings['debug_mode'])
	{
		debug_reports('<pre>'.print_r($list,true).'</pre>');
	}

	//Loops each supplier agent
	foreach($list AS $quote)
	{

		$cust_sql = 'SELECT `c`.`customer_id`,`c`.`cust_name`,`c`.`cust_email`,`c`.`recieve_emails`,`c`.`recieve_sms`,`v`.`vehicle_id`,`v`.`vehicle_make_name`,`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year`
				FROM customer_vehicles AS `v`
				JOIN `customer_information` AS `c` ON `c`.`customer_id` = `v`.`customer_id`
				WHERE `v`.`vehicle_id` = \''.$vehicle_id.'\'';
		$cust_info = $db->get_row($cust_sql);
		if($cust_info)
		{
			//Check the customer and vehicle exists
			if(!empty($cust_info->customer_id) && $cust_info->customer_id > 0 && !empty($cust_info->vehicle_id) && $cust_info->vehicle_id > 0)
			{
				$cust_info->recieve_emails = (validEmail($cust_info->cust_email) == true) ? $cust_info->recieve_emails : 0;
			
				$q_sql = 'INSERT INTO `supplier_quotes`(`company_id`,`company_user_id`,`customer_id`,`vehicle_id`,`quote_time`,`d_fee`,`d_est`)
							VALUES('.$quote['company'].','.$quote['agentid'].','.$company_user_id.','.$vehicle_id.','.time().','.$quote['delivery'].',\'3-d\')';
							
				$insert = mysql_query($q_sql);
				
				if(!$insert)
				{
					debug_reports(mysql_error(),$q_sql);
				}
				else
				{
					$quote_id = mysql_insert_id();

					if(is_int($quote_id) && $quote_id > 0)
					{
					
						$mail_parts = '';
						$price_total = 0;
					
						foreach($quote['parts'] AS $part)
						{
							$qd_sql = 'INSERT INTO `supplier_quotes_details`(`company_id`,`quote_id`,`request_id`,`vehicle_id`,`company_user_id`,`quote_price`,`quote_guarantee`,`quote_condition`) 
										VALUES('.$quote['company'].','.$quote_id.','.$part['requestid'].','.$vehicle_id.','.$quote['agentid'].',\''.$part['price'].'\',\''.$part['guarantee'].'\',\''.$part['condition'].'\')';	

							if(mysql_query($qd_sql))
							{
								$price_total = $price_total + $part['price'];
								
								if($cust_info->recieve_emails > 0)
								{
									$mail_parts .= '<tr><td>'.$part['name'].'</td><td>'.$part['condition'].'</td><td>'.$part['guarantee'].'</td><td>&pound;'.number_format($part['price'],2).'</td></tr>';
								}
								
								//TODO: SMS
								
							} else {
								debug_reports(mysql_error(),$qd_sql);
							}
						}
						
						if($cust_info->recieve_emails > 0)
						{
							$supsql = 'SELECT `c`.`company_id`,`c`.`c_name`,`c`.`c_phone`,`c`.`c_sales`,`a`.`strName` FROM `supplier_company` AS `c` JOIN `supplier_company_users` AS `a` ON `a`.`company_user_id` = '.$quote['agentid'].' WHERE `c`.`company_id` = '.$quote['company'].'';
							if(!$sup_data = $db->get_row($supsql)) {
								debug_reports(mysql_error(),$supsql);
							}

							$email_data = "PLEASE DO NOT REPLY TO THIS EMAIL\n\n";
							$email_data .= "Dear ".$cust_info->cust_name.",\n";
							$email_data .= "Thank you for using the TextSpares network to locate parts for your vehicle. Please find below a detailed quote for the part(s) requested\n\n";
							$email_data .= 'This is an Automatic Quote, please login to our website and contact the supplier before ordering any parts'."\n\n";
							$email_data .= "<b>Quote Reference:</b> ".pad_ref_number($vehicle_id)."\n\n";
							$email_data .= "<b>Vehicle Details:</b> ".$cust_info->vehicle_make_name." ".$cust_info->vehicle_model_name." - ".$cust_info->registration_year."\n\n";
							$email_data .= "<b>Quote Details:</b>\n\n";
							$email_data .= '<table cellspacing="2" cellpadding="2" border="0" width="80%"><tr><th>Part Name</th><th>Condition</th><th>Guarantee</th><th>Price</th></tr>'.$mail_parts.'</table>';
							$email_data .= "\n\n".'If you would like more information or would like to follow up this quote, the suppliers details are listed below.';
							$email_data .= "\n\n<b>Company Name:</b> ".$sup_data->c_name;
							//$email_data .= "\n".'<b>Tel:</b> '.((strlen($sup_data->c_sales) > 5) ? $sup_data->c_sales : $sup_data->c_phone);
							$email_data .= "\n\n<b>Quote Reference:</b> ".pad_ref_number($vehicle_id)."\n\n";
							$email_data .= "<b>What to do next:</b>\n";
							$email_data .= "You can login online and see all your quotes in one place and compare them.\n\n";
							$email_data .= 'Login now: <a href="'.BASE.'?k='.create_emailloginkey($cust_info->customer_id).'">Check My Quotes</a>'."\n";
							//$email_data .= "\n\n".'Textspares.co.uk - The UKs Top Online Car Parts Network';

							send_email($cust_info->cust_email,'noreply@textspares.co.uk','Textspares - Automatic Quote',$email_data);

						}
					} else {
						debug_reports('Invalid Quote ID after Record Insert!');
					}
				}
			} else {
				debug_reports('Invalid Customer ID or Vehicle ID was supplied');
			}
		} else {
			debug_reports(mysql_error(),$cust_sql);
		}
	}
}
function auto_alert_supplier($vehicle_id,$supid)
{
		global $db;

		$sup_data = $db->get_row('SELECT `u`.`strName`,`u`.`strEmail` FROM `supplier_company_users` AS `u` WHERE `u`.`company_user_id` = \''.$supid.'\'');

		$cust_sql = 'SELECT `v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year` FROM `customer_vehicles` AS `v` WHERE `v`.`vehicle_id` = \''.$vehicle_id.'\' ';
		$cust_info = $db->get_row($cust_sql);

		$email_data = "PLEASE DO NOT REPLY TO THIS EMAIL \n\n";
		$email_data .= "Dear ".$sup_data->strName.",\n";
		$email_data .= "Thank you for using the TextSpares supplier network.\nA new part request matching your alert settings has been submitted\n\n";
		$email_data .= 'Quote Reference: '.pad_ref_number($vehicle_id)."\n";
		$email_data .= "Vehicle Details: ".$cust_info->vehicle_make_name." ".$cust_info->vehicle_model_name." - ".$cust_info->registration_year."\n";
		$email_data .= "\nIf you would like more information or would like to quote this request, follow the link below.\n\n";
		$email_data .= "https://dev.textspares.co.uk/suppliers/?_action=requests&id=".$vehicle_id."\n\n";
		//$email_data .= "Textspares.co.uk - The UKs Top Online Car Parts Network";

		send_email($sup_data->strEmail,'noreply@textspares.co.uk','TextSpares.co.uk - Part Request Auto Alert',$email_data);
}
top();
?>

<select name="listedparts" id="parts" style="display:none;">
<?php
$sql = 'SELECT `part_id`,`part_cat_name` FROM `parts_categories` WHERE 1';
$parts = $db->get_results($sql);
$part_name = '';
foreach($parts AS $part)
{
	if(!empty($_REQUEST['pid']) && $_REQUEST['pid'] == $part->part_id) {
	$part_name = $part->part_cat_name;
	}
	echo '<option value="'.$part->part_id.'">'.$part->part_cat_name.'</option>';
}
?>
</select>

<script type="text/javascript" src="<?=BASE?>js/ajax.js"></script>
<script type="text/javascript" src="<?=BASE?>js/ajax-dynamic-list.js"></script>
<script type="text/javascript" src="<?=BASE?>include/wz_tooltip.js"></script> 
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#request_parts_form").submit(function()
		{
			$error = new Array();
			$errorno = 0;
			
			$lookup = $('#lookup').val();

			if($lookup == '1')
				$make_id = parseInt($("select[name='make_id']").val());
				if(!($make_id >= 1)) {
					$error[$errorno] = 'You must select a Vehicle Make';
					$errorno++;
				}

				$model_id = parseInt($("select[name='model_type']").val());
				if(!($model_id >= 1)) {
					$error[$errorno] = 'You have not selected a Model';
					$errorno++;
				}

				$reg_year = parseInt($("select[name='reg_year']").val().trim());
				if(!($reg_year > 0)) {
					$error[$errorno] = 'Please select a vehicle Year of Registration.';
					$errorno++;
				}

				$engine_cc = $("select[name='engine_capacity']").val().trim();
				if(!($engine_cc.length > 1)) {
					$error[$errorno] = 'Please Select your engine CC';
					$errorno++;
				}

				$fuel = $("select[name='fuel_type']").val().trim();
				if(!($fuel.length > 1)) {
					$error[$errorno] = 'Type of fuel is a required field.';
					$errorno++;
				}

				$gears = $("select[name='gear_type']").val().trim();
				if(!($gears.length > 1)) {
					$error[$errorno] = 'Please select your Gearbox Type';
					$errorno++;
				}
			}

			$("input[name='category\\[\\]']").each(function(index,object) {
				if(!($(object).val().length > 2)) {
					$error[$errorno] = 'You must enter a Part Name.';
					$errorno++;
				}
			});

			$cust_name = $("input[name=cust_name]").val().trim();
			if(!($cust_name.length > 1)) {
				$error[$errorno] = 'Please enter your name.';
				$errorno++;
			}

			$cust_email = $("input[name=cust_email]").val().trim();
			$cust_phone = $("input[name=cust_phone]").val().trim();
			if(!($cust_email.length > 1) && !($cust_phone.length > 1)) {
				$error[$errorno] = 'You must register an E-Mail Address or Telephone Number.';
				$errorno++;
			}

			if($errorno > 0) {
				$msg = 'Please correct the following:\n\n';
				$($error).each(function(i,val) {
					$msg = $msg + '\t - ' + val + '\n';
				});
				alert($msg);
				return false;
			} else {
				return true;
			}
		});
	});
</script>
<?php
common_middle();
$call = (!empty($_REQUEST['call'])) ? $_REQUEST['call'] : false;
switch($call) {
	default:
	display_request_form($db);
	break;
	
	case"show":
	process_request($db);
	break;
}
bottom();

function display_request_form(&$db,$errors = false)
{
	global $session,$settings,$part_name;
	
	$select_menu = true;

	if(!empty($_POST['reg_number']) && strlen($_POST['reg_number']) > 2)
	{
		$UIP = str_replace('.','',$_SERVER['REMOTE_ADDR']);
		$usage = $db->get_row('SELECT COUNT(*) AS `total`, `stamp` FROM `logs_vehicle_lookup` WHERE `ip` = \''.$UIP.'\'');
		if($settings['reg_lookup_enable'] == true)
		{
			if($usage->total > 0)
			{
				if((time() - $usage->stamp) > 1800)
				{
					$db->query('DELETE FROM `logs_vehicle_lookup` WHERE `ip` = \''.$UIP.'\'');
					$usage->total--;
				}
			}
			if($usage->total < 1)
			{
				/*
				 * <result id="1222730" generated="1329751909" mode="live" account_id="220">
				 *  <vrm>REG_NUMBER</vrm>
				 *  <make>ALFA ROMEO</make>
				 *  <model>SPIDER</model>
				 *  <first_registered>1999-08-31</first_registered>
				 *  <vin>ZAR91600006055977</vin>
				 *  <body>CONVERTIBLE</body>
				 *  <engine_size>1970</engine_size>
				 *  <engine_number>AR323012104090251</engine_number>
				 *  <fuel>PETROL</fuel>
				 * </result>
				*/

				$reg = preg_replace('/[^a-zA-Z0-9]/','',strtoupper($_POST['reg_number']));
				$feed = getXMLFeed('https://www.cdlvis.com/lookup/getxml?username=textspares9841&mode='.(($settings['reg_lookup_test_mode']) ? 'test' : 'live').'&key=GLCBZKJU&vrm='.$reg.'',true);

				$data = new SimpleXMLElement($feed);

				//echo '<pre>'.print_r($data).'</pre>';

				if(empty($data['error']))
				{
					$select_menu = false;
					if(!$settings['debug_mode'])
					{
						$db->query('INSERT INTO `logs_vehicle_lookup`(`ip`,`stamp`) VALUES(\''.$UIP.'\',\''.time().'\')');
					}
				}
			}
		}
	}
	?>

<div id="new_or_existing" class="content">
	<img src="images/findparts.gif"/><br clear="all" />
	<p><strong>UK's Top Online Car Parts Network</strong><br />
	We have wide range of new and used car parts &amp; spares. We also have great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</p>
</div>

<br clear="all" />

<?php
if(!$errors == false && count($errors) > 0) {
	echo '<div class="ui-state-highlight ui-corner-all"><p><span class="ui-icon ui-icon-info" style="float:left;margin-left:10px;margin-right:5px;"></span> Please correct the following:</p><ul>';
	foreach($errors AS $error) {
		echo '<li>'.$error.'</li>';
	}
	echo '</ul></div><br clear="all" />';
}
?>

<div id="registration">
<form name="request_parts_form" method="post" action="<?=BASE;?>partrequest.php" enctype="multipart/form-data" id="request_parts_form">
	<input type="hidden" name="call" value="show">
	<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>

				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
								<tr>
									<td class="heading1">1. VEHICLE DETAILS</td>
								</tr>
							</table>
						</td>
					</tr>
<?php if(!empty($_POST['reg_number']) && ($usage->total > 0 || $settings['reg_lookup_enable'] == false)) { ?>
					<tr>
						<td align="center" valign="middle" style="background-color:#ffa302;color:#000;padding:10px;"><b style="font-size:16px;">Sorry!</b><br/>Vehicle lookup is unavailable at this time.<br/><u>Please select your vehicle details from below</u>.</td>
					</tr>
<?php } ?>
<?php if(!empty($data['error'])) { ?>
					<tr>
						<td align="center" valign="middle" style="background-color:#ffa302;color:#000;padding:10px;"><b style="font-size:16px;">Sorry no match found!</b><br/>No matching vehicle was found for that Registration.<br/><u>Please select your vehicle details from below</u>.</td>
					</tr>
<?php } ?>
					<tr>
						<td style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
<?php
	$reg_lookup = (int) ((!empty($_POST['lookup'])) ? $_POST['lookup'] : 0);
	$select_menu = ($reg_lookup == 0) ? $select_menu : false;
	if($select_menu == false)
	{
		$vrm = (empty($_POST['regnum'])) ? $data->vrm : $_POST['regnum'];
		$make = (empty($_POST['make_name'])) ? $data->make : $_POST['make_name'];
		$model = (empty($_POST['model_name'])) ? $data->model : $_POST['model_name'];
		$first_registered = (empty($_POST['reg_year'])) ? $data->first_registered : $_POST['reg_year'];
		$engine_size = (empty($_POST['engine_capacity'])) ? $data->engine_size : $_POST['engine_capacity'];
		$fuel = (empty($_POST['fuel_type'])) ? $data->fuel : $_POST['fuel_type'];
		$smmt_transmission = (empty($_POST['transmission'])) ? $data->smmt_transmission : $_POST['transmission'];
		$smmt_no_of_gears = (empty($_POST['gears'])) ? $data->smmt_no_of_gears : $_POST['gears'];
		
		echo '
			<input type="hidden" name="regnum" value="'.$vrm.'" />
			<input type="hidden" id="lookup" name="lookup" value="1" />
			<div class="vch"><b>Reg Plate:</b></div><div class="vcc">'.$vrm.'</div><br />
			
			<input type="hidden" name="make_name" value="'.$make.'" />
			<div class="vch"><b>Make:</b></div><div class="vcc">'.$make.'</div><br />
			
			<input type="hidden" name="model_name" value="'.$model.'" />
			<div class="vch"><b>Model:</b></div><div class="vcc">'.$model.'</div><br />
			
			<input type="hidden" name="reg_year" value="'.$first_registered.'" />
			<div class="vch"><b>Date of Registration:</b></div><div class="vcc">'.$first_registered.'</div><br />
			
			<input type="hidden" name="engine_capacity" value="'.$engine_size.'" />
			<div class="vch"><b>Engine CC:</b></div><div class="vcc">'.$engine_size.'</div><br />
			
			<input type="hidden" name="fuel_type" value="'.$fuel.'" />
			<div class="vch"><b>Fuel Type:</b></div><div class="vcc">'.$fuel.'</div><br />
			
			<input type="hidden" name="transmission" value="'.$smmt_transmission.'" />
			<input type="hidden" name="gears" value="'.$smmt_no_of_gears.'" />
			<div class="vch"><b>Gearbox:</b></div><div class="vcc">'.$smmt_no_of_gears.' Gear '.$smmt_transmission.'</div><br />
		';
	}
	else
	{
?>
					<input type="hidden" id="lookup" name="lookup" value="0" />
					<input type="hidden" name="regnum" value="<?=$_POST['reg_number'];?>" />
					<table width="80%" border="0" align="center" cellpadding="1" cellspacing="0">
						<tr><td colspan="2" height="2px"></td></tr>
						<tr>
							<td class="formtext">Make:</td><td>
								<select name="make_id" id="make_id" class="formselectbox" onchange="showModels(this.value);chk_partrequestfrm(this.form)">
								<option value="-1">Select Manufacturer</option>
<?php
								$sqlop = mysql_query("select `make_name`,`make_id` from `vehicle_makes` where `parent_id` = 0 and `disp` = 'y' order by `make_name`")or die(mysql_error());
								if(mysql_num_rows($sqlop)>0)
								{
									while($data=mysql_fetch_array($sqlop)){
										if(!empty($_REQUEST['makeid']) && $_REQUEST['makeid'] == $data['make_id']){
											echo '<option value="'.$data['make_id'].'" selected>'.$data['make_name'].'</option>';
										} else {
											echo '<option value="'.$data['make_id'].'">'.$data['make_name'].'</option>';
										}
										
									}
								}
?>
								</select>
								<span><a href="#" onmouseover="Tip('Select your make <br>of vehicle from the<br> drop down list', CLOSEBTN, false, TITLE, 'Make', STICKY, false)"><img src="images/help.jpg" border="0"></a></span>&nbsp;<span id="error1" valign="top"></span> 
							</td>
						</tr>
<?php
	if(!empty($_REQUEST['makeid'])) {
		echo '<script language="javascript">showModels(\''.$_REQUEST['makeid'].'\',\''.$_REQUEST['modeltype'].'\')</script>';
	}
	
	$engine_capacity = (!empty($_REQUEST['engine_capacity'])) ? $_REQUEST['engine_capacity'] : false;
	$fuel_type = (!empty($_REQUEST['fuel_type'])) ? $_REQUEST['fuel_type'] : false;
	$gear_type = (!empty($_REQUEST['gear_type'])) ? $_REQUEST['gear_type'] : false;
	$body_type = (!empty($_REQUEST['body_type'])) ? $_REQUEST['body_type'] : false;
?>
					<tr>
					<td class="formtext">Model/Type:</td><td>
                	<select name="model_type" class="formselectbox" id="model_id">
                	<option value="-1">----------------------</option>	
					</select>
					<span><a href="#" onmouseover="Tip('Select your model <br>from the drop <br>down list', CLOSEBTN, false, TITLE, 'Model', STICKY, false)"><img src="images/help.jpg" border="0"></a></span>&nbsp;<span id="error2" valign="top"></span> 
					</td></tr>
					<tr><td class="formtext">Year of Registration:</td><td>
                	<select name="reg_year" class="formselectbox" onchange="chk_partrequestfrm(this.form)">
                	<option value="">- Select Year-Reg -</option>	
                	<?
                	$sqlr=mysql_query("select registration_name,registration_val,registration_id  from vehicle_registration order by registration_val desc ")or die(mysql_error());
                	if(mysql_num_rows($sqlr) > 0) {
                		while($recr=mysql_fetch_array($sqlr)) {
                			if(!empty($_REQUEST['regyear']) && $_REQUEST['regyear'] == $recr['registration_val']) {
								echo '<option value="'.$recr['registration_val'].'" selected>'.$recr['registration_name'].'</option>';
							} else {
								echo '<option value="'.$recr['registration_val'].'">'.$recr['registration_name'].'</option>';
							}
                		}
                	}
                	?>
				</select>
				<span>
                	<a href="#" onmouseover="Tip('Select the year your<br> vehicle was <br>registered or manufactured', CLOSEBTN, false, TITLE, 'Year of Manufacture', STICKY, false)"><img src="images/help.jpg" border="0"></a>
                	</span>&nbsp;<span id="error3" valign="top"></span>
              </td></tr>
              <tr><td class="formtext">Engine cc:</td><td>
                	<select name="engine_capacity" class="formselectbox" onchange="chk_partrequestfrm(this.form)">
                	<option value="">- Select Engine Size -</option>	
                	<option value="UNKNOWN" <?=chk_selected($engine_capacity,'UNKNOWN');?> >DON'T KNOW</option>
					<?php
						$engine_options = array(650,900,950,1000,1100,1200,1250,1300,1400,1500,1600,1700,1800,1900,2000,2200,2100,2200,2300,2400,2500,2600,2700,2800,2900,3000,3100,3200,3300,3400,3500,3600,3700,3800,3900,4000,4100,4200,4300,4400,4500,4600,4700,4800,4900,5000,5500,5600);
						foreach($engine_options AS $option)
						{
							$selected = (!empty($engine_capacity) && $option == $engine_capacity) ? 'selected="selected"' : '';
							echo '<option value="'.$option.'" '.$selected.'>'.$option.'cc</option>';
						}
					?>
                </select>
				<span><a href="#" onmouseover="Tip('Select your vehicle\'s engine <br> size, if you are unsure <br>select DON\'T KNOW', CLOSEBTN, false, TITLE, 'Engine Capacity (cc)', STICKY, false)"><img src="images/help.jpg" border="0"></a></span>&nbsp;<span id="error4" valign="top"></span>
              </td></tr>
              <tr><td class="formtext">Type of Fuel:</td><td>
                	<select name="fuel_type" class="formselectbox" onchange="chk_partrequestfrm(this.form)">
                		<option value="">-Select Fuel Type-</option>
                	<option value="petrol" <?=chk_selected($fuel_type,'petrol');?>>Petrol</option>
					<option value="lpg" <?=chk_selected($fuel_type,'lpg');?> >GAS (LPG)</option>
                    <option value="Diesel" <?=chk_selected($fuel_type,'Diesel');?>>Diesel</option>
                    <option value="turbo diesel" <?=chk_selected($fuel_type,'turbo diesel');?>>Turbo Diesel</option>
                    <option value="tdi" <?=chk_selected($fuel_type,'tdi');?>>TDi</option>
                    <option value="hdi" <?=chk_selected($fuel_type,'hdi');?>>HDi</option>
                    <option value="sdi" <?=chk_selected($fuel_type,'sdi');?>>SDi</option>
					<option value="UNKNOWN" <?=chk_selected($fuel_type,'UNKNOWN');?>>DON'T KNOW</option>
                </select>
				<span><a href="#" onmouseover="Tip('Select your vehicle\'s <br>fuel type, if unsure <br>select DON\'T KNOW', CLOSEBTN, false, TITLE, 'Fuel Type', STICKY, false)"><img src="images/help.jpg" border="0"></a></span>&nbsp;<span id="error5" valign="top"></span>
              </td></tr>
              <tr><td class="formtext">Gearbox Type:</td><td>
                	<select name="gear_type" class="formselectbox" onchange="chk_partrequestfrm(this.form)">
                		<option value="">-Select G/Box Type-</option>
                		<option value="4m" <?=chk_selected($gear_type,'4m');?>>4 Speed Manual</option>
						<option value="5m" <?=chk_selected($gear_type,'5m');?>>5 Speed Manual</option>
						<option value="6m" <?=chk_selected($gear_type,'6m');?>>6 Speed Manual</option>
						<option value="3a" <?=chk_selected($gear_type,'3a');?>>3 Speed Automatic</option>
						<option value="4a" <?=chk_selected($gear_type,'4a');?>>4 Speed Automatic</option>
						<option value="5a" <?=chk_selected($gear_type,'5a');?>>5 Speed Automatic</option>
						<option value="6a" <?=chk_selected($gear_type,'6a');?>>6 Speed Automatic</option>
						<option value="4x4m" <?=chk_selected($gear_type,'4x4m');?>>4x4 Manual</option>
						<option value="4x4a" <?=chk_selected($gear_type,'4x4a');?>>4x4 Auto</option>
						<option value="Tiptronic" <?=chk_selected($gear_type,'Tiptronic');?>>Tiptronic</option>
						<option value="Steptronic" <?=chk_selected($gear_type,'Steptronic');?>>Steptronic</option>
						<option value="Switchable Auto" <?=chk_selected($gear_type,'Switchable Auto');?>>Switchable Auto</option>
						<option value="UNKNOWN" <?=chk_selected($gear_type,'UNKNOWN');?>>DON'T KNOW</option>
                </select>	<span>
                	<a href="#" onmouseover="Tip('Select your vehicle\'s <br>type of Gearbox,<br> if unsure select<br> DON\'T KNOW', CLOSEBTN, false, TITLE, 'Gearbox Type', STICKY, 'false')"><img src="images/help.jpg" border="0"></a>
                	</span>&nbsp;<span id="error6" valign="top"></span>
              </td></tr>
              <tr><td class="formtext">Body Type:</td><td>
                	<select name="bodytype" class="formselectbox" onchange="chk_partrequestfrm(this.form)">
						<option value="">- Select Body Type -</option>
						<option value="2 DOOR COUPE" <?=chk_selected($body_type,'2 DOOR COUPE');?>>2 DOOR COUPE</option>
						<option value="3 DOOR HATCH" <?=chk_selected($body_type,'3 DOOR HATCH');?>>3 DOOR HATCH</option>
						<option value="4 DOOR SALOON" <?=chk_selected($body_type,'4 DOOR SALOON');?>>4 DOOR SALOON</option>
						<option value="5 DOOR HATCH" <?=chk_selected($body_type,'5 DOOR HATCH');?>>5 DOOR HATCH</option>
						<option value="COUPE" <?=chk_selected($body_type,'COUPE');?>>COUPE</option>
						<option value="ESTATE" <?=chk_selected($body_type,'ESTATE');?>>ESTATE</option>
						<option value="CONVERTIBLE" <?=chk_selected($body_type,'CONVERTIBLE');?>>CONVERTIBLE</option>
						<option value="MPV" <?=chk_selected($body_type,'MPV');?>>MPV</option>
						<option value="SUV" <?=chk_selected($body_type,'SUV');?>>SUV</option>
						<option value="SWB" <?=chk_selected($body_type,'SWB');?>>SWB</option>
						<option value="MWB" <?=chk_selected($body_type,'MWB');?>>MWB</option>
						<option value="LWB" <?=chk_selected($body_type,'LWB');?>>LWB</option>
						<option value="PICK-UP" <?=chk_selected($body_type,'PICK-UP');?>>PICK-UP</option>
						<option value="MINI-BUS" <?=chk_selected($body_type,'MINI-BUS');?>>MINI-BUS</option>
						<option value="UNKNOWN BODY TYPE" <?=chk_selected($body_type,'UNKNOWN BODY TYPE');?>>DON'T KNOW</option>	
					</select>
				<span><a href="#" onmouseover="Tip('Select your vehicle\'s <br>type of body, if<br> unsure select DON\'T KNOW', CLOSEBTN, false, TITLE, 'Body Type', STICKY, 'false')"><img src="images/help.jpg" border="0"></a></span>&nbsp;<span id="error7" valign="top"></span>
              </td></tr>
              </table>
<?php } ?>
				<input type="hidden" name="token" value="<?=$data->vin;?>" />
			  </td>
            </tr>
          	<tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table>
		<tr><td>&nbsp;</td></tr>
		<tr><td>

        	<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
							<tr><td class="heading1">2. PART DETAILS</td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
						<div id="myDiv"></div>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('input.sbtid').click(function(e) {
			e.preventDefault();
			$faults = 0;
			$('input[name=category]').each(function(index,element) {
				if(($(element).val()).length < 2)
				{
					$faults++;
					alert('Missing Part name for Part '+index);
				}
			});
			if($faults == 0) {
				$('#request_parts_form').submit();
			}
		});
		
		$partsList = new Array();
		$('#parts > option').each(function(i) {
			$partsList[i] = $(this).text();
		});
		var num=1;
		$.fn.showparttwo = function(partname){
			var category = "category" + num;
			var ni = document.getElementById('myDiv');
			var newdiv = document.createElement('div');
			newdiv.style.width = '100%';
			newdiv.innerHTML = "<table width='98%' border='0' align='center' cellpadding='1' cellspacing='0'><tr><td colspan='2' height='2px'></td></tr><tr><td class='formtext'>Part&nbsp;"  + num +  ":</td><td><input type='text' name='category[]' value='" + partname + "' id='" + category + "' class=\"partname special\" /></td></tr><tr><td valign='top'><span class='formtext'>Part&nbsp;"+num+"  Notes (Optional):</span> <div style='font-size:10px;font-family:Verdana, Arial, Helvetica, sans-serif;'>If possible provehicle_ide any additional information e.g Part Numbers / Codes ect </div></td><td><textarea name='desc[]' cols='35' rows='3' class=\"special\"></textarea></td></tr><tr><td><span class='formtext'>Part&nbsp;"+num+" Image (Optional):</span><div style='font-size:10px;font-family:Verdana, Arial, Helvetica, sans-serif;'>If available add a picture to help identify your part correctly. Click the browse button to select the image from your computer</div></td><td><div style='border:0px solid red;width:200px;float:left'><input type='file' name='part_img[]'></div><div id=img"+num+" style='float:right;width:200px;border:0px solid red'></div></td></tr><tr><td class='formlight' colspan='2'><img src='images/pixel-blue.gif' width='100%' height='1'></td></tr></table>";
			ni.appendChild(newdiv);
			$('#' + category).autocomplete({
				source: $partsList
			});
			num++;
		}
<?php
	$post_parts = (!empty($_POST['category']) && count($_POST['category']) > 0) ? $_POST['category'] : ((!empty($_POST['parts'])) ? $_POST['parts'] : 1);
	if(count($post_parts) > 1)
	{
		foreach($post_parts AS $part)
		{
			echo '		$.fn.showparttwo(\''.$part.'\');'."\n"; 
		}
	}
	else 
	{
		$part = (!empty($post_parts[0]) && strlen(ereg_replace('[^0-9a-zA-Z]','',$post_parts[0])) > 1) ? $post_parts[0] : (!empty($part_name) ? $part_name : ' ');
		echo '		$.fn.showparttwo(\''.$part.'\');'."\n";
	}
?>
	});
</script>
					</td>
				</tr>
				<tr>
					<td class="formlight" colspan="2" align="center" style="background:url(images/centerbox_mainrepeatbg.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;" valign="top">
						<a href="javascript:$.fn.showparttwo('');"><img src="images/butt-addpart.gif" border="0"></a>
					</td>
				</tr>
				<tr>
					<td><img src="images/footerpart.gif" width="551" height="12" /></td>
				</tr>
			</table>
		</td></tr>	
		<tr><td>&nbsp;</td></tr>
		<tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%">
        		<tr>
					<td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">3. YOUR DETAILS</td></tr>
            </table>
            	</td>
          </tr>
          <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
              		<tr><td colspan="2" height="2px"></td></tr>
              		<tr><td class="formtext">Contact Name:</td><td>
						<input type="text" name="cust_name" class="formtxtbox" onchange="chk_partrequestfrm(this.form)" value="<?=(!empty($_POST['cust_name'])) ? $_POST['cust_name'] : (($session->loggedin == true) ? $session->userdata['name'] : '');?>">&nbsp;
              			<a href="#" onmouseover="Tip('Please provehicle_ide us with a contact name so we can contact you.', CLOSEBTN, false, TITLE, 'First Name', STICKY, 'false')"><img src="images/help.jpg" border="0"></a>
              			&nbsp;<span id="error8" valign="top"></span>
              		<tr><td class="formtext">Contact Telephone No:</td><td><input type="text" name="cust_phone" class="formtxtbox" onchange="chk_partrequestfrm(this.form)" value="<?=(!empty($_POST['cust_phone'])) ? $_POST['cust_phone'] : (($session->loggedin == true) ? $session->userdata['phone'] : '');?>" />&nbsp;
              			<a href="#" onmouseover="Tip('Provehicle_ide a daytime telephone number so that we may contact you.', CLOSEBTN, false, TITLE, 'Telephone Number', STICKY, 'false')"><img src="images/help.jpg" border="0"></a>
              			&nbsp;<span id="error9" valign="top"></span>
              			</td></tr>
              		<tr>
						<td class="formtext">Email Address:</td><td>
						<?=(($session->loggedin == false) ? '<input type="text" name="cust_email" class="formtxtbox" value="'.(!empty($_POST['cust_email']) ? $_POST['cust_email'] : '').'" onchange="chk_partrequestfrm(this.form)" />' : '<input type="hidden" name="cust_email" class="formtxtbox" value="'.$session->userdata['email'].'" /><input type="hidden" name="confirm_email" value="'.$session->userdata['email'].'" />'.$session->userdata['email']);?>&nbsp;
              			<a href="#" onmouseover="Tip('Use an email address if you would like to be contacted via email.', CLOSEBTN, false, TITLE, 'Email', STICKY, false)"><img src="images/help.jpg" border="0"></a>
              			&nbsp;<span id="error10" valign="top"></span>
              			</td>
					</tr>
<?php if($session->loggedin == false) { ?>
              		<tr><td class="formtext">Confirm Email Address:</td><td><input type="text" name="confirm_email" value="<?=(!empty($_POST['confirm_email'])) ? $_POST['confirm_email'] : '';?>" class="formtxtbox">&nbsp;
              			<a href="#" onmouseover="Tip('Re-type email <br>address to ensure we have the correct email address from above.', CLOSEBTN, false, TITLE, 'Confirm Email', STICKY, false)"><img src="images/help.jpg" border="0"></a>
              			&nbsp;<span id="error11" valign="top"></span>
              			</td></tr>
<?php } ?>
					<tr>
						<td class="formtext">Recieve Quotes by TEXT SMS?:</td>
						<td><input type="radio" name="quote_answer" value="y" <?=((($session->loggedin == true && $session->userdata['recieve_sms'] == 'y') || (!empty($_POST['quote_answer']) && $_POST['quote_answer'] == 'y')) ? 'checked' : '');?> />Yes<input type="radio" value='n' name="quote_answer" <?=((($session->loggedin == true && $session->userdata['recieve_sms'] == 'n') || (!empty($_POST['quote_answer']) && $_POST['quote_answer'] == 'n')) ? 'checked' : (empty($_POST['quote_answer'])) ? 'checked' : '');?> />No</td>
					</tr>
<?php if(!$session->loggedin == true) { ?>
					<tr>
						<td class="formtext">Are you a trader?:</td>
						<td><input type="radio" name="trader" value="y" <?=( (($session->loggedin == true && $session->userdata['auth'] == 1) || (!empty($_POST['trader']) && $_POST['trader'] == 'y') ) ? 'checked' : '');?> />Yes<input type="radio" value='n' name="trader" <?=((($session->loggedin == true && $session->userdata['auth'] == 0) || (!empty($_POST['trader']) && $_POST['trader'] == 'n') ) ? 'checked' : (empty($_POST['trader'])) ? 'checked' : '');?> />No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onmouseover="Tip('Select Yes if you are a tradesman.<br/>We will give you free helpfull tools to help manage your customers.', CLOSEBTN, false, TITLE, 'Trader Account', STICKY, false)"><img src="images/help.jpg" border="0"></a></span></td>
					</tr>
              		<tr>
					<td class="formtext">Select your Country:</td>
              		<td><select name="country" class="formselectbox" style="width:160px"><?=country_options();?></select></td>
					</tr>
<?php } ?>
                  </table>             
              </td>
            </tr>
            <tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table>
      </td></tr>
        <tr><td>&nbsp;</td></tr>	
      </td></tr>
      <tr><td>
      	<table cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr><td align="right"><input type="image" src="images/submitnow_off.gif" id="sbtid" onmouseover="showimage('sbtid','images/submitnow_on.gif',1)" onmouseout="showimage('sbtid','images/submitnow_off.gif',2)"></td></tr>
      	<tr>
          <td valign="top" align="justify">
      		<input type="checkbox" value='y' name="agreechk" checked> 
            I have read &amp; agreed to the Textspares <a href='terms.php' class="midlinks" target="_blank">Terms and Conditions</a>, and understand I will be
sent quotes and offers from time to time by the network.<br/>
<font style="font-size:14px;font-weight:bold;">We will take all the running around away from you with one quick search and have the Car Part you require delivered next day to your door, or have it reserved for collection. We can help you find that Car Part you're looking for at textspares.co.uk, local to you. At Textspares we have one of the largest databases of Car Parts and Car Breakers in the UK.</font><br/>
<font style="font-size:11px;">You can opt in to recieve quotes by SMS, Where a SMS text is sent to activate "Text Quotes" on your mobile, if you choose to activate this service and reply back to the short code "60777" and the word SPARES please note there is a &pound;5.00 + 1 standard text message charge to activate this service. By activating this service does not guarantee you will receive text quotes, the charge is made merely to activate the service on your mobile phone number. Once the service has successfully been activated, all "TEXT QUOTES" will be sent to your mobile on which you have activated the service. The number of quotes sent when the service is activated is "unlimited", but subject to a fair use policy.</font>
          </td>
        </tr>
      </table>
      	</td></tr>
</table>        
</form>
</div>

<?php
}
function process_request($db)
{
	global $session,$settings;

	$timegroup = time();

	$verification_code = "";
	$alphanum  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num = '0123456789';
	$randno = substr(str_shuffle($num), 0, 6);
	$rand = substr(str_shuffle($alphanum), 0, 2);
	$verification_code.=$rand;
	$verification_code.=$randno;

	$password = rand(1000,10000);

	$x=0;
	$desc = (!empty($_POST['desc'])) ? $_POST['desc'] : '';
	
	$trader = (!empty($_POST['trader']) && $_POST['trader'] == 'y') ? 1 : 0;
	$customer_id = 0;
	$areg = false;
	
	if(!empty($_POST['cust_email']) && validEmail($_POST['cust_email']) && !$session->loggedin == true)
	{
		$sql = 'SELECT `customer_id` FROM `customer_information` WHERE `cust_email` = \''.$db->mysql_prep($_POST['cust_email']).'\' LIMIT 1';
		$customer_id = $db->get_var($sql);
		if($customer_id > 0) {
			$areg = true;
		}
	}
	
	/*----------------------------------------
	**------ BEGIN VALIDATE FORM -------------
	**----------------------------------------*/
	$badcust = false;
	$errors = array();
	$cust_name = trim($db->mysql_prep($_POST['cust_name']));
	$cust_phone = $db->mysql_prep(ereg_replace("[^0-9]",'',$_POST['cust_phone']));
	$cust_email = trim($db->mysql_prep($_POST['cust_email']));

	$make_id = (int) ((isset($_POST['make_id'])) ? $db->mysql_prep($_POST['make_id']) : 0);
	$model_id = (int) ((isset($_POST['model_type'])) ? $db->mysql_prep($_POST['model_type']) : 0);
	$make_name = (isset($_POST['make_name'])) ? trim($db->mysql_prep($_POST['make_name'])) : false;
	$model_name = (isset($_POST['model_name'])) ? trim($db->mysql_prep($_POST['model_name'])) : false;
	$vin = (!empty($_POST['token'])) ? trim($db->mysql_prep($_POST['token'])) : 'Not Found';

	$reg_year = (!empty($_POST['reg_year'])) ? trim($db->mysql_prep($_POST['reg_year'])) : false;
	$engine_capacity = (!empty($_POST['engine_capacity'])) ? trim($db->mysql_prep($_POST['engine_capacity'])) : false;
	$fuel_type = (!empty($_POST['fuel_type'])) ? trim($db->mysql_prep($_POST['fuel_type'])) : false;
	$transmission = (!empty($_POST['transmission'])) ? trim($db->mysql_prep($_POST['transmission'])) : false;
	$gears = (!empty($_POST['gears'])) ? trim($db->mysql_prep($_POST['gears'])) : false;
	$bodytype = (!empty($_POST['bodytype'])) ? trim($db->mysql_prep($_POST['bodytype'])) : false;

	if($make_id == 0) {
		$badcust = true;
		$errors[] = 'Supplier requires the Make of your Vehicle A'.$make_id;
	} else {
		if($make_id < 1 && strlen($make_name) < 1) {
			$badcust = true;
			$errors[] = 'Supplier requires the Make of your Vehicle B'.$make_id;
		}
	}

	if($model_id == 0 && !$model_name) {
		$badcust = true;
		$errors[] = 'Supplier requires the Model Name of your Vehicle';
	} else {
		if($model_id < 1 && strlen($model_name) < 1) {
			$badcust = true;
			$errors[] = 'Supplier requires the Model Name of your Vehicle';
		}
	}

	if(strlen($reg_year) < 3) {
		$badcust = true;
		$errors[] = 'Suppliers require your vehicles Year of Registration in order to find the correct part.';
	}

	if(strlen($cust_name) < 1) {
		$badcust = true;
		$errors[] = 'Do you not have a name? What should we call you?';
	}

	if(validEmail($cust_email) == false && strlen($cust_phone) < 10) {
		$badcust = true;
		$errors[] = 'You must at least provehicle_ide either an e-mail address or Landline/Mobile number';
	}

	if(strlen($cust_email) > 0 && validEmail($cust_email) == false) {
		$badcust = true;
		$errors[] = 'You have provehicle_ided an invalid e-mail address';
	}

	if(strlen($cust_phone) > 0 && strlen($cust_phone) < 10) {
		$bascust = true;
		$errors[] = 'You have provehicle_ided an invalid Contact Number.<br/>Please ensure you have included your area code';
	}

	if(is_array($_POST['category']))
	{
		$pi = 1;
		foreach($_POST['category'] as $key => $val)
		{
			if(strlen(trim($val)) < 2) {
				$bascust = true;
				$errors[] = 'Part '.$pi.' does not have a valid part name.';
			}
			$pi++;
		}
	} else {
		$bascust = true;
		$errors[] = 'You have not submitted any parts for quotation.';
	}
	
	/*----------------------------------------
	**------ END VALIDATE FORM -------------
	**----------------------------------------*/
	if($badcust == false)
	{
		if($session->loggedin == true || $customer_id > 0)
		{
			if(!$customer_id > 0) {
				$customer_id = $session->userdata['id'];
			}
			if($customer_id > 0) {
				$sqlcustomer = 'UPDATE `customer_information` SET `cust_name` = \''.$cust_name.'\', `cust_phone` =  \''.$cust_phone.'\', `recieve_sms` = \''.$db->mysql_prep($_POST['quote_answer']).'\' WHERE `customer_id` = \''.$customer_id.'\'';
				$result = mysql_query($sqlcustomer)or die(mysql_error());
			}
			$_SESSION['register_email'] = false;
		}
		else
		{
			$sqlcustomer = 'INSERT INTO `customer_information`(cust_name,cust_username,cust_password,account_status,cust_phone,cust_email,country,recieve_sms,trader)
					VALUES(	\''.$cust_name.'\', 
							\''.$verification_code.'\',
							\''.md5($password).'\',
							\'1\',
							\''.$cust_phone.'\', 
							\''.$cust_email.'\', 
							\''.$db->mysql_prep($_POST['country']).'\',
							\''.$db->mysql_prep($_POST['quote_answer']).'\',
							\''.$trader.'\'
					)';
			$result = mysql_query($sqlcustomer)or die(mysql_error());
			$customer_id = mysql_insert_id();
			$_SESSION['register_email'] = $cust_email;
		}
	} else {
		display_request_form(&$db,$errors);
	}

	if($customer_id > 0 && $badcust == false)
	{
		$sqlvehicle = 'INSERT INTO `customer_vehicles`(`customer_id`,`reg_number`,`vehicle_make`,`vehicle_make_name`,`vehicle_model`,`vehicle_model_name`,`registration_year`,`engine_size`,`fuel_type`,`transmission`,`gears`,`body_type`,`vin`)
						VALUES(
							\''.$customer_id .'\',
							\''.$db->mysql_prep($_POST['regnum']).'\',
							\''.$make_id.'\',
							'.(($make_id > 0) ? '(SELECT `make_name` FROM `vehicle_makes` WHERE `make_id` = \''.$make_id.'\' LIMIT 1)' : '\''.$make_name.'\'').',
							\''.$model_id.'\',
							'.(($model_id > 0) ? '(SELECT `make_name` FROM `vehicle_makes` WHERE `make_id` = \''.$model_id.'\' LIMIT 1)' : '\''.$model_name.'\'').',
							\''.$reg_year.'\',
							\''.$engine_capacity.'\',
							\''.$fuel_type.'\',
							\''.$transmission.'\',
							\''.$gears.'\',
							\''.$bodytype.'\',
							\''.$vin.'\'
						)';
		mysql_query($sqlvehicle)or die(mysql_error());
		$vehicle_id = mysql_insert_id();
		
		/*
		if($_POST['quote_answer'] == 'y')
		{
			$url = 'http://web.textvertising.co.uk/cgi-bin/smssend.pl';  
			$sqlmssg=mysql_query("select * from smstext")or die(mysql_error());
			$recmssg=mysql_fetch_array($sqlmssg);
			$institution="$recmssg[smstxt]";
			$uname="spares";
			$pass="catw63";
			$rpt="xml";
			$fields = array(
							'numbers' => $phone_number,
							'user'=>urlencode($uname),
							'pass'=>urlencode($pass),
							'message'=>$institution,
							'smsid'=>82055,
							'expiry'=>1231
			);

			//url-ify the data for the POST  
			foreach($fields as $key=>$value)
			{ 
				$fields_string .= $key.'='.$value.'&';
			}  
			rtrim($fields_string,'&');  
			//open connection  
			$ch = curl_init();  
			//set the url, number of POST vars, POST data  
			curl_setopt($ch,CURLOPT_URL,$url);  
			curl_setopt($ch,CURLOPT_POST,count($fields));  
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);  
			//execute post  
			curl_exec($ch);  
			//close connection  
			curl_close($ch); 
		}
		*/
		
		//echo '<pre>'.print_r($_POST['category']).'</pre>';
			
		if(is_array($_POST['category']))
		{
			$auto = array();
			foreach($_POST['category'] as $key => $val)
			{
				$ssql = 'SELECT `part_id` FROM `parts_categories` WHERE `part_cat_name` LIKE \''.$db->mysql_prep($val).'\' LIMIT 1';
				$part_id = $db->get_var($ssql) or 0;
				$part_name = $val;
				$part_name = (strlen($part_name) < 1) ? 'Part name missing!' : $part_name;

				$sql2 = 'INSERT INTO `customer_requests`(customer_id,vehicle_id,part_category,part_name,request_notes,request_stamp) VALUES( \''.$customer_id.'\', \''.$vehicle_id.'\', \''.$part_id.'\', \''.$part_name.'\', \''.$db->mysql_prep($desc[$x]).'\', \''.$timegroup.'\' )';
				mysql_query($sql2)or die(mysql_error());
				$request_id = mysql_insert_id();
				
				if($part_id > 0)
				{
					$sql_auto_quote = 'SELECT `aq`.*,`pc`.* FROM `tblAutoQuotes` AS `aq` INNER JOIN `parts_categories` AS `pc` ON `aq`.`part_id` = `pc`.`part_id` WHERE `aq`.`vehicle_make` = \''.$make_id.'\' AND `aq`.`vehicle_model` = \''.$model_id.'\' AND `aq`.`part_id` = \''.$part_id.'\' ';
					echo $quotes = $db->get_results($sql_auto_quote);
					$cache_quotes_error = mysql_error();
					if($quotes)
					{
						foreach($quotes as $quotes) {
							$agent = $quotes->company_user_id;
							if(!is_array($auto[$agent])) {
								$auto[$agent] = array();
								$auto[$agent]['agentid'] = $quotes->company_user_id;
								$auto[$agent]['company'] = $quotes->company_id;
								$auto[$agent]['delivery'] = $quotes->delivery_fee;
								$auto[$agent]['parts'] = array();
							}
							$auto[$agent]['parts'][$part_id]['id'] = $part_id;
							$auto[$agent]['parts'][$part_id]['requestid'] = $request_id;
							$auto[$agent]['parts'][$part_id]['name'] = $quotes->part_cat_name;
							$auto[$agent]['parts'][$part_id]['price'] = $quotes->decValue;
							$auto[$agent]['parts'][$part_id]['guarantee'] = $quotes->strGuarantee;
							$auto[$agent]['parts'][$part_id]['condition'] = $quotes->strCondition;
						}
					} else {
						if($cache_quotes_error) debug_reports($cache_quotes_error,$sql_auto_quote);
					}

					$sql_auto_alert = 'SELECT `company_user_id` FROM `tblRequestAlertSettings` WHERE `vehicle_make` = \''.$make_id.'\' AND `vehicle_model` = \''.$model_id.'\' AND (`part_id` = \''.$part_id.'\' OR `part_id` = 0) ';
					//print($sql_auto_quote);
					$reqAlert = $db->get_results($sql_auto_alert);
					$cache_alerts_error = mysql_error();
					if($reqAlert)
					{
						foreach($reqAlert as $reqAlert)
						{
							auto_alert_supplier($vehicle_id,$reqAlert->company_user_id);	
						}
					} else {
						if($cache_alerts_error) {
							debug_reports($cache_alerts_error,$sql_auto_alert);
						}
					}
				}

				if($_FILES['part_img']['error'][$x] == 0)
				{
					$allow = array('jpeg','jpg');
					$img_src = $_FILES['part_img']['tmp_name'][$x];
					$img_ext = pathinfo($img_src,PATHINFO_EXTENSION);
					
					if(in_array($img_ext,$allow))
					{
						create_image($img_src,'','_requests/images/',800,$request_id);
					}
				}
				$x++;
			}
			if(count($auto) > 0) {
				send_auto_quote($customer_id,$vehicle_id,$auto);
			}

			if($session->loggedin == true || $areg == true)
			{
				echo '
					<form name="frm" method="POST" action="thanks.php" >
					<input type="hidden" name="areg" value="'.(($areg == true) ? 'true':'false').'" />
					<input type="hidden" name="custid" value="'.$customer_id.'" />
					<input type="hidden" name="vehicle_id" value="'.$vehicle_id.'" />
					'.(($settings['debug_mode']) ? '<input type="submit" name="Submit" value="Submit" />' : '<script>document.frm.submit();</script>').'
					</form>
				';
			}
			else
			{
				echo '
					<form name="frm" method="POST" action="thanks.php" >
					<input type="hidden" name="vcode" value="'.$verification_code.'" />
					<input type="hidden" name="_username" value="'.$verification_code.'" />
					<input type="hidden" name="_password" value="'.$password.'" />
					<input type="hidden" name="custid" value="'.$customer_id.'" />
					<input type="hidden" name="vehicle_id" value="'.$vehicle_id.'" />
					'.(($settings['debug_mode']) ? '<input type="submit" name="Submit" value="Submit" />' : '<script>document.frm.submit();</script>').'
					</form>
				';
			}
		}
	}
}
?>

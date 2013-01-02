<?php
define('IN_TEXTSPARES',true);
include('include/common.inc.php');

if(!$session->loggedin) header('location:customer_login.php');

$action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : false;

switch($action)
{
	default:

	top();
	common_middle();

	$error = array();
	$halt = false;

	switch($_REQUEST['method'])
	{
		case 'phone':
			echo 'Phone Order';
			$method = 1;
			break;
			
		default:
			$error[] = 'No payment method selected!';
			$method = 0;
			break;
	}

	if(!is_numeric($_REQUEST['vehicle_id'])) {
		$error[] = 'Invalid Vehicle ID';
	} else { $vehicle_id = $db->mysql_prep($_POST['vehicle_id']); }

	if(!is_numeric($_REQUEST['quote_id'])) {
		$error[] = 'Invalid Quote ID';
	} else { $quote = $db->mysql_prep($_POST['quote_id']); }

	if(count($error) > 0)
	{
		foreach($error AS $e) {
			echo '<br/>'.$e.'<br/>';
		}
	} else {

		if($v = $db->get_row('SELECT `c`.`cust_name`,`c`.`cust_phone`,`c`.`cust_email`,`c`.`addr1`,`c`.`addr2`,`c`.`city`,`c`.`county`,`c`.`country`,`c`.`post_code`,`v`.`vehicle_id`,`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year`,`v`.`engine_size`,`v`.`fuel_type`,`v`.`transmission`,`v`.`body_type` FROM `customer_vehicles` AS `v` JOIN `customer_information` AS `c` ON `c`.`customer_id` = \''.$session->userdata['id'].'\' WHERE `v`.`customer_id` = \''.$session->userdata['id'].'\' AND `v`.`vehicle_id` = \''.$vehicle_id.'\' ORDER BY `vehicle_id` DESC'))
		{
			echo '
				<h1>Process Order</h1>

				<script type="text/javascript">
				$(document).ready(function() {
					$(\'#send-customer-details\').click(function(e) {
						e.preventDefault();
						$.getJSON("'.BASE.'my_order.php?action=submit_order",$(\'#customer-details\').serialize(), function(data)
						{
							if(data[0] == \'error\') {
								alert(data[1] + "\n\n" + data[2]);
							} else {
								window.location.href = "'.BASE.'my_requests.php?vehicle_id='.$v->vehicle_id.'";
							}
						});
					});
				});
				</script>
				<div class="myquotes" id="'.$v->vehicle_id.'">
					<h1>'.pad_ref_number($v->vehicle_id).' : '.$v->vehicle_make_name.' '.$v->vehicle_model_name.'</h1>
					<h3>'.$v->registration_year.' | '.$v->engine_size.' | '.$v->fuel_type.' | '.$v->transmission.' | '.$v->body_type.'</h3>';

			$sup_sql = 'SELECT
				`sq`.`quote_id`,
				`sq`.`quote_time`,
				`sq`.`d_fee`,
				`sq`.`d_est`,
				`su`.`strName` AS `agent`,
				`sc`.`c_name`,
				`sc`.`c_fax`,
				`sc`.`c_vat`,
				`sc`.`c_info`
					FROM `supplier_quotes` AS `sq`
					JOIN `supplier_company_users` AS `su` ON `su`.`company_user_id` = `sq`.`company_user_id`
					JOIN `supplier_company` AS `sc` ON `sc`.`company_id` = `sq`.`company_id`
					WHERE `sq`.`customer_id` = \''.$session->userdata['id'].'\' AND `sq`.`vehicle_id` = \''.$v->vehicle_id.'\' AND `sq`.`quote_id` = \''.$quote.'\' ';
			if($suppliers = $db->get_results($sup_sql))
			{
				foreach($suppliers AS $supplier)
				{
					$subtotal = 0;
					$est = explode('-',$supplier->d_est);
					$dtime = false;
					if($est[0] > 0) {
						switch($est[1]) {
							case "d":
								if($est[0] < 5) {
									$dtime = (24 * $est[0]).' Hours';
								} else {
									$dtime = $est[0].' Days';
								}
								break;
							case "w":
								$dtime = $est[0].' Week'.(($est[0] > 1) ? 's' : '');
								break;
						}
					}
					echo '
					<div class="supplier red-stripes">&nbsp; '.$supplier->c_name.' '.$supplier->agent.'</div>
					<div class="quote">
					<table width="100%" cellspacing="0" cellpadding="2" border="0">
						<tr>
							<th width="15" align="center"></th>
							<th width="*">Part</th>
							<th width="90">Guarentee</th>
							<th width="70">Price</th>
						</tr>
					';
					$quote_items = array();
					if(count($_POST['item']) > 0)
					{
						foreach($_POST['item'] AS $quote) {
							if(is_numeric($quote)) {
								$quote_items[] = '`qd`.`quote_detail_id` = '.$db->mysql_prep($quote).'';
							} else {
								echo 'HALTED';
								$halt = true;
							}
						}
					} else {
						$halt = true;
						echo'
							<tr class="partrow">
								<td align="center"></td>
								<td colspan="3">There are no parts in your basket! <a href="my_requests.php">&lt; Go Back</a></td>
							</tr>
						';
					}
					
					if(!$halt)
					{
						$extend = implode(' OR ',$quote_items);
						$quote_sql = 'SELECT `qd`.`quote_detail_id`,`qd`.`quote_price`,`qd`.`quote_guarantee`,`qd`.`quote_condition`,`qd`.`request_id`,`qd`.`cancelled`,
										`p`.`part_name`,`p`.`order_group`
										FROM `supplier_quotes_details` AS `qd` 
										JOIN `customer_requests` AS `p` ON `p`.`request_id` = `qd`.`request_id`
										WHERE `qd`.`quote_id` = \''.$supplier->quote_id.'\' AND `qd`.`method` = 0 AND ('.$extend.')';
						$result = mysql_query($quote_sql);
						if(!$result) {
							echo 'No Records Found due to error.<br/>'.mysql_error();
						}
						else
						{
							$records = mysql_num_rows($result);
							if($records > 0)
							{
								$sresult = mysql_query('UPDATE `supplier_quotes` SET `modified` = \''.time().'\' WHERE `quote_id` = \''.$supplier->quote_id.'\' ');
								if(!$sresult || mysql_affected_rows() == 0) {
									echo 'No Records Updated in Supplier Quotes.<br/>'.mysql_error();
								} else {
							
									while($q = mysql_fetch_object($result))
									{
		
										$sresult = mysql_query('UPDATE `supplier_quotes_details` SET `method` = \''.$method.'\', `modified` = '.time().', `cancelled` = 0 WHERE `quote_id` = \''.$supplier->quote_id.'\' AND `quote_detail_id` = \''.$q->quote_detail_id.'\' ');
										if(!$sresult || mysql_affected_rows() == 0) {
											echo 'No Records Updated in Quote Details.<br/>'.mysql_error();
										} else {
											$sresult = mysql_query('UPDATE `customer_requests` SET `order_group` =  \''.$supplier->quote_id.'\' WHERE `request_id` = \''.$q->request_id.'\' ');
											if(!$sresult || mysql_affected_rows() == 0) {
												echo 'No Records Updated in Consumer Requests.<br/>'.mysql_error();
												mysql_query('UPDATE `supplier_quotes_details` SET `method` = 0, `cancelled` = '.$quotes->cancelled.' WHERE `quote_id` = \''.$supplier->quote_id.'\' AND `quote_detail_id` = \''.$q->quote_detail_id.'\' AND `company_user_id` = \''.$session->userdata['id'].'\'');
											}
										}
									
										$subtotal = $subtotal + $q->quote_price;
										echo'
											<tr class="partrow">
												<td align="center"></td>
												<td>'.$q->part_name.' <b>('.$q->quote_condition.')</b></td>
												<td>'.$q->quote_guarantee.'</td>
												<td>
													<input id="price_'.$q->quote_detail_id.'" type="hidden" name="price['.$q->quote_detail_id.']" value="'.$q->quote_price.'" />
													<input id="target_'.$q->quote_detail_id.'" type="hidden" name="target['.$q->quote_detail_id.']" value="'.$supplier->quote_id.'" />
													&pound;'.$q->quote_price.'
												</td>
											</tr>
										';
									}
								}
								mysql_free_result($result);
							} else {
								echo 'No records returned.';
							}
						} 
						echo '
							<tr>
								<td></td>
								<td valign="top"><input type="hidden" name="delivery_'.$v->vehicle_id.'" value="'.$supplier->d_fee.'" />'.(($dtime == false) ? 'No Devlivery estimate given' : 'We estimate delivery within '.$dtime).'</td>
								<td valign="top" align="right" ><b>Delivery Fee: <br/>Sub Total:</b></td>
								<td valign="top">&pound;'.$supplier->d_fee.'<br/>&pound;'.number_format($subtotal + $supplier->d_fee, 2).'</td>
							</tr>
						';
					}
					echo'
						</table>
					</div>
					<div class="supplier grey-stripes">&nbsp; Delivery Details</div>
					<div class="quote input-details">
						<form name="customer-details" id="customer-details" method="POST" action="">
						<table width="530" cellspacing="0" cellpadding="2" border="0" style="margin:2px 15px;">
							<tr>
								<th width="160" align="right" valign="middle">Your Name:</th>
								<td width="*"><input type="text" name="cust_name" value="'.$v->cust_name.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">Phone Number:</th>
								<td><input type="text" name="cust_phone" value="'.$v->cust_phone.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">E-Mail Address:</th>
								<td><input type="text" name="cust_email" value="'.$v->cust_email.'" /></td>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th align="right" valign="middle">No./Name &amp; Street:</th>
								<td><input type="text" name="addr1" value="'.$v->addr1.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">Street Line 2:</th>
								<td><input type="text" name="addr2" value="'.$v->addr2.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">City:</th>
								<td><input type="text" name="city" value="'.$v->city.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">County</th>
								<td><input type="text" name="county" value="'.$v->county.'" /></td>
							</tr>
							<tr>
								<th align="right" valign="middle">Country:</th>
								<td><select name="country">'.country_options($v->country).'</select></td>
							</tr>
							<tr>
								<th align="right" valign="middle">Post Code:</th>
								<td><input type="text" name="postcode" value="'.$v->post_code.'" /></td>
							</tr>
						</table>
						<br clear="all" />
						<a class="sbb onblue" id="send-customer-details" href="">Finnish Order</a>
						</form>
					</div>
					';
				}
			} else {
				echo '<div class="quote"> &nbsp; You have not recieved any quotes yet.</div>';
			}
			echo '</div><br clear="all" />';
		}
	}
	bottom();
	break;
	
	case 'submit_order':
		$errors = array();

		$billing_valid = false;
		$delivery_valid = false;

		$cust_name = $db->mysql_prep(trim($_REQUEST['cust_name']));
		$cust_phone = $db->mysql_prep(ereg_replace("[^0-9]",'',$_REQUEST['cust_phone']));
		
		if(strlen(ereg_replace("[^A-Za-z_\s]",'',$cust_name)) < 2) {
			$errors[] = 'Invalid Customer Name.';
		}
		if(strlen($cust_phone) < 10) {
			$errors[] = 'Invalid Phone Number.';
		}
		
		$valid = validAddress('addr1','addr2','city','county','country','postcode');
		
		if(count($valid['errors']) > 0) {
			$errors = array_merge($errors,$valid['errors']);
		}
		
		//TODO: Intergrate support for seperate Billing and Delivery Addresses.
		//if($_REQUEST['same_billing'] != "yes") {
		//	$billing_valid = validAddress('biling_addr1','billing_addr2','billing_city','billing_county','billing_country','billing_post_code');
		//	if(count($billing_valid['errors']) > 0) {
		//		$errors = array_merge($errors,$valid['errors']);
		//	}
		//}
		
		//if($_REQUEST['same_delivery'] != "yes") {
			
		//	$delivery_valid = validAddress('delivery_addr1','delivery_addr2','delivery_city','delivery_county','delivery_country','delivery_post_code');
		//	if(count($delivery_valid['errors']) > 0) {
		//		$errors = array_merge($errors,$valid['errors']);
		//	}
		//}
		
		if(count($errors) == 0)
		{
			$sql = 'UPDATE `customer_information` SET 
				`cust_name` = \''.$cust_name.'\',
				`cust_phone` = \''.$cust_phone.'\',
				`addr1` = \''.$valid['data']['address1'].'\',
				`addr2` = \''.$valid['data']['address2'].'\',
				`city` = \''.$valid['data']['city'].'\',
				`county` = \''.$valid['data']['county'].'\',
				`country` = \''.$valid['data']['country'].'\',
				`post_code` = \''.$valid['data']['postcode'].'\'
				WHERE `customer_id` = \''.$session->userdata['id'].'\'';
			$db->query($sql);
		}
		
		if(count($errors) > 0) {
			$jsondata = array('error',implode("\n",$errors));
		} else {
			//TODO: Add notification alerts.
			$jsondata = array('ok');
		}
		
		request_json($jsondata,1);
		
	break;
}
?>      
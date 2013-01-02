<?php
define('IN_TEXTSPARES',true);

include('include/common.inc.php');

if(!$session->loggedin) header('location:customer_login.php');

top();
common_middle();

$future = 604800;

$user_id = $session->userdata['id'];

?>

<h1>Welcome to your requests page.</h1>

<script type="text/javascript">
$(document).ready(function() {
	$("input[type='checkbox']").change(function() {
		$trigger_detail = parseInt($(this).val());
		$trigger_quote = $('#quote_id_'+$trigger_detail).val();
		$target = 0;
		$count = 0;
		$result = 0.00;
		$("input[type='checkbox']").each(function() {
			$detail_id = parseInt($(this).val());
			$quote_id = $('#quote_id_'+$detail_id).val();
			if($trigger_quote == $quote_id) {
				$target = $quote_id;
				if(this.checked) {
					$result = $result + parseFloat($('#price_'+$detail_id).val());
					$count++;
				}
			}
		});
		$subbed = $result = $result + parseFloat($('#delivery_'+$target).val());
		if($('#vat_'+$target).val() > 0) $result = $result + (($result/100)*<?=$settings['vat_rate'];?>);
		if($count == 0) { $result = 0; $subbed = 0 }
		$subbed = $subbed.toFixed(2);
		$result = $result.toFixed(2);
		$('#phone_'+$target).text($result);
		$('#phone_sub_'+$target).text($subbed);
		$('#items_'+$target).text($count);
		if($count > 0) {
			$('#pay_by_phone_'+$target).css("background-color","#045F98");
		} else {
			$('#pay_by_phone_'+$target).css("background-color","#CCCCCC");
		}
	});
	$('.pay_by_phone').click(function(e) {
		e.preventDefault();
		$form = $(this).closest('form');
		$id = $form.attr("id");
		if(parseInt($('#items_'+$id).html()) > 0) {
			$form.attr('action','my_order.php?method=phone');
			$form.submit();
		} else {
			alert('There are no items in your cart!');
		}
	});
	$('.toggle_quote').click(function(e) {
		e.preventDefault();
		$bttn = $(this).attr('id');
		$qid = $bttn.split('-');
		$('#'+$qid[1]).toggle('slow', function() {
			$disp = $('#'+$qid[1]).css('display');
			if($disp != 'none') { $('#'+$bttn).html('Hide Quote'); } else { $('#'+$bttn).html('Show Quote'); }
		});
	});
});
</script>

<?php

$extend = '';
$error = array();
$halt = false;

$action = (!empty($_REQUEST['_action'])) ? $_REQUEST['_action'] : false;

switch($action)
{
	default:
	
		$vehicle_id = 0;
		
		if(!empty($_REQUEST['vehicle_id'])) {
			if(!is_int($_REQUEST['vehicle_id'])) {
				$error[] = 'Invalid Vehicle ID';
			} else {
				$vehicle_id = $db->mysql_prep($_REQUEST['vehicle_id']);
			}
		}

		$extend = (!count($error) > 0 && $vehicle_id > 0) ? ' AND `v`.`vehicle_id` = \''.$vehicle_id.'\'' : '';

		$sql = 'SELECT `v`.`vehicle_id`,`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year`,`v`.`engine_size`,`v`.`fuel_type`,`v`.`transmission`,`v`.`body_type`,
				(SELECT COUNT(*) FROM `customer_requests` AS `r` WHERE `r`.`vehicle_id` = `v`.`vehicle_id`) AS `total`,
				(SELECT COUNT(*) FROM `customer_requests` AS `c` WHERE `c`.`vehicle_id` = `v`.`vehicle_id` AND `c`.`compleated` > '.(time() + $future).') AS `compleated`,
				(SELECT COUNT(*) FROM `customer_requests` AS `o` WHERE `o`.`vehicle_id` = `v`.`vehicle_id` AND `order_group` > 0) AS `ordered`
				FROM `customer_vehicles` AS `v`
				WHERE `v`.`customer_id` = \''.$user_id.'\' '.$extend.' HAVING (`total` - `compleated`) > 0
				ORDER BY `v`.`vehicle_id` DESC';
		if($vehicles = $db->get_results($sql))
		{
			if(count($vehicles) > 1)
			{
				echo '<ul class="vehicles"><b>Jump to Vehicle:</b><br clear="all" />';
				foreach($vehicles AS $links)
				{
					echo '<li><a href="#'.$links->vehicle_id.'">'.$links->vehicle_make_name.' '.$links->vehicle_model_name.'</a></li>';
				}
				echo '</ul>';
			}

			foreach($vehicles AS $v)
			{
				if($settings['debug_mode']) {
					echo '<br/><br><b>Output Counts</b> ('.$v->vehicle_make_name.' '.$v->vehicle_model_name.')<br/>Total: '.$v->total.'<br/>Compleated: '.$v->compleated.'<br/>Ordered: '.$v->ordered.'<br/><br/>';
				}
				echo '
					<a name="'.$v->vehicle_id.'"></a>
					<div class="myquotes" id="'.$v->vehicle_id.'">
						<h1>'.pad_ref_number($v->vehicle_id).' : '.$v->vehicle_make_name.' '.$v->vehicle_model_name.'</h1>
						<h3>'.$v->registration_year.' | '.$v->engine_size.' | '.$v->fuel_type.' | '.$v->transmission.' | '.$v->body_type.'</h3>';
				//TODO	
				//if($v->ordered == 0) { echo '<a id="delete-'.$v->vehicle_id.'" class="sbb delete onblue">DELETE REQUEST</a>'; }
				
				
				//Grabs a list of all Quotation ID's 
				$pre_sql = 'SELECT `quote_id` FROM `supplier_quotes` WHERE `vehicle_id` = \''.$v->vehicle_id.'\' AND `deleted` = 0';
				$valid_ids = array();
				if($results = $db->get_results($pre_sql))
				{
					//Runs through list of ID's and returns one of each Unique ID valid for display.
					
					foreach($results AS $supply)
					{
						$pre_sql = 'SELECT `q`.`quote_id` FROM `supplier_quotes_details` AS `q` JOIN `customer_requests` AS `r` ON `r`.`request_id` = `q`.`request_id` WHERE `q`.`quote_id` = \''.$supply->quote_id.'\' AND (`r`.`order_group` = 0 OR `r`.`order_group` = `q`.`quote_id`) GROUP BY `q`.`quote_id`';
						$valid = $db->get_var($pre_sql);
						if($valid)
						{
							$valid_ids[] = $valid;
						}
					}
				}
				// Grabs detailed list of vefified quotes.
				if(count($valid_ids) > 0)
				{
					$sql = 'SELECT
						`sq`.`quote_id`,
						`sq`.`quote_time`,
						`sq`.`d_fee`,
						`sq`.`d_est`,
						`sq`.`accepted`,
						`sq`.`transaction_id`,
						`sq`.`dispatched`,
						`sq`.`deleted`,
						`su`.`strName` AS `agent`,
						`sc`.`c_name`,
						`sc`.`c_phone`,
						`sc`.`c_sales`,
						`sc`.`c_mobile`,
						`sc`.`c_fax`,
						`sc`.`c_vat`,
						`sc`.`c_info`
							FROM `supplier_quotes` AS `sq`
							JOIN `supplier_company_users` AS `su` ON `su`.`company_user_id` = `sq`.`company_user_id`
							JOIN `supplier_company` AS `sc` ON `sc`.`company_id` = `sq`.`company_id`
							WHERE `sq`.`quote_id` IN('.implode(',',$valid_ids).')';
				}
				if(count($valid_ids) > 0 && $records = $db->get_results($sql))
				{
					foreach($records AS $supplier)
					{
						$sales = (strlen($supplier->c_sales) < 10) ? $supplier->c_phone : $supplier->c_sales ;
						
						$subtotal = 0;
						$quotetotal = 0;
						$num_purch = 0;
						$method = 0;
						$output = '';
						
						//$hide = () ? : ;

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
						<div class="supplier red-stripes">&nbsp; '.$supplier->c_name.' ('.$supplier->agent.')<!--//<a id="delete-'.$supplier->quote_id.'" class="toggle_quote">Delete Quote</a>//--!></div>
						<div class="quote" id="display-'.$supplier->quote_id.'">
						<form name="buy_'.$supplier->quote_id.'" id="'.$supplier->quote_id.'" method="POST" action="" />
						<input type="hidden" name="vehicle_id" value="'.$v->vehicle_id.'" />
						<input type="hidden" name="quote_id" value="'.$supplier->quote_id.'" />
						<table width="100%" cellspacing="0" cellpadding="2" border="0">
							<tr>
								<th width="15" align="center"><span class="ui-icon ui-icon-cart"></span></th>
								<th width="*">Part</th>
								<th width="90">Guarentee</th>
								<th width="70">Price</th>
							</tr>
						';

						$quote_sql = 'SELECT `qd`.`quote_detail_id`,`qd`.`quote_id`,`qd`.`quote_price`,`qd`.`quote_guarantee`,`qd`.`quote_condition`,`qd`.`method`,`qd`.`accepted`,`qd`.`cancelled`,
										`p`.`part_name`,`p`.`request_id`,`p`.`order_group`,`p`.`order_group`
										FROM `supplier_quotes_details` AS `qd` 
										JOIN `customer_requests` AS `p` ON `p`.`request_id` = `qd`.`request_id`
										WHERE `qd`.`quote_id` = \''.$supplier->quote_id.'\' ';
						if($quotes = $db->get_results($quote_sql))
						{
							foreach($quotes AS $q)
							{
								$checkbox = '';
								$class = '';
								$quotetotal = $quotetotal + $q->quote_price;
								if($q->method > 0) {
									$method = $q->method;
									$class = ' ordered';
									$num_purch++;
									$subtotal = $subtotal + $q->quote_price;
								} else {
									$checkbox = '<input class="CheckBox" type="checkbox" name="item[]" value="'.$q->quote_detail_id.'" />';
								}
								if( $q->order_group == $q->quote_id || ($q->order_group == 0 && $num_purch == 0) ) {
									$output .= '
										<tr class="partrow'.$class.'">
											<td align="center">'.$checkbox.'</td>
											<td>'.$q->part_name.' <b>('.$q->quote_condition.')</b></td>
											<td>'.$q->quote_guarantee.'</td>
											<td>
												<input id="price_'.$q->quote_detail_id.'" type="hidden" name="price['.$q->quote_detail_id.']" value="'.$q->quote_price.'" />
												<input id="quote_id_'.$q->quote_detail_id.'" type="hidden" name="quote['.$q->quote_detail_id.']" value="'.$supplier->quote_id.'" />
												&pound;'.$q->quote_price.'
											</td>
										</tr>
									';
									if($q->cancelled > 0 && $q->order_group == 0) {
										$output .= '
										<tr>
											<td colspan="4"><font color="red"><strong>&nbsp;^ A Previous Order for this part was cancelled by the supplier.</strong></font></td>
										</tr>
										';
									}
								}
							}
						}
						if($num_purch > 0) {
							$total = $subtotal + $supplier->d_fee;
						} else {
							echo '
							<tr class="inforow">
								<td align="center"><span class="ui-icon ui-icon-triangle-1-s"></span></td>
								<td colspan="3">Add or Remove items from your shopping cart by clicking on the boxes.</td>
							</tr>
							';
							$total = $quotetotal + $supplier->d_fee;
						}

						echo $output;

						if($num_purch == 0) $total = 0;
						$total = number_format($total, 2);
						$vat = ($supplier->c_vat > 0) ? add_vat($total) : $total;
						$vattxt = ($supplier->c_vat > 0) ? ' inc VAT' : ' exc VAT';
						echo '
							<tr class="inforow">
								<td align="center" valign="middle"><span class="ui-icon ui-icon-triangle-1-e"></span></td>
								<td colspan="3" valign="middle" ><span class="ui-icon ui-icon-cart" style="display:inline-block;"></span><font style="display:inline;color:#3D9AD1;"> <b id="items_'.$supplier->quote_id.'">'.$num_purch.'</b> '.(($num_purch > 0) ? 'Item(s) on Order.' : 'Item(s) in your Cart.').'</font></td>
							</tr>
							<tr>
								<td></td>
								<td valign="top">
									<input type="hidden" id="delivery_'.$supplier->quote_id.'" name="delivery_'.$v->vehicle_id.'" value="'.$supplier->d_fee.'" />
									<input type="hidden" id="vat_'.$supplier->quote_id.'" name="vat_'.$supplier->quote_id.'" value="'.$supplier->c_vat.'" />
									'.(($dtime == false) ? 'No Devlivery estimate given' : 'We estimate delivery within '.$dtime).'
								</td>
								<td valign="top" align="right" ><b>Delivery Fee: <br/>Sub Total:</b></td>
								<td valign="top">&pound;'.$supplier->d_fee.'<br/>&pound;<font id="phone_sub_'.$supplier->quote_id.'">'.$total.'</font></td>
							</tr>
						';
						if(!$num_purch > 0) {
							/* When items are avialable for order */
							echo '
								<tr>
									<td colspan="4" style="height:1px;background-color:#02588E;"></td>
								</tr>
								<tr>
									<td colspan="4" align="center" style="padding:10px 0;">
										<b>To make enquiries about your parts please call this number:</b><br/>
										<strong class="sales-number">'.$sales.'</strong><br/>
										* Before placeing an order, it is recommended that you first ring the supplier.
									</td>
								</tr>
								<tr>
									<td colspan="4" style="height:1px;background-color:#02588E;"></td>
								</tr>
								<tr>
									<td colspan="4">
										<h2 style="text-align:left;padding-left:80px;">Purchase these parts?</h2>
										<div class="payment-options">
											<a href="" class="pay_by_phone" id="pay_by_phone_'.$supplier->quote_id.'" style="background-color:#CCCCCC;background-image:url(\''.BASE.'images/icons/phone.png\');"><font>Buy Now</font></a>
										</div>
										<br/>
										If you have already paid or are going to pay by phone then please click on the <b style="color:#3D9AD1">blue</b> buy button to confirm your order.<br/>
										<br/>
										<b>Total Amount Due:</b> &pound;<font id="phone_'.$supplier->quote_id.'">'.$vat.'</font>'.$vattxt.'
									</td>
								</tr>
								<tr>
									<td></td>
									<td colspan="3"><font color="red"><strong>Guarentee:</strong> When paying by phone you must click the Phone icon above.</font></td>
								</tr>
							';
						} else {
							/* When items have been ordered */
							if($method == 1 && $supplier->accepted > 0) {
								$order_status_msg = 'Your order is awaiting payment or payment is being processed.';
							} else {
								$order_status_msg = 'Your order is awaiting confirmation from the supplier.<br/>You may call the number below or wait to be contacted by the supplier.';
							}
							$status = array();
							$status['confirm_color']	= ($supplier->accepted > 0) ? '#045F98' : '#CCCCCC';
							$status['confirm_class']	= ($supplier->accepted > 0) ? 'tick' : 'cross';

							$status['process_color']	= ($supplier->transaction_id > 0) ? '#045F98' : '#CCCCCC';
							$status['process_class']	= ($supplier->transaction_id > 0) ? 'tick' : 'cross';

							$status['dispatch_color']	= ($supplier->dispatched > 0) ? '#045F98' : '#CCCCCC';
							$status['dispatch_class']	= ($supplier->dispatched > 0) ? 'tick' : 'cross';
//TODO
							//$status['cancelled_color']	= ($supplier->cancelled > 0) ? '#045F98' : '#CCCCCC';
							//$status['cancelled_class']	= ($supplier->cancelled > 0) ? 'tick' : 'cross';
							$status['cancelled_color']	= '#CCCCCC';
							$status['cancelled_class']	= 'cross';

							echo '
							<tr>
								<td colspan="4" style="height:1px;background-color:#02588E;"></td>
							</tr>
							<tr>
								<td colspan="4"><h2>Order Status</h2></td>
							</tr>
							<tr>
								<td colspan="4" valign="top" align="center" height="28">'.$order_status_msg.'<br/>&nbsp;</td>
							</tr>
							<tr>
								<td colspan="4">
							';
							switch($method) {
								case 1:
									echo'
									<div class="payment-options">
										<a style="background-color:#045F98;background-image:url(\''.BASE.'images/icons/phone.png\');"><font>Ordered</font></a>
									</div>
									<b>Payment Option Selected:</b> PHONE ORDER<br/>
									<strong class="sales-number">'.$sales.'</strong><br/>
									<b>Total Amount Due:</b> &pound;'.$vat.$vattxt.'
									';
								break;
							}
							echo '
								</td>
							</tr>
							<tr>
								<td colspan="4" class="order-status">
									<div style="background-color:'.$status['confirm_color'].';" class="'.$status['confirm_class'].'">Order Accepted</div>
									<div style="background-color:'.$status['process_color'].';" class="'.$status['process_class'].'">Payment Accepted</div>
									<div style="background-color:'.$status['dispatch_color'].';" class="'.$status['dispatch_class'].'">Dispatched</div>
									<div style="background-color:'.$status['cancelled_color'].';" class="'.$status['cancelled_class'].'">Cancelled</div>
								</td>
							</tr>
							';
						}
						echo '
						</table>
						</form>
						</div>';
					}
				} else {
					echo '<div class="quote"> &nbsp; You have not recieved any quotes yet.</div>';
				}
				echo '</div><br clear="all" />';
			}
		} else {
			echo 'You currently do not have any active requests.<br/>'.mysql_error();
		}
	break;

/* -- REMOVE QUOTE -- */
	case 'remove_quote':
		
	break;
}
bottom();
?>      
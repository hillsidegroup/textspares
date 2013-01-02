<?php

if(!defined('IN_TEXTSPARES')) { exit; }

include('Includes/pager.inc.php');

$uitest = false;

function sales($db)
{

	global $session, $settings;
	
	$display_sale = (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : 0;

	?>
	<div id="latest_requests" style="border:none;">
	<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?php echo ROOT; ?>Images/quotes.png">My Sales History</h1>
	<?=print_search_bar(true);?>
	<br clear="all" />
	<script type="text/javascript">
	$(document).ready(function()
	{
		function AjProcess(data){
			$start_pos = data.indexOf('<div id="result">')+17;
			$end_pos = data.indexOf("</div>",$start_pos);
			$length = $end_pos - $start_pos;
			return data.substr($start_pos,$length);	
		}
		$('#_update_private_notes').click(function(e)
		{
			e.preventDefault();
			$getform = $(this).closest('form').get(0);
			$form = "#"+$($getform).attr('id');
			$qid = $form.split("-");
			$button = $(this);
			$button.html('Saving');
			$button.attr('disabled',true);
			$url = "<?=ROOT;?>?_action=quotes&_ajax=true&_subaction=update_notes";
			$.ajaxSetup({cache: false});
			$.post(
				$url,
				{ _qid: $qid[1], _private_notes: $($('#_private_notes'),$getform).val() },
				function(data) {
					$result = AjProcess(data);
					if($result == "Success") {
						$button.attr('disabled',false);
						$button.html('Save Notes');
					} else {
						$button.attr('disabled',false);
						$button.html('Save Notes');
						return alert($result);
					}
				},
				'html'
			);
		});
		$.fn.setFocusTo = function() {}
		$(".print_invoice").click(function(e)
		{
			e.preventDefault();
			$data = "#"+$(this).attr('id');
			$qid = $data.split("-");
			window.open("<?=ROOT;?>print.php?invoice="+$qid[1],"print_invoice","width=640,height=905");
		});
		
		$(".refund_order").click(function(e)
		{
			e.preventDefault();
			$confirm = confirm('Are you sure you wish to issue the customer a refund?');
			if($confirm) {
				alert('Comming Soon...');
			}
		});
	});
	</script>
	
	<?php
	$pager = new Pager();
	//$pager->page_size = 25;
	$pager->display_rpp = true;

	$pager->list_options['Date'] = '`sq`.`quote_time` DESC';
	$pager->list_options['Make'] = '`cv`.`vehicle_make_name` ASC';
	$pager->list_options['Model'] = '`cv`.`vehicle_model_name` ASC';
	//$pager->list_options['Part'] = 'parts.part_cat_name ASC';

	$sql = 'SELECT COUNT(*) AS `Total` 
				FROM `supplier_quotes`
				WHERE `company_user_id` = \''.$session->userdata['id'].'\' AND `accepted` > 0';

	$count = $db->get_row($sql);
	$count = $count->Total;

	$get_record = ($display_sale > 0) ? ' AND `sq`.`quote_id` = \''.$display_sale.'\' ' : '';
	
	$sql = 'SELECT
			`sq`.`quote_id`,
			`sq`.`quote_time`,
			`sq`.`supplier_note`,
			`sq`.`d_fee`,
			`sq`.`d_est`,
			`sq`.`transaction_id`,
			`sq`.`dispatched`,
			`sq`.`courier`,
			`sq`.`tracking_id`,
			`ci`.`customer_id`,
			`ci`.`cust_name`,
			`ci`.`cust_phone`,
			`ci`.`post_code`,
			`cv`.`vehicle_id`,
			`cv`.`registration_year`,
			`cv`.`engine_size`,
			`cv`.`fuel_type`,
			`cv`.`transmission`,
			`cv`.`body_type`,
			`cv`.`vehicle_make_name` AS `make_name`,
			`cv`.`vehicle_model_name` AS `model`,
			`pn`.`part_name`,
			(SELECT COUNT(*) FROM `customer_requests` AS `cr` WHERE `cr`.`vehicle_id` = `sq`.`vehicle_id`) AS `num_parts`,
			(SELECT COUNT(*) FROM `supplier_quotes_details` AS `qt` WHERE `qt`.`quote_id` = `sq`.`quote_id` AND `qt`.`cancelled` = 0) AS `num_quoted`,
			(SELECT COUNT(*) FROM `supplier_quotes_details` AS `ot` WHERE `ot`.`vehicle_id` = `sq`.`vehicle_id` AND `ot`.`method` > 0 AND `ot`.`quote_id` = `sq`.`quote_id` AND `ot`.`cancelled` = 0) AS `num_ordered`,
			(SELECT SUM(`qd`.`quote_price`) FROM `supplier_quotes_details` AS `qd` WHERE `qd`.`quote_id` = `sq`.`quote_id` AND `qd`.`accepted` > 0) AS `parts_total`
		FROM `supplier_quotes` AS `sq`
		JOIN `customer_information` AS `ci` ON `ci`.`customer_id` = `sq`.`customer_id`
		JOIN `customer_vehicles` AS `cv` ON `cv`.`vehicle_id` = `sq`.`vehicle_id`
		LEFT OUTER JOIN `supplier_quotes_details` AS `ql` ON `ql`.`quote_id` = `sq`.`quote_id`
		LEFT OUTER JOIN `customer_requests` AS `pn` ON `pn`.`request_id` = `ql`.`request_id`
		WHERE `sq`.`company_user_id` = '.$session->userdata['id'].$get_record.' AND `sq`.`dispatched` > 0
		GROUP BY `sq`.`quote_id`
		ORDER BY `sq`.`modified` DESC, '.$pager->getOrder('`sq`.`quote_time` DESC').'
		LIMIT '.$pager->PageLimits().'';
	$quotes = $db->get_results($sql);
    if($quotes)
	{
		?>
        <div id="list_options">
            <p><?=$pager->OrderOptions();?></p>
        </div>
		<br clear="all" />
        <div class="request_tbl ui-corner-all">
			<table>
				<tr>
					<th width="80">Ref#</th>
					<th width="125" align="center">Date - Time</th>
					<th width="250">Make &amp; Model</th>
					<th width="300">Part</th>
					<th width="120">Your Quote</th>
					<th width="80">Status</th>
				</tr>
			</table>
		<div class="scroll-frame">
		<?php
		$i = 1;
        foreach($quotes as $quotes)
		{
			$status = 'Completed';
			$total = $quotes->parts_total + $quotes->d_fee;
		?>
			<table>
				<tr class="<?=(($quotes->transaction_id > 0) ? (($quotes->dispatched == 0) ? 'quote_orange ' : 'quote_green') : zebra($i));?> rowover" id="<?=$quotes->quote_id;?>" onclick="show_details(this.id);">
					<td width="80"><?=pad_ref_number($quotes->vehicle_id);?></td>
					<td width="125"><?=date("d/m/y - H:i",$quotes->quote_time);?></td>
					<td width="250"><?php echo $quotes->make_name.' '.$quotes->model; ?></td>
					<td width="300"><?php echo (($quotes->num_parts > 1) ? 'Quoted ' . $quotes->num_quoted .'/'. $quotes->num_parts . ' parts' : $quotes->part_name); ?></td>
					<td width="120"><b>&pound;<?php echo (($session->userdata['vat'] > 0) ? number_format(($total*($settings['vat_rate']/100))+$total,2) : number_format($total,2)); ?></b></td>
					<td width="80" id="ordered"><?=$status;?></td>
				</tr>
			</table>

			<div style="display:<?=(($display_sale == $quotes->quote_id) ? 'block' : 'none');?>" class="details" id="DT<?php echo $quotes->quote_id; ?>">
				<div class="m10 left w30">
					<div class="reg_grp ui-corner-all">
						<h2><b>Customer Information</b></h2>
						<table class="newuser_tbl">
							<tr>
								<th>Ref#:</th>
								<td><?=pad_ref_number($quotes->vehicle_id);?></td>
							</tr>
							<tr>
								<th>Quote Time#:</th>
								<td><?=date("d/m/Y - H:i",$quotes->quote_time);?></td>
							</tr>
							<tr>
								<th>Name:</th>
								<td><?php echo $quotes->cust_name; ?></td>
							</tr>
							<tr>
								<th>Phone No:</th>
								<td><?php echo $quotes->cust_phone; ?></td>
							</tr>
							<tr>
								<th>Post Code:</th>
								<td><?php echo $quotes->post_code; ?></td>
							</tr>
						</table>
					</div>

					<div class="reg_grp ui-corner-all" style="margin-top:20px;">
						<h2>Options</h2>
						<table class="newuser_tbl"><tr><td><div><button id="invoice-<?=$quotes->quote_id;?>" class="print_invoice">Print Invoice</button> &nbsp; <button id="refund-<?=$quotes->quote_id;?>" class="refund_order">Proccess Refund</button></div></td></tr></table> 
					</div>
				</div>
				
				<?php
				if($quotes->num_quoted > 0) {
					$sql = 'SELECT 
								`sqd`.`quote_detail_id`,`sqd`.`quote_price`,`sqd`.`quote_guarantee`,`sqd`.`quote_condition`,`sqd`.`method`,`pr`.`request_id`,`pr`.`request_notes`,`pr`.`part_name`
							FROM `supplier_quotes_details` AS `sqd`
							JOIN `customer_requests` AS `pr` ON `pr`.`customer_id` = \''.$quotes->customer_id.'\' AND `pr`.`request_id` = `sqd`.`request_id`
							WHERE `sqd`.`quote_id` = \''.$quotes->quote_id.'\' AND `sqd`.`accepted` > 0 AND `sqd`.`cancelled` = 0';

					$parts = $db->get_results($sql);
				}
				
				echo '
					<div style="margin-top:10px;margin-bottom:10px;" class="reg_grp ui-corner-all left w66">
					<h2>'.$quotes->make_name.' '.$quotes->model.' | '.$quotes->registration_year.' | '. $quotes->body_type.' | '.$quotes->fuel_type.' | '.$quotes->engine_size.'cc | '.$quotes->transmission.'</h2>
					<form method="post" action="?_action=quotes&_subaction=requote" id="_quoteid-'.$quotes->customer_id.'" name="_quote-'.$quotes->customer_id.'">
					<input type="hidden" name="_num_parts" value="'.$quotes->num_parts.'" />
					<table class="newuser_tbl">
					<tr>
						<th colspan="5"><b>Private Notes</b></th>
					</tr>
					<tr>
						<td colspan="5"><textarea name="_private_notes" id="_private_notes" style="width:595px;height:60px;">'.$quotes->supplier_note.'</textarea><br clear="all" /><button id="_update_private_notes">Save Notes</button></td>
					</tr>
					<tr>
						<th colspan="2">Part Name</th>
						<th>Guarantee</th>
						<th>Price</th>
					</tr>
				';
				
				if($parts)
				{
					$id = 1;
					$order_total = 0;
					foreach($parts AS $part)
					{
						$client_notes = ($part->request_notes) ? '<tr><th colspan="5">Notes for '.$part->part_name.':</th></tr><tr><td colspan="5">'.$part->request_notes.'</td></tr>' : '';
						$image = (file_exists('../_requests/images/'.$part->request_id.'.jpg')) ? '&nbsp; &nbsp;<a href="'.BASE.'_requests/images/'.$part->request_id.'.jpg" class="ui-state-default ui-corner-all lytebox" title="View attached image"><span class="ui-icon ui-icon-image"></span>Image</a>' : '';
						
						if($part->method > 0) $order_total = $order_total + $part->quote_price;

						echo '
						<tr>
							<td colspan="2" class="'.(($part->method > 0) ? 'part_ordered' : 'part').'">'.$part->part_name.' ('.$part->quote_condition.')'.$image.'</td>
							<td style="background-color:#FFF;">'.$part->quote_guarantee.'</td>
							<td align="center">
								&pound;'.$part->quote_price.'
							</td>
						<tr/>
						'.$client_notes;

						$id++;
					}
				}
				$order_total = $order_total + $quotes->d_fee;
				echo '
						<tr>
							<td colspan="2"><b>Delivery Fee:</b></td>
							<td style="text-align:right;"></td>
							<td style="background-color:#FFF;">&pound;'.$quotes->d_fee.'</td>
						</tr>
						<tr>
							<td style="text-align:right;" colspan="3"><b>Order Total:</b></td>
							<td style="background-color:#FFF;">&pound;'.number_format($total,2).'</td>
						</tr>
						<tr>
							<td style="text-align:right;" colspan="3"><b>Order Total +VAT:</b></td>
							<td style="background-color:#FFF;"><b>&pound;'.number_format(($total*($settings['vat_rate']/100))+$total,2).'</b></td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:right;padding:10px;" id="buttons-'.$quotes->quote_id.'">
							<div style="float:left;display:inline;"><b>Courier:</b> '.$quotes->courier.' &nbsp; <b>Tracking ID:</b> '.$quotes->tracking_id.'</div>
							</td>
						</tr>
					</table>
					
					<table class="newuser_tbl">
						<tr>
							<th>Messages Service</th>
						</tr>
					</table>
					</form>
				</div>
				';
				?>
				<a href="" class="close-details" onClick="show_details(<?=$quotes->quote_id;?>);">CLOSE</a>
			</div>
		<?php
		$i++;
		}
		?>
		</div>
		</div>
		<div id="pager_holder" style="padding-top:10px;">
			<?php
				echo $pager->ShowPages($count);
			?>
		</div>			
			<?php
	} else {
		?>
         <div class="ui-widget m10">
                <div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em;"> 
                	 <p><span class="ui-icon ui-icon-info" style="position:relative;float:left;margin-top:0.1em;margin-right:1em;"></span>You do not have any sales history yet.</p>
                </div>
            </div>
       <?php
	} 
	echo '</div>';
}

/////////////////////////////////////////////////////

$sub_action = (!empty($_REQUEST['_subaction'])) ? $_REQUEST['_subaction'] : false;

if(isset($_REQUEST['_update_private_notes']))
{
	$qid = $db->mysql_prep($_REQUEST['_qid']);
	if(is_numeric($qid) && $qid > 0)
	{
		$update = mysql_query('UPDATE `supplier_quotes` SET `supplier_note` = \''.$db->mysql_prep($_REQUEST['_private_notes']).'\' WHERE `quote_id` = \''.$qid.'\' AND `company_user_id` = \''.$session->userdata['id'].'\'');
		if($update)
		{
			echo '<div id="result">Success</div>';
		} else {
			echo '<div id="result">There was a problem posting your notes.</div>';
		}
	}
}
switch($sub_action)
{
	default: sales($db); break;
	
// ---------------------------
// --- PROCESS PAYMENT
// ---------------------------
	case 'order_payment':
	
		if($uitest == true) {
			echo '<div id="result">Success</div>';
		} else {
			$transaction_fail = false;
			$restore_requests = false;
			$qid = $db->mysql_prep($_REQUEST['_qid']);
			if(is_numeric($qid) && $qid > 0)
			{
				//Get parent record data.
				$sql = 'SELECT
							`q`.`d_fee`,`q`.`d_est`,
							`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,
							`c`.`customer_id`,`c`.`cust_email`,`c`.`cust_name`,`c`.`recieve_emails`,
							`s`.`c_name`,
							`u`.`strName`
						FROM `supplier_quotes` AS `q`
							JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `q`.`vehicle_id`
							JOIN `customer_information` AS `c` ON `c`.`customer_id` = `q`.`customer_id`
							JOIN `supplier_company` AS `s` ON `s`.`company_id` = `q`.`company_id`
							JOIN `supplier_company_users` AS `u` ON `u`.`company_user_id` = `q`.`company_user_id`
						WHERE `q`.`quote_id` = \''.$qid.'\' LIMIT 1';

				$result = mysql_query($sql);
				
				//If parent record data exists, get valid child orders
				if(mysql_affected_rows() > 0)
				{
					$info = mysql_fetch_object($result);
					mysql_free_result($result);
					
					$sql = 'SELECT
								`q`.`quote_detail_id`,
								`q`.`quote_price`,
								`q`.`quote_guarantee`,
								`q`.`quote_condition`,
								`p`.`part_name`
							FROM `supplier_quotes_details` AS `q`
								JOIN `customer_requests` AS `p` ON `p`.`request_id` = `q`.`request_id`
							WHERE `q`.`quote_id` = \''.$qid.'\' AND `q`.`accepted` > 0 AND `q`.`transaction_id` = 0  AND `q`.`cancelled` = 0
							ORDER BY `p`.`part_name` ASC';
					
					$result = mysql_query($sql);
					
					//If there are any valid child orders then begin prcessing ordered items only.
					if(mysql_affected_rows() > 0)
					{
						$total = 0;
						$pids = array();
						$partlist = '';
						//Get and calculate list of orders.
						while($parts = mysql_fetch_object($result))
						{
							$total = $total + $parts->quote_price;
							$pids[] = $parts->quote_detail_id;
							$partlist .= $parts->part_name.'('.$parts->quote_condition.')<br><strong>&pound;'.$parts->quote_price.'</strong> '.$parts->quote_guarantee.'<br/>&nbsp;<br/>';
						}
						mysql_free_result($result);
						
						$total = $total + $info->d_fee;
						$total = (($session->userdata['vat'] > 0) ? number_format(($total*($settings['vat_rate']/100))+$total,2) : number_format($total,2));

						$vat = ($session->userdata['vat'] > 0) ? $settings['vat_rate'] : 0.00;
						
						//Create a transaction record for the order.
						$sql = 'INSERT INTO `customer_transactions`(`company_user_id`,`quotation_id`,`company_id`,`company_user_id`,`payment_method`,`amount`,`vat_rate`) VALUES(\''.$info->customer_id.'\',\''.$qid.'\',\''.$session->userdata['companyid'].'\',\''.$session->userdata['id'].'\',\'PHONE_ORDER\',\''.$total.'\',\''.$vat.'\')';
						
						if(mysql_query($sql))
						{
							$transaction_id = mysql_insert_id();
							if($transaction_id > 0)
							{
								//BEGIN TRANSACTION SYNC
								//If a valid transaction record is created then sync all relative records.
								$sql = 'UPDATE `supplier_quotes_details` SET `transaction_id` = \''.$transaction_id.'\' WHERE `quote_detail_id` IN ('.implode(',',$pids).')';
								if(!mysql_query($sql)) {
									echo 'ERROR: UPDATE Failed!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
									$transaction_fail = true;
								} else {
									if(mysql_affected_rows() > 0)
									{
										$sql = 'UPDATE `customer_requests` AS `c`,`supplier_quotes` AS `s` SET `c`.`transaction_id` = \''.$transaction_id.'\',`s`.`transaction_id` = \''.$transaction_id.'\' WHERE `c`.`order_group` = \''.$qid.'\' AND `s`.`quote_id` = \''.$qid.'\'';
										if(mysql_query($sql)) {
											echo '<div id="result">Success</div>';

											$message = '
												<h1>Payment Processed</h1>
												<p><b>'.$info->strName.'</b> from <b>'.$info->c_name.'</b> has processed the payment for your order with them for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'</p>
												<p><ul>'.$partlist.'<br/><br/><b>Delivery Fee:</b> &pound;'.$info->d_fee.'<br/><br/><b>Total:</b> &pound;'.$total.'</ul></p>
												<p>You can login and contact the supplier if there are any problems: <a href="'.BASE.'">'.BASE.'</a>.</p>
												<p>Your order is now awaiting delivery dispatch. You will recieve a notification as soon as your order has been dispatched.</p>
												<p>Thank you.</p>
											';
											
											if($info->recieve_emails > 0 && !$message == false)
											{
												$send = send_email($info->cust_email,'noreply@textspares.co.uk','TextSpares - Payment Accepted',$message);
												if(!$send == true) echo $send;
											}
										} else {
											echo '<div id="result">There was a problem setting the status to "Payment Processed".</div>';
											echo 'ERROR: UPDATE Failed!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
											$transaction_fail = true;
											$restore_requests = true;
										}
									}
								}
								//END TRANSACTION SYNC
							} else {
								echo 'ERROR: Invalid Transaction ID, Payment Process Cancelled by System';
							}
						} else {
							echo 'ERROR: Create Record Faield!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
						}
					} else {
						echo 'ERROR: No records found!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
						
						//RESTORE RECORDS ON TRANSACTION FAIL
						if($transaction_fail == true && $transaction_id > 0)
						{
							mysql_query('DELETE FROM `customer_transactions` WHERE `transaction_id` = \''.$transaction_id.'\' LIMIT 1');
							mysql_query('UPDATE `supplier_quotes_details` SET `transaction_id` = \'0\' WHERE `quote_detail_id` IN ('.implode(',',$pids).')');
							if($restore_requests == true)
							{
								mysql_query('UPDATE `customer_requests` AS `c`,`supplier_quotes` AS `s` SET `c`.`transaction_id` = \'0\' ,`s`.`transaction_id` = \'0\' WHERE `c`.`order_group` = \''.$qid.'\' AND `s`.`quote_id` = \''.$qid.'\'');
							}
						}
					}
				} else {
					echo 'ERROR: No records found!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
				}
			} else {
				echo 'ERROR: Invalid Quotation ID sent to server.';
			}
		}
	break;
	
// ---------------------------
// --- PROCESS ORDER DISPATCH
// ---------------------------
	case 'order_dispatch':
	
		if($uitest == true) {
			echo '<div id="result">Success</div>';
		} else {
			$qid = $db->mysql_prep($_REQUEST['_qid']);
			if(is_numeric($qid) && $qid > 0)
			{
				//Get parent record data.
				$sql = 'SELECT
							`q`.`d_est`,
							`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,
							`c`.`customer_id`,`c`.`cust_email`,`c`.`cust_name`,`c`.`recieve_emails`,
							`s`.`c_name`,
							`u`.`strName`
						FROM `supplier_quotes` AS `q`
							JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `q`.`vehicle_id`
							JOIN `customer_information` AS `c` ON `c`.`customer_id` = `q`.`customer_id`
							JOIN `supplier_company` AS `s` ON `s`.`company_id` = `q`.`company_id`
							JOIN `supplier_company_users` AS `u` ON `u`.`company_user_id` = `q`.`company_user_id`
						WHERE `q`.`quote_id` = \''.$qid.'\' LIMIT 1';

				$result = mysql_query($sql);
				
				//If parent record data exists, get valid child orders
				if(mysql_affected_rows() > 0)
				{
					$info = mysql_fetch_object($result);
					mysql_free_result($result);
					$sql = 'UPDATE `supplier_quotes` SET `dispatched` = \''.time().'\', `courier` = \''.$db->mysql_prep($_REQUEST['_courier']).'\', `tracking_id` = \''.$db->mysql_prep($_REQUEST['_parcelid']).'\' WHERE `quote_id` = \''.$qid.'\'';
					if(mysql_query($sql)) {
						echo '<div id="result">Success</div>';
						$message = '
							<h1>Order Dispatched</h1>
							<p><b>'.$info->strName.'<b/> from <b>'.$info->c_name.'</b> has dispatched your parts for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'
							'.((strlen($_REQUEST['_courier']) > 1) ? '<br/><b>Courier:</b> '.$_REQUEST['_courier'] : '').((strlen($_REQUEST['_parcelid']) > 1) ? '<br/><b>Tracking ID:</b> '.$_REQUEST['_parcelid'] : '').'</p>
							<p>You can login and contact the supplier if there are any problems: <a href="'.BASE.'">'.BASE.'</a>.</p>
							<p>Your order has now been dispatched.</p>
							<p>Thank you.</p>
						';
						
						if($info->recieve_emails > 0 && !$message == false)
						{
							$send = send_email($info->cust_email,'noreply@textspares.co.uk','TextSpares - Order Dispatched',$message);
							if(!$send == true) echo $send;
						}
					} else {
						echo '<div id="result">There was a problem setting the status to "Order Dispatched".</div>';
						echo 'ERROR: UPDATE Failed!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
					}
				} else {
					echo 'ERROR: No records found!<br><i><b>'.mysql_error().'</b><br/>'.$sql.'</i><br clear="all" />';
				}
			} else {
				echo 'ERROR: Invalid Quotation ID sent to server.';
			}
		}
	
	break;
	
// ---------------------------
// --- PROCESS CANCEL ORDER
// ---------------------------
	case 'cancel_order':
	
		if($uitest == true)
		{
			echo '<div id="result">Success</div>';
		} else {
			$qid = $db->mysql_prep($_REQUEST['_qid']);
			if(is_numeric($qid) && $qid > 0)
			{
				$sql = 'UPDATE `supplier_quotes_details` AS `d`,`supplier_quotes` AS `s` SET `s`.`cancelled` = \''.time().'\', `d`.`method` = 0, `d`.`accepted` = 0, `s`.`accepted` = 0 WHERE `d`.`quote_id` = \''.$qid.'\' AND `d`.`company_user_id` = \''.$session->userdata['id'].'\' AND `d`.`method` > 0 AND `s`.`quote_id` = \''.$qid.'\' AND `s`.`company_user_id` = \''.$session->userdata['id'].'\'';
				if(!$result = mysql_query($sql)) {
					echo mysql_error();
				} else {
					if(mysql_affected_rows() > 0)
					{
						$sql = 'UPDATE `customer_requests` SET `order_group` = 0 WHERE `order_group` = \''.$qid.'\'';
						if(mysql_query($sql)) {
							echo '<div id="result">Success</div>';
							
							$sql = 'SELECT 
										`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,
										`c`.`cust_email`,`c`.`cust_name`,`c`.`recieve_emails`,
										`s`.`c_name`,
										`u`.`strName`
									FROM `supplier_quotes` AS `q`
										JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `q`.`vehicle_id`
										JOIN `customer_information` AS `c` ON `c`.`customer_id` = `q`.`customer_id`
										JOIN `supplier_company` AS `s` ON `s`.`company_id` = `q`.`company_id`
										JOIN `supplier_company_users` AS `u` ON `u`.`company_user_id` = `q`.`company_user_id`
									WHERE `q`.`quote_id` = \''.$qid.'\' LIMIT 1';
									
							$result = mysql_query($sql);
							if(mysql_affected_rows() > 0) {
								$info = mysql_fetch_object($result);
								mysql_free_result($result);
								$message = '
									<h1>Order Cancelled</h1>
									<p><b>'.$info->strName.'<b/> from <b>'.$info->c_name.'</b> has cancelled your order with them for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'</p>
									<p>You can login and contact the supplier or order the parts from another supplier: <a href="'.BASE.'">'.BASE.'</a>.</p>
									<p>Thank you.</p>
								';
							} else {
								echo mysql_error();
								$message = false;
							}
							
							if($info->recieve_emails > 0 && !$message == false)
							{
								$send = send_email($info->cust_email,'noreply@textspares.co.uk','TextSpares - Order Cancelled',$message);
								if(!$send == true) echo $send;
							}
						} else {
							echo '<div id="result">There was a problem cancelling the order.</div>';
						}
					}
				}
			}
		}
	break;
}

?>
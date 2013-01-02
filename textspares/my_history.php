<?php
define('IN_TEXTSPARES',true);
include('include/common.inc.php');

if(!$session->loggedin) header('location:customer_login.php');

top();
common_middle();

$future = 604800;
//$future = 0;
$user_id = $session->userdata['id'];
//$user_id = 1;

?>

<h1>Your Order History.</h1>

<script type="text/javascript">
$(document).ready(function() {
});
</script>

<?php

$extend = '';
$error = array();
$halt = false;

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
		(SELECT COUNT(*) FROM `supplier_quotes_details` AS `d` JOIN `supplier_quotes` AS `s` ON `s`.`quote_id` = `d`.`quote_id` WHERE `d`.`vehicle_id` = `v`.`vehicle_id` AND `d`.`transaction_id` > 0 AND `s`.`dispatched` > 0 AND `s`.`dispatched` > 0) AS `compleated`
		FROM `customer_vehicles` AS `v`
		WHERE `v`.`customer_id` = '.$user_id.$extend.' HAVING `compleated` > 0
		ORDER BY `v`.`vehicle_id` DESC';
if($vehicles = $db->get_results($sql))
{
	if(count($vehicles) > 3)
	{
		echo '<ul><b>Jump to Vehicle:</b>';
		foreach($vehicles AS $links) {
			echo '<li><a href="#'.$links->vehicle_id.'">'.$links->vehicle_make_name.' '.$links->vehicle_model_name.'</a></li>';
		}
		echo '</ul>';
	}

	foreach($vehicles AS $v)
	{
		echo '
			<a name="'.$v->vehicle_id.'"></a>
			<div class="myquotes" id="'.$v->vehicle_id.'">
				<h1>'.pad_ref_number($v->vehicle_id).' : '.$v->vehicle_make_name.' '.$v->vehicle_model_name.'</h1>
				<h3>'.$v->registration_year.' | '.$v->engine_size.' | '.$v->fuel_type.' | '.$v->transmission.' | '.$v->body_type.'</h3>';

		$sup_sql = 'SELECT
			`sq`.`quote_id`,
			`sq`.`quote_time`,
			`sq`.`d_fee`,
			`sq`.`d_est`,
			`sq`.`accepted`,
			`sq`.`transaction_id`,
			`sq`.`dispatched`,
			`su`.`strName` AS `agent`,
			`sc`.`c_name`,
			`sc`.`c_vat`,
			`sc`.`c_info`
				FROM `supplier_quotes` AS `sq`
				JOIN `supplier_company_users` AS `su` ON `su`.`company_user_id` = `sq`.`company_user_id`
				JOIN `supplier_company` AS `sc` ON `sc`.`company_id` = `sq`.`company_id`
				WHERE `sq`.`customer_id` = \''.$user_id.'\' AND `sq`.`vehicle_id` = \''.$v->vehicle_id.'\' AND `sq`.`transaction_id` > 0';

		if($suppliers = $db->get_results($sup_sql))
		{
			foreach($suppliers AS $supplier)
			{
				$total = 0;
				$output = '';

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
				<div class="supplier">&nbsp; '.$supplier->c_name.' ('.$supplier->agent.')</div>
				<div class="quote">
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
								WHERE `qd`.`quote_id` = \''.$supplier->quote_id.'\' AND `qd`.`transaction_id` > 0';
				if($quotes = $db->get_results($quote_sql))
				{
					foreach($quotes AS $q)
					{
						$total = $total + $q->quote_price;
						$output .= '
							<tr class="partrow">
								<td align="center"></td>
								<td>'.$q->part_name.' <b>('.$q->quote_condition.')</b></td>
								<td>'.$q->quote_guarantee.'</td>
								<td>
									<input id="price_'.$q->quote_detail_id.'" type="hidden" name="price['.$q->quote_detail_id.']" value="'.$q->quote_price.'" />
									<input id="quote_id_'.$q->quote_detail_id.'" type="hidden" name="quote['.$q->quote_detail_id.']" value="'.$supplier->quote_id.'" />
									&pound;'.$q->quote_price.'
								</td>
							</tr>
						';
					}
				}
				$total = $total + $supplier->d_fee;

				echo $output;

				$total = number_format($total, 2);
				$vat = add_vat($total);
				echo '
					<tr>
						<td></td>
						<td></td>
						<td valign="top" align="right" ><b>Delivery Fee: <br/>Total:<br/>+VAT:</b></td>
						<td valign="top">&pound;'.$supplier->d_fee.'<br/>&pound;'.$total.'<br/>&pound;'.$vat.'</td>
					</tr>
				</table>
			</form>
			</div>';
			}
		} else {
			echo '<div class="quote"> &nbsp; You have not recieved any quotes yet.</div>';
		}
		echo '</div><br clear="all" />';

	}
}
if(!count($vehicles) > 0) {
	echo 'You currently do not have any compleated orders.';
}
bottom();
?>      
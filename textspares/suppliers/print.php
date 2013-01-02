<?php
define('IN_TEXTSPARES',true);
include('Includes/config/php_enviroment.php');
/*
1 	Super Administrator	Backend
2 	Administrator		Backend
3 	Publisher			Backend
4 	Editor 				Backend
5 	All Backend 		Backend
6 	Supplier 			Frontend
7 	Registered User 	Frontend
*/

$page = $_REQUEST['_action'];

include_once('Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

include_once('Includes/function_global.php');
include_once('Includes/function_supplier_sessions.php');

$settings = get_settings();
$session = new session();
$authed = $session->check();

if($session->access(6) == true) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Print Quote</title>
<style type="text/css" media="all">
body{margin:0;padding:0;background-color:#ffffff;color:#3d3d3d;}
.bg{width:620px;height:877px;position:absolute;top:0;left:0;z-index:0;}
.invoice{width:620px;height:877px;z-index:1;}
.invoice div{position:absolute;}
.company{top:95px;right:20px;font-size:18px;font-weight:bold;}
.to{top:140px;left:330px;font-size:11px;}
.from{top:140px;left:20px;font-size:11px;}
.items{top:335px;left:20px;width:580px;height:350px;}
.row{position:absolute;left:0px;padding:0;margin:0;width:580px;height:20px;border-bottom:1px dashed #CCCCCC;}
.item{position:absolute;left:0;top:4px;width:400px;height:18px;font-size:12px;}
.price{position:absolute;left:470px;top:4px;width:100px;height:18px;font-size:12px;}
.date{top:700px;left:20px;}
.total{top:700px;left:290px;}
.vat{top:700px;left:495px;}
.message{top:740px;left:20px;font-size:10px;}
</style>
<script type="text/javascript" src="<?=ROOT;?>Scripts/jquery-1.6.3.min.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function()
{
	setTimeout("window.print()",3500);
});
</script>
</head>
<body>
<div class="invoice">
<?php
$invoice = $db->mysql_prep(trim($_REQUEST['invoice']));
if(is_numeric($invoice) && $invoice > 0) {
	$sql = 'SELECT 
				`q`.`d_fee`,`q`.`vehicle_id`,
				`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year`,`v`.`engine_size`,`v`.`fuel_type`,`v`.`transmission`,`v`.`body_type`,
				`c`.`addr1`,`c`.`addr2`,`c`.`city`,`c`.`county`,`c`.`country`,`c`.`post_code`,`c`.`cust_name`,`c`.`cust_phone`,
				`s`.`c_name`,`s`.`c_addr1`,`s`.`c_addr2`,`s`.`c_city`,`s`.`c_county`,`s`.`c_postcode`,`s`.`c_country`,`s`.`c_phone`,`s`.`c_sales`,
				`u`.`strName`
			FROM `supplier_quotes` AS `q`
				JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `q`.`vehicle_id`
				JOIN `customer_information` AS `c` ON `c`.`customer_id` = `q`.`customer_id`
				JOIN `supplier_company` AS `s` ON `s`.`company_id` = `q`.`company_id`
				JOIN `supplier_company_users` AS `u` ON `u`.`company_user_id` = `q`.`company_user_id`
			WHERE `q`.`quote_id` = \''.$invoice.'\' AND `q`.`company_id` = \''.$session->userdata['companyid'].'\' LIMIT 1';
			
	$result = mysql_query($sql);
	if(mysql_affected_rows() > 0)
	{
		$info = mysql_fetch_object($result);
		mysql_free_result($result);
echo '
<div class="bg"><img src="'.ROOT.'images/invoice.gif" width="620" height="877" /></div>
<div class="company">'.$info->c_name.' (Refs: '.$invoice.'/'.pad_ref_number($info->vehicle_id).')</div>
<div class="to"><p><b>'.$info->c_name.'</b></p><p>'.$info->c_addr1.((strlen(trim($info->c_addr2)) > 2) ? '<br/>'.$info->c_addr2 : '').'<br/>'.$info->c_city.'<br/>'.$info->c_county.'</p><p>'.$info->c_postcode.'<br/>'.$info->c_country.'<br/><br/>'.$info->c_phone.'</p></div>
<div class="from"><p><b>'.$info->cust_name.'</b></p><p>'.$info->addr1.((strlen(trim($info->addr2)) > 2) ? '<br/>'.$info->addr2 : '').'<br/>'.$info->city.'<br/>'.$info->county.'</p><p>'.$info->post_code.'<br/>'.$info->country.'<br/><br/>'.((strlen($info->c_sales) > 5) ? $info->c_sales : $info->cust_phone).'</p></div>
<div class="items">';
		$sql = 'SELECT
					`q`.`quote_detail_id`,
					`q`.`quote_price`,
					`q`.`quote_guarantee`,
					`q`.`quote_condition`,
					`p`.`part_name`
				FROM `supplier_quotes_details` AS `q`
					JOIN `customer_requests` AS `p` ON `p`.`request_id` = `q`.`request_id`
				WHERE `q`.`quote_id` = \''.$invoice.'\' AND `q`.`accepted` > 0 AND `q`.`transaction_id` > 0 AND `q`.`cancelled` = 0
				ORDER BY `p`.`part_name` ASC';
		
		$result = mysql_query($sql);
		
		//If there are any valid child orders then begin prcessing ordered items only.
		if(mysql_affected_rows() > 0)
		{
			$total = 0;
			$shift = 4;
			$partlist = '<div class="row" style="top:-24px;border-bottom-style:solid;"><div class="item" style="font-weight:bold;font-size:14px;">'.$info->vehicle_make_name.' '.$info->vehicle_model_name.', '.$info->registration_year.', '.$info->engine_size.'cc, '.$info->fuel_type.', '.$info->transmission.', '.$info->body_type.'</div></div><br clear="all" />';
			//Get and calculate list of orders.
			while($parts = mysql_fetch_object($result))
			{
				$total = $total + $parts->quote_price;
				$partlist .= '<div class="row" style="top:'.$shift.'px;"><div class="item">'.$parts->part_name.' ('.$parts->quote_condition.')</div><div class="price"><b>&pound;'.$parts->quote_price.'</b></div></div><br clear="all" />';
				$shift = $shift + 28;
			}
			mysql_free_result($result);
			
			$partlist .= '<div class="row" style="top:'.$shift.'px;"><div class="item">Delivery Fee</div><div class="price">&pound;'.$info->d_fee.'</div></div><br clear="all" />';
			
			$total = $total + $info->d_fee;
			$total = number_format($total,2);
			$vat = number_format(($total*($settings['vat_rate']/100))+$total,2);
		}
echo $partlist.'
</div>
<div class="date">'.date('jS F Y').'</div>
<div class="total">'.$total.'</div>
<div class="vat">'.(($session->userdata['vat'] > 0) ? $vat . ' %' . $settings['vat_rate'] : 'N/A').'</div>
<div class="message"></div>
';
	}
}
?>
</div>
</body>
</html>
<?php
}
?>
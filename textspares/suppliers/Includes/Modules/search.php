<?php

if(!defined('IN_TEXTSPARES')) { exit; }

include('Includes/pager.inc.php');

$searchmsg = '';
$results = false;

if(isset($_REQUEST['search']))
{
	switch ($_REQUEST['search_by'])
	{
		case 'ref':
			$vehicle_id = preg_replace('/[^0-9]/','',urldecode($_REQUEST['search']));
			$filters = ' AND `r`.`vehicle_id` = \''.$db->mysql_prep($vehicle_id).'\'';
			$searchmsg = 'Results for refrence number: '.pad_ref_number(urldecode($_REQUEST['search']));
			break;

		case 'name':
			$name = $db->mysql_prep(urldecode($_REQUEST['search']));
			$like = implode('%',explode(' ',$name));
			$filters = ' AND `c`.`cust_name` LIKE \'%'.$like.'%\'';
			$searchmsg = 'Results for '. urldecode($_REQUEST['search']);
			break;

		case 'pcode':
			$name = $db->mysql_prep(urldecode($_REQUEST['search']));
			$like = implode('%',explode(' ',$name));
			$filters = ' AND `c`.`post_code` LIKE \'%'.$like.'%\'';
			$searchmsg = 'Results for post code: '. urldecode($_REQUEST['search']);
			break;
	}
	
	$sql = 'SELECT `r`.`request_id`,`r`.`vehicle_id`,`r`.`part_name`,`r`.`order_group`,`r`.`transaction_id`,
			`c`.`cust_name`,
			`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,
			`s`.`quote_id` AS `in_quotes`,`s`.`accepted` AS `in_sales`,
			(SELECT COUNT(*) FROM `supplier_quotes` AS `h` WHERE `h`.`quote_id` = `r`.`order_group` AND `h`.`dispatched` > 0) AS `in_history`
			FROM `customer_requests` AS `r`
			JOIN `customer_information` AS `c` ON `c`.`customer_id` = `r`.`customer_id`
			JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `r`.`vehicle_id`
			LEFT JOIN `supplier_quotes_details` AS `s` ON `s`.`request_id` = `r`.`request_id` AND `s`.`company_id` = \''.$session->userdata['companyid'].'\'
			WHERE (`r`.`order_group` = 0 OR `r`.`order_group` = `s`.`quote_id`) '.$filters;

	if(!$results = $db->get_results($sql)) $searchmsg = 'found no matching records.';
}
?>

	<div id="latest_requests" style="border:none;">
	<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?php echo ROOT; ?>Images/quotes.png">Search <?=$searchmsg;?></h1>
	
	<script type="text/javascript">
	$(document).ready(function()
	{
		$.fn.setFocusTo = function() {}
	});
	</script>
	<?=print_search_bar(true);?>
	<br clear="all" />
	
	<div class="request_tbl ui-corner-all">	
		<table>
			<tr>
				<th width="90">Ref#</th>
				<th width="150">Client Name</th>
				<th width="240">Make &amp; Model</th>
				<th width="*">Parts</th>
				<th width="70" style="text-align:center;">Quote</th>
			</tr>
		</table>
		<div class="scroll-frame">
		<?php
		if($results != false)
		{
			foreach($results AS $s)
			{
				if($settings['debug_mode'])
				{
					echo 'In Quotes: '.$s->in_quotes .' | In Sales: '.$s->in_sales.' | In History: '.$s->in_history;
				}
				if($s->in_quotes > 0 && !$s->in_sales > 0)
				{
					$location = 'quotes';
					$id = $s->in_quotes;
					$class = 'quote_blue';
				}
				elseif($s->in_sales > 0 && $s->in_history == 0)
				{
					$location = 'sales';
					$id = $s->in_quotes;
					$class = 'quote_orange';
				}
				elseif($s->in_history > 0)
				{
					$location = 'history';
					$id = $s->in_quotes;
					$class = 'quote_green';
				}
				else
				{
					$location = 'requests';
					$id = $s->vehicle_id;
					$class = '';
				}
				
				echo '
				<table>
					<tr class="rowover '.$class.'" onclick="document.location = \'?_action='.$location.'&id='.$id.'\'">
						<td width="90">'.pad_ref_number($s->vehicle_id).'</td>
						<td width="150">'.$s->cust_name.'</td>
						<td width="240">'.$s->vehicle_make_name.' '.$s->vehicle_model_name.'</td>
						<td width="*">'.$s->part_name.'</td>
						<td width="70" style="text-align:center;"><a class="ui-state-default ui-corner-all" title="View/Quote this request now"><span class="ui-icon ui-icon-comment"></span>View</a></td>
					</tr>
				</table>';
			}
		} else {
			echo '
			<table>
				<tr>
					<td>No Results to Display...</td>
				</tr>
			</table>';
		}
		?>
		</div>
	</div>
	
	</div>
<?php

if(!defined('IN_TEXTSPARES')) { exit; }

include('Includes/pager.inc.php');
include_once ('Includes/validation.inc.php');
$vdata = new Validator();
function quote_list($db)
{
	global $session, $settings;

	$display_quote = (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : 0;
?>
	<div id="latest_requests" style="border:none;">
	<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?=ROOT;?>Images/quotes.png">My Quotes &amp; Orders</h1>
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
		$wait = "<span class=\"_wait\"><span class=\"ui-icon ui-icon-clock\"></span>Please Wait...</span>";
	
		$.fn.setFocusTo = function(id) {
			$('#price-1-'+id).focus();
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
		$(".take_order").click(function(e)
		{
			e.preventDefault();
			$getform = $(this).closest('form').get(0);
			$form = "#"+$($getform).attr('id');
			$qid = $form.split("-");
			$client = $("input[name='_cid-"+$qid[1]+"']").val();
			$('#buttons-'+$qid[1]+'-right').hide();
			$('#controls-'+$qid[1]).children("div").hide();
			$('#controls-'+$qid[1]).append($wait);
			$items = 0;
			$idset = new Array();
			$("input[name='basket-"+$qid[1]+"\\[\\]']:checked").each(function(index,object) {
				$idset[$items] = $(object).val();
				$items++;
			});
			if($items == 0) {
				alert('Please select atleast one item to order.');
				$('#controls-'+$qid[1]).children("div").show();
				$('._wait').remove();
			} else {
				if($client > 0) {
					$pass = confirm('Place customers order?\n\nYou should only use this if you have permission from\nthe customer to proccess an order for your quote on their behalf.');
					if($pass)
					{
						$.getJSON("<?=ROOT;?>?json=true&feed=address&client="+$client, function(data)
						{
							if(data[0] == 'error') {
								alert(data[1] + "\n\n" + data[2]);
								$parent.children("a").show();
								$('#buttons-'+$qid[1]+'-right').show();
								$('._wait').remove();
							} else {
								$('.get_address').show();
								$('.canvas').css('z-index','1000');
								$id = $('#target_quote').val($qid[1]);
								$('#cust_name').val(data.cust_name);
								$('#cust_phone').val(data.cust_phone);
								$('#addr1').val(data.addr1);
								$('#addr2').val(data.addr2);
								$('#city').val(data.city);
								$('#county').val(data.county);
								$('#post_code').val(data.post_code);
								$('#_basket').val($idset);
							}
						});
					} else {
						$parent.children("a").show();
						$('#buttons-'+$qid[1]+'-right').show();
						$('._wait').remove();
					}
				} else {
					alert('Internal Error: No Client ID!');
				}
			}
		});
		
		$(".submit_order").click(function(e)
		{
			e.preventDefault();
			$id = $('#target_quote').val();
			
			$url = "<?=ROOT;?>?_action=quotes&json=true&_subaction=submit_order";
			$.ajaxSetup({cache: false});
			$.post(
				$url,
				$('#address_form').serialize(),
				function(data) {
					if(data[0] == 'error') {
						alert(data[1]);
					} else {
						$('.get_address').hide();
						$('#DT'+$id).hide();
						$('.canvas').css('z-index','999').hide();
						$('#'+$id).removeClass('quote_blue zebra1 zebra2').addClass('quote_green').attr("onClick","document.location = '?_action=sales&id="+$id+"'").children('#ordered').text('Ordered');
					}
				},
				'json'
			);
		});
		
		$(".submit_order_cancel").click(function(e)
		{
			e.preventDefault();
			$target = $('#target_quote').val();
			$('.get_address').hide();
			$('.canvas').css('z-index','999');
			$('._wait').remove();
			$('#controls-'+$target).children("div").show();
		});
		
		$(".accept_order").click(function(e)
		{
			e.preventDefault();
			$data = "#"+$(this).attr('id');
			$qid = $data.split("-");
			$p = confirm('Are you sure you wish to accept this order and request payment from client?');
			if($p)
			{
				$url = "<?=ROOT;?>?_action=quotes&_ajax=true&_subaction=accept_order";
				$.ajaxSetup({cache: false});
				$.get(
					$url,
					{ _qid: $qid[1] },
					function(data) {
						$ele = '<div id="'+'result">';
						$start_ele = data.indexOf($ele);
						$start_pos = $start_ele+17;
						$end_pos = data.indexOf("</div>",$start_pos);
						$length = $end_pos - $start_pos;
						$result = data.substr($start_pos,$length);
						if($result == "Success") {
							$('#'+$qid[1]).removeClass('quote_blue zebra1 zebra2').addClass('quote_green').attr("onClick","document.location = '?_action=sales&id="+$qid[1]+"'").children('#ordered').text('Accepted');
							$('#DT'+$qid[1]).hide();
							$('.canvas').hide();
						} else {
							return alert($result);
						}
					},
					'html'
				);
			}
		});

		$(".cancel_order").click(function(e)
		{
			e.preventDefault();
			
			$data = "#"+$(this).attr('id');
			$qid = $data.split("-");

			$('#controls-'+$qid[1]).children("div").hide();
			$('#controls-'+$qid[1]).append($wait);
			
			$p = confirm('Are you sure you want to cancel the clients order?');
			if($p)
			{
				$url = "<?php echo ROOT; ?>?_action=quotes&_ajax=true&_subaction=cancel_order";
				$.ajaxSetup({cache: false});
				$.get(
					$url,
					{ _qid: $qid[1] },
					function(data) {
						$ele = '<div id="'+'result">';
						$start_ele = data.indexOf($ele);
						$start_pos = $start_ele+17;
						$end_pos = data.indexOf("</div>",$start_pos);
						$length = $end_pos - $start_pos;
						$result = data.substr($start_pos,$length);
						if($result == "Success") {
							$('#'+$qid[1]).removeClass('quote_blue zebra1 zebra2').addClass('quote_red').children('#ordered').text('Cancelled');
							$('#controls-'+$qid[1]).children("div").show();
							$('._wait').remove();
							$('#stage-left-one-'+$qid[1]).show();
							$('#stage-right-one-'+$qid[1]).show();
							$('#stage-left-two-'+$qid[1]).hide();
						} else {
							$('._wait').remove();
							$('#controls-'+$qid[1]).children("div").show();
							return alert($result);
						}
					},
					'html'
				);
			} else {
				$('._wait').remove();
				$('#controls-'+$qid[1]).children("div").show();
			}
		});
		$(".alter_quote").click(function(e)
		{
			e.preventDefault();
			$link_id = "#"+$(this).attr('id');
			$eform = $($link_id).closest('form').get(0);
			$form = "#"+$($eform).attr('id');
			$qid = $link_id.split("-");
			$('#controls-'+$qid[1]).children("div").hide();
			$('#controls-'+$qid[1]).append($wait);
			$p = confirm('Are you sure you want to alter this quote?');
			if($p)
			{
				$url = "<?=ROOT;?>?_action=quotes&_ajax=true&_subaction=alter_quote";
				$.ajaxSetup({cache: false});
				$.post(
					$url,
					$($form).serialize(),
					function(data) {
						$ele = '<div id="'+'result">';
						$start_ele = data.indexOf($ele);
						$start_pos = $start_ele+17;
						$end_pos = data.indexOf("</div>",$start_pos);
						$length = $end_pos - $start_pos;
						$result = data.substr($start_pos,$length);
						if($result == "Success") {
							$('#'+$qid[1]).removeClass('quote_blue zebra1 zebra2').children('#ordered').text('Updated');
							$total = 0;
							for(i=1;i<=$('#_num_parts-'+$qid[1]).val();i++)
							{
								$total = $total + parseFloat($('#price-'+i+'-'+$qid[1]).val());
							}
							$total = $total + parseFloat($('#delivery-'+$qid[1]).val());
							$vat = $total + ((($total)/100) * <?=$settings['vat_rate'];?>);
							$('#quote-total-'+$qid[1]).html('&pound;'+$total.toFixed(2));
							$('#quote-vat-'+$qid[1]).html('&pound;'+$vat.toFixed(2));
							$('#quote-row-price-'+$qid[1]).html('<b>&pound;'+<?=(($session->userdata['vat'] > 0) ? '$vat' : '$total');?>.toFixed(2)+'</b>');
							alret('Prices Ameneded');
						} else {
							return alert($result);
						}
					},
					'html'
				);
			}
			$('._wait').remove();
			$('#controls-'+$qid[1]).children("div").show();
		});
		$(".delete_quote").click(function(e)
		{
			e.preventDefault();
			$link_id = "#"+$(this).attr('id');
			$qid = $link_id.split("-");
			$('#controls-'+$qid[1]).children("div").hide();
			$('#controls-'+$qid[1]).append($wait);
			
			$p = confirm('Are you sure you want to delete this quote?');
			if($p) {
				$url = "<?=ROOT;?>?_action=quotes&_ajax=true&_subaction=delete_quote";
				$.ajaxSetup({cache: false});
				$.get(
					$url,
					{ _qid: $qid[1] },
					function(data) {
						$ele = '<div id="'+'result">';
						$start_ele = data.indexOf($ele);
						$start_pos = $start_ele+17;
						$end_pos = data.indexOf("</div>",$start_pos);
						$length = $end_pos - $start_pos;
						$result = data.substr($start_pos,$length);
						if($result == "Success") {
							$($link_id).parent().html("<span class=\"_saved\"><span class=\"ui-icon ui-icon-check\"></span>Deleted</span>");
							$('#'+$qid[1]).attr("onClick","document.location = '?_action=requests&id="+$qid[2]+"'").removeClass('quote_blue zebra1 zebra2').addClass('quote_red').children('#ordered').text('Deleted');
							$('#DT'+$qid[1]).hide();
							$('.canvas').hide();
						} else {
							$('._wait').remove();
							$('#controls-'+$qid[1]).children("div").show();
							return alert($result);
						}
					},
					'html'
				);
			} else {
				$('._wait').remove();
				$('#controls-'+$qid[1]).children("div").show();
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

	//Page Counter Query
	$sql = 'SELECT COUNT(*) AS `Total` 
				FROM `supplier_quotes`
				WHERE `company_user_id` = \''.$session->userdata['id'].'\' AND `accepted` = 0';
	$count = $db->get_row($sql);
	$count = $count->Total;
	// ^ Page Counter Query

	$get_record = ($display_quote > 0) ? ' AND `sq`.`quote_id` = \''.$display_quote.'\' ' : '';
	
	$sql = 'SELECT
			`sq`.`quote_id`,
			`sq`.`quote_time`,
			`sq`.`supplier_note`,
			`sq`.`d_fee`,
			`sq`.`d_est`,
			`ci`.`customer_id`,
			`ci`.`cust_name`,
			`ci`.`cust_phone`,
			`ci`.`country`,
			`ci`.`post_code`,
			`cv`.`vehicle_id`,
			`cv`.`registration_year`,
			`cv`.`engine_size`,
			`cv`.`fuel_type`,
			`cv`.`transmission`,
			`cv`.`body_type`,
			`cv`.`vehicle_make_name` AS `make_name`,
			`cv`.`vehicle_model_name` AS `model`,
			`cv`.`vin`,
			`cv`.`reg_number`,
			`pn`.`part_name`,
			(SELECT COUNT(*) FROM `customer_requests` AS `cr` WHERE `cr`.`vehicle_id` = `sq`.`vehicle_id` AND (`cr`.`order_group` = 0 OR `cr`.`order_group` = `sq`.`quote_id`)) AS `num_parts`,
			(SELECT COUNT(*) FROM `customer_requests` AS `other` WHERE `other`.`vehicle_id` = `sq`.`vehicle_id` AND `other`.`order_group` > 0 AND `other`.`order_group` != `sq`.`quote_id`) AS `num_order_other`,
			(SELECT COUNT(*) FROM `supplier_quotes_details` AS `qt` WHERE `qt`.`quote_id` = `sq`.`quote_id` AND `qt`.`accepted` = 0 AND `qt`.`company_user_id` = \''.$session->userdata['id'].'\') AS `num_quoted`,
			(SELECT COUNT(*) FROM `supplier_quotes_details` AS `ot` WHERE `ot`.`vehicle_id` = `sq`.`vehicle_id` AND `ot`.`method` > 0 AND `ot`.`accepted` = 0 AND `ot`.`quote_id` = `sq`.`quote_id`) AS `num_ordered`
		FROM `supplier_quotes` AS `sq`
		JOIN `customer_information` AS `ci` ON `ci`.`customer_id` = `sq`.`customer_id`
		JOIN `customer_vehicles` AS `cv` ON `cv`.`vehicle_id` = `sq`.`vehicle_id`
		LEFT OUTER JOIN `supplier_quotes_details` AS `ql` ON `ql`.`quote_id` = `sq`.`quote_id`
		LEFT OUTER JOIN `customer_requests` AS `pn` ON `pn`.`request_id` = `ql`.`request_id`
		WHERE `sq`.`company_user_id` = '.$session->userdata['id'].' AND `sq`.`accepted` = \'0\''.$get_record.'
		GROUP BY `sq`.`quote_id`
		HAVING `num_parts` > 0
		ORDER BY `sq`.`modified` DESC, '.$pager->getOrder('`sq`.`quote_time` DESC').'
		LIMIT '.$pager->PageLimits().'';
	$quotes = $db->get_results($sql);
    if($quotes)
	{
		?>
		
        <div id="list_options">
            <p><?php echo $pager->OrderOptions(); ?></p>
        </div>
		<br clear="all" />
		<div id="list_options">All Orders <?=(($session->userdata['vat'] > 0) ? 'include' : 'exclude');?> V.A.T.</div>
		<br clear="all" />
        <div class="request_tbl ui-corner-all">
			<table>
				<tr>
					<th width="115">Quote Ref#</th>
					<th width="100" align="center">Date</th>
					<th width="250">Make &amp; Model</th>
					<th width="300">Part</th>
					<th width="120">Your Quote</th>
					<th width="80" style="text-align:center;">Orders</th>
				</tr>
			</table>
		<div class="scroll-frame">
		<?php
		$i = 1;
        foreach($quotes as $quotes)
		{
			$total = 0;
			if($quotes->num_quoted > 0) {
				$sql = 'SELECT 
							`sqd`.`quote_detail_id`,`sqd`.`quote_price`,`sqd`.`quote_guarantee`,`sqd`.`quote_condition`,`sqd`.`method`,`pr`.`request_id`,`pr`.`request_notes`,`pr`.`part_name`
						FROM `supplier_quotes_details` AS `sqd`
						JOIN `customer_requests` AS `pr` ON `pr`.`customer_id` = \''.$quotes->customer_id.'\' AND `pr`.`request_id` = `sqd`.`request_id`
						WHERE `sqd`.`quote_id` = \''.$quotes->quote_id.'\' AND (`pr`.`order_group` = 0 OR `pr`.`order_group` = \''.$quotes->quote_id.'\')';

				$parts = $db->get_results($sql);
				$cache_parts_error = mysql_error();
				$disp_parts = array();
				if($parts) {
					foreach($parts AS $p)
					{
						$total = $total + $p->quote_price;
						$disp_parts[] = (!empty($p->part_name)) ? $p->part_name : 'Could not get part name';
					}
				} else {
					if($settings['debug_mode'])
					{
						echo 'ERROR - No Results Returned:<br/>'.$cache_parts_error;
					}
				}
				if($settings['debug_mode'])
				{
					echo 'Total Number of Parts: '.$quotes->num_parts.'<br/>Number of Parts Quoted: '.$quotes->num_quoted.'<br/>Number of Parts Ordered from self: '.$quotes->num_ordered.'<br/>Number of Parts Ordered from others: '.$quotes->num_order_other.'';
				}
			}
			$total = $total + $quotes->d_fee;
		?>
			<table>
				<tr class="<?php echo (($quotes->num_ordered > 0) ? 'quote_blue ' : zebra($i)) . 'rowover'; ?>" id="<?=$quotes->quote_id;?>" onclick="show_details(this.id);">
					<td width="115"><?=pad_ref_number($quotes->vehicle_id);?></td>
					<td width="100"><?=date("d/m/Y",$quotes->quote_time);?></td>
					<td width="250"><?php echo $quotes->make_name.' '.$quotes->model; ?></td>
					<td width="300"><?php foreach($disp_parts AS $name) { echo $name.'<br/>'; } ?></td>
					<td width="120" id="quote-row-price-<?=$quotes->quote_id;?>"><b>&pound;<?php echo (($session->userdata['vat'] > 0) ? number_format(($total*($settings['vat_rate']/100))+$total,2) : number_format($total,2)); ?></b></td>
					<td width="80" style="text-align:center;" id="ordered"><?=(($quotes->num_ordered > 0) ? '<b>' . $quotes->num_ordered . ' New</b>' : 'None');?></td>
				</tr>
			</table>

			<div style="display:<?=(($quotes->quote_id == $display_quote) ? 'block' : 'none');?>;" class="details" id="DT<?=$quotes->quote_id;?>">
				<div class="m10 left w30">
					<div class="reg_grp ui-corner-all">
						<h2>Vehicle Details</h2>
						<table class="newuser_tbl">
							<tr>
								<th width="100">VIN:</th>
								<td><?=((strlen($quotes->vin) > 5) ? $quotes->vin : 'Not Provided');?></td>
							</tr>
							<tr>
								<th width="100">REG:</th>
								<td><?=((strlen($quotes->reg_number) > 4) ? $quotes->reg_number : 'Not Provided');?></td>
							</tr>
						</table>
					</div>
					<div class="reg_grp ui-corner-all">
						<h2><b>Customer Details</b></h2>
						<table class="newuser_tbl">
							<tr>
								<th width="100">Ref#:</th>
								<td><?=pad_ref_number($quotes->vehicle_id);?></td>
							</tr>
							<tr>
								<th width="100">Name:</th>
								<td><?=$quotes->cust_name;?></td>
							</tr>
							<tr>
								<th>Phone No:</th>
								<td><?=$quotes->cust_phone;?></td>
							</tr>
							<tr>
								<th>Post Code:</th>
								<td><?=$quotes->post_code;?></td>
							</tr>
							<tr>
								<th>Country:</th>
								<td><?=$quotes->country;?></td>
							</tr>
						</table>
					</div>
				</div>
				<?php
				echo '
					<div style="margin-top:10px;margin-bottom:10px;" class="reg_grp ui-corner-all left w66">
					<h2>'.$quotes->make_name.' '.$quotes->model.' | '.$quotes->registration_year.' | '. $quotes->body_type.' | '.$quotes->fuel_type.' | '.$quotes->engine_size.'cc | '.$quotes->transmission.'</h2>
					<form method="post" action="?_action=quotes" id="_quoteid-'.$quotes->quote_id.'" name="_quote-'.$quotes->quote_id.'">
					<input type="hidden" name="cust_name" id="cust_name-'.$quotes->quote_id.'" value="'.$quotes->cust_name.'" />
					<input type="hidden" name="cust_phone" id="cust_phone-'.$quotes->quote_id.'" value="'.$quotes->cust_phone.'" />
					<input type="hidden" name="addr1" id="addr1-'.$quotes->quote_id.'" value="" />
					<input type="hidden" name="addr2" id="addr2-'.$quotes->quote_id.'" value="" />
					<input type="hidden" name="city" id="city-'.$quotes->quote_id.'" value="" />
					<input type="hidden" name="county" id="county-'.$quotes->quote_id.'" value="" />
					<input type="hidden" name="country" id="country-'.$quotes->quote_id.'" value="'.$quotes->country.'" />
					<input type="hidden" name="post_code" id="post_code-'.$quotes->quote_id.'" value="'.$quotes->post_code.'" />
					<input type="hidden" name="_num_parts" id="_num_parts-'.$quotes->quote_id.'" value="'.$quotes->num_parts.'" />
					<input type="hidden" name="_qid" value="'.$quotes->quote_id.'" />
					<input type="hidden" name="_cid-'.$quotes->quote_id.'" value="'.$quotes->customer_id.'" />
					<table class="newuser_tbl">
					<tr>
						<th colspan="5"><b>Private Notes</b></th>
					</tr>
					<tr>
						<td colspan="5"><textarea name="_private_notes" id="_private_notes" style="width:595px;height:60px;">'.$quotes->supplier_note.'</textarea><br clear="all" /><button name="_update_private_notes" id="_update_private_notes">Save Notes</button></td>
					</tr>
					<tr>
						'.(($quotes->num_ordered == 0) ? '<th width="10"></th>' : '').'
						<th width="*" colspan="2">Part Name</th>
						<th width="120">Guarantee</th>
						<th width="80">Price</th>
					</tr>
				';
				
				$order_total = 0;
				$id = 1;
				if($parts)
				{
					foreach($parts AS $part)
					{
						$client_notes = ($part->request_notes) ? '<tr><th colspan="5">Notes for '.$part->part_name.':</th></tr><tr><td colspan="5">'.$part->request_notes.'</td></tr>' : '';
						$image = (file_exists('../_requests/images/'.$part->request_id.'.jpg')) ? '&nbsp; &nbsp;<a href="'.BASE.'_requests/images/'.$part->request_id.'.jpg" class="ui-state-default ui-corner-all lytebox" title="View attached image"><span class="ui-icon ui-icon-image"></span>Image</a>' : '';
						
						if($part->method > 0) $order_total = $order_total + $part->quote_price;

						echo '
						<tr>
							'.(($quotes->num_ordered == 0) ? '<td align="center" valign="middle" class="take-order"><input type="checkbox" name="basket-'.$quotes->quote_id.'[]" value="'.$part->quote_detail_id.'" /></td>' : '').'
							<td colspan="2" class="'.(($part->method > 0) ? 'part_ordered' : 'part').'">'.$part->part_name.' ('.$part->quote_condition.')'.$image.'</td>
							<td style="background-color:#FFF;">'.$part->quote_guarantee.'</td>
							<td align="center">
								&pound; <input type="text" name="_price-'.$id.'" id="price-'.$id.'-'.$quotes->quote_id.'" value="'.$part->quote_price.'" size="6" />
								<input type="hidden" name="_original-'.$id.'" value="'.$part->quote_price.'" />
								<input type="hidden" name="_quote_detail_id-'.$id.'" value="'.$part->quote_detail_id.'" />
								<input type="hidden" name="_name-'.$id.'" value="'.$part->part_name.'" />
							</td>
						<tr/>
						'.$client_notes;

						$id++;
					}
				}
				$order_total = $order_total + $quotes->d_fee;
				echo '
						<tr>
							'.(($quotes->num_ordered == 0) ? '<td class="take-order"></td>' : '').'
							<td colspan="2"><b>Delivery Fee:</b></td>
							<td style="text-align:right;"></td>
							<td style="background-color:#FFF;">&pound; <input type="text" name="_delivery" id="delivery-'.$quotes->quote_id.'" value="'.$quotes->d_fee.'" size="6" /><input type="hidden" name="_original-delivery" value="'.$quotes->d_fee.'" /></td>
						</tr>
						<tr>
				';
				if($quotes->num_ordered > 0) {
					echo '
							<td style="text-align:right;"><b>Order Total:</b></td>
							<td style="background-color:#FFF;">&pound;'.number_format($order_total,2).'</td>
					';
				}
				echo '
							'.(($quotes->num_ordered == 0) ? '<td class="take-order"></td>' : '').'<td style="text-align:right;"'.(($quotes->num_ordered == 0) ? ' colspan="3"' : '').'><b>Quote Total:</b></td>
							<td style="background-color:#FFF;" id="quote-total-'.$quotes->quote_id.'">&pound;'.number_format($total,2).'</td>
						</tr>
						<tr>
				';
				if($quotes->num_ordered > 0) {
					echo '
							<td style="text-align:right;"><b>+VAT:</b></td>
							<td style="background-color:#FFF;">&pound;'.number_format(($order_total*($settings['vat_rate']/100))+$order_total,2).'</td>
					';
				}
				echo '
							'.(($quotes->num_ordered == 0) ? '<td class="take-order"><span class="ui-icon ui-icon-arrowstop-1-s" style="position:relative;margin:0;"></span></td>' : '').'<td style="text-align:right;"'.(($quotes->num_ordered == 0) ? ' colspan="3"' : '').'><b>+VAT:</b></td>
							<td style="background-color:#FFF;" id="quote-vat-'.$quotes->quote_id.'">&pound;'.number_format(($total*($settings['vat_rate']/100))+$total,2).'</td>
						</tr>
						<tr>
							<td colspan="'.(($quotes->num_ordered == 0) ? '5' : '4').'" style="padding:10px;" id="controls-'.$quotes->quote_id.'">
								<div style="float:left;display:inline-block;" id="buttons-'.$quotes->quote_id.'-left">
									<div id="stage-left-two-'.$quotes->quote_id.'" style="display:'.(($quotes->num_ordered > 0) ? 'block' : 'none').';"> 
										<button id="cancel-'.$quotes->quote_id.'" class="cancel_order">Cancel Order</button> 
										 &nbsp; 
										<button id="accept-'.$quotes->quote_id.'" class="accept_order">Accept Order</button>
									</div>
									<div id="stage-left-one-'.$quotes->quote_id.'" style="display:'.(($quotes->num_ordered > 0) ? 'none' : 'block').';"> 
										<button id="order-'.$quotes->quote_id.'" class="take_order">Take Order</button>
										&nbsp; 
										<button id="alter-'.$quotes->quote_id.'" class="alter_order">Modify Order</button>
									</div>
								</div>
								<div style="float:right;display:inline-block;" id="buttons-'.$quotes->quote_id.'-right">
									<div id="stage-right-one-'.$quotes->quote_id.'" style="display:'.(($quotes->num_ordered > 0) ? 'none' : 'block').';"> 
										<button id="quote-'.$quotes->quote_id.'" class="alter_quote">Alter Prices</button>
										 &nbsp; 
										<button id="delete-'.$quotes->quote_id.'-'.$quotes->vehicle_id.'" class="delete_quote">Delete Quote</button>
									</div>
								</div>
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
		<div class="get_address" style="display:none;">
			<div class="reg_grp ui-corner-all left">
			<h2>Customer Details</h2>
			<form name="address_form" id="address_form" method="POST" action="">
				<table class="newuser_tbl" width="400">
					<tbody>
						<tr><th colspan="2"><center>Contact Information</center></th></tr>
						<tr><th>Full Name:</th><td><input type="text" name="cust_name" id="cust_name" value="" /></td></tr>
						<tr><th>Contact Number:</th><td><input type="text" name="cust_phone" id="cust_phone" value="" /></td></tr>
						<tr><th colspan="2"><center>Address Details</center></th></tr>
						<tr><th>Address Line 1:</th><td><input type="text" name="addr1" id="addr1" value="" /></td></tr>
						<tr><th>Address Line 2:</th><td><input type="text" name="addr2" id="addr2" value="" /></td></tr>
						<tr><th>City:</th><td><input type="text" name="city" id="city" value="" /></td></tr>
						<tr><th>County:</th><td><input type="text" name="county" id="county" value="" /></td></tr>
						<tr><th>Country:</th><td><select name="country" style="width:250px"><?=country_options($quotes->country);?></select></td></tr>
						<tr><th>Post Code:</th><td><input type="text" name="post_code" id="post_code" value="" /></td></tr>
						<tr><td colspan="2" valign="middle" height="38">
							<input type="hidden" name="target_quote" id="target_quote" value="" />
							<input type="hidden" name="_basket" id="_basket" value="" />
							<div style="float:right;"><a href="" id="submit_order" title="Submit Customers Order" class="ui-state-default ui-corner-all submit_order"><span class="ui-icon ui-icon-gear"></span>&nbsp;Submit Order</a></div>
							<div style="float:left;"><a href="" id="submit_order_cancel" title="Cancel" class="ui-state-default ui-corner-all submit_order_cancel"><span class="ui-icon ui-icon-gear"></span>&nbsp;Cancel</a></div>
						</td></tr>
					</tbody>
				</table>
			</form>
			</div>
		</div>
		<div id="pager_holder" style="padding-top:10px;"><?=$pager->ShowPages($count);?></div>			
			<?php
	} else {
		?>
         <div class="ui-widget m10">
                <div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em;"> 
                	 <p><span class="ui-icon ui-icon-info"  style="position:relative;float:left;margin-top:0.1em;margin-right:1em;"></span>You have not quoted any part requests yet.</p>
                </div>
            </div>
       <?php
	} 
	echo '</div>';
}


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
	default: quote_list($db); break;

// ----------------------------
// --- PROCESS ACCEPT ORDER ---
// ----------------------------
	case 'accept_order':
		$qid = $db->mysql_prep($_REQUEST['_qid']);
		if(is_numeric($qid) && $qid > 0)
		{
			$sql = 'UPDATE `supplier_quotes_details` SET `accepted` = \''.time().'\' WHERE `quote_id` = \''.$qid.'\' AND `company_user_id` = \''.$session->userdata['id'].'\' AND `method` > 0';
			if(!$result = mysql_query($sql)) {
				echo mysql_error();
			} else {
				if(mysql_affected_rows() > 0)
				{
					$sql = 'UPDATE `supplier_quotes` SET `accepted` = \''.time().'\' WHERE `quote_id` = \''.$qid.'\' AND `company_user_id` = \''.$session->userdata['id'].'\'';
					if(mysql_query($sql))
					{
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
								<h1>Order Accepted</h1>
								<p><b>'.$info->strName.'</b> from <b>'.$info->c_name.'</b> has accepted your order for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'</p>
								<p>Visit <a href="'.BASE.'">'.BASE.'</a> to login and make payment or view details about your order.</p>
								<p>Thank you.</p>
							';
						} else {
							echo mysql_error();
							$message = false;
						}
						
						if($info->recieve_emails > 0 && !$message == false)
						{
							$send = send_email($info->cust_email,'noreply@textspares.co.uk','TextSpares - Your Order has been accepted',$message);
							if(!$send == true) echo $send;
						}
					} else {
						echo '<div id="result">There was a problem accepting the order.</div>';
					}
				}
			}
		}
	break;

// ---------------------------
// --- PROCESS CANCEL ORDER
// ---------------------------
	case 'cancel_order':
		$qid = $db->mysql_prep($_REQUEST['_qid']);
		if(is_numeric($qid) && $qid > 0)
		{
			$sql = 'UPDATE `supplier_quotes_details` SET `cancelled` = \''.time().'\', `method` = 0 WHERE `quote_id` = \''.$qid.'\' AND `company_user_id` = \''.$session->userdata['id'].'\' AND `method` > 0';
			if(!$result = mysql_query($sql)) {
				echo '<div id="result">There was a problem cancelling the order.</div>';
			} else {
				if(mysql_affected_rows() > 0)
				{
					$sql = 'UPDATE `customer_requests` SET `order_group` = 0 WHERE `order_group` = \''.$qid.'\'';
					if(mysql_query($sql)) {
						echo '<div id="result">Success</div>';
						
						$sql = 'SELECT 
									`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,
									`c`.`cust_email`,`c`.`cust_name`,`c`.`recieve_emails`,`c`.`customer_id`
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
								<p><b>'.$info->strName.'</b> from <b>'.$info->c_name.'</b> has cancelled your order with them for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'</p>
								<p>You can login and contact the supplier or order the parts from another supplier: <a href="http://www.textspares.co.uk/?k='.create_emailloginkey($info->customer_id).'">Check My Quotes</a>.</p>
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
	break;
	
	case 'alter_quote':
		$errors = array();
		$is_valid = true;
		$qid = (int) $db->mysql_prep($_POST['_qid']);
		$np = (int) $_POST['_num_parts'];
		
		if(is_int($qid) && $qid > 0)
		{
			$sql = 'SELECT COUNT(*) FROM `customer_requests` WHERE `order_group` = \''.$qid.'\' AND `transaction_id` = 0';
			$count = $db->get_var($sql);
			if($count > 0)
			{
				$is_valid = false;
				$errors[] = 'You can\'t alter a quote while it is on order!'."\n".'You must first cancel the order.';
			}
		} else {
			$is_valid = false;
			$errors[] = 'Invalid Quotation ID';
		}
		
		if(is_int($np) && $np > 0)
		{
			for($i=1;$i<$np;$i++)
			{
				if(!is_numeric($_POST['_price-'.$i]) && !$_POST['_price-'.$i] > 0) {
					$is_valid = false;
					$errors[] = 'Invalid price value for '.$_POST['_name'.$i]."\n";
				}
			}

			if(!is_numeric($_POST['_delivery'])) {
				$is_valid = false;
				$errors[] = 'Invalid value for the delivery Fee'."\n";
			}
		}
		else
		{
			$is_valid = false;
			$errors[] = 'There was no records to ammend!?';
		}

		if($is_valid == true)
		{
			$results = array();
			$mail = "\n\n";
			$new_total = 0;
			for($i=1;$i<=$np;$i++)
			{
				$new_total = $new_total + $_POST['_price-'.$i];
				$detail_id = $db->mysql_prep($_POST['_quote_detail_id-'.$i]);
				if(is_numeric($detail_id) && $detail_id > 0)
				{
					if($_POST['_original-'.$i] <> $_POST['_price-'.$i]) {
						$sql = 'UPDATE `supplier_quotes_details` SET `quote_price` = \''.$_POST['_price-'.$i].'\' WHERE `quote_id` = \''.$qid.'\' AND `quote_detail_id` = \''.$detail_id.'\' AND `company_user_id` = \''.$session->userdata['id'].'\'';
						$update = mysql_query($sql);
						if(!$update) {
							echo $sql.'<br/>'.mysql_error();
						} else {
							$mail .= ' - <b>'.$_POST['_name-'.$i].'</b> : WAS: &pound;'.$_POST['_original-'.$i].' &nbsp; &gt; &nbsp; NOW: &pound;'.$_POST['_price-'.$i]."\n\n";
						}
					}
				}
			}
			
			$new_total = $new_total + $_POST['_delivery'];
			
			if($_POST['_original-delivery'] <> $_POST['_delivery'])
			{
				$update = mysql_query('UPDATE `supplier_quotes` SET `d_fee` = \''.$_POST['_delivery'].'\' WHERE `quote_id` = \''.$qid.'\'');
				$mail .= "\n".' - <b>Derlivery Fee</b> : WAS: &pound;'.$_POST['_original-delivery'].' &nbsp; &gt; &nbsp; NOW: &pound;'.$_POST['_delivery']."\n\n";
			}

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
					<h1>Quotation Updated</h1>
					<p><b>'.$info->strName.'</b> from <b>'.$info->c_name.'</b> has altered their quote for your '.$info->vehicle_make_name.' '.$info->vehicle_model_name.'</p>
					<p>You can login and view the new quote from the supplier: <a href="'.BASE.'">'.BASE.'</a>.</p>
					<p>'.$mail.'</p>
					<p>Thank you.</p>
				';
			} else {
				echo mysql_error();
				$message = false;
			}
			
			if($info->recieve_emails > 0 && !$message == false)
			{
				$send = send_email($info->cust_email,'noreply@textspares.co.uk','TextSpares - Quotation Updated',$message);
				if(!$send == true) echo $send;
			}
		}
		else
		{
			echo '<div id="result">The following errors occoured while processing your quote:'."\n";
			foreach($errors AS $error) {
				echo ' - '.$error."\n";
			}
			echo '</div>';
		}
	break;
	
	case 'submit_order':
		$errors = array();
		$quote = (int) $_REQUEST['target_quote'];
		$billing_valid = false;
		$delivery_valid = false;
		if($quote > 0)
		{
			//TODO: Check the order has not been placed by someone else during interval
			$sql = 'SELECT `quote_id`,`customer_id` FROM `supplier_quotes` WHERE `quote_id` = \''.$quote.'\'';
			$row =  $db->get_row($sql);
			$quote = (int) $row->quote_id;
			$customer = (int) $row->customer_id;
			
			if($quote > 0 && $customer > 0)
			{
				$cust_name = $db->mysql_prep(trim($_REQUEST['cust_name']));
				$cust_phone = $db->mysql_prep(ereg_replace("[^0-9]",'',$_REQUEST['cust_phone']));
				
				if(strlen(ereg_replace("[^A-Za-z_\s]",'',$cust_name)) < 2) {
					$errors[] = 'Invalid Customer Name.';
				}
				if(strlen($cust_phone) < 10) {
					$errors[] = 'Invalid Phone Number.';
				}
				
				$valid = validAddress('addr1','addr2','city','county','country','post_code');
				
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
			} else {
				$errors[] = 'Record ID not found in database: '.$quote;
			}
			
		} else {
			$errors[] = 'Invalid Record ID: '.$quote;
		}
		
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
				WHERE `customer_id` = \''.$customer.'\'';
			$db->query($sql);
			
			$items = explode(',',$_REQUEST['_basket']);
			
			if(count($items) > 0)
			{
				foreach($items AS $item) {
					$item = (int) $item;
					if($item < 1) {
						$errors[] = 'Invalid Item ID: '.$item;
					}
				}
			} else {
				$errors[] = 'There where no items in the basket!';
			}
			$fails = 0;
			if(count($errors) == 0)
			{
				$sql = 'UPDATE `supplier_quotes` SET `accepted` = \''.time().'\', `modified` = \''.time().'\' WHERE `quote_id` = \''.$quote.'\'';
				$result = mysql_query($sql);
				if($result) {
					foreach($items AS $item) {
						$sql = 'UPDATE `supplier_quotes_details` AS `d`, `customer_requests` AS `r` SET `d`.`modified` = \''.time().'\', `d`.`accepted` = \''.time().'\', `d`.`method` = 1, `r`.`order_group` = \''.$quote.'\' WHERE `d`.`quote_detail_id` = \''.$item.'\' AND `d`.`quote_id` = \''.$quote.'\' AND `r`.`request_id` = `d`.`request_id`';
						$result = mysql_query($sql);
						if(!$result) {
							$errors[] = 'Could not update order status of the request: i'.$item.' / q'.$quote;
							$fails++;
						}
					}
					if($fails > 0)
					{
						$sql = 'UPDATE `supplier_quotes` SET `accepted` = 0 WHERE `quote_id` = \''.$quote.'\'';
						$result = mysql_query($sql);
						if(!$result) {
							$errors[] = 'Failed to restore master quote record after all items failed to update.';
							//TODO: Add record amendment fail logging with "repair records" tool in admin panel
						}
						foreach($items AS $item) {
							$sql = 'UPDATE `supplier_quotes_details` AS `d`, `customer_requests` AS `r` SET `d`.`accepted` = 0, `d`.`method` = 0, `r`.`order_group` = 0 WHERE `d`.`quote_detail_id` = \''.$item.'\' AND `d`.`quote_id` = \''.$quote.'\' AND `r`.`request_id` = `d`.`request_id`';
							$result = mysql_query($sql);
							if(!$result) {
								$errors[] = 'Status of this request was not properly restored: i'.$item.' / q'.$quote;
								//TODO: Add record amendment fail logging with "repair records" tool in admin panel
							}
						}
					}
				} else {
					$errors[] = 'Could not update supplier quotes.';
				}
			}
		}
		
		if(count($errors) > 0) {
			$jsondata = array('error',implode("\n",$errors));
		} else {
			//TODO: Add notification alerts.
			$jsondata = array('ok');
		}
	break;
	
	case 'delete_quote':
		$qid = (int) $db->mysql_prep($_REQUEST['_qid']);
		
		if(is_int($qid) && $qid > 0)
		{
			$sql = 'SELECT `q`.`quote_id`,(SELECT COUNT(*) FROM `customer_requests` AS `r` WHERE `r`.`order_group` = `q`.`quote_id`) AS `ordered` FROM `supplier_quotes` AS `q` WHERE `q`.`quote_id` = \''.$qid.'\' AND `q`.`company_id` = \''.$session->userdata['companyid'].'\' LIMIT 1';
			$quote = $db->get_row($sql);
			$quote_id = (int) $quote->quote_id;
			if($quote->ordered > 0)
			{
				echo '<div id="result">Can\'t Delete this quote while an order is in place!'."\n".'You must first cancel the order.</div>';
			} else {
				if(is_int($quote_id) && $quote_id > 0)
				{
					$sql = 'DELETE FROM `supplier_quotes_details` WHERE `quote_id` = \''.$quote_id.'\'';
					$result = mysql_query($sql);
					if(!$result) {
						echo '<div id="result">MYSQL ERROR:'."\n".mysql_error().'</div>';
					} else {
						$sql = 'DELETE FROM `supplier_quotes` WHERE `quote_id` = \''.$quote_id.'\' LIMIT 1';
						mysql_query($sql);
						echo '<div id="result">Success</div>';
					}
				} else {
					echo '<div id="result">Quotation ID not found!</div>';
				}
			}
		} else {
			echo '<div id="result">Bad Quotation ID</div>';
		}
	break;
}

?>
<?php

if(!defined('IN_TEXTSPARES')) { exit; }

include('Includes/pager.inc.php');
$vdata->checks["text"] = "";
$vdata->checks["dname"] = "/^.{1,64}$/";
$vdata->checks["select"] = "/[^invalid]/";
$vdata->checks["number"] = "/^[0-9][0-9]{0,}$/";
$vdata->checks["vcode"] = "/^[0-9][0-9]{5}$/";
$vdata->checks["email"] = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
$vdata->checks["phone"] = "/^[0][0-9[:space:]\-]{10,13}$/";
$vdata->checks["pswd"] = "/^[a-zA-Z0-9]{1}[_a-z0-9]{7,24}/";
$vdata->checks["user"] = "/^[a-zA-Z0-9]+[_a-zA-Z0-9-]{5,24}$/";
$vdata->checks["pcode"] = "/(((^[BEGLMNS][1-9]\d?)|(^W[2-9])|(^(A[BL]|B[ABDHLNRST]|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]|F[KY]|G[LUY]|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]|M[EKL]|N[EGNPRW]|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKL-PRSTWY]|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)\d\d?)|(^W1[A-HJKSTUW0-9])|(((^WC[1-2])|(^EC[1-4])|(^SW1))[ABEHMNPRVWXY]))(\s*)?([0-9][ABD-HJLNP-UW-Z]{2}))$|(^GIR\s?0AA$)/";

//Number of seconds in a day.
$secinday = 86400;

function latest_requests($db) 
{
	global $secinday, $settings, $session, $page;
	
	$filters = '';
	$display_record = (!empty($_REQUEST['id'])) ? $_REQUEST['id'] : 0;
?>
	<script type="text/javascript">
	$.fn.setFocusTo = function(id) {
		$('#_price-'+id+'-1').focus();
	}
	$("#chk_all").live("click", function(e) { 
		e.preventDefault();
		$("input[name^=remove]").attr("checked","checked");
	});
	$("#remove_rqs").live("click", function(e) {
		e.preventDefault();
		$("form[name=_removeRequests]").submit();
	});
	$("a.view_request").live("click", function(e) {
		e.preventDefault();
	});
	$.fn.tallytotal = function(rid,parts)
	{
		$sub = parseFloat('0.00');
		$vat = <?=(($session->userdata['vat'] == 'Yes') ? 'true' : 'false');?>;
		$fee = parseFloat($('#_delivery-'+rid).val());
		for(i=1;i<=parts;i++)
		{
			$sub = $sub + parseFloat($('#_price-'+rid+'-'+i).val());
		}
		$('#subtotal'+rid).html($sub.toFixed(2));
		$('#delivery'+rid).html($fee.toFixed(2));
		$('#total'+rid).html(($vat) ? (($sub + $fee) + ((($sub + $fee)/100) * <?=$settings['vat_rate'];?>)).toFixed(2) + ' incVAT %<?=$settings['vat_rate'];?>' : ($sub + $fee).toFixed(2) + ' VAT Exempt');
	}

	$(document).ready(function()
	{
		var $_GET = {};
		document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
			function decode(s) {
				return decodeURIComponent(s.split("+").join(" "));
			}
			$_GET[decode(arguments[1])] = decode(arguments[2]);
		});
		
		$(".do_quote").click(function(e)
		{
			e.preventDefault();
			$url = "<?=ROOT;?>?_action=requests&_ajax=true&_subaction=quote";
			$link_id = "#"+$(this).attr('id');
			$parent = $($link_id).parent();
			$vehicle_id = $link_id.split("-");
			$eform = $($link_id).closest('form').get(0);
			$form = "#"+$($eform).attr('id');
			$parent.children("button").hide();
			$parent.append("<span class=\"_wait\"><span class=\"ui-icon ui-icon-clock\"></span>Please wait..</span>");
			$.post(
				$url,
				$($form).serialize(),
				function(data)
				{
					$start_ele = data.indexOf('<div id="result">');
					$start_pos = $start_ele+17;
					$end_pos = data.indexOf("</div>",$start_pos);
					$length = $end_pos - $start_pos;
					$result = data.substr($start_pos,$length);
					
					$start_ele = data.indexOf('<div id="quoteid">');
					$start_pos = $start_ele+18;
					$end_pos = data.indexOf("</div>",$start_pos);
					$length = $end_pos - $start_pos;
					$qid = data.substr($start_pos,$length);

					if($result == 'Success' || $result == 'Partial')
					{
					<?php
					/*
						if($result == 'Partial') {
							$('#'+$vehicle_id[1]).removeClass('zebra1 zebra2').addClass("quote_blue");
							
							$start_ele = data.indexOf('<div id="'+'quoted">');
							$start_pos = $start_ele+17;
							$end_pos = data.indexOf("</div>",$start_pos);
							$length = $end_pos - $start_pos;
							$fields = data.substr($start_pos,$length);
							
							$rows = $fields.split(',');
							$.each($rows,function(i,v) {
								$ids = v.split('-');
								$num_parts = parseInt($('#'+$vehicle_id[1]+'_num_parts').val());
								$('#'+$vehicle_id[1]+'_num_parts').val($num_parts-1);
								$('#part_text-'+$vehicle_id[1]+'-'+$ids[0]).remove();
								$('#part_price-'+$vehicle_id[1]+'-'+$ids[0]).remove();
								$('._wait').remove();
								$parent.children("a").show();
							});
						}
						if($result == 'Success') {
					*/
					?>
							$('._wait').remove();
							$parent.append("<span class=\"_saved\"><span class=\"ui-icon ui-icon-check\"></span>Quote Submitted.</span>");
							$('#'+$vehicle_id[1]).attr("onClick","document.location = '?_action=quotes&id="+$qid+"'").removeClass('quote_blue zebra1 zebra2').addClass('quote_green');
							$('#DT'+$vehicle_id[1]).hide();
							$('.canvas').hide();
					<?php //	} ?>
						
						return alert('Your quote has been submitted'+"\n"+'You can find and edit your quotes in My Quotes');
					} else {
						$('._wait').remove();
						$parent.children("button").show();
						return alert($result);
					}
				},
				'html'
			);
		});

		$(".save_request").click(function(e)
		{
			e.preventDefault();
			$save_data = $(this).attr("id").split("-");
			$link_id = "#"+$(this).attr("id");
			$parent = $($link_id).parent();
			$vehicle_id = $save_data[1];
			$parent.children("button").hide();
			$parent.append("<span class=\"_wait\"><span class=\"ui-icon ui-icon-clock\"></span>Please wait..</span>");
			$url = "<?=ROOT;?>?_action=requests&_ajax=true&_subaction=save";
			$.ajaxSetup({cache: false});
			$.get(
				$url,
				{ _vehicle_id: $vehicle_id },
				function(data) {
					$ele = '<div id="'+'result">';
					$start_ele = data.indexOf($ele);
					$start_pos = $start_ele+17;
					$end_pos = data.indexOf("</div>",$start_pos);
					$length = $end_pos - $start_pos;
					$result = data.substr($start_pos,$length);
					if($result == "Success") {
						$($link_id).hide;
						$('._wait').remove();
						$($link_id).parent().append("<span class=\"_saved\"><span class=\"ui-icon ui-icon-check\"></span>Saved</span>");
					} else {
						$parent.children("button").show();
						$('._wait').remove();
						return alert($result);
					}
				},
				'html'
			);
		});

		$(".save_undo").click(function(e)
		{
			e.preventDefault();
			$saved_data = $(this).attr("id").split("-");
			$tableid = "#TT"+$saved_data[1];
			$link_id = "#"+$(this).attr("id");
			$parent = $($link_id).parent();
			$vehicle_id = $saved_data[1];
			$cid = $saved_data[2];
			$parent.children("button").hide();
			$parent.append("<span class=\"_wait\"><span class=\"ui-icon ui-icon-clock\"></span>Please wait..</span>");
			$url = "<?=ROOT;?>?_action=requests&_ajax=true&_subaction=saveundo";
			$.ajaxSetup({cache: false});
			$.get(
				$url,
				{ _vehicle_id: $vehicle_id },
				function(data) {
					$ele = '<div id="'+'result">';
					$start_ele = data.indexOf($ele);
					$start_pos = $start_ele+17;
					$end_pos = data.indexOf("</div>",$start_pos);
					$length = $end_pos - $start_pos;
					$result = data.substr($start_pos,$length);
					if($result == "Success") {
						$('._wait').remove();
						<?=(($page == 'saved') ? '$($tableid).remove();$(\'#\'+$saved_data[1]).remove(); $(\'#DT\'+$saved_data[1]).hide(); $(\'.canvas\').hide();' : '');?>
					} else {
						$parent.children("button").show();
						$('._wait').remove();
						return alert($result);
					}
				},
				'html'
			);
		});
		
		$(".remove_request").click(function(e)
		{
			e.preventDefault();
			$confirm = confirm('Are you sure you want to Remove this request?');
			if($confirm) {
				$save_data = $(this).attr("id").split("-");
				$link_id = "#"+$(this).attr("id");
				$parent = $($link_id).parent();
				$vehicle_id = $save_data[1];
				$request_id = $save_data[2];
				$parent.children("button").hide();
				$parent.append("<span class=\"_wait\"><span class=\"ui-icon ui-icon-clock\"></span>Please wait..</span>");
				$url = "<?=ROOT;?>?_action=requests&_ajax=true&_subaction=remove";
				$.ajaxSetup({cache: false});
				$.get(
					$url,
					{ vehicle_id: $vehicle_id, request_id: $request_id },
					function(data) {
						$ele = '<div id="'+'result">';
						$start_ele = data.indexOf($ele);
						$start_pos = $start_ele+17;
						$end_pos = data.indexOf("</div>",$start_pos);
						$length = $end_pos - $start_pos;
						$result = data.substr($start_pos,$length);
						if($result == "Success") {
							$($link_id).hide();
							$('#DT'+$vehicle_id).remove();
							$('.canvas').hide();
							$('#TT'+$vehicle_id).remove();
						} else {
							$parent.children("button").show();
							$('._wait').remove();
							return alert($result);
						}
					},
					'html'
				);
			}
		});

		//filter drop down		
		$("#_make_drop").change(function(){
			var $make = $("#_make_drop option:selected").val();
			$("#_model_drop option").css("display","none");
			$("#_model_drop option:first-child").css("display","block");
			$("#_model_drop option[rel="+$make+"]").css("display","block");
			$("#_model_drop").val($_GET['_make']);
		});	

		//filter apply
		$("#_filter").click(function(e){
			e.preventDefault();
			$filter = "?_action=<?=$page?>&_filter=true";
			$filter += "&_make="+$("#_make_drop option:selected").val();
			$filter += "&_model="+$("#_model_drop option:selected").val();
			$filter += "&_part="+$("#_part_drop option:selected").val();
			$filter += "&_date="+$("#_date_drop option:selected").val();
			//alert($filter);
			window.open($filter,"_self");
		});

		if($_GET['_make'] != 'All')
		{
			$("#_model_drop option").css("display","none");
			$("#_model_drop option:first-child").css("display","block");
			$("#_model_drop option[rel="+$_GET['_make']+"]").css("display","block");
		}
	<?php
		if($display_record > 0)
		{
			echo '
			$(\'.canvas\').show();
			$.fn.tallytotal('.$display_record.',$(\'#'.$display_record.'_num_parts\').val());';
		}
	?>
	});
	</script>

	<div id="latest_requests" style="border:none;">
		<h1><img src="<?=ROOT;?>Images/requests.png" width="35" height="35" alt="" style="vertical-align:middle;" />Latest Requests</h1>
		<?=print_search_bar();?>
            	<?php
				$pager = new Pager();
				//$pager->page_size = 25;
				$pager->display_rpp = true;

				$pager->list_options['Request Time'] = '`cr`.`request_stamp` DESC';
				$pager->list_options['Make'] = '`cv`.`vehicle_make_name` ASC';
				$pager->list_options['Model'] = '`cv`.`vehicle_model_name` ASC';
				$pager->list_options['Part'] = '`cr`.`part_name` ASC';
				
				$save_join = ($page == 'saved') ? 'LEFT OUTER JOIN `tblSavedRequests` AS `sv` ON `sv`.`vehicle_id` = `cr`.`vehicle_id` AND `sv`.`company_user_id` = \''.$session->userdata['id'].'\' ' : '';
				$save_clause = ($page == 'saved') ? ' AND `sv`.`vehicle_id` IS NOT NULL ' : '';

				if(!empty($_GET['_filter']))
				{
					$filters .= ($_GET['_make'] != 'All') ? ' AND `cv`.`vehicle_make` = \''.$db->mysql_prep($_GET['_make']).'\'' : '';
					$filters .= ($_GET['_model'] != 'All') ? ' AND `cv`.`vehicle_model` = \''.$db->mysql_prep($_GET['_model']).'\'' : '';
					$filters .= ($_GET['_part'] != 'All') ? ' AND `cr`.`part_category` = \''.$db->mysql_prep($_GET['_part']).'\'' : '';
					$filters .= ($_GET['_date'] != 'All') ? ' AND `cr`.`request_stamp` = \''.(time()-($secinday*$_GET['_date'])).'\'' : '';
				}

				?>
                <div id="list_options" style="width:948px;">
                    <p>
						<?php 
							$match_make = (!empty($_GET['_make'])) ? $_GET['_make'] : false;
							$match_model = (!empty($_GET['_model'])) ? $_GET['_model'] : false;
							echo $pager->OrderOptions();
						?>
                    	<span id="filters" style="float:right;">Filter by:
                        	<label for="_make">Make:</label>
                            <select id="_make_drop" name="_make">
                            		<option value="All" size="2">All</option>
									<?php
									foreach($db->get_results('SELECT `make_name`,`make_id` FROM `vehicle_makes` WHERE `parent_id` = \'0\' ORDER BY `make_name` ASC') as $make){ ?><option value="<?=$make->make_id;?>" <?php echo ($match_make == $make->make_id) ? ' selected="selected"' : ''; ?>><?=$make->make_name;?></option><?php } ?>
                            </select>
                            <label for="_model">Model:</label>
                            <select id="_model_drop" name="_model" size="1">
                            		<option id="all_value" value="All">All</option>
									<?php
									foreach($db->get_results('SELECT `make_name`,`make_id`,`parent_id` FROM `vehicle_makes` WHERE `parent_id` > 0 ORDER BY `parent_id` ASC') as $model){ 
									?><option value="<?=$model->make_id;?>" <?php echo ($match_model == $model->make_name) ? ' selected="selected"' : ''; ?> rel="<?php echo $db->get_var("SELECT `make_id` FROM `vehicle_makes` WHERE `make_id` = ".$model->parent_id); ?>" <?php echo ($match_make != $db->get_var("SELECT make_name FROM vehicle_makes WHERE make_id = ".$model->parent_id)) ? ' style="display:none;"' : ''; ?>><?=$model->make_name;?></option><?php } ?>
                            </select>
                            <label for="_part">Part:</label>
                            <select id="_part_drop" name="_part" size="1">
                            		<option value="All">All</option>
									<?php
									foreach($db->get_results('SELECT `part_id`,`part_cat_name` FROM `parts_categories` ORDER BY `part_cat_name` ASC') as $parts){
										echo '<option value="'.$parts->part_id.'" '.(!empty($_GET['_part']) && $_GET['_part'] == $parts->part_id ? ' selected' : '').'>'.$parts->part_cat_name.'</option>';
									}
									?>
                            </select>
							<label for="_date">Date:</label>
							<select id="_date_drop" name="_date" size="1">
								<option value="All">All</option>
								<?php
								$rollback = 14;
								for($rb=0;$rb<=$rollback;$rb++) {
									$s = time()-($secinday*$rb);
									echo '<option value="' . $rb . '"' . ( (isset($_GET['_date']) && $_GET['_date'] == $rb && $_GET['_date'] != 'All') ? ' selected' : '' ) . '>' . ( $s == time() ? 'Today' : date('l jS F',$s)) . '</option>';
								}
								?>
                            </select>
                            <a href="#" class="ui-state-default ui-corner-all" id="_filter"><span class="ui-icon ui-icon ui-icon-grip-dotted-horizontal"></span>Filter</a>
                        </span>
                    </p>
                </div>
				<br clear="all" />
                <div class="request_tbl ui-corner-all">	
                	<table>
                    	<tr>
                        	<th width="10"></th>
                        	<th width="80">Ref#</th>
                            <th width="150">Date - Time</th>
                            <th width="*">Parts</th>
                            <th width="220" style="text-align:center;">OPTIONS </th>
                        </tr>
					</table>
				<div class="scroll-frame">
                <?php
				$get_record = ($display_record > 0) ? ' AND `cv`.`vehicle_id` = \''.$display_record.'\' ' : '';
				$sql = 'SELECT
							`ci`.`customer_id`,
							`ci`.`cust_name`,
							`ci`.`cust_phone`,
							`ci`.`post_code`,
							`ci`.`country`,
							`cv`.`vehicle_id`,
							`cv`.`registration_year`,
							`cv`.`engine_size`,
							`cv`.`fuel_type`,
							`cv`.`transmission`,
							`cv`.`body_type`,
							`cv`.`vehicle_make_name` AS `make_name`,
							`cv`.`vehicle_model_name` AS `model`,
							`cv`.`reg_number`,
							`cv`.`vin`,
							`cr`.`request_id`,
							`cr`.`part_name`,
							`cr`.`request_notes`,
							`cr`.`request_stamp`,
							`sv`.`vehicle_id` AS `saved`,
							(SELECT COUNT(*) FROM `customer_requests` AS `cp` WHERE `cp`.`vehicle_id` = `cv`.`vehicle_id` AND `cp`.`order_group` = 0) AS `num_requests`,
							(SELECT COUNT(*) FROM `supplier_quotes_details` AS `sd` JOIN `customer_requests` AS `crsd` ON `crsd`.`request_id` = `sd`.`request_id` WHERE `sd`.`vehicle_id` = `cv`.`vehicle_id` AND `crsd`.`order_group` = 0 AND `sd`.`company_id` = \''.$session->userdata['companyid'].'\') AS `num_quotes`
						FROM `customer_vehicles` AS `cv`
							JOIN `customer_information` AS `ci` ON `ci`.`customer_id` = `cv`.`customer_id`
							LEFT JOIN `customer_requests` AS `cr` ON `cr`.`vehicle_id` = `cv`.`vehicle_id`
							LEFT JOIN `supplier_quotes` AS `sq` ON `sq`.`company_id` = \''.$session->userdata['companyid'].'\' AND `sq`.`vehicle_id` = `cv`.`vehicle_id`
							LEFT OUTER JOIN `tblRequestsRemoved` AS `rr` ON `rr`.`company_user_id` = \''.$session->userdata['companyid'].'\' AND `cr`.`request_id` = `rr`.`request_id`
							LEFT OUTER JOIN `tblSavedRequests` AS `sv` ON `sv`.`company_user_id` = \''.$session->userdata['id'].'\' AND `sv`.`vehicle_id` = `cv`.`vehicle_id`
						WHERE `cv`.`delete` = 0 AND `rr`.`request_id` IS NULL '.$get_record.$save_clause.$filters.'
						GROUP BY `cv`.`vehicle_id`
						HAVING `num_requests` > `num_quotes`
						ORDER BY '.$pager->getOrder('`cr`.`request_stamp` DESC').'
						LIMIT '.$pager->PageLimits();
				$numrows = 0;
				$results = $db->get_results($sql);
				if($results)
				{
					$i=1;	
					foreach($results as $requests)
					{
						$numrows++;
						$disp_parts = array();
						$total_parts = $requests->num_requests - $requests->num_quotes;
						
						$parts = array();
						if($total_parts > 0)
						{
							$sql = 'SELECT `cr`.`request_id`,`cr`.`request_notes`,`cr`.`part_name`
									FROM `customer_requests` AS `cr`
									LEFT JOIN `supplier_quotes_details` AS `sd` ON `sd`.`company_id` = '.$session->userdata['companyid'].' AND `sd`.`request_id` = `cr`.`request_id`
									WHERE `cr`.`vehicle_id` = \''.$requests->vehicle_id.'\' AND `cr`.`order_group` = 0 AND `sd`.`quote_detail_id` IS NULL';
							$part = $db->get_results($sql);
							if($part) {
								$r = 1;
								foreach($part AS $part)
								{
									$disp_parts[] = (!empty($part->part_name)) ? $part->part_name : 'Part Name Missing' ;
									$parts[$r]['quote_price'] = (!empty($part->quote_price)) ? $part->quote_price : 0;
									$parts[$r]['request_id'] = (!empty($part->request_id)) ? $part->request_id : 0;
									$parts[$r]['part_name'] = (!empty($part->part_name)) ? $part->part_name : 'Part Name Missing' ;
									$parts[$r]['request_notes'] = (!empty($part->request_notes)) ? $part->request_notes : false;
									$r++;
								}
							}
						}
						?>
						<table id="TT<?=$requests->vehicle_id;?>">
							<tr id="<?=$requests->vehicle_id;?>" class="rowover quote_blue" onclick="show_details(<?=$requests->vehicle_id;?>);$.fn.tallytotal(<?=$requests->vehicle_id;?>,<?=$total_parts;?>);"><td colspan="6"><?=$requests->make_name.' '.$requests->model.' | '.$requests->registration_year.' | '. $requests->body_type.' | '.$requests->fuel_type.' | '.$requests->engine_size.'cc | '.$requests->transmission;?></td></tr>
							<tr>
								<td width="10" height="40"><input type="checkbox" name="remove[]" value="<?=$requests->request_id;?>" /></td>
								<td width="80"><?=pad_ref_number($requests->vehicle_id);?></td>
								<td width="150"><?=date("D j/m/y H:i",$requests->request_stamp);?></td>
								<td width="*"><?=implode('<br/>',$disp_parts);?></td>
								<td width="220" style="text-align:center;">
									<button class="remove_request" id="remove-<?=$requests->vehicle_id;?>-<?=$requests->request_id;?>" title="Hide this request">Delete</button>
									<button class="view_request" title="View Request" onclick="show_details(<?=$requests->vehicle_id;?>);$.fn.tallytotal(<?=$requests->vehicle_id;?>,<?=$total_parts;?>);">View</button>
								</td>
							</tr>
						</table>
						<div style="display:<?=(($requests->vehicle_id == $display_record) ? 'block' : 'none');?>;" class="details" id="DT<?=$requests->vehicle_id;?>">
							<div class="m10 left w30">
								<div class="reg_grp ui-corner-all">
									<h2>Vehicle Details</h2>
									<table class="newuser_tbl">
										<tr>
											<th width="100">VIN:</th>
											<td><?=((strlen($requests->vin) > 1) ? $requests->vin : 'Not Provided');?></td>
										</tr>
										<tr>
											<th width="100">REG:</th>
											<td><?=((strlen($requests->reg_number) > 1) ? $requests->reg_number : 'Not Provided');?></td>
										</tr>
									</table>
								</div>
								<div class="reg_grp ui-corner-all">
									<h2>Customer Details</h2>
									<table class="newuser_tbl">
										<tr>
											<th width="100">Ref#:</th>
											<td><?=pad_ref_number($requests->vehicle_id);?></td>
										</tr>
										<tr>
											<th>Name:</th>
											<td><?=$requests->cust_name;?></td>
										</tr>
										<tr>
											<th>Phone No:</th>
											<td><?=$requests->cust_phone;?></td>
										</tr>
										<tr>
											<th>Post Code:</th>
											<td><?=$requests->post_code;?></td>
										</tr>
										<tr>
											<th>Country:</th>
											<td><?=$requests->country;?></td>
										</tr>
									</table>
								</div>
								<div class="reg_grp ui-corner-all" style="margin-top:20px;">
									<h2>Options</h2>
									<table class="newuser_tbl">
										<tr>
											<td>
												<?php 
												if($requests->saved != NULL)
												{
													if($page == 'saved')
													{
														echo '<button class="save_undo" id="unsave-'.$requests->vehicle_id.'-'.$requests->request_id.' title="Remove this request from saved">Un-Save</button>';
													} else {
														echo '<span class="_saved ui-widget"><span class="ui-icon ui-icon-check"></span>Saved</span>';
													}
												} else {
													echo '<button class="save_request" id="save-'.$requests->vehicle_id.'-'.$requests->request_id.'" title="Save this request to your account">Save</button>';
												}
												?>
											</td>
										</tr>
									</table> 
								</div>
							</div>
							<?php
							echo '
							<div style="margin-top:10px;margin-bottom:10px;" class="reg_grp ui-corner-all left w66">
								<h2>'.$requests->make_name.' '.$requests->model.' | '.$requests->registration_year.' | '. $requests->body_type.' | '.$requests->fuel_type.' | '.$requests->engine_size.'cc | '.$requests->transmission.'</h2>
								<form method="post" action="?_action=requests&_subaction=quote" id="_quoteid-'.$requests->vehicle_id.'" name="_quote-'.$requests->vehicle_id.'">
									<input type="hidden" name="_num_parts" id="'.$requests->vehicle_id.'_num_parts" value="'.$total_parts.'" />
									<input type="hidden" name="_customer" value="'.$requests->customer_id.'" />
									<input type="hidden" name="_vehicle_id" value="'.$requests->vehicle_id.'" />
									<table class="newuser_tbl">
							';
							if($parts)
							{
								for($p=1;$p<count($parts)+1;$p++)
								{
									$client_notes = ($parts[$p]['request_notes']) ? '<tr><th colspan="7">Notes from client:</th></tr><tr><td colspan="7">'.nl2br($parts[$p]['request_notes']).'</td></tr>' : '';
									$image = (file_exists('../_requests/images/'.$parts[$p]['request_id'].'.jpg')) ? '&nbsp; &nbsp;<a href="'.BASE.'_requests/images/'.$parts[$p]['request_id'].'.jpg" class="ui-state-default ui-corner-all lytebox" title="View attached image"><span class="ui-icon ui-icon-image"></span>Image</a>' : '';

									echo '
										<tr id="part_text-'.$requests->vehicle_id.'-'.$p.'">
											<th class="quoted" style="color:#000;" colspan=5">
												<input type="hidden" name="_partname'.$p.'" value="'.$parts[$p]['part_name'].'" />
												<input type="hidden" name="_part_req'.$p.'" value="'.$parts[$p]['request_id'].'" />
												Part Ref#: '.$parts[$p]['request_id'].' : <u>'.$parts[$p]['part_name'].'</u>
												'.$image.'
											</th>
										<tr/>
										'.$client_notes.'
										<tr id="part_price-'.$requests->vehicle_id.'-'.$p.'">
											<th align="center" valign="middle">&pound;</th>
											<td align="left" valign="middle">
												<input tabindex="'.$p.'" type="text" value="0.00" id="_price-'.$requests->vehicle_id.'-'.$p.'" name="_price'.$p.'" class="ui-corner-all" maxlength="10" size="5" onkeyup="$.fn.tallytotal('.$requests->vehicle_id.','.$total_parts.');" />
											</td>
											<th style="text-align:right"><span>Guarantee:</span></th>
											<td>
												<input type="text" value="3" name="_guarantee'.$p.'" class="ui-corner-all" maxlength="3" size="2">
												<select name="_guaranteeTime'.$p.'" class="ui-corner-all">
													<option value="Days">Days</option>
													<option value="Months" selected>Months</option>
													<option value="Years">Years</option>
												</select>
											</td>
											<th align="center" valign="middle">
												<select name="_condition'.$p.'" class="ui-corner-all">
													<option selected>Used</option>
													<option>New</option>
												</select>
											</th>
										</tr>';
								}
							}
							echo '
										<tr>
											<th class="delivery" colspan="5">Delivery Fee</th>
										<tr/>
										<tr>
											<th align="center" valign="middle">&pound;</th>
											<td align="left" valign="middle">
												<input tabindex="'.($p+1).'" type="text" value="10.00" id="_delivery-'.$requests->vehicle_id.'" name="_delivery_fee" class="ui-corner-all" maxlength="10" size="5" onkeyup="$.fn.tallytotal('.$requests->vehicle_id.','.($total_parts).');" />
											</td>
											<th style="text-align:right"><span>Estimate:</span></th>
											<td colspan="2">
												<input type="text" value="1" name="_delivery_date1" class="ui-corner-all" maxlength="2" size="2">
												<select name="_delivery_date2" class="ui-corner-all">
													<option value="d" selected>Day/s</option>
													<option value="w">Week/s</option>
												</select>
											</td>
										</tr>
										';
										/* TODO
										<tr>
											<th colspan="5" align="left" valign="middle">Ask customer for additional information:</th>
										</tr>
										<tr>
											<td colspan="5"><textarea name="_info" class="ui-corner-all" style="width:580px;height:20px;"></textarea></td>
										</tr>
										*/
									echo '
										<tr>
											<th colspan="5" align="left" valign="middle">Store Private Notes:</th>
										</tr>
										<tr>
											<td colspan="5"><textarea name="_private_notes" class="ui-corner-all" style="width:580px;height:40px;"></textarea></td>
										</tr>
										<tr>
											<td colspan="4" align="left" valign="middle">
												<b>Sub Total:</b> &pound;<font id="subtotal'.$requests->vehicle_id.'"></font> &nbsp; | &nbsp; 
												<b>Delivery Fee:</b> &pound;<font id="delivery'.$requests->vehicle_id.'"></font><br />
												<b>Total:</b> &pound;<font id="total'.$requests->vehicle_id.'"></font>
											</td>
											<td style="text-align:right;padding:10px;"><button id="quote-'.$requests->vehicle_id.'-'.$requests->customer_id.'" title="Quote this request now" class="do_quote">Submit Quote</button></td>
										</tr>
										';
										?>
									</table>
								</form>
							</div>
							<a href="" class="close-details" onClick="show_details(<?=$requests->vehicle_id;?>);">CLOSE</a>
						</div>
						<?php
						$i++;
						}
					} else {
						print "<td colspan=\"7\"><p>Zero Results</p></td>";
					}
					?>
                    <p><a id="chk_all" title="Check All" href="#" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-check"></span>Check all</a> &raquo;&raquo;&raquo; 
                    	<!--// <a id="remove_rqs" title="Remove Checked from your requests list" href="#" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-circle-close"></span>Remove Selected</a></p> //-->
                </div>
				</div>
            </div>
            <div id="pager_holder" style="padding-top:10px;">
            <?php
				$pager->page_size = 25;
				$pagenum = (int) (!empty($_REQUEST['_page'])) ? $_REQUEST['_page'] : 1;
				$pagenum = ($pagenum > 0) ? $pagenum : 1;
				$pagesize = (int) (!empty($_REQUEST['_pagesize'])) ? $_REQUEST['_pagesize'] : $pager->page_size;
				$pagesize = ($pagesize > 0) ? $pagesize : $pager->page_size;
                echo $pager->ShowPages(($pagesize * $pagenum) + $numrows);
            ?>
            </div>
		<?php
}

$sub_action = (!empty($_REQUEST['_subaction'])) ? $_REQUEST['_subaction'] : false;
switch($sub_action)
{
// ---------------------------
// --- PROCESS SUBMIT QUOTE
// ---------------------------
	case "quote": 

		$is_valid = true;
		$errors = array();
		$quote = array();
		
		// Check to see if the function was requested via the correct method.
		if($_SERVER['REQUEST_METHOD'] != "POST")
		{ 
			$is_valid = false; 
			$errors[] = 'There was a problem with the request method.';
		}
		else
		{
			// Validate the form data.
			if(is_numeric($_POST['_num_parts']) && $_POST['_num_parts'] > 0)
			{
				//Consumer ID
				if(is_numeric($_POST['_customer']) && $_POST['_customer'] > 0) {
					$quote['cid'] = $_POST['_customer'];
				} else { $is_valid = false; $errors[] = 'Invalid consumer ID!'; }
				
				if(is_numeric($_POST['_vehicle_id']) && $_POST['_vehicle_id'] > 0){
					$quote['vehicle_id'] = $_POST['_vehicle_id'];
				} else { $is_valid = false; $errors[] = 'Invalid vehicle ID!'; }

				//Delivery Fee
				if(is_numeric($_POST['_delivery_fee'])) {
					$quote['delivery'] = number_format($_POST['_delivery_fee'], 2, '.', '');
				} else { $quote['delivery'] = 0.00; }
				
				//Delivery Estimate
				if(is_numeric($_POST['_delivery_date1']) && $_POST['_delivery_date1'] >= 0 && $_POST['_delivery_date1'] < 100) {
					$quote['delivery_date_num'] = $_POST['_delivery_date1'];
				} else { $is_valid = false; $errors[] = 'Invalid delivery estimate, Must enter a number 0 - 99'; }
				
				$quote['delivery_date_duration'] = ($_POST['_delivery_date2'] == 'w') ? 'w' : 'd' ;
				
				//Additional information
				if(!empty($_POST['_info']))
				{
					$quote['info'] = $db->mysql_prep($_POST['_info']);
				} else {
					$quote['info'] = '';
				}
				
				//Private Notes
				if(!empty($_POST['_private_notes']))
				{
					$quote['private_notes'] = $db->mysql_prep($_POST['_private_notes']);
				} else {
					$quote['private_notes'] = '';
				}
			
				//Num Parts Requested
				$quote['num_parts'] = $_POST['_num_parts'];
				
				//Cycle Parts
				$quote['requests'] = array();
				
				$has_quoted = false;
				
				for($i=1;$i<=$quote['num_parts'];$i++)
				{
					$quote['requests'][$i]['field_id'] = $i;
					$quote['requests'][$i]['partname'] = $_POST['_partname'.$i];

					$price = $_POST['_price'.$i];
					if(is_numeric($price) || $price == '') {
						$quote['requests'][$i]['price'] = ($price == '' || $price < 0.01) ? false : $price;
					} else { $is_valid = false; $errors[] = 'Invalid Price: [ '. $part_id .' ] - Inventory ID: [ ' . $i . ' ] '; }
				
					$part_id = $_POST['_part_req'.$i];
					if(is_numeric($part_id)) {
						$quote['requests'][$i]['part_id'] = $part_id;
					} else { $is_valid = false; $errors[] = 'Invalid Part ID: [ '. $part_id .' ] - Inventory ID: [ ' . $i . ' ] '; }
					
					$guarantee = $_POST['_guarantee'.$i];
					if(is_numeric($guarantee) && $guarantee >= 0 && $guarantee < 1000) {
						$quote['requests'][$i]['guarantee_num'] = $guarantee;
					} else { $is_valid = false; $errors[] = 'Invalid Guarantee, Must enter a number 0 - 999'; }
					
					$gutime = $_POST['_guaranteeTime'.$i];
					$guvalid = array('Days','Months','Years');
					if(in_array($gutime, $guvalid)) {
						$quote['requests'][$i]['guarantee'] = $gutime;
					} else { $is_valid = false; $errors[] = 'Invalid Guarantee, Options are: Days, Months or Years'; }
					
					$condition = $_POST['_condition'.$i];
					$convalid = array('New','Used');
					if(in_array($condition, $convalid)) {
						$quote['requests'][$i]['condition'] = $condition;
					} else { $is_valid = false; $errors[] = 'Invalid Condition Selected'; }

					if($quote['requests'][$i]['price'] != false)
					{
						$has_quoted = true;
					}
				}
				
				if($has_quoted == false)
				{
					$is_valid = false;
					$errors[] = 'You have not quoted any prices!';
				}
			}
			else
			{
				$is_valid = false; 
				$errors[] = 'There where no parts listed to be quoted!';
			}
		}

		if($is_valid)
		{
			$already_quoted = array();
			foreach($quote['requests'] AS $request)
			{
				$sql = 'SELECT SQL_NO_CACHE `company_user_id` FROM `supplier_quotes_details` WHERE `company_id` = \''.$session->userdata['companyid'].'\' AND `request_id` = \''.$request['part_id'].'\' LIMIT 1';
				$quoted_by = $db->get_var($sql);
				if($quoted_by)
				{
					$already_quoted[] = $quoted_by;
				}
			}
			
			if(count($already_quoted) > 0)
			{
				die('<div id="result">Sorry, Another user from your company has already quoted this request!</div></result>');
			}
		
			//TODO SMS
			$type = ( isset($_POST['_sms']) && $_POST['_sms']==1 ) ? 't' : '';

			$rqsttime = time();

			//echo '<pre>'.print_r($quote).'</pre>';

			$q_sql = 'INSERT INTO `supplier_quotes`(`company_id`,`company_user_id`,`customer_id`,`vehicle_id`,`quote_time`,`supplier_note`,`d_fee`,`d_est`)
						VALUES(\''.$session->userdata['companyid'].'\',\''.$session->userdata['id'].'\',\''.$quote['cid'].'\',\''.$quote['vehicle_id'].'\',\''.$rqsttime.'\',\''.$quote['private_notes'].'\',\''.$quote['delivery'].'\',\''.$quote['delivery_date_num'].'-'.$quote['delivery_date_duration'].'\')';

			$db->query($q_sql);
			$qid = mysql_insert_id();
			
			if(is_numeric($qid) && $qid > 0 && strlen($quote['info']) > 1)
			{
				$db->query('INSERT INTO `supplier_messages`(`parent_id`,`client_id`,`supplier_id`,`msg_time`,`msg_text`) VALUES(\''.$qid.'\',\''.$quote['cid'].'\',\''.$session->userdata['id'].'\',\''.time().'\',\''.$quote['info'].'\')');
			}

			$quote_data = '';
			$count_quoted = 0;
			$part_ids = array();

			foreach($quote['requests'] AS $request)
			{
				if($request['price'] !== false)
				{
					$guarantee = $request['guarantee_num'].' '.$request['guarantee'];

					$qd_sql = 'INSERT INTO `supplier_quotes_details`(`company_id`,`quote_id`,`request_id`,`vehicle_id`,`company_user_id`,`quote_price`,`quote_guarantee`,`quote_condition`) 
								VALUES(\''.$session->userdata['companyid'].'\',\''.$qid.'\',\''.$request['part_id'].'\',\''.$quote['vehicle_id'].'\',\''.$session->userdata['id'].'\',\''.$request['price'].'\',\''.$guarantee.'\',\''.$request['condition'].'\')';
					$db->query($qd_sql);
					$part_ids[] = array('record' => mysql_insert_id(), 'field_id' => $request['field_id']);
					
					$quote_data .= '<li>'.$request['partname'].' - &pound;'.$request['price'].' - Condition: '.$request['condition'].' - Guarantee: '.$guarantee.'</li>';

					$count_quoted++;
				}
			}

			$cust_sql = 'SELECT SQL_NO_CACHE 
							`c`.`customer_id`,`c`.`cust_name`,`c`.`cust_username`,`c`.`cust_email`,`c`.`recieve_emails`,
							`v`.`vehicle_make_name`,`v`.`vehicle_model_name`,`v`.`registration_year`
							FROM `customer_information` AS `c`
								JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = \''.$quote['vehicle_id'].'\'
							WHERE `c`.`customer_id` = \''.$quote['cid'].'\' ';

			$cust_info = $db->get_row($cust_sql);
			$sup_data = $db->get_row('SELECT SQL_NO_CACHE `c`.`c_name`,`c`.`c_phone`,`u`.`strName` FROM `supplier_company_users` AS `u` JOIN `supplier_company` AS `c` ON `c`.`company_id` = `u`.`company_id` WHERE `u`.`company_user_id` = '.$db->mysql_prep($session->userdata['id']));

			$email_data = 'PLEASE DO NOT REPLY TO THIS EMAIL - SEE BELOW FOR YOUR LOGIN DETAILS, WHERE YOU CAN REPLY DIRECT TO THE SUPPLIER'."\n\n";
			$email_data .= 'Dear '.$cust_info->cust_name.','."\n\n";
			$email_data .= 'Thank you for using the TextSpares network to locate parts for your vehicle.'."\n".'Please find below a detailed quote for the part(s) requested'."\n\n";
			$email_data .= 'Quote Reference: '.pad_ref_number($quote['vehicle_id'])."\n";
			$email_data .= 'Vehicle Details: '.$cust_info->vehicle_make_name.' '.$cust_info->vehicle_model_name.' - '.$cust_info->registration_year."\n";
			$email_data .= "Quote Details :\n";
			$email_data .= '<ul>'.$quote_data.'</ul>';
			$email_data .= "\n\n".'Delivery Fee: '.$quote['delivery']."\n\n";
			$email_data .= "\n".'If you would like more information or would like to follow up this quote, the suppliers details are listed below.';
			$email_data .= "\n\n".'Company Name: '.$sup_data->c_name;
			//$email_data .= "\n\n".'Tel: '.$sup_data->c_phone;
			$email_data .= "\n\n".'Quote Reference: '.pad_ref_number($quote['vehicle_id']);
			$email_data .= "\n\n".'What to do next:'."\n\n";
			$email_data .= 'You can <a href="http://www.textspares.co.uk/?k='.create_emailloginkey($cust_info->customer_id).'">login</a> and see all your quotes in one place and compare them.'."\n\n";
			//$email_data .= 'Textspares.co.uk - The UKs Top Online Car Parts Network';

			if($cust_info->recieve_emails > 0)
			{
				$send = send_email($cust_info->cust_email,'noreply@textspares.co.uk','TextSpares - You have recieved a new quote!',$email_data,true);
				if(!$send == true) echo $send;
			}
			
			$merge = array();
			foreach($part_ids AS $pid)
			{
				$merge[] = $pid['field_id'].'-'.$pid['record'];
			}
			
			echo ($count_quoted < $_POST['_num_parts']) ? '<div id="result">Partial</div><div id="quoteid">'.$qid.'</div><div id="quoted">'.implode(',',$merge).'</div>' : '<div id="result">Success</div><div id="quoteid">'.$qid.'</div>';

		} else {
			echo '<div id="result">The following errors occoured while processing your quote:'."\n";
			foreach($errors AS $error) {
				echo ' - '.$error."\n";
			}
			echo '</div>';
		}
		
	break;
// ---------------------------
// --- PROCESS SAVE QUOTE
// ---------------------------
	case "save":

		$vehicle_id = $db->mysql_prep($_REQUEST['_vehicle_id']);
		$sid = $session->userdata['id'];

		$exists = $db->get_var('SELECT SQL_NO_CACHE COUNT(*) FROM `tblSavedRequests` WHERE `vehicle_id` = \''.$vehicle_id.'\' AND `company_user_id` = \''.$sid.'\'');
		if($exists == 0)
		{
			$sql = 'INSERT INTO `tblSavedRequests`(`vehicle_id`,`company_user_id`) VALUES(\''.$vehicle_id.'\',\''.$sid.'\')';
			$db->query($sql);
			echo '<div id="result">Success</div>';
		} else {
			echo '<div id="result">This request is already saved to your account</div>';
		}
	break;
// ---------------------------
// --- PROCESS UNSAVE QUOTE
// ---------------------------
	case "saveundo":

		$vehicle_id = $db->mysql_prep($_REQUEST['_vehicle_id']);
		$sid = $session->userdata['id'];

		$delete = $db->query('DELETE FROM `tblSavedRequests` WHERE `vehicle_id` = \''.$vehicle_id.'\' AND `company_user_id` = \''.$sid.'\' LIMIT 1');
		if($delete) {	
			echo '<div id="result">Success</div>';
		} else {
			echo '<div id="result">There was a problem undoing the save state.</div>';
		}

	break;
// ---------------------------
// --- PROCESS HIDE REQUEST
// ---------------------------
	case "remove":
	
		$request_id = (int) (!empty($_REQUEST['request_id'])) ? $_REQUEST['request_id'] : 0;
		if($request_id > 0)
		{
			$sql = 'SELECT SQL_NO_CACHE COUNT(*) FROM `tblRequestsRemoved` WHERE `request_id` = '.$request_id.' AND `company_user_id` = '.$session->userdata['companyid'].'';
			if($db->get_var($sql) == 0)
			{
				$sql = 'INSERT INTO `tblRequestsRemoved`(`request_id`,`company_user_id`) VALUES('.$db->mysql_prep($request_id).','.$session->userdata['companyid'].')';
				$result = $db->query($sql);

				if($result) {
					echo '<div id="result">Success</div>';
				} else {
					echo '<div id="result">There was a problem removing the quote.</div>';
				}
			} else {
				echo '<div id="result">Success</div>';
			}
		}
		
		$vehicle_id = (int) (!empty($_REQUEST['vehicle_id'])) ? $_REQUEST['vehicle_id'] : 0;
		$sid = $session->userdata['id'];
		
		if($vehicle_id > 0)
		{
			$saved = $db->get_row('SELECT SQL_NO_CACHE * FROM `tblSavedRequests` WHERE `vehicle_id` = \''.$vehicle_id.'\' AND `company_user_id` = \''.$sid.'\'');
			if($saved)
			{
				$sql = 'DELETE FROM `tblSavedRequests` WHERE `vehicle_id` = \''.$vehicle_id.'\' AND `company_user_id` = \''.$sid.'\' LIMIT 1';
				$db->query($sql);
			}
		}
	break;
	
	default:
		latest_requests($db);
	break;
}

?>
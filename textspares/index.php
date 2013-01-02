<?php
define('IN_TEXTSPARES',true);
include('include/common.inc.php');
top();
common_middle();
?>
<script type="text/javascript">	
$(document).ready(function() {
	$fbegin = 'Enter your part name here';
	$fanother = 'Enter another part name';
	$fvalue = $('#find').val();
	function find_part_input() {
		if($fvalue == '') {
			$('#find').val($fbegin);
			$('#find').addClass('grey');
		}
	}
	find_part_input();
	function addPart(value) {
		$('#partslist').append('<a href="javascript:$.fn.removePart(\'pn'+$partNum+'\');" id="pn'+$partNum+'" title="Remove this part"><div><input type="hidden" name="parts[]" value="' + value + '" />' + value + '</div></a>');
		setTimeout(function () { $('#find').addClass('grey');$('#find').val($fanother); }, 1000);
		$partNum++;
	}
	$partsList = new Array();
	$partNum = 0;
	$('#parts > option').each(function(i) {
		$partsList[i] = $(this).text();
	});
	$('#find').autocomplete({
		source: $partsList,
		select: function(event,ui) {
			addPart(ui.item.value);
		}
	});
	$.fn.removePart = function(e) {
		$('#'+e).remove();
	}
	$('#find').focus(function() {
		$fbegin = 'Enter your part name here';
		$fanother = 'Enter another part name';
		$fvalue = $('#find').val();
		if($fvalue == $fbegin || $fvalue == $fanother) {
			$('#find').removeClass('grey');
			$('#find').val('');
		}
	});
	$('#find').click(function() {
		$fbegin = 'Enter your part name here';
		$fanother = 'Enter another part name';
		$fvalue = $('#find').val();
		if($fvalue == $fbegin || $fvalue == $fanother) {
			$('#find').removeClass('grey');
			$('#find').val('');
		}
	});
	$('#find').keydown(function() {
		$fbegin = 'Enter your part name here';
		$fanother = 'Enter another part name';
		$fvalue = $('#find').val();
		if($fvalue == $fbegin || $fvalue == $fanother) {
			$('#find').removeClass('grey');
			$('#find').val('');
		}
	});
	$('#find').keypress(function(event) {
		if(event.which == 13) {
			event.preventDefault();
			if($('#find').val().length > 1) {
				addPart($('#find').val());
			}
		}
	});
});
</script>
<div class="welcome">
	<h1>Welcome to the Text Spares Website</h1>
	<h3><?=($meta_make ? ' <b>'.$meta_make.'</b> vehicle' : 'The Place for All Car');?> Parts, Engines and Spares<?=($meta_area ? ', <b>'.$meta_area.'</b>' : '');?>.</h3><br/>
	<h2>What is Text Spares?</h2><p><u>Text Spares</u> is a<?=($meta_make ? 'n '.$meta_make_link : '');?> vehicle spare parts quotation website.<br/>When you need a part for your<?=($meta_make ? ' <b>'.$meta_make.'</b>' : ' vehicle');?> simply make a <?=($meta_make ? '<a href="'.BASE.'FreeQuotes/'.$meta_make.($meta_area ? '/'.$meta_area : '').'" title="FREE '.$meta_make.' Car Part Quotations">free request</a>' : 'free request');?> for the parts you need and it will be sent out to our many suppliers who will then give you their best prices. You can then choose the best quotations and order the parts you want from our web site from multiple suppliers.</p>
</div>
<form name="regenter" method="post" action="<?=BASE;?>partrequest.php">
<div class="regcheck">
	<h1>Get <b>FREE</b><?=($meta_make ? ' '.$meta_make : ' car part');?> quotes now!</h1>
	<h2>Enter the Part Names &amp; Vehicle Registration below to get started..</h2>
	<div class="hint"><div class="title">HINT:</div><div class="text"> Press "Enter" on your keyboard to add your own part name.</div></div>
	<div class="partsearch">
		<input type="text" name="rawpart" value="" id="find" />
		<select name="listedparts" id="parts">
<?php
$sql = 'SELECT `part_id`,`part_cat_name` FROM `parts_categories` WHERE 1';
$parts = $db->get_results($sql);
foreach($parts AS $part)
{
	echo '<option value="'.$part->part_id.'">'.$part->part_cat_name.'</option>';
}
?>
		</select>
	</div>
	<div id="partslist"></div>
	<div class="regplate"><input type="text" name="reg_number" value="" class="regplate" maxlength="8" /><input type="submit" name="reg_check" value="" class="reggo" /></div>
	<h2><a href="javascript:document.regenter.submit();" class="onblue">DON'T KNOW THE VEHICLE REG? DON'T WORRY JUST PRESS GO!</a></h2>
</div>
</form>
<div class="welcome">
	<p><h2>Who uses Text Spares?</h2>Anyone can use our website to request parts, Consumers, Garages and Traders alike.</p>
	<p><h2>How much does this service cost?</h2>Our service is absoloutly <u>free</u> for anyone requesting and purchasing parts.<br/>You won't pay a penny more than prices quoted to you from our partner suppliers.<br/>No hidden costs!</p>
</div>
<br clear="all" />
<div style="float:left;position:relative;">We have a top selection of spares from recently-dismantled vehicles, with all manner of specialist parts available including gearboxes and engines. You will also be able to view a number of damage repairable vehicles which are offered at excellent discount prices.</div>
<br clear="all" />
<br/>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><img src="<?=BASE;?>images/h-recent.gif" width="551" height="29" alt="Recent Part Requests" /></td>
	</tr>
	<tr>
		<td height="139" style="background:url(<?=BASE;?>images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff" valign="top">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
				<tr>
					<td>
						<table cellpadding="1" cellspacing="1" width="100%" id="TBorder">
							<tr><td class="Cheader">Time</td><td class="Cheader">Make</td><td class="Cheader">Model</td><td class="Cheader">Part</td></tr>
							<?
							$sqlreq = mysql_query('SELECT 
													`cr`.`customer_id`,
													`cr`.`part_name`,
													`cv`.`vehicle_make_name`,
													`cv`.`vehicle_model_name`,
													`cr`.`request_stamp`
												FROM `customer_requests` AS `cr`
												JOIN `customer_vehicles` AS `cv` ON `cv`.`vehicle_id` = `cr`.`vehicle_id`
												ORDER BY `cr`.`request_stamp` DESC LIMIT 10')or die(mysql_error());
							if(mysql_num_rows($sqlreq)>0)
							{
								while($rdata=mysql_fetch_array($sqlreq))
								{
									echo '<tr><td>'.date('H:i',$rdata['request_stamp']).'</td><td><a href="'.BASE.'CarSpares/'.$rdata['vehicle_make_name'].($meta_area ? '/'.$meta_area : '').'" title="'.$rdata['vehicle_make_name'].' Car Spares">'.$rdata['vehicle_make_name'].'</a></td><td>'.$rdata['vehicle_model_name'].'</td><td>'.$rdata['part_name'].'</td></tr>';
								}
								mysql_free_result($sqlreq);
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><img src="<?=BASE;?>images/footerpart.gif" width="551" height="12" alt="Recent Part Requests"/></td>
	</tr>
</table>
<br clear="all" />
<div style="font-size:12px;margin-top:15px;">
	<h1 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;">Used <?=($meta_make ? $meta_make : 'Car');?> Parts<?=($meta_area ? ' '.$meta_area : '');?></h1>

	<p>Text Spares are the premier dealers in <strong>used car parts</strong> in <?=($meta_area ? '<a href="'.BASE.'CarSpares/'.$meta_area.'" title="Car Spares '.$meta_area.'">'.$meta_area.'</a>' : 'the United Kingdom');?>. Whether it is <?=($meta_make ? $meta_make.', ' : '');?>Citroen, Mazda, Mercedes or Audi, Text Spares has a comprehensive selection of <a href="<?=BASE?>Details/2546/Used-Car-Parts-">used car  parts</a>. Choose Text Spares UK for total car maintenance solutions.</p>

	<h2 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;"><?=($meta_make ? $meta_make : 'Car');?> Spares<?=($meta_area ? ' '.$meta_area : '');?></h2>
	<p>Text Spares <strong>car spares</strong> for almost all models available in <?=($meta_area ? '<a href="'.BASE.'CarSpares/'.$meta_area.'" title="Car Spares '.$meta_area.'">'.$meta_area.'</a>' : 'the United Kingdom');?>. If you need car spares instantly, you should check out the selection at Text Spares for the best deals on high quality <a href="<?=BASE?>Details/2544/Car-Spares">car spares</a>.</p>

	<h2 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;"><?=($meta_make ? $meta_make : 'Car');?> Parts<?=($meta_area ? ' '.$meta_area : '');?></h2>
	<p>Text Spares are the number one <strong>car parts</strong> dealers in the United Kingdom, with a large clientele built over the years. If you need <a href="<?=BASE?>MakeDetails/2552/Car-Parts">car parts</a> for Mazda, Audi or any other car, choose Text Spares for genuine, high quality products. </p>

	<h2 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;"><?=($meta_make ? $meta_make : 'Mitsubishi');?> Car Parts<?=($meta_area ? ' '.$meta_area : '');?></h2>
	<p>At Text Spares you will find genuine <strong><?=($meta_make ? $meta_make : 'Mitsubishi');?> car parts</strong> at the most competitive rates. Text Spares has all the relevant <?=($meta_make ? $meta_make_link.' car parts' : '<a href="'.BASE.'MakeDetails/1596/Mitsubishi">Mitsubishi car parts</a>');?>, right from spoilers to gliders. Choose Text Spares if you require <?=($meta_make ? $meta_make : 'Mitsubishi');?> car parts old or new. </p>

	<h2 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;">Mazda Car Parts<?=($meta_area ? ' '.$meta_area : '');?></h2>
	<p>If you want <strong>Mazda car parts</strong> for your Mazda, be sure to check out the inventory of <a href="<?=BASE?>MakeDetails/1210/Mazda">Mazda car parts</a> at Text Spares. Text Spares has all Mazda car parts of current running models and even many of the models that are no longer in production. </p>

	<h2 style="margin: 0px; padding: 0px; font-size: 14px; text-align: left;">Car Salvage<?=($meta_area ? ' '.$meta_area : '');?></h2>
	<p>Text Spares can help you find the best <strong>car salvage</strong> options which will help make your car as good as new using spare parts. Be smart and choose Text Spares for all kinds of <a href="<?=BASE?>Details/2542/Car-Salvage">car salvage</a> services, with competitive prices.</p>
</div>
<?php bottom(); ?>      
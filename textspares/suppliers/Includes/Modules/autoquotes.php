<?php
if(!defined('IN_TEXTSPARES')) { exit; }

$input = array();
$input['user'] = $session->userdata['id'];
$input['company'] = $session->userdata['companyid'];
$input['make'] = (int) (!empty($_POST['_make'])) ? $db->mysql_prep($_POST['_make']) : 0;
$input['model'] = (int) (!empty($_POST['_model'])) ? $db->mysql_prep($_POST['_model']) : 0;
$input['part'] = (int) (!empty($_POST['_part'])) ? $db->mysql_prep($_POST['_part']) : 0;
$input['value'] = (double) (!empty($_POST['_value'])) ? $db->mysql_prep($_POST['_value']) : 0.00;
$input['delivery'] = (double) (!empty($_POST['_delivery'])) ? $db->mysql_prep($_POST['_delivery']) : 0.00;
$input['condition'] = (!empty($_POST['_condition'])) ? $db->mysql_prep($_POST['_condition']) : false;
$input['guarantee'] = (int) (!empty($_POST['_guarantee'])) ? $db->mysql_prep($_POST['_guarantee']) : 3;
$input['guarantee_time'] = (!empty($_POST['_guaranteeTime'])) ? $db->mysql_prep($_POST['_guaranteeTime']) : 'Months';

function save_autoQuote($db)
{
	global $session,$input;
	
	$insert = true;
	$extend = '';
	if(!$input['make'] > 0)
	{
		$insert = false;
		$extend = ' &nbsp; <b>You have not selected a Vehicle Make.</b>';
	}
	elseif(!$input['model'] > 0)
	{
		$insert = false;
		$extend = ' &nbsp; <b>You have not selected a Vehicle Model.</b>';
	}
	elseif(!$input['part'] > 0)
	{
		$insert = false;
		$extend = ' &nbsp; <b>You have not selected a Part.</b>';
	}
	elseif(!(str_replace('.','',$input['value']) > 0))
	{
		$insert = false;
		$extend = ' &nbsp; <b>Please enter a Price Value.</b>';
	}
	if($insert == true)
	{
		$sql = 'SELECT COUNT(*) FROM `tblAutoQuotes` WHERE `company_user_id` = '.$input['user'].' AND `vehicle_make` = '.$input['make'].' AND `vehicle_model` = '.$input['model'].' AND `part_id` = '.$input['part'].'';
		$exists = $db->get_var($sql);
		if($exists > 0)
		{
			$insert = false;
			$extend = ' &nbsp; <b>An Auto Quote already exists for this Vehicle Make,Model and Part.</b>';
		}
		if($insert == true)
		{
			$sql = 'INSERT INTO `tblAutoQuotes`(`vehicle_make`,`vehicle_model`,`part_id`,`decValue`,`delivery_fee`,`company_id`,`company_user_id`,`strCondition`,`strGuarantee`) VALUES('.$input['make'].','.$input['model'].','.$input['part'].','.$input['value'].','.$input['delivery'].','.$input['company'].','.$input['user'].',\''.$input['condition'].'\',\''.$input['guarantee'].' '.$input['guarantee_time'].'\')';
			$insert = $db->query($sql);
		}
	}
	if($insert == true)
	{
	 ?>
		<div class="ui-widget">
			<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
				<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
				<strong>Success!</strong> Setting Saved.</p>
			</div>
		</div>
		<br />
	<?php   
	} else {
	?>
		<div class="ui-widget">
			<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all"> 
				<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span> 
				<strong>Alert:</strong> There was a problem saving your setting.<?=$extend;?></p>
			</div>
		</div>
		<br />
	<?php	
	}
}

function delete_autoQuote($db)
{
	global $session;

	$id = $db->mysql_prep($_POST['_id']);
	$sql = 'DELETE FROM `tblAutoQuotes` WHERE `company_user_id` = '.$session->userdata['id'].' AND `auto_quote_id` = '.$id.' LIMIT 1';
	//print($sql);
	$insert = $db->query($sql);
	if($insert)
	{
	 ?>
		<div class="ui-widget">
			<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
				<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
				<strong>Success!</strong> Setting Deleted.</p>
			</div>
		</div>
		<br />
	<?php   
	} else {
	?>
		<div class="ui-widget">
			<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all"> 
				<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span> 
				<strong>Alert:</strong> There was a problem removing your setting.</p>
			</div>
		</div>
		<br />
	<?php	
	}
}

function display_settings($db)
{
	global $session,$input;
	if(!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
		if($_POST['req'] == "save"){ save_autoQuote($db); }
		if($_POST['req'] == "delete"){ delete_autoQuote($db); }
	}

	?>
	<div class="reg_grp ui-corner-all">
	<h2>New AutoQuote Setting</h2>
	<form name="_saveQuote" action="<?=$_SERVER['REQUEST_URI'];?>" method="post">
	<input type="hidden" name="req" value="save" />
	<p style="overflow:hidden; padding:10px;">
	<span id="filters" style="float:none; text-align:left;">
	<label for="_make">Make:</label>
	<select id="_make_drop" name="_make" style="width:120px;">
	<option value="0">[Please Select]</option>
	<?
	foreach($db->get_results("SELECT `make_name`,`make_id` FROM `vehicle_makes` WHERE `parent_id` = 0 AND `disp` = 'y' ORDER BY `make_name` ASC") as $make){ ?><option value="<?=$make->make_id;?>" <?=($make->make_id == $input['make']) ? 'selected="selected"' : '';?>><?=$make->make_name;?></option><?php } ?>
	</select>
	<script type="text/javascript">
	$(document).ready(function()
	{
		$make = <?=$input['make'];?>;
		if($make > 0)
		{
			$("#_model_drop option").css("display","none");
			$("#_model_drop option:first-child").css("display","block");
			$("#_model_drop option[rel="+$make+"]").css("display","block");
			$("#_model_drop").val(<?=$input['model'];?>);
		}
	});
	</script>
	<label for="_model">&nbsp;Model:</label>
	<select id="_model_drop" name="_model" style="width:120px;">
	<option value="0">[Please Select]</option>
	<?
	foreach($db->get_results("SELECT `make_name`,`make_id`,`parent_id` FROM `vehicle_makes` WHERE `parent_id` > 0 AND `disp` = 'y' ORDER BY `parent_id` ASC") as $model){ 
	?><option value="<?=$model->make_id;?>" rel="<?=$model->parent_id;?>" style="display:none;"><?=$model->make_name;?></option><?php } ?>
	</select>

	<label for="_part">&nbsp;Part:</label>
	<select id="_part_drop" name="_part" style="width:230px;">
	<option value="0">[Please Select]</option>
	<?
	foreach($db->get_results("SELECT `part_id`,`part_cat_name` FROM `parts_categories` ORDER BY `part_cat_name` ASC") as $parts){ ?><option value="<?=$parts->part_id;?>" <?=($parts->part_id == $input['part']) ? 'selected="selected"' : '';?>><?=$parts->part_cat_name;?></option><?php } ?>
	</select>	 

	<label for="_value">&nbsp;&nbsp;Value: &pound;</label>
	<input type="text" name="_value" value="<?=$input['value'];?>" style="width:70px;" value="0.00" maxlength="8" />
	</span>
	</p>
	<p> 
	<span id="filters" style="float:none; text-align:left;">
	<label for="_guarantee">&nbsp;&nbsp;Guarantee:</label>
	<input type="text" maxlength="10" class="ui-corner-all" name="_guarantee" value="<?=(!empty($_POST['_guarantee'])) ? $_POST['_guarantee'] : 3;?>" />

	<select class="ui-corner-all" name="_guaranteeTime">
	<option<?=($input['guarantee_time'] == "Days") ? " selected=\"selected\"" : ""; ?>>Days</option>
	<option<?=($input['guarantee_time'] == "Months" || !$_guaranteeTime) ? " selected=\"selected\"" : ""; ?>>Months</option>
	<option<?=($input['guarantee_time'] == "Years") ? " selected=\"selected\"" : ""; ?>>Years</option>
	</select>

	<label for="_condition">&nbsp;&nbsp;Condition:</label>
	<select class="ui-corner-all" name="_condition">
	<option<?=($input['condition'] == "New") ? " selected=\"selected\"" : ""; ?>>New</option>
	<option<?=($input['condition'] == "Used" || !$input['condition']) ? " selected=\"selected\"" : ""; ?>>Used</option>
	</select>
	
	<label for="_delivery">&nbsp;&nbsp;Delivery Fee: &pound;</label>
	<input type="text" name="_delivery" style="width:70px;" value="<?=$input['delivery'];?>" maxlength="8" />
	
	&nbsp;&nbsp;<a href="#" class="ui-state-default ui-corner-all" style="padding:5px;padding-left:20px;" id="_addQuote"><span class="ui-icon ui-icon-plus"></span>Add</a>
	</span>
	</p>
	</form>
	</div>
    
  	<br>
    <form name="_deleteQuote"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="req" value="delete" />
    <input type="hidden" name="_id" value="" />
    </form>
    <div class="request_tbl ui-corner-all reg_grp">	
        <table>
            <tr>
                <th>Make</th>
                <th>Model</th>
                <th>Part</th>
                <th>Condition</th>
                <th>Gaurantee</th>
				<th>Delivery</th>
                <th>Value</th>
                <th>Remove</th>
            </tr> 
            <?php
			$sql = "SELECT *, make.make_name type FROM `tblAutoQuotes` AS `aq`
							INNER JOIN vehicle_makes make
							ON aq.vehicle_make = make.make_id
							INNER JOIN vehicle_makes model
							ON aq.vehicle_model = model.make_id
							INNER JOIN parts_categories parts
							ON aq.part_id = parts.part_id
					WHERE aq.company_user_id = ".$session->userdata['id'];
			//print("<pre>$sql</pre>");
					
			if($autoq = $db->get_results($sql))
			{
				foreach($autoq as $autoq)
				{
					?>
                    <tr>
                    	<td style="padding:10px;"><?php echo $autoq->type; ?></td>
                        <td><?=$autoq->make_name;?></td>
                        <td><?=$autoq->part_cat_name;?></td>
                        <td><?=$autoq->strCondition;?></td>
                        <td><?=$autoq->strGuarantee;?></td>
						<td>&pound;<?=$autoq->delivery_fee;?></td>
                        <td>&pound;<?=number_format($autoq->decValue,2);?></td>
                        <td style="text-align:center;"><a style="padding-left: 10px;" rel="<?=$autoq->auto_quote_id;?>" title="Delete this from your settings" href="#" class="_remove ui-state-default ui-corner-all"><span class="ui-icon ui-icon-closethick"></span></a></td>
                        	
					<?php	
				}
			} else {
				?><tr><td colspan="5"><p>No current AutoQuote settings</p></td></tr><?php
			}
			?>
    	</table>
    </div>    
   
    <?php
}
?>
<script type="text/javascript">
<!--
$(function(){
	//filter drop down		
	$("#_make_drop").change(function(){
		var $make = $("#_make_drop option:selected").val();
		//alert($make);
		$("#_model_drop option").css("display","none");
		$("#_model_drop option:first-child").css("display","block");
		$("#_model_drop option[rel="+$make+"]").css("display","block");
		$("#_model_drop").val(0);
	});		
	// Validate & Submit new setting
	$("#_addQuote").click(function(e){
		e.preventDefault();
		var curTest = new RegExp(/^\$?\d+(\.\d{2})?$/);
		if($("#_make_drop").val() == 0 || $("#_model_drop").val() == 0 || $("#_part_drop").val() == 0){ alert("You must select an option from all sections"); return; } else if (curTest.test($("input[name=_value]").val())==false||$("input[name=_value]").val()=="0.00"){ alert("You must enter a value in the format 0.00 which is greater than zero."); return; }
		$("form[name=_saveQuote]").submit();
	});
	// Submit removal form	
	$("._remove").click(function(e){
		e.preventDefault();
		$("input[name=_id]").val($(this).attr("rel"));
		$("form[name=_deleteQuote]").submit();
	});
});
-->
</script>
<h1><img style="vertical-align: middle;" alt="AutoQuotes" width="35" height="35" src="<?=ROOT;?>Images/settings.png">AutoQuote Settings</h1>
<br clear="all"/>
<div class="ui-widget">
	<div style="margin: 20px 0px 20px 0; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
		<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>	The AutoQuote Settings allow you to configure automated quotations to customers as soon as they make a request which matches your criteria.</p>
		<p>Simply select a make / model &amp; part, then enter the value you wish to quote for this part in the format 0.00. For each setting you have saved to your account an automated quote will be sent to customers matching your setting until otherwise removed from your settings.</p>
	</div>
</div>
    
<?php display_settings($db); ?>
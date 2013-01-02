<?php

if(!defined('IN_TEXTSPARES')) { exit; }

function save_alert()
{
	global $session, $db;
	
	$user = $db->mysql_prep($session->userdata['id']);
	$make = (int) $db->mysql_prep($_POST['_make']);
	$model = (int) $db->mysql_prep($_POST['_model']);
	$part = (int) $db->mysql_prep($_POST['_part']);
	
	$insert = true;
	$extend = '';
	
	if(!$make > 0)
	{
		$insert = false;
		$extend = ' &nbsp; <b>You have not selected a Vehicle Make</b>';
	}
	
	if($insert == true)
	{
	
		if($model == 0)
		{
			$sql = 'SELECT COUNT(*) FROM `tblRequestAlertSettings` WHERE `company_user_id` = '.$user.' AND `vehicle_make` = '.$make.'';
			$exists = $db->get_var($sql);
			if($exists > 0)
			{
				$insert = false;
				$extend = '&nbsp; <b>Cant create alert for Any Model when other alerts are already set up for specific Models.</b>';
			}
		}
		elseif($part == 0)
		{
			$sql = 'SELECT COUNT(*) FROM `tblRequestAlertSettings` WHERE `company_user_id` = '.$user.' AND `vehicle_model` > 0';
			$exists = $db->get_var($sql);
			if($exists > 0)
			{
				$insert = false;
				$extend = '&nbsp; <b>Cant create alert for Any Part when other alerts are already set up for specific Parts.</b>';
			}
		}
	
		$sql = 'SELECT COUNT(*) FROM `tblRequestAlertSettings` WHERE `company_user_id` = '.$user.' AND `vehicle_make` = '.$make.' AND `vehicle_model` = 0 AND `part_id` = 0';
		$exists = $db->get_var($sql);
		
		if($exists > 0)
		{
			$insert = false;
			$extend = '&nbsp; <b>An alert is setup for everything relating to this Make.</b>';
		}

		$sql = 'SELECT COUNT(*) FROM `tblRequestAlertSettings` WHERE `company_user_id` = '.$user.' AND `vehicle_make` = '.$make.' AND `vehicle_model` = '.$model.' AND `part_id` = 0';
		$exists = $db->get_var($sql);
		
		if($exists > 0)
		{
			$insert = false;
			$extend = '&nbsp; <b>An alert is setup for everything relating to this Make &amp; Model.</b>';
		}
			
		$sql = 'SELECT COUNT(*) FROM `tblRequestAlertSettings` WHERE `company_user_id` = '.$user.' AND `vehicle_make` = '.$make.' AND (`vehicle_model` = '.$model.' OR `vehicle_model` = 0) AND `part_id` = '.$part.'';
		$exists = $db->get_var($sql);
		
		if($exists > 0)
		{
			$insert = false;
			$extend = '&nbsp; <b>An alert is setup for this Make, Model &amp; Part.</b>';
		}
	
		if($insert == true)
		{
			$sql = 'INSERT INTO `tblRequestAlertSettings` (`vehicle_make`,`vehicle_model`,`part_id`,`company_user_id`) VALUES ('.$make.','.$model.','.$part.','.$user.')';
			$insert = $db->query($sql);
		}
	}
	
	if($insert == true) {
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
					<strong>Alert:</strong> There was a problem saving your setting. <?=$extend;?></p>
				</div>
			</div>
            <br />
        <?php	
		}
	}


function delete_alert()
{
	global $session, $db;

	$id = $db->mysql_prep($_POST['_id']);
	$sql = "DELETE FROM tblRequestAlertSettings WHERE company_user_id = ".$db->mysql_prep($session->userdata['id'])." AND auto_alert_id = $id";
	//print($sql);
	$insert = $db->query($sql);
	if($insert){
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

function display_settings()
{
	global $session, $db;

	if(isset($_POST['_subaction']) && $_POST['_subaction'] == 'save'){ save_alert($db); }
	if(isset($_POST['_subaction']) && $_POST['_subaction'] == 'delete'){ delete_alert($db); }

	?>
     <div class="reg_grp ui-corner-all">
    	<h2>New Alert Setting</h2>
        <form name="_saveAlert"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="_subaction" value="save" />
    	 <p style="overflow:hidden; padding:10px;">
			<span id="filters" style="float:none; text-align:left;">
				<label for="_make">Make:</label>
				<select id="_make_drop" name="_make" style="width:120px;">
				<option value="0">[Please Select]</option>
				<?
				foreach($db->get_results("SELECT make_name,make_id FROM vehicle_makes WHERE parent_id='0' AND disp='y' ORDER BY make_name ASC") as $make){ ?><option value="<?=$make->make_id;?>"><?=$make->make_name;?></option><?php } ?>
				</select>

				<label for="_model">&nbsp;Model:</label>
				<select id="_model_drop" name="_model" style="width:120px;">
				<option value="0">Any Model</option>
				<?
				foreach($db->get_results("SELECT make_name,make_id,parent_id FROM vehicle_makes WHERE parent_id > 0 AND disp='y' ORDER BY parent_id ASC") as $model){ 
				?><option value="<?=$model->make_id;?>" rel="<?=$model->parent_id;?>" style="display:none;"><?=$model->make_name;?></option><?php } ?>
				</select>

				<label for="_part">&nbsp;Part:</label>
				<select id="_part_drop" name="_part" style="width:230px;">
				<option value="0">Any Part</option>
				<?
				foreach($db->get_results("SELECT part_id pid, part_cat_name p  FROM parts_categories ORDER BY p ASC") as $parts){ ?><option value="<?=$parts->pid;?>"><?=$parts->p;?></option><?php } ?>
				</select>
				<button id="_addAlert">Add</button>
			</span>
       </p>
       </form>
    </div>
    
  	<br>
    <form name="_deleteAlert"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="_subaction" value="delete" />
    <input type="hidden" name="_id" value="" />
    </form>
    <div class="request_tbl ui-corner-all reg_grp">	
        <table>
            <tr>
                <th>Make</th>
                <th>Model</th>
                <th>Part</th>
                <th width="60">Remove</th>
            </tr>
            <?php
			$sql = 'SELECT
						`ras`.`auto_alert_id`,
						`ras`.`part_id`,
						`model`.`make_id` AS `model_id`,
						`make`.`make_name` AS `type`,
						`model`.`make_name` AS `model`,
						`parts`.`part_cat_name`
					FROM `tblRequestAlertSettings` AS `ras`
					LEFT JOIN `vehicle_makes` AS `make` ON `make`.`make_id` = `ras`.`vehicle_make`
					LEFT JOIN `vehicle_makes` AS `model` ON `model`.`make_id` = `ras`.`vehicle_model`
					LEFT JOIN `parts_categories` AS `parts` ON `parts`.`part_id` = `ras`.`part_id`
				WHERE `ras`.`company_user_id` = \''.$db->mysql_prep($session->userdata['id']).'\'';
			if($alerts = $db->get_results($sql))
			{
				foreach($alerts as $alerts)
				{
				?>
                    <tr>
                    	<td style="padding:10px;"><?=$alerts->type;?></td>
                        <td><?=(($alerts->model_id > 0) ? $alerts->model : 'Any Model');?></td>
                        <td><?=(($alerts->part_id > 0) ? $alerts->part_cat_name : 'Any Part');?></td>
                        <td style="text-align:center;"><a style="padding:3px 10px;" rel="<?=$alerts->auto_alert_id;?>" title="Delete this from your settings" href="#" class="_remove ui-state-default ui-corner-all"><span class="ui-icon ui-icon-closethick"></span></a></td>
					</tr>
				<?php	
				}
			} else {
				echo '<tr><td colspan="5"><p>'.((!mysql_error()) ? 'No current alert settings' : mysql_error()).'</p></td></tr>';
			}
			?>	
    	</table>
    </div>    
   
    <?php
}
?>
<script type="text/javascript">
$(document).ready(function() {
	//filter drop down		
	$("#_make_drop").change(function(){
		var $make = $("#_make_drop option:selected").val();
		//alert($make);
		$("#_model_drop option").css("display","none");
		$("#_model_drop option:first-child").css("display","block");
		$("#_model_drop option[rel="+$make+"]").css("display","block");
		$("#_model_drop").attr("selectedIndex",0);
	});		

	// Validate & Submit new setting
	$("#_addAlert").click(function(e){
		e.preventDefault();
		if($("#_make_drop").val() > 0 || $("#_model_drop").val() > 0)
		{
			$("form[name=_saveAlert]").submit();
		} else {
			alert("You must select a Vehicle Make.");
		}
	});
	
	// Submit removal form	
	$("._remove").click(function(e){
		e.preventDefault();
		$("input[name=_id]").val($(this).attr("rel"));
		$("form[name=_deleteAlert]").submit();
	});
});
</script>
 <h1><img style="vertical-align: middle;" alt="AutoQuotes" width="35" height="35" src="<?=ROOT;?>Images/settings.png">Request Alert Settings</h1>
 <br clear="all"/>
<div class="ui-widget">
	<div style="margin: 20px 0px 20px 0; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
		<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span> The Request Alert Settings allow you to configure automated email alerts for specific part requests received on the site.</p>
		<p>Simply select a make / model &amp; part  for which you wish to be alerted about. Once added to your settings you will receive an email alert whenever a user submits a new part request matching your criteria.</p>
	</div>
</div>        
<?php
display_settings();
?>
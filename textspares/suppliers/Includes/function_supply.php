<?php
if(!defined('IN_TEXTSPARES')) exit;

function print_search_bar($float = false)
{
	$style = '';
	if($float)
	{
		$style = 'position:inherit;top:15px;right:5px;';
	}
	echo '
	<div id="search_bar" style="float:right;'.$style.'">
		<form name="search_form" action="?_action=search" method="POST">
			<label for="search_by">Search:</label>
			<select name="search_by">
				<option value="ref" '.((!empty($_REQUEST['search_by']) && $_REQUEST['search_by'] == 'ref') ? 'selected' : '').'>Refrence No.</option>
				<option value="name" '.((!empty($_REQUEST['search_by']) && $_REQUEST['search_by'] == 'name') ? 'selected' : '').'>Customer Name</option>
				<option value="pcode" '.((!empty($_REQUEST['search_by']) && $_REQUEST['search_by'] == 'pcode') ? 'selected' : '').'>Post Code</option>
			</select>
			<input type="text" name="search" value="'.((!empty($_REQUEST['search'])) ? urldecode($_REQUEST['search']) : '').'">
			<a href="#" onclick="document.search_form.submit();" class="ui-state-default ui-corner-all" style="font-size:10px;"><span class="ui-icon ui-icon ui-icon-grip-dotted-horizontal"></span>Search</a>
		</form>
	</div>
	';
}

// Function calls all Available Access Level and Account Standing Options down into $settings
function get_access_levels()
{
	global $db,$settings;
	
	// Get Available Access Level Settings
	$sql = 'SELECT `intUTID`,`strUserType`,`strAccessArea` FROM `tblUserTypes` WHERE 1';
	$access = $db->get_results($sql);
	if($access)
	{
		$settings['account_access_levels'] = array();
		$settings['account_access_areas'] = array();
		foreach($access AS $level)
		{
			$settings['account_access_levels'][$level->intUTID] = $level->strUserType;
			$settings['account_access_areas'][$level->intUTID] = $level->strAccessArea;
		}
	}
	else
	{
		$settings['account_access_levels'] = false;
		$settings['account_access_areas'] = false;
	}

	// Get List of Account Standing Status Settings
	$sql = 'SELECT `intUSCID`,`strStatusCode` FROM `tblUserStatusCode` WHERE 1';
	$status = $db->get_results($sql);
	if($status)
	{
		$settings['account_status_levels'] = array();
		foreach($status AS $level)
		{
			$settings['account_status_levels'][$level->intUSCID] = $level->strStatusCode;
		}
	}
	else
	{
		$settings['account_status_levels'] = false;
	}
}

function rtn_select($field,$match,$display, $default = false)	
{
	if($_REQUEST['_subaction']!="_validate"&&$default==true){ echo " selected='selected'>$display"; } else {
		if($field==$match){
			echo " selected='selected'>$display";
		} else {
			echo ">$display";
		}
	}
}
function vf($field,$db){
	$sql = "SELECT intValidate FROM tbl_mod_Registration WHERE strFieldName = '$field'";
	$validate = $db->get_var($sql);
	if($validate==1){
		return true;
		} else {
		return false;
	}
}
function sf($field,$db){
	$sql = "SELECT intDisplay FROM tbl_mod_Registration WHERE strFieldName = '$field'";
	$validate = $db->get_var($sql);
	if($validate==1){
		return true;
		} else {
		return false;
	}
}
function rtn_data($field,$db_data)
{
	if($_REQUEST['_subaction']=="validate"){ return $field; } else { return $db_data; }
}
function rtn_vcode($num,$db)
{
	if($_REQUEST['_subaction'] == "validate")
	{
		$num = (int) ereg_replace('[^0-9]','',$num);
		$sql = 'SELECT `strCode` FROM `tblValidationCodes` WHERE `intVID` = '.$num;
		$validate = $db->get_var($sql);
		return $validate;
	}
}
?>
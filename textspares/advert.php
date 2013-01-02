<?php
define('IN_TEXTSPARES',true);
include('suppliers/Includes/config/php_enviroment.php');
include('suppliers/Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);
$sqlopt = $db->get_results('SELECT `make_id`,`make_name`,`parent_id` FROM `vehicle_makes` WHERE `parent_id` != 0');

foreach($sqlopt AS $row)
{
	if(empty($str))
	{
		$str = str_replace(" ","_",$row->make_name).",".$row->parent_id.",".$row->make_id;
	} else {
		$str .= "#".str_replace(" ","_",$row->make_name).",".$row->parent_id.",".$row->make_id;
	}
}

$txtbdy = '<link rel="stylesheet" type="text/css" href="'.BASE.'images/text.css"><script src="'.BASE.'js/textajax.js" type="text/javascript"></script><table width="100" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td><img src="'.BASE.'images/h-search.gif" width="217" height="35" alt="" /></td></tr><tr><td style="background:url('.BASE.'images/bg-search.gif)"><table width="97%" border="0" cellspacing="0" cellpadding="0"><tr><td colspan="2"><div align="center" class="style1">SELECT DETAIL BELOW</div></td></tr><tr><td colspan="2"><form name="srchfrm" method="post" action="'.BASE.'partrequest.php"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="3"><tr><td width="29%">Make</td><td width="71%"><label><select name="makeid" class="homeselect" onchange="show_Models(\''.$str.'\',this.value);"><option value="-1">-----Make-----</option>';

$sqlop = $db->get_results("select make_name,make_id from vehicle_makes where (parent_id='0' and disp='y') order by make_name");

foreach($sqlop AS $data)
{
	$txtbdy .= '<option value="'.$data->make_id.'">'.$data->make_name.'</option>';
}
$txtbdy .= '</select></label</td></tr><tr><td>Model</td><td><select name="modeltype" class="homeselect" id="modelid"><option>Model</option></select></td></tr><tr><td>Engine </td><td><select name="engine_capacity" class="homeselect"><option value="">Engine Size</option><option value="UNKNOWN">DONT KNOW</option><option value="650">650cc</option><option value="650">800cc</option><option value="900">900cc</option><option value="950">950cc</option><option value="1000">1000cc</option><option value="1100">1100cc</option><option value="1200">1200cc</option><option value="1250">1250cc</option><option value="1300">1300cc</option><option value="1400">1400cc</option><option value="1500">1500cc</option><option value="1600">1600cc</option><option value="1700">1700cc</option><option value="1800">1800cc</option><option value="1900">1900cc</option><option value="2000">2000cc</option><option value="2100">2100cc</option><option value="2200">2200cc</option><option value="2300">2300cc</option><option value="2400">2400cc</option><option value="2500">2500cc</option><option value="2600">2600cc</option><option value="2700">2700cc</option><option value="2800">2800cc</option><option value="2900">2900cc</option><option value="3000">3000cc</option><option value="3100">3100cc</option><option value="3200">3200cc</option><option value="3300">3300cc</option><option value="3400">3400cc</option><option value="3500">3500cc</option><option value="3600">3600cc</option><option value="3700">3700cc</option><option value="3800">3800cc</option><option value="3900">3900cc</option><option value="4000">4000cc</option><option value="4100">4100cc</option><option value="4200">4200cc</option><option value="4300">4300cc</option><option value="4400">4400cc</option><option value="4500">4500cc</option><option value="4600">4600cc</option><option value="4700">4700cc</option><option value="4800">4800cc</option><option value="4900">4900cc</option><option value="5000">5000cc</option><option value="5500">5500cc</option><option value="6000">6000cc</option></select></td></tr><tr><td>Fuel</td><td><select name="fuel_type" class="homeselect"><option value="">-----Fuel Type---</option><option value="petrol">Petrol</option><option value="lpg">GAS (LPG)</option><option value="Diesel">Diesel</option><option value="turbo diesel">Turbo Diesel</option><option value="tdi">TDi</option><option value="hdi">HDi</option><option value="sdi">SDi</option><option value="UNKNOWN">DONT KNOW</option></select></td></tr><tr><td>Trans</td><td><select name="gear_type" class="homeselect"><option value="">-----G/Box Type -</option><option value="4m">4 Speed Manual</option><option value="5m">5 Speed Manual</option><option value="6m">6 Speed Manual</option><option value="3a">3 Speed Automatic</option><option value="4a">4 Speed Automatic</option><option value="5a">5 Speed Automatic</option><option value="6a">6Speed Automatic</option><option value="4x4m">4x4 Manual</option><option value="4x4a">4x4 Auto</option><option value="Tiptronic">Tiptronic</option><option value="Steptronic">Steptronic</option><option value="Switchable Auto">Switchable Auto</option><option value="UNKNOWN">DONT KNOW</option></select></td></tr><tr><td>Body</td><td><select name="body_type" class="homeselect"><option value="">-----Body Type -</option><option value="2 DOOR COUPE">2 DOOR COUPE</option><option value="3 DOOR HATCH">3 DOOR HATCH</option><option value="4 DOOR SALOON">4 DOOR SALOON</option><option value="5 DOOR HATCH">5 DOOR HATCH</option><option value="COUPE">COUPE</option><option value="ESTATE">ESTATE</option><option value="CONVERTIBLE">CONVERTIBLE</option><option value="MPV">MPV</option><option value="SUV">SUV</option><option value="SWB">SWB</option><option value="MWB">MWB</option><option value="LWB">LWB</option><option value="PICK-UP">PICK-UP</option><option value="MINI-BUS">MINI-BUS</option><option value="UNKNOWN BODY TYPE">DONT KNOW</option></select></td></tr><tr><td height="51" colspan="2"><input type="image"  src="'.BASE.'images/b-continue.gif" id="contimg" alt=""></td></tr></table></form></td></tr></table></td></tr></table>';

echo $txtbdy;
?>

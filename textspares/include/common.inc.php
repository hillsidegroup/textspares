<?php
if(!defined('IN_TEXTSPARES')) exit;
include('suppliers/Includes/config/php_enviroment.php');

include_once('suppliers/Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

include_once('suppliers/Includes/function_global.php');
include_once('function_front_sessions.php');
include_once('./check.php');

$settings = get_settings();

/* Create Session */
$session = new session();
$alerts = false;
if(!empty($_POST['_login']) && $_POST['_login'] == true)
{
	if($session->login())
	{
		header('location:'.BASE);
	}
}

/* Auth Session */
$sessionAuth = (!$session->check()) ? false : (($session->access(1)) ? $session->loggedin : false);

/* Close Session */
if(!empty($_REQUEST['_action']) && $_REQUEST['_action'] == 'logout') $session->logout();

$override_header = false;
$meta_area = false;
$meta_make = false;
$meta_make_link = false;

if(!empty($_GET['data']))
{
	$match_data = $_GET['data'];
	$match_area = (!empty($_GET['area'])) ? $_GET['area'] : $_GET['data'];
	
	$area = location_options($match_area,true);
	if($area != false)
	{
		if(is_array($area))
		{
			$meta_area = $area[0].', '.$area[1];
		}
		else
		{
			$meta_area = $area;
		}
		$override_header = true;
	}
	
	$make_brand_id = false;

	if($area == false | !empty($_GET['area']))
	{
		$brands = $db->get_results('SELECT `make_name`,`make_id` FROM `vehicle_makes` WHERE `parent_id` = 0 ORDER BY `make_name` ASC',ARRAY_A);
		if($brands)
		{
			foreach($brands AS $brand)
			{
				if(strtolower($brand['make_name']) == strtolower($match_data))
				{
					$meta_make_file = getfileId($brand['make_id']);
					if($meta_make_file)
					{
						$meta_make_find = str_replace(' ','-',$brand['make_name']);
						if(($brand['make_id']=='1318') or ($brand['make_id']=='1319') or ($brand['make_id']=='1320'))
						{
							$meta_make_link = '<a href="'.BASE.'Details/'.$meta_make_file.'/'.$meta_make_find.'" title="'.$brand['make_name'].' Car Parts">'.$brand['make_name'].'</a>';
						} else {
							$meta_make_link = '<a href="'.BASE.'MakeDetails/'.$meta_make_file.'/'.$meta_make_find.'" title="'.$brand['make_name'].' Car Parts">'.$brand['make_name'].'</a>';
						}
					}
					else
					{
						$meta_make_link = $brand['make_name'];
					}

					$meta_make = $brand['make_name'];
					$make_brand_id = $brand['make_id'];
					$override_header = true;
					break;
				}
			}
		}
	}

	if($override_header)
	{
		$res_file['pageTitle'] = 'New or Used '.(($meta_make) ? $meta_make : 'Car and Van').' Parts '.(($meta_area) ? $meta_area : 'world wide').'';
		if($meta_make) $res_file['pageKeywords'] = (($meta_area) ? $meta_area.' ' : '').$meta_make.','.$meta_make.' Parts,'.$meta_make.' Spares,'.(($meta_area) ? $meta_area.' ' : '').$meta_make.' Car Parts,'.$meta_make.' Engines,'.$meta_make.' Body Panels,'.$meta_make.' Turbos';
		$res_file['pageDesc'] = '';
	}
}

function top() {

	global $session, $sessionAuth, $settings, $res_file;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"><head>
<?php
	$a = basename($_SERVER['SCRIPT_NAME']);

	if(!empty($_REQUEST['fileid'])) {
		$file_id=$_REQUEST['fileid'];
	}
	if(!empty($_REQUEST['mid'])) {
		$file_id=$_REQUEST['mid'];
	}

	if(!empty($_REQUEST['fileid']) && !empty($_REQUEST['mid'])){
		$file_id=2539;
	}

	if(!empty($res_file['pageKeywords'])){
		$keys = strip_tags($res_file['pageKeywords']);
	}else{
		$keys = $settings['meta_keywords'];
	}
	if(!empty($res_file['pageDesc'])){
		$desc = strip_tags($res_file['pageDesc']);
	}else{
		$desc = $settings['meta_description'];
	}
	if(!empty($res_file['pageTitle'])){
		$pagetitle = strip_tags($res_file['pageTitle']);
	}else{
		$pagetitle = $settings['meta_title'];
	}

   ?>
    <title><?=$pagetitle;?></title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
	<meta http-equiv="content-language" content="en" />
	<meta name="subject" content="Car Parts and Used Auto Spares" />
	<meta name="description" content="<?=$desc;?>" />
	<meta name="keywords" content="<?=$keys;?>" />
	<meta name="DC.Title" content="<?=$pagetitle;?>" />
	<meta name="DC.Creator" content="TextSpares - Marc A Newton" />
	<meta name="DC.Subject" content="Comparison Website" />
	<meta name="DC.Description" content="<?=$desc;?>" />
	<meta name="DC.Publisher" content="oTeck Office Solutions" />
	<meta name="DC.Date" content="2011-12-07" />
	<meta name="DC.Type" content="Vehicle Trade" />
	<meta name="DC.Coverage" content="53.38;-2.9" />
	<meta name="DC.Language" content="en-GB" />
	<meta name="DC.Resource" content="http://<?=$_SERVER['SERVER_NAME'];?>" />
	<meta name="ICBM" content="53.38, -2.9" />
	<meta name="geo.position" content="53.38;-2.9" />
	<meta name="geo.country" content="GB" />
	<meta name="geo.placename" content="Rochdale, Lancashire, England, United Kingdom" />
	<meta name="geo.region" content="GB-LAN" />
	<meta itemprop="name" content="<?=$pagetitle;?>" />
	<meta itemprop="description" content="<?=$desc;?>" />
	<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="<?=BASE;?>rssfeed.xml" />
	<link rel="stylesheet" type="text/css" href="<?=BASE;?>include/text.css"/>
	<link rel="stylesheet" href="<?=BASE;?>suppliers/Stylesheets/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" />
	<link rel="stylesheet" href="<?=BASE;?>Include/lytebox.css" type="text/css" />
	<script type="text/javascript" language="javascript" src="<?=BASE;?>Include/lytebox.js"></script>
	<script type="text/javascript" language="javascript" src="<?=BASE;?>include/textspares.js"></script>
	<script type="text/javascript" language="javascript" src="<?=BASE;?>include/textspares_ajax.js"></script>
	<script type="text/javascript" language="javascript" src="<?=BASE;?>include/jquery-1.6.4.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?=BASE;?>suppliers/Scripts/jquery-ui-1.8.2.custom.min.js"></script>
	<style type="text/css">
	<!--
	body {
		margin-left: 0px;
		margin-top: 5px;
		margin-right: 0px;
		margin-bottom: 5px;
		background-image: url(<?=BASE?>images/mainbg.gif);
	}
	.style1 {
		font-size: 17px;
		font-weight: bold;
	}
	-->
	</style>
	<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$('.replace').addClass("grey");
		$('.replace').focus(function() {
			$(this).removeClass("grey");
			if (this.value == this.defaultValue){
				this.value = '';
			}
			if(this.value != this.defaultValue){
				this.select();
			}
		});
	});
	window.___gcfg = {lang: 'en-GB'};
	(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
	</script>
</head>

<body itemscope="" itemtype="http://schema.org/LocalBusiness">
	<?=($settings['debug_mode']) ? 'Debug Mode Enabled' : '';?>
	<table width="981" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tr>
		<td><img src="<?=BASE?>sitebanner/head.jpg" width="981" height="133" alt="New &amp; Used Car, Van, Vehicle Parts and Spares from <?=$_SERVER['SERVER_NAME'];?>"/></td>
	  </tr>
	</table>
<?
}

function common_middle()
{
	global $session, $sessionAuth, $alerts;
?>
	<table width="981" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="191" valign="top">
				<div class="leftlinks" style="padding:0 0 10px 10px;"><div class="g-plusone" data-size="medium" data-count="true"></div></div>
				<div class="leftlinks">
					<div><img src="<?=BASE?>images/h-quicklinks.gif" width="167" height="35" alt="Quick Links"/></div>
					<div><a href="<?=BASE?>index.php" title="Home">Home</a></div>
<?php
if($session->loggedin !== true) {
?>
					<div><a href="<?=BASE?>customer_login.php" title="Customer Login">Customer Login</a></div>
<?php
}
?>
					<div><a href="<?=BASE?>suppliers/" title="Supplier Login">Supplier Login</a></div>
					<div><a href="<?=BASE?>sitemap.php" title="Sitemap">Sitemap</a></div>
					<div><a href="<?=BASE?>Engine/1013" title="Engines">Engines</a></div>
					<div><a href="<?=BASE?>Gearbox/1011" title="Gearboxes">Gearboxes</a></div>
					<?
					$sqlmake= mysql_query('SELECT `make_id`,`make_name` FROM `vehicle_makes` WHERE `parent_id` = \'0\' ORDER BY `disp_order`')or die(mysql_error());
					if(mysql_num_rows($sqlmake)>0)
					{
						while($data=mysql_fetch_array($sqlmake))
						{
							$file_id=getfileId($data['make_id']);
							if($file_id)
							{
								$CNAME=str_replace(" ","-",$data['make_name']);
								if(($data['make_id']=='1318') or ($data['make_id']=='1319') or ($data['make_id']=='1320') )
								{
									echo '<div><a href="'.BASE.'Details/'.$file_id.'/'.$CNAME.'" title="'.$data['make_name'].' Car Parts">'.$data['make_name'].'</a></div>'."\n";
								} else {
									echo '<div><a href="'.BASE.'MakeDetails/'.$file_id.'/'.$CNAME.'" title="'.$data['make_name'].' Car Parts">'.$data['make_name'].' Car Parts</a></div>'."\n";
								}
							}
						}
						mysql_free_result($sqlmake);
					}
					?>
				</div>
				<div style="width:162px;text-align:center;"><script type="text/javascript" language="javascript" src="//smarticon.geotrust.com/si.js"></script></div>
			</td>
			<td valign="top">
<?php
		if($session->messages) {
			display_alert_message($session->messages);
			echo '<br clear="all" />';
		}
		if($alerts) {
			display_alert_message($alerts);
			echo '<br clear="all" />';
		}
}

function bottom(){

	global $db, $session, $sessionAuth, $nosidebar, $settings;

  ?>
				<br />
				<br />
			</td>
			<td width="241" valign="top">
				<table border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
					<td>
						<div class="homefront" style="color:#ffbb11;font-weight:bold;">
<?php
if($session->loggedin !== true) {
?>
							<h1>Retreive Your Quotes</h1>
							<form name="_login_form" action="<?=BASE;?>" method="post">
								<input type="hidden" name="_login" value="true" />
								Usercode or E-mail Address:
								<input type="text" name="_username" value="Usercode or E-mail Address" class="replace login" style="width:185px;" /><br/>
								<br/>
								and Your Password:
								<input type="password" name="_password" value="" class="replace login" style="width:185px;" />
								<br/>
								<br/>
								<input type="submit" name="customer_login" value="Login" class="login" style="width:185px;height:30px;font-size:14px;" />
							</form>
							<br/>&nbsp;<br/>
							<a class="sbb onblue" href="my_login.php">Get Password</a>
							<br/>
<?php
} else {
?>
							<h1>Welcome back <?=$session->userdata['name'];?></h1>
							<a href="<?=BASE;?>" target="_self" class="sbb onblue">New Request</a>
							<a href="<?=BASE;?>my_requests.php" target="_self" class="sbb onblue">My Requests</a>
							<a href="<?=BASE;?>my_history.php" target="_self" class="sbb onblue">Order History</a>
							<a href="<?=BASE;?>?_action=logout" target="_self" class="sbb onblue">Logout</a>
							<br/>
<?php
}
?>
						</div>
<?php if(!$nosidebar == true) { ?>
<div class="homefront">
	<h1>NEW &amp; USED CAR PARTS</h1>
	<div class="image"><img width="198" height="83" alt="New and Used Cars" title="New and Used Car Parts and Engines" src="<?=BASE;?>homebanners/thumb/centercar_image01_01.gif" /></div>
	<select name="select8" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?php
			$sqlcars=mysql_query("select `tb1`.`make_name`,`tb1`.`make_id`,`tb2`.* from `vehicle_makes` AS `tb1`,`manage_makes` AS `tb2` where `tb1`.`make_id` = `tb2`.`parent_id` and `tb2`.`vehicle_type` = 'c' order by `make_name`")or die(mysql_error());
			if(mysql_num_rows($sqlcars) > 0)
			{
				while($cdata=mysql_fetch_array($sqlcars))
				{
					$sqlchk=mysql_query('SELECT `file_id` FROM `files` WHERE `page_id` = \''.$cdata['make_id'].'\' AND `status` = \'A\'')or die(mysql_error());
					$recchk=mysql_fetch_array($sqlchk);
					echo '<option value="'.$recchk['file_id'].','.$cdata['make_name'].'">'.$cdata['make_name'].'</option>';
				}
			}
		?>
	</select>
</div>
<div class="homefront">
	<h1>4x4 SUV PARTS</h1>
	<div class="image"><img width="198" height="83" alt="4x4 SUV Parts" title="4x4 SUV Parts and Engiens" src="<?=BASE;?>homebanners/thumb/centercar_image02_02.jpg" /></div>
	<select name="select9" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?php
			$sqlsuv=mysql_query("select `tb1`.`make_name`,`tb1`.`make_id`,`tb2`.* from `vehicle_makes` AS `tb1`,`manage_makes` AS `tb2` where `tb1`.`make_id` = `tb2`.`parent_id` and `tb2`.`vehicle_type` = 's' order by `make_name`")or die(mysql_error());
			if(mysql_num_rows($sqlsuv)>0)
			{
				while($sdata=mysql_fetch_array($sqlsuv))
				{
					$sqlchk2=mysql_query('select `file_id` from `files` where `page_id` = \''.$sdata['make_id'].'\' AND `status` = \'A\'')or die(mysql_error());
					$recchk2=mysql_fetch_array($sqlchk2);
					echo '<option value="'.$recchk2['file_id'].','.$sdata['make_name'].'">'.$sdata['make_name'].'</option>';
				}	
			}
		?>
	</select>
</div>

<div class="homefront">
	<h1>COMMERCIAL VAN PARTS</h1>
	<div class="image"><img width="198" height="83" alt="Commercial Vans" title="Commercial Van Parts and Engines" src="<?=BASE;?>homebanners/thumb/centercar_image03_03.gif" /></div>
	<select name="select10" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?php
			$sqlvan=mysql_query("select `tb1`.`make_name`,`tb1`.`make_id`,`tb2`.* from `vehicle_makes` AS `tb1`,`manage_makes` AS `tb2` where `tb1`.`make_id` = `tb2`.`parent_id` and `tb2`.`vehicle_type` = 'v' order by `make_name`")or die(mysql_error());
			if(mysql_num_rows($sqlvan)>0)
			{
				while($vdata=mysql_fetch_array($sqlvan))
				{
					$sqlchk3=mysql_query('select `file_id` from `files` where `page_id` = '.$vdata['make_id'].' and `status` = \'A\'')or die(mysql_error());
					$recchk3=mysql_fetch_array($sqlchk3);
					echo '<option value="'.$recchk3['file_id'].','.$vdata['make_name'].'">'.$vdata['make_name'].'</option>';
				}
			}
		?>
	</select>
</div>

<div class="homefront">
	<h1>JAPANESE IMPORTS</h1>
	<div class="image"><img width="198" height="83" alt="Japanese Imports" title="Japanese Car Parts and Engines" src="<?=BASE;?>homebanners/thumb/centercar_image04_04.gif" /></div>
	<select name="select11" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?php
			$sqljap=mysql_query("select `tb1`.`make_name`,`tb1`.`make_id`,`tb2`.* from `vehicle_makes` AS `tb1`,`manage_makes` AS `tb2` where `tb1`.`make_id` = `tb2`.`parent_id` and `tb2`.`vehicle_type` = 'j' order by `make_name`")or die(mysql_error());
			if(mysql_num_rows($sqljap)>0)
			{
				while($jdata=mysql_fetch_array($sqljap))
				{
					$sqlchk4=mysql_query('select `file_id` from `files` where `page_id` = \''.$jdata['make_id'].'\' AND `status` = \'A\'')or die(mysql_error());
					$recchk4=mysql_fetch_array($sqlchk4);
					echo '<option value="'.$recchk4['file_id'].','.$jdata['make_name'].'">'.$jdata['make_name'].'</option>';
				}
			}
		?>
	</select>
</div>

<div class="homefront">
	<h1>ENGINES &amp; CYLINDER HEADS</h1>
	<div class="image"><img width="198" height="83" alt="ENGINES &amp; CYLINDER HEADS" title="ENGINES &amp; CYLINDER HEADS" src="<?=BASE;?>homebanners/thumb/centercar_image05_05.gif" /></div>
	<select name="select12" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?
			$sqleng=mysql_query("select tb1.make_name,tb1.make_id,tb2.* from vehicle_makes tb1,manage_makes tb2 where tb1.make_id=tb2.parent_id and tb2.vehicle_type='e' order by make_name")or die(mysql_error());
			if(mysql_num_rows($sqleng)>0)
			{
				while($edata=mysql_fetch_array($sqleng))
				{
					$sqlchk5=mysql_query("select file_id from files where page_id='$edata[make_id]' and status='A'")or die(mysql_error());
					$recchk5=mysql_fetch_array($sqlchk5);
					echo"<option value='$recchk5[file_id],$edata[make_name]'>".$edata[make_name]."</option>";
				}
			}
		?>
	</select>
</div>

<div class="homefront">
	<h1>RECONDITIONED GEARBOXES</h1>
	<div class="image"><img width="198" height="83" alt="RECONDITIONED GEARBOXES" title="RECONDITIONED GEARBOXES" src="<?=BASE;?>homebanners/thumb/centercar_image06_06.gif" /></div>
	<select name="select13" onchange="MM_jumpMenu('parent',this,0)">
		<option value="">-------Make------</option>
		<?
			$sqlgear=mysql_query("select `tb1`.`make_name`,`tb1`.`make_id`,`tb2`.`mid`,`tb2`.`parent_id`,`tb2`.`vehicle_type` FROM `vehicle_makes` AS `tb1`,`manage_makes` AS `tb2` WHERE `tb1`.`make_id` = `tb2`.`parent_id` and `tb2`.`vehicle_type` = 'g' order by `make_name`")or die(mysql_error());
			if(mysql_num_rows($sqlgear)>0)
			{
				while($gdata=mysql_fetch_array($sqlgear))
				{
					$sqlchk6=mysql_query('select `file_id` from `files` where `page_id` = \''.$gdata['make_id'].'\' and `status` = \'A\'')or die(mysql_error());
					$recchk6=mysql_fetch_array($sqlchk6);
					echo '<option value="'.$recchk6['file_id'].','.$gdata['make_name'].'">'.$gdata['make_name'].'</option>';
				}
			}
		?>
	</select>
</div>
<?php } ?>
					</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#01366C" style="height:20px"><td align="center"><a href="<?=BASE?>index.php" class="bottomlinks">Home</a>&nbsp;<span style="color:#FFFFFF">|&nbsp;</span><a href="<?=BASE?>faqs.php" class="bottomlinks">F.A.Q.s</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="<?=BASE?>sitemap.php" class="bottomlinks">Sitemap</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="<?=BASE?>privacy.php" rel="nofollow" class="bottomlinks">Privacy</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="<?=BASE?>contact_us.php" class="bottomlinks">Contact Us</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="<?=BASE?>textspares_network.php" class="bottomlinks">Join Network</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="<?=BASE?>terms.php" rel="nofollow" class="bottomlinks">Terms &amp; Conditions</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="http://validator.w3.org/check?uri=http://www.textspares.co.uk/" target="_blank" class="bottomlinks">Valid XHTML</a>&nbsp;<span style="color:#FFFFFF">|</span>&nbsp;<a href="/rssfeed.xml" target="_blank" class="bottomlinks">RSS</a></td></tr>
	</table>

<script type="text/javascript" language="javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7326446-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?=debug_window();?>
</body>
</html>
<?
mysql_close();
}
function getparent($id){
	$sql=mysql_query("select make_name from vehicle_makes where make_id='$id'")or die(mysql_error());
	//echo"select make_name from vehicle_makes where make_id='$id'";
	$rec=mysql_fetch_array($sql);
	return $rec['make_name'];
}
function getpartname($id){
	$sql=mysql_query("select part_cat_name from parts_categories where part_id='$id'")or die(mysql_error());
	$rec=mysql_fetch_array($sql);
	return $rec['part_cat_name'];
}

function getfileId($id)
{
	global $db;
	return $db->get_var('SELECT `file_id` FROM `files` WHERE `page_id` = \''.$id.'\' AND `status` = \'A\'');
}
function chk_selected($val1,$val2){
	$str="";
	if($val1==$val2){
		$str="selected";
	}else{
		$str="";
	}

	return $str;
}
function get_part($id){
	//echo"select part_cat_name from parts_categories where part_id='$id'";
	$sql=mysql_query("select part_cat_name from parts_categories where part_id='$id'")or die(mysql_error());
	$rec=mysql_fetch_array($sql);
	return $rec[part_cat_name];
}
?>

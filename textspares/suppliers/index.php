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

$alerts = false;
$jsondata = false;
$display_html = (isset($_REQUEST['_ajax']) || isset($_REQUEST['json'])) ? false : true;
$page = ($display_html == true) ? ((isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : 'index') : ((isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : null);

include_once('Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

include('Includes/function_global.php');
include('Includes/function_supplier_sessions.php');
include('Includes/function_supply.php');

$settings = get_settings();
get_access_levels();

/* Create Session */
$session = new session();
if(isset($_POST['_login']))
{
	if($session->login())
	{
		header('location:'.ROOT);
	}
}

/* Auth Session */
$authed = $session->check();
if($authed == false || $session->loggedin == false)
{
	switch($page) {
		case "register":
		case "password":
			$page = $page;
		break;
		
		default:
			$page = 'login';
		break;
	}
}

/* Close Session */
if($page == 'logout') $session->logout();

$sales = 0;
$orders = 0;
$requests = 0;

$datafeed = (isset($_REQUEST['feed'])) ? $_REQUEST['feed'] : false;

if($session->access(6) == true)
{
	if(!isset($_POST['_login']) && $display_html == true)
	{
		$sales = $db->get_var('SELECT COUNT(*) FROM `supplier_quotes` WHERE `company_user_id` = \''.$session->userdata['id'].'\' AND `accepted` > 0 AND `dispatched` = 0');
		$sales = (!$sales) ? 0 : $sales;

		@setcookie('last_query_order',time(), time() + 172800);
		$extend = '';
		if($datafeed == 'new')
		{
			$order_request = (int) $db->mysql_prep($_COOKIE['last_query_order']) or 0;
			$extend = (is_numeric($order_request) == true ) ? ' AND `modified` > \''.$order_request.'\'' : '';
		}
		$orders = $db->get_var('SELECT COUNT(`vehicle_id`) FROM `supplier_quotes_details` WHERE `company_user_id` = \''.$session->userdata['id'].'\' AND `method` > 0 AND `accepted` = 0 AND `cancelled` = 0 '.$extend.' ');
		$orders = (!$orders) ? 0 : $orders;
		
		$last_visited_requests = !empty($_COOKIE['last_visited_requests']) ? $_COOKIE['last_visited_requests'] : 0;
		$visit_request = (int) $db->mysql_prep($last_visited_requests);
		if(is_numeric($visit_request) == true ) {
			$requests = $db->get_var('SELECT COUNT(*) FROM `customer_requests` WHERE `order_group` = 0 AND `request_stamp` > \''.$visit_request.'\' GROUP BY `vehicle_id`');
			$requests = (!$requests) ? 0 : $requests;
		}
		@setcookie('last_visited_requests',time(), time() + 172800);
	}
	
	if($display_html == false)
	{
		if($datafeed == 'new')
		{
			$jsondata = array('requests' => $requests, 'orders' => $orders);
		}
		
		if($datafeed == 'address' && isset($_REQUEST['client']))
		{
			$client = (int) $_REQUEST['client'];
			if(is_int($client) && $client > 0) {
				$jsondata = get_client_details($client);
			} else {
				$jsondata = false;
			}
		}
	}
}
else
{
	$alerts = $session->messages;
}

if($display_html == true) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>TextSpares.co.uk | Control Panel</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="noindex,nofollow" />
    <link rel="stylesheet" href="<?=ROOT;?>Stylesheets/all.css" type="text/css" />
    <link rel="stylesheet" href="<?=ROOT;?>Stylesheets/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" />
	<link rel="stylesheet" href="<?=BASE;?>Include/lytebox.css" type="text/css" />
    <script type="text/javascript" src="<?=ROOT;?>Scripts/jquery-1.6.3.min.js"></script>
	<script type="text/javascript" src="<?=ROOT;?>Scripts/jquery-ui-1.8.2.custom.min.js"></script>
	<script type="text/javascript" src="<?=BASE;?>Include/lytebox.js"></script>
<script type="text/javascript">
<!--
$(function(){
	//hover states on the static widgets
	$('#general_opts a, .reg_grp a, btns a').hover(
		function() { $(this).addClass('ui-state-hover'); },
		function() { $(this).removeClass('ui-state-hover'); }
	);
<?php if($session->access(6) == true) { ?>
	$.fn.requests_feed = function() {
		$.getJSON("<?=ROOT;?>?json=true&feed=new", function(data) {
			$new_requests = parseInt(data.requests);
			if($new_requests > 0) {
				$requests = parseInt($('#num_requests').html());
				$('#num_requests').html($requests + $new_requests);
				$('#num_requests_button').animate({
					backgroundColor: "#AEF25D",
					color: "#000000"
				}, 5000 );
			}
			$new_orders = parseInt(data.orders);
			if($new_orders > 0) {
				$requests = parseInt($('#num_orders').html());
				$('#num_orders').html($requests + $new_orders);
				$('#num_orders_button').animate({
					backgroundColor: "#AEF25D",
					color: "#000000"
				}, 5000 );
			}
		});
	}
	$('.close-details').click(function(e) {
		e.preventDefault();
	});
	$.fn.toggle_canvas = function(show) {
		$('.canvas').css('height',$(window).height()).css('width',$(window).width()).css('display',show);
	}
	//<?=((isset($_REQUEST['id']) && $_REQUEST['id'] > 0) ? 'setTimeout("$.fn.toggle_canvas(\'block\')",100);' : '');?>
	$interval = setInterval($.fn.requests_feed,60000);
<?php } ?>
});
function show_details(id){var obj = document.getElementById('DT'+id);var show = (obj.style.display == 'block') ? 'none' : 'block';obj.style.display = show;$.fn.setFocusTo(id);$.fn.toggle_canvas(show);}
-->
</script> 
</head>
<body>
<div class="canvas" style="display:none;"></div>
<div class="block">
    <div id="head">
        <img  alt="TextSpares | Supplier Control Panel" src="<?=BASE;?>sitebanner/head.jpg" />
    </div>
    <div id="content">
        <div id="account_opts">
            <div id="breadcumbs" class="ui-corner-all b1med">
                <dl>
                	<dt><a href="<?=ROOT;?>" title="Home"><span class="ui-icon ui-icon-home" style="margin:0px 10px 0px 5px; position:relative; top:5px;"></span></a></dt>
                    <?php if($session->loggedin == true){?>
                    <dt class="user_opts"><a href="<?=ROOT;?>?_action=logout"><span class="ui-icon ui-icon-extlink"></span>Logout</a></dt>
					<?=($session->access(5) == true ? '<dt class="user_opts"><a href="'.ROOT.'managers.php"><span class="ui-icon ui-icon-cogg"></span>Manage</a></dt>' : '');?>
                    <dt class="user_opts"><a href="<?=ROOT;?>?_action=profile&amp;_page=viewprofile"><span class="ui-icon ui-icon-person"></span>Account</a></dt>
					<?php if($session->access(6) == true) {?>
					<dt class="user_opts"><a href="<?=ROOT;?>?_action=history"><span class="ui-icon ui-icon-clock"></span>Sales History</a></dt>
					<dt class="user_opts"><a href="<?=ROOT;?>?_action=sales"><span class="ui-icon ui-icon-cart"></span>Sales (<?=$sales;?>)</a></dt>
					<dt class="user_opts" id="num_orders_button"><a href="<?=ROOT;?>?_action=quotes"><span class="ui-icon ui-icon-comment"></span>Quoted (<font id="num_orders"><?=$orders;?></font>)</a></dt>
					<dt class="user_opts"><a href="<?=ROOT;?>?_action=saved"><span class="ui-icon ui-icon-disk"></span>Saved</a></dt>
					<dt class="user_opts" id="num_requests_button"><a href="<?=ROOT;?>?_action=requests" style="color:#c11414;"><span class="ui-icon ui-icon-document"></span>New (<font id="num_requests"><?=$requests;?></font>)</a></dt>
                    <?php }} ?>
                </dl>
            </div>
        </div>
        <?php 
		if(isset($_REQUEST['_logout']) && $session->loggedin == false)
		{
			echo '
			<div class="ui-widget m10">
				<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all"> 
					<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>	You have successfully logged out!</p>
				</div>
			</div>
			';
        }
        echo '<div id="main_content">';
		if($alerts)
		{ 	  
			display_alert_message($alerts);
		}
}
		$noauth = false;

		switch($page)
		{
			case 'index':
				include('Includes/Modules/control-panel.php');
				break;
				
			case 'news':
				include('Includes/Modules/news.php');
				break;

			case 'requests':
			case 'saved':
				if($session->access(6) == true) { include('Includes/Modules/requests.php'); } else { $noauth = true; }
				break;

			case 'quotes':
				if($session->access(6) == true) { include('Includes/Modules/quotes.php'); } else { $noauth = true; }
				break;

			case 'sales':
				if($session->access(6) == true) { include('Includes/Modules/sales.php'); } else { $noauth = true; }
				break;
				
			case 'history':
				if($session->access(6) == true) { include('Includes/Modules/sales_history.php'); } else { $noauth = true; }
				break;
				
			case 'autoquotes':
				if($session->access(6) == true) { include('Includes/Modules/autoquotes.php'); } else { $noauth = true; }
				break;
				
			case 'alerts':
				if($session->access(6) == true) { include('Includes/Modules/alerts.php'); } else { $noauth = true; }
				break;

			case 'search':
				if($session->access(6) == true) { include('Includes/Modules/search.php'); } else { $noauth = true; }
				break;

			case 'profile':
				include('Includes/Modules/userprofile.php');
				break;
				
			case 'statistics':
				if($session->access(6) == true) { include('Includes/Modules/stats.php'); } else { $noauth = true; }
				break;

			case 'login':
				include('Includes/Modules/login_form.php');
				break;

			case 'register':
				include('Includes/Modules/registration.php');
				break;

			case 'password':
				include('Includes/Modules/forgot_pswd.php');
				break;
				
			//case 'table_merge':
			//	include('Includes/Modules/_table_merge.php');
			//	break;
		}

		if($noauth == true) { include('Includes/Modules/control-panel.php'); }
/*
		echo '<br clear="all" />';
		echo '<pre>'.print_r($_SERVER).'</pre>';
		
		echo '<b>User enviroment output test</b><br/><pre>';
		print_r($session);
		echo '</pre>';
*/

if($display_html == true) {
		?> 

    	</div>
    </div>
    <div id="footer">
    	<div class="menu">
            <h3>Supplier Help</h3>
            <dl>
                <dt><a href="#">Supplier Help Gcompany_user_ide</a></dt>
                <dt><a href="<?=BASE?>contact_us.php">Feedback</a></dt>
                <dt><a href="<?=BASE?>contact_us.php">Contact Us</a></dt>
                <dt><a href="<?=BASE?>sitemap.php">Sitemap</a></dt>
            </dl>
        </div>  
        <div class="menu">
            <h3>Our Network</h3>
            <dl>
                <dt><a href="<?=ROOT?>?_action=register">Join Our Network</a></dt>
                <dt><a href="<?=BASE?>terms.php">Terms &amp; Conditions</a></dt>
                <dt><a href="<?=BASE?>privacy.php">Privacy</a></dt>
                <dt><a href="<?=BASE?>faqs.php">FAQ</a></dt>
            </dl>
        </div>    
        <div class="menu" style="float:right; text-align:right;">  
        	<p>&copy; TextSpares.co.uk 2010</p>
            <p><a href="<?=BASE?>contact_us.php">Advertise your business</a></p>
        </div>
    </div>
</body>
</html>
<?php
} else {
	if(isset($_REQUEST['json']))
	{
		request_json($jsondata);
	}
}
?>
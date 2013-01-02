<?php
define('IN_TEXTSPARES',true);
define('SCRIPT','managers.php');
include('Includes/config/php_enviroment.php');

/*	$session->userdata['auth'] = intUserType
1 	Super Administrator	Backend
2 	Administrator		Backend
3 	Publisher			Backend
4 	Editor 				Backend
5 	All Backend 		Backend
6 	Supplier 			Frontend
7 	Registered User 	Frontend
*/

/* $this->userdata['account'] = intStatusCode;
1	Active
2	Blocked
3	Suspended
4	Inactive
*/

$alerts = false;
$jsondata = false;
$display_html = (isset($_REQUEST['_ajax']) || isset($_REQUEST['json'])) ? false : true;
$page = ($display_html == true) ? ((isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : 'index') : ((isset($_REQUEST['_action'])) ? $_REQUEST['_action'] : null);
$subaction = (!empty($_REQUEST['subaction'])) ? $_REQUEST['subaction'] : false;

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

if($session->access(5) == true)
{
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
    <title>TextSpares.co.uk | Managers</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="noindex,nofollow" />
    <link rel="stylesheet" href="<?=ROOT?>Stylesheets/all.css" type="text/css" />
    <link rel="stylesheet" href="<?=ROOT?>Stylesheets/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" />
	<link rel="stylesheet" href="<?=BASE?>Include/lytebox.css" type="text/css" />
    <script type="text/javascript" src="<?=ROOT?>Scripts/jquery-1.6.3.min.js"></script>
	<script type="text/javascript" src="<?=ROOT?>Scripts/jquery-ui-1.8.2.custom.min.js"></script>
	<script type="text/javascript" src="<?=BASE?>Include/lytebox.js"></script>
<script type="text/javascript">
<!--
$(function(){
	//hover states on the static widgets
	$('#general_opts a, .reg_grp a, btns a').hover(
		function() { $(this).addClass('ui-state-hover'); },
		function() { $(this).removeClass('ui-state-hover'); }
	);

	$('.close-details').click(function(e) {
		e.preventDefault();
	});
	$.fn.toggle_canvas = function(show) {
		$('.canvas').css('height',$(window).height()).css('width',$(window).width()).css('display',show);
	}
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
                    <dt class="user_opts"><a class href="<?=ROOT;?>?_action=logout"><span class="ui-icon ui-icon-extlink"></span>Logout</a></dt>
                    <dt class="user_opts"><a href="<?=ROOT;?>"><span class="ui-icon ui-icon-carat-1-s"></span>Return</a></dt>
					<?php if($session->access(2) == true) {?>
					<dt class="user_opts"><a href="<?=ROOT.SCRIPT;?>?_action=templates"><span class="ui-icon ui-icon-document"></span>Templates</a></dt>
					<dt class="user_opts"><a href="<?=ROOT.SCRIPT;?>?_action=settings"><span class="ui-icon ui-icon-gear"></span>Settings</a></dt>
					<dt class="user_opts"><a href="<?=ROOT.SCRIPT;?>?_action=billing"><span class="ui-icon ui-icon-document"></span>Billing</a></dt>
					<dt class="user_opts"><a href="<?=ROOT.SCRIPT;?>?_action=suppliers"><span class="ui-icon ui-icon-document"></span>Suppliers</a></dt>
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
		?>
		<div id="alerts" class="ui-widget m10">
			<div class="ui-state-highlight ui-corner-all" style="padding: 0.5em 0.7em;"> 
				<?php
				if(is_array($alerts))
				{
					foreach($alerts AS $alert)
					{
						echo '<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span> '.$alert.'</p>';
					}
				}
				else
				{
					echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span> '.$alerts;
				}
				?>
			</div>
		</div>
		<?php 
		}
}
		$noauth = false;
/*
1 	Super Administrator	Backend
2 	Administrator		Backend
3 	Publisher			Backend
4 	Editor 				Backend
5 	All Backend 		Backend
6 	Supplier 			Frontend
7 	Registered User 	Frontend
*/
		switch($page)
		{
			default:
				echo '404 File Not Found!';
			break;

			case 'index':
				include('Includes/Modules/control-panel.php');
				break;
				
			case 'news':
				if($session->access(4) == true) { include('Includes/Manager/news.php'); } else { $noauth = true; }
				break;

			case 'billing':
				if($session->access(2) == true) { include('Includes/Manager/billing.php'); } else { $noauth = true; }
				break;
				
			case 'settings':
				if($session->access(2) == true) { include('Includes/Manager/settings.php'); } else { $noauth = true; }
				break;
				
			case 'suppliers':
				if($session->access(2) == true) { include('Includes/Manager/suppliers.php'); } else { $noauth = true; }
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
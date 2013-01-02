<?php
/*	RESERVED VAR NAMES
 *   - $session
 *   - $settings
 *   - $smarty
 *   - $domain
 */

define('IN_TEXTSPARES',true);
include('include/common.inc.php');

require('include/smarty/Smarty.class.php');

# Configure Smarty
$smarty = new Smarty;
$smarty->left_delimiter = '<?';
$smarty->right_delimiter = '?>';
$smarty->force_compile = true; #Disable Force Compile on LIVE Release
$smarty->debugging = true;
$smarty->caching = true;
$smarty->cache_lifetime = 120;

# Common Values
$smarty->assign('BASE',BASE);
$smarty->assign('loggedin',$session->loggedin);

# Set skin folder based on domain name visited
$domain = $_SERVER['SERVER_NAME'];

switch($domain)
{
	default:
		$smarty->skin = 'textspares.co.uk';
	break;
}

# Apply Default Meta Data Values if page by page values are not set.
if(!empty($res_file['pageKeywords']))
{
	$keys=strip_tags($res_file['pageKeywords']);
}else{
	$keys="car parts, used car parts, car engines, used car engines, car spares, used car spares, car breakers, engines, gearboxes, 4x4 parts, van parts, Audi car parts, bmw car parts, citroen car parts, daewoo car parts, fiat car parts, ford car parts, honda car parts, hyundai car parts, jaguar car parts, kia car parts, lexus car parts, mazda car parts, mitsubishi car parts, nissan car parts, peugeot car parts, renault car parts, rover car parts, saab car parts, suzuki car parts, toyota car parts, vauxhall car parts, vw cars parts, volvo car parts,volkswagen car parts, landrover can parts, auto accessories, performance exhaust parts. Textspares";
}
if(!empty($res_file['pageDesc']))
{
	$desc=strip_tags($res_file['pageDesc']);
}else{
	$desc="Online scrap yards &amp; breakers car engines, gearboxes. Find new or used parts for Vauxhall, Ford, Nissan, Volvo, Honda, Rover, Toyota, Mitsubishi Mazda.";
}
if(!empty($res_file['pageTitle']))
{
	$pagetitle=strip_tags($res_file['pageTitle']);
}else{
	$pagetitle="New, Used Car Parts, Spares, Engines from online breakers";
}

# Process Page Requests
$page = false;
switch($page)
{
	# Default script to load.
	default:
		
		# HTML Template file name for requested content.
		$content = 'index';

		$smarty->assign("Name","Fred Irving Johnathan Bradley Peppergill",true);
		$smarty->assign("FirstName",array("John","Mary","James","Henry"));
		$smarty->assign("LastName",array("Doe","Smith","Johnson","Case"));
		$smarty->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"),
			  array("I", "J", "K", "L"), array("M", "N", "O", "P")));

		$smarty->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
			  array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")));

		$smarty->assign("option_values", array("NY","NE","KS","IA","OK","TX"));
		$smarty->assign("option_output", array("New York","Nebraska","Kansas","Iowa","Oklahoma","Texas"));
		$smarty->assign("option_selected", "NE");

	break;
}

# Pass remaing pre-processed data
$smarty->assign('content',$content); # Passes to skin file what content file to load.
$smarty->assign('debug_window',debug_window());
$smarty->assign('head_title',$pagetitle);

# Loads requested skin and body template.
$smarty->display($smarty->skin.'/_body.html');

mysql_close();

?>
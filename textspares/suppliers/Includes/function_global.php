<?php
if(!defined('IN_TEXTSPARES')) exit;

$debug_reports = array();

function debug_reports($content,$sql = false)
{
	global $debug_reports;
	
	$reportid = count($debug_reports) + 1;
	$debug_reports[$reportid]['content'] = $content;
	$debug_reports[$reportid]['sql'] = $sql;
}

function debug_window()
{
	global $debug_reports,$settings;
	
	if($settings['debug_mode'] && count($debug_reports) > 0)
	{
		echo '<div class="debug_window">';
		foreach($debug_reports AS $row)
		{
			if($row['sql']) {
				echo '<div class="debug_row_error"><p>'.$row['content'].'</p><p><i>'.$row['sql'].'</i></p></div>';
			} else {
				echo '<div class="debug_row_warning">'.$row['content'].'</div>';
			}
		}
		echo '</div>'; 
	}
}

function display_alert_message($alerts)
{
	echo '<div id="alerts" class="ui-widget m10"><div class="ui-state-highlight ui-corner-all" style="padding: 0.5em 0.7em;">';
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
	echo '</div></div>';
}

function get_settings()
{
	$settings = array();

	$sql = 'SELECT `varname`,`value`,`type` FROM `manager_settings` WHERE 1';
	$res = mysql_query($sql);
	if(!$res) { 
		die('Website could not load its settings!');
	} else {
		if(mysql_num_rows($res) > 0)
		{
			while($vars = mysql_fetch_array($res)) {
				switch($vars['type']) {
					default:
					case 'string':
						$settings[$vars['varname']] = (string) $vars['value'];
						break;
					case 'int':
						$settings[$vars['varname']] = (int) $vars['value'];
						break;
					case 'float':
						$settings[$vars['varname']] = (float) $vars['value'];
						break;
					case 'bool':
						$settings[$vars['varname']] = (boolean) $vars['value'];
						break;
				}
			}
			mysql_free_result($res);
		}
	}
	return $settings;
}

foreach($_REQUEST AS $k => $v)
{
	if(!$v == '')
	{
		$v = preg_replace("|<[^>]+>(.*)</[^>]+>|U",'',$v);
		if(isset($_POST[$k])) {
			$_POST[$k] = $v;
		}
		if(isset($_GET[$k])) {
			$_GET[$k] = $v;
		}
		$_REQUEST[$k] = $v;
	}
}

function request_json($jsondata = false, $access = 6)
{
	global $session;

	header('Content-Type: application/json');
	header('Content-Disposition: attachment; filename="query"');
	header('Content-Transfer-Encoding: binary');
	header('Accept-Ranges: bytes');
	header('Cache-control: no-cache, must-revalidate');
	header('Pragma: private');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(is_array($jsondata))
	{
		if($session->loggedin == true && $session->access($access) == true)
		{
			print json_encode($jsondata);
		} else {
			print json_encode(array('error','Not Authorized!'));
		}
	} else {
		print json_encode(array('error','Bad data for encoding!'."\n".'Line: '.__LINE__."\n".'Page Requested: '.$page."\n".'Page Action: '.$_REQUEST['_subaction'],$jsondata));
	}
}

function add_vat($v) {
	global $settings;
	return number_format($v+(($v/100)*$settings['vat_rate']),2);
}

function getXMLFeed($url,$ssl=false){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	if($ssl) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
	$result = curl_exec($ch);                      
	curl_close($ch);
	return $result;
}

function get_client_details($client)
{
	global $db;
	$client = (int) $db->mysql_prep($client);
	
	if(is_int($client) && $client > 0)
	{
		$sql = 'SELECT `cust_name`,`cust_phone`,`cust_email`,`addr1`,`addr2`,`city`,`county`,`country`,`post_code` FROM `customer_information` WHERE `customer_id` = \''.$client.'\'';
		$result = mysql_query($sql);
		if(!$result) {
			return false;
		} else {
			$data = mysql_fetch_assoc($result);
			if(count($data) > 0) {
				mysql_free_result($result);
				return $data;
			} else {
				return false;
			}
		}
	}
	return false;
}

function validAddress($pd1,$pd2,$pd3,$pd4,$pd5,$pd6)
{
	global $db;
	$errors = array();
	$data = array();
	
	$data['address1'] = $db->mysql_prep(trim($_REQUEST[$pd1]));
	$data['address2'] = $db->mysql_prep(trim($_REQUEST[$pd2]));
	$data['city'] = $db->mysql_prep(trim($_REQUEST[$pd3]));
	$data['county'] = $db->mysql_prep(trim($_REQUEST[$pd4]));
	$data['country'] = $db->mysql_prep(trim($_REQUEST[$pd5]));
	$data['postcode'] = $db->mysql_prep(trim($_REQUEST[$pd6]));

	if(strlen(ereg_replace("[^A-Za-z0-9]",'',$data['address1'])) < 2) {
		$errors[] = 'Invalid Street Address Line.';
	}
	if(strlen(ereg_replace("[^A-Za-z]",'',$data['city'])) < 2) {
		$errors[] = 'Invalid City Name.';
	}
	if(strlen(ereg_replace("[^A-Za-z]",'',$data['county'])) < 2) {
		$errors[] = 'Invalid County.';
	}
	if(strlen(ereg_replace("[^A-Za-z]",'',$data['country'])) < 2) {
		$errors[] = 'Invalid Country Name.';
	}
	if(strlen(ereg_replace("[^A-Za-z0-9]",'',$data['postcode'])) < 6) {
		$errors[] = 'Invalid Postal Code';
	}
	
	return array('errors' => $errors, 'data' => $data);
}

function validEmail($email)
{
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex)
	{
		$isValid = false;
	}
	else
	{
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64)
		{
			// local part length exceeded
			$isValid = false;
		}
		else if ($domainLen < 1 || $domainLen > 255)
		{
			// domain part length exceeded
			$isValid = false;
		}
		else if ($local[0] == '.' || $local[$localLen-1] == '.')
		{
			// local part starts or ends with '.'
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $local))
		{
			// local part has two consecutive dots
			$isValid = false;
		}
		else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		{
			// character not valid in domain part
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			$isValid = false;
		}
		else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
		{
			// character not valid in local part unless 
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local)))
			{
				$isValid = false;
			}
		}
		if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		{
			// domain not found in DNS
			$isValid = false;
		}
	}
	return $isValid;
}

function files_in_folder($dir,$limit = false) {

	global $errors;

	if(is_dir($dir)) {
		
		$handle = opendir($dir);
		if($handle) {
		
			$fl = array();

			while (false !== ($file = readdir($handle))) {

				if(is_array($limit)) {
				
					if(in_array(pathinfo($dir . $file,PATHINFO_EXTENSION),$limit)) {
						$fl[] = $file;
					}

				}

			}
			
			return $fl;
			
			closedir($handle);
			
		} else {
			echo $error = 'Failed to open directory!';
		}
		
	} else {
		echo $error = 'Could not identify path as a directory!';
	}
	
	if($error) {
		$errors[] = __FUNCTION__ .' : ' . $error;
	}
	
	return false;

}

function create_thumbnails($images,$dir,$size) {

	global $errors;
	
	$tdir = $dir.'thumbnails/';

	if(!file_exists($tdir)) {
		mkdir ( $tdir, 0775, true );
	}

	if(is_array($images) && is_dir($dir) && is_numeric($size)) {
	
		$tl = array();
		
		foreach($images AS $img) {
		
			if(!file_exists($tdir . $img)) {

				$new = create_image($img,$dir,$tdir,$size);
				
				if($new) {
					$tl[] = $new;
				}
				
			} else {
			
				$tl[] = $img;
				
			}

		}
		
		return $tl;
		
	} else {
	
		$errors[] = '
			<b>function create_thumbnails</b><br>
			$images is an array: '.is_array($images).'<br/>
			$dir is a directory: '.is_dir($tdir).'<br/>
			$size is numerical: '.is_numeric($size).'<br/>
		';
		
	}

	return false;

}

function create_image($srcimg,$srcpath,$target,$size,$name = false) {

	if(is_file($srcpath . $srcimg) && is_numeric($size)) {

		$data = imagecreatefromjpeg($srcpath.$srcimg);

		if($data) {

			$width = imagesx($data);
			$height = imagesy($data);

			if($width >= $height) {
				$new_width = $size;
				$new_height = floor( $height * ($size / $width) );
			} else {
				$new_width = floor( $width * ($size / $height) );
				$new_height = $size;
			}

			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			imagecopyresampled( $tmp_img, $data, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			
			$name = (!$name) ? $srcimg : $name;

			imagejpeg( $tmp_img, $target.$name, 90 );
			
			return $name;
			
		}

	}
	
	return false;
	
}

function zebra($num)
{
	if(substr_count(($num/2),".")>0)
		{ echo 'zebra1 '; }
	else
		{ echo 'zebra2 '; }
}

function pad_ref_number($value)
{
	return ( ($value < 10000) ? ( ($value < 1000) ? ( ($value < 100) ? ( ($value < 10) ? 'TSRX' : 'TSR' ) : 'TS' ) : 'T' ) : '' ).$value;
}

function create_emailloginkey($id)
{
	global $db;
	$id = (int) $id;
	if($id > 0)
	{
		$user = $db->get_row('SELECT `cust_email`,`cust_password` FROM `customer_information` WHERE `customer_id` = \''.$id.'\' ');
		return md5($user->cust_email).'-'.$user->cust_password;
	}
	return false;
}

function send_email($to,$from,$subject,$message,$format=true) {

	if($format) $message = nl2br($message);

	$valid = validEmail($to);
	if($valid == false) {
		return 'Invalid send to address!';
	}
	
	$valid = validEmail($from);
	if($valid == false) {
		return 'Invalid sent from address!';
	}
	
	$valid = (strlen($message) > 0) ? true : false;
	if($valid == false) {
		return 'You cant send an empty message';
	}
	
	if($valid == true)
	{
		$emailbody = '
<html>
<head>
<style type="text/css">
body{background: url("http://www.textspares.co.uk/images/mainbg.gif") repeat-x scroll center top #FFFFFF;color:#464646;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;margin:8px 0;text-align:center;font-weight:normal;}
td{font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12px;color:#464646;font-weight:normal;}
table{margin:auto;}
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" width="981">
	<tr>
		<td><img src="http://www.textspares.co.uk/images/head.jpg" width="981" height="133" border="0"></td>
	</tr>
	<tr>
		<td style="background-color:#ffffff;padding:20px;">'.$message.'</td>
	</tr>
</table>
</body>
</html>
';
		$result = mail($to,''.$subject.'',$emailbody,"From:".$from."\n"."Content-type: text/html");
		if($result) {
			return true;
		} else {
			return $result;
		}
	}
}

function country_options($match = false,$names = false)
{
$countries = array('United Kingdom','Albania','Algeria','Antigua','Argentina','Armenia','Aruba','Australia','Austria','Azerbaijan',
'Bahamas','Bahrain','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bolivia','Bosnia','Brazil','Bulgaria','Burkina Faso','Burundi',
'Cameroon','Canada','Cayman Islands','Chad','Chile','China','Colombia','Congo','Costa Rica','Croatia','Curacao','Cyprus','Czech Republic',
'Denmark','Dominica','Ecuador','Egypt','El Salvador','Eritrea','Estonia','Ethiopia','Finland','France','Gabon','Georgia','Germany','Ghana',
'Greece','Greneda','Guam','Guatemala','Guinea','Guyana','Haiti','Honduras','Hong Kong','Hungary','India','Indonesia','Ireland','Israel','Italy',
'Ivory Coast','Jamaica','Jordan','Kazakhstan','Kenya','Kosovo','Kuwait','Kyrgyzstan','Latvia','Lebanon','Lithuania','Luxembourg','Macedonia',
'Madagascar','Mali','Malta','Martinique','Mauritania','Mexico','Mongolia','Morocco','Namibia','Neger','Nepal','Netherlands','New Zealand','Nicaragua',
'Nigeria','Norway','Oman','Pakistan','Panama','Paraguay','Peru','Philippines','Poland','Portugal','Puerto Rico','Qatar','Romania','Russia','S. Korea',
'Saudi Arabia','Scotland','Senegal','Singapore','Slovakia','Slovenia','Somalia','South Africa','Spain','Sri Lanka','St. Lucia','St. Maarten','St. Vincent',
'Surinam','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Togo','Trinidad & Tobago','Tunisia','Turkey','Turkmenistan','U.A.E.','Uganda',
'Ukraine','Uruguay','USA','Uzbekistan','Venezuela','Yemen','Yugoslavia');
	if($names == true)
	{
		return $countries;
	}
	else
	{
		$code = '';
		$selected = '';

		if($match) {
			$key = strtoupper(ereg_replace("[^A-Za-z]",'',$match));
		}

		foreach($countries AS $c) {
			if($match) {
				$lock = strtoupper(ereg_replace("[^A-Za-z]",'',$c));
				$selected = ($lock == $key) ? 'selected="selected"' : '';
			}
			$code .= '<option value="'.$c.'" '.$selected.'>'.$c.'</option>';
		}
		return $code;
	}
}
function location_options($match = false,$names = false)
{
	$countries = array(
		'united_kingdom' => array(
			'england' => array(
				'division' => 'England',
				'southend-on-sea' => 'Southend-on-Sea',
				'bath_and_north_east_somerset' => array('Bath and North East Somerset','Bath'),
				'bedfordshire' => array('Bedfordshire','Bedford','Dunstable'),
				'blackburn' => 'Blackburn',
				'blackpool' => 'Blackpool',
				'bournemouth' => 'Bournemouth',
				'bracknell' => 'Bracknell',
				'brighton' => 'Brighton',
				'hove' => 'Hove',
				'bristol' => 'Bristol',
				'buckinghamshire' => array('Buckinghamshire','Aylesbury','High Wycombe'),
				'cambridgeshire' => array('Cambridgeshire','Cambridge'),
				'cheshire' => array('Cheshire','Chester','Crewe','Ellesmere Port','Macclesfield','Northwich'),
				'cornwall' => array('Cornwall','Camborne-Redruth'),
				'cumbria' => array('Cumbria','Barrow-in-Furness','Carlisle'),
				'darlington' => 'Darlington',
				'derby' => 'Derby',
				'derbyshire' => array('Derbyshire','Chesterfield','Long Eaton'),
				'devon' => array('Devon','Exeter'),
				'dorset' => array('Dorset','Weymouth'),
				'east_sussex' => array('East Sussex','Eastbourne','Hastings'),
				'essex' => array('Essex','Basildon','Benfleet','Braintree','Brentwood','Chelmsford','Clacton-on-Sea','Colchester','Grays','Harlow'),
				'gloucestershire' => array('Gloucestershire','Cheltenham','Gloucester'),
				'greater_manchester' => array('Greater Manchester','Ashton-under-Lyne','Bolton','Bury','Cheadle and Gatley','Leigh','Manchester','Middleton','Oldham','Rochdale','Sale','Salford','Stockport','Wigan'),
				'halton' => array('Halton','Runcorn','Widnes'),
				'hampshire' => array('Hampshire','Aldershot','Basingstoke','Eastleigh','Fareham','Farnborough','Gosport','Havant','Locks Heath','Waterlooville','Winchester'),
				'hartlepool' => 'Hartlepool',
				'herefordshire' => array('Herefordshire','Hereford','Worcester','Bishops Stortford','Cheshunt','Hemel Hempstead','Saint Albans','Stevenge','Watford'),
				'kent' => array('Kent','Ashford','Chatham','Dartford','Folkestone','Gillingham','Gravesend','Maidstone','Margate','Royal Tunbridge Wells'),
				'kingston_upon_hull' => 'Kingston upon Hull',
				'lancashire' => array('Lancashire','Burnley','Lancaster','Lytham Saint Annes','Morecambe','Preston'),
				'leicester' => 'Leicester',
				'leicestershire' => array('Leicestershire','Hinckley','Loughborough'),
				'lincolnshire' => array('Lincolnshire','Lincoln'),
				'london' => 'London',
				'luton' => 'Luton',
				'merseyside' => array('Merseyside','Bebington','Birkenhead','Bootle','Crosby','Greasby','Huyton-with-Roby','Liverpool','Saint Helens','Southport','Wallasey'),
				'middlesbrough' => 'Middlesbrough',
				'milton_keynes' => array('Milton Keynes','Bletchley','Wolverton-Stony Stratford'),
				'norfolk' => array('Norfolk','Great Yarmouth','Norwich'),
				'north_east_lincolnshire' => array('North East Lincolnshire','Grimsby'),
				'north_lincolnshire' => array('North Lincolnshire','Scunthorpe'),
				'north_somerset' => array('North Somerset','Weston-super-Mare'),
				'north_yorkshire' => array('North Yorkshire','Harrogate'),
				'northamptonshire' => array('Northamptonshire','Corby','Kettering','Northampton','Wellingborough'),
				'nottingham' => 'Nottingham',
				'nottinghamshire' => array('Nottinghamshire','Beeston and Stapleford','Carlton','Mansfield','West Bridgeford'),
				'oxfordshire' => array('Oxfordshire','Banbury','Oxford'),
				'peterborough' => 'Peterborough',
				'plymouth' => 'Plymouth',
				'poole' => 'poole',
				'portsmouth' => 'Portsmouth',
				'reading' => 'Reading',
				'shropshire' => array('Shropshire','Shrewsbury'),
				'slough' => 'Slough',
				'somerset' => array('Somerset','Taunton','Yeovil'),
				'south_gloucestershire' => array('South Gloucestershire','Kingswood'),
				'south_yorkshire' => array('South Yorkshire','Barnsley','Doncaster','Rotherham','Sheffield'),
				'southampton' => 'Southampton',
				'staffordshire' => array('Staffordshire','Burton-upon-Trent','Cannock','Newcastle-under-Lyme','Stafford','Tamworth'),
				'stockton-on-tees' => 'Stockton-on-Tees',
				'stoke-on-trent' => 'Stoke-on-Trent',
				'suffolk' => array('Suffolk','Ipswich','Lowestoft'),
				'surrey' => array('Surrey','Camberley-Frimley','Crawley','Epsom and Ewell','Esher-Molesey','Guildford','Leatherhead','Reigate-Redhill','Staines','Walton and Weybridge','Woking-Byfleet'),
				'swindon' => 'Swindon',
				'torbay' => array('Torbay','Paignton','Torquay'),
				'tyne_and_wear' => array('Tyne and Wear','Gateshead','Newcastle upon Tyne','South Shields','Sunderland','Washington'),
				'warrington' => 'Warrington',
				'warwickshire' => array('Warwickshire','Nuneaton','Royal Leamington Spa','Rugby'),
				'west_midlands' => array('West Midlands','Birmingham','Coventry','Dudley','Halesowen','Oldbury-Smethwick','Solihull','Stourbridge','Sutton Coldfield','Walsall','West Bromwich','Wolverhampton'),
				'west_sussex' => array('West Sussex','Bognor Regis','Horsham','Littlehampton','Worthing'),
				'west_yorkshire' => array('West Yorkshire','Batley','Bradford','Dewsbury','Halifax','Huddersfield','Keighley','Leeds','Morley','Wakefield'),
				'windsor_and_maidenhead' => array('Windsor and Maidenhead','Maidenhead'),
				'worcestershire' => array('Worcestershire','Kidderminster','Redditch'),
				'york' => 'York'
			),
			'scotland' => array(
				'division' => 'Scotland'
			)
		)
	);
	
	$metropolitan = array();
	
	if($names == true)
	{
		if($match)
		{
			$match = str_replace(' ', '_', $match);
			$normalized = ucwords(str_replace('_', ' ', $match));
			if(array_key_exists(strtolower($match),$countries))
			{
				return $normalized;
			}
			else
			{
				foreach($countries AS $country)
				{
					if(array_key_exists(strtolower($match),$country))
					{
						return $normalized;
					}
					else
					{
						$match = str_replace('_',' ',$match);
						foreach($country AS $county)
						{
							if(is_array($county))
							{
								foreach($county AS $towns)
								{
									if(is_array($towns))
									{
										foreach($towns AS $town)
										{
											//echo $town.'<br/>';
											if(strtolower($match) == strtolower($town))
											{
												return $town;
											}
										}
									}
									else
									{
										if(strtolower($match) == strtolower($towns))
										{
											return $towns;
										}
									}
								}
							}
							else
							{
								if(strtolower($county) == strtolower($match))
								{
									return $area;
								}
							}
						}
					}
				}
			}
			return false;
		}
		else
		{
			return array($locations,$metropolitan);
		}
	}
	else
	{
		return false;
	}
}
?>
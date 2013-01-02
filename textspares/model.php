<?php
define('IN_TEXTSPARES',true);
include('suppliers/Includes/config/php_enviroment.php');
include('suppliers/Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

$cat_id = mysql_real_escape_string($_REQUEST['q']);
$sql = mysql_query('SELECT `make_id`, `make_name` FROM `vehicle_makes` WHERE `parent_id` = \''.$cat_id.'\' ')or die(mysql_error());
if(mysql_num_rows($sql) > 0)
{
	while($rec=mysql_fetch_array($sql))
	{
		if(empty($num))
		{
			$num = $rec['make_name'].'='.$rec['make_id'];
		} else {
			$num = $num.','.$rec['make_name'].'='.$rec['make_id'];
		}
	}
	echo $num;
} else {
	echo 'No Models Found';
}	

//echo "select make_id,make_name from vehicle_makes where parent_id='$cat_id'";
?>
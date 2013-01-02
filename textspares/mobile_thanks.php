<?
//extract($_POST); 
define('IN_TEXTSPARES',true);
include('suppliers/Includes/config/php_enviroment.php');
include('suppliers/Includes/ez_sql.inc.php');
$db = new db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);

	$user_number = $db->mysql_prep($_POST['number']);
	$keywrd = $db->mysql_prep($_POST['number']);
	$mssg = $db->mysql_prep($_POST['message']);
	$ntwrk = $db->mysql_prep($_POST['network']);

mysql_query("insert into userDetails(number,keyword,message,network) values('$user_number','$keywrd','$mssg','$ntwrk')")or die(mysql_error());
/*
echo"<form name='mobile' method='get' action='http://web.textvertising.co.uk/cgi-bin/smssend.pl'>";
echo"<input type='hidden' name='numbers' value='47771716012'>";
echo"<input type='hidden' name='user' value='spares'>";
echo"<input type='hidden' name='pass' value='catw63'>";
echo"<input type='hidden' name='message' value='Please reply SPARES to receive the information to your mobile as per the webiste. Each txt costs £1.50. Txt STOP to end'>";
echo"<input type='hidden' name='report' value='STD'>";
echo"<input type='hidden' name='smsid' value='82055'>";
echo"<input type='hidden' name='expiry' value='1231'>";
echo"</form>";
echo"<script>document.mobile.submit()</script>";
*/
?>
username :TEXTSPARES
password :o9fs168
number - the number of the person who text in
keyword - the keyword used when texting in
message - the message body sent (excluding your keyword at the start)
network - the clients network
time - the time the request was received.

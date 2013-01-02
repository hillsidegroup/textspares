<?
$conn = mysql_connect("db1393.oneandone.co.uk","dbo239121714","Y4qGbZeY");
mysql_select_db("db239121714",$conn);

if(isset($_GET['getCountriesByLetters']) && isset($_GET['letters'])){
	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	$res = mysql_query("SELECT post. * , loc. * FROM postcodes post, locations loc WHERE post.post_id = loc.post_id AND post.postcode LIKE '".$letters."%'") or die(mysql_error());
	#echo "1###select ID,countryName from ajax_countries where countryName like '".$letters."%'|";
	##echo"select country_id,country_name from countries where post_code like '".$letters."%'";
	if(mysql_num_rows($res)>0){
	while($inf = mysql_fetch_array($res)){
		echo $inf["post_id"]."###".$inf["postcode"]."-".$inf["location_name"]."|";
	}	
	}else{
	$cntid=0;
	$cntname="No Match Found";
	echo $cntid."###".$cntname."|";
}
}
?>

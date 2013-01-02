<?php
require_once("admin/include/connection.inc.php");
	$result1 = mysql_query("select part_cat_name,part_id from parts_categories order by part_cat_name");
	if (mysql_num_rows($result1) > 0 )
	{
		$str="";
		while ($row1 = mysql_fetch_array($result1))
		{
			$str.="$row1[part_cat_name]=$row1[part_id],";
		}	
	}else
	{
		$str="";
	}
echo $str;

?>
<?
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
switch($_REQUEST[call]){
	case"":
	showval();
	break;
}
bottom();

function showval(){
	?>
<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%" border="0">
        		<tr>
              <td valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<?
              		$sql=mysql_query("select content,page_id from files where file_id='$_REQUEST[fileid]'")or die(mysql_error());
              		$rec=mysql_fetch_array($sql);
              		//$content=stripslashes($rec[content]);
              		$content=str_replace("../cmtimages/","cmtimages/",(stripslashes($rec[content])));
              		echo $content; 
              		?>
              	</td></tr>
              	<tr><td>
              		<table cellpadding="1" cellspacing="1" width="100%" border="0" align="center">
              	<?
              		$sqlm=mysql_query("select make_name from vehicle_makes where parent_id='$rec[page_id]'")or die(mysql_error());
              		$parent_name=getparent($rec[page_id]);
              		if(mysql_num_rows($sqlm)>0){
              			$colsPerRow = 4;
              			while($data=mysql_fetch_array($sqlm)){
              				if ($i % $colsPerRow == 0) {
                            							echo"<tr>";
                            							 }
              				echo"<td><a href='' class='midlinks'>".$parent_name." ".$data[make_name]."</a></td>";
              				if ($i % $colsPerRow == $colsPerRow - 1) {
                            							echo '</tr>';
                            							} 
                            							$i += 1;
              			}
              			
              			}
              					
              		?>
             </table>
            </td></tr>
              	</table>
             
             </td></tr> 	
             
         </table>   
        	
      </td></tr>	
</table>        
<?	
}
?>

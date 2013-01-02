<?
session_start();
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
$call = empty($_REQUEST['call']) ? false : $_REQUEST['call'];
switch($call){
	case"":
	middle('');
	break;
	
	case"chk_user":
	chkUser();
	break;
	
	case"delete":
	deleteRequest();
	break;
	
	case"show_details":
	show_details();
	break;
	
	case"quote":
	submit_quotes();
	break;
	
	case"save":
	savequote();
	break;
	
	case"show_quotes":
	show_quotes();
	break;
}
bottom();
function middle($msg){
?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%"  align="left" class="content"><strong>UK's Top Online Car Parts Network</strong></td></tr>
                
          </table>
          </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		     <tr>
              <td valign="top">
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<table cellpadding="2" cellspacing="2" width="100%" border="0" >
              		<tr><td><img src="images/head-makes.gif"></td></tr>
              		<tr><td>
              		<table cellpadding="1" cellspacing="1" width="100%">
              			<?
              			$sql=mysql_query("select make_id,make_name from vehicle_makes where parent_id='0'")or die(mysql_error());
              			$colsPerRow = 3;
						$i = 0;
              			while($data=mysql_fetch_array($sql))
						{
              				$fileid=getfileId($data['make_id']);
              				$CNAME=str_replace(" ","-",$data['make_name']);
              				if ($i % $colsPerRow == 0) {
								echo"<tr>";
							}
              				echo '<td><a href="'.BASE.'MakeDetails/'.$fileid.'/'.$CNAME.'" class="midlinks">'.$data['make_name'].'</a></td>';
              				if ($i % $colsPerRow == $colsPerRow - 1) {
								echo '</tr>';
							} 
							$i += 1;
              			}
              			?>
              		</table>	
              	</td></tr>	
              	<tr><td><img src="images/pixel-blue.gif" width="520px" height="1px"></td></tr>
              	<tr><td><img src="images/head-parts.gif"></td></tr>
              	<tr><td>
              		<table cellpadding="1" cellspacing="1" width="100%">
              			<?
              			$sqlc=mysql_query("select part_cat_name,part_id from parts_categories order by part_cat_name")or die(mysql_error());
              			$colsPerRow = 2;
						$x = 0;
              			while($row=mysql_fetch_array($sqlc))
						{
              				if ($x % $colsPerRow == 0) {
								echo"<tr>";
							}
              				echo '<td><a href="partrequest.php?pid='.$row['part_id'].'" class="midlinks">'.$row['part_cat_name'].'</a></td>';
              				if ($x % $colsPerRow == $colsPerRow - 1) {
								echo '</tr>';
							} 
							$x += 1;
              			}
              			?>
              		</table>	
              	</td></tr>
              		 </table>
              		
              	</td></tr>
              		
              </table>
               <tr>
              <td></td>
            </tr>
             </td></tr> 	
             
         </table>   
        	
      </td></tr>	
</table> 
<?}?>
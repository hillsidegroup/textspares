<?
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
?>
<script type="text/javascript">
	/*
if (window.addEventListener)
window.addEventListener("load", initializemarquee, false)
else if (window.attachEvent)
window.attachEvent("onload", initializemarquee)
else if (document.getElementById)
window.onload=initializemarquee
*/
</script>
<?
common_middle();
switch($_REQUEST[call]){
	case"":
	showval();
	break;
}
bottom();

function showval(){
	$sqlnm=mysql_query("select file_name,page_id from files where file_id='$_REQUEST[fileid]'")or die(mysql_error());
	$recnm=mysql_fetch_array($sqlnm);
	$bmkid=$recnm[page_id];
	$sql22=mysql_query("select make_name,make_id,parent_id from vehicle_makes where make_id='$bmkid'")or die(mysql_error());
	$rec22=mysql_fetch_array($sql22);
	$mkname=$rec22[make_name];
	//$mkname=str_replace("_"," ",$recnm[file_name]);
	?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%" border="0">
        	 	<tr><td><a href="index.php" class='toplinks'>Home</a> > <a href="faqs.php" class='toplinks'>FAQS</a></td></tr>
        	 	<tr><td>&nbsp;</td></tr>
        	 	<!--<tr>
          <td>
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="29"><img src="images/h-our.gif" width="551" height="29" alt=""/></td>
              </tr>
              <tr>
                <td height="139" style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff"><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                  <td >
                  	
                  	<div id='marqueecontainer' onmouseover="copyspeed=pausespeed" onmouseout="copyspeed=marqueespeed">
                  		<div id='vmarquee' style='position: absolute; width: 100%;'>
                  			<table cellpadding="1" cellspacing="1">
                  				<?
                  				/*
                  				$sqlbanner=mysql_query("select banner_image from spare_sup_banners where make_id='$bmkid' order by banner_id")or die(mysql_error());
                  				if(mysql_num_rows($sqlbanner)>0){
                  					while($banners=mysql_fetch_array($sqlbanner)){
                  						echo"<tr><td><img src='banners/$banners[banner_image]' alt=''/></td></tr>";
                  					}
                  				}
                  				*/
                  				?>
                  				                  						
                  		</table>	
                  		</div>
                  		</div>
                  
                  </td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td valign="top"><img src="images/footerpart.gif" width="551" height="12" alt=""/></td>
              </tr>
          </table></td>
        </tr>-->
        		<tr>
              <td valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<?
        	$sqld=mysql_query("select * from files where file_name='faqs'")or die(mysql_error());
        	$recd=mysql_fetch_array($sqld);
        	$content=str_replace("../cmtimages/","cmtimages/",(stripslashes($recd[content])));
        	echo $content;
        	?>
              	</td></tr>
              	<?
              	$parent_name=getparent($rec[page_id]);
              	?>
              	
              	<tr><td>
              		<table cellpadding="0" cellspacing="1" width="100%" border="0" align="center">
              	<?
              	/*
              		$sqlm=mysql_query("select make_name,make_id from vehicle_makes where parent_id='$rec[page_id]'order by disp_order")or die(mysql_error());
              		echo"select make_name,make_id from vehicle_makes where parent_id='$rec[page_id]'order by disp_order";
              		if(mysql_num_rows($sqlm)>0){
              			$colsPerRow = 4;
              			while($data=mysql_fetch_array($sqlm)){
              				$fileid=getfileId($data[make_id]);
              				if ($i % $colsPerRow == 0) {
                            							echo"<tr>";
                            							 }
              				echo"<td><a href='modeldetails.php?mid=$fileid&fileid=$_REQUEST[fileid]' class='midlinks'>".$parent_name." ".$data[make_name]."</a></td>";
              				if ($i % $colsPerRow == $colsPerRow - 1) {
                            							echo '</tr>';
                            							} 
                            							$i += 1;
              			}
              			
              			}
              			*/		
              		?>
             </table>
            </td></tr>
              	</table>
             </td></tr> 	
             <tr><td>&nbsp;</td></tr>
      <tr><td>
        
        <tr><td>&nbsp;</td></tr>	
      </td></tr>
            
             
         </table>   
        	
      </td></tr>	
</table>        
<?	
}
?>

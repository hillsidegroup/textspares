<?php
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
$res_file['pageTitle'] = 'New and Used'.($meta_make ? ' '.($meta_make) : '').' Gearboxes '.($meta_area ? $meta_area : 'UK');
top();
?>
<script type="text/javascript">

if (window.addEventListener)
window.addEventListener("load", initializemarquee, false)
else if (window.attachEvent)
window.attachEvent("onload", initializemarquee)
else if (document.getElementById)
window.onload=initializemarquee

</script>
<?php
common_middle();
showval();
bottom();

function showval(){
	global $db;
	$sqlnm=mysql_query('SELECT `file_name`,`page_id` FROM `files` WHERE `file_id` = \''.$db->mysql_prep($_REQUEST['fileid']).'\'')or die(mysql_error());
	$recnm=mysql_fetch_array($sqlnm);
	$bmkid=$recnm['page_id'];
	$sql22=mysql_query('SELECT `make_name`,`make_id`,`parent_id` FROM `vehicle_makes` WHERE `make_id`= \''.$bmkid.'\'')or die(mysql_error());
	$rec22=mysql_fetch_array($sql22);
	$mkname=$rec22['make_name'];

	?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%" border="0">
        	 	<tr><td><a href="<?=BASE?>index.php" class='toplinks'>Home</a> &gt; <a href="gearbox.php" class='toplinks'>Gearboxes</a></td></tr>
        		<tr>
              <td valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<?
              		$sqld=mysql_query("select * from files where file_name='gearbox'")or die(mysql_error());
        	$recd=mysql_fetch_array($sqld);
        	$content=str_replace("../cmtimages/","/cmtimages/",(stripslashes($recd['content'])));
        	echo $content;
              		?>
              	</td></tr>
              	            	
              	<tr><td>
              		<table cellpadding="0" cellspacing="1" width="100%" border="0" align="center">
              	<?
 
              		$sqlm = mysql_query('select make_name,make_id from vehicle_makes where parent_id=\''.$recnm['page_id'].'\' order by disp_order')or die(mysql_error());
              		if(mysql_num_rows($sqlm)>0){
              			$colsPerRow = 4;
						$i = 0;
              			while($data=mysql_fetch_array($sqlm)){
              				$fileid=getfileId($data['make_id']);
              				if ($i % $colsPerRow == 0) {
							echo"<tr>";
							 }
              				echo '<td><a href="'.BASE.'modeldetails.php?mid='.$fileid.'&fileid='.$_REQUEST['fileid'].'" class="midlinks">'.$data['make_name'].'</a></td>';
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

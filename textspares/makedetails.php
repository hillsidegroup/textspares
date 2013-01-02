<?php
define('IN_TEXTSPARES',true);
include("include/common.inc.php");

$res_file = array();
$field_id = (int) (!empty($_REQUEST['fileid'])) ? $_REQUEST['fileid'] : 0;
$data = $db->get_row('SELECT `file_name`,`page_id`,`content`,`title`,`page_title`,`meta_desc` FROM `files` WHERE `file_id` = \''.$field_id.'\'');

if($data)
{
	$res_file['pageDesc'] = $data->meta_desc;
	$res_file['pageTitle'] = $data->page_title;
}
if(!empty($_GET['make']) && strlen($res_file['pageTitle']) < 2)
{
	$res_file['pageTitle'] = $_GET['make'].' Car Parts,Used '.$_GET['make'].' Car Parts, '.$_GET['make'].' Car Breakers, '.($meta_area ? $meta_area : 'UK');
}
top();
common_middle();
showval();
bottom();

function showval()
{
	global $session,$db,$data,$field_id;
	
	$bmkid=$data->page_id;
	$sql22=mysql_query("select `make_name`,`make_id`,`parent_id` from `vehicle_makes` where `make_id`='$bmkid'")or die(mysql_error());
	$rec22=mysql_fetch_array($sql22);
	$mkname = $rec22['make_name'];
	$CNAME = str_replace(" ","-",$mkname);
	?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%" border="0">
        	 	<tr><td><a href="<?=BASE?>index.php" class="toplinks">Home</a> &gt; <a href="<?=BASE?>MakeDetails/<?=$_REQUEST['fileid']?>/<?=$CNAME?>" class='toplinks'><?=$mkname?></a> &lt; <a href="<?=BASE;?>CarSpares/<?=$mkname?>" title="Get free <?=$mkname?> car part quotations!">GET FREE PART QUOTATIONS NOW</a></td></tr>
        		<tr>
              <td valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<?php
              		$sql=mysql_query("select content,page_id from files where file_id='$field_id'")or die(mysql_error());
              		$rec=mysql_fetch_array($sql);
              		$findme="no_box";
              		$pos = strpos($rec['content'], $findme);
              		if ($pos === false) {
              			$disply="y";
              			} else {
              				
              				$content=str_replace("no_box"," ",$rec['content']);
              				 $disply="n";
              				  }
              		$content=str_replace("../",BASE,(stripslashes($rec['content'])));
              		echo $content; 
              		
              		?>
              	</td></tr>
              	<?php
              	$parent_name=getparent($rec['page_id']);
              	$sqlm=mysql_query('SELECT `v`.`make_name`,`v`.`make_id`,`pc`.* FROM `vehicle_makes` AS `v` ,`page_categories` AS `pc` WHERE `v`.`make_id` = `pc`.`make_id` AND `pc`.`type` = \'cm\' AND `pc`.`file_id` = \''.$db->mysql_prep($field_id ).'\' ORDER BY `set_order` ')or die(mysql_error());
              	if(mysql_num_rows($sqlm)>0){
              	?>
              	<tr><td><b><?=$parent_name?> Car Models:</b></td></tr>
              	<tr><td>
              		<table cellpadding="0" cellspacing="1" width="100%" border="0" align="center">
              	<?php
              		$colsPerRow = 3;
					$i = 0;
              			while($data=mysql_fetch_array($sqlm)){
              				$fileid=getfileId($data['make_id']);
              				$CNAME=str_replace(" ","-",$parent_name);
                       $CNAME.="-".str_replace(" ","-",$data['make_name']);
              				if ($i % $colsPerRow == 0) {
                       echo"<tr>";
                        }
              				echo '<td><a href="'.BASE.'ModelDetails/'.$fileid.'/'.$_REQUEST['fileid'].'/'.$CNAME.'" class="midlinks">'.$parent_name.' '.$data['make_name'].'</a></td>';
              				if ($i % $colsPerRow == $colsPerRow - 1) {
                            	echo '</tr>';
							} 
							$i++;
              			}
              		?>
             </table>
            </td></tr>
            
          <?php
          }?>
          <tr><td>&nbsp;</td></tr>
          <?php
              	$parent_name=getparent($rec['page_id']);
              	$sqlm=mysql_query('SELECT `v`.`make_name`,`v`.`make_id`,`pc`.* FROM `vehicle_makes` AS `v`,`page_categories` AS `pc` WHERE `v`.`make_id` = `pc`.`make_id` AND `pc`.`type` = \'sv\' AND `pc`.`file_id` = \''.$db->mysql_prep($field_id ).'\' ORDER BY `set_order` ')or die(mysql_error());
              	if(mysql_num_rows($sqlm)>0){
              	?>
              	<tr><td><b><?=$parent_name?> 4x4 Models:</b></td></tr>
              	<tr><td>
              		<table cellpadding="0" cellspacing="1" width="100%" border="0" align="center">
              	<?php
              		//$sqlm=mysql_query("select make_name,make_id from vehicle_makes where parent_id='$rec[page_id]'order by disp_order")or die(mysql_error());
              		//echo"select make_name,make_id from vehicle_makes where parent_id='$rec[page_id]'order by disp_order";
              		$colsPerRow = 3;
					$n = 0;
              			while($data=mysql_fetch_array($sqlm)){
              				$CNAME=str_replace(" ","-",$parent_name);
                       $CNAME.="-".str_replace(" ","-",$data['make_name']);
              				$fileid=getfileId($data['make_id']);
              				if ($n % $colsPerRow == 0) {
                       echo"<tr>";
                        }
              				echo '<td><a href="'.BASE.'ModelDetails/'.$fileid.'/'.$_REQUEST['fileid'].'/'.$CNAME.'" class="midlinks">'.$parent_name.' '.$data['make_name'].'</a></td>';
              				if ($i % $colsPerRow == $colsPerRow - 1) {
							echo '</tr>';
							} 
							$n++;
              			}
              			
              			
              					
              		?>
             </table>
            </td></tr>
            
          <?php
          }
          ?>
          <tr><td>&nbsp;</td></tr>
          <?php
			$parent_name=getparent($rec['page_id']);
			$sqlm=mysql_query('select v.make_name,v.make_id,pc.* from vehicle_makes v,page_categories pc where v.make_id=pc.make_id and pc.type=\'cc\' and pc.file_id=\''.$_REQUEST['fileid'].'\' order by `set_order` ')or die(mysql_error());
			if(mysql_num_rows($sqlm)>0)
			{
              	?>
              	<tr><td><b><?=$parent_name?> Commercial Models:</b></td></tr>
              	<tr><td>
              		<table cellpadding="0" cellspacing="1" width="100%" border="0" align="center">
              	<?php
				$colsPerRow = 3;
				$x = 0;
				while($data=mysql_fetch_array($sqlm))
				{
					$CNAME=str_replace(" ","-",$parent_name);
					$CNAME.="-".str_replace(" ","-",$data['make_name']);
					$fileid=getfileId($data['make_id']);
					if ($x % $colsPerRow == 0) {
						echo"<tr>";
					}
					echo '<td><a href="'.BASE.'ModelDetails/'.$fileid.'/'.$_REQUEST['fileid'].'/'.$CNAME.'" class="midlinks">'.$parent_name.' '.$data['make_name'].'</a></td>';
					if ($x % $colsPerRow == $colsPerRow - 1) {
						echo '</tr>';
					} 
					$x++;
				}
              			
              			
              					
              		?>
             </table>
            </td></tr>
          <?php }
          ?>
              	</table>
             </td></tr> 	
             <tr><td>&nbsp;</td></tr>
             
          <?php
          if($disply=='y'){
          ?>   
             
      <tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%">
        		<tr>
             <td>
            	<table cellpadding="0" cellspacing="0" background="<?=BASE?>images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">RECENT PART REQUESTS</td></tr>
            </table>
            	</td>
          </tr>
          <tr>
              <td  style="background:url(<?=BASE?>images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr>
                <td>
                	<table cellpadding="1" cellspacing="1" width="100%" id="TBorder">
                		<tr><td class="Cheader">Time</td><td class="Cheader">Make</td><td class="Cheader">Model</td><td class="Cheader">Part</td></tr>
                 <?php
                 $sqlreq = mysql_query('SELECT `p`.`part_name`,`p`.`request_stamp`,`v`.`vehicle_make_name`,`v`.`vehicle_model_name` FROM `customer_requests` AS `p` JOIN `customer_vehicles` AS `v` ON `v`.`vehicle_id` = `p`.`vehicle_id` WHERE `p`.`vehicle_id` = `v`.`vehicle_id` AND `v`.`vehicle_make` = \''.$rec['page_id'].'\' ORDER BY `p`.`request_stamp` DESC LIMIT 10')or die(mysql_error());
                 if(mysql_num_rows($sqlreq)>0){
                 	while($rdata=mysql_fetch_array($sqlreq)){
  
                 		$reqtime = date('H:i',$rdata['request_stamp']);
                 		
                 		echo '<tr><td>'.$reqtime.'</td><td><a href="'.BASE.'FreeQuotes/'.$rdata['vehicle_make_name'].'" title="FREE '.$rdata['vehicle_make_name'].' Car Part Quotations">'.$rdata['vehicle_make_name'].'</a></td><td>'.$rdata['vehicle_model_name'].'</td><td>'.$rdata['part_name'].'</td></tr>';
                 	}
					 mysql_free_result($sqlreq);
                }
                 ?>
                </table>
                </td>
                  </tr>
                  
                  </table>             
              </td>
            </tr>
            <tr>
              <td><img src="<?=BASE?>images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table></td></tr>
        <tr><td>&nbsp;</td></tr>	
      </td></tr>
            <tr><td class="disclaimer">* <?=$mkname?> is in no way directly affiliated or associated with TextSpares. We do not sell new genuine <?=$mkname?> parts.</td></tr>
             <?php
            }
             ?>
             
             
             
             
         </table>   
        	
      </td></tr>	
</table>        
<?php
}
?>

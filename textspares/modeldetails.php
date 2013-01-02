<?
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
showval();
bottom();

function showval(){
	$sqlnm = mysql_query('SELECT `file_name`,`page_id` FROM `files` WHERE `file_id` = \''.$_REQUEST['fileid'].'\'')or die(mysql_error());

	$recnm=mysql_fetch_array($sqlnm);
	$bmkid=$recnm['page_id'];
	$sql22=mysql_query('SELECT `make_name`,`make_id`,`parent_id` FROM `vehicle_makes` WHERE `make_id` = \''.$bmkid.'\'')or die(mysql_error());
	$rec22=mysql_fetch_array($sql22);
	$mkname=$rec22['make_name'];

	$sqlmn=mysql_query('SELECT `file_name`,`page_id` FROM `files` WHERE `file_id` = \''.$_REQUEST['mid'].'\'')or die(mysql_error());
	$recmn=mysql_fetch_array($sqlmn);
	$mnid=$recmn['page_id'];
	
	$sql22=mysql_query('SELECT `make_name`,`make_id`,`parent_id` FROM `vehicle_makes` WHERE `make_id`= \''.$mnid.'\'')or die(mysql_error());
	$rec22=mysql_fetch_array($sql22);
	$mname=$rec22['make_name'];

	$CNAME=str_replace(" ","-",$mkname);
    $MDNAME=str_replace(" ","-",$mkname);
    $MDNAME.="-".str_replace(" ","-",$mname);
	?>
<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%" border="0">
        	 	<tr><td><a href="<?=BASE?>index.php" class='toplinks'>Home</a> &gt; <a href="<?=BASE?>MakeDetails/<?=$_REQUEST['fileid']?>/<?=$CNAME?>" class='toplinks'><?=$mkname?></a> &gt;
        	 	 <a href="<?=BASE?>ModelDetails/<?=$_REQUEST['mid']?>/<?=$_REQUEST['fileid']?>/<?=$MDNAME?>" class='toplinks'><?=$mname?></a> &lt; <a href="<?=BASE;?>CarSpares/<?=$CNAME?>" title="Get free <?=$CNAME?> car part quotations!">GET FREE PART QUOTATIONS NOW</a></td></tr>
        		<tr>
              <td valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              		<?
              		$sql=mysql_query('SELECT `content`,`page_id` FROM `files` WHERE `file_id` = \''.$_REQUEST['mid'].'\'')or die(mysql_error());
              		$rec=mysql_fetch_array($sql);
              		$content=str_replace("../cmtimages/","/cmtimages/",(stripslashes($rec['content'])));
              		$modeltype=$rec['page_id'];
              		echo $content; 
              		?>
              	</td></tr>
              	<tr><td>
              		<table cellpadding="1" cellspacing="1" width="100%" border="0" align="center">
              	
             </table>
            </td></tr>
              	</table>
            </td></tr> 	
             <tr><td>&nbsp;</td></tr>
      <tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td align="center" height="70px"><a href="<?=BASE?>partrequest.php"><img src="<?=BASE?>images/requestQuotes_btn.gif" alt="Request Quotes" border="0"></a></td></tr>
        		
        		<tr>
             <td>
            	<table cellpadding="0" cellspacing="0" background="<?=BASE?>images/h-blank.gif" width="217" height="35">
            		
            	<tr><td class="heading1">Request quotes now <span style="color:red;font-size:16px">FREE</span></td></tr>
            </table>
            	</td>
          </tr>
          <tr>
              <td  style="background:url(<?=BASE?>images/grad.gif);background-repeat:repeat-x;border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="5">
              		<tr><td>
              		<table cellpadding="0" cellspacing="5px" width="100%" border="0" align="center">
              	<?
              		$sqlm=mysql_query("select * from parts_categories  order by part_cat_name")or die(mysql_error());

              		$sqlmak=mysql_query('SELECT `page_id` FROM `files` WHERE `file_id` = \''.$_REQUEST['fileid'].'\'')or die(mysql_error());
              		$recs=mysql_fetch_array($sqlmak);
              		$makeid=$recs['page_id'];
					$i = 0;
              		if(mysql_num_rows($sqlm)>0)
					{
              			$colsPerRow = 3;
              			while($data=mysql_fetch_array($sqlm))
						{
							if ($i % $colsPerRow == 0)
							{
								echo"<tr>";
							}
              				echo '<td><a href="'.BASE.'partrequest.php?pid='.$data['part_id'].'&modeltype='.$modeltype.'&makeid='.$makeid.'" class="prtmidlinks">'.$data['part_cat_name'].'</a></td>';
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
              </td>
            </tr>
            <tr>
              <td><img src="<?=BASE?>images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table>
        <tr><td>&nbsp;</td></tr>	
       <tr><td class="disclaimer">* <?=$mkname?> is in no way directly affiliated or associated with TextSpares. We do not sell new genuine <?=$mkname?> parts.</td></tr>
             
         </table>   
        	
      </td></tr>	
</table>        
<?	
}
?>

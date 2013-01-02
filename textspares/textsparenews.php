<?
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
switch($_REQUEST[call]){
	case"":
	middle();
	break;
	
	case"show":
	showval();
	break;
}
bottom();
function middle(){?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="116" colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" align="left" style="padding-left:3px"><img src="images/findparts.gif"/></td>
              </tr>
              <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%" height="80" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong><br />
                  We have wide range of new and usedcar parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%">
        		<tr>
            <td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">NEWS</td></tr>
            </table>
            	</td>
          </tr>
          <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
                 <?
                 $sql=mysql_query("select description  from spare_news where news_id='$_REQUEST[news_id]'")or die(mysql_error());
                 if(mysql_num_rows($sql)>0){
                 	$rec=mysql_fetch_array($sql);
                 	$content=str_replace('../cmtimages/','cmtimages/',(stripslashes($rec[description])));
                 	echo $content;
                 	
                }
                 ?>
                </td></tr>
              </table></td>
            </tr>	
          	<tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table>	
      </td></tr>	
      
</table>
<?
}
?>
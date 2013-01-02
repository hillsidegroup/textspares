<?
session_start();
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
switch($_REQUEST[call]){
	case"":
	middle();
	break;
	case"send_email":
	old_send_email();
	break;
	
}
bottom();
function middle(){
?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
       <tr>
          <td  colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" align="left" style="padding-left:3px"><img src="images/findparts.gif"/></td>
              </tr>
              <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%" height="80" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong><br />
                  We have wide range of new and used car parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td>
            	<table cellpadding="0" cellspacing="0">
            		<tr><td width="11" height="35" ><img src="images/left_part.gif" width="11" height="35" /></td>
            			<td style="background:url(images/repeat_bg.gif)" width="120px" align="left"><span class="heading1">Contact Us</span></td>
            			 <td width="11" height="35"><img src="images/right_part.gif" width="11" height="35" /></td>
            			 </tr>
            	</table>
            </td></tr>
            <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              			<form name="cntfrm" method="post" action="contact_us.php" onsubmit="return chk_contact_form(this)">
              				<input type="hidden" name="call" value="send_email">
              			<table cellpadding="2" cellspacing="2" width="100%" border="0" >
              				<?
              		if($_REQUEST[errorMessage]<>""){
              		echo"<tr bgcolor='#AB0000'><td colspan='2' class='heading1' align='center'>$_REQUEST[errorMessage]</td></tr>";
              	 }
              		?>
              				<tr><td class="formtext">Name:</td><td><input type="text"  value="<?=$_POST[cname]?>" name="cname" class="formtxtbox" onchange="chk_contact_form(this.form)">&nbsp;<span id="error1"></span></td></tr>
              					<tr><td class="formtext">Email:</td><td><input type="text"   value="<?=$_POST[cemail]?>" name="cemail" class="formtxtbox" onchange="chk_contact_form(this.form)">&nbsp;<span id="error2"></span></td></tr>
              					<tr><td class="formtext">Telephone:</td><td><input type="text" name="telephone" value="<?=$_POST[telephone]?>"  class="formtxtbox" onchange="chk_contact_form(this.form)">&nbsp;<span id="error3"></span></td></tr>	
              					<tr><td class="formtext">Reference #:</td><td><input type="text" name="refid" value="<?=$_POST[refid]?>" class="formtxtbox">&nbsp;<span id="error4"></span></td></tr>		
              					<tr><td class="formtext" valign="top">Comments:</td><td><textarea name="comments" rows="7" cols="28" onchange="chk_contact_form(this.form)"><?=$_POST[comments]?></textarea>&nbsp;<span  id="error5"></span></td></tr>		
              							<tr><td class="formtext">Security Code:</td><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="images/simg/randomImage3.php" alt=""/></font></td></tr>
              		<tr><td class="formtext">Enter the security code shown:</td><td><input type="text" name="security_code" class="formtxtbox">&nbsp;<span id="error6"></span></td></tr>
              					<tr><td>&nbsp;</td><td><input type="submit" value="Send Info"></td></tr>	
              		  </table>
              		</form>
              	</td></tr>
              		
              </table>
               <tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
             </td></tr> 	
             
         </table>   
        	
      </td></tr>	
</table> 
<?
}
//TODO: Prevent viral JS script injection!
function old_send_email(){
	$number   = $_POST[security_code];

	if (md5($number) == $_SESSION[image_random_value]){
		$_SESSION[image_random_value] = '';
		$txbody="<table cellpadding='1' cellspacing='1' width='100%'>";
		$txbody.="<tr><td colspan='2'>A new Contact Form</td></tr>";
		$txbody.="<tr><td colspan='2'><b>Contact Details</b></td></tr>";
		$txbody.="<tr><td>Name</td><td>".$_POST[cname]."</td></tr>";
		$txbody.="<tr><td>Email</td><td>".$_POST[cemail]."</td></tr>";
		$txbody.="<tr><td>Telephone</td><td>".$_POST[telephone]."</td></tr>";
		$txbody.="<tr><td>Comments</td><td>".$_POST[comments]."</td></tr>";
		$txbody.="</table>";
		$sqladmin=mysql_query("select email from admin_users where admin_user_id='1'")or die(mysql_error());
		$recadmin=mysql_fetch_array($sqladmin);
		
		mail($recadmin[email],"New contact Form",$txbody,"From:".$_POST[cemail]." \n"."Content-type:text/html");
	}else{
		$mssg=" Sorry, you have provehicle_ided an invalid security code.";
	echo"<form name='backfrm' method='post' action='contact_us.php'>";
	foreach($_POST as $key=>$val){
		if($key!="call"){
			echo"<input type='hidden' name='$key' value=\"$val\" >";
      }
      echo"<input type='hidden' name='errorMessage' value=\"$mssg\" >";
      }
      echo"</form>";
      echo"<script>document.backfrm.submit()</script>";
	}
?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%"  align="left" class="content"><strong>UK's Top Online Car Parts Network</strong></td></tr>
                	             <tr><td colspan="2" align="left">
              	
            </td></tr>	
          </table>
          </td>
        </tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            		
            	<tr><td class="heading1">Thank You</td></tr>
            	</table>
            </td></tr>
            <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              			
              			<table cellpadding="2" cellspacing="2" width="100%" border="0" >
              		<tr><td>Thank You. We have received your contact info.</td></tr>
              		<tr><td>We will address your request as soon as possible.</td></tr>

              		  </table>
              		
              	</td></tr>
              		
              </table>
               <tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
             </td></tr> 	
             
         </table>   
        	
      </td></tr>	
</table>
<?	
}
?>
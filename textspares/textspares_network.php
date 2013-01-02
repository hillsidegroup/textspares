<?
header("location: suppliers/");
end();
session_start();
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
switch($_REQUEST[call]){
	case"":
	middle();
	break;
	
	case"save":
	saveSupplier();
	break;
}
bottom();
function middle(){?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
              <td width="99%" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong></td></tr>
               <tr><td colspan="2" align="left">
              	<table cellpadding="1" cellspacing="1" width="100%">
              		<tr><td>
              	<?
              	$sqltxt=mysql_query("select content from files where file_id='2538'")or die(mysql_error());
              	$rectxt=mysql_fetch_array($sqltxt);
              		//$content=stripslashes($rec[content]);
              		$content=str_replace("../cmtimages/","cmtimages/",(stripslashes($rectxt[content])));
              		echo $content; 
              	?>
              </td></tr>
              </table>
            </td></tr>	
          </table>
          </td>
        </tr>
        <tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%" border="0">
        		
        		<tr>
            <td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">JOIN TEXTSPARES NETWORK</td></tr>
            </table>
            	</td>
          </tr>
         
          <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<form name="netform" method="post" action="textspares_network.php" onsubmit="return chk_supplierfrm1(this)">
              		<input type="hidden" name="call" value="save">
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td colspan="2" height="1px"></td></tr>
              		<?
              		if($_REQUEST[errorMessage]<>""){
              		echo"<tr bgcolor='#AB0000'><td colspan='2' class='heading1' align='center'>$_REQUEST[errorMessage]</td></tr>";
              	 }
              		?>
              		<tr><td class="formtext">Company:</td><td><input type="text" name="sup_company" value="<?=$_POST[sup_company]?>" class="formtxtbox" id="c1" onblur="chk_supplierfrm1(this.form,'error1')">&nbsp;<span id="error1"></span></td></tr> 
              		<tr><td class="formtext">Contact Name:</td><td><input type="text" name="sup_name" value="<?=$_POST[sup_name]?>" class="formtxtbox" id="c2" onblur="chk_supplierfrm1(this.form,'error2')">&nbsp;<span id="error2"></span></td></tr>
              		<tr><td class="formtext">Position In Comp:</td><td><input type="text" name="sup_position" value="<?=$_POST[sup_position]?>" class="formtxtbox" onblur="chk_supplierfrm1(this.form,'error3')">&nbsp;<span id="error3"></span></td></tr>
              		<tr><td class="formtext">Address 1:</td><td><input type="text" name="sup_add1" value="<?=$_POST[sup_add1]?>" class="formtxtbox" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error4"></span></td></tr>
              		<tr><td class="formtext">Address 2:</td><td><input type="text" name="sup_add2"  value="<?=$_POST[sup_add2]?>" class="formtxtbox"></td></tr>
              		<tr><td class="formtext">Town / County:</td><td><input type="text" name="sup_county" class="formtxtbox" value="<?=$_POST[sup_county]?>" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error5"></span></td></tr>
              		<tr><td class="formtext">Postal Code:</td><td><input type="text" name="sup_zipcode" class="formtxtbox" value="<?=$_POST[sup_zipcode]?>" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error6"></span></td></tr>
              		<tr><td class="formtext">E-Mail:</td><td><input type="text" name="sup_email" class="formtxtbox" value="<?=$_POST[sup_email]?>" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error7"></span></td></tr>
              		<tr><td class="formtext">Telephone:</td><td><input type="text" name="sup_phone" class="formtxtbox" value="<?=$_POST[sup_phone]?>" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error8"></span></td></tr>
              		<tr><td class="formtext">Are You VAT Registered?:</td><td><select name="sup_vat" onchange="chk_supplierfrm1(this.form,'error4')"><option value=""></option><option value="y">Yes</option><option value="n">No</option></select>&nbsp;<span id="error9"></span></td></tr>
              		<tr><td class="formtext">Do You Have A Waste Management License?:</td><td><select name="sup_license" onchange="chk_supplierfrm1(this.form,'error4')"><option value=""></option><option value="y">Yes</option><option value="n">No</option></select>&nbsp;<span id="error10"></span></td></tr>
              		<tr><td class="formtext" valign="top">Additional Info:</td><td><textarea name="sup_info" rows="5" cols="20" onblur="chk_supplierfrm1(this.form,'error4')"><?=$_POST[sup_info]?></textarea>&nbsp;<span id="error11"></span></td></tr>
              		<tr><td class="formtext">Security Code:</td><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="simg/randomImage3.php" alt=""/></font></td></tr>
              		<tr><td class="formtext">Enter the security code shown:</td><td><input type="text" name="security_code" class="formtxtbox" onblur="chk_supplierfrm1(this.form,'error4')">&nbsp;<span id="error12"></span></td></tr>
              	<tr><td>&nbsp;<td><input type="submit" value="Send Info"></td></tr>
              	</table>
              </form>
              	</td>
            </tr>	
          	<tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
        </table>	
      </td></tr>	
      
</table>
<?
}
function saveSupplier(){
	$sqladmin=mysql_query("select email from admin_users where admin_user_id='1'")or die(mysql_error());
	$recadmin=mysql_fetch_array($sqladmin);
	$email_to=$recadmin[email];
	$number   = $_POST[security_code];
	if (md5($number) == $_SESSION[image_random_value]){
	 $_SESSION[image_random_value] = '';
	 $sqlins="insert into textspares_suppliers(company_name,contact_name,sup_position,sup_address1,sup_address2,sup_town,sup_zipcode,sup_email,sup_phone,sup_vat,sup_mlicense,sup_info,sup_activation)";
	 $sqlins.="values(\"$_POST[sup_company]\",\"$_POST[sup_name]\",\"$_POST[sup_position]\",\"$_POST[sup_add1]\",\"$_POST[sup_add2]\",\"$_POST[sup_county]\",\"$_POST[sup_zipcode]\",\"$_POST[sup_email]\",\"$_POST[sup_phone]\",\"$_POST[sup_vat]\",\"$_POST[sup_license]\",\"$_POST[sup_info]\",'n')";
	 mysql_query($sqlins)or die(mysql_error());
	 	$str="<table style='font-family:verdana;font-size:13;color:#000000' cellpadding=5 border=0>
              									<tr><td colspan='2'>Dear <b>Admin</b>, </td></tr>
              									<tr><td>&nbsp;</td><td colspan=6>A new supplier registration request is  sent by $_POST[sup_name]</td></tr>
              									<tr><td>Message : </td><td>&nbsp;</td><td>$_POST[sup_info]</td></tr>
              									<br>
              									<tr><td colspan='2'>From: <br><br>&nbsp;&nbsp;<a href='mailto:$_POST[sup_email]' target='_blank'>$_POST[sup_email]</a></td></tr>
              									</table>";
                 mail($email_to,"New Supplier Registration Request Form",$str,"From:".$_POST[sup_email]." \n"."Content-type:text/html");
}else{
	$mssg=" Sorry, you have provehicle_ided an invalid security code.";
	echo"<form name='backfrm' method='post' action='textspares_network.php'>";
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
                <td width="99%" height="80" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong><br />
                  We have wide range of new and usedcar parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td>
        	<table cellpadding="0" cellspacing="0" width="100%" border="0">
        		
        		<tr>
            <td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">THANKS</td></tr>
            </table>
            	</td>
          </tr>
         
          <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td height="1px"></td></tr>
              		<tr><td>Thank You. We have received your email regarding joining  TextSpares Network.
 We will address your request as soon as possible.</td></tr>
              		
              	</table>
              
              	</td>
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
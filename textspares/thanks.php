<?
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
if(!is_int($_POST['custid']) && !$_POST['custid'] > 0)
{
	echo '<script>window.location.href="index.php"</script>';
}
else
{
top();
common_middle();
middle();
bottom();
}

function middle()
{
	global $db, $session;

$thanks_content = '
	<p>
<table width="100%" cellspacing="1" cellpadding="1" border="0">
<tr><td>
<p><strong>In order to receive SMS quotations.</strong></p>
<p>Please TEXT the word <b>SPARES</b> to the number <b>60777</b><br />
(one off activation fee of &pound;5 pounds + std network charge to cover admin costs)</p>
<p>Need help with your Spares Request? Call - 01706 651321</p>
<br /><br />
<font size="5">What Happens Next?</font>
</strong></p></td></tr>
<tr><td>If the part(s) you have requested are in stock, our network of dismantlers will contact you directly either by a telephone - email or a SMS text 
message directly to your mobile within the next&nbsp;15 minutes (during normal business hours). However if you are not contacted or recieve a reponse, then 
we do not have the part(s) available at the time. Unfortunately given the enormous inquiries we recieve each day, we cannot logistically call each customer 
back unless we have the parts in stock.<br /><br /></td></tr>
<tr><td><strong><font color="#0000cc">Important</font></strong> <br /><font color="#ff0033">It is absolutely vital you make a note of the name and telephone 
number of each dismantler that calls you with quotes.</font><br /><br /></td></tr>
<tr><td><strong><font color="#0000cc">View Quotes Online<br /></font></strong></td></tr><tr><td>Please make a note of your request reference and login pin, 
as you will need them to login to view your quotes. <br /><a target="_self" href="'.BASE.'?k='.create_emailloginkey($_POST['custid']).'">You can login here to view your quotes online</a>.</td></tr>
<tr><td>Important Information</td></tr><tr><td><strong><br /><font color="#0000cc">Recieving a Quote</font></strong></td></tr>
<tr><td>If the part(s) you have requested are in stock, then you will receive a reponse either via Telephone - Email - SMS Text. You should make a note of the 
supplier\'s details e.g Name and Telephone Number, as well as the price of the part and length of guarantee, all parts supplied are fully guaranteed with minimum 
period of 30 days, however some supplier do offer guaranteed periods of up to 1 year on new and reconditoned items.<br /><br /><strong><font color="#0000cc">SMS 
Costs</font></strong><br />We now offer our customers the benefit of receiving sms car parts quotes to your mobile phone. Our unique Car Spares location service 
will enable you to find those parts you need at the right place. You will not be charged for any messages you receive from our suppliers unless you confirm your 
mobile number. There is a limite to the amount of quotes you may receive this is five. in order to acivate the SMS service on your phone please text the word 
SPARES to 60777. *There is a single one-off fee of &pound;5 to activate this service.<br /></td></tr>
<tr><td><strong><br /><font color="#0000cc">Payment</font></strong></td></tr><tr><td>The&nbsp;Text Spares Network members accept all major credit and debit cards 
as payment, we strongly suggest you use this method of payment. Under no circumstance should you send cash or deposit cash into any account, if you are asked to 
do please contact customer service support direct on <a href="mailto:support@textspares.co.uk">support@textspares.co.uk</a></td></tr>
<tr><td><strong><br /><font color="#0000cc">Delivery</font></strong></td></tr><tr><td>All items are usually delivered by overnight courier, please ensure at time 
of ordering you confirm delivery times with the supplying member, we cannot guarantee when the parts will be delivered during the day, most items are delivered 
before 17:00. Please ensure when you recieve your part(s) you carefully examine them, as no responsablity can be taken once the items are signed for and later 
found to be damaged in transit. We strongly suggest that if you are unable to examine the item(s) you sign for them as being &quot;DAMAGED&quot; signing for them 
as being &quot;UNCHECKED&quot; will not be sufficent.</td></tr><tr><td><strong><br /><font color="#0000cc">Returns</font></strong></td></tr><tr><td>If for any 
reason you need to return the part(s), please contact the supplying member to arrange for the item(s) to be returned, Remember all parts correctly supplied and no 
longer required will incure a 20% handling - admin charge. All Electrical items are non-returnable unless they are deemed to be faulty.</td></tr>
<tr><td><strong><br /><font color="#0000cc">Guarantees</font></strong></td></tr><tr><td>All parts supplied through the&nbsp;Text Spares Network are fully guaranteed, 
all items carry a minimum 30 day guarantee. All our network branches offer different guarantee periods. Please ask at time of ordering the length of guarantee that 
is being provided with your part(s).</td></tr><tr><td><strong><br /><font color="#0000cc">Got A Problem?</font></strong></td></tr>
<tr><td>If you\'re experiencing difficulties, or unhappy with the service you have recieved, please contact our customer support line. ensure you have the following 
details when call - the supplying members name - your reference number. If you are calling because you have NOT recieved your part(s) please call the supplying branch 
first, if you do not have thier contact details then please check with your debit or credit card company to ensure your payment has been authorised. 
<a href="mailto:Support@textspares.co.uk">Support@textspares.co.uk</a> - we will be as quick as possible.</td></tr></table>
</p>';

	$partname = '';
	$sqlparts = mysql_query('SELECT `part_name` FROM `customer_requests` WHERE `customer_id` = \''.$db->mysql_prep($_POST['custid']).'\' AND `vehicle_id` = \''.$db->mysql_prep($_POST['vehicle_id']).'\'')or die(mysql_error());
	if(mysql_num_rows($sqlparts) > 0)
	{
		while($record = mysql_fetch_array($sqlparts))
		{
			$partname .= '<li><b>'.$record['part_name'].'</b></li>';
		}
	}

	if($_POST['areg'] == 'true') {
		$account = '<tr><td>Thank you for using our website '.$session->userdata['name'].'<br/>There is an account already registered with this e-mail address.<br/>You quote has been submitted on the account you have associated with this e-mail.<br/><a href="http://www.textspares.co.uk/my_login.php">Click here to retrive your login details!</a></td></tr>';
	}
	
	if(is_numeric($_POST['custid']) && $_POST['custid'] > 0)
	{
		$sqlcust = mysql_query('select * from `customer_information` where `customer_id` = \''.$_POST['custid'].'\' ')or die(mysql_error());
		if(mysql_num_rows($sqlcust) > 0)
		{
			$detail = mysql_fetch_array($sqlcust);
			
			if($session->loggedin == true)
			{
					$account = '<tr><td>Thank you for using us again '.$session->userdata['name'].'<br/>Your new request was added to your existing account.</td></tr>';
			}
			else
			{
				if(!$_POST['areg'] == 'true') {
					$account = '
								<tr><td><b>Your login details:</b> </td></tr>
								<tr><td><b>Your Username:</b> '.$detail['cust_username'].'</td></tr>
								<tr><td><b>Your Password:</b> '.$_POST['_password'].'</td></tr>
								<tr><td>Please make a note of your login details, you will need these to login and retrieve any quotes from suppliers. </td></tr>
					';
				}
			}
		}
	}
	
	if(strlen($detail['cust_email']) > 5)
	{
$custemail = '
	Part Request Confirmed<br/>
	<table cellpadding="2" cellspacing="2" width="100%" border="0" >
		<tr>
			<td width="100%">
				<table cellpadding="1" cellspacing="1" width="100%" bgcolor="#FFFFFF" style="border:1px solid #000000">
				'.$account.'
				</table>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<table cellpadding="1" cellspacing="1" width="100%">
					<tr><td class="thnksmsg"><u>Your requested parts:</u><ul>'.$partname.'</ul></td></tr>
				</table>	
			</td>
		</tr>		
	</table>
'.$thanks_content;
		send_email($detail['cust_email'],'noreply@textspares.co.uk','TextSpares Part Request',$custemail,false);
	}
	
?>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="116" colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
                <td width="99%" height="80" align="left" class="content"><strong>UK"s Top Online Car Parts Network</strong><br />
                  We have wide range of new and used car parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">Your Part Request Confirmed </td></tr>
            	</table>
            </td></tr>
            <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td><table cellpadding="2" cellspacing="2" width="100%" border="0" >
              			<tr>
							<td width="100%">
								<table cellpadding="1" cellspacing="1" width="100%" bgcolor="#FFFFFF" style="border:1px solid #000000">
								<?=$account;?>
								</table>	
							</td>
						</tr>
						<tr>
							<td valign="top">
								<table cellpadding="1" cellspacing="1" width="100%">
									<tr><td class="thnksmsg"><u>Your requested parts:</u><ul><?=$partname;?></ul></td></tr>
								</table>	
							</td>
						</tr>		
					</table>
              	</td></tr>
              <tr><td>
<?=$thanks_content?>
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
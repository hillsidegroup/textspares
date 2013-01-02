<?
session_start();
define('IN_TEXTSPARES',true);
include("include/common.inc.php");
top();
common_middle();
switch($_REQUEST[call]){
	case"":
	show_details('');
	break;
}
bottom();
function show_details($id){
	$sql=mysql_query("select * from customer_information where customer_id='$_SESSION[custId]'")or die(mysql_error());
	$data=mysql_fetch_array($sql);
	$make_name=getparent($data[vehicle_make]);
	$model_name=getparent($data[vehicle_model]);
	
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="116" colspan="2" align="center">
          	<table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                <td width="1%">&nbsp;</td>
                <td width="100%" height="80" align="left" class="content"><strong>UK's Top Online Car Parts Network</strong><br />
                  We have wide range of new and usedcar parts &amp; spares. We also have   great selection of car breakers, imported Japanese car parts, van parts, recon   engines &amp; gearboxes. Find cheap car parts online by completing a part request form on the Text Spares website.</td>
              </tr>
          </table>
          </td>
        </tr>
        <tr><td >
        	 <table cellpadding="0" cellspacing="0" width="100%">
        		<tr><td>
            	<table cellpadding="0" cellspacing="0" background="images/h-blank.gif" width="217" height="35">
            	<tr><td class="heading1">Access Granted</td></tr>
            	</table>
            </td></tr>
            <tr>
              <td  style="background:url(images/grad.gif);border-left:1px solid #bcdfff; border-right:1px solid #bcdfff;border-top:1px solid #bcdfff" valign="top">
              	
              	<table width="99%" border="0" align="center" cellpadding="1" cellspacing="0">
              		<tr><td>
              			<tr><td>Welcome <?=$data[cust_name]?></td></tr>
              			<tr><td><b>Vehicle Details for registration number </b></td></tr>
              			<tr><td>
              				<table cellpadding="1" cellspacing="1" width="100%">
              					<tr><td><b>Make:</b></td><td><?=$make_name?></td><td><b>Year:</b></td><td><?=$data[registration_year]?></td></tr>
              					<tr><td><b>Model:</b></td><td><?=$model_name?></td><td><b>Body Type:</b></td><td><?=$data[body_type]?></td></tr>
              			  </table>	
              	  </td></tr> 
              	  <tr><td><img src="images/pixel-blue.gif" width="530px" height="1px"></td></tr>         		
              	  <tr><td><b>Quotes</b></td></tr>         		
              	          	  	<?
              	  	//$sql=mysql_query("select sq.*,sqd.* from supplier_quotes sq, supplier_quotes_details sqd where sq.quote_id=sqd.quote_id and sq.customer_id='$_SESSION[custId]' order by sq.quote_date desc")or die(mysql_error());
              	  	$option_page=$_REQUEST[option_page];
										    $per_page = 10;
										    $sqlsearch="select sq.*,sqd.* from supplier_quotes sq, supplier_quotes_details sqd where sq.quote_id=sqd.quote_id and sq.customer_id='$_SESSION[custId]' and sq.quote_type='' order by sq.quote_date desc";
										    //echo $sqlsearch;
										    if (!isset($option_page)) {
										      $option_page = 1;
										    }
										    $prev_option_page = $option_page - 1;
										    $next_option_page = $option_page + 1;
										    $option_query = mysql_query($sqlsearch);
										    $option_page_start = ($per_page * $option_page) - $per_page;
										    $num_rows = mysql_num_rows($option_query);
										    if ($num_rows <= $per_page) {
										      $num_pages = 1;
										    } else if (($num_rows % $per_page) == 0) {
										      $num_pages = (int)($num_rows / $per_page);
										    } else {
										      $num_pages = (int)($num_rows / $per_page) + 1;
										    }
										      $sqlsearch = $sqlsearch . " LIMIT $option_page_start, $per_page";
										      $rs=mysql_query($sqlsearch)or die(mysql_error());
										      // echo $sqlsearch;
              	  	if(mysql_num_rows($rs)>0){
              	  		echo"<tr><td>";
              	  		while($rec=mysql_fetch_array($rs)){
              	  			$dat=explode("-",$rec[quote_date]);
              	  			$qdate=$dat[2]."-".$dat[1]."-".$dat[0];
              	  			$sqlm=mysql_query("select company_name,contact_name,sup_town,sup_phone,sup_email from textspares_suppliers where company_id='$rec[company_id]'")or die(mysql_error());
              	  			$recm=mysql_fetch_array($sqlm);
				              	 echo"<table cellpadding='1' cellspacing='1' width='100%' id='boxt'>";
				              	 echo"<tr><td class='box'>Date</td><td class='box'>Company Name</td><td class='box'>Contact Name</td><td class='box'>Telephone</td><td class='box'>E-mail</td><td class='box'>Town</td></tr>";
				              	 
				              	 echo"<tr><td>".$qdate."</td><td>".$recm[company_name]."</td><td>".$recm[contact_name]."</td><td>".$recm[sup_phone]."</td><td>".$recm[sup_email]."</td><td>".$recm[sup_town]."</td></tr>";
				              	 echo"<tr><td colspan='6'>";
				              	 echo"<table cellpadding='1' cellspacing='1' width='100%'>";
				              	 echo"<tr><td><b>Part Name</b></td><td>".get_part($rec[request_id])."</td></tr>";
				              	 echo"<tr><td><b>Quote Price</b></td><td>&pound;".sprintf('%.2f',$rec[quote_price])."</td></tr>";
				              	 echo"<tr><td><b>Guarantee</b></td><td>".$rec[quote_guarantee]."</td></tr>";
				              	 echo"<tr><td><b>Addtional Info</b></td><td>".$rec[additional_info]."</td></tr>";			              	 
				              	 echo"</table>";
				              	 echo"</td>";
				              	 echo"</td></tr>";
				              	 
                        echo"</table>";
				              	}
				              	echo"</td></tr>";
				              	echo"<tr><td align='right'>";
				              	if ($prev_option_page)  {
                       echo "<a href='show_customer_quotes.php?option_page=$prev_option_page' class='midlinks'> &lt;&lt; </a> | ";
                        }
                        for ($i = 1; $i <= $num_pages; $i++) {
                        if ($i != $option_page) {
                        echo "<a href='show_customer_quotes.php?option_page=$i'  class='midlinks'> $i</a> | ";
                        } else {
                        echo '<b><font color=#E68321>' . $i . '</font></b> | ';
                        }
                        }
                        // Next
                        if ($option_page < $num_pages) {
                         echo"<a href='show_customer_quotes.php?option_page=$next_option_page' class='midlinks'> &gt;&gt; </a>";
                         }
                      echo"</td></tr>";
				              }
				             ?>    
            </td></tr>
              </table>
               <tr>
              <td><img src="images/footerpart.gif" width="551" height="12" /></td>
            </tr>
             </td></tr> 	
             
         </table>   
        	
      </td></tr>	
</table> 
<?}?>
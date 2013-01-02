<?php

if(!defined('IN_TEXTSPARES')) { exit; }

function display_stats($db)
{
	
	global $session,$settings;
		
	?><h1><img style="vertical-align: middle;" alt="Statistics" src="<?php echo ROOT; ?>Images/settings.png">Statistics</h1>
	
    <div class="reg_grp ui-corner-all left w30 m10">
        <h2>Past 30 Days</h2>
        
        <table class="m10">
        	<tr>
            	<th style="text-align:right;">No of Quotes:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM supplier_quotes WHERE company_id = ".$session->userdata['id']." AND DATE_SUB(NOW(), INTERVAL 30 DAY) < quote_date"); echo $cnt == "" ? 0 : $cnt; ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Average Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT AVG(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 30 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Largest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT MAX(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 30 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
             <tr>
            	<th style="text-align:right;">Lowest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT MIN(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 30 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>        
        </table>
    </div>
     <div class="reg_grp ui-corner-all left w33 m10">
        <h2>Past Year</h2>
        
        <table class="m10">
        	<tr>
            	<th style="text-align:right;">No of Quotes:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM supplier_quotes WHERE company_id = ".$session->userdata['id']." AND DATE_SUB(NOW(), INTERVAL 365 DAY) < quote_date"); echo $cnt == "" ? 0 : $cnt; ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Average Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT AVG(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 365 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Largest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT MAX(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 365 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
             <tr>
            	<th style="text-align:right;">Lowest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT MIN(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 365 DAY) < quote_date"); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>        
        </table>
    </div>
     <div class="reg_grp ui-corner-all left w30 m10">
        <h2>Totals</h2>
        
        <table class="m10">
        	<tr>
            	<th style="text-align:right;">No of Quotes:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT COUNT(*) AS Total FROM supplier_quotes WHERE company_id = ".$session->userdata['companyid']." AND DATE_SUB(NOW(), INTERVAL 365 DAY) < quote_date"); echo $cnt == "" ? 0 : $cnt; ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Average Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT AVG(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
            <tr>
            	<th style="text-align:right;">Largest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var("SELECT MAX(quote_price) AS Total FROM supplier_quotes_details WHERE company_id = ".$session->userdata['companyid']); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>
             <tr>
            	<th style="text-align:right;">Lowest Quote:</th>
                <td style="font-size:20px; color:#6C0;"><p><?php $cnt = $db->get_var('SELECT MIN(`quote_price`) AS `Total` FROM `supplier_quotes_details` WHERE `company_id` = '.$session->userdata['companyid']); echo $cnt == "" ? "&pound;0.00" : "&pound;".number_format($cnt,2); ?></p></td>
            </tr>        
        </table>
    </div>    
	<?php	
	}



///////////////////////////////////////////////////////////////////////////////////////////

?>
<script type="text/javascript">
<!--

$(function(){
	

});


-->
</script>
<?php

display_stats($db);
?>
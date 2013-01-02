<?php

if(!defined('IN_TEXTSPARES') || $session->access(2) != true) { exit; }

$error = array();

switch ($subaction) {

//---------------
//DEFAULT DISPLAY
//---------------

	default:
	
		include('Includes/pager.inc.php');

		$uitest = false;
		$errors = array();

		//-----------------------
		// GET  LIST OF COMPANIES
		
		$sql = 'SELECT 
				(SELECT SUM(`t`.`amount`) FROM `customer_transactions` AS `t` WHERE `t`.`company_id` = `c`.`company_id`) AS `total`,
				(SELECT COUNT(*) FROM `supplier_quotes_details` AS `d` JOIN `customer_transactions` AS `dc` ON `dc`.`quotation_id` = `d`.`quote_id` WHERE `d`.`company_id` = `c`.`company_id` AND `dc`.`record_time` > `c`.`last_payment`) AS `unpaid`,
				`c`.`company_id`,`c`.`last_payment`,`c`.`c_name`,`c`.`c_phone`,`c`.`c_sales`,`c`.`c_mobile`,`c`.`c_country` 
				FROM `supplier_company` AS `c` WHERE 1 ORDER BY `c`.`c_name` ASC';
		$company = mysql_query($sql);
		if(!$company) 
		{
			$error[] = '<b>MYSQL ERROR</b><br/>'.mysql_error().'<br/><br/><i>'.$sql.'</i><br/><br/>Line: '.__LINE__.' in ' . $_SERVER['SCRIPT_NAME'];
		} else {
			if(mysql_num_rows($company) == 0) {
				$company = false;
			}
		}
		// ^ GET  LIST OF COMPANIES
		//-------------------------


		//-----------------------------
		// GET DETAILS FOR EACH COMPANY
		if($company) {

		}
		// ^ GET DETAILS FOR EACH COMPANY
		//-------------------------------

		// PRINT ANY ERRORS
		if(count($error) > 0) {
			foreach($error AS $report) {
				echo '<p class="error">'.$report.'</p>';
			}
		} else {
		// OUTPUT CONTENT
		?>
		<div id="latest_requests" style="border:none;">
			<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?php echo ROOT; ?>Images/quotes.png">Management &gt; Billing</h1>

			<script type="text/javascript">
			$(document).ready(function()
			{
			});
			</script>
			
			<div class="request_tbl ui-corner-all">
				<table>
					<tbody>
						<tr>
							<th>Company ID</th>
							<th>Company Name</th>
							<th>Income</th>
							<th>Last Payment Recieved</th>
							<th>Total Due.</th>
						</tr>
			<?php
				if($company) {
					
					while($print = mysql_fetch_object($company)) {
						echo '<tr><td>'.$print->company_id.'</td><td>'.$print->c_name.'</td><td>&pound;'.$print->total.'</td><td>'.(($print->last_payment > 0) ? date('l jS M, Y g:iA',$print->last_payment) : 'Never Paid').'</td><td><b>&pound;'.number_format(($print->unpaid * $settings['service_fee']),2).'</b></td></tr>';
					}
					mysql_free_result($company);
				}
			?>
					</tbody>
				</table>
			</div>
		</div>

		<?php
			$pager = new Pager();
			//$pager->page_size = 25;
			$pager->display_rpp = true;
		}
		
	break;
}
?>
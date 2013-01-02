Hello
<?php

if(!defined('IN_TEXTSPARES')) { exit; }

if(!$session->access(6))
{
	if(!empty($session->messages))
	{
		echo '
		<div class="ui-state-error ui-corner-all m5">
			<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>'.$session->messages.'</p>
		</div>
		';
	}
}
else
{
?>
	<script type="text/javascript">
	</script>
	<h1><img src="<?php echo ROOT; ?>Images/requests.png" alt="Latest Requests" style="vertical-align:middle;" />Latest Requests</h1>
<?php

	$sql = 'SELECT `q`.`quote_id`,`s`.`company_user_id` FROM `supplier_quotes_details` AS `q` JOIN `supplier_quotes` AS `s` ON `s`.`quote_id` = `q`.`quote_id` WHERE 1';

	$res = $db->get_results($sql);
	
	foreach($res AS $r)
	{
		$db->query('UPDATE `supplier_quotes_details` SET `company_user_id` = \''.$r->company_user_id.'\' WHERE `quote_id` = \''.$r->quote_id.'\' ');
	}
}
?>
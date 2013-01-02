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
		$updates = array();

		//-----------------------
		// GET LIST OF SETTINGS
		
		$sql = 'SELECT * FROM `manager_settings` WHERE 1 ORDER BY `varname` ASC';
		$results = mysql_query($sql);
		if(!$results) 
		{
			$error[] = '<b>MYSQL ERROR</b><br/>'.mysql_error().'<br/><br/><i>'.$sql.'</i><br/><br/>Line: '.__LINE__.' in ' . $_SERVER['SCRIPT_NAME'];
		} else {
			if(mysql_num_rows($results) == 0) {
				$results = false;
			}
		}
		$settings_table = '<tr><td><b>PHP Version</b></td><td>'.phpversion().'</td><td>Current version of PHP running on this Web Server.</td></tr>';
		if($results)
		{
			while($print = mysql_fetch_object($results))
			{
				$posted = false;
				$badvar = '';
				//Check for valid post field on update
				if(!empty($_POST['save_settings']))
				{
					$posted = (isset($_POST[$print->varname])) ? $_POST[$print->varname] : false;
				}
			
				//Process Input Boxes & Default Values
				//IF BOOLEAN
				if($print->type == 'bool')
				{
					$options = array(1 => 'True',0 => 'False');

					//If value differs from post then update databse
					if($posted !== false && $posted !== $print->value)
					{
						if($posted == 0 || $posted == 1)
						{
							$db->query('UPDATE `manager_settings` SET `value` = \''.$posted.'\' WHERE `varname` = \''.$print->varname.'\' LIMIT 1');
							$updates[] = '<li><b>'.$print->title.'</b> was changed from <b>'.$options[$print->value].'</b> to <b>'.$options[$posted].'</b>';
							$print->value = $posted;
						}
						else
						{
							$badvar = ' row_error';
						}
					}
					
					$input = '<select name="'.$print->varname.'">';
					foreach($options AS $value => $key)
					{
						$selected = ($print->value == $value) ? ' selected="selected"' : '';
						$input .= '<option value="'.$value.'"'.$selected.'>'.$key.'</option>';
					}
					$input .= '</select>';
				}
				else
				{
				//IF STRING / TEXT / INTEGER / FLOAT
					$size = 6;
					switch($print->type)
					{
						case "int":
							if($print->options != NULL)
							{
								//TODO: Process custom options
							}
							if($posted !== false && !is_int($posted))
							{
								$badvar = ' row_error';
								$posted = false;
							}
							break;
							
						case "float":
							if($print->options != NULL)
							{
								//TODO: Process custom options
							}
							if($posted !== false)
							{
								if(!is_numeric($posted))
								{
									$badvar = ' row_error';
									$posted = false;
								} else {
									$posted = number_format($posted,2);
								}
							}
							break;
							
						case "string":
							if($print->options != NULL)
							{
								//TODO: Process custom options
							}
							$size = 32;
							if($posted !== false)
							{
								$posted = $db->mysql_prep($posted);
							}
							break;
							
						case "text":
							if($print->options != NULL)
							{
								//TODO: Process custom options
							}
							if($posted !== false)
							{
								$posted = $db->mysql_prep($posted);
							}
							break;
					}
					if($posted !== false && $posted !== $print->value)
					{
						$db->query('UPDATE `manager_settings` SET `value` = \''.$posted.'\' WHERE `varname` = \''.$print->varname.'\' LIMIT 1');
						$updates[] = '<li><b>'.$print->title.'</b> was changed from <b>'.$print->value.'</b> to <b>'.$posted.'</b>';
						$print->value = $posted;
					}
					if($print->type == 'text')
					{
						$input = '<textarea name="'.$print->varname.'" rows="4" cols="20">'.$print->value.'</textarea>';
					} else {
						$input = '<input type="text" name="'.$print->varname.'" value="'.$print->value.'" size="'.$size.'" />';
					}
				}
				//Output table rows.
				$settings_table .= '<tr class="rowover'.$badvar.'"><td><b>'.$print->title.'</b></td><td>'.$input.'</td><td>'.$print->info.'</td></tr>';
			}
			$settings_table .= '<tr><td colspan="3" align="right" valign="middle"><input type="submit" name="save_settings" value="Save Settings" /></td></tr>';
			mysql_free_result($results);
		} else {
			$settings_table = '<tr><td colspan="3">There where no settings to be displayed.</td></tr>';
		}
		// ^ GET LIST OF SETTINGS
		//-------------------------

		// PRINT ANY ERRORS
		if(count($error) > 0) {
			foreach($error AS $report) {
				echo '<p class="error">'.$report.'</p>';
			}
		} else {
		// OUTPUT CONTENT
		?>
		<script type="text/javascript">
		$(document).ready(function()
		{
		});
		</script>
		<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?php echo ROOT; ?>Images/quotes.png">Management &gt; Website Settings</h1>
		<div id="latest_requests" style="border:none;">
			<?php
			if(count($updates) > 0)
			{ 	  
			?>
			<div id="alerts" class="ui-widget m10">
				<div class="ui-state-highlight ui-corner-all" style="padding: 0.5em 0.7em;"> 
					<?php
					if(is_array($updates))
					{
						echo '<p><b>Settings Saved</b></p><ul>';
						foreach($updates AS $msg)
						{
							echo $msg;
						}
						echo '</ul>';
					}
					else
					{
						echo $updates;
					}
					?>
				</div>
			</div>
			<?php } ?>
			
			<form name="edit_settings" action="<?=ROOT.SCRIPT;?>?_action=settings" method="POST">
			<div class="request_tbl ui-corner-all">
				<table>
					<tbody>
						<tr>
							<th>Setting Name</th>
							<th>Setting Value</th>
							<th>Description</th>
						</tr>
						<?=$settings_table;?>
					</tbody>
				</table>
			</div>
			</form>
		</div>

<?php
		}
		
	break;
}
?>
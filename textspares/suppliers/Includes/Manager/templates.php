<?php

if(!defined('IN_TEXTSPARES') || $session->access(2) != true) { exit; }

include('tools/filemanager.php');
include('tools/formelements.php');
$fm = new fileManager();

$error = array();

?>
<script type="text/javascript">
$(document).ready(function()
{
	$('[name=templates]').change(function(){
		alret('TEST');
	});
});
</script>
<h1><img style="vertical-align: middle;" alt="" width="35" height="35" src="<?=ROOT;?>Images/quotes.png">Management &gt; Templates</h1>
<div id="latest_requests" style="border:none;">
<?php

switch ($subaction) {

//---------------
//DEFAULT DISPLAY
//---------------

	default:
		$files = $fm->getFiles('../templates');
		echo form_dropmenu('templates', $files['folders'], true);
		
		?>
		<div class="request_tbl ui-corner-all">
			<table>
				<tbody>
					<tr>
						<th>Select Skin to Edit</th>
						<th>Create New Skin</th>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		
	break;
}
?>
</div>
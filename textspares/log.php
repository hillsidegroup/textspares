<?php echo 'Sorry! INVALID POST VARIABLE. Custom Error have Occured. Your IP address has been logged!'?><h4><?php $ip=$_SERVER['REMOTE_ADDR']."--".$_SERVER['REQUEST_URI']; $log=("iplog.txt"); $logip=fopen($log,"a"); fputs($logip,gmdate('m-d-y@H:i:sT')." - ".$ip."\n"); fclose($logip); ?></h4>

<?php echo '<br>System Administrator have been Notified.!' ?>

<?php
	mail("support@textspares.co.uk","Suspect Hack","Please check log file! <br> $ip ","From:universal_developer@rediffmail.com");
?>
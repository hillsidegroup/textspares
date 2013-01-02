<?php

if(!defined('IN_TEXTSPARES')) exit;

error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('UTC');

define('BASE','http://'.$_SERVER['HTTP_HOST'].'/');
define('ROOT','http://'.$_SERVER['HTTP_HOST'].'/suppliers/');
define('AJAX_JSON',ROOT.'Includes/Manager/tools/_ajax_request.php?r=');

define('EZSQL_DB_USER',		'dbo392727837');				// <-- mysql db user
define('EZSQL_DB_PASSWORD',	's7HjeHjd385Jh47KjeR31K0dU94H4Gys');					// <-- mysql db password
define('EZSQL_DB_NAME',		'db392727837');					// <-- mysql db pname
define('EZSQL_DB_HOST',		'localhost:/tmp/mysql5.sock');	// <-- mysql server host

?>
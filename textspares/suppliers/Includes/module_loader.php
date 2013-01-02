<?php


class Module_Loader extends db {
	
	
	function Module_Loader()
		{
		$this->db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);
		$row_count = $this->get_var("SELECT COUNT(*) FROM tblAeonModules WHERE strModule = '".$_GET["_load"]."'");
		//$this->debug();
		//print($row_count);
		if($row_count==0){
			include "Includes/Modules/load_error.php";
			} else {
			$row = $this->get_row("SELECT strPath,intAccess FROM tblAeonModules WHERE strModule = '".$_GET["_load"]."'");
			if( ($_GET['_load']!="index"&&$_SESSION['_AccessLvl']<=$row->intAccess) || $_GET['_load']=="index" )
				{
				//print($row->intAccess."<br>");
				//print($_SESSION['_AccessLvl']."<br>");
				$path = $row->strPath;
				include "Includes/Modules/$path";
				} else {
				//print($row->intAccess."<br>");
				//print($_SESSION['_AccessLvl']."<br>");
				include "Includes/Modules/access_error.php";
				}
			}	
		}
		
	}
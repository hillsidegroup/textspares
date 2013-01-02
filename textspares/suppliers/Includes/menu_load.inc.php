<?php

class Menu_Loader
{
	var $access_lvl;
	
	function LoadMenu($menu_item,$access,$method = NULL)
	{
		if(isset($_SESSION["_AccessLvl"])){
			$this->access_lvl = $_SESSION["_AccessLvl"];
			} else {
			$this->access_lvl = 6;
			}
		if($this->access_lvl<=$access)
		{
			if($method==1){
			return $menu_item;
			} else {
			print($menu_item);
			}
		}
	}

	function CheckIndex($link)
	{
		if($link=="index.php"||$link=="index.php?_load=page&amp;_pageid=1")
		{
		return ROOT;
		} else {
		return $link;		
		}
	}
}
	
	
class User_Menu_Loader
{
	var $access_lvl;
	
	function LoadMenu($menu_item,$access,$method = NULL)
		{
		if($access==2){ $access = 7; } elseif($access==1) {  $access = 8; }
		
		if(isset($_SESSION["_AccessLvl"])){
			$this->access_lvl = $_SESSION["_AccessLvl"];
			} else {
			$this->access_lvl = 8;
			}
			
		if($this->access_lvl<=$access)
			{
			if($method==1){
				return $menu_item;
				} else {
				print($menu_item);
				}
			}
		}
		
	function CheckIndex($link)
		{
		if($link=="index.php"||$link=="index.php?_load=page&amp;_pageid=1")
			{
			return ROOT;
			} else {
			return $link;		
			}
		}
		
	function LoadContent($access)
		{
		if($access==2){ $access = 7; } elseif($access==1) {  $access = 8; }
		
		if(isset($_SESSION["_AccessLvl"])){
			$this->access_lvl = $_SESSION["_AccessLvl"];
			} else {
			$this->access_lvl = 8;
			}
			
		if($this->access_lvl<=$access)
			{
			return true;
			}
		else { return false; }
		}	
		
	}

?>
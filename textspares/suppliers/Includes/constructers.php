<?php
define("LOAD_CHECK", true);

function insert_menu($db,$menu_id,$list_open_type=FALSE,$list_close_type=FALSE,$sublist_open_type=FALSE,$sublist_close_type=FALSE)
	{	
	//Default List types	
	if($list_open_type==FALSE){ $list_open_type="<dt>"; }
	if($list_close_type==FALSE){ $list_close_type="</dt>"; }
	if($sublist_open_type==FALSE){ $sublist_open_type="<li>"; }
	if($sublist_close_type==FALSE){ $sublist_close_type="</li>"; }
		
	$submenudisplay = $db->get_var("SELECT intSubMenu FROM tblMenuList WHERE intMencompany_user_id = $menu_id");
	$pagemenu = new User_Menu_Loader();
	if($menu = $db->get_results("SELECT * FROM tblMenuItems WHERE intMencompany_user_id = $menu_id ORDER BY intOrder ASC")){
		foreach($menu as $menu)
			{
			if($menu->intOpenIn==2){ $openin = " target='_blank' "; } else { $openin = ""; }
			$menu_link = "$list_open_type<a class=\"main_menu\" href=\"".$pagemenu->CheckIndex($menu->strDestination)."\" $openin title=\"".$menu->strHoverText."\"><span>".$menu->strItemName."</span></a>";
			$pagemenu->LoadMenu($menu_link,$menu->intUserAccess);
			if($submenudisplay==1)
				{
				$msql = "SELECT * FROM tblMenuList INNER JOIN tblMenuItems ON tblMenuList.intMencompany_user_id = tblMenuItems.intMencompany_user_id WHERE tblMenuList.intSubMenuLink = ".$menu->intItemID." ORDER BY intOrder ASC";
				//print($msql);
				if($submenu = $db->get_results($msql))
					{
					print("<ul class=\"submenu\">");
					foreach($submenu as $submenu)
						{
						if($submenu->intOpenIn==2){ $openin = " target='_blank' "; } else { $openin = ""; }
						$menu_link = "$sublist_open_type<a href=\"".$pagemenu->CheckIndex($submenu->strDestination)."\" $openin title=\"".$submenu->strHoverText."\"><span>".$submenu->strItemName."</span></a>$sublist_close_type";
						$pagemenu->LoadMenu($menu_link,$submenu->intUserAccess);
						}
					print("</ul>");	
					}
				}
			$menu_link = "$list_close_type";
			$pagemenu->LoadMenu($menu_link,$menu->intUserAccess);	
		  	}	
		}	
	}

function page_url_detect ($db)
{
	global $cid, $type;
	//$uri = substr($_SERVER['REQUEST_URI'],10);
	$uri = substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'],"/")+1);
	$uri = substr($uri,10);
	//echo $uri;
	if(substr($uri,1,1)=="_"||$uri=="/"){ $type = "module"; } else { $type = "page"; }
	if($type=="page")
	{ 
		$id = $db->get_var("SELECT intPageID FROM tblPages WHERE strUrl = '".$db->mysql_prep($uri)."'");
		//print("<br>"."SELECT intPageID FROM tblPages WHERE strUrl = '".$db->mysql_prep($uri)."'"."<br>");
		if(!is_numeric($id)){ $id = 0; }
		$cid = $id;
		//print("<br> CID: ".$cid);
	} 
	elseif($type=="module")
	{
		$id = $db->get_var("SELECT intMID FROM tblModules WHERE strUrl = '".$db->mysql_prep($uri)."'");
		//print("<br>"."SELECT intMID FROM tblModules WHERE strUrl = '".$db->mysql_prep($uri)."'"."<br>");
		if(!is_numeric($id)){ $id = 0; }
		$cid = $id;
	}
}

?>
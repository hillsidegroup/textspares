<?php

if(!defined('IN_TEXTSPARES')) { exit; }

/**
* Module:		News Index
* Version:		v0.98.1
* File:			news.php
* Copyright:	Copyright 2006 - 2009 e-Orchards Ltd.
* License:		see "Application License" details.

Module Info
--------------------------------------------------------------|
Main News page. Pulls news content items from the Aeon Database


Funtion List
--------------------------------------------------------------|

Module Specific
***************
rtn_news()					- Returns the news content based on the news config settings.
showNewsTicker()			- Lists the news items dependat on news config settings
displayNewsItem()			- Displays an indivehicle_idual news item.


Generic
**************
get_user()					- Takes an input variable as a field within the user table to be returned relating to the input user id.


Revision Info
--------------------------------------------------------------|
v0.5-v0.98 		| General beta development/improvements
v0.98-v0.98.1	| Module Info boxes implemented
v0.98.1-v0.99	| URL Rewrite support


**********************************************************************************/
/****** Module Code *******/


include "Includes/pager.inc.php";
$pager = new Pager();			

$pager->list_options["Date"]="date";
$pager->list_options["Title"]="title";
if(!isset($_GET['_pagesize'])){ $pager->page_size = 5; }



if(!function_exists(showNewsTicker)){
function showNewsTicker($db,$pager)
	{	
?>
<h1 style="margin-bottom:-10px;"><img src="<?php echo ROOT; ?>Images/news.png" alt="News" style="vertical-align:middle;" />TextSpare News</h1>
<br />
<div id="list_options">
	<p><?php echo $pager->OrderOptions(); ?></p>
</div>
<div id="news" style="width:100%; padding:0; margin:0; float:none;">
<?php
	$news_count = $db->get_var("SELECT COUNT(*) FROM spare_news ORDER BY ".$pager->getOrder("date DESC"));
	$news_sql = "SELECT * FROM spare_news ORDER BY ".$pager->getOrder("date DESC")." LIMIT ".$pager->PageLimits();
	
	//print($news_sql);
	
	if( $news_content = $db->get_results($news_sql) )
		{
		
		foreach ( $news_content as $news_content )
			{ ?>
			
				<div class="news_item">
				<h2><a href="<?php echo ROOT; ?>_news/?action=view&id=<?php echo $news_content->news_id; ?>"><?php echo $news_content->title; ?></a></h2>
				<?php
				print("<p class='news_author'>Created on ".date("d/m/y",strtotime($news_content->date))."</p>");
				print("<p>".$news_content->small_description."</p>");
							
				print("<p><a href=\"".ROOT."_news/?action=view&id=".$news_content->news_id."\">Read full article...</a></p>");
				?>	
				</div>
		
			
			<?php
			}
			?>
			<div id="pager_holder">
			<?php
			echo $pager->ShowPages($news_count);
			?>
			</div>
			<br />
			<?php
		} else {
		print("<p>No Current News.</p>");
		}

?>
</div>
<?php		
	}
}


if(!function_exists(displayNewsItem)){
function displayNewsItem($db)
	{
	?>
	   
    
	<?php
	
	$sql = "SELECT * FROM spare_news WHERE news_id = ".$db->mysql_prep($_GET['id']);
	
	if($news = $db->get_row($sql))
		{
		
		$title = $news->title;
		$content = $news->description;
		$created = $news->date;
		
		print("<h1>$title</h1><p class='news_author'>Created on ".date("d/m/y",strtotime($created))." </p>");
		print($content);
		print("<br /><p><a href='".ROOT."_news/'>&laquo; Back to News Index</a></p><br />");
		
		} else {
		
		print("<h1>Sorry!!</h1><p>This news item has been taken offline.</p>");
		
		}
	}
}
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
if(($_GET['action']=="view") )
	{
	displayNewsItem($db);
	}
else {
	showNewsTicker($db,$pager);	
	}
?>
<?php

class Pager {
	
	var $page = 1;
	var $page_size = 25;
	var $list_options = array();
	var $display_rpp = true;
	
	function Pager()
	{
		if(!empty($_GET['_page'])) { $this->page = $_GET['_page']; }
		if(!empty($_GET['_pagesize'])) { $this->page_size = $_GET['_pagesize']; }
	}

	function change_query($var,$val, $url=NULL )
	{
		// ?_load=page&_test=sumthn&_anothertest=sumthn
		
		if($url==NULL){ $url="?".$_SERVER['QUERY_STRING']; /*print("Url is set to NULL<br>");*/ } else {$url=$url; /*print("Url is set to argument<br>");*/ }
		//print($url."<br />");
		$new_q = $var.$val;
		//print($new_q."<br>");
		$start_pos = strpos($url,$var); // Where the variable occurs in the querystring
		$end_pos = strpos($url,"&_",$start_pos); // Where the querystring variable ends
		$q_len = $end_pos - $start_pos; // Querystring variable length
		
		if(!$start_pos){
			//print("Start pos returned false<br />");
			if(substr_count($url,"&_")>0){
				//print("&_ count is greater than zero<br>");
				$url .= "&".$new_q;
				//print($url."<br>");
				} else {
				//print("&_ count = zero<br>");
				$url .= "&".$new_q;
				}
			} else{
			if(!$end_pos){
				//print("end pos returned false<br>");
				$url = substr_replace($url,$new_q,$start_pos);
				} else {
				//print("end pos returned true<br>");
				$url = substr_replace($url,$new_q,$start_pos,$q_len);
				}
			}		
		//print($url."<br>");
		return $url;
		
		}

	function pagecrawl($t,$url = NULL)
	{
		$new_url = $this->change_query("_page=",$t);
		if (!empty($url) && substr_count($url,"_page=") > 0)
			{
			if ($this->page == $t)
				{
				return "<dt class=\"ui-corner-all ui-state-active\" id='curpage'><a href='".$new_url."'>".$t."</a></dt>";
				}
			else
				{
				return "<dt class=\"ui-corner-all\"><a href='".$new_url."'>".$t."</a></dt>";
				}
			}
		else
			{
			if ($this->page == $t)
				{
				return "<dt class=\"ui-corner-all ui-state-active\" id='curpage'><a href='".$new_url."'>".$t."</a></dt>";
				}
			else
				{
				return "<dt class=\"ui-corner-all\"><a href='".$new_url."'>".$t."</a></dt>";
				}
			}	
		// End of Function
	}	
	
	function PageLimits ()
		{	
		if ( $this->page > 1 )
			{
			$start = ($this->page-1) * $this->page_size;
			$limit = $this->page_size;
			}
		else
			{
			$start = 0;
			$limit = $this->page_size;
			}
		return $start.", ".$limit;
		}	
		
	function getOrder($default)
		{
		if(!isset($_GET['_orderby']))
			{
			$order = $default;
			} else {
			$order = $this->list_options[$_GET['_orderby']];
			}	
		return $order;
		}
	
	function getGroup($col)
		{
		if(!isset($_GET['_groupby']))
			{
			$group="";
			} else {
			$group = "WHERE $col = ".$_GET['_groupby'];
			}
		return $group;
		}
	
	function rtn_pagecount($page_no,$test)
		{
		if($page_no==$test){ return " selected='selected'"; }
		}
	
	function ShowPages($count)
		{
		$pages = ceil($count / $this->page_size);
		
		$page_size_url = $this->change_query("_page=",1);
		//print($page_size_url);
		if($this->display_rpp==true){
			$output = "<div id=\"_rpp\">
					Results Per Page <select onchange=\"window.open('". $this->change_query("_pagesize=","'+this.value+'",$page_size_url)."','_self')\" />
					<option". $this->rtn_pagecount($this->page_size,5). " value='5'>5</option>
					<option". $this->rtn_pagecount($this->page_size,10). " value='10'>10</option>
					<option". $this->rtn_pagecount($this->page_size,15). " value='15'>15</option>
					<option". $this->rtn_pagecount($this->page_size,25). " value='25'>25</option>
					<option". $this->rtn_pagecount($this->page_size,50). " value='50'>50</option>
					</select></div>";
			} else {
			$ouput = "";
			}
			
			
		if ( $pages > 1 )
			{

			$output .= '<dl class="paging"><dt id="page_st">Page:</dt>';
			for ($i=1; $i <= $pages; $i++)
				{ 
				switch($i)
					{
					case 1:
						if($this->page!=1)
						{ $output .= "<dt class=\"ui-corner-all\" style=\"width:50px;\"><a style=\"width:50px;\" href=\"".$this->change_query("_page=",$this->page-1)."\">&laquo; Prev</a></dt>"; }
						$output .= $this->pagecrawl($i);
						break;
					case $pages:
						$output .= $this->pagecrawl($i);
						if($this->page!=$pages){ $output .= "<dt class=\"ui-corner-all\" style=\"width:50px;\"><a style=\"width:50px;\" href=\"".$this->change_query("_page=",$this->page+1)."\">Next &raquo;</a></dt>"; }
						break;
					default:
						if($pages>6){
							if($i==2&&$this->minpage()>2)
								{ $output .= "<dt style=\"border:none; background:none;\">...</dt>"; } 
							elseif($i==($pages-1)&&$this->maxpage()<($pages-1))	
								{ $output .= "<dt style=\"border:none; background:none;\">...</dt>"; } 
							else 
								{
								if($i>=$this->minpage()&&$i<=$this->maxpage()){
									$output .= $this->pagecrawl($i);
									}
								}
						} else {
							$output .= $this->pagecrawl($i);
						}
						break;
					}
				}
			
			$output .= "</dl>";
			}
		return $output;	
		}
		
	
	function minpage()
		{
		return $this->page - 2;
		}
		
	function maxpage()
		{
		return $this->page + 2;
		}
	
	function rtn_order_selected($opt)
	{
		if(!empty($_GET['_orderby']) && $_GET['_orderby'] == $opt) { return ' selected="selected"'; }
	}
	
	function OrderOptions()
		{	
		$output = "Order By: <select onchange=\"window.open('". $this->change_query("_orderby=","'+this.value+'")."' , '_self')\" />";
		foreach($this->list_options as $option => $value)
			{
			$output .= "<option".$this->rtn_order_selected($option)." value='$option'>$option</option>";
			}		
		$output .= "</select>";
		return $output;
		}
			
		
	}	
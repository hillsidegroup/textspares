<?php

if(!defined('IN_TEXTSPARES')) { exit; }

?>
			<script type="text/javascript">

			function loop()
			{
				$active = $('#_request_pager a.ui-state-active').html();	
				switch($active)
				{ 
					case "1": $('#_slider2').click(); break; 
					case "2": $('#_slider3').click(); break; 
					case "3": $('#_slider1').click(); break; 
				}
			}

			$(function()
			{
				//hover states on the static widgets
				$('#account_summary a, #latest_requests a').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);

				// Request Slider
				$('#_request_pager a').click(function(e){
					//e.preventDefault();
					$('#_request_pager a.ui-state-active').removeClass('ui-state-active');
					$(this).addClass('ui-state-active');
					$class = '._requests_'+$(this).html();
					$("tr.slider_row").css("display","none");						
					$($class).fadeIn(1500);
				});

				$("a.save_request").click(function(e){
					e.preventDefault();
					$save_data = $(this).attr("id").split("-");
					$link_id = "#"+$(this).attr("id");
					$cid = $save_data[1];
					$rid = $save_data[2];
					$url = "<?php echo ROOT; ?>_requests/";/*?_action=save&_cid="+$cid+"&_rid="+$rid;*/
					$.ajaxSetup({cache: false});
					$.get(
						$url,
						{ _action: "save", _cid: $cid, _rid: $rid },
						function(data){
							$ele = '<div id="'+'result">';
							$start_ele = data.indexOf($ele);
							$start_pos = $start_ele+17;
							$end_pos = data.indexOf("</div>",$start_pos);
							$length = $end_pos - $start_pos;
							$result = data.substr($start_pos,$length);
							if($result=="Success"){
								$($link_id).css("display","none");
								$($link_id).parent().append("<span class=\"_saved\"><span class=\"ui-icon ui-icon-check\"></span>Saved</span>");
								} else {
								return alert($result);
								}
							},
						'html'
						);
				});
					
			});

			$(document).ready(function(){});
			</script>

            <div id="account_summary">
    			<?php
					include('Includes/Modules/userprofile.php');

					$sql = 'SELECT SUM( (SELECT COUNT(*) FROM `customer_requests` WHERE `order_group` = 0) - (SELECT COUNT(*) FROM `tblRequestsRemoved` WHERE `company_user_id` = \''.$session->userdata['id'].'\') ) AS `Total`';
					$total_pr = $db->get_var($sql);
					
					$sql = 'SELECT COUNT(*) AS `Total` FROM `customer_requests` WHERE `order_group` = 0 AND `request_stamp` > '.(time() - 86400).'';
					$total_prt = $db->get_var($sql);
				
                ?>
				<div id="request_stats_summary">
					<h1><img src="Images/requests.png" alt="Latest Requests" style="vertical-align:middle;" />No. of requests:</h1><br clear="all" /><br />
					<p id="part_nums" style="font-size:16px;text-align:left;padding-left:100px;">Total Unresolved: <span><?php echo $total_pr; ?></span><br />Last 24 Hours: <span><?php echo (($total_prt) ? $total_prt : 0); ?></span></p>
					<br clear="all" />
                </div>
				<br clear="all" />
				&nbsp;
    		</div>

            <div id="quotes" class="ui-corner-all b1light" style="padding-bottom:10px;">
            
            	<h1><img src="Images/quotes.png" alt="Quotes" style="vertical-align:middle;" />Quote Requests</h1>
            	<dl style="margin-left:30px;">
                	<dt>&raquo;&nbsp;<a href="<?php echo ROOT; ?>?_action=requests">View All New Requests</a></dt>
                    <dt>&raquo;&nbsp;<a href="<?php echo ROOT; ?>?_action=saved">My Saved Requests</a></dt>
                    <dt>&raquo;&nbsp;<a href="<?php echo ROOT; ?>?_action=quotes">My Quotes</a></dt>
                </dl>
                <?php if(!$session->access(6)){ ?>
                <div class="ui-widget-overlay ui-corner-all">
                	<div class="ui-widget-overlay ui-corner-all"></div>
                </div>
                <?php } ?>    
            </div>

            <div id="tools" class="ui-corner-all b1light" style="padding-bottom:10px;">
            
            	<h1><img src="Images/settings.png" alt="Settings" style="vertical-align:middle;" />Tools &amp; Settings</h1>
            	<dl style="margin-left:30px;">
                	<dt>&raquo;&nbsp;<a href="<?php echo ROOT; ?>?_action=alerts">Request Alert Settings</a></dt>
                    <dt>&raquo;&nbsp;<a href="<?php echo ROOT; ?>?_action=autoquotes">AutoQuote Settings</a></dt>
                </dl>  
                 <?php if(!$session->access(6)){ ?>
                <div class="ui-widget-overlay ui-corner-all">
                	<div class="ui-widget-overlay ui-corner-all"></div>
                </div>
                <?php } ?> 
            </div>

            <div id="news">
            	<h1 style="margin-bottom:-10px;"><img src="Images/news.png" alt="News" style="vertical-align:middle;" />TextSpare News</h1>
                <?php 
				if($news = $db->get_results('SELECT `news_id`,`title`,`date` FROM `spare_news` ORDER BY `date` DESC LIMIT 0,3'))
					{
					foreach($news as $news)
						{
						?><p><span style="font-weight:bold;"><?php echo date("d/m/y",strtotime($news->date)); ?></span> - <?php echo $news->title; ?>&nbsp;&nbsp;<a href="<?php echo ROOT; ?>_news/?action=view&id=<?php echo $news->news_id; ?>">read more &raquo;</a></p><?php		
						}
					} else {
					?><p>Currently No News</p><?php
					}
              	?>  

              	 <span id="profile_summary"><p><a class="ui-state-default ui-corner-all" href="<?php echo ROOT; ?>?_action=news" title="View all News"><span class="ui-icon ui-icon-script"></span>View All News</a></p></span>
                <!--<p style="margin-top:15px;">&raquo;&nbsp;<a href="#">View all news</a></p>-->
            </div>
           
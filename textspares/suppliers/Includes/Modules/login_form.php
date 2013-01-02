<?php

if(!defined('IN_TEXTSPARES')) { exit; }

/**
* Module:		Login Form
* Version:		v0.99
* File:			login_form.php
* Copyright:	Copyright 2006 - 2010 e-Orchards Ltd.
* License:		see "Application License" details.

Module Info
--------------------------------------------------------------|
Basic form which submits login data to the login class.

Funtion List
--------------------------------------------------------------|

Module Specific
***************

Revision Info
--------------------------------------------------------------|
v0.5-v0.98 		| General beta development/improvements
v0.98-v0.98.1	| Module Info boxes implemented
v0.98.1-v0.99	| URL rewrite support

**********************************************************************************/
/****** Module Code *******/
		if($session->loggedin){ 

				?>
				<h1>Account Login</h1>
				<p>Your are already logged in.</p>
				<br />
				<p>&nbsp;&raquo;&nbsp;<a href="<?php echo ROOT; ?>/_profile/">View your Account Profile.</a></p>
				<br />
				<p>&nbsp;&raquo;&nbsp;<a href="<?php echo ROOT; ?>/?_action=logout">Logout</a></p>
				
				
				<?php

		} else { ?>
        
        <h1>Account Login<?php if(!isset($_GET['_loginmsg'])){?> / Register<?php } ?></h1>
		<br clear="all" />
		<?php if(isset($_GET['_loginmsg'])){ ?>
        <div class="ui-state-error ui-corner-all m10">
            <?php echo "<p><span style=\"float: left; margin-right: 0.3em;\" class=\"ui-icon ui-icon-alert\"></span>".$_GET['_loginmsg']."</p>"; ?>
        </div>
        <?php } ?>

		<script type="text/javascript">
			$(function(){
				//hover states on the static widgets
				$('.login_box a').hover(
					function() { $(this).addClass('ui-state-hover'); }, 
					function() { $(this).removeClass('ui-state-hover'); }
				);

				$('#login_btn').click(function(e){
					$("form[name=_login]").submit();
				});
			});
		</script>
		<div class="login_box ui-corner-all">
			<h2 class="ui-tabs-nav">Existing Supplier<br/></h2>		
            <form name="_login" action="<?php echo ROOT; ?>" method="post">
                <input type="hidden" name="_return" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<input type="hidden" name="_login" value="true" />
                <table style="margin:10px;">
                    <tr>
                        <th>Username:</th>
                        <td><input type="text" class="ui-corner-all" name="_user" tabindex="1" size="32" /></td>
                    </tr>
                    <tr>
                        <th>Password:</th>
                        <td><input type="password" class="ui-corner-all" name="_pswd" tabindex="2" size="26" /></td>
                    </tr>
					<tr>
                        <td colspan="2" style="padding:20px 0 5px 89px;"><!--//<input type="checkbox" value="1" name="_saveUser" /> Remember me?//--></td>
                    </tr>
                    <tr>
                        <td><a class="ui-state-default ui-corner-all" href="#" id="login_btn"><span class="ui-icon ui-icon-key"></span>Login</a></td>
						<td colspan="2"><a class="ui-state-default ui-corner-all" href="<?php echo ROOT; ?>?_action=password"><span class="ui-icon ui-icon-notice"></span>Forgot Password?</a></td>
                    </tr>

                </table>
            </form>
		</div>

        <?php if(!isset($_GET['_loginmsg'])){?>
        <div class="login_box ui-corner-all">	
			<h2>New Supplier</h2>
			<!--<p>Not a registered TextSpares.co.uk Supplier?<br /> Create your own Supplier Account now!</p>-->
            <h1>Join Text Spares <span style="color:#000; font-weight:bold;">TODAY</span></h1>
            <p>We can increase your sales, your turnover and your profit over night.</p>
            <p>How?...<br/>We specialize in generating sales leads directly to you the supplier in real time.<br/>This is your <b style="color:#F90;">FREE</b> to join invitation for you to use our service.</p>
		
            <!--<p>We will be sending out marketing material to 18,000 MOT station, Repair Body Shops and Garages in the UK. We will be the one stop place&nbsp;where Trade and Public will request car parts.</p>
            <p>Dont get left out <span style="color:#F90; font-weight:bold;">JOIN NOW</span>.</p>-->
            <?php if($_SERVER['REQUEST_URI']=="/_shop/checkout/") {
				$rtn = "?_return=checkout";
				}
				?>
			<p style="margin:15px 0 15px 8px;"><a class="ui-state-default ui-corner-all" href="<?=ROOT;?>?_action=register<?=((!empty($rtn)) ? $rtn : '');?>"><span class="ui-icon ui-icon-person"></span>Register an Account &raquo;</a></p>

		</div>
			<?php 
			}
			
		} ?>		
	
	<div class="clearer"></div>
	<br />
    <div style="height:100px;">
        <h2>Logging In Problems?</h2>
        <p>Having trouble logging in, please contact <a href="mailto:support@textspares.co.uk">support@textspares.co.uk</a>!</p>
    </div>
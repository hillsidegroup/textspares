<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Test</title>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
		<meta http-equiv="content-language" content="en" />
		<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="http://textspares.co.uk/rssfeed.xml" />
		<link rel="stylesheet" type="text/css" href="http://textspares.co.uk/include/text.css"/>
		<link rel="stylesheet" href="http://textspares.co.uk/suppliers/Stylesheets/ui-lightness/jquery-ui-1.8.2.custom.css" type="text/css" />
		<link rel="stylesheet" href="http://textspares.co.uk/Include/lytebox.css" type="text/css" />
		<style type="text/css">
			<!--
			body {
				margin-left: 0px;
				margin-top: 5px;
				margin-right: 0px;
				margin-bottom: 5px;
				background-image: url(<?=BASE?>images/mainbg.gif);
				}
			.style1 {
				font-size: 17px;
				font-weight: bold;
				}
				-->
		</style>
	</head>
	<body>
		<table width="981" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<img src="http://textspares.co.uk/sitebanner/head.jpg" width="981" height="133" alt="New &amp; Used Car, Van, Vehicle Parts and Spares from <?=$_SERVER['SERVER_NAME'];?>"/>
				</td>
			</tr>
		</table>
		<table width="981" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
			<tr>
				<td width="191" valign="top">
					<div class="leftlinks" style="padding:0 0 10px 10px;">
						<div class="g-plusone">
						</div>
					</div>
					<div class="leftlinks">
						<div>
							<img src="http://textspares.co.uk/images/h-quicklinks.gif" width="167" height="35" alt="Quick Links"/>
						</div>
						<div>
							<a href="http://textspares.co.uk/index.php" title="Home">Home</a>
						</div>
						<div>
							<a href="http://textspares.co.uk/customer_login.php" title="Customer Login">Customer Login</a>
						</div>
						<div>
							<a href="http://textspares.co.uk/suppliers/" title="Supplier Login">Supplier Login</a>
						</div>
						<div>
							<a href="http://textspares.co.uk/sitemap.php" title="Sitemap">Sitemap</a>
						</div>
						<div>
							<a href="http://textspares.co.uk/Engine/1013" title="Engines">Engines</a>
						</div>
						<div>
							<a href="http://textspares.co.uk/Gearbox/1011" title="Gearboxes">Gearboxes</a>
						</div>
					</div>
					<div style="width:162px;text-align:center;">
						<script type="text/javascript" language="javascript" src="//smarticon.geotrust.com/si.js"></script>
					</div>
				</td>
				<td valign="top">
				<br />
				<br />
				</td>
				<td width="241" valign="top">
					<table border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<div class="homefront">
									<h1>Retreive Your Quotes</h1>
									<form name="_login_form" action="http://textspares.co.uk/" method="post">
										<input type="hidden" name="_login" value="true" />
										<input type="text" name="_username" value="Username or E-mail Address" class="replace login" style="width:185px;" /><br/>
										<input type="password" name="_password" value="Password" class="replace login" style="width:90px;" />
										<input type="submit" name="customer_login" value="Login" class="login" style="width:90px;" />
									</form>
									<br/>
									&nbsp;
									<br/>
									<a class="lyteframe sbb onblue" href="my_login.php">Forgot Login Details</a>
									<br/>
									<h1>Welcome back <?=$session->userdata['name'];?></h1>
									<a href="http://textspares.co.uk/" target="_self" class="sbb onblue">New Request</a>
									<a href="http://textspares.co.uk/my_requests.php" target="_self" class="sbb onblue">My Requests</a>
									<a href="http://textspares.co.uk/my_history.php" target="_self" class="sbb onblue">Order History</a>
									<a href="http://textspares.co.uk/?_action=logout" target="_self" class="sbb onblue">Logout</a>
									<br/>
								</div>
								<div class="homefront">
									<h1>NEW &amp; USED CAR PARTS</h1>
									<div class="image">
										<img width="198" height="83" alt="New and Used Cars" title="New and Used Car Parts and Engines" src="http://textspares.co.uk/homebanners/thumb/centercar_image01_01.gif" />
									</div>
									<select name="select8" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
								<div class="homefront">
									<h1>4x4 SUV PARTS</h1>
									<div class="image">
										<img width="198" height="83" alt="4x4 SUV Parts" title="4x4 SUV Parts and Engiens" src="http://textspares.co.uk/homebanners/thumb/centercar_image02_02.jpg" />
									</div>
									<select name="select9" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
								<div class="homefront">
									<h1>COMMERCIAL VAN PARTS</h1>
									<div class="image">
										<img width="198" height="83" alt="Commercial Vans" title="Commercial Van Parts and Engines" src="http://textspares.co.uk/homebanners/thumb/centercar_image03_03.gif" />
									</div>
									<select name="select10" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
								<div class="homefront">
									<h1>JAPANESE IMPORTS</h1>
									<div class="image">
										<img width="198" height="83" alt="Japanese Imports" title="Japanese Car Parts and Engines" src="http://textspares.co.uk/homebanners/thumb/centercar_image04_04.gif" />
									</div>
									<select name="select11" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
								<div class="homefront">
									<h1>ENGINES &amp; CYLINDER HEADS</h1>
									<div class="image">
										<img width="198" height="83" alt="ENGINES &amp; CYLINDER HEADS" title="ENGINES &amp; CYLINDER HEADS" src="http://textspares.co.uk/homebanners/thumb/centercar_image05_05.gif" />
									</div>
									<select name="select12" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
								<div class="homefront">
									<h1>RECONDITIONED GEARBOXES</h1>
									<div class="image">
										<img width="198" height="83" alt="RECONDITIONED GEARBOXES" title="RECONDITIONED GEARBOXES" src="http://textspares.co.uk/homebanners/thumb/centercar_image06_06.gif" />
									</div>
									<select name="select13" onchange="MM_jumpMenu('parent',this,0)">
										<option value="">-------Make------</option>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr bgcolor="#01366C" style="height:20px">
				<td align="center">
					<a href="http://textspares.co.uk/index.php" class="bottomlinks">Home</a>&nbsp;
					<span style="color:#FFFFFF">|&nbsp;</span>
					<a href="http://textspares.co.uk/faqs.php" class="bottomlinks">F.A.Q.s</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://textspares.co.uk/sitemap.php" class="bottomlinks">Sitemap</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://textspares.co.uk/privacy.php" rel="nofollow" class="bottomlinks">Privacy</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://textspares.co.uk/contact_us.php" class="bottomlinks">Contact Us</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://textspares.co.uk/textspares_network.php" class="bottomlinks">Join Network</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://textspares.co.uk/terms.php" rel="nofollow" class="bottomlinks">Terms &amp; Conditions</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="http://validator.w3.org/check?uri=http://www.textspares.co.uk/" target="_blank" class="bottomlinks">Valid XHTML</a>&nbsp;
					<span style="color:#FFFFFF">|</span>&nbsp;
					<a href="/rssfeed.xml" target="_blank" class="bottomlinks">RSS</a>
				</td>
			</tr>
		</table>
	</body>
	</html>
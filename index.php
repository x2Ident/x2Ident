<!--
Author (Design): W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("keygen/inc/config.php");
require_once("keygen/inc/init.php");
?>
<!DOCTYPE html>
<html style="height:100%">
<head>
<title>x2Ident</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="x2Ident secure login service" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link href='https://fonts.googleapis.com/css?family=Ubuntu:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body <?php
if(isset(getallheaders()["xident-real-ip"])) {
	echo 'style="background:#ff8825;height:100%"';
}
?> >
	<div class="main">
		<h1>x2Ident - home</h1>
		<?php
if(isset($_SESSION['user'])) {
	echo '<h1>Hallo <i>'.$_SESSION['user'].'</i>, ';
}
else {
	echo '<h1>nicht angemeldet, ';
}
if(isset(getallheaders()["xident-real-ip"])) {
	echo $language['proxy_aktiv'].'</h1>';
}
else {
	echo $language['proxy_inaktiv'].'</h1>';
}
?>
		<center>
		<div class="w3l_main_grids">
			<a href="admin">
				<div class="w3l_main_grid1_w3ls w3l_main_grid1_w3ls_sub">
					<h3>Admin</h3>
					<font color="black">
						<div class="w3l_main_grid1_w3ls_grid">
							<?php echo $language['admin_text']; ?>
						</div>
					</font>
				</div>
			</a>
			<a href="keygen">
				<div class="w3l_main_grid1_w3ls">
					<h3>Keygen</h3>
					<font color="black">
						<div class="w3l_main_grid1_w3ls_grid">
							<?php echo $language['keygen_text']; ?>				
						</div>
					</font>
			</div>
			<div class="clear"> </div>
		</div>
		</center>
		<div class="wthree_copyright">
			<p><font color="white"> © 2016 x2Ident </font> <font color="gray">| Design Template by <a href="https://w3layouts.com"><font color="gray">W3layouts</a></font></font></p> 
		</div>
	</div>
</body>
</html>

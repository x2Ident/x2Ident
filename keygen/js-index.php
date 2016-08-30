<?php
/*
* x2Ident (web interface)
* @see https://github.com/x2Ident/x2Ident
*/

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if(strlen($_SESSION['user'])<1) {
	header("Location: login");
	die($language['loginfirst_link']);
}

require_once("inc/config.php");
require_once("inc/init.php");

//ggf. Logout
if(isset($_POST['logout'])) {
	//TODO: deactivate all OTKs
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
    //echo $eintrag;
	$mysqli->query($eintrag);
	session_unset();
	header("Location: login");
	die($language['loginfirst_link']);
}

?>

<html>
<head>
<title>xIdent: <?php echo $language['keygen_title']; ?></title>
<link rel="stylesheet" href="pure-io.css">
<meta charset="UTF-8">
<style>
.otk_input { width:150px }
</style>
</head>
<body <?php
if(isset(getallheaders()["xident-real-ip"])) {
	echo 'style="background:#ff9925;height:100%"';
}
?> >
<script type='text/javascript'>
var js_id = '<?php
echo $_SESSION['js-id'];
?>';
<?php
	// very proud of this code ;) ~jgherb
	$interface_js = file_get_contents("interface.js");
	preg_match_all("/(@@)([^@]*)(@@)/", $interface_js, $output_array);
	$arr2 = $output_array[2];
	foreach($arr2 as $key) {
		$translation = $language[$key];
		$old = "@@".$key."@@";
		$interface_js = str_replace($old,$translation,$interface_js);
	}
	echo $interface_js;
?>
</script>
<h1><a href="../">x2Ident</a>: <?php echo $language['otk_create_title']; ?></h1>
<?php
if(isset(getallheaders()["xident-real-ip"])) {
	echo ucfirst($language['proxy_aktiv']);
}
else {
	echo ucfirst($language['proxy_inaktiv']);
}
echo "<br>".ucfirst($language['angemeldet_als']).": <i>".$_SESSION['user']."</i>";
echo '<div id="session_countdown"></div>';
echo '<form action="" method="post"><input type="hidden" name="logout" value="true"><input type="submit" value="'.$language['logout'].'"></form>';
?>
<div id="content">
bitte warten...
</div>
<br><a href="settings"><?php echo $language['settings']; ?></a>
</body>
</html>

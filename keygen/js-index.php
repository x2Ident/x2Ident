<?php
/*
* x2Ident (web interface)
* @version: release 1.0.0
* @see https://github.com/x2Ident/x2Ident
*/

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if(strlen($_SESSION['user'])<1) {
	header("Location: login");
	//var_dump($_SESSION);
	die('Bitte zuerst <a href="login">einloggen</a>');
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
	die('Bitte zuerst <a href="login">einloggen</a>');
}

?>
<html>
<head>
<title>xIdent: Keygen JS demo</title>
<link rel="stylesheet" href="pure-io.css">
<meta charset="UTF-8">
<style>
.otk_input { width:150px }
</style>
</head>
<body>
<script src="interface.js"></script>
<h1><a href="../">x2Ident</a>: Einmal-Key erstellen</h1>
<?php
echo "Angemeldet als: <i>".$_SESSION['user']."</i>";
echo '<div id="session_countdown"></div>';
echo '<form action="" method="post"><input type="hidden" name="logout" value="true"><input type="submit" value="Logout"></form>';
?>
<div id="content">
bitte warten...
</div>
<br><a href="settings">Einstellungen</a>
</body>
</html>

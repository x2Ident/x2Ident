<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../inc/config.php");
require_once("../inc/init.php");

if(strlen($_SESSION['user'])<1) {
	header("Location: login");
	die('Bitte zuerst <a href="login">einloggen</a>');
}

//ggf. Logout
if(isset($_POST['logout'])) {
	//TODO: deactivate all OTKs
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
	$mysqli->query($eintrag);
	session_unset();
	header("Location: login");
	die('Bitte zuerst <a href="login">einloggen</a>');
}


$form_keyerstellen = '<form action="" method="post"><input type="hidden" name="otk_pw_id" value="@@id@@"><input type="submit" value="Key erstellen"></form>';

//ggf. wert in config schreiben
if(isset($_POST['save_key'])) {
	$conf_key = $_POST['save_key'];
	$conf_value = $_POST['save_value'];
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "UPDATE config SET conf_value='$conf_value' WHERE conf_key='$conf_key' ";
	$mysqli->query($eintrag);
}

echo '
<html>
<head>
<link rel="stylesheet" href="../pure-io.css">
<title>xIdent: Settings</title>
<!-- <meta http-equiv="refresh" content="30"> -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
</script>
<meta charset="utf-8"/>
</head>
<body>
<h1><a href="../">x2Ident</a>: Settings</h1>';
echo "Angemeldet als: <i>".$_SESSION['user']."</i>";
echo '<form action="" method="post"><input type="hidden" name="logout" value="true"><input type="submit" value="Logout"></form>';

echo '<table style="width:100%" class="pure-table"><thead>
  <tr>
    <th>Key</th>
    <th style="width:80%">Value</th>
  </tr></thead><tbody>';

//Load config
$config = array();
$query = "SELECT conf_key, conf_value FROM config";
if ($result = $GLOBALS['mysqli']->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$conf_key = $obj->conf_key;			
			$conf_value = $obj->conf_value;
			$GLOBALS['config'][$conf_key] = $conf_value;
		}
    }
    /* free result set */
    $result->close();

foreach ($config as $key => $val) {
	$key_html = $key;//"<input type=\"text\" value=\"$key\" readonly></input>";
	$value_html = '<form action="" method="post"><input type="hidden" name="save_key" value="'.$key.'"><input style="width:80%" type="text" name="save_value" value="'.$val.'"></input> <input style="width:15%" type="submit" value="Save"></input></form>';

	echo "<tr>
    <td>$key_html</td>
    <td>$value_html</td>
  </tr>";

}
//var_dump($data);
echo " </tbody></table></body></html>";

//Sonderzeichen (auch Satzzeichen) verursachen beim Login Probleme
function rand_char($length) {
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  	$random = '';
  	$max = strlen($characters) - 1;
 	for ($i = 0; $i < $length; $i++) {
		$random .= $characters[mt_rand(0, $max)];
	}
	return $random;
}

function reload_conf() {
//Load config
$config = array();
$query = "SELECT conf_key, conf_value FROM config";
if ($result = $GLOBALS['mysqli']->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$conf_key = $obj->conf_key;			
			$conf_value = $obj->conf_value;
			$GLOBALS['config'][$conf_key] = $conf_value;
		}
    }
    /* free result set */
    $result->close();
}

?>

<?php
/*
* x2Ident (web interface)
* @version: release 1.0.0
* @see https://github.com/x2Ident/x2Ident
*/

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once("../inc/config.php");
require_once("../inc/init.php");

if(strlen($_SESSION['user'])<1) {
	header("Location: login");
	die($language['loginfirst_link']);
}

//ggf. Logout
if(isset($_POST['logout'])) {
	//TODO: deactivate all OTKs
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
	$mysqli->query($eintrag);
	session_unset();
	header("Location: ../login");
	die($language['loginfirst_link']);
}

//ggf. wert in config schreiben
if(isset($_POST['save_key'])) {
	$conf_key = $_POST['save_key'];
	$conf_value = $_POST['save_value'];
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "UPDATE config SET conf_value='$conf_value' WHERE conf_key='$conf_key' ";
	$mysqli->query($eintrag);
	header("Location: ");
	die();
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
<h1><a href="../">x2Ident</a>: '.$language['settings'].'</h1>';
echo $language['angemeldet_als'].": <i>".$_SESSION['user']."</i>";
echo '<form action="" method="post"><input type="hidden" name="logout" value="true"><input type="submit" value="'.$language['logout'].'"></form>';

echo '<table style="width:100%" class="pure-table"><thead>
  <tr>
    <th>'.$language['key'].'</th>
    <th style="width:50%">'.$language['value'].'</th>
    <th>'.$language['default'].'</th>
    <th>'.$language['info'].'</th>
  </tr></thead><tbody>';


//Load config
$config = array();
$query = "SELECT * FROM config";
if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			if($obj->only_admin==1) {
				if($_SESSION['user'] !== 'admin') {
					continue;
				}
			}
			$conf_key = $obj->conf_key;
			$conf_value = $obj->conf_value;
			$conf_default = $obj->conf_default;
			$conf_info = $obj->conf_info;
			$config[$conf_key] = $conf_value;
			$config_default[$conf_key] = $conf_default;
			$config_info[$conf_key] = $conf_info;
		}
    }
    /* free result set */
    $result->close();


foreach ($config as $key => $val) {
	$key_html = $key;//"<input type=\"text\" value=\"$key\" readonly></input>";
	$value_html = '<form action="" method="post"><input type="hidden" name="save_key" value="'.$key.'"><input style="width:80%" type="text" name="save_value" value="'.$val.'"></input> <input style="width:15%" type="submit" value="'.$language['save'].'"></input></form>';
	$default_html = $GLOBALS['config_default'][$key];
	$info_html = $GLOBALS['config_info'][$key];
	echo "<tr>
    <td>$key_html</td>
    <td>$value_html</td>
    <td>$default_html</td>
    <td>$info_html</td>
  </tr>";

}
//var_dump($data);
echo " </tbody></table><br>
".$language['einstellungen_erst_nach_login']."
</body></html>";

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


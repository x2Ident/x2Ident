<?php
/*
* x2Ident (web interface)
* @version: release 1.0.0
* @see https://github.com/x2Ident/x2Ident
*/

if (session_id() == "") {
	session_start();
}

$config = array();
$config_default = array();
$config_info = array();

$mysqli = new mysqli($host, $database, $password, $database);

//Check DB connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

$timestamp = time();
$query = "SELECT * FROM session_user WHERE expires<$timestamp";
if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$sess_id = $obj->sess_id;			
			$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
			$mysqli->query($eintrag);
		}
    }
    /* free result set */
    $result->close();

$query = "DELETE FROM session_user WHERE expires<$timestamp";
$mysqli->query($query);

//Load config
$config = array();
$query = "SELECT * FROM config";
if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			if($obj->only_admin==1) {
				if(!isset($_SESSION['user'])) {
					continue;
				}
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

//Load language
$language = array();
$query = "SELECT * FROM language";
if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($row = $result->fetch_assoc()) {
			$conf_lang = $config['language'];
			$lang_key = $row['key'];
			$lang_value = $row[$conf_lang];
			$language[$lang_key] = $lang_value;
		}
    }
    /* free result set */
    $result->close();

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
?>

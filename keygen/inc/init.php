<?php
$config = array();

$mysqli = new mysqli($host, $database, $password, $database);

//Check DB connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
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

load_conf();

function load_conf() {
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

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
?>

<?php
$config = array();
$config_default = array();
$config_info = array();

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

//Load config
$config = array();
$query = "SELECT * FROM config";
if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
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

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
?>

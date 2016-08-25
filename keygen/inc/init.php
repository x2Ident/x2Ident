<?php

$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");

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

function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
?>

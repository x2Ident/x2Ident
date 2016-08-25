<?php

$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");

//Check DB connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$timestamp = time();
$query = "DELETE FROM session_user WHERE expires<$timestamp";
$mysqli->query($query);
?>

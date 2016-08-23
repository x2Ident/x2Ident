<?php
require_once 'inc/GoogleAuthenticator.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
echo "Secret is: ".$secret."\n\n";
$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$eintrag = "UPDATE auth SET secret='".$secret."' WHERE id=1";
echo $eintrag;
$mysqli->query($eintrag);

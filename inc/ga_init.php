<?php
/*
* x2Ident (web interface)
* @version: release 1.2.0
* @see https://github.com/x2Ident/x2Ident
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../keygen/inc/config.php");

if(isset($_SESSION['login'])) {
if(strlen($_SESSION['login'])>0) {
	$mysqli_xi = new mysqli($host, $user, $password, $database);
	// check connection
	if ($mysqli_xi->connect_error) {
	  trigger_error('Database connection failed: '  . $mysqli_xi->connect_error, E_USER_ERROR);
	}
	
	$secret = "";
	$show_popup = true;
	$anzahl = 0;
	$no_secret = true;
	$abfrage = "SELECT user, secret, not_show FROM auth WHERE user = '".$_SESSION['login']."';";

	if ($result = $mysqli_xi->query($abfrage)) {
	    while ($obj = $result->fetch_object()) {
			$anzahl++;
			if(($obj->not_show)==1) {
				$show_popup = false;
			}
			if(strlen($obj->secret)>1) {
				$no_secret = false;
			}
			$secret = $obj->secret;
		}
	}
	if($show_popup) {
		require_once 'ga.php';
		$ga = new PHPGangsta_GoogleAuthenticator();
		if($anzahl==0) {
			$secret = $ga->createSecret();
			$query = "INSERT INTO auth (user,secret,not_show) VALUES ('".$_SESSION['login']."','".$secret."','0');";
			$mysqli_xi->query($query);
		}
		else if($no_secret) {
			$secret = $ga->createSecret();
			$query = "UPDATE auth SET secret='".$secret."', not_show='0' WHERE user = '".$_SESSION['login']."';";
			$mysqli_xi->query($query);
		}
		$qrCodeUrl = $ga->getQRCodeGoogleUrl('xIdent:'.$_SESSION['login'], $secret);
		$ch = curl_init ($qrCodeUrl);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    	$raw=curl_exec($ch);
    	curl_close ($ch);
		if(file_exists('../inc/qr.png')){
		    unlink('../inc/qr.png');
    	}
		// delete file before writing due to security reasons would be better
    	$fp = fopen('../inc/qr.png','x');
		fwrite($fp, $raw);
		fclose($fp);
		echo '
<div id="xident_qr" class="div_center">
            <div id="xident_qr_inner" style="min-height:25px;background-color:#FFC0C0;border:2px solid #FF0000;padding:5px;text-align:center; z-index:9999999;">xIdent:'.$_SESSION['login'].'<br><img src="../inc/qr.php" alt="Google Auth">'.'<br>'.$secret.'<br>
<button type="button" onclick="document.getElementById(\'xident_qr\').style.display=\'none\'">Schließen</button></div>
        </div>';
	}
	$mysqli_xi->commit();
	$mysqli_xi->close();
}
}
?>


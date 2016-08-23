<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if(strlen($_SESSION['login'])>0) {
	$mysqli_xi = new mysqli("localhost", "xident", "jugendhackt", "xident");
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
		require_once 'GoogleAuthenticator.php';
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
		echo '
<div id="xident_qr" class="div_center">
            <div id="xident_qr_inner" style="min-height:25px;background-color:#FFC0C0;border:2px solid #FF0000;padding:5px;text-align:center; z-index:9999999;">xIdent:'.$_SESSION['login'].'<br><img src="'.$qrCodeUrl.'" alt="Google Auth">'.'<br>'.$secret.'<br>
<button type="button" onclick="document.getElementById(\'xident_qr\').style.display=\'none\'">Schließen</button></div>
        </div>';
	}
	$mysqli_xi->commit();
	$mysqli_xi->close();
}
?>


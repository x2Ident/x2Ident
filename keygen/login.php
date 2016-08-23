<?php
session_start();

//Get user IP address
$ip = "";
if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
}
else {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$proxy_ip = getallheaders()["xident-real-ip"];
if(strlen($proxy_ip)>1) {
	$ip = $proxy_ip;
}
//var_dump(getallheaders());

echo '
<html>
<head>
<title>xIdent: Login</title>
</head>
<body>
<h1><a href="../">xIdent</a>: Keygen Login</h1>
';


$login_form = '<form action="" method="post">Benutzername:<input type="text" name="user_name" value=""><br>Google Authenticator Code:<input type="text" name="auth_code" value=""><br><input type="submit" value="Einloggen"></form>
<br>Falls du keinen Code hast: <a href="../admin">logge dich in der Admin-Zone ein</a> und scanne den QR-Code.';

$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

require_once 'inc/GoogleAuthenticator.php';
$secret = "";
$ga = new PHPGangsta_GoogleAuthenticator();
 
if(isset($_POST['auth_code'])) {
	$oneCode = $_POST['auth_code'];
	$username = $_POST['user_name'];	

	$query = "SELECT secret FROM auth WHERE user='$username'";
	if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
	        $secret = $obj->secret;
	    }
	}

	if(strlen($secret)>0) {
		$checkResult = $ga->verifyCode($secret, $oneCode, 2);    // 2 = 2*30sec clock tolerance
		if ($checkResult) {
			$sess_id = rand_char(10);
            $user_agent = $_SERVER ['HTTP_USER_AGENT'];
            $_SESSION['sess_id'] = $sess_id;
			$_SESSION['user']=$username;
			$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
			$mysqli->query($eintrag);
			$eintrag = "INSERT INTO session_user (user,ip,sess_id,user_agent) VALUES ('$username','$ip','$sess_id','$user_agent')";
            echo $ip;
			$mysqli->query($eintrag);
			header("Location: ./");
			die();
		}
		else {
			login_failed($login_form);
		}
	}
	else {
		echo "<h1>Du hast noch keinen Google Authenticator Code!</h1>";
		login_failed($login_form);
	}
}
else {

	echo $login_form;
}

echo '
</body>
</html>
';


function login_failed($login_form) {
	echo '<h1>Anmeldung fehlgeschlagen!</h1>';
	$_SESSION['user'] = null;
	$_SESSION['sess_id'] = null;
	$_SESSION['cookie_id'] = null;
	echo $login_form;
}

function rand_char($length) {
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  	$random = '';
  	$max = strlen($characters) - 1;
 	for ($i = 0; $i < $length; $i++) {
		$random .= $characters[mt_rand(0, $max)];
	}
	return $random;
}
?>

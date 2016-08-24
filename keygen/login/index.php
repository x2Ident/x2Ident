<?php

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);


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

$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

require_once '../inc/GoogleAuthenticator.php';
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
			header("Location: ../");
			die();
		}
		else {
			printLoginForm("Login failed");
			login_failed($login_form);
		}
	}
	else {
		printLoginForm("Du hast noch keinen Google Authenticator Code!");
		login_failed($login_form);
	}
}
else {
	//default case
	printLoginForm("");
}

function login_failed($login_form) {
	$_SESSION['user'] = null;
	$_SESSION['sess_id'] = null;
	$_SESSION['cookie_id'] = null;
}

function printLoginForm($message) {
	$maske = file_get_contents("maske.html");
	if(strlen($message)>1) {
		$maske = str_replace("<!--message-->","<center><h2>".$message."</h2></center>",$maske);
	}
	echo $maske;
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

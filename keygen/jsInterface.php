<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once("inc/init.php");

if(strlen($_SESSION['user'])<1) {
	die('[xi]_jsif_not-logged-in|Bitte zuerst <a href="login">einloggen</a>');
}

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

//Get JS id
$js_id = $_POST['js-id'];
if(strlen($js_id)<5) {
	die("JS-id not valid.");
}

include('api.secret.php');

//Check js_id
$js_id_valide = false;
$user = "";
$db_ip = "";
$sess_id = "";
$query = "SELECT user, ip, sess_id FROM session_user WHERE js_id='$js_id'";
    //echo $query;
	if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$js_id_valide = true;
			$user = $obj->user;
			$sess_id = $obj->sess_id;
			$db_ip = $obj->ip;
		}
	}
if(!$js_id_valide) {
	session_unset();
	die("[xi]_JS-id_not_valid.|Bitte zuerst <a href=\"login\">einloggen</a>");
}

if(strcmp($ip,$db_ip)!=0) {
	session_unset();
	die("[xi]_IP-Address_not_valid.|Bitte zuerst <a href=\"login\">einloggen</a>");
}

//Daten abrufen
$ch = curl_init();
$url = str_replace("@@user@@",$user,$api_url);

//URL übergeben
curl_setopt($ch, CURLOPT_URL, $url);

//Parameter für Netzwerk-Anfrage konfigurieren
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

//Anfrage durchführen und Antwort in $result speichern
$result = curl_exec ($ch);
$data = json_decode($result,true);

//ggf. OTK generieren und in DB schreiben
if(isset($_POST['createOTK-id'])) {
	$pwid = $_POST['createOTK-id'];
	$real_password = $data[$pwid]['pw'];
	$pw_url = $data[$pwid]['url'];
	$timestamp = time();
	$key = rand_char(10);
	$eintrag = "DELETE FROM onetimekeys WHERE pwid=$pwid AND ((user='$user' AND sess_id='$sess_id') OR expires<$timestamp)";
	//echo $eintrag;
	$mysqli->query($eintrag);
	$timestamp = time();
	$expires = $timestamp + 60;
	$eintrag = "INSERT INTO onetimekeys (user, sess_id, pwid, onetime, real_pw, pw_active, expires, url) VALUES ('$user', '$sess_id', '$pwid', '$key', '$real_password','1', '$expires', '$pw_url')";
	//echo $eintrag;
	$mysqli->query($eintrag);
	die("OK");
}

//ggf. OTK löschen
if(isset($_POST['removeOTK-id'])) {
	$del_id = $_POST['removeOTK-id'];
	$eintrag = "UPDATE onetimekeys SET pw_active='0', expires='-1' WHERE pwid='$del_id' AND sess_id='$sess_id' ";
	$mysqli->query($eintrag);
	die("OK");
}

//ggf. Global setzen
if(isset($_POST['set_global'])) {
	$global_state = $_POST['set_global'];
	$pwid = $_POST['global_otk_id'];
	$eintrag = "UPDATE onetimekeys SET pw_global=$global_state WHERE pwid='$pwid' AND sess_id='$sess_id' ";
	$mysqli->query($eintrag);
	die("OK");
}

$id = 0;
foreach ($data as $key => $val) {
	$id = $key;
	$title = $val['label'];
	$url = $val['url'];
	$website = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
	$username = $val['login'];
	$lastlogin = 0;
	$expires = 0;
	$otk = "-";
	$pw_global = "2";
	
	//Get OTKs from db
	$query = "SELECT onetime, expires, pw_active, pw_global FROM onetimekeys WHERE pwid='$id' AND sess_id='$sess_id'";
    //echo $query;
	if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$expires = $obj->expires;
			if($obj->pw_active == 1) {
	        	$otk = $obj->onetime;
				$pw_global = $obj->pw_global;
			}
			else {
				$pw_global = "2";
			}
	    }
	
	    /* free result set */
	    $result->close();
	}

	//Get last login time
	$query = "SELECT last_login FROM history WHERE pwid='".$id."'";
	if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$lastlogin = $obj->last_login;
	    }
	
	    /* free result set */
	    $result->close();
	}

	//Calc last login
	$timestamp = time();
	$diff = $timestamp-$lastlogin;
	$lastlogin_text = "vor ".$diff." Sekunde(n)";
	if($diff>=60) {
		$diff = round($diff/60);
		$lastlogin_text = "vor ".$diff." Minute(n)";

		if($diff>=60) {
			$diff = round($diff/60);
			$lastlogin_text = "vor ".$diff." Stunde(n)";

			if($diff>=24) {
				$diff = round($diff/24);
				$lastlogin_text = "vor ".$diff." Tag(en)";

				if($diff>=30) {
					$diff = round($diff/30);
					$lastlogin_text = "vor ".$diff." month ago";
				}
			}
		}
	}

	//Calc last login
	$timestamp = time();
	$diff2 = $expires-$timestamp;
	$expires_text = $diff2." Sekunden";
    
	
	//echo "expires: ".$expires."; timestamp: ".$timestamp."|";
	if($expires<$timestamp-1) {
		//maybe delete real passwort due to security?
		$eintrag = "UPDATE onetimekeys SET pw_active='0' WHERE pwid = '$id' AND sess_id='$sess_id'";
		$mysqli->query($eintrag);
		$otk = "-";
		$expires_text = "-";
	}

	$output = "$id;$title;$url;$username;$otk;$pw_global;$expires;$lastlogin|";
	$output = html_entity_decode($output);
	echo $output;

}

echo "OK";
//var_dump($data);
//echo " </tbody></table></body></html>";

//Sonderzeichen (auch Satzzeichen) verursachen beim Login Probleme
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

<?php
/*
* x2Ident (web interface)
* @see https://github.com/x2Ident/x2Ident
*/

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once("inc/config.php");
require_once("inc/init.php");

if(strlen($_SESSION['user'])<1) {
	die('[xi]_jsif_login_not-logged-in|'.$language['loginfirst_link']);
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
	die("[xi]_jsif_login_JS-id_not_valid.|".$language['loginfirst_link']);
}

//include('api.secret.php'); --> api_key is now stored in the database

$api_key = $config['api_key'];

$api_url = $config['url_xi_dir']."/admin/apix/index.php/read/userpw/@@user@@?apikey=".$api_key;
$api_url = str_replace("//admin","/admin",$api_url);
#echo $api_url.";";

//Check js_id
$js_id_valide = false;
$user = "";
$db_ip = "";
$sess_id = "";
$session_expires = 0;
$query = "SELECT user, ip, sess_id, expires FROM session_user WHERE BINARY js_id='$js_id'";
    //echo $query;
	if ($result = $mysqli->query($query)) {
	
	    /* fetch object array */
	    while ($obj = $result->fetch_object()) {
			$js_id_valide = true;
			$user = $obj->user;
			$sess_id = $obj->sess_id;
			$db_ip = $obj->ip;
			$session_expires = $obj->expires;
		}
	}

//ggf. session beenden, weil sie ausgelaufen ist
$timestamp = time();
if($timestamp>$session_expires) {
	die("[xi]_jsif_login_session_expired.|".$language['loginfirst_link']);
}

if(!$js_id_valide) {
	session_unset();
	die("[xi]_jsif_login_JS-id_not_valid.|".$language['loginfirst_link']);
}

if(strcmp($ip,$db_ip)!=0) {
	session_unset();
	die("[xi]_jsif_login_IP-Address_not_valid.|".$language['loginfirst_link']);
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

//Prüfen, ob Verbindung erfolgreich war
if(strcmp($result,'{"err":"No results"}')!=0) {
	$error_result = '{"err":"';
	if(substr( $result, 0, strlen($error_result) ) === $error_result) {
		die("[xi]_jsif_API_conn_failed.|".$language['api_conn_failed']);
	}
}

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
	$expires = $timestamp + $config['otk_expires'];
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

	if($expires<$timestamp-1) {
		//maybe delete real passwort due to security?
		$eintrag = "UPDATE onetimekeys SET pw_active='0', real_pw='' realWHERE pwid = '$id' AND sess_id='$sess_id'";
		$mysqli->query($eintrag);
		$otk = "-";
		$expires_text = "-";
	}

	$output = "$id;$title;$url;$username;$otk;$pw_global;$expires;$lastlogin|";
	$output = html_entity_decode($output);
	echo $output;

}

//calc session countdown
$timestamp = time();
$session_countdown = $session_expires - $timestamp;

echo "$session_countdown|OK";
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

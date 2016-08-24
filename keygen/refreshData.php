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

//Get JS id
$js_id = $_POST['js-id'];

include('api.secret.php');

$mysqli = new mysqli("localhost", "xident", "jugendhackt", "xident");

//Check DB connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

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

if(!js_id_valide) {
	die("JS-id not valid.");
}

if(strcmp($ip,$db_ip)) {
	//evtl. Warnung
}

//Daten abrufen
$ch = curl_init();
$url = str_replace("@@user@@",$_SESSION['user'],$api_url);

//URL übergeben
curl_setopt($ch, CURLOPT_URL, $url);

//Parameter für Netzwerk-Anfrage konfigurieren
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

//Anfrage durchführen und Antwort in $result speichern
$result = curl_exec ($ch);
$data = json_decode($result,true);

$form_keyerstellen = '<form action="" method="post"><input type="hidden" name="otk_pw_id" value="@@id@@"><input type="submit" value="Key erstellen"></form>';

//ggf. OTK generieren und in DB schreiben
if(isset($_POST['otk_pw_id'])) {
	$username = $_SESSION['user'];
    $sess_id = $_SESSION['sess_id'];
	$pwid = $_POST['otk_pw_id'];
	$real_password = $data[$pwid]['pw'];
	$timestamp = time();
	$key = rand_char(10);
	$eintrag = "DELETE FROM onetimekeys WHERE pwid=$pwid AND ((user='$username' AND sess_id='$sess_id') OR expires<$timestamp)";
	//echo $eintrag;
	$mysqli->query($eintrag);
	$timestamp = time();
	$expires = $timestamp + 60;
	$eintrag = "INSERT INTO onetimekeys (user, sess_id, pwid, onetime, real_pw, pw_active, expires) VALUES ('$username', '$sess_id', '$pwid', '$key', '$real_password','1', '$expires')";
	//echo $eintrag;
	$mysqli->query($eintrag);
}

//ggf. OTK löschen
if(isset($_POST['otk_del_id'])) {
	$del_id = $_POST['otk_del_id'];
	$eintrag = "UPDATE onetimekeys SET pw_active='0', expires='-1' WHERE pwid = '".$del_id."' ";
	$mysqli->query($eintrag);
}

//ggf. OTK-Global setzen
if(isset($_POST['otk_global_id'])) {
	$pwid = $_POST['otk_global_id'];
	$global_value = $_POST['otk_global'];
	$eintrag = "UPDATE onetimekeys SET globalpw=$global WHERE pwid = '".$pwid."' ";
	$mysqli->query($eintrag);
}

//ggf. Logout
if(isset($_POST['logout'])) {
	//TODO: deactivate all OTKs
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "DELETE FROM session_user WHERE sess_id = '$sess_id'";
    //echo $eintrag;
	$mysqli->query($eintrag);
	$_SESSION['user'] = null;
	$_SESSION['sess_id'] = null;
	$_SESSION['cookie_id'] = null;
	header("Location: login");
	die('Bitte zuerst <a href="login">einloggen</a>');
}

/*echo '<html><head>

<link rel="stylesheet" href="pure-io.css">
<title>xIdent: Keygen</title>
<meta http-equiv="refresh" content="5">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
window.setTimeout(countdown_expire, 1000);
function countdown_expire() {
    var elements = document.getElementsByClassName("expires");
    var elements_len = elements.length;
    for(i=0; i<elements.length; i++) {
		var item = elements[i];
		var counter = parseInt(item.innerHTML);
        if(!isNaN(counter)) {
    		counter--;
            if(counter<1) {
				item.innerHTML = "abgelaufen";
			}
			else {
    			var counter_text = counter+" Sekunden";
				if(counter==1) {
					counter_text = counter+" Sekunde";
				}
				if(counter==0) {
					item.innerHTML = "abgelaufen";
				}
				if(counter<10) {
					item.style.color = "red"; 
				}
				item.innerHTML = counter_text;
			}
		}
	}
	window.setTimeout(countdown_expire, 1000);
}
</script>
<meta charset="utf-8"/>
</head>
<body>
<h1><a href="../">xIdent</a>: Einmal-Key erstellen</h1>';
echo "Angemeldet als: <i>".$_SESSION['user']."</i>";
echo '<form action="" method="post"><input type="hidden" name="logout" value="true"><input type="submit" value="Logout"></form>';

echo '<table  class="pure-table"><thead>
  <tr>
    <th>ID</th>
    <th>Titel</th>
    <th>Website</th>
    <th>Benutzername</th>
    <th>Einmal-Key</th>
    <th>Global</th>
    <th>Läuft ab in</th>
    <th>Letzter Login</th>
  </tr></thead><tbody>';
*/

$id = 0;
foreach ($data as $key => $val) {
	$id = $key;
	$title = $val['label'];
	$url = $val['url'];
	$website = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
	$username = $val['login'];
	$lastlogin = 0;
	$expires = -1;
	$otk = "-";
	$pw_global = "0";
    $sess_id = $_SESSION['sess_id'];
	
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
    
	$sess_id = $_SESSION['sess_id'];
	
	//echo "expires: ".$expires."; timestamp: ".$timestamp."|";
	if($expires<$timestamp-1) {
		$eintrag = "UPDATE onetimekeys SET pw_active='0' WHERE pwid = '$id' AND sess_id='$sess_id'";
		$mysqli->query($eintrag);
		$otk = str_replace("@@id@@",$id,$form_keyerstellen);
		$expires_text = "-";
	}

	$output = "$id;$title;$website;$expires;$pw_global;$last_login|";
	echo $output;

/*
	echo "<tr>
    <td>$id</td>
    <td>$title</td>
    <td>$website</td>
    <td>$username</td>
    <td>$otk</td>
    <td>$global_text</td>
    <td><div class=\"expires\">$expires_text</div></td>
    <td>$lastlogin_text</td>
  </tr>";
*/

}
//var_dump($data);
echo " </tbody></table></body></html>";

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

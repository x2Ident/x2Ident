<?php

// TODO: Error Handling

// FÜr das Install-Skript ist es hilfreich, Fehler auszugeben
error_reporting(E_ALL);
ini_set('display_errors', 1);

// get url to working directory
$currentURL = curPageURL();
$url2working_dir = str_replace("/install/index.php","/",$currentURL);
$url2working_dir = str_replace("/install/","/",$url2working_dir);
$url2working_dir = str_replace("/install","/",$url2working_dir);


// check if user input finished
if(!isset($_POST['start_install'])) {
	echo '
		<html>
			<head>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>
				<h2>Sie müssen vor der x2Ident-Installation folgendes erledigt haben:</h2>
				<ul>
					<li>TeamPass (bzw. die Admin-Zone) installiert haben. <a href="../admin">Zur Admin-Zonen-Installation</a></li>
					<li>Eine MySQL-Datenbank (und ggf. einen Benutzer) für x2Ident eingerichtet haben</li>
					<li>In der Admin-Zone (als admin) einen API-Key mit Read-Rechten auf alle Einträge erstellt haben</li>
				</ul>
				<form action="" method="post">
					<h2>Datenbankzugangsdaten</h2>
						<p>Host</p> <input type="text" name="db_host"></input>
						<p>Login</p> <input type="text" name="db_login"></input>
						<p>Passwort</p> <input type="text" name="db_password"></input>
						<p>Datenbank</p> <input type="text" name="db_database"></input>
					<h2>TeamPass-API-Key</h2>
						<p>API-Key</p> <input type="text" name="api_key"></input>
					<br>
					<h2>Installation starten</h2>
						<input type="hidden" name="start_install" value="1"></input>
						<input type="submit" value="Installation starten"></input>
				</form>
			</body>
		</html>
	';
	die();
}

$post_values = array("db_host","db_login","db_password","db_database","api_key");
$install_data = array();


// read data from post
foreach ($post_values as $value) {
	if( (isset($_POST[$value])) && (strlen($_POST[$value])>0) ) {
		$install_data[$value] = $_POST[$value];
	}
	else {
		error("Sie müssen für '".$value." einen Wert eingeben.");
	}
}


// save DB credentials
// php config file
$db_host = $install_data['db_host'];
$db_login = $install_data['db_login'];
$db_password = $install_data['db_password'];
$db_database = $install_data['db_database'];

$php_config_file = '<?php
$host = "'.$db_host.'";
$user = "'.$db_login.'";
$password = "'.$db_password.'";
$database = "'.$db_database.'";
?>';
file_put_contents("../keygen/inc/config.php",$php_config_file);


// save DB credentials
// python config file
$python_config_file = 'class config:
    def host(self):
        return "'.$db_host.'"

    def user(self):
        return "'.$db_login.'"

    def password(self):
        return "'.$db_password.'"

    def database(self):
        return "'.$db_database.'"';
file_put_contents("../proxy/config.py",$python_config_file);


// save API-Key
$api_key = $install_data['api_key'];
$api_url = $url2working_dir.'/admin/apix/index.php/read/userpw/@@user@@?apikey='.$api_key;
$api_url = str_replace("//admin","/admin",$api_url);
$php_api_key_file = '<?php
$api_url = "'.$api_url.'";
?>';
file_put_contents("../keygen/api.secret.php",$php_api_key_file);


// establish API connection
$ch = curl_init();
$url = str_replace("@@user@@","admin",$api_url);

//URL übergeben
curl_setopt($ch, CURLOPT_URL, $url);

//Parameter für Netzwerk-Anfrage konfigurieren
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

//Anfrage durchführen und Antwort in $result speichern
$result = curl_exec ($ch);

//Prüfen, ob Verbindung erfolgreich war
$error_result = '{"err":"';
if(substr( $result, 0, strlen($error_result) ) === $error_result) {
	error("API-Verbindung fehlgeschlagen: ".$result);
}


// establish db connection
$mysqli = new mysqli($db_host, $db_login, $db_password, $db_database);
//Check DB connection
if (mysqli_connect_errno()) {
    error("Datenbank-Verbindung fehlgeschlagen: ".mysqli_connect_error());
}


// save config to db
$api_key = $install_data['api_key'];

writeConfig("url_xi_dir", $url2working_dir, $url2working_dir, "");
writeConfig("otk_expires", "60", "60", "in seconds");
writeConfig("session_expires", "3600", "3600", "in seconds");
writeConfig("language", "en");
writeConfig("installed", "1");
writeConfig("api_key", $api_key);

echo '
		<html>
			<head>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>
				<h2>Installation erfolgreich beendet</h2>
				<p>Starten Sie damit, sich in der <a href="../admin">Admin-Zone</a> mit ihrem Benutzer einzuloggen (verwenden Sie nicht admin) und den QR-Code mit der Google-Authenticator-App zu scannen. Dann können Sie sich in der Keygen-Zone mit dem Benutzernamen und einem Google-Authenticator-Key anmelden.</p>
			</body>
		</html>
	';
	die();

function error($message) {
	echo '
		<html>
			<head>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>
				<h1>Installation fehlgeschlagen</h1>
				<h2>'.$message.'</h2>
				<hr></hr>
				<h2>Sie müssen vor der x2Ident-Installation folgendes erledigt haben:</h2>
				<ul>
					<li>TeamPass (bzw. die Admin-Zone) installiert haben</li>
					<li>Eine MySQL-Datenbank (und ggf. einen Benutzer) für x2Ident eingerichtet haben</li>
					<li>In der Admin-Zone (als admin) einen API-Key mit Admin-Rechten erstellt haben</li>
				</ul>
				<form action="" method="post">
					<h2>Datenbankzugangsdaten</h2>
						<p>Host</p> <input type="text" name="db_host"></input>
						<p>Login</p> <input type="text" name="db_login"></input>
						<p>Passwort</p> <input type="text" name="db_password"></input>
						<p>Datenbank</p> <input type="text" name="db_database"></input>
					<h2>TeamPass-API-Key</h2>
						<p>API-Key</p> <input type="text" name="api_key"></input>
					<br>
					<h2>Installation starten</h2>
						<input type="hidden" name="start_install" value="1"></input>
						<input type="submit" value="Installation starten"></input>
				</form>
			</body>
		</html>
	';
	die();
}

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if (false/*$_SERVER["SERVER_PORT"] != "80"*/) {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function writeConfig($key, $value, $default="", $info="") {
	$eintrag = "UPDATE config SET conf_value='$value' WHERE conf_key='$key' ";
	$GLOBALS['mysqli']->query($eintrag);
	//var_dump($GLOBALS);
	if($GLOBALS['mysqli']->affected_rows!=1) {
		$eintrag = "DELETE FROM config WHERE conf_key='$key' ";
		$GLOBALS['mysqli']->query($eintrag);
		$eintrag = "INSERT INTO config (`conf_key`,`conf_value`,`conf_default`,`conf_info`) VALUES ('$key','$value','$default','$info') ";
		$GLOBALS['mysqli']->query($eintrag);
		//echo $eintrag."|".$GLOBALS['mysqli']->affected_rows;
	}
}

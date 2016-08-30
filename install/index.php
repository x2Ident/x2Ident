<?php
/*
* x2Ident (web interface)
* @version: release 1.2.0
* @see https://github.com/x2Ident/x2Ident
*/
$version = "1.3.0";

// TODO: Error Handling

// FÜr das Install-Skript ist es hilfreich, Fehler auszugeben
error_reporting(E_ALL);
ini_set('display_errors', 1);

$install_form = '<form action="" method="post">
					<h2>database credentials</h2>
						<p>Host</p> <input type="text" name="db_host"></input>
						<p>Login</p> <input type="text" name="db_login"></input>
						<p>Password</p> <input type="text" name="db_password"></input>
						<p>Database</p> <input type="text" name="db_database"></input>
					<h2>TeamPass-API-Key</h2>
						<p>API-Key</p> <input type="text" name="api_key"></input>
					<h2>language</h2>
						<select name="language">
							<option value="en" selected>English</option>
							<option value="de" >Deutsch (German)</option>
						</select>
					<br>
					<h2>Start Installation</h2>
						<input type="hidden" name="start_install" value="1"></input>
						<input type="submit" value="Start Installation"></input>
				</form>';

// get url to working directory
$currentURL = curPageURL();
$url2working_dir = str_replace("/install/index.php","/",$currentURL);
$url2working_dir = str_replace("/install/","/",$url2working_dir);
$url2working_dir = str_replace("/install","/",$url2working_dir);

$ssl_conn = false;
$ssl_error = "";
if(!isset($_SERVER["HTTPS"])) {
		$_SERVER["HTTPS"] = "";
	}
	if ($_SERVER["HTTPS"] == "on") {$ssl_conn = true;}
if(!$ssl_conn) {
	$ssl_error = '<h1>Please call this site via TLS (https://), else your x2Ident installation will not be secure!</h1>';
}

// check if user input finished
if(!isset($_POST['start_install'])) {
	echo '
		<html>
			<head>
				<title>x2Ident Installation</title>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>'.$ssl_error.'				
				<h2>You must have following done before installation:</h2>
				<ul>
					<li>TeamPass (admin zone) installed</li>
					<li>created a MySQL-database (and maybe also a user) for x2Ident</li>
					<li>created an API-key in the admin zone (as admin) with the permissions you want</li>
				</ul>
				'.$install_form.'
			</body>
		</html>
	';
	die();
}

$post_values = array("db_host","db_login","db_password","db_database","api_key","language");
$install_data = array();


// read data from post
foreach ($post_values as $value) {
	if( (isset($_POST[$value])) && (strlen($_POST[$value])>0) ) {
		$install_data[$value] = $_POST[$value];
	}
	else {
		error("You left '".$value." free.");
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
if(strcmp($result,'{"err":"No results"}')!=0) {
	$error_result = '{"err":"';
	if(substr( $result, 0, strlen($error_result) ) === $error_result) {
		error("API-Connection failed: ".$result);
	}
}



// establish db connection
$mysqli = new mysqli($db_host, $db_login, $db_password, $db_database);
//Check DB connection
if ($mysqli->connect_error) {
    error("Database-Connection failed: ".$mysqli->connect_error);
}


/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    error("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}


// import db dump
$filename = "dump.sql";
// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line)
{
	// Skip it if it's a comment
	if (substr($line, 0, 2) == '--' || $line == '') {
    	continue;
	}
	if (substr($line, 0, 1) == '/*' || $line == '') {
    	continue;
	}
	// Add this line to the current segment
	$templine .= $line;
	// If it has a semicolon at the end, it's the end of the query
	if (substr(trim($line), -1, 1) == ';')
	{
    	// Perform the query
    	$mysqli->query($templine) or error('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');
    	// Reset temp variable to empty
    	$templine = '';
	}
}



// save config to db
$api_key = $install_data['api_key'];
$language = $install_data['language'];

writeConfig("url_xi_dir", $url2working_dir, 0, $url2working_dir, "You need https://!");
writeConfig("otk_expires", "60", 0, "60", "in seconds");
writeConfig("session_expires", "3600", 0, "3600", "in seconds");
writeConfig("language", $language, 0, "en", "en: English, de: Deutsch");
writeConfig("installed", "1", 1);
writeConfig("version", $version, 1);
writeConfig("api_key", $api_key, 1);

echo '
		<html>
			<head>
				<title>x2Ident Installation successfull</title>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>
				<h2>Installation finished successfully</h2>
				<p>Start with logging in into the <a href="../admin">admin zone</a> with your user name. Then scan the QR-Code with the Google-Authenticator-App. After that you can login into the keygen zone with your user name und a google authenticator key.</p>
			</body>
		</html>
	';
	die();

function error($message) {
	$install_form = $GLOBALS['install_form'];
	echo '
		<html>
			<head>
				<title>x2Ident Installation failed</title>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1>x2Ident: Installation</h1>
				<h1>Installation failed</h1>
				<h2>'.$message.'</h2>
				<hr></hr>
				<h2>You must have following done before installation:</h2>
				<ul>
					<li>TeamPass (admin zone) installed</li>
					<li>created a MySQL-database (and maybe also a user) for x2Ident</li>
					<li>created an API-key in the admin zone (as admin) with the permissions you want</li>
				</ul>
				'.$install_form.'
			</body>
		</html>
	';
	die();
}

function curPageURL() {
	$pageURL = 'http';
	if(!isset($_SERVER["HTTPS"])) {
		$_SERVER["HTTPS"] = "";
	}
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if (false/*$_SERVER["SERVER_PORT"] != "80"*/) {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function writeConfig($key, $value, $only_admin=0, $default="", $info="") {
	$eintrag = "UPDATE config SET conf_value='$value' WHERE conf_key='$key' ";
	$GLOBALS['mysqli']->query($eintrag);
	//var_dump($GLOBALS);
	if($GLOBALS['mysqli']->affected_rows!=1) {
		$eintrag = "DELETE FROM config WHERE conf_key='$key' ";
		$GLOBALS['mysqli']->query($eintrag);
		$eintrag = "INSERT INTO config (`conf_key`,`conf_value`,`conf_default`,`conf_info`,`only_admin`) VALUES ('$key','$value','$default','$info','$only_admin') ";
		$GLOBALS['mysqli']->query($eintrag);
		//echo $eintrag."|".$GLOBALS['mysqli']->affected_rows;
	}
}

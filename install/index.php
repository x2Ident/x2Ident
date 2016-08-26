<?php

// check if user input finished
if(!isset($_POST['start_install'])) {
	echo '
		<html>
			<body>
				<h1>x2Ident: Install</h1>
			</body>
		</html>
	';
	die();
}

// get url to working directory
$currentURL = curPageURL();
$url2working_dir = str_replace("/install/index.php","/",$currentURL);
$url2working_dir = str_replace("/install/","/",$url2working_dir);
$url2working_dir = str_replace("/install","/",$url2working_dir);

// save config to db
writeConfig("url_xi_dir", $url2working_dir, $url2working_dir, "");
writeConfig("otk_expires", "60", "60", "in seconds");
writeConfig("session_expires", "3600", "3600", "in seconds");
writeConfig("language", "en");
writeConfig("installed", "1");

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function writeConfig($key, $value, $default="", $info="") {
    $sess_id = $_SESSION['sess_id'];
	$eintrag = "UPDATE config SET conf_value='$conf_value' WHERE conf_key='$key' ";
	$GLOBALS['mysqli']->query($eintrag);
	if($GLOBALS['mysqli']->affected_rows!=1) {
		$eintrag = "DELETE FROM config WHERE conf_key='$key' ";
		$GLOBALS['mysqli']->query($eintrag);
		$eintrag = "INSERT INTO config ('conf_key','conf_value','conf_default','conf_info') VALUES ($key,$value,$default,$info) ";
		$GLOBALS['mysqli']->query($eintrag);
	}
}

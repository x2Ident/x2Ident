<?php

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);


if(strlen($_SESSION['user'])<1) {
header("Location: login");
	die('Bitte zuerst <a href="login">einloggen</a>');
}
?>
<html>
<head>
<title>xIdent: Keygen JS demo</title>
<link rel="stylesheet" href="pure-io.css">
<script>


</script>
</head>
<body>
<h1>x2Ident Keygen</h1>
<div id="content">
</div>
</body>
</html>

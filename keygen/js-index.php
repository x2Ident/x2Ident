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
var js_id = "Cu1BOlpysp";
refreshData();
function refreshData() {
	var data = new FormData();
	data.append("js-id",js_id);
	var request = new XMLHttpRequest();
	request.open("POST","refreshData.php");
	request.addEventListener('load', function(event) {
		if ((request.status > 199) && (request.status < 300)) {
		    var antwort = request.responseText;
			var html = "<table  class=\"pure-table\"><thead><tr><th>ID</th><th>Titel</th><th>Website</th><th>Benutzername</th><th>Einmal-Key</th><th>Global</th><th>Läuft ab in</th><th>Letzter Login</th></tr></thead><tbody>";
			var arr1 = antwort.split("|");
			for(i=0; i<arr1.length-1; i++) {
				var arr2 = arr1[i].split(";");
				var zeile = "<tr><td>"+arr2[0]+"</td><td>"+arr2[1]+"</td><td>"+arr2[2]+"</td><td>"+arr2[3]+"</td><td>"+arr2[4]+"</td><td>"+arr2[5]+"</td><td><div class=\"expires\">"+arr2[6]+"</div></td><td>"+arr2[7]+"</td></tr>";
				html = html + zeile;
			}
			console.log(html);
			var content_element = document.getElementById("content");
			content_element.innerHTML = html;
			setTimeout(refreshData,1000);
	    } else {
		    console.warn(request.statusText, request.responseText);
	    }
    });
    request.send(data);
}

</script>
</head>
<body>
<h1>x2Ident Keygen</h1>
<div id="content">
bitte warten...
</div>
</body>
</html>

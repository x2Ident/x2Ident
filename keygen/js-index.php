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
<meta charset="UTF-8"> 
<script>
var arr_expires_time = [];
var js_id = "Cu1BOlpysp";
refreshData(false);
refreshExpires(false);
function refreshData(once) {
	console.log("refresh Data");
	var data = new FormData();
	data.append("js-id",js_id);
	var request = new XMLHttpRequest();
	request.open("POST","refreshData.php");
	request.addEventListener('load', function(event) {
		if ((request.status >= 200) && (request.status < 300)) {
		    var antwort = request.responseText;
			var html = "<table  class=\"pure-table\"><thead><tr><th>ID</th><th>Titel</th><th>Website</th><th>Benutzername</th><th>Einmal-Key</th><th>Global</th><th>Läuft ab in</th><th>Letzter Login</th></tr></thead><tbody>";
			var arr1 = antwort.split("|");
			var arr_expires_time_new = [];
			for(i=0; i<arr1.length-1; i++) {
				var arr2 = arr1[i].split(";");
				var otk_html = "<button></button>";
				var zeile = "<tr><td>"+arr2[0]+"</td><td>"+arr2[1]+"</td><td>"+arr2[2]+"</td><td>"+arr2[3]+"</td><td>"+arr2[4]+"</td><td>"+arr2[5]+"</td><td><div id=\"expires_"+i+"\" class=\"expires\">"+arr2[6]+"</div></td><td>"+arr2[7]+"</td></tr>";
				html = html + zeile;
				var expires_time = arr2[6];
				arr_expires_time_new.push(expires_time);
			}
			//console.log(html);
			var content_element = document.getElementById("content");
			content_element.innerHTML = html;
			arr_expires_time = arr_expires_time_new;
			refreshExpires(true);
			if(!once) {
				setTimeout(refreshData,1000);
			}
	    } else {
		    console.warn(request.statusText, request.responseText);
	    }
    });
    request.send(data);
}
function refreshExpires(once) {
	console.log("refresh Expire");
	for(i=0; i<arr_expires_time.length; i++) {
		var expires_elem = document.getElementById("expires_"+i);
		var expires_time = arr_expires_time[i];
		var timestamp = Math.floor(Date.now() / 1000);
		var counter = expires_time - timestamp;
		if(counter>0) {
			expires_elem.innerHTML = counter + " Sekunden";
			if(counter < 10) {
				expires_elem.style.color = "red";
			}
		}
		else {
			expires_elem.innerHTML = "-";
			refreshData(true);
		}
	}
	if(!once) {
		setTimeout(refreshExpires,200);
	}
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

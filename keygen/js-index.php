<?php

session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//TODO: rename refreshData.php -> jsInterface.php

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
var last_html;
var arr_expires_time = [];
var arr_lastlogin_time = [];
var js_id = "hvdrjqY9Cs";
fetchData(false);
refreshData(false);

function fetchData(once) {
	var data = new FormData();
	data.append("js-id",js_id);
	var request = new XMLHttpRequest();
	request.open("POST","jsInterface.php");
	request.addEventListener('load', function(event) {
		if ((request.status >= 200) && (request.status < 300)) {
		    var antwort = request.responseText;
			var html = "<table  class=\"pure-table\"><thead><tr><th>ID</th><th>Titel</th><th>Website</th><th>Benutzername</th><th>Einmal-Key</th><th>Global</th><th>Läuft ab in</th><th>Letzter Login</th></tr></thead><tbody>";
			var arr1 = antwort.split("|");
			var arr_expires_time_new = [];
			var arr_lastlogin_time_new = [];
			for(i=0; i<arr1.length-1; i++) {
				var arr2 = arr1[i].split(";");
				var otk_html = "<button onclick=\"createOTK("+i+")\">Key erstellen</button>";
				var otk_value = arr2[4];
				var otk_string = otk_value+"";
				if(otk_string.length>1) {
					otk_html = "<input value=\""+otk_string+"\"></input><button onclick=\"removeOTK("+i+")\">Key löschen</button>";
				}
				var global_html = "<input type=\"checkbox\">";
				var global_value = arr2[5];
				if(global_value==1) {
					global_html = "<input type=\"checkbox\" checked>";
				}
				var expires_time = arr2[6];
				arr_expires_time_new.push(expires_time);
				var lastlogin_time = arr2[7];
				arr_lastlogin_time_new.push(lastlogin_time);
				var zeile = "<tr><td>"+arr2[0]+"</td><td>"+arr2[1]+"</td><td>"+arr2[2]+"</td><td>"+arr2[3]+"</td><td>"+otk_html+"</td><td>"+global_html+"</td><td><div id=\"expires_"+i+"\" class=\"expires\">"+""+"</div></td><td><div id=\"lastlogin_"+i+"\" class=\"lastlogin\">"+""+"</div></td></tr>";
				html = html + zeile;
			}
			var content_element = document.getElementById("content");
			if(html.localeCompare(last_html)==0) {
				//console.log("gleich");
			}
			else {
				content_element.innerHTML = html;
			}
			last_html = html;
			arr_expires_time = arr_expires_time_new;
			arr_lastlogin_time = arr_lastlogin_time_new;

			refreshData(true);
			if(!once) {
				//console.log("set timeout fetchData");
				setTimeout(fetchData,1000);
			}
	    } else {
		    console.warn(request.statusText, request.responseText);
	    }
    });
    request.send(data);
}

function createOTK(OTK_id) {
	var data = new FormData();
	data.append("js-id",js_id);
	data.append("createOTK-id",OTK_id);
	var request = new XMLHttpRequest();
	request.open("POST","jsInterface.php");
	request.addEventListener('load', function(event) {
		if ((request.status >= 200) && (request.status < 300)) {
		    var antwort = request.responseText;
			//TODO: if error -> alert
			
	    } else {
		    console.warn(request.statusText, request.responseText);
	    }
    });
    request.send(data);
}

function refreshData(once) {
	refreshExpires(true);
	refreshLastlogin(true);
	if(!once) {
		setTimeout(refreshData,200);
	}
}

function refreshLastlogin(once) {
	for(i=0; i<arr_lastlogin_time.length; i++) {
		var lastlogin_elem = document.getElementById("lastlogin_"+i);
		var lastlogin_time = arr_lastlogin_time[i];
		var timestamp = Math.floor(Date.now() / 1000);
		var diff = timestamp - lastlogin_time;
		var diff_html = getTimeHTML(diff);
		lastlogin_elem.innerHTML = diff_html;
	}
	if(!once) {
		setTimeout(refreshLastlogin,200);
	}
}
function refreshExpires(once) {
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
			if(counter == 0) {
				fetchData(true);
			}
		}
	}
	if(!once) {
		setTimeout(refreshExpires,200);
	}
}
function getTimeHTML(time) {
	//möglw. Bug
	var time_text = "vor "+time+" Sekunde(n)";
	if(time>=60) {
		time = Math.floor(time/60);
		time_text = "vor "+time+" Minute(n)";

		if(time>=60) {
			time = Math.floor(time/60);
			time_text = "vor "+time+" Stunde(n)";

			if(time>=24) {
				time = Math.floor(time/24);
				time_text = "vor "+time+" Tag(en)";

				if(time>=30) {
					time = Math.floor(time/30);
					time_text = "vor "+time+" Monat(en)";
				}
			}
		}
	}
	//console.log(time_text);
	return time_text;
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

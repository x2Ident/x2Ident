var last_html;
var session_countdown = -1;
var arr_expires_time = [];
var arr_lastlogin_time = [];
var arr_pwid = [];
fetchData(false);
refreshData(false);

function fetchData(once) {
	var data = new FormData();
	data.append("js-id",js_id);
	var request = new XMLHttpRequest();
	request.open("POST","jsInterface.php");
	request.addEventListener('load', function(event) {
		if ((request.status >= 200) && (request.status < 300)) {
			var content_element = document.getElementById("content");
		    var antwort = request.responseText;
			var html = "<table  class=\"pure-table\"><thead><tr><th>Titel</th><th>Website</th><th>Benutzername</th><th>Einmal-Key</th><th>Global</th><th>Läuft ab in</th><th>Letzter Login</th></tr></thead><tbody>";
			var arr1 = antwort.split("|");
			session_countdown = arr1[arr1.length-2];
			if(arr1[0].includes("[xi]_jsif")) {
				console.log("debug1");
				content_element.innerHTML = arr1[1];
				var current_url = window.location;
				var new_url = current_url + "/../login/";
				window.location.replace(new_url);
				setTimeout(fetchData,1000);
				return;
			}
			var arr_expires_time_new = [];
			var arr_lastlogin_time_new = [];
			var arr_pwid_new = [];
			for(i=0; i<arr1.length-2; i++) {

				var arr2 = arr1[i].split(";");

				var pwid = arr2[0];
				arr_pwid_new.push(pwid);

				var otk_html = "<button onclick=\"createOTK("+pwid+")\">Key erstellen</button>";
				var otk_value = arr2[4];
				var otk_string = otk_value+"";
				if(otk_string.length>1) {
					otk_html = "<input class=\"otk_input\" value=\""+otk_string+"\" readonly></input><button onclick=\"removeOTK("+pwid+")\">Löschen</button>";
				}
				var global_html = "<input type=\"checkbox\" onclick=\"set_global(this,"+pwid+")\">";
				var global_value = arr2[5];
				if(global_value==1) {
					global_html = "<input type=\"checkbox\" onclick=\"set_global(this,"+pwid+")\" checked>";
				}
				if(global_value==2) {
					global_html = "-";
				}
				var website_url = arr2[2]
				var website_html = "<a href=\""+website_url+"\"  target=\"_blank\">"+website_url+"</a>";
				
				var expires_time = arr2[6];
				arr_expires_time_new.push(expires_time);
				var lastlogin_time = arr2[7];
				arr_lastlogin_time_new.push(lastlogin_time);
				var zeile = "<tr><td>"+arr2[1]+"</td><td>"+website_html+"</td><td>"+arr2[3]+"</td><td>"+otk_html+"</td><td>"+global_html+"</td><td><div id=\"expires_"+i+"\" class=\"expires\">"+""+"</div></td><td><div id=\"lastlogin_"+i+"\" class=\"lastlogin\">"+""+"</div></td></tr>";
				html = html + zeile;
			}
			if(html.localeCompare(last_html)==0) {
				//console.log("gleich");
			}
			else {
				content_element.innerHTML = html;
			}
			last_html = html;
			arr_expires_time = arr_expires_time_new;
			arr_lastlogin_time = arr_lastlogin_time_new;
			arr_pwid = arr_pwid_new;

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

function removeOTK(OTK_id) {
	var data = new FormData();
	data.append("js-id",js_id);
	data.append("removeOTK-id",OTK_id);
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

function set_global(checkbox,pwid) {
	var global_state = 0;
	if (checkbox.checked) {
		global_state = 1;
	}
	var data = new FormData();
	data.append("js-id",js_id);
	data.append("set_global",global_state);
	data.append("global_otk_id",pwid);
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
	refreshSessionCountdown(true);
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
		var diff_html = "vor "+getTimeHTML(diff);
		if(lastlogin_time==0) {
			diff_html = "noch nie";
		}
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

function refreshSessionCountdown(once) {
	var session_countdown_elem = document.getElementById("session_countdown");
	var session_countdown_html = "Session noch "+getTimeHTML(session_countdown)+" aktiv";
	if(session_countdown<0) {
		session_countdown_html = "";
	}
	if(session_countdown < 300) { //wenn nur noch 5 Minuten verbleiben rote Schriftfarbe
		session_countdown_elem.style = "color: red";
	}
	session_countdown_elem.innerHTML = session_countdown_html;
	if(!once) {
		setTimeout(refreshSessionCountdown,200);
	}
}

function getTimeHTML(time) {
	//möglw. Bug
	var time_text = ""+time+" Sekunde(n)";
	if(time>=60) {
		time = Math.floor(time/60);
		time_text = ""+time+" Minute(n)";

		if(time>=60) {
			time = Math.floor(time/60);
			time_text = ""+time+" Stunde(n)";

			if(time>=24) {
				time = Math.floor(time/24);
				time_text = ""+time+" Tag(en)";

				if(time>=30) {
					time = Math.floor(time/30);
					time_text = ""+time+" Monat(en)";
				}
			}
		}
	}
	//console.log(time_text);
	return time_text;
}

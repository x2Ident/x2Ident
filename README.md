# x2Ident
x2Ident is the worlds first login technique, which protects users on every website in the internet against keylogger, clipboard-spoofing and unauthorized man-in-the-middle-attacks.

## Warning
Until the first release this repository will be experimental. So we do not recommend to use x2Ident in production in this time.

## Installation
* (install Apache2, mysql, php)
* clone repository
* change urls in proxy/* (they should point to your x2Ident directory on your server)
* create a user in mysql (e.g. x2ident) and a database (e.g. x2ident)
* import database structure from "install/x2ident_db_schema.sql"
* change db credentials in proxy/* and keygen/*
* open admin/index.php in your browser and follow the instructions
* cd mitmproxy
* run ./dev.sh
* activate venv by ". venv/bin/activate" and install mysqldb for python
* deactivate venv
* start the proxy server by "mitmproxy -s proxy/x2ident_replace.py -q"

we are working on an install script ;-)

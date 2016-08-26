# x2Ident
x2Ident is the worlds first login technique, which protects users on every website in the internet against keylogger, clipboard-spoofing and unauthorized man-in-the-middle-attacks.

It powers a proxy server which replaces generated one-time-keys with your real passwords.

## Installation
* (install Apache2, mysql, php, python, pip, virtualenv)
* clone repository
* change urls in proxy/* (they should point to your x2Ident directory on your server)
* create a user in mysql (e.g. x2ident) and a database (e.g. x2ident)
* add a row to the table "config" in your database with the conf_key "url_xi_dir", put the url to your 
* import database structure from "install/x2ident_db_schema.sql"
* change db credentials in proxy/config.py and keygen/inc/config.php
* open admin/index.php in your browser and follow the instructions
* cd mitmproxy
* run ./dev.sh
* activate venv by ". venv/bin/activate" and install mysqldb for python
* deactivate venv
* Download the Google Authenticator App (or an compatible) on your smartphone

### Start the proxy server
* start the proxy server by "mitmproxy -s proxy/x2ident_replace.py -q --anticache"
* wait until message "proxy started"
* we recommend you to use "screen" for running the proxy

### Security
* we recommend you to make the proxy/* files, the mitmproxy/* files not acce

we are working on an install script ;-)

## Tutorial
* First you must add your passwords to the admin zone. (we recommend you to set url)
* Scan the QR code with the Google Authenticator App
* Setup your browser to use the proxy
* Go to "mitm.it" in your browser and install the certificate (if you want to know why, check the mitmproxy repository)
* Login into the keygen zone with your Google Authenticator App
* Generate one time key
* set global if you want to use the one time key an another url as displayed (another subdomain e.g. www.example.com instead of example.com is also a different url); that is due to security reasons. See issue #17
* Login with your username and your one time key on the website

# x2Ident
x2Ident is the worlds first login technique, which protects users on every website in the internet against keylogger, clipboard-spoofing and unauthorized man-in-the-middle-attacks.

It powers a proxy server which replaces generated one-time-keys with your real passwords.

See the wiki for a documentation.

## Installation
* (install Apache2, mysql, php, python, pip, virtualenv)
* clone repository
* change urls in proxy/* (they should point to your x2Ident directory on your server)
* create a user in mysql (e.g. x2ident) and a database (e.g. x2ident)
* import database structure from "install/x2ident_db_schema.sql"
* change db credentials in proxy/config.py and keygen/inc/config.php
* open admin/index.php in your browser and follow the instructions
* create users and an API Key in the admin zone (TeamPass)
* give the API root permissions
* add a row to the table "config" in your x2Ident database with the conf_key "url_xi_dir", put the url to your x2Ident folder in conf_value
* add a row to the table "config" in your x2Ident database with the conf_key "api_key", put your API Key in conf_value
* add a row to the table "config" in your x2Ident database with the conf_key "api_key", put your API Key in conf_value
* add a row to the table "config" in your x2Ident database with the conf_key "otk_expires", put "60" in conf_value
* add a row to the table "config" in your x2Ident database with the conf_key "session_expires", put "3600" in conf_value
* manage conf_default and conf_info on your one
* cd mitmproxy
* run ./dev.sh
* activate venv by ". venv/bin/activate" and install mysqldb for python
* deactivate venv
* Download the Google Authenticator App (or an compatible) on your smartphone

### Start the proxy server
* start the proxy server by ./proxy.sh
* wait until message "proxy started"
* we recommend you to use "screen" for running the proxy

### Security
* we recommend you to make the proxy/* files, the mitmproxy/* files not accesable from the web

we are working on an install script ;-)

## Tutorial
* First you must add your passwords to the admin zone. (we recommend you to set url)
* Scan the QR code with the Google Authenticator App
* Setup your browser to use the proxy
* Go to "mitm.it" in your browser and install the certificate (if you want to know why, check the mitmproxy repository)
* Login into the keygen zone with your Google Authenticator App
* Generate one time key
* set global if you want to use the one time key an another url as displayed (another subdomain e.g. www.example.com instead of example.com means also a different url; x2Ident checks, wether the url begins with the pattern, but ignores the protocol); that is due to security reasons. See issue #17
* Login with your username and your one time key on the website

## Contribute
* Feel free to share your feedback, code etc. with us
* Happy coding!

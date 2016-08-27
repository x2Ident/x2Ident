# x2Ident
x2Ident is the worlds first login technique, which protects users on every website in the internet against keylogger, clipboard-spoofing and unauthorized man-in-the-middle-attacks.

It powers a proxy server which replaces generated one-time-keys with your real passwords.

See the wiki for documentation.

## Demo
Try the x2Ident service on our server!   
Please keep in mind, that everyone has access to the passwords and data you saved at the demo service, if you don't delete them.
* Proxy IP-Address: `85.214.222.8`
* Proxy Port: `8080` (use the same for every protocol)
* User: `demo`
* Password: `demo`

[x2Ident demo](https://noscio.eu/x2Ident_demo)

## Installation

### Basics
* install Apache2, mysql, php, python, pip, virtualenv
* clone repository

### Database
* create a user for x2Ident in mysql (e.g. x2ident) and a database (e.g. x2ident)
* import database structure from `install/x2ident_db_schema.sql`
* create a user for TeamPass (admin zone) in mysql (e.g. x2ident_teampass) and a database (e.g. x2ident_teampass)

### TeamPass (admin zone)
* open `admin/index.php` in your browser and follow the instructions
* create users and an API Key in the admin zone (TeamPass), don't forget to enable the API
* give the API permissions you want (we recommend read access to any users' folder, who want to use x2Ident)

### Web interface
* open `/install/index.php` in your browser and follow the instructions

### Proxy Server (mitmproxy)
* `cd mitmproxy`
* run `./dev.sh`
* activate virtualenv by `. venv/bin/activate` and install mysqldb for python
* deactivate virtualenv

### Google Authenticator
* Download the Google Authenticator App (or an compatible) on your smartphone

### Start the proxy server
* start the proxy server by `./proxy.sh`
* wait until message `proxy started`
* we recommend you to use `screen` for running the proxy

### Security
* we recommend you to make the `proxy/*` files and the `mitmproxy/*` files not accesable from the web

we are working on an easier way to install x2Ident ;)

## Tutorial
* First you must add your passwords to the admin zone. (we recommend you to set url, see #38)
* Scan the QR code with the Google Authenticator App
* Setup your browser to use the proxy
* Go to `mitm.it` in your browser and install the certificate (if you want to know why, check the mitmproxy repository)
* Login into the keygen zone with your Google Authenticator App
* Generate one time key
* set global if you want to use the one time key an another url as displayed (another subdomain e.g. www.example.com instead of example.com means also a different url; x2Ident checks, wether the url begins with the pattern, but ignores the protocol); that is due to security reasons. See issue #17
* Login with your username and your one time key on the website

## Contribute
* Feel free to share your feedback, code etc. with us
* What about installing x2Ident as demo on your server?
* What about sharing your hardware as a testing platform with us?
* Happy coding!

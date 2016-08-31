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

[Browser Setup](https://github.com/x2Ident/x2Ident/wiki/Browser-Setup)   
[x2Ident demo](https://noscio.eu/x2Ident/demo)

## Editions
x2Ident is available in 4/(6) Editions.

* #### [x2Ident](https://github.com/x2Ident/x2Ident)
Includes the full sourcecode.   
Use it if you have nor TeamPass neither mitmproxy installed.   
Use it if you want an easy installation.   

* #### [x2Ident_woTP](https://github.com/x2Ident/x2Ident_woTP)
Includes the full sourcecode without TeamPass.   
Use it, if you want to use x2Ident with an existing TeamPass installation.   
You have to change the link at the x2Ident home page to the admin area.   
You have to replace the `index.php` of your existing installation with the `/admin/index.php` of x2Ident.   
If you use an old TeamPass version, which does not include [our Pull Request #1455 on the TeamPass repository](https://github.com/nilsteampassnet/TeamPass/pull/1455) (version 2.1.26), you need to replace `/api/functions.php` of your existing TeamPass installtion with `/admin/api/functions.php` of x2Ident.

* #### [x2Ident_woMP](https://github.com/x2Ident/x2Ident_woMP)
Includes the full sourcecode without mitmproxy.   
Use it, if you want to use x2Ident with an existing mitmproxy installation.   
You have to install MySqlDB for python in your mitmproxy venv.   
You have to change the directory in `proxy.sh`.   

* #### [x2Ident_standalone](https://github.com/x2Ident/x2Ident_standalone)
Includes the full sourcecode without TeamPass and mitmproxy.   
Use it, if you want to use x2Ident with an existing TeamPass installation and an existing mitmproxy installation.   
(Or if you want to update x2Ident without reinstalling TeamPass and mitmproxy).   
You have to install MySqlDB for python in your mitmproxy venv.   
You have to change the directory in `proxy.sh`.   
You have to change the link at the x2Ident home page to the admin area.   
You have to replace the `index.php` of your existing TeamPass installation with the `/admin/index.php` of x2Ident.   
If you use an old TeamPass version, which does not include [our Pull Request #1455 the TeamPass repository](https://github.com/nilsteampassnet/TeamPass/pull/1455) (version 2.1.26), you need to replace `/api/functions.php` of your existing TeamPass installtion with `/admin/api/functions.php` of x2Ident.

### Development Editions

* #### [x2Ident_test](https://github.com/x2Ident/x2Ident_test)
Includes the latest sourcecode and an fully installed admin area.   
Simply clone and test (if you have mitmproxy installed).   
You have to run `/install` before using.   
Proposed for testers and contributors.   
  ##### Credentials:
  * TeamPass admin password: jugendhackt
  * TeamPass MySQL user: teampass_xi_test   
  * TeamPass MySQL database: teampass_xi_test
  * TeamPass MySQL password: jugendhackt
  * TeamPass MySQL host: localhost
  * TeamPass salt key: KEn87HF29HwHp2tv
  * x2Ident MySQL user: x2Ident_test   
  * x2Ident MySQL database: x2Ident_test
  * x2Ident MySQL password: jugendhackt
  * x2Ident MySQL host: localhost#

* #### [x2Ident_ide](https://github.com/x2Ident/x2Ident_ide)
Includes tools and scripts to easily develop x2Ident.   
Proposed for contributors.   


## Installation

### Basics
* install Apache2, mysql, php, python, pip, virtualenv
* clone repository

### Database
* create a user for x2Ident in mysql (e.g. x2ident) and a database (e.g. x2ident)
* create a user for TeamPass (admin zone) in mysql (e.g. x2ident_teampass) and a database (e.g. x2ident_teampass)

### TeamPass (admin zone)
* open `/admin` in your browser and follow the instructions
* create users and an API Key in the admin zone (TeamPass), don't forget to enable the API
* give the API permissions you want (we recommend read access to any users' folder, who want to use x2Ident)

### Web interface
* open `/install` in your browser and follow the instructions

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
* First you must add your passwords to the admin zone. (we recommend you to set url)
* Scan the QR code with the Google Authenticator App
* Setup your browser to use the proxy
* Go to `mitm.it` in your browser and install the certificate (if you want to know why, check the mitmproxy repository)
* Login into the keygen zone with your Google Authenticator App
* Generate one time key
* set global if you want to use the one time key an another url as displayed (another subdomain e.g. www.example.com instead of [example.com](example.com) means also a different url; x2Ident checks, wether the url begins with the pattern, but ignores the protocol); that is due to security reasons. See [issue #17](https://github.com/x2Ident/x2Ident/issues/17)
* Login with your username and your one time key on the website

## Contribute
* Feel free to share your feedback, code etc. with us
* What about installing x2Ident as demo on your server?
* What about sharing your hardware as a testing platform with us?
* Happy coding!

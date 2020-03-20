Installation:

1. 	Download Wamp server 3.2.0. and install it ( http://www.wampserver.com/en/)

- WampServer includes Apache 2.4.41, MySQL 5.7.28, MariaDB 10.4.10, PHP 7.3.13. and phpMyAdmin 4.9.2.
- www directory in WampServer is automatically created (usually c:\wamp\www)
- run/start WampServer from Start menu

2. 	Import Database
- click on wampserver icon on taskbar and select phpMyAdmin to open it in browser. 
- Login to MariaDB in open browser with parameters from .env file. (username:root and password:)
- Select Import tab and import railway.sql from repository

2.	Download Composer and install it globally on computer (https://getcomposer.org/download/)

3.	Download project from repository and unzip to c:\wamp\www\Railway\
 (c:\wamp\www\Railway\public\index.php)
 
4. terminal
- cd to folder where is composer.json
- composer install
 
5. Open in Browser http://localhost/railway/public/index.php





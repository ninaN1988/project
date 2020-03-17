Installation:

1. 	Download Wamp server and install it ( http://www.wampserver.com/en/)

- WampServer includes Apache, MySQL and PHP
- www directory in WampServer is automatically created (usually c:\wamp\www)
- run/start WampServer from Start menu

2.	Download Composer and install it globally on computer (https://getcomposer.org/download/)

3.	Steps for Symfony installation –

- open cmd (Terminal) from Start menu and change directory in cmd with 'cd c:\wamp\www\' press enter
- now you can automatically create project using command in cmd:
	'composer create-project symfony/web-site skeleton Railway' press enter
after this action Symfony project will be created in folder c:\wamp\www\Railway

4.	Set up project

Please follow it step by step

- change directory for the project in cmd 'cd c:\wamp\www\Railway' press enter

- update DATABASE_URL (user, password, host, port, db name) in file 'railways/.env'

DB_USER=root
DB_PASSWORD=
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=railway
DATABASE_URL=mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}?serverVersion=5.7

Entity
- Make empty entity City, Schedule and Nodes in cmd with command:
	'php bin/console make:entity City' press enter
	'php bin/console make:entity Schedule' press enter
	'php bin/console make:entity Nodes' press enter

- 'Railway/src/Entity/City.php', 'Railway/src/Repository/CityRepository.php', 
'Railway/src/Entity/Schedule.php' and 'Railway/src/Repository/ScheduleRepository.php', 
'Railway/src/Entity/Nodes.php', 'Railway/src/Repository/NodesRepository.php' are created automatically
Please rewrite them from this github repository in your created project. I explained this in READ.me

- Create database and tables by using commands in cmd: 
	'php bin/console doctrine:database:create' press enter
	'php bin/console doctrine:schema:update --force' press enter

phpMyAdmin
- click on wampserver icon on taskbar and select phpMyAdmin to open it in browser. 
- Login to MariaDB in open browser with parameters that you set in .env file. 
- Select railway db on the left side, Click SQL and run:
INSERT INTO `city` (`id`, `name`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'E'),
(6, 'F'),
(7, 'G');

INSERT INTO `schedule` (`id`, `city_start_id`, `city_end_id`, `time_start`, `time_end`, `status`, `distance`) VALUES
(1, 1, 2, '08:00:00', '09:00:00', 'unvisited', 1),
(2, 2, 3, '12:00:00', '14:00:00', 'unvisited', 2),
(3, 1, 3, '09:00:00', '10:00:00', 'unvisited', 3),
(4, 3, 2, '13:00:00', '15:00:00', 'unvisited', 7),
(5, 2, 4, '14:00:00', '16:00:00', 'unvisited', 6),
(6, 4, 5, '12:00:00', '13:00:00', 'unvisited', 3),
(7, 4, 3, '11:00:00', '15:00:00', 'unvisited', 7),
(8, 6, 3, '16:00:00', '19:00:00', 'unvisited', 7),
(9, 4, 6, '08:00:00', '11:00:00', 'unvisited', 7),
(10, 7, 3, '06:00:00', '09:00:00', 'unvisited', 8),
(11, 1, 7, '09:00:00', '12:00:00', 'unvisited', 3),
(12, 1, 2, '04:00:00', '05:00:00', 'unvisited', 5),
(13, 2, 3, '14:00:00', '16:00:00', 'unvisited', 7),
(14, 1, 6, '09:00:00', '10:00:00', 'unvisited', 3),
(15, 3, 2, '12:00:00', '15:00:00', 'unvisited', 5),
(16, 2, 4, '06:00:00', '07:00:00', 'unvisited', 7),
(17, 4, 5, '12:00:00', '13:00:00', 'unvisited', 10),
(18, 4, 3, '08:00:00', '10:00:00', 'unvisited', 8),
(19, 6, 3, '16:00:00', '19:00:00', 'unvisited', 1),
(20, 4, 6, '08:00:00', '11:00:00', 'unvisited', 2),
(21, 7, 3, '08:00:00', '12:00:00', 'unvisited', 6),
(22, 1, 7, '05:00:00', '07:00:00', 'unvisited', 1),
(23, 6, 4, '11:00:00', '12:00:00', 'unvisited', 4),
(24, 4, 2, '13:00:00', '14:00:00', 'unvisited', 5);

Controllers 
– create ScheduleController in cmd by using command:
	'php bin/console make:controller ScheduleController' press enter
-Project has 2 Controllers 
	- ScheduleController for our index() action with view response.
	- ServicesController - as service - actions(method) with only json response. 
We didn't create ServiceController automatically. 
If you create it by using cmd, just delete created folder templates/services, because our controller returns json format, not view response. 
Rewrite created files from github repository - 'src/Controller/ScheduleController.php' and 'Railway\templates\schedule\index.html.twig';
Copy 'src/Controller/ServicesController.php' from github repository to your project.

Form 
– create TimeTableType form by using cmd with command:
	'php bin/console make:form' press enter
	set name as TimeTableType 
	rewrite created TimeTableType.php from github repository (Railway/src/Form/TimeTableType.php).

Also rewrite these files from repository and that is all.
	config/packages/twig.yaml
	templates/base.html.twig

So finally, this is list of important files in our symfony web-site skeleton project:
1.	railway/.env
2.	railway/config/packages/twig.html.yaml
3.	railway/src/Controller/ScheduleController.php
4.	railway/src/Controller/ServicesController.php
5.	railway/src/Entity/City.php
6.	railway/src/Entity/Schedule.php
7.	railway/src/Form/TimeTableType.php
8.	railway/src/Repository/CityRepository.php
9.	railway/src/Repository/ScheduleRepository.php
10.	railway/templates/base.html.twig
11.	railway/templates/schedule/index.html.twig

GitHub:
1. Create user in https://github.com/
2. Create repository in github

3. Download git https://git-scm.com/download/win

4. Open Git Bash from Windows Start menu and use commands in this order:

cd /d/wamp2/www/railway

git init

git add README.md

git config --global user.email yourgithumailregistration

git config --global user.name youusernamefromgithubregistration

git commit -m "first commit"

git remote add origin remote https://github.com/ninaN1988/project.git

git push origin master --force




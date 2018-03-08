# Trip Attendance System

Project: Trip Attendance System
Code Version: 1.5
Author: Benjamin Sommer
GitHub: https://github.com/remmosnimajneb
Theme Design by: HTML5 UP (HTML5UP.NET) - Theme `Identity`
Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)

## Table of Contents:
1. Overview
2. Requirements & Install Instructions
3. Program Explanation (Computer Stupid Explanation)
4. Code Explanation
5. Files Explanation
6. Updates to come

## SECTION 1 - OVERVIEW

We've all been to the petting zoo field trip in 4th grade right? Well ever been in the parent/teacher role for this? It's such a pain to take attendance, make sure all the kids made it back on the busses, and when you have 2, 3, or more busses it can become a 30 minute wait.....well no more of that!

This program was built for those situations in mind! It's a simple PHP/MySQL program that allows you to take attendance on your mobile phone and Syncs Live (!) with all other clients so you can see who's here and who's not in seconds.

It's a simple as that. So now each teacher pulls the webpage up on his/her phone, signs in all the kids present on the bus, while syncing with all other busses and within minutes you get a full list of all kids on and not on busses, and which bus there on!

This program is 100% open source, feel free to do anything you want to it! Just make sure to remember to give me some credit and make sure to ShareAlike! (For the full licence and fine text stuff see creativecommons.org/licenses/by-sa/4.0/).

Also while speaking about giving credit, the HTML theme comes from html5up.net made by @ajlkn (twitter.com/ajlkn). This guy makes siiiiick stuff, make sure to check him out at html5up.net (Free HTML5 Stunning Mobile Friendly Website Templates (Free!)), carrd.co (An Incredible website builder that looks amazing and works even better!) and his Twitter page (@ajlkn).


## SECTION 2 - REQUIRMENTS & INSTALL INSTRUCTIONS
	
Requirments:

- A web server, that can be accessed over the internet for use out of Local Area Network
- MySQL with PDO type PHP Extention (!Important!)
- PHP
- That's it

Aight, let's go! Let's install this thing already!!

Install: 

Here's how to install this:
1. Create a new MySQL Database on your server
2. Import or run the SQL commands to setup the system on the server - (File: SQLInstall.sql)
3. Open the functions file (File: functions.php) in your favorite text editor (h/t to mine Sublime Text 3) and on the MySQL connection line, add your DB Host, DB Name, MySQL Name and MySQL Password
4. Move all the files to your public directory on the server (Can exclude this file and SQLInstall.sql, everything else required)
5. That's it! Open your browser to the directory you stuck this in and use admin, admin as the defualt login

-Tip! Use the Edit Roster button on the Admin panel to add students and the System Users button to add Users

## SECTION 3 - PROGRAM EXPLINATION

Aight, here's a Computer Stupid explanation of this program.

- When you load the front page of this program you will get a list of students and ability to either sign them in (on the bus) or a it will say who's bus they are on and a button to un-sign them in. When you click sign in or out it sends a request to the server telling the server to sign them in/out. The server parses that into the MySQL DB (MySQL Table: `roster`) and then adds it to the list of changes to be sent to clients (MySQL Table: `changes`). Now what happens is every couple seconds each client hits the server and checks if there are new updates. The server checks the last change ID they were sent (MySQL Table: `users`.`lastStudentID`) and sees if there is a newer update in the changes table (MySQL Table: `changes`) and if so it sends it back to the client.

- In the admin panel (File: admin.php) you get all the options to do things:
	- Make a New Trip or New Stop (See below - !Important!)
	- Add/Remove Students once a New Stop has been made
	- Add/Reset Password/Delete/Make-Remove as Admin System Users
	- Edit Roster - Add/Change Name/Delete Individual Students, CSV Upload

- New Trip vs. New Stop: The way this program was built is so that you can have multiple 'stops' per trip and keep the same roster list
	Example: You start the trip at school (System Status: newTrip), your roster has 200 kids but only 180 are coming, you sign in 180 kids on the system and then you can go to the admin panel and click New Stop (System Status: newStop). Now when you end the zoo and are on the way to the carnival the system will only show 180 kids not all 200!

- Hopefully something that I want to implement would to have some kind of 'Save' for rosters, for example, if you go on a trip every Thursday with 50 kids out of 200 total you can have the system remember and show now only those 50 kids!

## SECTION 4 - CODE EXPLANATION

So for all them programmers out here, how does it work?!

Here we go:
1. Index.php - this page parses the MySQL (MySQL Table: `roster`) and outputs all students onto the page:
If it's a New Trip (System Status: newTrip) it shows all records, if they are signed in already show who did so and a sign out button, if not signed in show sign in button
If it's a New Stop (System Status: newStop) it shows all records of students on the trip (MySQL: `roster`.`onTrip` = '1') and if signed in already show who did so and a sign out button, if not signed in show sign in button. All students not on trip (MySQL: `roster`.`onTrip` = '0') it outputs and hides (CSS: 'display:none').

Everytime you click sign in (or out) it uses an AJAX call (File: assets/js/clientFunctions.js) to the server (File: serverUpdater.php) which updates the MySQL (MySQL Table: `roster`) and then adds it to the changes table (MySQL Table: `changes`)

Now if there is a client that reloads or loads new the page he gets the latest updates.

All other clients use another script (File: assets/js/clientFunctions.js) that Long Polls the server (File: clientUpdater.php) waiting for new updates every few seconds. This script checks (code) if(lastStudentID() > lastStaffID()) { //Send Updates! }; (/code) - Basicly the changes table (MySQL Table: `changes`) has an auto incrementing collumn (MySQL: `changes`.`ChangeID`), lastStudentID() (File: functions.php) checks what the last ChangeID was in the MySQL, lastStaffID() (File: functions.php) checks the users table (MySQL: `users`.`lastStudentID`) to find the last ID sent to the user and if the lastStudentID is greater it means some changes never got sent and sends them.

All the updates are parsed client side (File: assets/js/clientFunctions.js) using JQuery/JavaScript

So in simple:
	index.php (sign in/out) ----(VIA clientFunctions.js)----> serverUpdater.php --> MySQL (`roster` & `changes`)

index.php ----(VIA clientFunctions.js)----> clientUpdater.php -> MySQL (`changes` & `users`) -> Return Data to client (index.php)

And yes, technically sending updates and getting them have kinda no direct connection to each other.

## SECTION 5 - FILES EXPLANATION

1. functions.php - Holds MySQL DB Connection info and many Required Functions
2. login.php - login page
3. auth.php - logges user in from login.php, usernames and hashed (MD5) passwords stored in Users (MySQL: `users`) table
4. index.php - main page to sign kids in and out
5. serverUpdater.php - gets updates from index.php and parses them into Roster and Changes MySQL Table
6. clientUpdater.php - sends updates to clients
7. admin.php - Admin Panel for various system options, Needs to be admin (MySQL: `users`.`admin` == "true") to see page (and all linked pages)
	- This page includes all System User Functions (Add/Delete/Reset Password/Make-Remove as Admin)
8. recordUpdater.php - updates MySQL for the various functions listed in admin.php
9. systemUpdate.php - makes newTrip & newStop
10. editRoster.php - Edit roster of kids. Allows for CSV upload
11. editTrip.php - Add/Remove students to trip once newStop declared
12. csvUpload.php - handles CSV roster upload from editRoster.php
13. assets/js/clientFunctions.js - handles signing in, long polling and other client functions
14. assets/js/adminFunctions.js - handles various admin functions (client side)
15. logout.php - log out of system, kills all sessions
16. assets/css/main.css - main CSS file

## SECTION 6 - FUTURE UPDATES LIST
	
- WARNING SUBJECT TO CHANGE/I MIGHT NEVER GET TO THESE!

1. Change all code from JavaScript to JQuery (or visa versa, but let's keep it unified)
2. Add one time guest to the system (for one trip)
3. Edit/Replace Student Image
4. Rename System Users
5. Trip Instances - Allowing a newStop to be effectively spit into more sub-trips

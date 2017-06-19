<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** System Update page
* Update database to make a newTrip and a newStop
*/

//Check $_SESSION['name'] has been made and user is an Admin
session_start();
//If no session was created let's die()...the script obvio.....okay I made enought of these stupid jokes already....
if($_SESSION['name'] == null || $_SESSION['isAdmin'] != "true"){
	die();
};

//Include Functions/Connection to DB File
require 'functions.php';

//Get status to update - newTrip or newStop
$toUpdate = $_POST['status'];

//newTrip update
if($toUpdate == "newTrip"){
	//set onTrip = 0 for all
	$sql = "UPDATE `roster` SET `onTrip` = '0'";
	$stm = $con->prepare($sql);
	$stm->execute();

	//set onStop = 0 for all
	$sql = "UPDATE `roster` SET `onStop` = '0'";
	$stm = $con->prepare($sql);
	$stm->execute();	
	
	//Truncate changes table
	$sql = "TRUNCATE changes";
	$stm = $con->prepare($sql);
	$stm->execute();	
	
	//Set lastStudentID = 0 for all users
	$sql = "UPDATE `users` SET `lastStudentID` = '0'";
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Set systemStatus = newTrip
	$sql = "UPDATE `systemstatus` SET `value` = 'newTrip' WHERE name LIKE 'status'";
	$stm = $con->prepare($sql);
	$stm->execute();	
	
	//Reset all tripStaff names to null
	$sql = ("UPDATE `roster` SET `tripStaff` = ''");
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Reset all stopStaff names to null
	$sql = ("UPDATE `roster` SET `stopStaff` = ''");
	$stm = $con->prepare($sql);
	$stm->execute();

	//Delete all guests from DB
	//Ignore this for now  - This is for Version 2.0 for adding a one time guest to the trip
	$sql = ("DELETE FROM `roster` WHERE `association` = 'guest'");
	$stm = $con->prepare($sql);
	$stm->execute();

	//Insert reload for changes table
	$sql = ("INSERT INTO `changes` (StudentID,action,staffName,system) VALUES ('0','systemUpdate','System','newTrip')");
	$stm = $con->prepare($sql);
	$stm->execute();
	
	//Echo 1 to let know update was successfull
	echo "1";
} else if($toUpdate == "newStop"){ //newStop update
	//set onStop = 0 for all
	$sql = "UPDATE `roster` SET `onStop` = '0'";
	$stm = $con->prepare($sql);
	$stm->execute();
	
	//Truncate changes table
	$sql = "TRUNCATE changes";
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Set lastStudentID = 0 for all users
	$sql = "UPDATE `users` SET `lastStudentID` = '0'";
	$stm = $con->prepare($sql);
	$stm->execute();	

	//set systemStatus to newStop
	$sql = "UPDATE `systemstatus` SET `value` = 'newStop' WHERE name LIKE 'status'";
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Reset all stopStaff names to null
	$sql = ("UPDATE `roster` SET `stopStaff` = ''");
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Insert reload for changes table
	$sql = ("INSERT INTO `changes` (StudentID,action,staffName,system) VALUES ('0','systemUpdate','System','newStop')");
	$stm = $con->prepare($sql);
	$stm->execute();	

	//Echo success status
	echo "1";
};
?>
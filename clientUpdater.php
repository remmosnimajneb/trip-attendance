<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Script to send live updates to all clients
* Check based on SESSION['user'] and send any updated from the changes table
*/

//Create Long-Polling/SSE headers
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


session_start();
//If no session was created let's die()...the script obviously! Not us....we don't want to die!
if($_SESSION['name'] === null){
	die();
};

//Include functions/connection to DB file
include 'functions.php';

//Get lastStaffID - ID of the last update this user was sent & lastStudentID - the newest update in the Database
$lastStaffID = getLastStaffID($_SESSION['name']); 
$lastStudentID = getLastStudentID(); 

//Check, if there is a newer update that this user hasn't gotten
	if($lastStudentID > $lastStaffID){
		//If so, get all updates this user hasn't recived yet
		$sql = ("SELECT * from `changes` WHERE `ChangeID` > '" . $lastStaffID . "'");
		$stm = $con->prepare($sql);
		$stm->execute();
		$data = $stm->fetchAll();
		foreach($data as $row){
			//Send it in format of JSON String: eventType: add, remove, ect; data: {StudentID, staffName, system}
			$string = '{"StudentID": "' . $row['StudentID'] . '", "staffName": "' . $row['staffName'] . '", "system": "' . $row['system'] . '"}';		
			echo "event: " . $row['action'] . "\n";
			echo "data: {$string}\n\n";
			flush();
	    };
	    //Then update the DB that we've updated to the latest updates!
	    updateDB($_SESSION['name']);
	};
?> 
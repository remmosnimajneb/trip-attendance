<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Server Update Page
* This page is where sign in requests from index.php get sent to be parsed. 
* All return information gets sent back to clients VIA the client updater long polling script.
* If update success send back a response of '1' else if error send back '0'
*/

//Create variables
session_start();
$staffName = $_SESSION['name'];

//If no session was created let's die()...the script....not us :)
if($_SESSION['name'] === null){
	echo $StudentID . '0';
	die();
};

//AJAX data
$StudentID = $_POST['StudentID'];
$status = $_POST['systemStatus'];
$action = $_POST['action'];

//Include PHP page to connect to Database
require 'functions.php';

//If we are doing a manual add (like for Add/Remove Student from trip) run the function and then stop the script
if($status == "manualAdd"){
	manualAdd($StudentID, $action, $staffName);
	die();
};

//Else it's not a manual add, go on to a normal student sign in!

//Set lastStudentID for StudentID
$lastStudentID;
//Set variable for success of update (needs to be set globally)
$returnCode;
//If we are adding a student to the DB (Signing someone in) or un-signing them in
$changesName;
$sql = ("SELECT * FROM `roster` WHERE StudentID LIKE '" . $StudentID . "'");
$stm = $con->prepare($sql);
$stm->execute();
$data = $stm->fetchAll();
	foreach($data as $row){
		//If he is already signed in then we don't need to update - this checks all cases of where he already is in the system
		if(($action == "add" && $status == "newTrip" && $row['onTrip'] == 1) || ($action == "add" && $status == "newStop" && $row['onStop'] == 1) || ($action == "remove" && $status == "newTrip" && $row['onTrip'] == 0) || ($action == "remove" && $status == "newStop" && $row['onStop'] == 0)){
			$returnCode = 2;
		} else { //He hasn't been signed in
			//Set some variables and do some basic logic to set all the right strings
			$name;
			$sysName;
			$num;

			if($action == "add"){
				$num = 1;
				$name = $staffName;
				$changesName = $_SESSION['name'];
			} else {
				$num = 0;
				$name = null;
				$changesName = "remove";
			};

			if($status == "newTrip"){
				$sysName = "Trip";
			} else {
				$sysName = "Stop";
			};

			//$sql to update that student is on trip/stop and mark the Staff the signed him in
			$sql = "UPDATE `roster` SET `on" . $sysName . "` = '" . $num . "', `" . $sysName . "Staff` = '" . $name . "' WHERE `StudentID` = '" . $StudentID . "'";

			$stm = $con->prepare($sql);
			$stm->execute();

			if($stm){
				$returnCode = 1;
			} else {
				$returnCode = 0;
			}
		};

		//If $returnCode is a 0 then send back to origin client an error. If $returnCode is 1 then update to changes and send back response code '1' 
		if($returnCode == 0){
			echo $StudentID . ",0," . $system;
		} else if($returnCode == 1){
			$sql = ("INSERT INTO `changes` (StudentID, action, staffName, system) VALUES ('" . $StudentID . "', '" . $action . "', '".$changesName."', '" . $status . "')");
			$stm = $con->prepare($sql);
			$stm->execute();
			echo "1";
		} else {
			//If $returnCode was a '2' - manualAdd - we don't want to update changes, that was done above, but we do want to send a 1 back to client
			echo "1";
		};
	};
?>
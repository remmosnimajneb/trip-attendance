<?php
/********************************
* Project: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/**
*Functions Page - Stores various functions for the system to run
*/


/**
* Declare MySQL connection information. Required for program to work!
*/
$con = new PDO("mysql:host=DB_HOST_NAME;dbname=DB_NAME_HERE", "DB_USERNAME_HERE", "DB_PASSWORD_HERE");


/*==========================SYSTEM FUNCTIONS=============================*/

/**
* Function to get the current status of the system
* Data Sent: None
* Data returned: (String) Either 'newTrip' or 'newStop'
*/
function getSysStatus(){
	$status;
	$sql = ("SELECT * from `systemstatus` WHERE `name` LIKE 'status'");
	$stm = $GLOBALS['con']->prepare($sql);
	$stm->execute();
	$data = $stm->fetchAll();
		foreach($data as $row){
       		$status = $row['value'];
   		};
   	return $status;
};

/**
* Function to get the last StudentID submitted to the system (Sign student In or Out)
* Data Send: None
* Data Returned: (int) Number representing the last ID in the 'Chages' table
*/
function getLastStudentID(){
	$lastID;
	$sql = ("SELECT * FROM `changes` ORDER BY ChangeID DESC LIMIT 1");
	$stm = $GLOBALS['con']->prepare($sql);
	$stm->execute();
	$row_count = $stm->rowCount();
	$row = $stm->fetch();
		//Check there is something in the DB
		if($row_count > 0){
			$lastID = $row['ChangeID'];
		} else {
			$lastID = 0;
		}
		return $lastID;
};

/**
* Function to update DB that a user has been fully updated with updates
* Data Send: (String) Name of User
* Data Returned: None.
* Functions Required: getLastStudentID();
*/
function updateDB($username){
	$lastStudentID = getLastStudentID();
	$sql = "UPDATE `users` SET `lastStudentID` = '" . $lastStudentID . "' WHERE name LIKE '" . $username . "'";
	$stm = $GLOBALS['con']->prepare($sql);
	$stm->execute();
};

/**
* Function to get the last updated ID of a certain user to see if he is updated with the latest update
* Data Send: (String) Name of User
* Data Returned: (int) number representing the last update in the 'Changed' table he recived.
*/
function getLastStaffID($username) { 
	$lastID;
  	$sql = ("SELECT * from `users` WHERE name LIKE '" . $username . "'");
	$stm = $GLOBALS['con']->prepare($sql);
	$stm->execute();
	$users = $stm->fetchAll();
		if($users != false){
			foreach($users as $row){
				$lastID = $row['lastStudentID'];
			};
		};
		return $lastID;
};

/**
* Function to open a CSV file uploaded by user and add records to the database
* Data Send: (File) CSV file with columns (StudentID, firstName, lastName, assocciation)
* Data Returned: Nothing
*/
function parseCSV(){
	$file = fopen("upload.csv","r");
	while(! feof($file))
 	{
 		$array = fgetcsv($file);
 		$sql = "INSERT INTO `roster` (lastName, firstName, studentImage, association) VALUES ('" . $array[0] . "', '" . $array[1] . "','" . $array[2] . "', 'NULL')";
 		$stm = $GLOBALS['con']->prepare($sql);
		$stm->execute();
  	};
	fclose($file);
};

/**
* Function to manually add student to Database (Sign in or out of a trip)
* Data Send: StudentID, action [Add/Remove], staffName
* Data Returned: (int) number representing if the function was successfull.
*/
function manualAdd($StudentID, $action, $staffName){
	
	//$sql1 to update that student is on trip, $sql2 to update that he is NOT on the stop
	//Note that Manual Add HAS to be done in: Adding kid to Trip WHILE newStop declared. Otherwise if it were newTrip so he could just sign him in, if it were newStop and onTrip he could do the same, therefore it MUST be newStop and onTrip = 0
	$sql1;
	
	if($action == "add"){
		$sql1 = "UPDATE `roster` SET `onTrip` = '1', `tripStaff` = '".$staffName."' WHERE `StudentID` = '".$StudentID."'";
	} else if($action == "remove"){
		$sql1 = "UPDATE `roster` SET `onTrip` = '0', `tripStaff` = '' WHERE `StudentID` = '".$StudentID."'";
	};

	$sql2 = "UPDATE `roster` SET `onStop` = '0', `stopStaff` = '' WHERE `StudentID` = '".$StudentID."'";

	$stm1 = $GLOBALS['con']->prepare($sql1);
	$stm1->execute();

	$stm2 = $GLOBALS['con']->prepare($sql2);
	$stm2->execute();

	//If update success add to changes table, send a response '1', otherwise it probably failed and send back '0'
	if($stm1 && $stm2){
		$sql = ("INSERT INTO `changes` (StudentID, action, staffName, system) VALUES ('" . $StudentID . "', '".$action."Record', '".$staffName."', '" . $action . "')");
		$stm = $GLOBALS['con']->prepare($sql);
		$stm->execute();
		echo "1";
	} else {
		echo "0";
	};
};
?>

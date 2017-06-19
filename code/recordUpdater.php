<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Record Updater Page
* Updates records in the DB
* Gets Updates from: admin.php - to add, delete, set isAdmin status and reset password of a system user; editRoster.php - Edit student Name, delete student, add student, empty entire roster;
*/

//Check $_SESSION['name'] has been made and that user is an admin
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=admin.php');
} else if($_SESSION['isAdmin'] != "true"){
	header('Location: index.php');
};

$formType = $_POST['formType'];

include 'functions.php';

$sql;
switch ($formType) {
	case 'addUser': //Add System User
	$sql = "INSERT INTO `users` (name, password, admin, lastStudentID) VALUES ('" . $_POST['username'] . "','" . md5($_POST['password']) . "','" . $_POST['isAdmin'] . "','0')";
		break;
	case 'resetPassword': //Reset System User Password
		$sql = "UPDATE `users` SET `password` = '" . md5($_POST['newPassword']) . "' WHERE `UserID` = '" . $_POST['username'] . "'";
		break;
	case 'changeAdmin': //Set user isAdmin status [True/False]
		$sql = "UPDATE `users` SET `admin` = '" . $_POST['isAdmin'] . "' WHERE `UserID` = '" . $_POST['username'] . "'";
		break;
	case 'deleteUser': //Delete System User
		$sql = "DELETE FROM `users` WHERE `users`.`UserID` = '" . $_POST['username'] . "'";
		break;
	case 'editRecord': // Edit Name of Student
		$sql= "UPDATE `roster` SET `lastName` = '".$_POST['lastName']."', `firstName` = '".$_POST['firstName']."' WHERE `StudentID` = " . $_POST['StudentID'];
		break;
	case 'deleteRecord': //Delete Student
		$sql = "DELETE FROM `roster` WHERE `StudentID` = '" . $_POST['StudentID'] . "'";
		break;
	case 'addRecord': //Add Student to system
		$sql = "INSERT INTO `roster` (StudentID, firstName, lastName) VALUES ('" . $_POST['StudentID'] . "', '" . $_POST['firstName'] . "', '" . $_POST['lastName'] . "')";
		break;
	case 'emptyRecord': //Empty Roster
		$sql = "TRUNCATE `roster`";
		break;

};

$stm = $con->prepare($sql);
$stm->execute();
if($stm){
	//If success and this was an AJAX call, echo '1' else if it was a href call redirect to that page
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'?update=Update Success!');
	};
	echo "1";
} else {
	//If failure and this was an AJAX call, echo '0' else if it was a href call redirect to that page
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'?update=Update Error, Please Try Again.');
	};
	echo "0";
};

?>
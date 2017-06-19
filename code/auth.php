<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/**Set user Session page.
* This allows us to track both Client Updates and who is submitting attendance by a name.
* Authenticate by checking name and password are corresponding to those in DB. Passwords are MD5() hashed
*/

//Start the session() and include Functions Scripts.
session_start();
require 'functions.php';

//Get Username and Password from login form
$username = $_POST['username'];
$password = $_POST['password'];

//Hash the password
$hashed = md5($password);

//Check the password corresponds to that of the username supplied.
$sql = "SELECT admin FROM `users` WHERE `name` = '" . $username . "' AND `password` = '" . $hashed . "'";

$stm = $con->prepare($sql);
$stm->execute();
$row_count = $stm->rowCount();
$result = $stm->fetchColumn();

//If there is a row in the DB with this $username and md5($password)
if($row_count > 0){
	//Set the Session Name to the username
	$_SESSION['name'] = $username;
	//And set a session if he is an admin or not.
	$_SESSION['isAdmin'] = $result;

	//If there was somewhere to redirect to, do so.
	if($_POST['redirect'] != null && $_POST['redirect'] != ""){
		header('Location: ' . $_POST['redirect']);
	} else {
		//Else go to the 'homepage'
		header('Location: index.php');
	};
} else {
	//If he isn't authenticated, send back to the login page.
	$_SESSION['name'] = null;
	$_SESSION['isAdmin'] = null;
	header('Location: login.php?redirect=' . $_POST['redirect']);
};
?>

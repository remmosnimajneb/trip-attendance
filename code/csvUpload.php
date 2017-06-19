<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** CSV Upload page
* Recives a CSV file to upload and parse into the database for student records
*/

//Check $_SESSION['name'] has been made and user is admin
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=editRoster.php');
} else if($_SESSION['isAdmin'] != "true"){
	header('Location: index.php');
};

include 'functions.php';
$valid_file = true;
//if they DID upload a file...
if($_FILES['file']['name'])
{
	//if no errors... && file is a csv file!
	$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	if(!$_FILES['file']['error'] && $ext == "csv")
	{
		//now is the time to modify the future file name and validate the file
		$new_file_name = strtolower($_FILES['file']['tmp_name']); //rename file
		if($_FILES['file']['size'] > (8000)) //can't be larger than 8 KB
		{
			$valid_file = false;
			header('Location: editRoster.php?fileUpload=Error! File Size too large!');
			exit();
		}
		
		//if the file has passed the test
		if($valid_file)
		{
			//move it to where we want it to be (File always is named upload.csv in the root of the folder)
			move_uploaded_file($_FILES['file']['tmp_name'], 'upload.csv');
			//Call the parse CSV Function to parse the file to the database
			parseCSV();
			header('Location: admin.php');
		}
	}
	//if there is an error...
	else
	{
		//set that to be the returned message
		header('Location: editRoster.php?fileUpload=Upload Error!');
	}
}

?>
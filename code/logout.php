<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Logout page
* Kills all sessions and redirects to the login.php page
*/

session_start();
$_SESSION['name'] = null;
$_SESSION['isAdmin'] = null;
session_destroy();
header('Location: login.php#logoutsuccess');
?>
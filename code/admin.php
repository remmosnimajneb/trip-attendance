<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Admin Options Page
* Link to various other pages to do various things.....more or less
* Here is the page to edit the system/trip, on this page directly you can add, edit, delete users, or click a link on the menu to do other options
*/

//Check $_SESSION['name'] has been made, check that user is an Admin and can view this page, if not go back to the index.php page
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=admin.php');
} else if($_SESSION['isAdmin'] != "true"){
	header('Location: index.php');
};
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Trip System | Yeshivat Netiv Aryeh</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />		
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/adminFunctions.js"></script>
	</head>
	<body class="is-loading">
		<div id="wrapper">
			<section id="main" style="margin:auto;">
				<header>
					<h1>Admin Panel</h1>
					<?php
						//If there is a ?update= in the URL, put it here (Will most likely be something like Update Success/Error)
						if(isset($_GET['update'])){
							echo "<p style='color:red;'>" . $_GET['update'] . "</p>";
						};
					?>
					<hr />
				</header>	
				<section id="options">
					<button onclick="systemUpdate(`newTrip`)";>New Trip</button> | <button onclick="systemUpdate(`newStop`)">New Stop</button><br /><br />
					<?php
					require 'functions.php';
					if(getSysStatus() == 'newStop'){
						//Show buttons to add/remove student from trip but only if were are on a newStop - otherwise you can just sign the student in
						echo '<p><b>Add/Remove Students from Trip</b></p>';
						echo '<a href="editTrip.php?action=add"><button>Add</button></a> | <a href="editTrip.php?action=remove"><button>Remove</button></a>';
					};
					?>
					<hr />
					<button onclick="display('sysUsers', 'options')">System Users</button><br /><br />
					<a href="editRoster.php"><button>Edit Roster</button></a>

					<hr />
					<a href="index.php"><button>Home</button></a> | <a href="logout.php"><button>Log Out</button></a>
				</section>
				<section id="sysUsers" style="display:none;">
					<button onclick="display('options', 'sysUsers')">Back</button>
					<hr />
					<p>Add New User</p>
					<form action="recordUpdater.php" method="POST">
						<input type="hidden" name="formType" value="addUser">
						<input type="hidden" name="redirect" value="admin.php">
						<input type="text" name="username" placeholder="User Name" required="required"><br />
						<input type="text" name="password" placeholder="Password" required="required"><br />
						<select name="isAdmin">
							<option value="false">Not an Admin</option>
							<option value="true">Is an Admin</option>
						</select><br />
						<input type="submit" value="Submit">
					</form>
					<hr />
					<p>Reset Password</p>
					<form action="recordUpdater.php" onsubmit="return validate('reset');" method="POST">
						<input type="hidden" name="formType" value="resetPassword">
						<input type="hidden" name="redirect" value="admin.php">
						<select name="username">
							<?php
								$sql = "SELECT * FROM `users`";
								$stm = $con->prepare($sql);
								$stm->execute();
								$data = $stm->fetchAll();
								foreach($data as $row){
									echo "<option value=" . $row['UserID'] . ">" . $row['name'] . "</option>";
								};
							?>
						</select><br />
						<input type="text" id="newPassword" name="newPassword" placeholder="New Password"><br />
						<input type="text" id="newPasswordConfirm" placeholder="Confirm New Password"><br />
						<input type="submit" value="Submit">
					</form>
					<hr />
					<p>Change User Admin Status <br /> <i>Only takes effect until next login!</i></p>
					<form action="recordUpdater.php" method="POST">
						<input type="hidden" name="formType" value="changeAdmin">
						<input type="hidden" name="redirect" value="admin.php">
						<select name="username">
							<?php
								$sql = "SELECT * FROM `users`";
								$stm = $con->prepare($sql);
								$stm->execute();
								$data = $stm->fetchAll();
								foreach($data as $row){
									echo "<option value=" . $row['UserID'] . ">" . $row['name'] . "</option>";
								};
							?>
						</select><br />
						<select name="isAdmin">
							<option value="true">Is an Admin [True]</option>
							<option value="false">Not an Admin [False]</option>
						</select><br />
						<input type="submit" value="Submit">
					</form>
					<hr />
					<p>Delete User</p>
					<form action="recordUpdater.php" onsubmit="return validate('delete');" method="POST">
						<input type="hidden" name="formType" value="deleteUser">
						<input type="hidden" name="redirect" value="admin.php">
						<select name="username">
							<?php
								$sql = "SELECT * FROM `users`";
								$stm = $con->prepare($sql);
								$stm->execute();
								$data = $stm->fetchAll();
								foreach($data as $row){
									echo "<option value=" . $row['UserID'] . ">" . $row['name'] . "</option>";
								};
							?>
						</select><br />
						<input type="text" id="deleteConfirm" placeholder="Type 'DELETE' to Confirm"><br />
						<input type="submit" value="Submit">
					</form>
				</section>
			</section>
			<footer id="footer">
				<ul class="copyright">
					<li>&copy; <a href="https://bensommer.net" target="_blank">Benjamin Sommer, 2017</a></li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
				</ul>
			</footer>
		</div>
		<!--Theme Scripts-->
		<!--[if lte IE 8]><script src="assets/js/respond.min.js"></script><![endif]-->
		<script>
			if ('addEventListener' in window) {
				window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
				document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
			}
		</script>
		<script>
			//Basic validations for the Delete User and Reset User forms
			function validate(type){
				if(type == "delete" && document.getElementById('deleteConfirm').value.toLowerCase() == "delete"){
					return true;
				} else if((type == "reset") && (document.getElementById('newPassword').value == document.getElementById('newPasswordConfirm').value)){
					return true;
				} else {
					alert("Form Error! Please check the information you submitted and try again!");
					return false;
				};
			}
		</script>
	</body>
</html>

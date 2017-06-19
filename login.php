<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

//If user is logged in already redirect to index.php
session_start();
if(isset($_SESSION['name']) != null){
	header('Location: index.php');
};
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Trip Attendance | Yeshivat Netiv Aryeh</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script src="assets/js/jquery.js"></script>
	</head>
	<body class="is-loading">
		<!-- Wrapper -->
			<div id="wrapper">
				<!-- Main -->
					<section id="main" style="margin:auto;padding: 10px;">
						<header>
							<h1>Trip Attendance</h1>
							<p style="color:red;" id="status">PLEASE LOGIN</p>
							<p style="color:red;" id="status_msg"></p>
							<hr />
						</header>
						<form action="auth.php" method="POST">
							<select name="username">
  								<?php
	  								/**Login page for Trip Attendance System
									* List all users from table `users`
									* Upon selection redirect to auth.php
									*/
									//Include functions.php to connect to DB
	  								require 'functions.php';
	  								//Get all users from DB
									$query = "SELECT * FROM `users`";
									$stm = $con->prepare($query);
									$stm->execute();
									$data = $stm->fetchAll();
									
									//Output a selection of users			
									foreach($data as $row){
										echo "<option value='";
										echo $row['name'];
										echo "'>";
										echo $row['name'];
										echo "</option>";
									};
									echo "</select>"; //End Select Tag
  								?>
							<br />
							<input type="password" name="password" placeholder="Password" required="true">
							<input type="hidden" name="redirect" value="<?php /**If we want to go directly to a certain page...Like admin.php, ect.*/ if(isset($_GET['redirect'])){echo $_GET['redirect'];}; ?>"> 
							<br />
							<input type="submit" value="Submit">
  						</form>
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
		<script type="text/javascript">
			//Script to display error messages
			function error(){
				var hash = window.location.hash;
				if(hash == "#logoutsuccess"){
					document.getElementById('status_msg').innerHTML = "Logout Success!";
				} else if(hash == "#loginerror"){
					document.getElementById('status_msg').innerHTML = "Authentication Failure. Please try again!";
				};
			};
			window.onload = error();
		</script>
	</body>
</html>
<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Edit Trip Functions
* Code to add or remove a student to a trip if he comes or leaves in the middle - can only do this if were on a newStop - if it's a newTrip just sign him in like normal!
*/

//Check $_SESSION['name'] has been made, and the user is an admin, and can view page and that $_GET['action'] is add or remove
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=editTrip.php');
} else if($_SESSION['isAdmin'] != "true"){
	header('Location: index.php');
} else if($_GET['action'] != "add" && $_GET['action'] != "remove"){
	header('Location: admin.php');
}
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
		<div id="wrapper">
			<section id="main" style="margin:auto;">
				<header>
					<h1>Edit Trip</h1>
					<a href="index.php"><button>Home</button></a><br />
				</header>
				<div>
					<input type="text" id="recordsSearch" onkeyup="recordsSearch()" placeholder="Search">
				</div><br />
				<!--Table to display student records listings-->
				<table>
					<tr class='border'>
						<td>Picture</td>
						<td>Name</td>
						<td>Sign In</td>
					</tr>
					<tr><!--Spacer for top line and menu listings- Don't Remove!-->
						<td class='spacer'></td>
					</tr>
					<?php
						//Include PHP page to connect to Database
						require 'functions.php';
						//Create SQL query based on $_GET['action']
						if($_GET['action'] == 'add'){
							$sql = ("SELECT * from `roster` WHERE onTrip LIKE '0' ORDER BY `roster`.`lastName` ASC");
						} else if($_GET['action'] == 'remove'){
							$sql = ("SELECT * from `roster` WHERE onTrip LIKE '1' ORDER BY `roster`.`lastName` ASC");
						}
						$stm = $con->prepare($sql);
						$stm->execute();
						$data = $stm->fetchAll();
						if($data != false){
							foreach($data as $row){
								//Output all records.
								echo "<tr class='border record'><td class='align'><img src='" . $row['studentImage'] . "' height=70px width=50px></img></td><td class='align studentName'>" . $row['firstName'] . " " . $row['lastName'] . "</td>";
								echo"<td class='align'>";
								echo "<button onclick=signIn(" . $row['StudentID'] . ",'" . $_GET['action'] . "') style='text-transform:upper;'>" . $_GET['action'] . "</button>";
								echo "</td></tr>";		
							};
						};
					?>
				</table>		
			</section>
			<footer id="footer">
				<ul class="copyright">
					<li>&copy; <a href="https://bensommer.net" target="_blank">Benjamin Sommer, 2017</a></li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
				</ul>
			</footer>
		</div>
		<!-- Scripts - First 2 scripts are for the theme do not edit!-->
		<!--[if lte IE 8]><script src="assets/js/respond.min.js"></script><![endif]-->
		<script>
			if ('addEventListener' in window) {
				window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
				document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
			}
		</script>
		<script type="text/javascript">
			/**
			* Function to sign student in
			*/
			function signIn(StudentID, action){
				$.ajax({ 
			        data: {'StudentID': StudentID, 'action': action, 'systemStatus' : 'manualAdd'},
			        url: 'serverUpdate.php', 
			        method: 'POST', 
			        success: function(msg){ 
			        //If response is '1' success, and redirect to index.php, if response is '0' alert(Error);
		                if(msg == 0){
		                   alert("Yikes, something seems to have gone wrong, please try again or relolad the page.");
		                } else if(msg == 1){
		                    window.location.href = "index.php";
		                };
			        },
			        error: function(){
			            alert("Yikes! It seems you're having a slight network issue! Please ensure your intenet connection is active!");
			        }
			    }); //End AJAX
			};
			/**
			*Script to handle search bar to search for records
			*/
			function recordsSearch(){
				var input = document.getElementById("recordsSearch").value.toLowerCase();
				var records = document.getElementsByClassName('studentName');
				for (var i = 0; i < records.length; ++i) {
					var name = records[i].innerHTML.toLowerCase();
					if(name.match(input)){
						records[i].parentElement.style.display = "";
					} else {
						records[i].parentElement.style.display = "none";
					}
				};
			};
		</script>
	</body>
</html>
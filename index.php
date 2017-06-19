<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Main Index page
* This page is where all the actual signing in and using of the program happens :)
*/

//Check $_SESSION['name'] has been made.
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=index.php');
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
		<script src="assets/js/clientFunctions.js"></script> <!--Include the Client Functions File (!Important!)-->
	</head>
	<body class="is-loading">
		<div id="wrapper">
			<section id="main">
				<header>
					<h1>Trip Attendance</h1>
					<hr />
					<?php
						//Show button to Admin Actions if User is an Admin.
						if($_SESSION['isAdmin'] == "true"){
							echo '<a href="admin.php"><button>Admin Actions</button></a> |';
						};
						//Else/Also show.....
						echo '<a href="logout.php"><button>Log Out</button></a>';
					?>
				</header>
				<hr />
				<!-- Show Search Bar-->
				<div>
					<input type="text" id="recordsSearch" onkeyup="recordsSearch()" placeholder="Search">
				</div>
				<!--Table to display student records listings-->
				<table id="recordsTable">
					<tr class='border'>
						<td></td>
						<td>Name</td>
						<td>In</td>
						<td>Out</td>
					</tr>
					<tr><!--Spacer for top line and menu listings- Don't Remove! (Yes I need to learn CSS better....Stop makin fun of me!!)-->
						<td class='spacer'></td>
					</tr>
					<?php
						require 'functions.php';
						//Update the database to set that this user is fully updated so he doesn't get all the changes again.
						updateDB($_SESSION['name']);

						//Find out if we are in a newTrip or newStop
						$status = getSysStatus();

						//Output all students from DB
						$sql = ("SELECT * FROM `roster` ORDER BY `roster`.`lastName` ASC");
						$stm = $con->prepare($sql);
						$stm->execute();
						$records = $stm->fetchAll();

						//Create Variables
						$staffName;
							
							if($status == 'newTrip'){
								$staffName = "tripStaff";
							} else {
								$staffName = "stopStaff";
							};

						//On your marks, get set, recursion!
						foreach($records as $row){

							$hideRecord; //If it's a newStop and student is not on trip we hide the record on the page
							if($status == "newStop" && $row['onTrip'] == "0") {
								$hideRecord = "display:none;"; 
							} else {
								$hideRecord = "";
							};
							
							//Echo the <tr>
							echo "<tr class='border record' id='tr_" . $row['StudentID'] . "' StudentID='" . $row['StudentID'] . "' onTrip=" . $row['onTrip'] . " style='" . $hideRecord . "'>";
								
							//Echo the Student Image, First and Last Name
							echo "<td class='align'>
									<img src='" . $row['studentImage'] . "' height=70px width=50px>
								</td>
								<td class='align align-text ";

							//Add class for seach bar, if we are in a newStop and he in !onTrip we don't want the search bar showing his records.
								if(!($status == "newStop" && $row['onTrip'] == "0")){
									echo "studentName";
								};

							echo "'>
									<b>" . $row['lastName'] . "</b><br /> " . $row['firstName'] . "
								</td>";

							//Echo the buttons to sign in and out.
							echo"<td id=add_" . $row['StudentID'] . " class='align'><span id=status_" . $row['StudentID'] . ">";
							
							//If student is signed in (newTrip or newStop, doesn't matter), output here and the signOut button
							if($status == "newTrip" && $row['onTrip'] == "1" || $status == "newStop" && $row['onStop'] == "1"){
								echo "Here: <br /><b>".$row[$staffName]."</b></span>";
								echo "<span id=addButton_".$row['StudentID']." style='display:none;'>
										<button onclick=signIn(" . $row['StudentID'] . ",'add','".$status."')>
											<i class='fa fa-check' aria-hidden='true'></i>
										</button>
									</span></td>";
								echo"<td class='align' id=remove_" . $row['StudentID'] . ">
										<button onclick=signIn(" . $row['StudentID'] . ",'remove','".$status."')>
											<i class='fa fa-check' aria-hidden='true'></i>
										</button></td>";
							} else if($status == "newTrip" && $row['onTrip'] == "0" || $status == "newStop" && $row['onStop'] == "0"){										
								//Else he's not here so output the signIn button
								echo "</span>";
								echo "<span id=addButton_".$row['StudentID'].">
										<button onclick=signIn(" . $row['StudentID'] . ",'add','".$status."')>
											<i class='fa fa-check' aria-hidden='true'></i>
										</button>
									</span></td>";
								echo"<td class='align' id=remove_" . $row['StudentID'] . " style='visibility:hidden;'>
										<button onclick=signIn(" . $row['StudentID'] . ",'remove','".$status."')>
											<i class='fa fa-check' aria-hidden='true'></i>
										</button></td></tr>";
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
		<!--Theme Responsive and Styling Scripts, Edit at your own will-->
		<script>
			if ('addEventListener' in window) {
				window.addEventListener('load', function() { document.body.className = document.body.className.replace(/\bis-loading\b/, ''); });
				document.body.className += (navigator.userAgent.match(/(MSIE|rv:11\.0)/) ? ' is-ie' : '');
			}
		</script>
	</body>
</html>
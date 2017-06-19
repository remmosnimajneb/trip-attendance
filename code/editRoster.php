<?php
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/** Edit Roster Page
* Update roster - Edit names of students, delete students, add students, CSV import.
*/

//Check $_SESSION['name'] has been made and that user is an admin
session_start();
if($_SESSION['name'] === null){
	header('Location: login.php?redirect=editRoster.php');
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
	</head>
	<body class="is-loading">
		<div id="wrapper">
			<section id="main">
				<header>
					<h1>Edit Roster</h1>
					<a href="index.php"><button>Home</button></a> | <a href="admin.php"><button>Admin Panel</button></a>
					<hr />
				</header>
				<p style="color:red;"><i>Note: Clients need to reload <br /> in order for changes to be shown!</i></p>
				<button onclick="showModal('', 'add', 'First Name', 'Last Name')">Add Student</button> <br /><br />
				<button onclick="showModal('', 'empty')">Empty Roster</button> | <button onclick="showModal('', 'csv')">CSV Upload</button>
				<hr />
				<div>
					<input type="text" id="recordsSearch" onkeyup="recordsSearch()" placeholder="Search">
				</div><br />
				<table>
					<?php
						include 'functions.php';
						//Output all students with buttons for Edit and Remove Student
						$sql = "SELECT * FROM `roster` ORDER BY `roster`.`lastName` ASC";
						$stm = $con->prepare($sql);
						$stm->execute();
						$data = $stm->fetchAll();
						foreach($data as $row){
								echo "<tr class='border record' id='".$row['StudentID']."'><td class='align'><img src='" . $row['studentImage'] . "' height='70px' width='50px' /></td>"; 
								echo "<td class='align studentName'><b>" . $row['lastName'] . "</b> " . $row['firstName'] . "</td>";
								echo "<td class='align'><button onclick=showModal(" . $row['StudentID'] . ",'edit','" . $row['firstName'] . "','" . $row['lastName'] . "')>Edit</button>   <button onclick=showModal(" . $row['StudentID'] . ",'delete')>Remove</button></td></tr>";
							};
					?>
				</table>
				<!-- Modal box to show various functions-->
				<div id="modal" class="modal">		
		  			<div class="modal-content">
		    			<div class="modal-header">
		      				<span class="close">&times;</span>
		      					<h2>Update Database</h2>
		    			</div>
		    			<div class="modal-body">
		    				<div id="modaledit" style="display: none;"><br />
			    				<input type="text" id="editStudentID" value="" disabled="true"> <br />
			      				<input type="text" id="editFirstName" value="" placeholder="First Name"> <br />
			      				<input type="text" id="editLastName" value="" placeholder="Last Name"> <br />
			      				<button onclick="sendUpdate('edit')">Submit</button><br /><br />
			      			</div>
			      			<div id="modaldelete" style="display: none;">
				      			<p>Are you sure you want to delete this student from roster?</p>
			      				<input type="hidden" id="deleteStudentID" value="">
			      				<input type="hidden" id="deleteFirstName" value="" placeholder="First Name">
			      				<input type="hidden" id="deleteLastName" value="" placeholder="Last Name">
			      				<button onclick="sendUpdate('delete')">Delete Student</button><br /><br />	
			      			</div>
			      			<div id="modaladd" style="display: none;">
				      			<p>Add New Student</p>
			      				<input type="text" id="addStudentID" value="" placeholder="Student Image URL"> <br />
			      				<input type="text" id="addFirstName" value="" placeholder="First Name"> <br />
			      				<input type="text" id="addLastName" value="" placeholder="Last Name"> <br />
			      				<button onclick="sendUpdate('add')">Add Student</button><br /><br />	
			      			</div>
			      			<div id="modalempty" style="display: none;">
			      				<p><i>This action will delete ALL records in the database. Are you sure you want to do this?</i></p>
			      				<button onclick="sendUpdate('empty')">Yes, Empty Database</button><br />
			      			</div>
			      			<div id="modalcsv" style="display: none;">
			      				<p>Import a CSV File</p>
			      				<a href="assets/sampleCSV.csv"><i>Click for sample CSV file</i></a>
			      				<hr style="margin:0px;"/>
			      				<form action="csvUpload.php" method="post" enctype="multipart/form-data">
		    						Select CSV File to upload:<br /><br />
		    						<input type="file" name="file"><br /><br />
		    						<input type="submit" value="Submit">
								</form>
			      			</div>
		    			</div>
		    			<div class="modal-footer">
		      				<h3></h3>
		    			</div>
		  			</div>
		  		</div>
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
		//Modal Setup
		var modal = document.getElementById('modal');
		var span = document.getElementsByClassName("close")[0];

		span.onclick = function() { //Close modal on click of 'x'
		    modal.style.display = "none";
		    document.getElementById('modaledit').style.display = "none";
		    document.getElementById('modaldelete').style.display = "none";
		    document.getElementById('modaladd').style.display = "none";
		    document.getElementById('modalempty').style.display = "none";
		    document.getElementById('modalcsv').style.display = "none";
		}
		window.onclick = function(event) { //Close modal if click anywhere else on page
		    if (event.target == modal) {
		        modal.style.display = "none";
		        document.getElementById('modaledit').style.display = "none";
		    	document.getElementById('modaldelete').style.display = "none";
		    	document.getElementById('modaladd').style.display = "none";
		    	document.getElementById('modalempty').style.display = "none";
		    	document.getElementById('modalcsv').style.display = "none";
		    }
		}

		function showModal(StudentID, action, firstName, lastName){
			//Show various boxes in the modal based on what user clicks
			if(action == "add" || action == "edit" || action == "delete"){
				document.getElementById(action + 'FirstName').value = firstName;
				document.getElementById(action + 'LastName').value = lastName;
				document.getElementById(action + 'StudentID').value = StudentID;
			};
			var length = $(window).scrollTop();
			var modal = document.getElementById('modal');
			modal.style.paddingTop = length + "px";
			modal.style.display = "block";
			document.getElementById('modal' + action).style.display = "block";
		};

		function sendUpdate(action){
			//Send update to Server
			if(action == "add" || action == "edit" || action == "delete"){
				var StudentID = document.getElementById(action + 'StudentID').value;
				var firstName = document.getElementById(action + 'FirstName').value;
				var lastName = document.getElementById(action + 'LastName').value;
			};
			$.ajax({ 
				data: {'formType': action + 'Record', 'StudentID': StudentID, 'firstName': firstName, 'lastName': lastName}, //Send the database ID number
				url: 'recordUpdater.php', 
				method: 'POST', 
				success: function(msg){ 
					if(msg == 1){
						//If update is '1' Alert Success and reload page
						alert("Update Success!");
						modal.style.display = "none";
						window.location.reload(false);
					} else if(msg == 0){
						alert("Yikes! It seems something went wrong. Please try again or reload the page.");
					};
				},
				error: function(){
	   				alert("Yikes! It seems something went wrong. Please try again or reload the page.");
				},
			});
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
/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/
/**
* JavaScript functions for the Admin pages (client side functions) - Scripts that if user is not an admin, user should not be able to access
*/


/**
* Function to make a 'newTrip' or 'newStop' - Although even non-admins can technicaly run the function here, PHP will check $_SESSION and die() if not admin
*/
function systemUpdate(status){
	var conf = confirm("This action will delete existing records are you sure you want to do this?");
	if (conf == true) {
   		$.ajax({ 
			data: {'status': status}, //Send the database ID number
			url: 'systemUpdate.php', 
			method: 'POST', 
			success: function(msg){ 
				if(msg == 1){
					alert("Update Success!");
					window.location.href = "index.php";
				};
			},
			error: function(){
   				alert("Yikes! It seems something went wrong. Please try again or reload the page.");
			},
		});
	};
};

/**
* Function to hide and show various blocks on the Admin Panel.
*/
function display(show, hide){
	document.getElementById(hide).style.display = "none";
	document.getElementById(show).style.display = "block";
};
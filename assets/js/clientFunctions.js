/********************************
* Prject: Trip Attendance System
* Code Version: 1.5
* Author: Benjamin Sommer
* GitHub: https://github.com/remmosnimajneb
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/**
*Functions Page - Stores functions for the system to work Client side
*/

/**
* Function to sign student in - Used for main index.php page
*/
function signIn(StudentID, action, system){
	
	//Replace button clicked with a loading GIF
	var domReplace;
	if(action == "add"){
		domReplace = "addButton_";
	} else {
		domReplace = "remove_";
	};

	document.getElementById(domReplace + StudentID).innerHTML = "<img src='images/loading.gif' style='width:30px;height:30px;'class='align' id='img_" + StudentID + "'></img>";

	//Send AJAX to server about the student (StudentID, Action [Add/Remove], SysStatus [newTrip/newStop])
    $.ajax({ 
        data: {'StudentID': StudentID, 'action': action, 'systemStatus' : system},
        url: 'serverUpdate.php', 
        method: 'POST',
        success: function(msg, StudentID){ 
        	//Response is sent in 3 parts: ID [StudentID], success [1,0], and system [newTrip,newStop]; We split it here based on ','
            var data = msg.split(",");
            var ID = data[0];
            var success = data[1];
            var system = data[2];
                if(success == 0){
                    //If something went wrong and 0 was sent back, alert the user there is an issue, replace the loading GIF with the button again to sign in.
                    alert("Yikes, something seems to have gone wrong, please try again or relolad the page.");
                    document.getElementById(domReplace + ID).innerHTML = "<button id="+domReplace+ID+" onclick=signIn("+ID+", 'add', '"+ system +"')><i class='fa fa-check' aria-hidden='true'></i></button><div>";
                } else if(success == 1){
                    //Then this means it worked! Yay! Nothing happens here though as we send response with Long Polling below not here
                };
        },
        error: function(){
        	//And if we got no internet connection throw an alert error and replace it back to the sign in button
            alert("Yikes! It seems you're having a slight network issue! Please ensure your intenet connection is active!");
            document.getElementById(domReplace + StudentID).innerHTML = "<button onclick=signIn(" + StudentID + ",'add', '" + system + "')><i class='fa fa-check' aria-hidden='true'></i></button>";
        }
    }); //End AJAX
};

/**
*Long Polling script to get updates from Server
*/

var source = new EventSource('clientUpdater.php');

//Event Types: 'add' (Sign Student In), 'remove'(Sign Student Out), 'showRecord'(Show record for student who has been added (manualAdd) to Trip), 'hideRecord'(Hide record for student who has been removed (manualAdd) to Trip), 'systemUpdate'(newTrip/newStop)

//Student Add Function
source.addEventListener('add', function(e) {
	var data = JSON.parse(e.data);
	$('#addButton_' + data.StudentID).css('display', 'none'); //Hide the SignIn Button
	$('#remove_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'remove','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>"); //Add the signOut button
    $('#remove_' + data.StudentID).css('visibility', 'visible'); //Show the Remove Button
    $('#status_' + data.StudentID).html("Here: <br /><b>" + data.staffName + "</b>"); //Set Status
    $('#tr_' + data.StudentID).attr('onTrip', '1'); //onTrip Attb to 1
}, false);

//Student Remove Function
source.addEventListener('remove', function(e) {
	var data = JSON.parse(e.data);
	$('#addButton_' + data.StudentID).css('display', 'block'); //Show the SignIn Button
	$('#remove_' + data.StudentID).css('visibility', 'hidden'); //Hide the SignOut Button
	$('#status_' + data.StudentID).html(""); //Clear Status
	$('#addButton_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>"); //Add the signIn button
    $('#tr_' + data.StudentID).attr('onTrip', '0'); //onTrip Attb to 0
}, false);

//Show Record Function
source.addEventListener('addRecord', function(e) {
	var data = JSON.parse(e.data);
	$('#tr_' + data.StudentID).attr('onTrip', '1'); //onTrip Attb to 1
	$('#remove_' + data.StudentID).css('visibility', 'hidden'); //Hide the SignOut Button
	$('#addButton_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>"); //Add the signIn button
	$('#addButton_' + data.StudentID).css('display', 'block'); //Show the SignIn Button
	$('#status_' + data.StudentID).html(""); //Clear Status
	$('#tr_' + data.StudentID).css('display', ''); //Hide the Record
	$("#tr_" + data.StudentID + " td:nth-child(2)").addClass("studentName"); //Add studentName class for search bar
}, false);

//Hide Record Function
source.addEventListener('removeRecord', function(e) {
	var data = JSON.parse(e.data);
	$('#tr_' + data.StudentID).css('display', 'none'); //Hide the Record
    $('#tr_' + data.StudentID).attr('onTrip', '0'); //onTrip Attb to 0
    $("#tr_" + data.StudentID + " td:nth-child(2)").removeClass("studentName"); //Remove studentName class for search bar
}, false);

//System Update Function
source.addEventListener('systemUpdate', function(e) {
	alert("New Trip/Stop has been started - hold on while I setup the board...er...list!");
	var data = JSON.parse(e.data);
	var records = document.getElementsByClassName('record');
	//Parse all records on the page and reset them for the newTrip/Stop
	for (var i = 0; i < records.length; ++i) {
	    var item = records[i];
	    var StudentID = item.getAttribute("StudentID");
	    var onTrip = item.getAttribute("onTrip");
	    $('#status_' + StudentID).html(""); //Clear Status
		$('#addButton_' + StudentID).html("<button onclick=signIn("+StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>"); //Add the signIn button
		$('#remove_' + StudentID).html("<button onclick=signIn("+StudentID+",'remove','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>"); //Add the signOut button
		$('#remove_' + StudentID).css('visibility', 'hidden'); //Hide the SignOut Button
		$('#addButton_' + StudentID).css('display', 'block'); //Show the SignIn Button
		//If it's a newStop and there onTrip = 1
	    if(data.system == "newStop" && onTrip == "1") {
	        item.setAttribute('onTrip', '1'); //Set onTrip Attb to 1
	    } else {
	        item.setAttribute('onTrip', '0'); //Else set onTrip Attb to 0
	    };
	    
	    //If it's a newStop and not onTrip
	    if(data.system == "newStop" && onTrip == "0"){
	        document.getElementById("tr_" + StudentID).style.display = "none"; //Hide Record
	        $("#tr_" + StudentID + " td:nth-child(2)").removeClass("studentName"); //Remove studentName class
	    } else { //Else newStop and they are onTrip
	        document.getElementById("tr_" + StudentID).style.display = ""; //Show Record
	        $("#tr_" + StudentID + " td:nth-child(2)").addClass("studentName"); //Add studentName class
	    };
	};
}, false);

/**
*Script to handle search bar to search for records
*/
function recordsSearch(){
	var input = document.getElementById("recordsSearch").value.toLowerCase();
	var records = document.getElementsByClassName('studentName');
	for (var i = 0; i < records.length; ++i) {
		var name = records[i].innerHTML.toLowerCase();
		//Search through records with className `studentName` and if a match is found show it else hide it
		if(name.match(input)){
			records[i].parentElement.style.display = "";
		} else {
			records[i].parentElement.style.display = "none";
		}
	};
};
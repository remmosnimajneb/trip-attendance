/********************************
* Prject: Trip Attendance System
* Code Version: 1.0 - YNA Release
* Author: Benjamin Sommer
* Website: bensommer.net
* GitHub: https://github.com/remmosnimajneb
* Email: ben@bensommer.net
* Theme Design by: HTML5 UP [HTML5UP.NET] - Theme `Identity`
* Licensing Information: CC BY-SA 4.0 (https://creativecommons.org/licenses/by-sa/4.0/)
***************************************************************************************/

/**
*Functions Page - Stores functions for the system to work Client side
*/

/**
* Function to sign student in
*/
function signIn(StudentID, action, system){
	
	var domReplace;
	if(action == "add"){
		domReplace = "addButton_";
	} else {
		domReplace = "remove_";
	};

document.getElementById(domReplace + StudentID).innerHTML = "<img src='images/loading.gif' style='width:30px;height:30px;'class='align' id='img_" + StudentID + "'></img>";

    $.ajax({ 
        data: {'StudentID': StudentID, 'action': action, 'systemStatus' : system},
        url: 'serverUpdate.php', 
        method: 'POST', 
        success: function(msg, StudentID){ 
            var data = msg.split(","); //The message is sent as ID,success - it gets parsed out into two vars, ID and success (the ID number and a 1 or 0 if its succesfull)
            var ID = data[0];
            var success = data[1];
            var system = data[2];
                if(success == 0){
                    //If something went wrong and 0 was sent back, alert the user there is an issue, replace the loading GIF with the button again to sign in.
                    alert("Yikes, something seems to have gone wrong, please try again or relolad the page.");
                    document.getElementById(domReplace + ID).innerHTML = "<button id="+domReplace+ID+" onclick=signIn("+ID+", 'add', '"+ system +"')><i class='fa fa-check' aria-hidden='true'></i></button><div>";
                } else if(success == 1){
                    //Then this means it worked, this won't really happen as we send a succefull response code back with the long polling not this.
                };
        },
        error: function(){
            alert("Yikes! It seems you're having a slight network issue! Please ensure your intenet connection is active!");
            document.getElementById(domReplace + StudentID).innerHTML = "<button onclick=signIn(" + StudentID + ",'add', '" + system + "')><i class='fa fa-check' aria-hidden='true'></i></button>";
        }
    }); //End AJAX
};

/**
*Long Polling script to get updates from Server
*/

var source = new EventSource('clientUpdater.php');

//Event Types: 'add', 'remove', 'showRecord', 'hideRecord', systemUpdate'

//Student Add Function
source.addEventListener('add', function(e) {
	var data = JSON.parse(e.data);
	$('#addButton_' + data.StudentID).css('display', 'none'); //Hide the SignIn Button
	$('#remove_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'remove','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>");
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
	$('#addButton_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>");
    $('#tr_' + data.StudentID).attr('onTrip', '0'); //onTrip Attb to 0
}, false);

//Show Record Function
source.addEventListener('addRecord', function(e) {
	var data = JSON.parse(e.data);
	$('#tr_' + data.StudentID).attr('onTrip', '1'); //onTrip Attb to 1
	$('#remove_' + data.StudentID).css('visibility', 'hidden'); //Hide the SignOut Button
	$('#addButton_' + data.StudentID).html("<button onclick=signIn("+data.StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>");
	$('#addButton_' + data.StudentID).css('display', 'block'); //Show the SignIn Button
	$('#status_' + data.StudentID).html(""); //Clear Status
	$('#tr_' + data.StudentID).css('display', ''); //Hide the Record
}, false);

//Hide Record Function
source.addEventListener('removeRecord', function(e) {
	var data = JSON.parse(e.data);
	$('#tr_' + data.StudentID).css('display', 'none'); //Hide the Record
    $('#tr_' + data.StudentID).attr('onTrip', '0'); //onTrip Attb to 0
}, false);

//System Update Function
source.addEventListener('systemUpdate', function(e) {
	alert("New Trip/Stop has been started - hold on while I setup the board...er...list!");
	var data = JSON.parse(e.data);
	var records = document.getElementsByClassName('record');
	for (var i = 0; i < records.length; ++i) {
	    var item = records[i];
	    var StudentID = item.getAttribute("StudentID");
	    var onTrip = item.getAttribute("onTrip");
	    $('#status_' + StudentID).html(""); //Clear Status
		$('#addButton_' + StudentID).html("<button onclick=signIn("+StudentID+",'add','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>");
		$('#remove_' + StudentID).html("<button onclick=signIn("+StudentID+",'remove','"+data.system+"')><i class='fa fa-check' aria-hidden='true'></i></button>");
		$('#remove_' + StudentID).css('visibility', 'hidden'); //Hide the SignOut Button
		$('#addButton_' + StudentID).css('display', 'block'); //Show the SignIn Button
	    if(data.system == "newStop" && onTrip == "1") {
	        item.setAttribute('onTrip', '1');
	    } else {
	        item.setAttribute('onTrip', '0');
	    };
	    
	    if(data.system == "newStop" && onTrip == "0"){
	        document.getElementById("tr_" + StudentID).style.display = "none";
	    } else {
	        document.getElementById("tr_" + StudentID).style.display = "";
	    };
	};
}, false);

/**
*Show/Hide records that have been signed in - NOTE: Not implemented yet in this version - Coming in V1.5
*/
function recordsDisplay(action){
	var records = document.getElementsByClassName('record');
	for (var i = 0; i < records.length; ++i) {
	    var item = records[i];
	    var StudentID = item.getAttribute("StudentID");
	    $('#tr_' + StudentID).css('display', action); //Show the Record
	};
};

/**
*Script to handle search bar to search for records
*/
function recordsSearch(){
	var trs = document.getElementsByClassName('record');
	var input = document.getElementById("recordsSearch").value.toLowerCase();
	var records = document.getElementsByClassName('studentName');
	for (var i = 0; i < records.length; ++i) {
		var name = records[i].innerHTML.toLowerCase();
		trs[i].style.display = "none";
		if(name.search(input) > -1){
			trs[i].style.display = "";
		};
	};
};
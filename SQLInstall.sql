-- SQL Install Code for Trip Attendance System V1.5; See README.MD for more information

--Select DB to use here:
use databaseName;

--Create Changes table

CREATE TABLE IF NOT EXISTS `changes` (
  `ChangeID` int(9) NOT NULL AUTO_INCREMENT,
  `StudentID` varchar(120) NOT NULL,
  `action` varchar(40) DEFAULT NULL,
  `staffName` varchar(40) NOT NULL,
  `system` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`ChangeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--Create Roster table

CREATE TABLE IF NOT EXISTS `roster` (
  `StudentID` int(9) NOT NULL AUTO_INCREMENT,
  `lastName` varchar(120) NOT NULL,
  `firstName` varchar(120) NOT NULL,
  `studentImage` varchar(120) DEFAULT 'images/studentImages/defualt.jpg',
  `association` varchar(120) DEFAULT NULL,
  `onTrip` int(11) DEFAULT '0',
  `onStop` int(11) DEFAULT '0',
  `tripStaff` varchar(40) DEFAULT NULL,
  `stopStaff` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`StudentID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--Insert Sample Students

INSERT INTO `roster` (`StudentID`, `lastName`, `firstName`, `studentImage`, `association`, `onTrip`, `onStop`, `tripStaff`, `stopStaff`) VALUES
(1, 'Ashley', 'Johnson', 'images/studentImages/defualt.jpg', NULL, 0, 0, '', ''),
(2, 'James', 'Jamerson', 'images/studentImages/defualt.jpg', NULL, 0, 0, '', ''),
(3, 'Kevin', 'Austin', 'images/studentImages/defualt.jpg', NULL, 0, 0, '', ''),
(4, 'Kelly', 'Clarkson', 'images/studentImages/defualt.jpg', NULL, 0, 0, '', ''),
(5, 'Macy', 'Fisher', 'images/studentImages/defualt.jpg', NULL, 0, 0, '', '');

--Create System Status Table

CREATE TABLE IF NOT EXISTS `systemstatus` (
  `StatusID` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `value` varchar(40) NOT NULL,
  PRIMARY KEY (`StatusID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--Mark it's a New Trip (default)

INSERT INTO `systemstatus` (`StatusID`, `name`, `value`) VALUES
(1, 'status', 'newTrip');

--Create Users Table

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `password` varchar(150) NOT NULL,
  `admin` varchar(20) NOT NULL DEFAULT 'false',
  `lastStudentID` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--Insert defualt user to table

INSERT INTO `users` (`UserID`, `name`, `password`, `admin`, `lastStudentID`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'true', 1);
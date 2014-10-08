<?php
header('Content-Type: application/json');

$userID = $_POST['userid'];
$courseID = $_POST['courseid'];

// Connect to the db
include 'php/db.php';

if(addCourseToPlanned($userID, $courseID)){
	echo("1");
}else{
	echo("0");
}

?>
<?php
header('Content-Type: application/json');

$userID = $_POST['userid'];
$courses = $_POST['courses'];

// Connect to the db
include 'php/db.php';

if(addCompletedCourses($userID, $courses)){
	echo("1");
}else{
	echo("0");
}

?>
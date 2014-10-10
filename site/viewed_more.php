<?php
header('Content-Type: application/json');

$userID = $_POST['userID'];
$courseID = $_POST['courseID'];

// Connect to the db
include 'php/db.php';

if(viewedCourseMore($userID,$courseID)){
	echo("1");
}else{
	echo("0");
}

?>
<?php

include 'php/db.php';

$code = strtolower($_POST['code']);
$name = strtolower($_POST['name']);
$ects = $_POST['ects'];
$descr_short = $_POST['descr_short'];
$descr_long = $_POST['descr_long'];
$department = $_POST['department'];

if( addCourse($code, $name, $ects, $descr_short, $descr_long, $department) ){
	echo 1;
}else{
	echo 0;
}
?>
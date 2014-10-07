<?php

include 'php/db.php';

$from = strtolower($_POST['from']);
$to = strtolower($_POST['to']);

if($from == $to){
	echo 0;
	die();
}

if( addPre($from, $to) ){
	echo 1;
}else{
	echo 0;
}
?>
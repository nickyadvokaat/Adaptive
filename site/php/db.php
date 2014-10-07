<?php
	// Connecting, selecting database
	$link = mysql_connect('web304.webfaction.com', 'nicky_adaptive', 'rYLhIsOMYpFyjgkLbsCu')
		or die('Could not connect: ' . mysql_error());

	mysql_select_db('nicky_adaptive') or die('Could not select database');

	function getDepartmentName($department_id){
		$query = "SELECT * FROM department WHERE id=$department_id";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
        $row = mysql_fetch_array($result);
		$name = $row['name'];
		
		return $name;
	}
	
	function getCourses(){
		$query = "SELECT * FROM course ORDER BY name";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
		$courses = array();
		while($row = mysql_fetch_array($result)){
			$courses[] = $row;
		}
		
		return $courses;
	}
	
	function getCourse($course_id){
		$query = "SELECT * FROM course WHERE id=$course_id";
		$result = mysql_query($query) or die("Failed" . mysql_error());		
		
		$row = mysql_fetch_array($result);
				
		return $row;
	}
	
	function userIsAdmin($username){
		$query = "SELECT admin FROM users WHERE username='$username'";
		$result = mysql_query($query) or die("Failed" . mysql_error());	
		
		$row = mysql_fetch_array($result);
		
		return $row['admin'] == 1;
	}
	
	function addCourse($code, $name, $ects, $descr_short, $descr_long, $department){
		$query = "INSERT INTO course (code, name, ects, descr_short, descr_long, department)
			VALUES ('$code', '$name', $ects, '$descr_short', '$descr_long', $department)";

		$result = mysql_query($query);	
		if($result){
			return true;
		}
		return false;
	}
	
	function addPre($from, $to){
		$query = "INSERT INTO prerequisite (id1, id2)
			VALUES ('$from', '$to')";

		$result = mysql_query($query);	
		if($result){
			return true;
		}
		return false;
	}
	
	function x(){
		$query = "SELECT c1.name AS course_from, c2.name AS course_to FROM course AS c1, course AS c2, prerequisite WHERE c1.id = prerequisite.id1 AND c2.id = prerequisite.id2";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
		$pres = array();
		while($row = mysql_fetch_array($result)){
			$pres[] = $row;

			}
		
		return $pres;
	}
	
	function addCompletedCourses($userID, $courseIDs){
		$query = "
				INSERT IGNORE INTO completed
				(user_id, course_id)
				VALUES";
		$i = 0;
		foreach($courseIDs as $courseID) {
			if($i != 0){
				$query .= ",";
			}
			$i++;
			$query .= "(" . $userID . "," . $courseID . ")";
		}
		$query .= ";";	

		$result = mysql_query($query);	
		if($result){
			return visitedPage($userID);
		}
		return false;
		
	}
	
	function visitedPage($userID){
		$query = "
				UPDATE users
				SET first_visit=0
				WHERE id='$userID'";			

		$result = mysql_query($query);	
		
		if($result){
			return true;
		}
		
		return false;
	}
?>
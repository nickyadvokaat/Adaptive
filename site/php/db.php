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
	
	function isFirstVisit($userID){
		$query = "
				SELECT first_visit
				FROM users				
				WHERE id='$userID'";			

		$result = mysql_query($query);	
		
		
		$row = mysql_fetch_array($result);
		
		return $row['first_visit'] == 1;		
	}
	
	function addCourseToPlanned($userID, $courseID){
		$query = "
					INSERT IGNORE INTO planned (user_id, course_id)
					VALUES ('$userID', '$courseID')";

		$result = mysql_query($query);	
		if($result){
			return true;
		}
		return false;
	}
	
	/* New 	*/
	function markVisited($userID, $courseID){
		$query = "
			INSERT IGNORE INTO viewed
			(user_id, course_id)
			VALUES 
		";
		$query .= "(".$userID.", ".$courseID.")";
		
		$result = mysql_query($query);
		
		if ($result){
			return true;
		}
		return false;
	}
	
	/**
	 * Returns whether a user has completed a course or not
	 */
	function completedCourse($userID, $courseID){
		$query = "
			SELECT * FROM completed
			WHERE user_id=".$userID." 
			AND course_id=".$courseID."
		";
		
		$result = mysql_query($query);
		return (mysql_num_rows($result) > 0);
	}
	
	
	/**
	 * Returns a list of ID's for courses which are prerequisites for the given course
	 * Returns the empty list if there are no prerequisites
	 */
	function getPrereqs($courseID){
		$query = "
			SELECT id1 FROM prerequisite
			WHERE id2 = ".$courseID."
		";
		$result = mysql_query($query);
		return mysql_fetch_array($result);
	}
	
	/**
	 * Returns a list of ID's for courses which are from the same department
	 * The course with $courseID is not returned
	 * Returns an empty list if no courses from the same department exist
	 */
	function getSameDepartment($courseID){
		$query = "
			SELECT department FROM course
			WHERE course_id = ".$courseID."
		";
		$result = mysql_query($query);
		//Assuming this function is only called on existing courseID's
		$row = mysql_fetch_array($result);
		$department = $row['department'];
		
		$query2 = "
			SELECT courseID FROM course
			WHERE department = ".$department."
			AND course_id NOT ".$courseID."
		";
		$result2 = mysql_query($query2);
		return mysql_fetch_array($result2);
	}
	
	function isViewed($userID, $courseID){
		$query = "
			SELECT * FROM viewed
			WHERE user_id = ".$userID."
			AND course_id = ".$courseID."
		";
		$result = mysql_query($query);
		return mysql_num_rows($result > 0);
	}
	
	function isViewedMore($userID, $courseID){
		$query = "
			SELECT * FROM viewed_more
			WHERE user_id = ".$userID."
			AND course_id = ".$courseID."
		";
		$result = mysql_query($query);
		return mysql_num_rows($result > 0);
	}
	
	/**
	 * For a given user and course, returns the suitability of the course
	 * The suitability is a number in the range 0..100
	 * If a course is already completed, the suitability is 100
	 * Else, it is (#prereqs/(#prereqs completed))*40+(#siblings/(#siblings completed))*40+viewed*10+viewed_more*10
	 */
	function getCourseSuitability($userID, $courseID){
		//If the course has been followed already, the suitability is 100%
		if (completedCourse($userID, $courseID){
			return 100;
		}
		
		
		$suitability = 0;
		//Compute score for completed prerequisites
		$prereqs = getPrereqs($courseID);
		$nrOfPrereqs = sizeof($prereqs);
		if ($nrOfPrereqs == 0){
			$suitability += 40;
		}
		else{
			//Get the prerequisites, and for each prerequisite which has been completed, do $suitability += 40/$nrOfPrereqs;
		}
		
		//Compute score for completed courses of the same department
		$sameDept = getSameDepartment($courseID);
		$nrOfSameDept = sizeof($sameDept);
		if ($nrOfSameDept == 0){
			$suitability += 40;
		}
		else{
			//Get the courses from the same department, and for each prerequisite which has been completed, do $suitability += 40/$nrOfSameDept;
		}
		
		//Compute score for viewing and viewing more
		if (isViewed($userID, $courseID)){
			$suitability += 10;
		}
		if (isViewedMore($userID, $courseID)){
			$suitability += 10;
		}
		
		return $suitability;
	}
	/* End new */
?>
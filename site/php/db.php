<?php
	// Connecting, selecting database
	$link = mysql_connect('web304.webfaction.com', 'nicky_adaptive', 'rYLhIsOMYpFyjgkLbsCu')
		or die('Could not connect: ' . mysql_error());

	mysql_select_db('nicky_adaptive') or die('Could not select database');

	/*
	 * Get name of department given its ID
	 */
	function getDepartmentName($department_id){
		$query = "SELECT * FROM department WHERE id=$department_id";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
        $row = mysql_fetch_array($result);
		$name = $row['name'];
		
		return $name;
	}
	
	/*
	 * Get all information of all courses
	 */
	function getCourses(){
		$query = "SELECT * FROM course ORDER BY name";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
		$courses = array();
		while($row = mysql_fetch_array($result)){
			$courses[] = $row;
		}
		
		return $courses;
	}
	
	/*
	 * Get all data of a specific course with given ID
	 */
	function getCourse($course_id){
		$query = "SELECT * FROM course WHERE id=$course_id";
		$result = mysql_query($query) or die("Failed" . mysql_error());		
		
		$row = mysql_fetch_array($result);
				
		return $row;
	}
	
	/*
	 * Check whether user with given ID is an admin
	 */
	function userIsAdmin($userID){
		$query = "SELECT admin FROM users WHERE id='$userID'";
		$result = mysql_query($query) or die("Failed" . mysql_error());	
		
		$row = mysql_fetch_array($result);
		
		return $row['admin'] == 1;
	}
	
	/*
	 * Add a course to the database
	 */
	function addCourse($code, $name, $ects, $descr_short, $descr_long, $department){
		$query = "INSERT INTO course (code, name, ects, descr_short, descr_long, department)
			VALUES ('$code', '$name', $ects, '$descr_short', '$descr_long', $department)";

		$result = mysql_query($query);	
		if($result){
			return true;
		}
		return false;
	}
	
	/*
	 * Add a prerequisite
	 * Input 2 course ID's, $from is a prerequisite of $to
	 */
	function addPre($from, $to){
		$query = "INSERT INTO prerequisite (id1, id2)
			VALUES ('$from', '$to')";

		$result = mysql_query($query);	
		if($result){
			return true;
		}
		return false;
	}
	
	/*
	 * List all prerequisites
	 */
	function x(){
		$query = "SELECT c1.name AS course_from, c2.name AS course_to FROM course AS c1, course AS c2, prerequisite WHERE c1.id = prerequisite.id1 AND c2.id = prerequisite.id2";
		$result = mysql_query($query) or die("Failed" . mysql_error());
		
		$pres = array();
		while($row = mysql_fetch_array($result)){
			$pres[] = $row;
		}
		
		return $pres;
	}
	
	/*
	 * Mark course with $courseID as completed for user with ID $userID
	 */
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
	
	/*
	 * Add course with ID $courseID to planned courses for user with ID $userID
	 */
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
	
	/*
	 * The user has visited the main page, used to show the 'select completed courses' page only once
	 */
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
	
	/*
	 * Check whether this is first visit to main page
	 */
	function isFirstVisit($userID){
		$query = "
				SELECT first_visit
				FROM users				
				WHERE id='$userID'";			

		$result = mysql_query($query);	
		
		
		$row = mysql_fetch_array($result);
		
		return $row['first_visit'] == 1;		
	}
	
	/*
	 * Sum on ects of completed courses for user with ID $userID
	 */
	function getStudyPointsCompleted($userID){
		$query = "
				SELECT 	SUM(ects) AS points
				FROM 	course, completed			
				WHERE 	completed.user_id='$userID' AND
						completed.course_id=course.id						
				";
		$result = mysql_query($query) or die("Failed" . mysql_error());			
		$row = mysql_fetch_array($result);		
		return $row['points'];		
	}
	
	/*
	 * Sum on ects of planned courses for user with ID $userID
	 */
	function getStudyPointsPlanned($userID){
		$query = "
				SELECT 	SUM(ects) AS points
				FROM 	course, planned			
				WHERE 	planned.user_id='$userID' AND
						planned.course_id=course.id						
				";
		$result = mysql_query($query) or die("Failed" . mysql_error());		
		$row = mysql_fetch_array($result);		
		return $row['points'];		
	}
	
	/* New 	*/
	
	/*
	 * Mark course with ID $courseID as visited for user with ID $userID
	 */
	function viewedCourse($userID, $courseID){
		$query = "
			INSERT IGNORE INTO viewed
			(user_id, course_id)
			VALUES ('$userID','$courseID')";
		
		$result = mysql_query($query);
		
		if ($result){
			return true;
		}
		return false;
	}
	
	/*
	 * Mark user with ID $userID has opened 'read more' section of page of course with ID $courseID
	 */
	function viewedCourseMore($userID, $courseID){
		$query = "
			INSERT IGNORE INTO viewed_more
			(user_id, course_id)
			VALUES ('$userID', '$courseID')";
		
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
			WHERE user_id='$userID'
			AND course_id='$courseID'			
		";
		
		$result = mysql_query($query);		
		return !(mysql_num_rows($result) == 0);
	}
	
	
	/**
	 * Returns a list of ID's for courses which are prerequisites for the given course
	 * Returns the empty list if there are no prerequisites
	 */
	function getPrereqs($courseID){
		$query = "
			SELECT id1 FROM prerequisite
			WHERE id2 = '$courseID'
		";
		$result = mysql_query($query);
		return mysql_fetch_array($result);
	}
	
	/**
	 * Returns a list of ID's for courses which are from the same department
	 * The course with $courseID is not returned
	 * Returns an empty list if no courses from the same department exist
	 */
	function getCoursesSameDepartment($courseID){
		$query = "
			SELECT department FROM course
			WHERE id = '$courseID'
		";
		$result = mysql_query($query);
		//Assuming this function is only called on existing courseID's
		$row = mysql_fetch_array($result);
		$department = $row['department'];
		
		$query2 = "
			SELECT id 
			FROM course
			WHERE department = '$department'
			AND id <> '$courseID'
		";
		$result2 = mysql_query($query2);
		
		$courses = array();
		while($row = mysql_fetch_array($result2)){
			$courses[] = $row;
		}		
		
		return $courses;
	}
	
	function isViewed($userID, $courseID){
		$query = "
			SELECT * 
			FROM viewed
			WHERE user_id = '$userID'
			AND course_id = '$courseID'
		";
		$result = mysql_query($query);
		return !(mysql_num_rows($result) == 0);
	}
	
	function isViewedMore($userID, $courseID){
		$query = "
			SELECT * 
			FROM viewed_more
			WHERE user_id = '$userID'
			AND course_id = '$courseID'
		";
		$result = mysql_query($query);
		return !(mysql_num_rows($result) == 0);
	}
	
	/**
	 * For a given user and course, returns the suitability of the course
	 * The suitability is a number in the range 0..100
	 * If a course is already completed, the suitability is 100
	 * Else, it is (#prereqs/(#prereqs completed))*40+(#siblings/(#siblings completed))*40+viewed*10+viewed_more*10
	 */
	function getCourseSuitability($userID, $courseID){
		//If the course has been followed already, the suitability is 100%
		if (completedCourse($userID, $courseID)){
			return "Done";
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
		$coursesDept = getCoursesSameDepartment($courseID);
		$nrOfSameDept = count($coursesDept);
		if ($nrOfSameDept == 0){
			$suitability += 40;
		}else{
			//Get the courses from the same department, and for each prerequisite which has been completed, do $suitability += 40/$nrOfSameDept;
			$nrCompleted = 0;
			
			foreach($coursesDept as $course) {
				$departmentCourseID = $course['id'];
				if(completedCourse($userID, $departmentCourseID)){
					$nrCompleted++;
				}
			}
			
			$suitability += intval(($nrCompleted/$nrOfSameDept) * 40);			
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

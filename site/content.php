<?php
require ("config.php");

// redirect to index when user is not logged in
if (empty($_SESSION['user'])) {
	header("Location: index.php");
	die("Redirecting to index.php");
}

// Connect to the db
include 'php/db.php';

$user_id = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];

// Show select completed courses page when this is the first visit
if(isFirstVisit($user_id)){
	header("Location: select_courses.php");
	die("Redirecting to select_courses.php");
}

$content_title = "404";
$content_text = "not found";
$page = htmlspecialchars($_GET["page"]);

$isCoursePage = false;

if ($page == "") {
	
}else if (is_numeric($page)) {
	$isCoursePage = true;

	$course = getCourse($page);
	$content_title = $course['name'];
	$content_text = $course['descr_short'];
	$content_text_long = $course['descr_long'];
	
	// Mark this course page as viewed
	viewedCourse($user_id, $page);
}

// Load side bar content
$content_courses = "";
$courses = getCourses();
foreach($courses as $course) {
	$course_id = $course['id'];
	$course_name = $course['name'];
	$active = "";
	if ($course_id == $page) {
		$active = "active";
	}
	$content_courses.= '<a href="http://www.internetusage.nl/adaptive/content.php?page=' . $course_id . '" class="list-group-item ' . $active . '">' . ucwords($course_name) . '<span class="badge">25</span></a>';
}

// If the user is Admin, show admin tools menu item
if(userIsAdmin($user_id)){
	$adminButton =  "<li class='dropdown'>
		<a class='dropdown-toggle' data-toggle='dropdown' href='#'>Admin Tools<span class='caret'></span></a>
		<ul class='dropdown-menu' role='menu'>
		<li><a href='../adaptive/admin_add_course.php'>Add a course</a></li>
		<li><a href='../adaptive/admin_add_pre.php'>Add a prerequisite</a></li>
		</ul>
		</li>";
}

$points_completed = intval(getStudyPointsCompleted($user_id));
$points_completed_fraction = intval(($points_completed * 100) / 180);

$points_planned = intval(getStudyPointsPlanned($user_id));
$points_planned_fraction = intval(($points_planned * 100) / 180);

mysql_close($link);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AWS</title>
	
	<!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/navbar.css" rel="stylesheet">
  </head>
  <body>

    <div class="container">

      <!-- Static navbar -->
      <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          <a class="navbar-brand" href="../adaptive/content.php">AWS</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="../adaptive/content.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
		    <?php echo $adminButton; ?>
            <li><a href="logout.php">Logout <?php echo $_SESSION['user']['username']; ?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

	
    <div class="jumbotron">
		<div class="row">
			<div class="col-md-4">
			  <div class="list-group">
				<?php echo $content_courses; ?>
			  </div>
			</div>
			<div class="col-md-8">		
				<?php 
					if($isCoursePage){ 
						echo '
							<h1>'. ucwords($content_title) .'</h1>
							<p>' . $content_text . '</p>					
					
							<div class="panel-group" id="accordion">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a id="collapse-text" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Show more</a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse">
										<div class="panel-body">
											<p>'. $content_text_long .'</p>
										</div>
									</div>
								</div>       
							</div>
					
							<button id="addButton" type="button" class="btn btn-info">Add course</button>			
						 ';
				}else{
					echo '
							<h1>Welcome</h1>
							<p>Select a course on the left to view information about it.</p>
						 ';			
				}
			?>
		</div>
      </div>
    </div>

	<div class="progress">
        <div class="progress-bar" style="width: <?php echo $points_completed_fraction; ?>%"></div>
        <div class="progress-bar progress-bar-info" style="width: <?php echo $points_planned_fraction; ?>%"></div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

	<script type="text/javascript">
	$(document).ready(function(){
		$('#collapseOne').on('hidden.bs.collapse', function(){
			$('#collapse-text').text('Show more');
		});
		$('#collapseOne').on('shown.bs.collapse', function(){
			$('#collapse-text').text('Show less');
			
			$.ajax({
				url: "viewed_more.php",
				type: "post",
				data: {'userID': <?php echo $user_id; ?>, 'courseID': <?php echo $page; ?>},
				dataType: 'json',
				success: function(data){
					 if(data == "1"){
					}else{
					}
			  },
				error:function(){
			  }   
			}); 
		});
	});
	
	$("#addButton").click(function(){		
		 $.ajax({
			url: "add_course.php",
			type: "post",
			data: {'userid': <?php echo $user_id; ?>, 'courseid': <?php echo $page; ?>},
			dataType: 'json',
			success: function(data){
				 if(data == "1"){
					window.location = "../adaptive/content.php";
				}else{
					console.log(data);
				}
		  },
			error:function(){
			  console.log("error");
		  }   
		}); 	
	});
	</script>
  </body>
</html>

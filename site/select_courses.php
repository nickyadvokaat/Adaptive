<?php
require ("config.php");

if (empty($_SESSION['user'])) {
	header("Location: index.php");
	die("Redirecting to index.php");
}

$userID = $_SESSION['user']['id'];
echo $userID;

// Connect to the db

include 'php/db.php';

$content_courses = "";
$courses = getCourses();

foreach($courses as $course) {
	$course_id = $course['id'];
	$course_code = $course['code'];
	$course_name = $course['name'];

	$content_courses.= '<a class="list-group-item clickAdd" href="#" courseid="' . $course_id . '"><span>' . ucwords($course_name) . ' (' . strtoupper($course_code) . ')</span></a>';
}

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
            <li><a href="../adaptive/content.php">Home</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
		    <?php echo $adminButton; ?>
            <li><a href="logout.php">Logout <?php
echo $_SESSION['user']['username']; ?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

	
    <div class="jumbotron">	
	
		<h2>Completed courses</h2>
		<h4>Select the courses you have already completed in the past</h4>			
		<form role="form">
			<div class="form-group">
				<input class="form-control" id="searchinput" type="search" placeholder="Search..." />
			</div>
			<div id="searchlist" class="list-group">
				<?php echo $content_courses; ?>
			</div>
		</form>

		<button id="doneButton" type="button" class="btn btn-primary">Done</button>
		
    </div> <!-- /container -->	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-list-filter.min.js"></script>
	<script>

	$('#searchlist').btsListFilter('#searchinput', {itemChild: 'span'});
	
	$('.clickAdd').click(function(){
		$(this).toggleClass("active");
	});
	
	$("#doneButton").click(function(){
		var mySet = new Set();
		
		// for each active child
		$("#searchlist").children(".active").each(function(){
			mySet.add(($(this).attr("courseid")));
		});
		
		// convert set to plain Array
		var myArr = [v for (v of mySet)];
		
		 $.ajax({
			url: "completed_courses.php",
			type: "post",
			data: {'userid': <?php echo $userID; ?>, 'courses': myArr},
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

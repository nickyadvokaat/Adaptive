<?php
require("config.php");

if (empty($_SESSION['user'])) {
	header("Location: index.php");
	die("Redirecting to index.php");
}

// Connect to the db
include 'php/db.php';

$username = $_SESSION['user']['username'];

if (userIsAdmin($username)) {
} else {
	header("Location: index.php");
	die("Redirecting to index.php");
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
	
	<link href="css/custom.css" rel="stylesheet">	

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
			<li class='dropdown active'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>Admin Tools<span class='caret'></span></a>
				<ul class='dropdown-menu' role='menu'>
				<li><a href='../adaptive/admin_add_course.php'>Add a course</a></li>
				<li><a href='../adaptive/admin_add_pre.php'>Add a prerequisite</a></li>
				</ul>
			</li>
            <li><a href="logout.php">Logout <?php
echo $_SESSION['user']['username'];
?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

	
    <div class="jumbotron">
		<div id="output"></div>
	
		<h2> Add a course </h2>
		<form role="form" action="process_form.php" method="post" id="myForm">
			<div class="form-group">
			<label for="code">Code</label>
			<input type="text" class="form-control" name="code" value="" placeholder="Course code">
		  </div>
		  <div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" name="name" value="" placeholder="Course name">
		  </div>
		  <div class="form-group">
			<label for="ects">ECTS</label>
			<select name="ects"  class="form-control">
			  <option value="3">3 </option>
			  <option value="5">5</option>
			  <option value="6">6</option>
		    </select>		 
		  </div>
		  <div class="form-group">
			<label for="descr_short">Short Description</label>
			<textarea rows="3" type="text" class="form-control" name="descr_short" value="" placeholder="A short description of the course"></textarea>
		  </div>
		  <div class="form-group">
			<label for="descr_long">Long Description</label>
			<textarea rows="6" type="text" class="form-control" name="descr_long" value="" placeholder="An extended description of the course"></textarea>
		  </div>
		  <div class="form-group">
			<label for="department">Departement</label>
			<select name="department"  class="form-control">
			  <option value="1">Visualization </option>
			  <option value="2">Algorithms</option>
			  <option value="3">Architecture of Information Systems</option>
			  <option value="4">Web Engineering</option>
			  <option value="5">Formal Systems Analysis</option>
			  <option value="6">Software Engineering and Technology</option>
			  <option value="7">Security</option>
			  <option value="8">Systems Architecture and Networks</option>
		    </select>		 
		  </div>

		 
		   <button type="submit" class="btn btn-lg btn-primary" value="Register">Submit</button>
		</form>
		
    </div> <!-- /container -->	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="http://malsup.github.com/jquery.form.js"></script> 
    <script> 
      // prepare the form when the DOM is ready
	$(document).ready(function() {
		var options = {
			target:        '',   // target element(s) to be updated with server response
			beforeSubmit:  showRequest,  // pre-submit callback
			success:       showResponse  // post-submit callback        
		};

		// bind to the form's submit event
		$('#myForm').submit(function() {
			$('#output').html("<div class='alert alert-info'><p>Working..</p></div>");
		
			// inside event callbacks 'this' is the DOM element so we first
			// wrap it in a jQuery object and then invoke ajaxSubmit
			$(this).ajaxSubmit(options);

			// !!! Important !!!
			// always return false to prevent standard browser submit and page navigation
			return false;
		});
	});

	// pre-submit callback
	function showRequest(formData, jqForm, options) {
		// formData is an array; here we use $.param to convert it to a string to display it
		// but the form plugin does this for you automatically when it submits the data
		//var queryString = $.param(formData);

		// jqForm is a jQuery object encapsulating the form element.  To access the
		// DOM element for the form do this:
		// var formElement = jqForm[0];

		//alert('About to submit: \n\n' + queryString);

		// here we could return false to prevent the form from being submitted;
		// returning anything other than false will allow the form submit to continue
		return true;
	}

	// post-submit callback
	function showResponse(responseText, statusText)  {
		// for normal html responses, the first argument to the success callback
		// is the XMLHttpRequest object's responseText property

		// if the ajaxSubmit method was passed an Options Object with the dataType
		// property set to 'xml' then the first argument to the success callback
		// is the XMLHttpRequest object's responseXML property

		// if the ajaxSubmit method was passed an Options Object with the dataType
		// property set to 'json' then the first argument to the success callback
		// is the json data object returned by the server

		//alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
		//	'\n\nThe output div should have already been updated with the responseText.');
		
		if(responseText == "1"){
			$('#output').html("<div class='alert alert-success'><p>Course successfully inserted</p></div>");
			$('#myForm').trigger("reset");
		}else{
			$('#output').html("<div class='alert alert-danger'><p>Something went wrong..</p></div>");
		}
	}
    </script> 
  </body>
</html>

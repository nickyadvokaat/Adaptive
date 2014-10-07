<?php
require ("config.php");

if (!empty($_POST)) {

	// Ensure that the user fills out fields

	if (empty($_POST['username'])) {
		die("Please enter a username.");
	}

	if (empty($_POST['password'])) {
		die("Please enter a password.");
	}

	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		die("Invalid E-Mail Address");
	}

	// Check if the username is already taken

	$query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username 
        ";
	$query_params = array(
		':username' => $_POST['username']
	);
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}

	catch(PDOException $ex) {
		die("Failed to run query: " . $ex->getMessage());
	}

	$row = $stmt->fetch();
	if ($row) {
		die("This username is already in use");
	}

	$query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        ";
	$query_params = array(
		':email' => $_POST['email']
	);
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}

	catch(PDOException $ex) {
		die("Failed to run query: " . $ex->getMessage());
	}

	$row = $stmt->fetch();
	if ($row) {
		die("This email address is already registered");
	}

	// Add row to database

	$query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email,
				admin			
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email,
				:admin
            ) 
        ";

	// Security measures

	$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
	$password = hash('sha256', $_POST['password'] . $salt);
	$admin = 0;
	for ($round = 0; $round < 65536; $round++) {
		$password = hash('sha256', $password . $salt);
	}

	$query_params = array(
		':username' => $_POST['username'],
		':password' => $password,
		':salt' => $salt,
		':email' => $_POST['email'],
		':admin' => $admin
	);
	try {
		$stmt = $db->prepare($query);
		$result = $stmt->execute($query_params);
	}

	catch(PDOException $ex) {
		die("Failed to run query: " . $ex->getMessage());
	}

	header("Location: index.php");
	die("Redirecting to index.php");
}

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
          <a class="navbar-brand" href="../adaptive">AWS</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="../adaptive">Home</a></li>
          </ul>          
        </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>
	
    <div class="jumbotron">
	
		<h1>Register</h1> <br /><br />

		<form role="form" action="register.php" method="post">
			<div class="form-group">
			<label for="exampleInputEmail1">Name</label>
			<input type="text" class="form-control" name="username" value="" placeholder="Enter name">
		  </div>
		  <div class="form-group">
			<label for="exampleInputEmail1">Email address</label>
			<input type="email" class="form-control" name="email" value="" placeholder="Enter email">
		  </div>
		  <div class="form-group">
			<label for="exampleInputPassword1">Password</label>
			<input type="password" class="form-control" name="password" value="" placeholder="Password">
		  </div>
		   <button type="submit" class="btn btn-lg btn-primary" value="Register">Submit</button>
		</form>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </body>
</html>

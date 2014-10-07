<?php
require ("config.php");

$submitted_username = '';

if (!empty($_POST)) {
	$query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                email 
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

	$login_ok = false;
	$row = $stmt->fetch();
	if ($row) {
		$check_password = hash('sha256', $_POST['password'] . $row['salt']);
		for ($round = 0; $round < 65536; $round++) {
			$check_password = hash('sha256', $check_password . $row['salt']);
		}

		if ($check_password === $row['password']) {
			$login_ok = true;
		}
	}

	if ($login_ok) {
		unset($row['salt']);
		unset($row['password']);
		$_SESSION['user'] = $row;
		header("Location: content.php");
		die("Redirecting to: content.php");
	}
	else {
		$submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
	}
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
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
		    <li><a href="register.php">Register</a></li>
			<li class="divider-vertical"></li>
            <li class="dropdown">
            <a class="dropdown-toggle" href="#" data-toggle="dropdown">Log In <strong class="caret"></strong></a>
            <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                <form action="index.php" method="post"> 
                    Username:<br /> 
                    <input type="text" name="username" value="<?php echo $submitted_username; ?>" /> 
                    <br /><br /> 
                    Password:<br /> 
                    <input type="password" name="password" value="" /> 
                    <br /><br /> 
                    <input type="submit" class="btn btn-primary" value="Login" style="width:100%; margin-bottom:10px"/> 
                </form> 
            </div>
          </li>
          </ul>
        </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

	
    <div class="jumbotron">
	
		<?php

if (isset($submitted_username) && $submitted_username != "") {
	echo '
				<div class="alert alert-danger" role="alert">
					<strong>Incorrect login.</strong> Sorry ' . $submitted_username . ', your password is incorrect
				</div>';
}

?>  
        <h1>Welcome</h1>
        <p>Learn about courses. Please create an acount and log in.</p>        

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

  </body>
</html>


<?php

	include 'database_connection.php';

	session_start();

	$message  = '';

	if(isset($_SESSION['user_id'])){
		header('location:index.php');
	}

	if(isset($_POST["register"])){
		$username = trim($_POST["username"]);
		$password = trim($_POST["password"]);

		$check_query = "SELECT * FROM tbl_twitter_user WHERE username = :username";
		$statement = $connect->prepare($check_query);
		$check_data = array(':username' => $username);
		if($statement->execute($check_data)){
			if($statement->rowCount() > 0)
			{
				$message .= '<p><label>Username already taken</label></p>';
			}else{
				if(empty($username)){
					$message .= '<p><label>Username is required</label></p>';
				}
				if(empty($password)){
					$message .= '<p><label>Password is required</label></p>';
				}else{
					if($password != $_POST["confirm_password"]){
						$message .= '<p><label>Password does not match</label></p>';
					}
				}

				if($message == ''){
					$data = array(
						':username' => $username,
						':password' => password_hash($password, PASSWORD_DEFAULT)
					);
					$query = "INSERT INTO tbl_twitter_user(username, password)VALUES(:username, :password)";
					$statement = $connect->prepare($query);

					if($statement->execute($data)){
						$message .= '<p class="text-success bg-light"><label>Registration Completed</label></p>';
					}
				}
			}
		}


	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register || Ajax Based Follow Unfollow System</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<br>
		<h3 align="center">Twitter Like Ajax Based Follow Unfollow System in PHP</h3>
		<br>
		<div class="card">
			<div class="card-header"><h4>Register</h4></div>
			<div class="card-body">
				<form action="" method="post" id="register-form">
					<span class="text-danger"><?php echo $message; ?></span>
					<div class="form-group">
						<label for="">Enter Username</label>
						<input type="text" name="username" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Enter Password</label>
						<input type="password" name="password" id="password" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Re-enter Password</label>
						<input type="password" name="confirm_password" class="form-control" required>
					</div>
					<div class="form-group">
						<input type="submit" name="register" id="registerBtn" class="btn btn-info" value="Register">
					</div>
					<div align="center">
						<a href="login.php">Login</a>
					</div>
				</form>
			</div>
		</div>

	</div>
</body>
</html>
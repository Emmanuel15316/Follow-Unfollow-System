<?php

	include 'database_connection.php';

	session_start();

	$message  = '';

	if(isset($_SESSION['user_id'])){
		header('location:index.php');
	}

	if(isset($_POST["login"])){
		
		$check_query = "SELECT * FROM tbl_twitter_user WHERE username = :username";
		$statement = $connect->prepare($check_query);
		$statement->execute([':username' => $_POST["username"]]);
		$count = $statement->rowCount();

		if($count > 0)
		{
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			foreach($result as $row) {
				if(password_verify($_POST['password'], $row['password']))
				{
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['username'] = $row['username'];
					header('location:index.php');
				}else{
					$message = '<label>Wrong Password</label>';
				}
			}
		}else
		{
			$message = '<label>Wrong Username</label>';
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Login | Ajax Based Follow Unfollow System</title>
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
			<div class="card-header"><h4>Login</h4></div>
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
						<input type="submit" name="login" class="btn btn-info" value="Login">
					</div>
					<div align="center">
						<a href="register.php">Register</a>
					</div>
				</form>
			</div>
		</div>

	</div>
</body>
</html>
<?php
//Profile.php

include 'database_connection.php';

session_start();

if(!isset($_SESSION['user_id'])){
	header('location:login.php');
}

$message = '';

if(isset($_POST['edit_profile'])){
	$file_name = '';
	if(isset($_POST['profile_image'])){
		$file_name = $_POST['profile_image'];
	}
	if($_FILES['profile_image']['name'] != ''){
		if($file_name != ''){
			unlink('images/'.$file_name);
		}
		$image_name = explode(".", $_FILES['profile_image']['name']);
		$extension = end($image_name);
		$temporary_location = $_FILES['profile_image']['tmp_name'];
		$file_name = rand().'.'.$extension;
		$location = 'images/'.$file_name;
		move_uploaded_file($temporary_location, $location);
	}
	$check_query = "SELECT * FROM tbl_twitter_user WHERE username = :username AND user_id != :user_id ";
	$statement = $connect->prepare($check_query);
	$statement->execute(
		array(
			':username' => trim($_POST["username"]),
			':user_id' => $_SESSION["user_id"]
		)
	);
	$total_row = $statement->rowCount();
	if($total_row > 0){
		$message = '<div class="alert alert-danger alert-dismissible">Username Already Exists</div>';
	}else{
		$data = array(
			':username' => trim($_POST["username"]),
			':name' => trim($_POST['name']),
			':profile_image' => $file_name,
			':bio' => trim($_POST["bio"]),
			':user_id' => $_SESSION["user_id"]
		);
		if($_POST['password'] != ''){
			$data[] = array(
				':password' => password_hash($_POST["password"], PASSWORD_DEFAULT)
			);
			$query = "UPDATE tbl_twitter_user SET username = :username, password = :password, name = :name, profile_image = :profile_image, bio = :bio WHERE user_id = :user_id";
		}else{
			$query = 'UPDATE tbl_twitter_user SET username = :username, name = :name, profile_image = :profile_image, bio = :bio WHERE user_id = :user_id';
		}
		$statement = $connect->prepare($query);
		if($statement->execute($data)){
			$message = '<div class="alert alert-success alert-dismissible">Profile Updated</div>';
			header('location: logout.php');
		}
	}
}

$query = "SELECT * FROM tbl_twitter_user WHERE user_id = '".$_SESSION['user_id']."'";
$statement = $connect->prepare($query);
$statement->execute();

$result = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Twitter Like Follow Unfollow System</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<?php
			include 'menu.php';
		?>
		<br>
		<div class="row">
			<div class="col-md-3">
				
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							Edit Profile
						</h3>
					</div>
					<div class="card-body">
						<?php
						foreach($result as $row){
							echo $message;
						?>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for=""><h6>Username</h6></label>
								<input type="text" name="username" id="username" pattern="^[a-zA-Z0-9_.-]*$" required class="form-control" value="<?= $row['username'];?>">
							</div>
							<div class="form-group">
								<label><h6>Password</h6></label>
								<input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
							</div>
							<div class="form-group">
								<label for=""><h6>Name</h6></label>
								<input type="text" name=" name" id="name" required class="form-control" value="<?= $row["name"]; ?>">
							</div>
							<div class="form-group">
								<label for=""><h6>Profile Image</h6></label>
								<input type="file" name="profile_image" id="profile_image" accept="image/*" class="form-control" >
								<?php
								if($row["profile_image"] != ''){
									echo '<img src="images/'.$row["profile_image"].'" class="img-thumbnail" width="150" >';
									echo '<input type="hidden" name="profile_image" value="'.$row["profile_image"].'" >';
								}
								?> 
							</div>
							<div class="form-group">
								<label><h6>Short Bio</h6></label>
								<textarea name="bio" id="bio" class="form-control"><?php echo $row["bio"];?></textarea>
							</div>
							<div class="form-group">
								<input type="submit" name="edit_profile" id="edit_profile" class="btn btn-primary" value="Save">
							</div>
						</form>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				
			</div>
		</div>
	</div>
</body>
</html>
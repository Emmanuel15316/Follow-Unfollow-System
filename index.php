<?php

	include 'database_connection.php';

	session_start();

	if(!isset($_SESSION['user_id']))
	{
		header('location: login.php');
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ajax Based Follow Unfollow System</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="fontawesome/js/all.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="js/popper.min.js"></script>
	<script type="text/javascript" src="typeahead/bootstrap3-typeahead.min.js"></script>
</head>
<body>
	<div class="container">
		<?php 
			include 'menu.php';
		?>
		<div class="row mt-3">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header bg-light">
						<h4 class="card-title">Start Writing Here</h4>		
					</div>
					<div class="card-body">
						<form method="post" id="post_form" action="" enctype="multipart/form-data">
							<div class="form-group">
								<input type="text" name="post_title" id="post_title" class="form-control" maxlength="60" placeholder="(Optional) Write Your Post Title (0 - 60 words)" style="display: none;">
							</div>

							<div class="form-group" id="dynamic_field">
								<textarea name="post_content" id="post_content" maxlength="200" class="form-control" placeholder="Write Something Here"></textarea>
							</div>

							<div class="form-group">
								<input type="file" name="image" id="uploadFile" class="form-control" style="display: none;">
							</div>
							
							<div class="form-group" style="float: right;">
								<input type="hidden" name="action" value="insert" />
								<button class="btn btn-warning" type="button" id="title_button">Add Post Title</button>
								<button class="btn btn-success" type="button" id="fileBtn">Add Photo</button>
								<input type="submit" name="share_post" id="share_post" class="btn btn-info" title="Share Post Now" value="Share">
							</div>
						</form>
					</div>
				</div>

				<div class="card mt-3">
					<div class="card-header bg-light">
						<h4 class="card-title">Trending Now</h4>
					</div>
					<div class="card-body">
						<div id="post_list">
							Loading...
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="card">
					<div class="card-header bg-light">
						<h4 class="card-title">User List</h4>
					</div>
					<div class="card-body">
						<div id="user_list">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
	include 'jquery.php';
?>
</body>
</html>
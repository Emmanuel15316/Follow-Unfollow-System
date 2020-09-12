<?php

	include 'database_connection.php';

	session_start();

	if(!isset($_SESSION['user_id']))
	{
		header('location: login.php');
	}

	$query = "SELECT * FROM tbl_samples_post INNER JOIN tbl_twitter_user ON tbl_twitter_user.user_id = tbl_samples_post.user_id WHERE tbl_twitter_user.username = '".$_GET["data"]."' GROUP BY tbl_samples_post.post_id ORDER BY tbl_samples_post.post_id DESC";
	$statement = $connect->prepare($query);
	$statement->execute();

	$total_row = $statement->rowCount();

	$user_id = Get_user_id($connect, $_GET["data"]);



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>User's Wall</title>
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
			<?php
				if($total_row > 0){
					$result = $statement->fetchAll();
			?>
			<div class="col-md-9">
				<div class="card">
					<div class="card-header bg-light">
						<div class="row">
							<div class="col-md-6">
								<h4 class="card-title"><?php echo '<b>'.$_GET["data"].'</b>';?> Post Details</h4>
							</div>		
							<div class="col-md-6" align="right">
								<?php
								if($user_id != $_SESSION["user_id"]){
									echo make_follow_button($connect, $user_id, $_SESSION["user_id"]);
								}
								?>
							</div>
						</div>
					</div>
					<div class="card-body">
						<?php
							foreach($result as $row){
								$profile_image = '';
								if($row["profile_image"] != ''){
									$profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive" />';
								}else{
									$profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive" />';
								}

								$repost = 'disabled';

								if($row['user_id'] != $_SESSION['user_id']){
									$repost = '';
								}

								echo '<div class="jumbotron" style="padding: 24px 30px 24px 30px">
									<div class="row">
										<div class="col-md-2">
											'.$profile_image.'
										</div>
										<div class="col-md-10">
											<h4><b>@'.$row["username"].'</b></h4>
											<p>'.$row["post_content"].' <br><br>
											<button type="button" name="post_comment" class="btn btn-link post_comment" id="'.$row["post_id"].'" data-user_id="'.$row["user_id"].'"> '.count_comment($connect, $row["post_id"]).' Comment</button>
											<button type="button" class="btn btn-danger repost" data-post_id="'.$row["post_id"].'" '.$repost.'> '.count_comment($connect, $row["post_id"]).'&nbsp;&nbsp;<i class="fas fa-retweet"></i>
											</button>
											<button type="button" class="btn btn-link like_button" data-post_id="'.$row["post_id"].'"> '.count_total_post_like($connect, $row["post_id"]).' <i class="fas fa-thumbs-up"></i> Like </button>
											</p>
											<div id="comment_form'.$row["post_id"].'" style="display:"none">
												<span id="old_comment'.$row["post_id"].'"></span>
												<div class="form-group">
													<textarea name="comment" class="form-control" id="comment'.$row["post_id"].'"></textarea>
												</div>
												<div class="form-group" align="right">
													<button type="button" name="submit_comment" class="btn btn-primary btn-xs submit_comment" data-post_id="'.$row["post_id"].'">Comment</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								';			
							}
						?>
					</div>
				</div>

			</div>
			
			<div class="col-md-3">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?php echo $_GET["data"]; ?>'s Followers</h4>
					</div>
					<div class="card-body">
						<?php

						$follower_query = "SELECT * FROM tbl_twitter_user INNER JOIN tbl_follow ON tbl_follow.receiver_id = tbl_twitter_user.user_id WHERE tbl_follow.sender_id = '".$user_id."'";
						$sql_stmt = $connect->prepare($follower_query);
						$sql_stmt->execute();
					
						$follower_result = $sql_stmt->fetchAll(PDO::FETCH_ASSOC);

						foreach($follower_result as $follower_row){

							$profile_image = '';

							if($follower_row['profile_image'] != ''){
								$profile_image = '<img src="images/'.$follower_row["profile_image"].'" class="img-thumbnail img-responsive rounded-circle">';
							}else{
								$profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive rounded-circle">';
							}
							
							echo '
							<div class="row bg-light p-1 mt-1">
								<div class="col-md-4">
									'.$profile_image.'
								</div>
								<div class="col-md-8">
									<h5><b>@<a href="wall.php?data='.$follower_row["username"].'">'.$follower_row["username"].'</a></b></h5>
								</div>
							</div>
							';
						}
						
						?>
					</div>
				</div>
			</div>
			<?php 
				}
				else{
					echo '<h3 align="center">No Post Found</h3>';
				}
			?>
		</div>
	</div>
</body>
</html>
<?php
	include 'jquery.php';
?>
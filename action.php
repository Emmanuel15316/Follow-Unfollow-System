<?php

include 'database_connection.php';

session_start();

if(isset($_POST['action'])){

	$output = '';

	if($_POST['action'] == 'insert'){
		if(isset($_FILES['image']) && isset($_POST["post_title"])){

			if(!empty($_POST["post_content"])){

			$user_id = $_SESSION["user_id"];
			$post_title = trim(stripslashes(htmlspecialchars($_POST["post_title"])));
			$post_content = trim(stripslashes(htmlspecialchars($_POST["post_content"])));
			$post_datetime = date("Y-m-d").' '.date("H:i:s", strtotime(date('h:i:sa')));
			$image = $_FILES['image']['name'];

			$folder = 'uploads/';
			if(isset($_FILES['image']['name']) && ($_FILES['image']['name'] != "")){
				$newImage = $folder.$_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $newImage);
			}

			$query = "INSERT INTO tbl_samples_post(user_id, post_title, post_content, post_image, post_datetime)VALUES(:user_id, :post_title, :post_content, :post_image, :post_datetime)";
			$statement = $connect->prepare($query);
			$statement->execute(['user_id'=>$user_id, 'post_title'=>$post_title, 'post_content'=>$post_content,'post_image'=>$image, 'post_datetime'=>$post_datetime]);
			echo 'Post saved';
			}else{
				echo 'You need to type something';
			}

		}else if(isset($_FILES['image']) && !isset($_POST["post_title"])){
			if(!empty($_POST["post_content"])){

			$user_id = $_SESSION["user_id"];
			$post_content = $_POST["post_content"];
			$post_datetime = date("Y-m-d").' '.date("H:i:s", strtotime(date('h:i:sa')));
			$image = $_FILES['image']['name'];

			$folder = 'uploads/';
			if(isset($_FILES['image']['name']) && ($_FILES['image']['name'] != "")){
				$newImage = $folder.$_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $newImage);
			}

			$query = "INSERT INTO tbl_samples_post(user_id, post_content, post_image, post_datetime)VALUES(:user_id, :post_content, :post_image, :post_datetime)";
			$statement = $connect->prepare($query);
			$statement->execute(['user_id'=>$user_id,'post_content'=>$post_content,'post_image'=>$image, 'post_datetime'=>$post_datetime]);
			echo 'Post saved';
			}else{
				echo 'You need to type something';
			}
		}else if(!isset($_FILES['image']) && isset($_POST["post_title"])){
			if(!empty($_POST["post_content"])){

				$user_id = $_SESSION["user_id"];
				$post_title = trim(stripslashes(htmlspecialchars($_POST["post_title"])));
				$post_content = $_POST["post_content"];
				$post_datetime = date("Y-m-d").' '.date("H:i:s", strtotime(date('h:i:sa')));

				$query = "INSERT INTO tbl_samples_post(user_id, post_title, post_content, post_datetime)VALUES(:user_id, :post_title, :post_content, :post_datetime)";
				$statement = $connect->prepare($query);
				$statement->execute(['user_id'=>$user_id,'post_title'=>$post_title, 'post_content'=>$post_content, 'post_datetime'=>$post_datetime]);
				echo 'Post saved';
			}else{
				echo 'You need to type something';
			}
		}else{
			$user_id = $_SESSION["user_id"];
			$post_content = $_POST["post_content"];
			$post_datetime = date("Y-m-d").' '.date("H:i:s", strtotime(date('h:i:sa')));

			if(!empty(trim($_POST["post_content"]))){
				$query = "INSERT INTO tbl_samples_post(user_id, post_content, post_datetime)VALUES(:user_id, :post_content, :post_datetime)";
				$statement = $connect->prepare($query);
				$statement->execute(['user_id'=>$user_id,'post_content'=>$post_content,'post_datetime'=>$post_datetime]);
				echo 'Post saved';
			}
		}
		$notification_query = "SELECT receiver_id FROM tbl_follow WHERE sender_id = '".$_SESSION["user_id"]."'";
		$statement = $connect->prepare($notification_query);
		
		$statement->execute();

		$notification_result = $statement->fetchAll();

		foreach($notification_result as $notification_row){
			$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> has shared a new post';
			$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$notification_row['receiver_id']."', '".$notification_text."', 'no')";

			$statement = $connect->prepare($insert_query);

			$statement->execute();
		}
	}

	if($_POST['action'] == 'fetch_post'){

		$query = "SELECT * FROM tbl_samples_post INNER JOIN tbl_twitter_user ON tbl_twitter_user.user_id = tbl_samples_post.user_id LEFT JOIN tbl_follow ON tbl_follow.sender_id = tbl_samples_post.user_id WHERE tbl_follow.receiver_id = '".$_SESSION["user_id"]."' OR tbl_samples_post.user_id = '".$_SESSION["user_id"]."' GROUP BY tbl_samples_post.post_id ORDER BY tbl_samples_post.post_id DESC";

		$statement = $connect->prepare($query);
		$statement->execute();

		$result = $statement->fetchAll();
		$total_row = $statement->rowCount();

		if($total_row > 0)
		{
			foreach($result as $row){
				$profile_image = '';
				if($row['profile_image'] != ''){
					$profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive" >';
				}else{
					$profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive mb-2" />';
				}
				if($row['post_image'] != ''){
					$post_image = '<img src="uploads/'.$row["post_image"].'" class="img-responsive mb-1" height="350px" width="100%">';
				}else{
					$post_image = '';
				}
				if($row['post_title'] != ''){
					$post_title = $row['post_title'];
				}else{
					$post_title = '';
				}
				$repost = 'disabled';
				if($row['user_id'] != $_SESSION['user_id']){
					$repost = '';
				}
				$output .= '
				<div class="jumbotron bg-light p-3">
				<div class="row">
					<div class="col-md-2">
						'.$profile_image.'
					</div>
					<div class="col-md-8">
						<h6><a href="index.php"><b>@'.$row["username"].'</b></a></h6>
						<h5>'.$post_title.'</h5>
						<p>'.$row["post_content"].'<br>'.$post_image.'<br>
						<button type="button" class="btn btn-link badge-primary badge-sm post_comment" id="'.$row["post_id"].'" data-user_id="'.$row["user_id"].'">'.count_comment($connect, $row["post_id"]).' Comment</button>
						<button type="button" class="btn btn-danger repost" data-post_id="'.$row["post_id"].'" '.$repost.'><i class="fas fa-retweet text-white"></i>&nbsp;&nbsp;'.count_retweet($connect, $row["post_id"]).'</button>
						<button type="button" class="btn btn-link btn-light like_button" data-post_id="'.$row["post_id"].'"><i class="fas fa-thumbs-up"></i>&nbsp;Like '.count_total_post_like($connect, $row["post_id"]).'</button>
						</p>
						<div id="comment_form'.$row["post_id"].'" style="display:none;"><span id="old_comment'.$row["post_id"].'"></span>
							<div class="form-group">
								<textarea name="comment" id="comment'.$row["post_id"].'" class="form-control"></textarea>
							</div>
							<div class="form-group" align="right">
								<button type="button" name="submit_comment" class="btn btn-primary btn-sm submit_comment">Comment</button>
							</div>
						</div>
					</div>
				</div>
				</div>
				';
			}
		}else{
			$output .= '<h5>No Post Found</h5>';

		}
		echo $output;
	}

	if($_POST['action'] == 'fetch_user'){
		$query = "SELECT * FROM tbl_twitter_user WHERE user_id != '".$_SESSION['user_id']."' ORDER BY user_id DESC";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row){
			$profile_image = '';
			if($row['profile_image'] != ''){
				$profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail img-responsive" >';
			}else{
				$profile_image = '<img src="images/user.png" class="img-thumbnail img-responsive" />';
			}
			$output .= '
				<div class="row">
					<div class="col-md-4">
						'.$profile_image.'
					</div>
					<div class="col-md-8">
						<h5><b>@'.$row["username"].'</b></h5>
						'.make_follow_button($connect, $row["user_id"], $_SESSION["user_id"]).'
						<span class="badge badge-success">'.$row["follower_number"].' Followers</span>
					</div>
				</div>
				<hr>
			';
		}
		echo $output;
	}

	if($_POST['action'] == 'follow'){
		$query = "INSERT INTO tbl_follow(sender_id, receiver_id)VALUES('".$_POST["sender_id"]."', '".$_SESSION["user_id"]."')"	;
		$statement = $connect->prepare($query);
		if($statement->execute()){
			$sub_query = "UPDATE tbl_twitter_user SET follower_number=follower_number+1 WHERE user_id = '".$_POST["sender_id"]."'";
			$statement = $connect->prepare($sub_query);
			$statement->execute();

			$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> started following you';

			$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$_POST["sender_id"]."', '".$notification_text."', 'no')";
				
			$statement = $connect->prepare($insert_query);

			$statement->execute();
			
		}
	}

	if($_POST['action'] == 'unfollow'){
		$query = "DELETE FROM tbl_follow WHERE sender_id = '".$_POST["sender_id"]."' AND receiver_id = '".$_SESSION["user_id"]."' ";
		$statement = $connect->prepare($query);
		if($statement->execute()){
			$sub_query = "UPDATE tbl_twitter_user SET follower_number = follower_number - 1 WHERE user_id = '".$_SESSION["user_id"]."'";


				$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> unfollowed  you';

				$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$_POST["sender_id"]."', '".$notification_text."', 'no')";
				
				$statement = $connect->prepare($insert_query);

				$statement->execute();
		}
	}

	if($_POST['action'] == 'submit_comment'){
		$data = array(
			':post_id' 		=> $_POST["post_id"],
			':user_id' 		=> $_SESSION["user_id"],
			':comment' 		=> $_POST["comment"],
			':timestamp' 	=> date("Y-m-d") . ' '.date("H:i:s", STRTOTIME(date('h:i:sa')))

		);
		$query = "INSERT INTO tbl_comment(post_id, user_id, comment, timestamp)VALUES(:post_id, :user_id, :comment, :timestamp)
		";
		$statement = $connect->prepare($query);
		$statement->execute($data);

		$notification_query = "SELECT user_id, post_content FROM tbl_samples_post WHERE post_id = '".$_POST["post_id"]."'";

		$statement = $connect->prepare($notification_query);

		$statement->execute();

		$notification_result = $statement->fetchAll();

		foreach($notification_result as $notification_row){
			$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> commented on your post - "'.strip_tags(substr($notification_row["post_content"], 0, 30)).'..."';

			$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$notification_row['user_id']."','".$notification_text."','no')";

			$statement = $connect->prepare($insert_query);

			$statement->execute();
		}
	}

	if($_POST['action'] == 'fetch_comment'){

		$post_id = $_POST["post_id"];

		$query = "SELECT * FROM tbl_comment INNER JOIN tbl_twitter_user ON tbl_twitter_user.user_id = tbl_comment.user_id WHERE post_id = '$post_id' ORDER BY comment_id ASC";
		$statement = $connect->prepare($query);
		$output = '';

		if($statement->execute()){
			$result = $statement->fetchAll();
			foreach($result as $row){
				$profile_image = '';
				if($row['profile_image'] != ''){
					$profile_image = '<img src="images/'.$row["profile_image"].'" class="img-thumbnail rounded-circle img-responsive" />';

				}else{
					$profile_image = '<img src="images/user.png" class="img-thumbnail rounded-circle img-responsive" />';
				}

				$output .= '
					<div class="row">
						<div class="col-md-2">
						'.$profile_image.'
						</div>
						<div class="col-md-10" style="; padding-left:0">
							<small><b>@'.$row["username"].'</b><br>
								'.$row["comment"].'
							</small>
						</div>
					</div>

				';
			}
		}

		echo $output;
	
	}

	if($_POST['action'] == 'repost'){

		$post_id = $_POST["post_id"];
		$query = "SELECT * FROM tbl_repost WHERE post_id ='".$_POST["post_id"]."' AND user_id = '".$_SESSION["user_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$total_row = $statement->rowCount();

		if($total_row > 0){
			echo "You have already retweeted this post";
		}else{
			$query1 = "INSERT INTO tbl_repost(post_id, user_id) VALUES('".$_POST["post_id"]."', '".$_SESSION["user_id"]."')";
			$statement = $connect->prepare($query1);
			if($statement->execute()){
				$query_two = "SELECT * FROM tbl_samples_post WHERE post_id = '".$_POST["post_id"]."'";
				$statement = $connect->prepare($query_two);

				if($statement->execute())
				{
					$result = $statement->fetchAll();
					foreach($result as $row){
						$post_content = $row["post_content"];
					}
					$user_id = $_SESSION["user_id"];
					$post_date = date("Y-m-d") .' '. date("H:i:s", STRTOTIME(date("h:i:sa")));

					$last_query = "INSERT INTO tbl_samples_post(user_id, post_content, post_datetime) VALUES(:user_id, :content, :post_datetime)";
					$statement = $connect->prepare($last_query);
					if($statement->execute(['user_id'=>$user_id, 'content'=>$post_content, 'post_datetime'=>$post_date])){
						echo 'Tweet successfully reposted';

						$notification_query = "SELECT user_id, post_content FROM tbl_samples_post WHERE post_id = '".$_POST["post_id"]."'";

						$statement = $connect->prepare($notification_query);

						$statement->execute();

						$notification_result = $statement->fetchAll();

						foreach($notification_result as $notification_row){
							$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> retweeted your post - "'.strip_tags(substr($notification_row["post_content"], 0, 30)).'..."';

							$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$notification_row['user_id']."', '".$notification_text."', 'no')";
							$statement = $connect->prepare($insert_query);

							$statement->execute();
						}
					}
				}
			}
		}
	}

	if($_POST["action"] == "like"){
		$query = "SELECT * FROM tbl_like WHERE post_id = '".$_POST["post_id"]."' AND user_id = '".$_SESSION["user_id"]."'";
		$statement = $connect->prepare($query);
		$statement->execute();

		$total_row = $statement->rowCount();

		if($total_row > 0){
			echo 'You have already liked this post';
		}else{
			$ins_query = "INSERT INTO tbl_like(user_id, post_id)VALUES('".$_SESSION["user_id"]."','".$_POST["post_id"]."') ";
			$statement = $connect->prepare($ins_query);
			
			if($statement->execute()){
				echo 'Liked';

				$notification_query = "SELECT user_id, post_content FROM tbl_samples_post WHERE post_id = '".$_POST["post_id"]."'";

				$statement = $connect->prepare($notification_query);

				$statement->execute();

				$notification_result = $statement->fetchAll();

				foreach($notification_result as $notification_row)
				{
					$notification_text = '<b>'.Get_username($connect, $_SESSION["user_id"]).'</b> retweeted your post - "'.strip_tags(substr($notification_row["post_content"], 0, 30)).'..."';

					$insert_query = "INSERT INTO tbl_notification(notification_receiver_id, notification_text, read_notification)VALUES('".$notification_row['user_id']."', '".$notification_text."', 'no')";
				
					$statement = $connect->prepare($insert_query);

					$statement->execute();
				}
			}
		}
	}

	if($_POST['action'] == "like_user_list"){

		$query = "SELECT * FROM tbl_like INNER JOIN tbl_twitter_user ON tbl_twitter_user.user_id = tbl_like.user_id WHERE tbl_like.post_id = '".$_POST["post_id"]."'";

		$statement = $connect->prepare($query);

		$statement->execute();

		$result = $statement->fetchAll();

		foreach($result as $row){
			$output = '<p>'.$row["username"].'</p>';
		}

		echo $output;
	}

	if($_POST['action'] == "update_notification_status"){

		$query = "UPDATE tbl_notification SET read_notification='yes' WHERE notification_receiver_id = '".$_SESSION["user_id"]."'";

		$statement = $connect->prepare($query);

		$statement->execute();
	}

	if($_POST["action"] == "search_user"){
		$query = "SELECT username, profile_image FROM tbl_twitter_user WHERE username LIKE '%".$_POST["query"]."%' AND user_id != '".$_SESSION["user_id"]."'";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();

		foreach($result as $row){
			$data[] = $row["username"];
		}
		echo json_encode($data);
	}

}

function Get_username($connect, $user_id){
	$query = "SELECT username FROM tbl_twitter_user WHERE user_id = '".$user_id."'";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	foreach($result as $row){
		return $row["username"];
	}
}
?>
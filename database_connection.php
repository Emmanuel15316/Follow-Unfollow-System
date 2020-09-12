<?php
	
//Database Connection
$connect = new PDO("mysql:host=localhost;dbname=testing","root","");

function count_notification($connect, $receiver_id){
	$query = "SELECT COUNT(notification_id) as total FROM tbl_notification WHERE notification_receiver_id = '".$receiver_id."' AND read_notification = 'no' ";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	foreach($result as $row){
		return $row["total"];
	}
}

function Load_notification($connect, $receiver_id){
	$query = "SELECT * FROM tbl_notification WHERE notification_receiver_id='".$receiver_id."' ORDER BY notification_id DESC";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$total_row = $statement->rowCount();

	$output = '';

	if($total_row > 0){
		foreach($result as $row){
			$output .= '<li><a href="#" class="nav-link text-white">'.$row["notification_text"].'</a></li>';
		}
	}
	return $output;
}

function count_comment($connect, $post_id){
	$query = "SELECT * FROM tbl_comment WHERE post_id = '".$post_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_retweet($connect, $post_id){
	$query = "SELECT * FROM tbl_repost WHERE post_id = '".$post_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
					
	return $statement->rowCount();
}

function count_total_post_like($connect, $post_id){
	$query = "SELECT * FROM tbl_like WHERE post_id = '".$post_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();

	return $statement->rowCount();
}

function Get_user_id($connect, $username){
	$query = "SELECT * FROM tbl_twitter_user WHERE username = '".$username."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();

	foreach($result as $row){
		return $row["user_id"];
	}
}

function make_follow_button($connect, $sender_id, $receiver_id){
	$query = "SELECT * FROM tbl_follow WHERE sender_id = '".$sender_id."' AND receiver_id = '".$receiver_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$output = '';
	$total_row = $statement->rowCount();

	if($total_row > 0){
		$output = '<button type="button" name="follow_button" class="btn btn-warning action_button" data-action="unfollow" data-sender_id="'.$sender_id.'">Following</button>';
	}else{
		$output = '<button type="button" name="follow_button" class="btn btn-warning action_button" data-action="follow" data-sender_id="'.$sender_id.'"><i class="glyphicon glyphicon-plus"></i>Follow</button>';
	}
	return $output;
}

?>
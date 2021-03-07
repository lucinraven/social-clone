<html>

<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="../assets/css/stylesheet.css">
	<script src="https://kit.fontawesome.com/035ed2373e.js" crossorigin="anonymous"></script>
</head>

<body>
	<!--INTERNAL CSS-->
	<style type="text/css">
		form {
			display: flex;
			flex-direction: row;
			position: absolute;
			top: 0;
		}

		form .like_value {
			margin-right: 2vw;
			color: #ffff;
		}

		form button {
			background-color: transparent;
			border: none;
			color: #99aab5;
		}

		form button:focus {
			outline: none;
		}

		.liked {
			color: #7289da;
		}
	</style>

	<?php
	//to call config, and classes: user, post, notification
	require '../private/config/config.php';
	include("../private/includes/classes/User.php");
	include("../private/includes/classes/Post.php");
	include("../private/includes/classes/Notification.php");

	//to get the userLoggedin
	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];

		$user_query = $con->prepare("SELECT * FROM users WHERE username=?");
		$user_query->bind_param("s", $userLoggedIn);
		$user_query->execute();

		$result = $user_query->get_result();
		$user = $result->fetch_assoc();
	} else {
		header("Location: register.php");
	}

	//Get id of post
	if (isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$get_likes = $con->prepare("SELECT likes, added_by FROM posts WHERE id=?");
	$get_likes->bind_param("i", $post_id);
	$get_likes->execute();

	$result = $get_likes->get_result();
	$row = $result->fetch_assoc();
	$total_likes = $row['likes'];
	$user_liked = $row['added_by'];

	$user_details_query = $con->prepare("SELECT * FROM users WHERE username=?");
	$user_details_query->bind_param("s", $user_liked);
	$user_details_query->execute();

	$result = $user_details_query->get_result();
	$row = $result->fetch_assoc();

	//Like button
	if (isset($_POST['like_button'])) {
		$total_likes++;

		$query = $con->prepare("UPDATE posts SET likes=? WHERE id=?");
		$query->bind_param("ii", $total_likes, $post_id);
		$query->execute();

		$insert_query = $con->prepare("INSERT INTO likes VALUES('', ?, ?)");
		$insert_query->bind_param("si", $userLoggedIn, $post_id);
		$insert_query->execute();

		//Insert Notification
		if ($user_liked != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_liked, "like");
		}
	}
	//Unlike button
	if (isset($_POST['unlike_button'])) {
		$total_likes--;

		$query = $con->prepare("UPDATE posts SET likes=? WHERE id=?");
		$query->bind_param("ii", $total_likes, $post_id);
		$query->execute();

		$delete_query = $con->prepare("DELETE FROM likes WHERE username=? AND post_id=?");
		$delete_query->bind_param("si", $userLoggedIn, $post_id);
		$delete_query->execute();
	}

	//Check for previous likes
	$check_query = $con->prepare("SELECT * FROM likes WHERE username=? AND post_id=?");
	$check_query->bind_param("si", $userLoggedIn, $post_id);
	$check_query->execute();

	$result = $check_query->get_result();
	$num_rows = $result->num_rows;

	if ($num_rows > 0) {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
		<div class="like_value">
					' . $total_likes . '
				</div>
				<button type="submit" class="comment_like liked" name="unlike_button"><i class="fas fa-thumbs-up"></i></button>
				
			</form>
		';
	} else {
		echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
		<div class="like_value">
					' . $total_likes . '
				</div>
				<button type="submit" class="comment_like" name="like_button"><i class="fas fa-thumbs-up"></i></button>
			</form>
		';
	}
	?>

</body>

</html>
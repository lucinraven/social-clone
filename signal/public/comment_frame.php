<html>

<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="../assets/css/stylesheet.css">
</head>

<body>
	<!--INTERNAL CSS-->
	<style type="text/css">
		body {
			width: 98%;
			height: auto;
			padding: 0 10px;
		}

		#comment_form {
			display: flex;
			flex-direction: row;
			align-items: center;
			justify-content: space-between;
		}

		#comment_form textarea {
			resize: none;
			width: 85%;
			border: none;
			background-color: #2c2f33;
			border-radius: 10px;
			padding: 5px 10px;
			color: #FFFFFF;
		}

		#comment_form textarea:focus {
			outline: none;
		}

		#comment_form input {
			padding: 10px 20px;
			background-color: #6959cb;
			border: none;
			border-radius: 10px;
			color: #FFFFFF;
		}

		#comment_form input:focus {
			outline: none;
		}

		.comment_section {
			width: 100%;
			margin: 6px 0;
			display: flex;
			flex-direction: column;
		}

		.comment_section a img {
			height: 19vh;
			width: 5vw;
			border-radius: 50px;
			margin-right: 5px;
		}

		.comment_section .comment_header {
			display: flex;
			flex-direction: row;
			align-items: center;
		}

		.comment_section a {
			height: 19vh;
			width: auto;
			margin-right: 5px;
			color: white;
			text-decoration: none;
		}

		.comment_section .date {
			height: 19vh;
			width: auto;
			font-size: 0.9rem;
			margin-right: 5px;
			color: #99aab5;
			text-decoration: none;
		}

		.comment_section p {
			color: #FFFFFF;
			padding: 10px 0;
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

		$user_query =$con->prepare("SELECT * FROM users WHERE username=?");
		$user_query->bind_param("s", $userLoggedIn);
		$user_query->execute();

		$result = $user_query->get_result();
		$user = $result->fetch_assoc();
	} else {
		header("Location: register.php");
	}

	?>
	<script>
		function toggle() {
			var element = document.getElementById("comment_section");

			if (element.style.display == "block")
				element.style.display = "none";
			else
				element.style.display = "block";
		}
	</script>

	<?php
	//Get id of post
	if (isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
	$row = mysqli_fetch_array($user_query);

	$posted_to = $row['added_by'];
	$user_to = $row['user_to'];

	if (isset($_POST['postComment' . $post_id])) {
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($con, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		$insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', '$post_id')");

		if ($posted_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $posted_to, "comment");
		}

		if ($user_to != 'none' && $user_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_to, "profile_comment");
		}


		$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
		$notified_users = array();
		while ($row = mysqli_fetch_array($get_commenters)) {

			if (
				$row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
				&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)
			) {

				$notification = new Notification($con, $userLoggedIn);
				$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

				array_push($notified_users, $row['posted_by']);
			}
		}
	}
	?>
	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
		<textarea name="post_body" placeholder="Comment.."></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
	</form>

	<!-- Load comments -->
	<?php
	$get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
	$count = mysqli_num_rows($get_comments);

	if ($count != 0) {

		while ($comment = mysqli_fetch_array($get_comments)) {

			$comment_body = $comment['post_body'];
			$posted_to = $comment['posted_to'];
			$posted_by = $comment['posted_by'];
			$date_added = $comment['date_added'];

			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if ($interval->y >= 1) {
				if ($interval->y == 1)
					$time_message = $interval->y . " year ago"; //1 year ago
				else
					$time_message = $interval->y . " years ago"; //1+ year ago
			} else if ($interval->m >= 1) {
				if ($interval->d == 0) {
					$days = " ago";
				} else if ($interval->d == 1) {
					$days = $interval->d . " day ago";
				} else {
					$days = $interval->d . " days ago";
				}

				if ($interval->m == 1) {
					$time_message = $interval->m . " month" . $days;
				} else {
					$time_message = $interval->m . " months" . $days;
				}
			} else if ($interval->d >= 1) {
				if ($interval->d == 1) {
					$time_message = "Yesterday";
				} else {
					$time_message = $interval->d . " days ago";
				}
			} else if ($interval->h >= 1) {
				if ($interval->h == 1) {
					$time_message = $interval->h . " hour ago";
				} else {
					$time_message = $interval->h . " hours ago";
				}
			} else if ($interval->i >= 1) {
				if ($interval->i == 1) {
					$time_message = $interval->i . " minute ago";
				} else {
					$time_message = $interval->i . " minutes ago";
				}
			} else {
				if ($interval->s < 30) {
					$time_message = "Just now";
				} else {
					$time_message = $interval->s . " seconds ago";
				}
			}

			$user_obj = new User($con, $posted_by);


	?>
			<div class="comment_section">
				<div class="comment_header"><a href="<?php echo $posted_by ?>" target="_parent"><img src="../<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by; ?>"></a>
					<a href="<?php echo $posted_by ?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
					<a class="date"> <?php echo $time_message . "</a>" . "</div><p>" . $comment_body;
										"</p>" ?>
				</div>
		<?php

		}
	} else {
		echo "<center style='color: white;'><br><br>No Comments to Show!</center>";
	}

		?>






</body>

</html>
<?php
class Post
{
	private $user_obj;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to)
	{
		$body = strip_tags($body); //removes html tags 
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces 

		if ($check_empty != "") {


			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on own profile, user_to is 'none'
			if ($user_to == $added_by)
				$user_to = "none";

			//insert post 
			$insert_query = $this->con->prepare("INSERT INTO posts VALUES('', ?, ?, ?, ?, 'no', '0')");
			$insert_query->bind_param("ssss", $body, $added_by, $user_to, $date_added);
			$insert_query->execute();

			$returned_id = mysqli_insert_id($this->con);

			//Insert notification
			if ($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "like");
			}

			//Update post count for user 
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;

			$update_query = $this->con->prepare("UPDATE users SET num_posts=? WHERE username=?");
			$update_query->bind_param("is", $num_posts, $added_by);
			$update_query->execute();
		}
	}

	public function loadPostsFriends($data, $limit)
	{

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1) {
			$start = 0;
		} else {
			$start = ($page - 1) * $limit;
		}


		$str = ""; //String to return
		$data_query = $this->con->prepare("SELECT * FROM posts ORDER BY id DESC");
		$data_query->execute();

		$result = $data_query->get_result();

		if ($result->num_rows > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while ($row = $result->fetch_assoc()) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//Prepare user_to string so it can be included even if not posted to a user
				if ($row['user_to'] == "none") {
					$user_to = "";
				} else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if ($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if ($user_logged_obj->isFriend($added_by)) {

					if ($num_iterations++ < $start) {
						continue;
					}


					//Once 10 posts have been loaded, break
					if ($count > $limit) {
						break;
					} else {
						$count++;
					}

					if ($userLoggedIn == $added_by) {
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					} else {
						$delete_button = "";
					}

					$user_details_query = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username=?");
					$user_details_query->bind_param("s", $added_by);
					$user_details_query->execute();

					$result_data = $user_details_query->get_result();

					$user_row = $result_data->fetch_assoc();
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


?>
					<script>
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if (element.style.display == "block")
									element.style.display = "none";
								else
									element.style.display = "block";
							}
						}
					</script>
				<?php

					$comments_check = $this->con->prepare("SELECT * FROM comments WHERE post_id=?");
					$comments_check->bind_param("i", $id);
					$comments_check->execute();

					$result_data = $comments_check->get_result();
					$comments_check_num = $result_data->num_rows;

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
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

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='../$profile_pic' width='50'>
								</div>

								<div class='posted_by'>
								<div class='post_header'><a href='$added_by'> $first_name $last_name </a> $time_message</div>
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>";
				}

				?>
				<script>
					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("../private/includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
									result: result
								});

								if (result)
									location.reload();

							});
						});


					});
				</script>
			<?php

			} //End while loop

			if ($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;
	}


	public function loadProfilePosts($data, $limit)
	{

		$page = $data['page'];
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 

		$data_query = $this->con->prepare("SELECT * FROM posts WHERE ((added_by=? AND user_to='none') OR user_to=?)  ORDER BY id DESC");
		$data_query->bind_param("ss", $profileUser, $profileUser);
		$data_query->execute();

		$result = $data_query->get_result();

		if ($result->num_rows > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while ($row = $result->fetch_assoc()) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];


				if ($num_iterations++ < $start)
					continue;


				//Once 10 posts have been loaded, break
				if ($count > $limit) {
					break;
				} else {
					$count++;
				}

				if ($userLoggedIn == $added_by) {
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				} else {
					$delete_button = "";
				}

				$user_details_query = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username=?");
				$user_details_query->bind_param("s", $added_by);
				$user_details_query->execute();

				$result_data = $user_details_query->get_result();

				$user_row = $result_data->fetch_assoc();
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


			?>
				<script>
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if (!target.is("a")) {
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if (element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";
						}
					}
				</script>
				<?php

				$comments_check = $this->con->prepare("SELECT * FROM comments WHERE post_id=?");
				$comments_check->bind_param("i", $id);
				$comments_check->execute();

				$result_data = $comments_check->get_result();
				$comments_check_num = $result_data->num_rows;


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
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

				$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='../$profile_pic' width='50'>
								</div>

								<div class='posted_by'>
								<div class='post_header'><a href='$added_by'> $first_name $last_name </a> $time_message</div>

								
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>";

				?>
				<script>
					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
									result: result
								});

								if (result)
									location.reload();

							});
						});


					});
				</script>
			<?php

			} //End while loop

			if ($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;
	}

	public function getSinglePost($post_id)
	{

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = $this->con->prepare("UPDATE notifications SET opened='yes' WHERE user_to=? AND link LIKE '%=?'");
		$opened_query->bind_param("si", $userLoggedIn, $post_id);
		$opened_query->execute();

		$data_query = $this->con->prepare("SELECT * FROM posts WHERE id=?");
		$data_query->bind_param("i", $post_id);
		$data_query->execute();

		$result = $data_query->get_result();

		$str = ""; //String to return 

		if ($result->num_rows > 0) {

			$row = $result->fetch_assoc();
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];

			//Prepare user_to string so it can be included even if not posted to a user
			if ($row['user_to'] == "none") {
				$user_to = "";
			} else {
				$user_to_obj = new User($this->con, $row['user_to']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
			}

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if ($added_by_obj->isClosed()) {
				return;
			}

			$user_logged_obj = new User($this->con, $userLoggedIn);
			if ($user_logged_obj->isFriend($added_by)) {


				if ($userLoggedIn == $added_by) {
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				} else {
					$delete_button = "";
				}

				$user_details_query = $this->con->prepare("SELECT first_name, last_name, profile_pic FROM users WHERE username=?");
				$user_details_query->bind_param("s", $added_by);
				$user_details_query->execute();

				$result_data = $user_details_query->get_result();

				$user_row = $result_data->fetch_assoc();
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


			?>
				<script>
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if (!target.is("a")) {
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if (element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";
						}
					}
				</script>
				<?php

				$comments_check = $this->con->prepare("SELECT * FROM comments WHERE post_id=?");
				$comments_check->bind_param("i", $id);
				$comments_check->execute();

				$result_data = $comments_check->get_result();
				$comments_check_num = $result_data->num_rows;


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
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

				$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='../$profile_pic'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
								<div class='post_header'>
								<a href='$added_by'> $first_name $last_name </a> $time_message
								</div>
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>";


				?>
				<script>
					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Are you sure you want to delete this post?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {
									result: result
								});

								if (result)
									location.reload();

							});
						});


					});
				</script>
<?php
			} else {
				echo "<p>You cannot see this post because you are not friends with this user.</p>";
				return;
			}
		} else {
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
			return;
		}

		echo $str;
	}
}

?>
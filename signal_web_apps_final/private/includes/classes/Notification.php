<?php
class Notification
{
	private $user_obj;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getUnreadNumber()
	{
		$userLoggedIn = $this->user_obj->getUsername();

		$query = $this->con->prepare("SELECT * FROM notifications WHERE viewed='no' AND user_to=?");
		$query->bind_param("s", $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		return $result->num_rows;
	}

	public function getNotifications($data, $limit)
	{

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";

		if ($page == 1) {
			$start = 0;
		} else {
			$start = ($page - 1) * $limit;
		}

		$set_viewed_query = $this->con->prepare("UPDATE notifications SET viewed='yes' WHERE user_to=?");
		$set_viewed_query->bind_param("s", $userLoggedIn);
		$set_viewed_query->execute();

		$query = $this->con->prepare("SELECT * FROM notifications WHERE user_to=? ORDER BY id DESC");
		$query->bind_param("s", $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		if ($result->num_rows == 0) {
			echo "You have no notifications!";
			return;
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		while ($row = $result->fetch_assoc()) {

			if ($num_iterations++ < $start)
				continue;

			if ($count > $limit)
				break;
			else
				$count++;


			$user_from = $row['user_from'];

			$user_data_query = $this->con->prepare("SELECT * FROM users WHERE username=?");
			$user_data_query->bind_param("s", $user_from);
			$user_data_query->execute();

			$result = $user_data_query->get_result();

			$user_data = $result->fetch_assoc();


			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['datetime']); //Time of post
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

			$opened = $row['opened'];
			$new = ($opened == 'no') ? "new" : "";

			$return_string .= "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification " . $new . "'>
										<figure>
											<img src='../" . $user_data['profile_pic'] . "'>
										</figure>
										<div class='texts'>
											<p class='timestamp_smaller'>" . $time_message . "</p>
											<p class='type'>" . $row['message'] . "</p>
										</div>
									</div>
								</a>";
		}


		//If posts were loaded
		if ($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";

		return $return_string;
	}

	public function insertNotification($post_id, $user_to, $type)
	{

		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		switch ($type) {
			case 'comment':
				$message = $userLoggedInName . " commented on your post";
				break;
			case 'like':
				$message = $userLoggedInName . " liked your post";
				break;
			case 'profile_post':
				$message = $userLoggedInName . " posted on your profile";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " commented on a post you commented on";
				break;
			case 'profile_comment':
				$message = $userLoggedInName . " commented on your profile post";
				break;
		}

		$link = "post.php?id=" . $post_id;

		$insert_query = $this->con->prepare("INSERT INTO notifications VALUES('', ?, ?, ?, ?, ?, 'no', 'no')");
		$insert_query->bind_param("sssss", $user_to, $userLoggedIn, $message, $link, $date_time);
		$insert_query->execute();
	}
}

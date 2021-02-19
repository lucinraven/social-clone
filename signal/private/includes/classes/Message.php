<?php
class Message
{
	private $user_obj;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getMostRecentUser()
	{
		$userLoggedIn = $this->user_obj->getUsername();

		$query = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to=? OR user_from=? ORDER BY id DESC LIMIT 1");
		$query->bind_param("ss", $userLoggedIn, $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		if ($result->num_rows == 0) {
			return false;
		}

		$row = $result->fetch_assoc();
		$user_to = $row['user_to'];
		$user_from = $row['user_from'];

		if ($user_to != $userLoggedIn) {

			return $user_to;
		} else {
			return $user_from;
		}
	}

	public function sendMessage($user_to, $body, $date)
	{

		if ($body != "") {
			$userLoggedIn = $this->user_obj->getUsername();

			$query = $this->con->prepare("INSERT INTO messages VALUES('', ?, ?, ?, ?, 'no', 'no')");
			$query->bind_param("ssss", $user_to, $userLoggedIn, $body, $date);
			$query->execute();
		}
	}

	public function getMessages($otherUser)
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$data = "";

		$query = $this->con->prepare("UPDATE messages SET opened='yes' WHERE user_to=? AND user_from=?");
		$query->bind_param("ss", $userLoggedIn, $otherUser);
		$query->execute();

		$get_messages_query = $this->con->prepare("SELECT * FROM messages WHERE (user_to=? AND user_from=?) OR (user_from=? AND user_to=?)");
		$get_messages_query->bind_param("ssss", $userLoggedIn, $otherUser, $userLoggedIn, $otherUser);
		$get_messages_query->execute();

		$result = $get_messages_query->get_result();

		while ($row = $result->fetch_assoc()) {
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];
			$body = $row['body'];

			$div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
			$data = $data . $div_top . $body . "</div><br><br>";
		}
		return $data;
	}

	public function getLatestMessage($userLoggedIn, $user2)
	{
		$details_array = array();

		$query = $this->con->prepare("SELECT body, user_to, date FROM messages WHERE (user_to=? AND user_from=?) OR (user_from=? AND user_to=?) ORDER BY id DESC LIMIT 1");
		$query->bind_param("ssss", $userLoggedIn, $user2, $userLoggedIn, $user2);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();
		$sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

		//Timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($row['date']); //Time of post
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

		array_push($details_array, $sent_by);
		array_push($details_array, $row['body']);
		array_push($details_array, $time_message);

		return $details_array;
	}

	public function getConvos()
	{
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		$query = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to=? OR user_from=? ORDER BY id DESC");
		$query->bind_param("ss", $userLoggedIn, $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		while ($row = $result->fetch_assoc()) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			if (!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}

		foreach ($convos as $username) {
			$user_found_obj = new User($this->con, $username);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;

			$return_string .= "<a href='messages.php?u=$username'>
			<figure>
			<img src='../" . $user_found_obj->getProfilePic() . "'>
			</figure>
			<p>" . $user_found_obj->getFirstAndLastName() . "</p>
								
								</a>";
		}

		return $return_string;
	}

	public function getConvosDropdown($data, $limit)
	{

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		if ($page == 1) {
			$start = 0;
		} else {
			$start = ($page - 1) * $limit;
		}

		$set_viewed_query = $this->con->prepare("UPDATE messages SET viewed='yes' WHERE user_to=?");
		$set_viewed_query->bind_param("s", $userLoggedIn);
		$set_viewed_query->execute();

		$query = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to=? OR user_from=? ORDER BY id DESC");
		$query->bind_param("ss", $userLoggedIn, $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		while ($row = $result->fetch_assoc()) {
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

			if (!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}

		$num_iterations = 0; //Number of messages checked 
		$count = 1; //Number of messages posted

		foreach ($convos as $username) {

			if ($num_iterations++ < $start) {
				continue;
			}

			if ($count > $limit) {
				break;
			} else {
				$count++;
			}

			$is_unread_query = $this->con->prepare("SELECT opened FROM messages WHERE user_to=? ORDER BY id DESC");
			$is_unread_query->bind_param("s", $userLoggedIn);
			$is_unread_query->execute();

			$result_unread = $is_unread_query->get_result();

			$row = $result_unread->fetch_assoc();

			$opened = $row['opened'];
			$new = ($opened == 'no') ? "new" : "";

			$user_found_obj = new User($this->con, $username);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;

			$return_string .= "<a href='messages.php?u=$username'> 
								<div class='user_found_messages " . $new . "'>
									<figure>
										<img src='../" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
									</figure>
									<div class='texts'>	
										" . $user_found_obj->getFirstAndLastName() . "
										<span class='timestamp_smaller' id='grey'> " . $latest_message_details[2] . "</span>
										<p id='grey'>" . $latest_message_details[0] . $split . " </p>
									</div>
								</div>
								</a>";
		}


		//If posts were loaded
		if ($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more messages to load!</p>";

		return $return_string;
	}

	public function getUnreadNumber()
	{
		$userLoggedIn = $this->user_obj->getUsername();


		$query = $this->con->prepare("SELECT * FROM messages WHERE viewed='no' AND user_to=?");
		$query->bind_param("s", $userLoggedIn);
		$query->execute();

		$result = $query->get_result();

		$row = $result->num_rows;

		return $row;
	}
}

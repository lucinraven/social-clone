<?php
class User
{
	private $user;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUsername()
	{
		return $this->user['username'];
	}

	public function getNumberOfFriendRequests()
	{
		$username = $this->user['username'];

		$user_query = $this->con->prepare("SELECT * FROM users WHERE username=?");
		$user_query->bind_param("s", $username);
		$user_query->execute();

		$result = $user_query->get_result();

		$final = $result->num_rows;

		return $final;
	}

	public function getNumPosts()
	{
		$username = $this->user['username'];

		$query = $this->con->prepare("SELECT num_posts FROM users WHERE username=?");
		$query->bind_param("s", $username);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();

		return $row['num_posts'];
	}

	public function getFirstAndLastName()
	{
		$username = $this->user['username'];

		$query = $this->con->prepare("SELECT first_name, last_name FROM users WHERE username=?");
		$query->bind_param("s", $username);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();

		return $row['first_name'] . " " . $row['last_name'];
	}

	public function getProfilePic()
	{
		$username = $this->user['username'];

		$query = $this->con->prepare("SELECT profile_pic FROM users WHERE username=?");
		$query->bind_param("s", $username);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();

		return $row['profile_pic'];
	}

	public function getFriendArray()
	{
		$username = $this->user['username'];

		$query = $this->con->prepare("SELECT friend_array FROM users WHERE username=?");
		$query->bind_param("s", $username);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();

		return $row['friend_array'];
	}

	public function isClosed()
	{
		$username = $this->user['username'];

		$query = $this->con->prepare("SELECT user_closed FROM users WHERE username=?");
		$query->bind_param("s", $username);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();

		if ($row['user_closed'] == 'yes') {
			return true;
		} else {
			return false;
		}
	}

	public function isFriend($username_to_check)
	{
		$usernameComma = "," . $username_to_check . ",";

		if ((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])) {
			return true;
		} else {
			return false;
		}
	}

	public function didReceiveRequest($user_from)
	{
		$user_to = $this->user['username'];

		$query = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to=? AND user_from=?");
		$query->bind_param("ss", $user_to, $user_from);
		$query->execute();

		$result = $query->get_result();

		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function didSendRequest($user_to)
	{
		$user_from = $this->user['username'];

		$query = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to=? AND user_from=?");
		$query->bind_param("ss", $user_to, $user_from);
		$query->execute();

		$result = $query->get_result();

		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function removeFriend($user_to_remove)
	{
		$logged_in_user = $this->user['username'];

		$query = $this->con->prepare("SELECT friend_array FROM users WHERE username=?");
		$query->bind_param("s", $user_to_remove);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();
		$friend_array_username = $row['friend_array'];

		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		$remove_friend = $this->con->prepare("UPDATE users SET friend_array=? WHERE username=?");
		$remove_friend->bind_param("ss", $new_friend_array, $logged_in_user);
		$remove_friend->execute();

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = $this->con->prepare("UPDATE users SET friend_array=? WHERE username=?");
		$remove_friend->bind_param("ss", $new_friend_array, $user_to_remove);
		$remove_friend->execute();
	}

	public function sendRequest($user_to)
	{
		$user_from = $this->user['username'];

		$query = $this->con->prepare("INSERT INTO friend_requests VALUES('', ?, ?)");
		$query->bind_param("ss", $user_to, $user_from);
		$query->execute();
	}

	public function getMutualFriends($user_to_check)
	{
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);


		$query = $this->con->prepare("SELECT friend_array FROM users WHERE username=?");
		$query->bind_param("s", $user_to_check);
		$query->execute();

		$result = $query->get_result();

		$row = $result->fetch_assoc();
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach ($user_array_explode as $i) {

			foreach ($user_to_check_array_explode as $j) {

				if ($i == $j && $i != "") {
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;
	}
}

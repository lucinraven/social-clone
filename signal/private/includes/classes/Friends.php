<?php
class Friends
{
	private $user_obj;
	private $con;

	public function __construct($con, $user)
	{
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getFriends($userLoggedIn)
	{
		$print = "";
		$user_query = $this->con->prepare("SELECT * FROM users WHERE username=?");
		$user_query->bind_param("s", $userLoggedIn);
		$user_query->execute();

		$result = $user_query->get_result();

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {

				$friends = explode(',', $row['friend_array']);
				$print = "";
				$num_friends = "";


				foreach ($friends as $f) {

					//$print = print_r($f);

					$friend_query = $this->con->prepare("SELECT * FROM users WHERE username=?");
					$friend_query->bind_param("s", $f);
					$friend_query->execute();

					$result = $friend_query->get_result();

					$friend_array = $result->fetch_assoc();

					if (!$f == "") {
						$num_friends = (substr_count($friend_array['friend_array'], ",")) - 2;
						$print .= "
				<div class='friend_container'>
					<figure><img src='../" . $friend_array['profile_pic'] . "'></figure>
					<div class='header_container'>
						<a href='$f'>" . $friend_array['first_name'] . " " . $friend_array['last_name'] . "</a>
						<p>" . $num_friends . " Mutual Friends</p>
					</div>
				</div>
			";
					}
				}
			}
		}

		return $print;
	}
}

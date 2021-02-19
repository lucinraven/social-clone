<?php
//to call the config and class: user
include("../../config/config.php");
include("../classes/User.php");

//to get query from message.php
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

//to separate the typed name to array
$names = explode(" ", $query);

//to check if the typed name is a username
if(strpos($query, "_") !== false) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
}
//to both the separated string
else if(count($names) == 2) {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
}
else {
	$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}
//if query returns a value
if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {

		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn) {
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		}
		else {
			$mutual_friends = "";
		}

		//result
		if($user->isFriend($row['username'])) {
			echo "<div class='resultDisplay'>
					<a href='messages.php?u=" . $row['username'] . "' style='color: #000'>
						<figure class='liveSearchProfilePic'>
							<img src='../". $row['profile_pic'] . "'>
						</figure>

						<div class='liveSearchText'>
							".$row['first_name'] . " " . $row['last_name']. "
							<p style='margin: 0;'>". $row['username'] . "</p>
						</div>
					</a>
				</div>";
		}
	}
}

?>
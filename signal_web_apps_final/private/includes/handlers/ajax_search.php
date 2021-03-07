<?php
//to call the config and class: user
include("../../config/config.php");
include("../../includes/classes/User.php");

//input from search bar
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

//to separate the typed name to array
$names = explode(" ", $query);

//If query contains an underscore, assume user is searching for usernames
if (strpos($query, '_') !== false) {

	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
}
//If there are two words, assume they are first and last names respectively
else if (count($names) == 2) {

	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
}
//If query has one word only, search first names or last names 
else {

	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
}

//if query returns a value
if ($query != "") {

	while ($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($con, $userLoggedIn);

		if ($row['username'] != $userLoggedIn)
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		else
			$mutual_friends == "";

		//result
		echo "<div class='resultDisplay'>
				<a href='" . $row['username'] . "'>
					<figure class='liveSearchProfilePic'>
						<img src='../" . $row['profile_pic'] . "'>
					</figure>

					<div class='liveSearchText'>
						<p>" . $row['first_name'] . " " . $row['last_name'] . "</p>
						<p>" . $row['username'] . "</p>
						<p id='grey'>" . $mutual_friends . "</p>
					</div>
				</a>
				</div>";
	}
}

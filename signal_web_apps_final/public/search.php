<?php
//to activate the class "active", which is none
$active = "";
//to change the header title
$header = 'Search';

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//to get the searched item
if (isset($_GET['q'])) {
	$query = $_GET['q'];
} else {
	$query = "";
}

//to categorize if its a username or name
if (isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	$type = "name";
}
?>

<!--SEARCH MAIN CONTAINER-->
<div class="search_column">
	<?php
	if ($query == "")
		//No searched item
		echo "You must enter something in the search box.";
	else {

		//If query contains an underscore, assume user is searching for usernames
		if ($type == "username")
			$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
		//If there are two words, assume they are first and last names respectively
		else {
			/*to separate the name between a " "
			EXAMPLE: Zaira Mundo
			into: array("zaira" "mundo") */

			$names = explode(" ", $query);

			if (count($names) == 3)
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
			//If query has one word only, search first names or last names 
			else if (count($names) == 2)
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
			else
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
		}

		//Check if results were found 
		if (mysqli_num_rows($usersReturnedQuery) == 0) {
			echo "<div class='content'> We can't find anyone with a " . $type . " like: " . $query;
		} else {
			echo "<div class='content'>" . mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";
		}

		echo "<p id='grey'>Try searching for:</p>";
		echo "<div class='try'><a href='search.php?q=" . $query . "&type=name'>Names</a>, <a href='search.php?q=" . $query . "&type=username'>Usernames</a></div><br>";

		while ($row = mysqli_fetch_array($usersReturnedQuery)) {
			$user_obj = new User($con, $user['username']);

			$button = "";
			$mutual_friends = "";

			if ($user['username'] != $row['username']) {

				//Generate button depending on friendship status 
				if ($user_obj->isFriend($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
				else if ($user_obj->didReceiveRequest($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to request'>";
				else if ($user_obj->didSendRequest($row['username']))
					$button = "<input type='submit' class='default' value='Request Sent'>";
				else
					$button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";

				$mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";


				//Button forms
				if (isset($_POST[$row['username']])) {
					
					if ($user_obj->isFriend($row['username'])) {
						$user_obj->removeFriend($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					} else if ($user_obj->didReceiveRequest($row['username'])) {
						header("Location: requests.php");
					} else if ($user_obj->didSendRequest($row['username'])) {
					} else {
						$user_obj->sendRequest($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}
				}
			}

			echo "<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<form action='' method='POST'>
							" . $button . "
							<br>
						</form>
					</div>


					<div class='result_container'>
					<div class='result_profile_pic'>
						<a href='" . $row['username'] . "'><img src='../" . $row['profile_pic'] . "' style='height: 100px;'></a>
					</div>

					<div class='result_texts'>
					<a href='" . $row['username'] . "'> " . $row['first_name'] . " " . $row['last_name'] . "
						<p id='grey'> " . $row['username'] . "</p>
						</a>
						<br>
						" . $mutual_friends . "<br>
					</div>
					</div>

				</div>
				<hr id='search_hr'>";
		} //End while
	}


	?>
</div>

</body>

</html>
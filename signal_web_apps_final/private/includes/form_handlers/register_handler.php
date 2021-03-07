<?php
//Declaring variables to prevent errors
$fname = ""; //First name
$lname = ""; //Last name
$em = ""; //email
$password = ""; //password
$date = ""; //Sign up date 
$error_array = array(); //Holds error messages

if (isset($_POST['register_button'])) {

	//Registration form values

	//First name
	$fname = strip_tags($_POST['reg_fname']); //Remove html tags
	$fname = str_replace(' ', '', $fname); //remove spaces
	$fname = ucfirst(strtolower($fname)); //Uppercase first letter
	$_SESSION['reg_fname'] = $fname; //Stores first name into session variable

	//Last name
	$lname = strip_tags($_POST['reg_lname']); //Remove html tags
	$lname = str_replace(' ', '', $lname); //remove spaces
	$lname = ucfirst(strtolower($lname)); //Uppercase first letter
	$_SESSION['reg_lname'] = $lname; //Stores last name into session variable

	//email
	$em = strip_tags($_POST['reg_email']); //Remove html tags
	$em = str_replace(' ', '', $em); //remove spaces
	$_SESSION['reg_email'] = $em; //Stores email into session variable

	//Password
	$password = strip_tags($_POST['reg_password']); //Remove html tags

	$date = date("Y-m-d"); //Current date

	if (filter_var($em, FILTER_VALIDATE_EMAIL)) {

		$em = filter_var($em, FILTER_VALIDATE_EMAIL);

		//Check if email already exists
		$e_check = $con->prepare("SELECT email FROM users WHERE email=?");
		$e_check->bind_param("s", $em);
		$e_check->execute();

		$result = $e_check->get_result();

		//Count the number of rows returned
		$num_rows = $result->num_rows;

		if ($num_rows > 0) {
			array_push($error_array, "Email already in use<br>");
		}
	} else {
		array_push($error_array, "Invalid email format<br>");
	}


	if (strlen($fname) > 25 || strlen($fname) < 2) {
		array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
	}

	if (strlen($lname) > 25 || strlen($lname) < 2) {
		array_push($error_array,  "Your last name must be between 2 and 25 characters<br>");
	}

	if (preg_match('/[^A-Za-z0-9]/', $password)) {
		array_push($error_array, "Your password can only contain english characters or numbers<br>");
	}

	if (strlen($password > 30 || strlen($password) < 5)) {
		array_push($error_array, "Your password must be betwen 5 and 30 characters<br>");
	}


	if (empty($error_array)) {
		$password = md5($password); //Encrypt password before sending to database

		//Generate username by concatenating first name and last name
		$username = strtolower($fname . "_" . $lname);

		$check_username_query = $con->prepare("SELECT username FROM users WHERE username=?");
		$check_username_query->bind_param("s", $username);
		$check_username_query->execute();

		$result = $check_username_query->get_result();

		$i = 0;
		//if username exists add number to username
		while ($result->num_rows != 0) {
			$i++; //Add 1 to i
			$username = $username . "_" . $i;

			$check_username_query = $con->prepare("SELECT username FROM users WHERE username=?");
			$check_username_query->bind_param("s", $username);
			$check_username_query->execute();

			$result = $check_username_query->get_result();
		}

		$profile_pic = "assets/images/profile_pics/default_profile.png";
		$cover_pic = "assets/images/cover_pics/default_cover.jpg";

		$query = $con->prepare("INSERT INTO users VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, '0', 'no', ',')");
		$query->bind_param("ssssssss", $fname, $lname, $username, $em, $password, $date, $profile_pic, $cover_pic);
		$query->execute();

		//Clear session variables 
		$_SESSION['reg_fname'] = "";
		$_SESSION['reg_lname'] = "";
		$_SESSION['reg_email'] = "";

		$_SESSION['username'] = $username;
		header("Location: index.php");
		exit();
	}
}

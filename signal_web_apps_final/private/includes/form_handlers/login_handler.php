<?php
//to login the user from register.php
if (isset($_POST['login_button'])) {
	//sanitize email
	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);

	//Store email into session variable 
	$_SESSION['log_email'] = $email;
	//Get password
	$password = md5($_POST['log_password']);

	$check_database_query = $con->prepare("SELECT * FROM users WHERE email=? AND password=?");
	$check_database_query->bind_param("ss", $email, $password);
	$check_database_query->execute();

	$result = $check_database_query->get_result();

	$check_login_query = $result->num_rows;

	if ($check_login_query == 1) {
		$row = $result->fetch_assoc();
		$username = $row['username'];

		$user_closed_query = $con->prepare("SELECT * FROM users WHERE email=? AND user_closed='yes'");
		$user_closed_query->bind_param("s", $email);
		$user_closed_query->execute();

		$result_user = $user_closed_query->get_result();

		if ($result_user->num_rows == 1) {

			$reopen_account = $con->prepare("UPDATE users SET user_closed='no' WHERE email=?");
			$reopen_account->bind_param("s", $email);
			$reopen_account->execute();
		}

		$_SESSION['username'] = $username;
		header("Location: index.php");
		exit();
	} else {
		array_push($error_array, "Email or password was incorrect<br>");
	}
}

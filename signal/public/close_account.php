<?php
//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//to cancel the close account
if (isset($_POST['cancel'])) {
	header("Location: settings.php");
}

//to close the account
if (isset($_POST['close_account'])) {

	$close_query =$con->prepare("UPDATE users SET user_closed='yes' WHERE username=?");
	$close_query->bind_param("s", $userLoggedIn);
	$close_query->execute();
	
	session_destroy();
	header("Location: register.php");
}
?>

<!--CLOSE ACCOUNT MAIN CONTAINER-->
<div class="closeAccount_column">

	<h4>Close Account</h4>

	Are you sure you want to close your account?<br><br>
	Closing your account will hide your profile and all your activity from other users.<br><br>
	You can re-open your account at any time by simply logging in.<br><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Yes! Close it!" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="No way!" class="info settings_submit">
	</form>

</div>

</body>

</html>
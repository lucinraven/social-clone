<?php
//to call the config / database
require '../private/config/config.php';

//to call the form handlers: register(sign up) & login(sign in)
require '../private/includes/form_handlers/register_handler.php';
require '../private/includes/form_handlers/login_handler.php';
?>

<html>

<head>
	<title>Signal: Sign in or Sign up</title>
	<link rel="stylesheet" type="text/css" href="../assets/css/stylesheet.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="../assets/js/register.js"></script>
</head>

<body>
	<div class="reg-main-ctn">

		<!--To show and hide both forms-->
		<?php
		if (isset($_POST['register_button'])) {
			echo '
		<script>
		$(document).ready(function() {
			$("#first").hide();
			$("#second").show();
		});
		</script>
		';
		}
		?>

		<!--Logo-->
		<figure class="logo">
			<img id="path-logo" src="../assets/images/others/signal2.png" alt="">
		</figure>

		<!--REGISTER FORM MAIN CONTENT-->
		<div class="reg-inner-ctn">
			<div class="login_box">
				<!--LOGIN FORM-->
				<div id="first">
					<h1>Welcome back!</h1>
					<form action="register.php" method="POST">
						<div class="input_group">
							<label>Email</label>
							<input type="email" name="log_email" value="<?php
																		if (isset($_SESSION['log_email'])) {
																			echo $_SESSION['log_email'];
																		}
																		?>" required>
						</div>
						<div class="input_group">
							<label>Password</label>
							<input type="password" name="log_password">
							<?php if (in_array("Email or password was incorrect<br>", $error_array)) echo  "Email or password was incorrect<br>"; ?>
						</div>
						<input type="submit" name="login_button" value="Login" class="reg-btn">
						<a href="#" id="signup" class="signup reg-login">Don't have an account?</a>
					</form>
				</div>

				<!--REGISTER FORM-->
				<div id="second">
					<h1>Create your account</h1>
					<form action="register.php" method="POST">
						<div class="input_group">
							<label>First Name</label>
							<input type="text" name="reg_fname" value="<?php
																		if (isset($_SESSION['reg_fname'])) {
																			echo $_SESSION['reg_fname'];
																		}
																		?>" required>

							<?php if (in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>"; ?>
						</div>
						<div class="input_group">
							<label>Last Name</label>
							<input type="text" name="reg_lname" value="<?php
																		if (isset($_SESSION['reg_lname'])) {
																			echo $_SESSION['reg_lname'];
																		}
																		?>" required>

							<?php if (in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>"; ?>
						</div>
						<div class="input_group">
							<label>Email</label>
							<input type="email" name="reg_email" value="<?php
																		if (isset($_SESSION['reg_email'])) {
																			echo $_SESSION['reg_email'];
																		}
																		?>" required>
						</div>
						<?php if (in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
						else if (in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
						else if (in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>
						<div class="input_group">
							<label>Password</label>
							<input type="password" name="reg_password" required>
						</div>
						<?php if (in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>";
						else if (in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
						else if (in_array("Your password must be betwen 5 and 30 characters<br>", $error_array)) echo "Your password must be betwen 5 and 30 characters<br>"; ?>
						<input type="submit" name="register_button" value="Register" class="reg-btn">
						<br>
						<?php if (in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>"; ?>
						<a href="#" id="signin" class="signin reg-login">Already have an account? Sign in here!</a>
					</form>
				</div>

				<!--DISPLAY CONTAINER-->
				<div id="login_header">
					<figure>
						<img src="../assets/images/others/signal1.png" alt="">
					</figure>
					<p>Lorem ipsum Lorem ipsum.</p>
				</div>
			</div>
		</div>
	</div>

</body>

</html>
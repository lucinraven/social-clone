<?php
//to activate the class "active"
$active = 'settings';
//to change the header title
$header = "Settings";

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//Form Handlers for Setting Page
include("../private/includes/form_handlers/settings_handler.php");
?>

<!--SETTINGS MAIN CONTAINER-->
<div class="setting_main_column">

	<!--UPDATE ACCOUNT SETTINGS-->
	<h4>Account Settings</h4>
	<div class="profile_images">
		<!--Preview of Profile Picture-->
		<div class="profile_pic">
			<figure class='small_profile_pic'>
				<?php
				echo "<img src='../" . $user['profile_pic'] . "' >";
				?>
			</figure>
			<br>
			<form action="settings.php" method="post" enctype="multipart/form-data">
				<input type="file" name="image" />
				<input type="submit" name="change_image" value="Change Profile Image" />
			</form>
		</div>

		<div class="cover_pic">
			<!--Preview of Cover Picture-->
			<figure class='small_profile_pic'>
				<?php
				echo "<img src='../" . $user['cover_pic'] . "' >";
				?>
			</figure>
			<br>
			<form action="settings.php" method="post" enctype="multipart/form-data">
				<input type="file" name="cover_image" />
				<input type="submit" name="change_cover" value="Change Cover Image" />
			</form>
		</div>
	</div>

	Modify the values and click 'Update Details'

	<?php
	//Values of each input for Account Setting
	$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($user_data_query);

	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$email = $row['email'];
	?>

	<form action="settings.php" method="POST">
		<div class="input-group">
			<label>First Name:</label>
			<input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input">
		</div>
		<div class="input-group">
			<label>Last Name:</label>
			<input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input">
		</div>
		<div class="input-group">
			<label>Email:</label>
			<input type="text" name="email" value="<?php echo $email; ?>" id="settings_input">
		</div>

		<?php echo $message; ?>

		<input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit"><br>
	</form>

	<!--CHANGE PASSWORD FORM-->
	<h4>Change Password</h4>
	<form action="settings.php" method="POST">
		<div class="input-group">
			<label>Old Password:</label>
			<input type="password" name="old_password" id="settings_input">
		</div>
		<div class="input-group">
			<label>New Password:</label>
			<input type="password" name="new_password_1" id="settings_input">
		</div>
		<div class="input-group">
			<label>Retype New Password:</label>
			<input type="password" name="new_password_2" id="settings_input">
		</div>

		<?php echo $password_message; ?>

		<input type="submit" name="update_password" id="save_details" value="Update Password" class="info settings_submit"><br>
	</form>

	<!--EDIT INTRODUCTION FROM PROFILE PAGE SECTION-->
	<h4>Edit Profile</h4>
	<?php
	//Values of each input for Edit Profile Setting
	$profile_value = mysqli_query($con, "SELECT * FROM profile_user WHERE users='$userLoggedIn'");
	$valueRow = mysqli_fetch_array($profile_value);

	$locationValue = "";
	$companyValue = "";
	$educationValue = "";
	$contactValue = "";

	if ($valueRow != NULL) {
		$locationValue = $valueRow['address'];
		$companyValue = $valueRow['works'];
		$educationValue = $valueRow['education'];
		$contactValue = $valueRow['contacts'];

		if ($locationValue == NULL || $locationValue == "") {
			$locationValue = "";
		}
		if ($companyValue == NULL || $companyValue == "") {
			$companyValue = "";
		}
		if ($educationValue == NULL || $educationValue == "") {
			$educationValue = "";
		}
		if ($contactValue == NULL ||$contactValue == "") {
			$contactValue = "";
		}
	}

	?>

	<form action="settings.php" method="POST">
		<div class="input-group">
			<label>Location:</label>
			<input type="location" name="location" id="settings_input" value="<?php echo $locationValue; ?>">
		</div>
		<div class="input-group">
			<label>Company (Works at):</label>
			<input type="company" name="company" id="settings_input" value="<?php echo $companyValue; ?>">
		</div>
		<div class="input-group">
			<label>Education:</label>
			<input type="education" name="education" id="settings_input" value="<?php echo $educationValue; ?>">
		</div>
		<div class="input-group">
			<label>Contact:</label>
			<input type="contact" name="contact" id="settings_input" value="<?php echo $contactValue; ?>">
		</div>

		<?php echo $introduction_message; ?>

		<input type="submit" name="update_introduction" id="save_details" value="Update Profile" class="info settings_submit"><br>
	</form>

	<!--CLOSE THE ACCOUNT-->
	<h4>Close Account</h4>
	<form action="settings.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
	</form>


</div>
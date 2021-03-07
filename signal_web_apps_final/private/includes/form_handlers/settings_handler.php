<?php
if (isset($_POST['update_details'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];

	$email_check = $con->prepare("SELECT * FROM users WHERE email=?");
	$email_check->bind_param("s", $email);
	$email_check->execute();

	$result = $email_check->get_result();

	$row = $result->fetch_assoc();
	$matched_user = $row['username'];

	if ($matched_user == "" || $matched_user == $userLoggedIn) {
		$message = "Details updated!<br><br>";

		$query = $con->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE username=?");
		$query->bind_param("ssss", $first_name, $last_name, $email, $userLoggedIn);
		$query->execute();

		header("Location: settings.php");
	} else{
		$message = "That email is already in use!<br><br>";
	}
} else
	$message = "";


//**************************************************

if (isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = $con->prepare("SELECT password FROM users WHERE username=?");
	$password_query->bind_param("s", $userLoggedIn);
	$password_query->execute();

	$result = $password_query->get_result();

	$row = $result->fetch_assoc();
	$db_password = $row['password'];

	if (md5($old_password) == $db_password) {

		if ($new_password_1 == $new_password_2) {


			if (strlen($new_password_1) <= 4) {
				$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
			} else {
				$new_password_md5 = md5($new_password_1);

				$password_query = $con->prepare("UPDATE users SET password=? WHERE username=?");
				$password_query->bind_param("ss", $new_password_md5, $userLoggedIn);
				$password_query->execute();

				$password_message = "Password has been changed!<br><br>";

				header("Location: settings.php");
			}
		} else {
			$password_message = "Your two new passwords need to match!<br><br>";
		}
	} else {
		$password_message = "The old password is incorrect! <br><br>";
	}
} else {
	$password_message = "";
}

//**************************************************

if (isset($_POST['update_introduction'])) {

	$location = strip_tags($_POST['location']);
	$company = strip_tags($_POST['company']);
	$education = strip_tags($_POST['education']);
	$contact = strip_tags($_POST['contact']);

	$profile_query = $con->prepare("SELECT users FROM profile_user WHERE users=?");
	$profile_query->bind_param("s", $userLoggedIn);
	$profile_query->execute();

	$result = $profile_query->get_result();

	$row = $result->fetch_assoc();

	if ($row == NULL) {
		if ($location != NULL) {
			$location_query = $con->prepare("INSERT INTO profile_user VALUES
			('', ?, ?, '', '', '')");
			$location_query->bind_param("ss", $userLoggedIn, $location);
			$location_query->execute();
		}

		if ($company != NULL) {
			$company_query = $con->prepare("INSERT INTO profile_user VALUES
			('', ?, '', ?, '', '')");
			$company_query->bind_param("ss", $userLoggedIn, $company);
			$company_query->execute();
		}

		if ($education != NULL) {
			$education_query = $con->prepare("INSERT INTO profile_user VALUES
			('', ?, '', '', ?, '')");
			$education_query->bind_param("ss", $userLoggedIn, $education);
			$education_query->execute();
		}

		if ($contact != NULL) {
			$contact_query = $con->prepare("INSERT INTO profile_user VALUES
			('', ?, '', '', '', ?)");
			$contact_query->bind_param("ss", $userLoggedIn, $contact);
			$contact_query->execute();
		}

		$introduction_message = "Profile Introduction has been updated!<br><br>";
	} else {
		if ($location != NULL) {
			$location_query = $con->prepare("UPDATE profile_user SET address=? WHERE users=?");
			$location_query->bind_param("ss", $location, $userLoggedIn);
			$location_query->execute();
		}

		if ($company != NULL) {
			$company_query = $con->prepare("UPDATE profile_user SET works=? WHERE users=?");
			$company_query->bind_param("ss", $company, $userLoggedIn);
			$company_query->execute();
		}

		if ($education != NULL) {
			$education_query = $con->prepare("UPDATE profile_user SET education=? WHERE users=?");
			$education_query->bind_param("ss", $education, $userLoggedIn);
			$education_query->execute();
		}

		if ($contact != NULL) {
			$contact_query = $con->prepare("UPDATE profile_user SET contacts=? WHERE users=?");
			$contact_query->bind_param("ss", $contact, $userLoggedIn);
			$contact_query->execute();
		}

		$introduction_message = "Profile Introduction has been updated!<br><br>";

		header("Location: settings.php");
	}
} else {
	$introduction_message = "";
}


if (isset($_POST['close_account'])) {
	header("Location: close_account.php");
}

//**************************************************
//Change Profile Image
if (isset($_POST['change_image'])) {
	if (isset($_FILES['image'])) {
		$fileName = $_FILES['image']['name'];
		$fileTmp = $_FILES['image']['tmp_name'];
		$fileSize = $_FILES['image']['size'];
		$fileError = $_FILES['image']['error'];
		$fileType = $_FILES['image']['type'];

		$fileNameNew = "";

		$fileExt = explode('.', $fileName);
		$fileActualExt = strtolower(end($fileExt));

		$allowed = array('jpg', 'jpeg', 'png');

		if (in_array($fileActualExt, $allowed)) {
			if ($fileError === 0) {
				if ($fileSize < 1000000) {
					$fileNameNew = "profile" . $userLoggedIn . "." . $fileActualExt;
					$fileDestination = '../assets/images/profile_pics/' . $fileNameNew;

					$directory = 'assets/images/profile_pics/' . $fileNameNew;

					move_uploaded_file($fileTmp, $fileDestination);


					$insert_pic_query = $con->prepare("UPDATE users SET profile_pic=? WHERE username=?");
					$insert_pic_query->bind_param("ss", $directory, $userLoggedIn);
					$insert_pic_query->execute();

					header("Location: settings.php");
				}
			}
		}
	}
}

//**************************************************
//Change Cover Image
if (isset($_POST['change_cover'])) {
	if (isset($_FILES['cover_image'])) {
		$fileName = $_FILES['cover_image']['name'];
		$fileTmp = $_FILES['cover_image']['tmp_name'];
		$fileSize = $_FILES['cover_image']['size'];
		$fileError = $_FILES['cover_image']['error'];
		$fileType = $_FILES['cover_image']['type'];

		$fileNameNew = "";

		$fileExt = explode('.', $fileName);
		$fileActualExt = strtolower(end($fileExt));

		$allowed = array('jpg', 'jpeg', 'png');

		if (in_array($fileActualExt, $allowed)) {
			if ($fileError === 0) {
				if ($fileSize < 1000000) {
					$fileNameNew = "cover" . $userLoggedIn . "." . $fileActualExt;
					$fileDestination = '../assets/images/cover_pics/' . $fileNameNew;

					$directory = 'assets/images/cover_pics/' . $fileNameNew;

					move_uploaded_file($fileTmp, $fileDestination);

					$insert_pic_query = $con->prepare("UPDATE users SET cover_pic=? WHERE username=?");
					$insert_pic_query->bind_param("ss", $directory, $userLoggedIn);
					$insert_pic_query->execute();
					
					header("Location: settings.php");
				}
			}
		}
	}
}

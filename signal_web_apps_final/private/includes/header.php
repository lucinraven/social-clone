<?php
//to call config and classes: user, post, message, friends, notification
require '../private/config/config.php';
include("../private/includes/classes/User.php");
include("../private/includes/classes/Post.php");
include("../private/includes/classes/Message.php");
include("../private/includes/classes/Friends.php");
include("../private/includes/classes/Notification.php");

//To check if an user is Logged In; if not it will redirect to register.php
if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];

	$user_query =$con->prepare("SELECT * FROM users WHERE username=?");
	$user_query->bind_param("s", $userLoggedIn);
	$user_query->execute();

	$result = $user_query->get_result();
	$user = $result->fetch_assoc();
} else {
	header("Location: register.php");
}
?>

<html>

<head>
	<!-- $header's value are declared on each pages-->
	<title>Signal: <?php echo $header; ?></title>


	<link rel="shortcut icon" href="../assets/images/others/signalIcon.ico" />

	<!-- Javascript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="../assets/js/functions.js"></script>
	<script src="../assets/js/bootbox.min.js"></script>
	<script src="../assets/js/bootstrap.js"></script>


	<!-- CSS -->
	<link rel="stylesheet" href="../assets/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="../assets/css/stylesheet.css">
	<!-- FontAwesome -->
	<script src="https://kit.fontawesome.com/035ed2373e.js" crossorigin="anonymous"></script>
</head>

<body>
	<!-- HEADER -->
	<div class="header">
		<div class="logoContainer">
			<figure>
				<img src="../assets/images/others/signal3.png">
			</figure>
		</div>

		<div class="searchContainer">
			<form action="search.php" method="GET" name="search_form">
				<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" >
				<i class="fas fa-search search_btn"></i>
			</form>

			<div class="search_content">
				<div class="search_results"></div>
				<div class="search_results_footer_empty"></div>
			</div>
		</div>

		<!-- messages, notifications, and friendRequest -->
		<nav>
			<?php
			//Unread messages 
			$messages = new Message($con, $userLoggedIn);
			$num_messages = $messages->getUnreadNumber();

			//Unread notifications 
			$notifications = new Notification($con, $userLoggedIn);
			$num_notifications = $notifications->getUnreadNumber();

			//Unread notifications 
			$user_obj = new User($con, $userLoggedIn);
			$num_requests = $user_obj->getNumberOfFriendRequests();
			?>

			<!-- messages -->
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
				<i class="fa fa-envelope fa-lg"></i>
				<?php
				if ($num_messages > 0)
					echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
				?>
			</a>
			<!-- notifications -->
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fa fa-bell fa-lg"></i>
				<?php
				if ($num_notifications > 0)
					echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<!-- friendRequest -->
			<a href="requests.php">
				<i class="fa fa-users fa-lg"></i>
				<?php
				if ($num_requests > 0)
					echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>';
				?>
			</a>

		</nav>

		<!-- div for messages and notification -->
		<div class="dropdown_data_window" style="height:0px; padding:0px; border:none;"></div>
		<input type="hidden" id="dropdown_data_type" value="">

	</div>

	<script>
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';

		$(document).ready(function() {
			$('.dropdown_data_window').scroll(function() {
				//Div containing data
				var inner_height = $('.dropdown_data_window').innerHeight();
				var scroll_top = $('.dropdown_data_window').scrollTop();
				var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
				var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

				if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {
					//Holds name of page to send ajax request to
					var pageName;
					var type = $('#dropdown_data_type').val();

					if (type == 'notification') {
						pageName = "ajax_load_notifications.php";
					} else if (type = 'message') {
						pageName = "ajax_load_messages.php";
					}

					var ajaxReq = $.ajax({
						url: "/private/includes/handlers/" + pageName,
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
						cache: true,

						success: function(response) {
							//Removes current .nextpage
							$('.dropdown_data_window').find('.nextPageDropdownData').remove();
							//Removes current .nextpage 
							$('.dropdown_data_window').find('.noMoreDropdownData').remove();
							$('.dropdown_data_window').append(response);
						}
					});

				} //End if 
				return false;
			}); //End (window).scroll(function())
		});
	</script>
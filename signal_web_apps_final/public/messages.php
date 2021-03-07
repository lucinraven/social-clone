<?php
//to activate the class "active"
$active = 'message';
//to change the header title
$header = "Message";

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//to call new Message class
$message_obj = new Message($con, $userLoggedIn);

//to get the user message
if (isset($_GET['u'])) {
	$user_to = $_GET['u'];
} else {
	$user_to = $message_obj->getMostRecentUser();
	if ($user_to == false) {
		$user_to = 'new';
	}
}

//to knwo if the new message
if ($user_to != "new") {
	$user_to_obj = new User($con, $user_to);
}

//to post new messages
if (isset($_POST['post_message'])) {
	if (isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date);
	}
}

?>

<!--MESSAGE MAIN CONTAINER-->
<div class="message_column">

	<!--CONVERSATIONS FROM: users-->
	<div class="user_details column" id="conversations">
		<h4>Conversations</h4>

		<div class="loaded_conversations">
			<?php echo $message_obj->getConvos(); ?>
		</div>
		<br>
		<a href="messages.php?u=new">New Message</a>
	</div>

	<!--THE MESSAGES with the choosen user-->
	<div class="message_wrapper new">

		<?php
		if ($user_to != "new") {
			echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";
			echo "<div class='loaded_messages' id='scroll_messages'>";
			echo $message_obj->getMessages($user_to);
			echo "</div>";
		} else {
			echo "<h4>New Message</h4>";
		}
		?>

		<!--NEW MESSAGE-->
		<div class="message_post">
			<form action="" method="POST">
				<?php
				if ($user_to == "new") {
					echo "Select the friend you would like to message <br><br>";
				?>
					To: <input type='text' class='to_input' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

				<?php
					echo "<div class='results'></div>";
				} else {
					echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
					echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
				}

				?>
			</form>
		</div>

		<!--SCROLLABLE MESSAGES-->
		<script>
			var div = document.getElementById("scroll_messages");
			div.scrollTop = div.scrollHeight;
		</script>
	</div>

</div>

</body>

</html>
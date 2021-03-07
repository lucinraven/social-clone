<?php
//to activate the class "active", which is none
$active = "";
//to change the header title
$header = "Notification";

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//to get POST ID
if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else {
	$id = 0;
}
?>

<!--NOTIFICATION MAIN CONTAINER-->
<div class="notification_column">

	<!--to show the notified posts-->
	<div class="posts_area ">
		<?php
		$post = new Post($con, $userLoggedIn);
		$post->getSinglePost($id);
		?>
	</div>

</div>

</body>

</html>
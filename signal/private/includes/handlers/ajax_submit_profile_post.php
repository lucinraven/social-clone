<?php
//to call config and classes: user, post, notification
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");
include("../classes/Notification.php");

//to submitted post from post
if(isset($_POST['post_body'])) {
	$post = new Post($con, $_POST['user_from']);
	$post->submitPost($_POST['post_body'], $_POST['user_to']);
}
	
?>
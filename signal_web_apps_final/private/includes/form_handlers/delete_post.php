<?php
//to call the config
require '../../config/config.php';

//to get post id
if (isset($_GET['post_id'])) {
	$post_id = $_GET['post_id'];
}

//to get the bootbox result
if (isset($_POST['result'])) {
	//if true delete the post
	if ($_POST['result'] == 'true') {

		$delete_query = $con->prepare("DELETE FROM posts WHERE id=?");
		$delete_query->bind_param("i", $post_id);
		$delete_query->execute();
	}
}

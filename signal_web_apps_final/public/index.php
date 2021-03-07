<?php
//to activate the class "active"
$active = 'index';
//to change the header title
$header = "Home";

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//To submit post
if (isset($_POST['post'])) {
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
}


?>
<!-- CREATE NEW POST MODAL-->
<div id="home-modal-wrapper">
	<!--overlay, to close the modal-->
	<div id="overlay" onclick="closeModal()"></div>
	<div class="post-modal-wrapper">
		<div id="modal-pst-ctn">
			<div id="modal" class="modal-post">
				<form action="index.php" method="post" class="form-pst-ctn">
					<div class="modal-header">
						<div class=""></div>
						<h1>Create Post</h1>
						<!--a close button for the modal-->
						<button onclick="closeModal()" class="close-btn"><i class="fas fa-times"></i></button>
					</div>

					<div class="modal-content-create">
						<figure class="modal-profile-icon">
							<img src="../<?php echo $user['profile_pic']; ?>">
						</figure>
						<p><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></p>
						<textarea type="text" class="post-create" name="post_text" placeholder="What's on your mind?"></textarea>
					</div>

					<div class="post-btn-ctn">
						<button class="post-btn" type="submit" name="post">Post</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- HOMEPAGE MAIN CONTAINER-->
<div class="index_column">
	<!--to open the create new modal-->
	<div class="createContainer" onclick="openModal()">
		<figure class="profile-icon">
			<img src="../<?php echo $user['profile_pic']; ?>">
		</figure>
		<p class="post-modal">What's on your mind?</p>
	</div>

	<!--to call the posts (see script)-->
	<div class="posts_area"></div>

	<!--to appear and disappear-->
	<div id="loading">
		<i class="fas fa-spinner fa-spin"></i>
	</div>


</div>

<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		//show the loading icon
		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "../private/includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn,
			cache: false,

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function() {
			//Div containing posts
			var height = $('.posts_area').height(); 
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "../private/includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache: false,

					success: function(response) {
						//Removes current .nextpage 
						$('.posts_area').find('.nextPage').remove(); 
						//Removes current .nextpage 
						$('.posts_area').find('.noMorePosts').remove(); 

						//hide the loading; if there are posts
						$('#loading').hide();
						$('.posts_area').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});
</script>

</body>

</html>
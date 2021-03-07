<?php
//to activate the class "active"
$active = "profile";
//to change the header title
$header = 'Profile';

//to call header and navigation
include("../private/includes/header.php");
include("../private/includes/navigation.php");

//to call new Message and Friends class
$message_obj = new Message($con, $userLoggedIn);
$friend_obj = new Friends($con, $userLoggedIn);

//to get the owner of the profile
if (isset($_GET['profile_username'])) {
  $username = $_GET['profile_username'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
  $user_array = mysqli_fetch_array($user_details_query);

  //if the user doesn't exist
  if($user_array == NULL){
    header("Location: error.php");
  }

  $profileuser_details_query = mysqli_query($con, "SELECT * FROM profile_user WHERE users='$username'");
  $profile_array = mysqli_fetch_array($profileuser_details_query);

  $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

//submitted remove friend
if (isset($_POST['remove_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->removeFriend($username);
}

//submitted add friend
if (isset($_POST['add_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->sendRequest($username);
}

//submitted respond to the profile friend request
if (isset($_POST['respond_request'])) {
  header("Location: requests.php");
}

//submitted message
if (isset($_POST['post_message'])) {
  if (isset($_POST['message_body'])) {
    $body = mysqli_real_escape_string($con, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }

  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script> 
          $(function() {
              $('" . $link . "').tab('show');
          });
        </script>";
}

//display for introduction
$aDisplay = "style = 'display:none;'";
$wDisplay = "style = 'display:none;'";
$eDisplay = "style = 'display:none;'";
$cDisplay = "style = 'display:none;'";

//if user doesn't exist in database table
if ($profile_array != NULL) {
  $locationValue = $profile_array['address'];
  $companyValue = $profile_array['works'];
  $educationValue = $profile_array['education'];
  $contactValue = $profile_array['contacts'];

  if ($locationValue == NULL || $locationValue == "") {
    $aDisplay = "style = 'display:none;'";
  } else {
    $aDisplay = "";
  }
  if ($companyValue == NULL || $companyValue == "") {
    $wDisplay = "style = 'display:none;'";
  } else {
    $wDisplay = "";
  }
  if ($educationValue == NULL || $educationValue == "") {
    $eDisplay = "style = 'display:none;'";
  } else {
    $eDisplay = "";
  }
  if ($contactValue == NULL || $contactValue == "") {
    $cDisplay = "style = 'display:none;'";
  } else {
    $cDisplay = "";
  }
}

?>

<!--HEADER OF PROFILE PAGE-->
<div class="profile_top">
  <img class="cover" src="../<?php echo $user_array['cover_pic']; ?>">
  <div class="display_image">
    <figure>
      <img src="../<?php echo $user_array['profile_pic']; ?>">
    </figure>
  </div>
  <div class="profile_header">
    <h1><?php echo $user_array['first_name'] ?> <?php echo $user_array['last_name'] ?></h1>
    <p><?php echo $user_array['username'] ?></p>
  </div>
</div>

<!--MAIN CONTAINER OF PROFILE PAGE-->
<div class="profile_main_column">
  <div class="profile_row">
    <div class="friend">
      <form action="<?php echo $username; ?>" method="POST">
        <?php
        //if the user is closed
        $profile_user_obj = new User($con, $username);
        if ($profile_user_obj->isClosed()) {
          header("Location: user_closed.php");
        }

        $logged_in_user_obj = new User($con, $userLoggedIn);

        //if the user is not the paged user: it can add, remove, and accept friend
        if ($userLoggedIn != $username) {
          if ($logged_in_user_obj->isFriend($username)) {
            echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
          } else if ($logged_in_user_obj->didReceiveRequest($username)) {
            echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
          } else if ($logged_in_user_obj->didSendRequest($username)) {
            echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
          } else
            echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
        }

        ?>
      </form>

      <?php
      if ($userLoggedIn != $username) {
        echo '<div class="profile_info_bottom">';
        echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends";
        echo '</div>';
      }


      ?>
    </div>

    <!--PROFILE TABS -->
    <ul class="nav nav-tabs" role="tablist" id="profileTabs">
      <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
      <li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
      <li role="presentation"><a href="#friends_div" aria-controls="friends_div" role="tab" data-toggle="tab"><?php $num_friends ?> Friends</a></li>
    </ul>
  </div>
  <div class="content_row">
    <!-- ACCOUNT PROFILE INTRODUCTION WIDGET-->
    <div class="introduction_div">
      <h2>Introduction</h2>
      <span <?php echo $aDisplay; ?>><i class="fas fa-home"></i><span>Lives in: </span>
        <p><?php echo $profile_array['address'] ?></p>
      </span>
      <span <?php echo $wDisplay; ?>><i class="fas fa-briefcase"></i><span>Works in: </span>
        <p><?php echo $profile_array['works'] ?></p>
      </span>
      <span <?php echo $eDisplay; ?>><i class="fas fa-user-graduate"></i><span>Education: </span>
        <p><?php echo $profile_array['education']; ?></p>
      </span>
      <span><i class="fas fa-envelope"></i><span>Email: </span>
        <p><?php echo $user_array['email'];  ?></p>
      </span>
      <span <?php echo $cDisplay; ?>><i class="fas fa-phone"></i><span>Contact: </span>
        <p><?php echo $profile_array['contacts']; ?></p>
      </span>
    </div>

    <!--CONTAINER OF THE TABS -->
    <div class="tab-content">
      <!--POST CONTENT -->
      <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
        <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post Something">
        <div class="posts_area"></div>
        <!--to appear and disappear-->
        <div id="loading">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
      </div>

      <!--MESSAGE CONTENT -->
      <div role="tabpanel" class="tab-pane fade" id="messages_div">
        <?php
        echo "<h4>You and <a href='" . $username . "'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
        echo "<div class='loaded_messages' id='scroll_messages'>";
        echo $message_obj->getMessages($username);
        echo "</div>";
        ?>
        <div class="message_post">
          <form action="" method="POST">
            <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
            <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
          </form>
        </div>

        <script>
          var div = document.getElementById("scroll_messages");
          div.scrollTop = div.scrollHeight;
        </script>
      </div>

      <!--FRIEND CONTENT -->
      <div role="tabpanel" class="tab-pane fade" id="friends_div">
        <?php echo $friend_obj->getFriends($username); ?>
      </div>

    </div>

  </div>
</div>

<!-- POST MODAL -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">Post something!</h4>
      </div>

      <div class="modal-body">
        <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>

        <form class="profile_post" action="" method="POST">
          <div class="form-group">
            <textarea class="form-control" name="post_body"></textarea>
            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
            <input type="hidden" name="user_to" value="<?php echo $username; ?>">
          </div>
        </form>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>


<script>
  var userLoggedIn = '<?php echo $userLoggedIn; ?>';
  var profileUsername = '<?php echo $username; ?>';

  $(document).ready(function() {

    $('#loading').show();

    //Original ajax request for loading first posts 
    $.ajax({
      url: "../private/includes/handlers/ajax_load_profile_posts.php",
      type: "POST",
      data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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
          url: "../private/includes/handlers/ajax_load_profile_posts.php",
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
          cache: false,

          success: function(response) {
            //Removes current .nextpage 
            $('.posts_area').find('.nextPage').remove();
            //Removes current .nextpage 
            $('.posts_area').find('.noMorePosts').remove();

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
<!-- SIDE BAR -->
<div class="sidebar">
    <a href="<?php echo $userLoggedIn; ?>" class="<?php if($active == 'profile'){
            echo 'active'; 
        } ?>">
        <figure>
            <img src="../<?php echo $user['profile_pic']; ?>">
        </figure>
        <?php echo $user['first_name']; ?>
    </a>

    <div class="nav-tabs">
        <a href="index.php" class="<?php if($active == 'index'){
            echo 'active'; 
        } ?>"><i class="far fa-newspaper"></i>Newsfeed</a>
        <a href="messages.php" class="<?php if($active == 'message'){
            echo 'active'; 
        } ?>"><i class="fas fa-envelope"></i>Messages</a>
        <a href="settings.php" class="<?php if($active == 'settings'){
            echo 'active'; 
        } ?>"><i class="fas fa-cog"></i>Settings</a>
        <a href="../private/includes/handlers/logout.php"><i class="fa fa-sign-out fa-lg"></i>Logout</a>
    </div>
</div>
<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );
include( 'classes/Post.php' );
include( 'classes/Image.php' );
include( 'classes/Notify.php' );

$username = '';
$profile_img = '';
$posts = '';
$verified = false;
$is_followed = false;

if ( Login::isLoggedIn() ) {

} else {
	die( 'Not logged in' ) ;
}

if ( isset($_GET['username'] ) ) {
	if ( DB::query( 'SELECT username FROM users WHERE username = :username', array( ':username' => $_GET['username'] ) ) ) {

		$username = DB::query( 'SELECT username FROM users WHERE username = :username', array( ':username' => $_GET['username'] ) )[0]['username'];
		$profile_img = DB::query( 'SELECT profileimg FROM users WHERE username = :username', array( ':username' => $_GET['username'] ) )[0]['profileimg'];
		$user_id = DB::query( 'SELECT id FROM users WHERE username = :username', array( ':username' => $_GET['username'] ) )[0]['id'];
		$verified = DB::query( 'SELECT verified FROM users WHERE username = :username', array( ':username' => $_GET['username'] ) )[0]['verified'];
		$logged_user_id = Login::isLoggedIn();


		$followed = DB::query( 'SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id = :followerid', array( ':userid' => $user_id, ':followerid' => $logged_user_id ) );

		if ( ! empty( $followed ) ) {
			$is_followed = true;
		}

		//follow button
		if ( isset( $_POST['follow'] ) ) {

			if ( $user_id !== $logged_user_id ) {

				if ( empty( $followed ) ) {
					if ( $logged_user_id == 1 ) {
						DB::query( 'UPDATE users SET verified = 1 WHERE id = :userid', array( ':userid' => $user_id ) );
					}
					DB::query( 'INSERT INTO followers VALUES ( id, :userid, :followerid )', array( ':userid' => $user_id, ':followerid' => $logged_user_id ) );

				} else {
					echo 'Already following!';
				}
				$is_followed = true;

			}
		}

		//unfollow button
		if ( isset( $_POST['unfollow'] ) ) {
			if ( $user_id !== $logged_user_id ) {
				if ( ! empty( $followed ) ) {
					if ( $logged_user_id == 1 ) {
						DB::query( 'UPDATE users SET verified = 0 WHERE id = :userid', array( ':userid' => $user_id ) );
					}
					DB::query( 'DELETE FROM followers WHERE user_id = :userid AND follower_id = :followerid', array( ':userid' => $user_id, ':followerid' => $logged_user_id ) );
				}
				$is_followed = false;
			}
		}

		//create posts
		if ( isset( $_POST['post'] ) ) {
			if ( $_FILES['postimg']['size'] == 0 ) {
				Post::create_post( $_POST['postbody'], $logged_user_id, $user_id );
			} else {
				$post_id = Post::create_img_post( $_POST['postbody'], $logged_user_id, $user_id );
				Image::upload_image( 'postimg', 'UPDATE posts SET postimg = :postimg WHERE id = :postid', array( ':postid' => $post_id ) );
			}
		}

		//delete posts
		if ( isset( $_POST['deletepost'] ) ) {
			$post_exist = DB::query( 'SELECT id from posts WHERE id = :postid AND user_id = :userid', array( ':postid' => $_GET['postid'], ':userid' => $logged_user_id ) );
			if ( $post_exist ) {
				DB::query( 'DELETE FROM posts WHERE id = :postid AND user_id = :userid', array( ':postid' => $_GET['postid'], ':userid' => $logged_user_id ) );
				DB::query( 'DELETE FROM post_likes WHERE post_id = :postid', array( ':postid' => $_GET['postid'] ) );
				echo 'Post deleted';
			}
		}

		//insert likes in db
		if ( isset( $_GET['postid'] ) && ! isset( $_POST['deletepost'] ) ) {
			Post::like_post( $_GET['postid'], $logged_user_id );
		}

		//display posts
		$posts = Post::display_post( $user_id, $username, $logged_user_id );

	} else {
		die( 'User not found' );
	}
}

?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Proiect PHP - retea sociala</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
		<link href="assets/css/bootstrap.css" rel="stylesheet">
		<link href="assets/css/style.css" rel="stylesheet">
	</head>
	<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Social network</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

			<ul class="nav navbar-nav navbar-right">
				<li><a href="#">My profile</a></li>
				<li><a href="index.php">Home</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Account <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="my-account.php">Profile Settings</a></li>
						<li><a href="change-password.php">Change password</a></li>
						<li><a href="logout.php">Loggout</a></li>
					</ul>
				</li>
			</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="posts-container">
					<img src="<?php echo $profile_img ?>" alt="" class="profile-img">
					<h3 class="profile-username"><?php echo $username; ?>'s Profile <?php if ( $verified ) { echo '-Verified'; } ?></h3>
					<form action="profile.php?username=<?php echo $username; ?>" method="post">
						<?php
						if ( isset( $_GET['username'] ) ) {
							if ( $user_id !== $logged_user_id ) {
								if ( $is_followed === true ) {
									echo '<input type="submit" name="unfollow" value="Unfollow">';
								} else {
									echo '<input type="submit" name="follow" value="Follow">';
								}
							}
						}
						?>
					</form>

					<form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
						<textarea class="form-control" name="postbody" cols="80" rows="5"></textarea>
						<br/>Upload an image:
						<input type="file" name="postimg">
						<input class="profile-post-btn btn btn-primary" type="submit" name="post" value="Post">
					</form>

					<div class="posts">
						<?php echo $posts;?>
					</div>
				</div> <!--/.post-container-->
			</div> <!--/.col-xs-12-->
		</div> <!--/.row-->
	</div> <!--/.container-->

<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>
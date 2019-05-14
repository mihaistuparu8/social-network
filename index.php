<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader
require 'vendor/autoload.php';

include( 'classes/DB.php' );
include( 'classes/Login.php' );
include( 'classes/Post.php' );
include( 'classes/Comment.php' );


$show_timeline = false;

if ( Login::isLoggedIn() ) {
	$logged_user_id = Login::isLoggedIn();
	$logged_username = DB::query( 'SELECT username FROM users WHERE id = :id', array( ':id' => $logged_user_id) )[0]['username'];
	$show_timeline = true;
} else {
	die( 'Not logged in' );
}

// insert likes in db
if ( isset( $_GET['postid'] ) ) {
	Post::like_post( $_GET['postid'], $logged_user_id );
}

// insert comments in db
if ( isset( $_POST['comment'] ) ) {
	Comment::create_comment( $_POST['commentbody'], $_GET['postid'], $logged_user_id );
}

// search box
if ( isset( $_POST['searchbox'] ) ) {

	$tosearch = explode( " ", $_POST['searchbox'] );
	if ( count( $tosearch == 1 ) ) {
		$tosearch = str_split( $tosearch[0], 2 );
	}
	$whereclause = '';
	$params_array = array(
		':username' => '%' . $_POST['searchbox'] . '%',
	);
	for ( $i = 0; $i <= count( $tosearch ) - 1; $i++ ) {
		$whereclause .= " OR username LIKE :u$i ";
		$params_array[ ":u$i" ] = $tosearch[$i];
	}

	$users = DB::query( 'SELECT users.username FROM users WHERE users.username LIKE :username ' . $whereclause, $params_array );

	$whereclause = '';
	$params_array = array(
		':body' => '%' . $_POST['searchbox'] . '%',
	);
	for ( $i = 0; $i <= count( $tosearch ) - 1; $i++ ) {
		if ( $i % 2 ) {
			$whereclause .= " OR body LIKE :p$i ";
			$params_array[ ":p$i" ] = $tosearch[$i];
		}
	}
	$posts = DB::query( 'SELECT posts.body FROM posts WHERE posts.body LIKE :body ' . $whereclause, $params_array );
	var_dump(count( $tosearch ));

	echo '<pre>';
	print_r( $users );
	print_r( $posts );
	echo '</pre>';
}

function display_timeline_posts() {

	global $logged_user_id;

	$following_posts = DB::query(
		'SELECT posts.body, posts.likes, posts.id, users.username
		FROM users, posts, followers
		WHERE posts.user_id = followers.user_id
		AND users.id = posts.user_id
		AND follower_id = :userid
		ORDER BY posts.likes DESC',
		array( ':userid' => $logged_user_id )
	);

	foreach ( $following_posts as $post ) {

		$like_btn_name = array();
		$like_exists = DB::query( 'SELECT post_id FROM post_likes WHERE post_id = :postid AND user_id = :userid', array( ':postid' => $post['id'], ':userid' => $logged_user_id ) );
		$like_btn_name = empty( $like_exists ) ? array( 'like', 'Like' ) : array( 'unlike', 'Unike' );

		$like_btn = "<form action='index.php?postid=" . $post['id'] . "' method = 'post'>
						<button class='btn like-btn' type='submit' name='" . $like_btn_name[0] . "' value='" . $like_btn_name[1] . "'>
							<svg class='heart-svg' viewBox='467 392 58 57' xmlns='http://www.w3.org/2000/svg'>
								<g id='Group' fill='none' fill-rule='evenodd' transform='translate(467 392)'>
								<path d='M29.144 20.773c-.063-.13-4.227-8.67-11.44-2.59C7.63 28.795 28.94 43.256 29.143 43.394c.204-.138 21.513-14.6 11.44-25.213-7.214-6.08-11.377 2.46-11.44 2.59z' id='heart' fill='#AAB8C2'/>
								</g>
							</svg>
						</button>
						<span>" . $post['likes'] . " likes</span>
					</form>";

		echo '<div class="posts-container"><ul class="posts">';
			echo '<li><a href="profile.php?username=' . $post['username'] . '"><span class="posts-username">' . $post['username'] . '</span></a> ' . '<span class="like-form">' . $like_btn . '</span> <p>' . htmlspecialchars( $post['body'] ) . '</p></li>';
		echo '</ul></div>';
		?>

		<div class="posts-container">
			<form action="index.php?postid=<?php echo $post['id']; ?>" method="post">
				<div class="form-group">
					<textarea name="commentbody" class="form-control status-box" rows="2" placeholder="Adauga un comentariu"></textarea>
				</div>
				<div class="button-group pull-right">
					<button type="submit" name="comment" class="btn btn-primary">Comment</button>
				</div>
			</form>
		</div>

		<?php
		Comment::display_comments( $post['id'] );
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
				<li>
					<form class="search-form" action="index.php" method="post">
						<input type="text" name="searchbox" value="">
						<input type="submit" name="search" value="Search...">
					</form>
				</li>
				<li><a href="profile.php?username=<?php echo $logged_username; ?>">My profile</a></li>
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
				<?php display_timeline_posts();?>
			</div> <!--/.col-xs-12-->
		</div> <!--/.row-->
	</div> <!--/.container-->

<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>
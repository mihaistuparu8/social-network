<?php

include( 'classes/DB.php' );
include( 'classes/Image.php' );
include( 'classes/Login.php' );

if ( Login::isLoggedIn() ) {
	$logged_user_id = Login::isLoggedIn();
} else {
	die('Not logged in');
}

if ( isset( $_POST['uploadprofileimage'] ) ) {

	Image::upload_image('profileimg', 'UPDATE users SET profileimg = :profileimg WHERE id = :userid', array( ':userid' => $logged_user_id ) );
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

	<div class="wrapper">
		
		<form class="form-signin" action="my-account.php" method="post" enctype="multipart/form-data">
			<h2 class="form-signin__heading">My Account</h2>
			<label for="profileimg">Upload a profile image:</label>
			<input type="file" name="profileimg">
			<input class="form-signin__btn btn btn-lg btn-primary btn-block" type="submit" name="uploadprofileimage" value="Upload Image">
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>



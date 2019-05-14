<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );

if (!Login::isLoggedIn()) {
	die("Not logged in");
}

	if ( isset( $_POST['confirm'] ) ) {
		if ( isset( $_POST['alldevices'] ) ) {
			DB::query('DELETE FROM login_tokens WHERE user_id = :user_id', array(':user_id' => Login::isLoggedIn() ) );
		} else {
			if ( isset( $_COOKIE['SNID'] ) ) {
				DB::query( 'DELETE FROM login_tokens WHERE token = :token', array(':token' => sha1( $_COOKIE['SNID']) ) );
			}
			setcookie( 'SNID', '1', time()-3600 );
			setcookie( 'SNID_', '1', time()-3600 );
			header( 'Location: login.php' );
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

	<div class="wrapper">
		<form class="form-signin" action="logout.php" method="post">
			<h2 class="form-signin__heading">Logout of your Account?</h2>
			<p>Are you sure you'd like to logout?</p>
			<div class="form__checkbox">
				<input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
			</div>
			<button class="form-signin__btn btn btn-lg btn-primary btn-block" name="confirm" type="submit">Confirm</button>
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>
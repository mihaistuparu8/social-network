<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );

$form_error_msg = '';

if ( isset( $_POST['login'] ) ) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	$username_exists = DB::query( 'SELECT * FROM users WHERE username = :username', array( ':username' => $username ) );
	if ( $username_exists ) {
		$db_password = DB::query( 'SELECT password FROM users WHERE username = :username', array( 'username' => $username ) )[0]['password'];
		if ( password_verify( $password, $db_password ) ) {
			header( 'Location: index.php' );

			$cstrong = true;
			$token = bin2hex( openssl_random_pseudo_bytes( 64, $cstrong ) );

			$user_id = DB::query('SELECT id FROM users WHERE username = :username', array( ':username' => $username ))[0]['id'];
			DB::query('INSERT INTO login_tokens VALUES ( id, :token, :user_id)', array( ':token' => sha1( $token ), ':user_id' => $user_id ) );

			setcookie( "SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE );
			setcookie( "SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE );

		} else {
			$form_error_msg = 'The user or password is incorrect!';
		}
	} else {
		$form_error_msg = 'The user or password is incorrect!';
	}
}

if ( Login::isLoggedIn() ) {
	$logged_user_id = Login::isLoggedIn();
	$user_name = DB::query( 'SELECT username FROM users WHERE id = :id', array( ':id' => $logged_user_id ) )[0]['username'];
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
		<form class="form-signin" action="login.php" method="post">
			<h2 class="form-signin__heading">Please login</h2>
			<label for="username">Username</label>
			<input type="text" class="form-control" name="username" placeholder="Username" required="" autofocus="" />
			<label for="password">Password</label>
			<input type="password" class="form-control" name="password" placeholder="Password" required=""/>
			<!-- <label class="checkbox">
				<input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
			</label> -->
			<button class="form-signin__btn btn btn-lg btn-primary btn-block" name="login" type="submit">Login</button>
			<a href="forgot-password.php">Forgot your password?</a>
			<a href="create-account.php">Sing Up</a>
			<p class="form-error"><?php echo $form_error_msg ; ?></p>
			<p class="copyright-text">&copy; 2018 Stuparu Mihai Iulian ALL RIGHTS RESERVED</p>
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>

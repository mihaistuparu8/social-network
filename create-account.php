<?php

include( 'classes/DB.php' );
include( 'classes/Mail.php' );

$form_error_msg = '';
$form_success_msg = '';

function register( $username, $password, $rpassword, $email ) {

	global $form_error_msg;
	global $form_success_msg;

	// check if the password and repeat password matches
	if ( $password !== $rpassword ) {
		$form_error_msg = 'The repeated password must be the same!';
		return;
	}
	// check if username and email exists
	$accont_exists = DB::query( 'SELECT * FROM users WHERE username = :username', array( ':username' => $username ) );
	$email_exists = DB::query( 'SELECT * FROM users WHERE email = :email', array( ':email' => $email) );
	if ( $accont_exists) {
		$form_error_msg = 'The username already exists!';
		return;
	}
	if ( $email_exists ) {
		$form_error_msg = 'The email already exists!';
		return;
	}
	// check username characters length
	if ( strlen( $username ) < 5 || strlen( $username ) > 32 ) {
		$form_error_msg = 'The username characters length must be between 5 and 32 characters';
		return;
	}
	// TODO: check preg_match - the user should add only numbers & letters and underscores
	// check username characters format
	if ( preg_match( '/[a-zA-z0-9_]+/', $username ) === 0 ) {
		$form_error_msg = 'Invalid username characters format. You can use only letters, numbers and underscore.';
		return;
	}
	// email validation
	if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		$form_error_msg = 'Invalid email';
		return;
	}
	// check password characters length
	if ( strlen( $password ) < 6 || strlen( $password ) > 60 ) {
		$form_error_msg = 'Invalid password';
		return;
	}
	DB::query( 'INSERT INTO users VALUES ( id, :username, :password, :email, \'0\', \'\' )',
		array(
			':username' => $username,
			':password' => password_hash( $password, PASSWORD_BCRYPT ),
			':email'    => $email,
		)
	);
	$form_success_msg = 'You have registered successfully! Please check your email';
	Mail::sendMail( 'Wlecome to Wakanda!', 'Your account has been created', $email );
}

if ( isset( $_POST['createaccount'] ) ) {
	register( $_POST['username'], $_POST['password'], $_POST['rpassword'], $_POST['email'] );
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
		<form class="form-signin" action="create-account.php" method="post">
			<h2 class="form-signin__heading">Register</h2>
			<label for="username">Username</label>
			<input class="form-control" type="text" name="username" placeholder="Username">
			<label for="password">Password</label>
			<input class="form-control" type="password" name="password" placeholder="Password">
			<label for="rpassword">Password</label>
			<input class="form-control" type="password" name="rpassword" placeholder="Repeat Password">
			<label for="email">Email</label>
			<input class="form-control" type="email" name="email" placeholder="Your Email">
			<input class="form-signin__btn btn btn-lg btn-primary btn-block" type="submit" name="createaccount" value="Create Account">
			<p class="form-error"><?php echo $form_error_msg ; ?></p>
			<p class="form-success"><?php echo $form_success_msg ; ?></p>
			<a href="login.php">Back to login page</a>
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>


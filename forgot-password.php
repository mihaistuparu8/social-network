<?php

include( 'classes/DB.php' );
include( 'classes/Mail.php' );

$form_error_msg = '';
$form_success_msg = '';

if ( isset( $_POST['resetpassword'] ) ) {

	$cstrong = true;
	$token = bin2hex( openssl_random_pseudo_bytes( 64, $cstrong ) );
	$email = $_POST['email'];
	if ( ! empty ( $email ) && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		$user_id =  DB::query('SELECT id FROM users WHERE email = :email', array( ':email' => $email ) );
		if ( ! empty( $user_id ) ) {
			$user_id = $user_id[0]['id'];
			DB::query('INSERT INTO password_tokens VALUES ( id, :token, :user_id )', array( ':token' => sha1( $token ), ':user_id' => $user_id ) );
			$form_success_msg = 'Au fost trimise instructuni pentru resetarea parolei la adresa de email!';
			//echo $token;
			//this will be used to allow the user to add a new password
			// change-password.php?token=$token
			$reset_link = 'Click on the following link to reset your account password: <a href="http://localhost/socialnetwork/change-password.php?token=' . $token . '">http://localhost/socialnetwork/change-password.php?token=' . $token . '</a>';
			Mail::sendMail( 'Forgot password', $reset_link, $email );
		} else {
			$form_error_msg = 'Adresa de email nu exista!';
		}
	} else {
		$form_error_msg = 'Adresa de email invalida!';
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
		<form class="form-signin" action="forgot-password.php" method="post">
			<h2 class="form-signin__heading">Forgot Password </h2>
			<input type="email" class="form-control" name="email" placeholder="Email Address" required="" autofocus="" />
			<button class="btn btn-lg btn-primary btn-block" name="resetpassword" type="submit">Reset Password</button>
			<p class="form-error"><?php echo $form_error_msg ; ?></p>
			<p class="form-success"><?php echo $form_success_msg ; ?></p>
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>

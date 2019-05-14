<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );
$token_is_valid = false;
$form_error_msg = '';
$form_success_msg = '';

if ( Login::isLoggedIn() ) {

	if ( isset( $_POST['changepassword'] ) ) {
		$user_id = Login::isLoggedIn();
		$db_password = DB::query( 'SELECT password FROM users WHERE id = :id', array( ':id' => $user_id ) )[0]['password'];
		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];
		$newpasswordrepeat = $_POST['newpasswordrepeat'];

		if ( password_verify( $oldpassword, $db_password ) ) {
			if ( $newpassword === $newpasswordrepeat ) {
				if ( strlen( $newpassword ) >= 6 && strlen( $newpassword ) <= 60 ) {
					DB::query( 'UPDATE users SET password = :newpassword WHERE id = :id', array( ':newpassword' => password_hash( $newpassword, PASSWORD_BCRYPT ), ':id' => $user_id ) );
					$form_success_msg = 'Parola a fost schimbata cu succes!';
				}
			} else {
				$form_error_msg =  'Parolele nu se potrivesc!';
			}
		} else {
			$form_error_msg =  'Parola veche este incorecta!';
		}
	}

} else {

	if ( isset( $_GET['token'] ) ) {
		$token = $_GET['token'];
		if ( DB::query( 'SELECT user_id FROM password_tokens WHERE token = :token', array( ':token' => sha1( $token ) ) ) ) {
			$user_id = DB::query( 'SELECT user_id FROM password_tokens WHERE token = :token', array( ':token' => sha1( $token ) ) )[0]['user_id'];
			$token_is_valid = true;
			if ( isset( $_POST['changepassword'] ) ) {

				$newpassword = $_POST['newpassword'];
				$newpasswordrepeat = $_POST['newpasswordrepeat'];

				if ( $newpassword === $newpasswordrepeat ) {
					if ( strlen( $newpassword ) >= 6 && strlen( $newpassword ) <= 60 ) {
						DB::query( 'UPDATE users SET password = :newpassword WHERE id = :id', array( ':newpassword' => password_hash( $newpassword, PASSWORD_BCRYPT ), ':id' => $user_id ) );
						$form_success_msg = 'Parola a fost schimbata cu succes!';
						DB::query(' DELETE FROM password_tokens WHERE user_id = :user_id', array( ':user_id' => $user_id ) );
					}
				} else {
					$form_error_msg = 'Parolele nu se potrivesc!';
				}
			}
		} else {
			die( 'Token invalid' );
		}
	} else {
		die( 'Not logged in' );
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
		<form class="form-signin" action=" <?php if ( ! $token_is_valid ) { echo 'change-password.php'; } else { echo 'change-password.php?token=' . $token . ''; } ?> " method="post">
		<h2 class="form-signin__heading">Change Password </h2>
		<?php
		if ( ! $token_is_valid ) {
			echo '<label for="oldpassword">Old Password</label><input class="form-control" type="password" name="oldpassword" placeholder="Current password ...">';
		}
		?>
			<label for="newpassword">New Password</label>
			<input class="form-control" type="password" name="newpassword" placeholder="New password ...">
			<label for="newpasswordrepeat">Repeat new Password</label>
			<input class="form-control" type="password" name="newpasswordrepeat" placeholder="Repeat password ...">
			<input class="form-signin__btn btn btn-lg btn-primary btn-block" type="submit" name="changepassword" value="Change Password"><br>
			<p class="form-error"><?php echo $form_error_msg ; ?></p>
			<p class="form-success"><?php echo $form_success_msg ; ?></p>
			<a href="login.php">Inapoi la login</a>
		</form>
	</div>

	<script src="assets/js/jquery-3.3.1.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/script.js"></script>
	</body>
</html>

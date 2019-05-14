<?php
session_start();
$cstrong = true;
$token = bin2hex( openssl_random_pseudo_bytes( 64, $cstrong ) );
if ( ! isset( $_SESSION['token'] ) ) {
	$_SESSION['token'] = $token;
}
include( 'classes/DB.php' );
include( 'classes/Login.php' );

if ( Login::isLoggedIn() ) {
	$logged_user_id = Login::isLoggedIn();
} else {
	die( 'Not logged in' );
}

if ( isset( $_POST['send'] ) ) {

	if( ! isset( $_POST['nocsrf'] ) ) {
		die( 'Invalid token' );
	}
	if ( $_POST['nocsrf'] != $_SESSION['token'] ) {
		die( 'Invalid token' );
	}

	$user_id = DB::query( 'SELECT id FROM users WHERE id = :receiver', array( ':receiver' => htmlspecialchars( $_GET['receiver'] ) ) );
	if ($user_id) {
		$params = array(
			':body'     => $_POST['body'],
			':sender'   => $logged_user_id,
			':receiver' => htmlspecialchars( $_GET['receiver']),
		);
		DB::query( 'INSERT INTO messages VALUES ( ID, :body, :sender, :receiver, 0 )', $params );
		echo 'message sent';
	} else {
		die( 'invalid id' );
	}

	session_destroy();
}

?>


<h1> Send a message </h1>

<form action="send-messages.php?receiver=<?php echo htmlspecialchars( $_GET['receiver'] ); ?>" method="post">
	<textarea name="body" cols="80" rows="8"></textarea>
	<input type="submit" name="send" value="Send Message">
	<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
</form>
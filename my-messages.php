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
?>



<?php

if ( isset( $_GET['mid'] ) ) {
	$mymessage = DB::query( 'SELECT * FROM messages WHERE id = :mid AND receiver = :receiver OR sender = :sender',
		array(
			':mid' => $_GET['mid'],
			':receiver' => $logged_user_id,
			':sender' => $logged_user_id,
		)
	)[0];
	echo '<h1> View messages </h1>';
	echo htmlspecialchars( $mymessage['body'] );
	echo '<hr/>';

	if ( $mymessage['sender'] == $logged_user_id ) {
		$id = $mymessage['receiver'];
	} else {
		$id = $mymessage['sender'];
	}
	DB::query( 'UPDATE messages SET readed = 1 WHERE id = :mid', array( ':mid' => $_GET['mid'] ) );
?>

	<form action="send-messages.php?receiver=<?php echo htmlspecialchars( $id ); ?>" method="post">
		<textarea name="body" cols="80" rows="8"></textarea>
		<input type="submit" name="send" value="Send Message">
		<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
	</form>

<?php
} else {
	echo '<h1> My messages </h1>';
	$messages = DB::query( 'SELECT messages.*, users.username FROM messages, users
	WHERE receiver = :receiver OR sender = :sender
	AND messages.sender = users.id',
		array(
			':receiver' => $logged_user_id,
			':sender' => $logged_user_id,
		)
	);
	foreach ( $messages as $message ) {

		if ( strlen( $message['body'] ) > 10 ) {
			$m = substr( $message['body'], 0, 10 ) . '...';
		} else {
			$m = $message['body'];
		}
		if( $message['readed'] == 0 ) {
			echo '<a href="my-messages.php?mid=' . $message['id'] . '"><strong>' . $m . "</strong></a> sent by " . $message['username'] . '<hr/>';
		} else {
			echo '<a href="my-messages.php?mid=' . $message['id'] . '">' . $m . " sent by  " . $message['username'] . '</a><hr/>';
		}
	}
}


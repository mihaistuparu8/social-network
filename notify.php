<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );

if ( Login::isLoggedIn() ) {
	$logged_user_id = Login::isLoggedIn();
} else {
	die( 'Not logged in' );
}

echo '<h1>Notifications</h1>';
$notifications = DB::query( 'SELECT * FROM notifications WHERE receiver = :userid ORDER BY id DESC', array( ':userid' => $logged_user_id ) );

if ( $notifications ) {
	foreach ( $notifications as $n ) {
		if ( '1' === $n['type'] ) {
			$senderName = DB::query( 'SELECT username FROM users WHERE id = :senderID', array( ':senderID' => $n['sender'] ) )[0]['username'];
			if ( $n['extra'] == '' ) {
				echo 'You got a notification <hr />';
			} else {
				$extra = json_decode( $n['extra'] );
				echo $senderName. ' mentioned you in a post! ' . $extra->postbody . '<hr />';
			}
		} elseif ( '2' === $n['type'] ) {
			$senderName = DB::query( 'SELECT username FROM users WHERE id = :senderID', array( ':senderID' => $n['sender'] ) )[0]['username'];
			echo $senderName. ' liked your post <hr />';
		}
	}
}
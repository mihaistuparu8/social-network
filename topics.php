<?php

include( 'classes/DB.php' );
include( 'classes/Login.php' );
include( 'classes/Post.php' );
include( 'classes/Image.php' );


if ( isset( $_GET['topic'] ) ) {
	$get_topics = DB::query( 'SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)', array( ':topic' => $_GET['topic'] ) );
	if ( $get_topics ) {
		foreach( $get_topics as $topic ) {
			echo $topic['body'];
		}
	}
}

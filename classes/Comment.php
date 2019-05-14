<?php

class Comment {

	public static function create_comment( $commentbody, $post_id, $user_id ) {

		if ( strlen( $commentbody ) > 160 || strlen( $commentbody ) < 1 ) {
			die( 'Incorrect length!' );
		}

		if ( ! DB::query( 'SELECT id FROM posts WHERE id = :postid', array( ':postid' => $post_id ) ) ) {
			echo 'Invalid post ID';
		} else {
			DB::query( 'INSERT INTO comments VALUES (id, :comment, :userid, NOW(), :postid)', array( ':comment' => $commentbody, ':userid' => $user_id, ':postid' => $post_id ) );
		}

	}

	public static function display_comments( $post_id ) {

		$comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id; ', array( ':postid' => $post_id ) );
		echo '<div class="posts-container"><ul class="posts">';
		foreach ( $comments as $comment ) {
			echo '<li><a href="profile.php?username=' . $comment['username'] . '"><span class="posts-username">' . $comment['username'] . '</span></a> ' . $comment['comment'] . '</li>';
		}
		echo '</ul></div>';
	}

}
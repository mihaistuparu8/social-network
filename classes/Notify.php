<?php

class Notify {

	public function __construct() {

	}

	public static function create_notify( $text = "", $postId = 0 ) {
		var_dump(  $text  );
		var_dump(  $postId  );
		if (  $text == '' && $postId != 0 ) {
			$temp = DB::query(
				'SELECT posts.user_id AS reciever, post_likes.user_id AS sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id = :postid',
				array(
					'postid' => $postId,
				)
			);
			$r = $temp[0]['reciever'];
			$s = $temp[0]['sender'];
			DB::query( 'INSERT INTO notifications VALUES (id, :type, :reciever, :sender, :extra)',
				array(
					':type'     => 2,
					':reciever' => $r,
					':sender'   => $s,
					':extra'    => '',
				)
			);
		} else {
			$text = explode( ' ', $text );
			$notify = array();
			foreach ( $text as $word ) {
				if ( substr( $word, 0, 1 ) === '@') {
					$notify[substr( $word, 1 )] = array( 'type' => 1, 'extra' => '{ "postbody": "' . htmlentities( implode( $text, ' ' ) ) . '"} ');
				}
			}
			return $notify;
		}
	}
}
<?php

class Post {

	public static function create_post( $postbody, $logged_user_id, $profile_user_id ) {

		if ( strlen( $postbody ) > 160 || strlen( $postbody ) < 1 ) {
			die( 'Incorrect length!' );
		}

		$topics = self::get_topics( $postbody );

		if ( $logged_user_id == $profile_user_id ) {
			if ( count( Notify::create_notify( $postbody ) ) !== 0 ) {
				foreach ( Notify::create_notify( $postbody ) as $key => $n ) {
					$s = $logged_user_id;
					$r = DB::query( 'SELECT id FROM users WHERE username = :username', array( ':username' => $key ) );
					if ( ! empty( $r ) ) {
						$r = $r[0]['id'];
						DB::query( 'INSERT INTO notifications VALUES (id, :type, :reciever, :sender, :extra)',
							array(
								':type'     => $n['type'],
								':reciever' => $r,
								':sender'   => $s,
								':extra'    => $n['extra']
							)
						);
					}
				}
			}
			DB::query( 'INSERT INTO posts VALUES ( id, :postbody, NOW(), :userid, 0, \'\', :topics )', array( ':postbody' => $postbody, ':userid' => $logged_user_id, ':topics' => $topics ) );
		} else {
			die( 'You can post only on your profile!' );
		}
	}

	public static function create_img_post( $postbody, $logged_user_id, $profile_user_id ) {

		if ( strlen( $postbody ) > 160 ) {
			die( 'Incorrect length!' );
		}

		$topcis = self::get_topics( $postbody );

		if ( $logged_user_id == $profile_user_id ) {
			if ( count( Notify::create_notify( $postbody ) ) !== 0 ) {
				foreach ( Notify::create_notify( $postbody ) as $key => $n ) {
					$s = $logged_user_id;
					$r = DB::query( 'SELECT id FROM users WHERE username = :username', array( ':username' => $key ) );
					if ( ! empty( $r ) ) {
						$r = $r[0]['id'];
						DB::query( 'INSERT INTO notifications VALUES (id, :type, :reciever, :sender, :extra)',
							array(
								':type'     => $n['type'],
								':reciever' => $r,
								':sender'   => $s,
								':extra'    => $n['extra']
							)
						);
					}
				}
			}
			DB::query( 'INSERT INTO posts VALUES ( id, :postbody, NOW(), :userid, 0, \'\' )', array( ':postbody' => $postbody, ':userid' => $logged_user_id ) );
			$postid = DB::query( 'SELECT id FROM posts WHERE user_id = :userid ORDER BY ID DESC LIMIT 1', array( ':userid' => $logged_user_id ) )[0]['id'];
			return $postid;
		} else {
			die( 'You can post only on your profile!' );
		}
	}

	public static function like_post( $post_id, $liker_id ) {

		$has_like = DB::query( 'SELECT user_id FROM post_likes WHERE post_id = :postid AND user_id = :userid', array( ':postid' => $post_id, ':userid' => $liker_id ) );

		if ( empty( $has_like ) ) {
			DB::query( 'UPDATE posts SET likes = likes + 1 WHERE id = :postid', array( ':postid' => $post_id ) );
			DB::query( 'INSERT INTO post_likes VALUES (id, :postid, :userid)', array( ':postid' => $post_id, ':userid' => $liker_id ) );
			Notify::create_notify( "", $post_id );
		} else {
			DB::query( 'UPDATE posts SET likes = likes - 1 WHERE id = :postid', array( ':postid' => $post_id ) );
			DB::query( 'DELETE FROM post_likes WHERE post_id = :postid AND user_id = :userid', array( ':postid' => $post_id, ':userid' => $liker_id ) );
		}
	}

	public static function get_topics( $text ) {

		$text = explode( ' ', $text );
		$topics = '';
		foreach ( $text as $word ) {
			if ( substr( $word, 0, 1 ) === '#' ) {
				$topics .= substr( $word, 1 ) . ',';
			}
		}
		return $topics;
	}

	public static function link_add( $text ) {

		$text = explode( ' ', $text );
		$newstring = '';
		foreach ( $text as $word ) {
			if ( substr( $word, 0, 1 ) === '@') {
				$newstring .= "<a href='profile.php?username=" . substr( $word, 1 ) . "'>" . htmlspecialchars( $word ) . " </a>";
			} else if ( substr( $word, 0, 1 ) === '#') {
				$newstring .= "<a href='topics.php?topic=" . substr( $word, 1 ) . "'>" . htmlspecialchars( $word ) . " </a>";
			} else {
				$newstring .= htmlspecialchars( $word ).' ';
			}
		}
		return $newstring;
	}

	public static function display_post( $user_id, $username, $logged_user_id ) {
		$posts = '';
		$dbposts = DB::query( 'SELECT * FROM posts WHERE user_id = :userid ORDER BY id DESC', array( ':userid' => $user_id ) );

		$posts .= '<div class="posts-container">';
		$posts .= '<ul class="posts">';
		foreach ( $dbposts as $p ) {
			$posts .= '<li>';
			//like button
			$like_btn_name = array();
			$like_exists = DB::query( 'SELECT post_id FROM post_likes WHERE post_id = :postid AND user_id = :userid', array( ':postid' => $p['id'], ':userid' => $logged_user_id ) );
			$like_btn_name = empty( $like_exists ) ? array( 'like', 'Like' ) : array( 'unlike', 'Unike' );

			$post_form = "<form action='profile.php?username=" . $username . '&postid=' . $p['id'] . "' method = 'post'>
							<button class='btn like-btn' type='submit' name='" . $like_btn_name[0] . "' value='" . $like_btn_name[1] . "'>
								<svg class='heart-svg' viewBox='467 392 58 57' xmlns='http://www.w3.org/2000/svg'>
									<g id='Group' fill='none' fill-rule='evenodd' transform='translate(467 392)'>
									<path d='M29.144 20.773c-.063-.13-4.227-8.67-11.44-2.59C7.63 28.795 28.94 43.256 29.143 43.394c.204-.138 21.513-14.6 11.44-25.213-7.214-6.08-11.377 2.46-11.44 2.59z' id='heart' fill='#AAB8C2'/>
									</g>
								</svg>
							</button>
							<span>" . $p['likes'] . "</span>";
			if ( $user_id == $logged_user_id ) {
				$post_form  .= '<input type="submit" name="deletepost" value="x" />';
			}
			$post_form .= '</form>';
			$post_img = ( ! empty( $p['postimg'] ) ) ? '<img class="post-img" src="' . $p['postimg'] . '"/>' : '';
			$posts .= $post_img . '<p>' . self::link_add( $p['body'] ) . '</p>' . $post_form;
			$posts .= '</li>';
		}
		$posts .= '</ul>';
		$posts .= '</div>';

		return $posts;
	}
}
<?php

class Image {

	public static function upload_image( $formname, $query, $params ) {
		$image = base64_encode( file_get_contents( $_FILES[$formname]['tmp_name'] ) );
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: Bearer ff16d8c82920cb8ee1594dd65c7598f30fc46a93\n".
				"Content-Type: application/x-www-form-urlencoded",
				'content' => $image,
		) );

		$context = stream_context_create( $options );
		$image_url = 'https://api.imgur.com/3/image';

		if ( $_FILES[$formname]['size'] > 10240000 ) {
			die( 'Image too big, must be 10MB or less!' );
		}

		$response = file_get_contents( $image_url, false, $context );
		$response = json_decode( $response );
		$image_link = $response->data->link;
		$preparams = array( $formname => $image_link);
		$params = $preparams + $params;
		DB::query( $query, $params );
	}
}


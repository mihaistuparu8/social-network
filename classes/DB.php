<?php
class DB {

	private static function connect() {
		$pdo = new PDO( 'mysql:host=localhost;dbname=social_network;charset=utf8', 'root', '' );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $pdo;
	}

	public static function query( $query, $params = array() ) {
		$statement = self::connect()->prepare( $query );
		$statement->execute( $params );
		if ( 'SELECT' === explode( ' ', $query )[0] ) {
			$data = $statement->fetchAll();
			return $data;
		}
	}
}


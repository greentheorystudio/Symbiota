<?php
class ImageBatchConnectionFactory {
	public static $SERVER = array(
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => '',
        'port' => '3306',
        'charset' => ''		//utf8, latin1, latin2, etc
	);

	public static function getCon(): ?mysqli
    {
		// Figure out which connections are open, automatically opening any connections
		// which are failed or not yet opened but can be (re)established.
        $connection = new mysqli(self::$SERVER['host'], self::$SERVER['username'], self::$SERVER['password'], self::$SERVER['database'], self::$SERVER['port']);
        if(mysqli_connect_errno()){
            throw new RuntimeException('Could not connect to any databases! Please try again later.');
        }
        if(isset(self::$SERVER['charset']) && self::$SERVER['charset'] && !$connection->set_charset(self::$SERVER['charset'])) {
            throw new RuntimeException('Error loading character set '.self::$SERVER['charset'].'.');
        }
        return $connection;
    }
}

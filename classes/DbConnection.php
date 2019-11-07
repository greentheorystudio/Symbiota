<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class DbConnection {
    public function getConnection() {
        global $DB_SERVER;
        $connection = new mysqli($DB_SERVER['host'], $DB_SERVER['username'], $DB_SERVER['password'], $DB_SERVER['database']);

        return $connection;
    }
}

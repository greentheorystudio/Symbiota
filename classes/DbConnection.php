<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class DbConnection {
    public function getConnection() {
        global $DB_SERVER;
        $connection = new mysqli($DB_SERVER['host'], $DB_SERVER['username'], $DB_SERVER['password'], $DB_SERVER['database'], $DB_SERVER['port']);

        return $connection;
    }

    public function getVersion() {
        global $DB_SERVER;

        return $DB_SERVER['version'];
    }
}

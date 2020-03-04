<?php
include_once(__DIR__ . '/../config/dbconnection.php');

class DbConnection {
    public function getConnection(): mysqli
    {
        global $DB_SERVER;
        return new mysqli($DB_SERVER['host'], $DB_SERVER['username'], $DB_SERVER['password'], $DB_SERVER['database'], $DB_SERVER['port']);
    }

    public function getVersion() {
        global $DB_SERVER;

        return $DB_SERVER['version'];
    }
}

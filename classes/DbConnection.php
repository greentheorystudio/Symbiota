<?php
include_once(__DIR__ . '/../config/dbconnection.php');

class DbConnection {
    public function getConnection(): ?\mysqli
    {
        $con = null;
        if($GLOBALS['DB_SERVER']['host'] && $GLOBALS['DB_SERVER']['username'] && $GLOBALS['DB_SERVER']['password'] && $GLOBALS['DB_SERVER']['database'] && $GLOBALS['DB_SERVER']['port']){
            $con = new mysqli($GLOBALS['DB_SERVER']['host'], $GLOBALS['DB_SERVER']['username'], $GLOBALS['DB_SERVER']['password'], $GLOBALS['DB_SERVER']['database'], $GLOBALS['DB_SERVER']['port']);
        }
        return $con;
    }

    public function getVersion()
    {
        return $GLOBALS['DB_SERVER']['version'];
    }
}

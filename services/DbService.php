<?php
include_once(__DIR__ . '/../config/dbconnection.php');

class DbService {
    public function getConnection(): ?\mysqli
    {
        $con = null;
        if($GLOBALS['DB_SERVER']['host'] && $GLOBALS['DB_SERVER']['username'] && $GLOBALS['DB_SERVER']['password'] && $GLOBALS['DB_SERVER']['database'] && $GLOBALS['DB_SERVER']['port']){
            $con = new mysqli($GLOBALS['DB_SERVER']['host'], $GLOBALS['DB_SERVER']['username'], $GLOBALS['DB_SERVER']['password'], $GLOBALS['DB_SERVER']['database'], $GLOBALS['DB_SERVER']['port']);
        }
        return $con;
    }

    public function getSqlFieldNameArrFromFieldData($fields, $alias = null): array
    {
        $fieldNameArr = array();
        foreach($fields as $field => $fieldArr){
            if($field !== 'password'){
                if($field === 'state' || $field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                    $fieldNameArr[] = ($alias ? ($alias . '.') : '') . '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = ($alias ? ($alias . '.') : '') . $field;
                }
            }
        }
        return $fieldNameArr;
    }

    public function getVersion()
    {
        return $GLOBALS['DB_SERVER']['version'];
    }
}

<?php
include_once(__DIR__ . '/DbConnection.php');

class Sanitizer {

    public static function cleanInStr($str): string
    {
        $connection = new DbConnection();
        $conn = $connection->getConnection();
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $conn->real_escape_string($newStr);
        return $newStr;
    }

    public static function cleanInArray($arr): array
    {
        $newArray = array();
        foreach($arr as $key => $value){
            $newArray[self::cleanInStr($key)] = self::cleanInStr($value);
        }
        return $newArray;
    }

    public static function cleanOutStr($str): string
    {
        return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
    }
}

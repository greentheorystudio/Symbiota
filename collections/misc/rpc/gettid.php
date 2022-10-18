<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$sciName = array_key_exists('sciname',$_REQUEST)?$con->real_escape_string($_REQUEST['sciname']):'';

$responseStr = '';
if($sciName){
    $sql = 'SELECT t.tid FROM taxa t '.
        "WHERE (t.sciname = '".$sciName."')";
    $result = $con->query($sql);
    if($row = $result->fetch_object()){
        $responseStr = $row->tid;
    }
    $result->close();
    $con->close();
}

echo $responseStr;

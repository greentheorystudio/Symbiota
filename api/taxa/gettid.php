<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$responseStr = '';
$connection = new DbConnection();
$con = $connection->getConnection();

$sciName = $con->real_escape_string($_REQUEST['sciname']);
$rankid = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:0;
$author = array_key_exists('author',$_POST)?$con->real_escape_string($_POST['author']):'';

$sql = 'SELECT t.tid FROM taxa AS t '.
	"WHERE t.sciname = '".$sciName."' ";
if($rankid){
    $sql .= 'AND t.rankid = '.$rankid;
}
if($author){
    $sql .= 'AND t.author = "'.$author.'" ';
}
$result = $con->query($sql);
if($row = $result->fetch_object()){
	$responseStr = $row->tid;
}
$result->close();
$con->close();

echo $responseStr;

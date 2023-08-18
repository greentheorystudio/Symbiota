<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=UTF-8' );

$responseStr = '';
$connection = new DbConnection();
$con = $connection->getConnection();

$sciName = $con->real_escape_string($_REQUEST['sciname']);
$rankid = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:0;
$kingdomid = array_key_exists('kingdomid',$_POST)?(int)$_POST['kingdomid']:0;
$author = array_key_exists('author',$_POST)?$con->real_escape_string($_POST['author']):'';

$sql = 'SELECT tid FROM taxa WHERE sciname = "'.$sciName.'" ';
if($rankid){
    $sql .= 'AND rankid = '.$rankid.' ';
}
if($author){
    $sql .= 'AND author = "'.$author.'" ';
}
if($kingdomid){
    $sql .= 'AND kingdomId = '.$kingdomid.' ';
}
$result = $con->query($sql);
if($row = $result->fetch_object()){
	$responseStr = $row->tid;
}
$result->close();
$con->close();

echo $responseStr;

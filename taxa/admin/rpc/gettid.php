<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$sciName = $con->real_escape_string($_REQUEST['sciname']); 
$rankid = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:0;
$author = array_key_exists('author',$_POST)?$con->real_escape_string($_POST['author']):'';

$retArr = array();
$sql = 'SELECT t.tid FROM taxa t ';
$sql .= 'WHERE (t.sciname = "'.$sciName.'" OR CONCAT(t.sciname," ",t.author) = "'.$sciName.'") ';
if($rankid){
	$sql .= ' AND t.rankid = '.$rankid;
}
if($author){
	$sql .= ' AND t.author = "'.$author.'" ';
}
$result = $con->query($sql);
while($row = $result->fetch_object()){
	$retArr[] = $row->tid;
}
$result->free();
$con->close();

if($retArr) {
    echo implode(',', $retArr);
}
else {
    echo 0;
}

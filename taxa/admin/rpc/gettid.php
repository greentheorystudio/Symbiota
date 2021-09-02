<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$sciName = $con->real_escape_string($_REQUEST['sciname']); 
$taxAuthId = array_key_exists('taxauthid',$_POST)?(int)$_POST['taxauthid']:0;
$rankid = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:0;
$author = array_key_exists('author',$_POST)?$con->real_escape_string($_POST['author']):'';

$retArr = array();
$sql = 'SELECT t.tid FROM taxa t ';
if($taxAuthId){
	$sql .= 'INNER JOIN taxstatus ts ON t.tid = ts.tid ';
}
$sql .= 'WHERE (t.sciname = "'.$sciName.'" OR CONCAT(t.sciname," ",t.author) = "'.$sciName.'") ';
if($taxAuthId){
	$sql .= 'AND ts.taxauthid = '.$taxAuthId;
}
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

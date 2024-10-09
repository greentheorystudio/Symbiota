<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
header('Content-Type: text/html; charset=UTF-8' );

$clid = (int)$_REQUEST['cl'];

$returnArr = array();
if(is_numeric($clid)){
	$connection = new DbService();
	$conn = $connection->getConnection();
	$clid = $conn->real_escape_string($clid);
	$queryString = $conn->real_escape_string($_REQUEST['term']);
		
	$sql = 'SELECT t.sciname '. 
		'FROM taxa AS t INNER JOIN fmchklsttaxalink AS cl ON t.tid = cl.tid '.
		'WHERE t.sciname LIKE "'.$queryString.'%" AND cl.clid = '.$clid;
	//echo $sql;
	$result = $conn->query($sql);
	while ($r = $result->fetch_object()) {
		$returnArr[] = $r->sciname;
	}
	$result->free();
	
	$sql = 'SELECT DISTINCT t.family '.
		'FROM fmchklsttaxalink AS cl INNER JOIN taxa AS t ON cl.tid = t.tid '.
		'WHERE t.family LIKE "'.$queryString.'%" AND cl.clid = '.$clid.' ';
	//echo $sql;
	$result = $conn->query($sql);
	while ($r = $result->fetch_object()) {
		$returnArr[] = $r->family;
	}
	$result->free();
	
	$conn->close();
}
sort($returnArr);
echo json_encode($returnArr);

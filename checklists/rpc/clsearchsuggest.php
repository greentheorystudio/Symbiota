<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$clid = (int)$_REQUEST['cl'];

$returnArr = array();
if(is_numeric($clid)){
	$connection = new DbConnection();
	$conn = $connection->getConnection();
	$clid = $conn->real_escape_string($clid);
	$queryString = $conn->real_escape_string($_REQUEST['term']);
		
	$sql = 'SELECT t.sciname '. 
		'FROM taxa t INNER JOIN fmchklsttaxalink cl ON t.tid = cl.tid '.
		'WHERE t.sciname LIKE "'.$queryString.'%" AND cl.clid = '.$clid;
	//echo $sql;
	$result = $conn->query($sql);
	while ($r = $result->fetch_object()) {
		$returnArr[] = $r->sciname;
	}
	$result->free();
	
	$sql = 'SELECT DISTINCT ts.family '. 
		'FROM fmchklsttaxalink cl INNER JOIN taxstatus ts ON cl.tid = ts.tid '.
		'WHERE ts.family LIKE "'.$queryString.'%" AND cl.clid = '.$clid.' AND ts.taxauthid = 1 ';
	//echo $sql;
	$result = $conn->query($sql);
	while ($r = $result->fetch_object()) {
		$returnArr[] = $r->family;
	}
	$result->free();
	
	$conn->close();
}
sort($returnArr);
echo json_encode($returnArr, JSON_THROW_ON_ERROR);

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbConnectionService.php');
header('Content-Type: text/html; charset=UTF-8' );

$clid = (int)$_REQUEST['cl'];

$returnArr = array();
if(is_numeric($clid)){
	$connection = new DbConnectionService();
	$conn = $connection->getConnection();
	$clid = $conn->real_escape_string($clid);
	$queryString = $conn->real_escape_string($_REQUEST['term']);
		
	$sql = 'SELECT t.sciname '. 
		'FROM taxa t INNER JOIN fmchklsttaxalink cl ON t.tid = cl.tid '.
		'WHERE sciname LIKE "'.$queryString.'%" AND cl.clid = '.$clid.' ORDER BY sciname';
	//echo $sql;
	$result = $conn->query($sql);
	while ($r = $result->fetch_object()) {
		$returnArr[] = $r->sciname;
	}
	$conn->close();
}
echo json_encode($returnArr);

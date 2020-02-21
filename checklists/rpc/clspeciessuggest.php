<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$clid = $_REQUEST['cl'];

$returnArr = array();
if(is_numeric($clid)){
	$connection = new DbConnection();
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

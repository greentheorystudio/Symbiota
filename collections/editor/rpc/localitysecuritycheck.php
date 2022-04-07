<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();

$retStr = 0;
$tid = (int)$_REQUEST['tid'];
$state = trim($con->real_escape_string($_REQUEST['state']));

if(is_numeric($tid) && $state){
	$sql = 'SELECT c.clid '.
		'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid '.
		'INNER JOIN taxstatus AS ts1 ON cl.tid = ts1.tid '.
		'INNER JOIN taxstatus AS ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
		'WHERE c.type = "rarespp" '.
		'AND (ts2.tid = '.$tid.') AND (c.locality = "'.$state.'")';
	//echo $sql;
	$rs = $con->query($sql);
	if($rs->num_rows){
		$retStr = 1;
	}
	$rs->free();
}
$con->close();

echo $retStr;

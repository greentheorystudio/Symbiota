<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: application/json; charset=UTF-8' );

$connection = new DbConnection();
$con = $connection->getConnection();

$retStr = 0;
$tid = (int)$_REQUEST['tid'];
$state = trim($con->real_escape_string($_REQUEST['state']));

if(is_numeric($tid) && $state){
	$sql = 'SELECT c.clid '.
		'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid '.
		'INNER JOIN taxa AS t ON cl.tid = t.tid '.
		'WHERE c.type = "rarespp" '.
		'AND t.tidaccepted = '.$tid.' AND c.locality = "'.$state.'" ';
	//echo $sql;
	$rs = $con->query($sql);
	if($rs->num_rows){
		$retStr = 1;
	}
	$rs->free();
}
$con->close();

echo $retStr;

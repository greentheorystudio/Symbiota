<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$connection = new DbConnection();
$con = $connection->getConnection();
$queryString = $con->real_escape_string($_REQUEST['term']);
$retStr = '';
if($queryString){
	$sql = 'SELECT tid '.
		'FROM taxa '.
		'WHERE sciname = "'.$queryString.'" ';
	//echo $sql;
	$result = $con->query($sql);
	if($row = $result->fetch_object()) {
		$retStr = $row->tid;
	}
	$result->free();
}
$con->close();
echo $retStr;

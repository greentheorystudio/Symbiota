<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
header('Content-Type: text/html; charset=UTF-8' );

$connection = new DbService();
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

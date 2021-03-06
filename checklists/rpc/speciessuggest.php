<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$retArr = array();
$connection = new DbConnection();
$con = $connection->getConnection();

$queryString = $con->real_escape_string($_REQUEST['term']);
$taxLevel = (isset($_REQUEST['level'])?$con->real_escape_string($_REQUEST['level']):'low');

$sql = 'SELECT tid, sciname FROM taxa WHERE sciname LIKE "'.$queryString.'%" ';
if($taxLevel === 'low'){
	$sql .= 'AND rankid > 179';
}
else{
	$sql .= 'AND rankid < 180';
}
//echo $sql;
$result = $con->query($sql);
while ($r = $result->fetch_object()) {
	$retArr[$r->tid]['id'] = $r->tid;
	$retArr[$r->tid]['value'] = $r->sciname;
}
$result->free();
$con->close();

echo json_encode($retArr);

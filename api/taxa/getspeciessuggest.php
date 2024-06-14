<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbConnectionService.php');

$connection = new DbConnectionService();
header('Content-Type: application/json; charset=UTF-8' );
$con = $connection->getConnection();
$retArr = array();
$term = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT DISTINCT tid, sciname ' .
	'FROM taxa ' .
	"WHERE sciname LIKE '".$term."%' ";
//echo $sql;
$rs = $con->query($sql);
while ($r = $rs->fetch_object()){
	$retArr[] = array('id' => $r->tid, 'value' => $r->sciname);
}
$rs->free();
$con->close();

if($retArr){
    echo json_encode($retArr);
}
else{
	echo 'null';
}

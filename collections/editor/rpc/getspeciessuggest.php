<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);
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
	if($GLOBALS['CHARSET'] === 'UTF-8'){
		echo json_encode($retArr, JSON_THROW_ON_ERROR);
	}
	else{
		$str = '[';
		foreach($retArr as $k => $vArr){
			$str .= '{"id":"'.$vArr['id'].'","value":"'.str_replace('"',"''",$vArr['value']).'"},';
		}
		echo trim($str,',').']';
	}
}
else{
	echo 'null';
}

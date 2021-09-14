<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$term = trim($con->real_escape_string($_REQUEST['term']));
if($term){
	$sql = 'SELECT DISTINCT t.tid, t.author, ts.family, t.securitystatus '.
		'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
		'WHERE t.sciname = "'.$term.'" AND ts.taxauthid = 1 ';
	//echo $sql;
	$rs = $con->query($sql);
	while ($r = $rs->fetch_object()) {
		$retArr['tid'] = $r->tid;
		$retArr['family'] = $r->family;
		$retArr['author'] = $r->author;
		$retArr['status'] = $r->securitystatus;
	}
	$rs->free();
	$con->close();
}

if($retArr){
	if($GLOBALS['CHARSET'] === 'UTF-8'){
		echo json_encode($retArr);
	}
	else{
		echo '{"tid":"'.$retArr['tid'].'","family":"'.$retArr['family'].'","author":"'.str_replace('"',"''",$retArr['author']).'","status":"'.$retArr['status'].'"}';
	}
}
else{
	echo 'null';
}

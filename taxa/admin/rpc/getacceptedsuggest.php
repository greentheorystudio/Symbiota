<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$connection = new DbConnection();
$con = $connection->getConnection();
$q = $con->real_escape_string($_REQUEST['term']);
$taxAuthId = array_key_exists('taid',$_REQUEST)?$con->real_escape_string($_REQUEST['taid']):'1'; 

$retArr = array();
$sql = 'SELECT t.tid, t.sciname, t.author FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
	'WHERE (ts.taxauthid = '.$taxAuthId.') AND (ts.tid = ts.tidaccepted) AND (t.sciname LIKE "'.$q.'%") ORDER BY t.sciname LIMIT 10';
$result = $con->query($sql);
while($row = $result->fetch_object()){
	if($CHARSET === 'UTF-8') {
		$retArr[] = $row->sciname . ' ' . $row->author;
	}
	else {
		$retArr[] = utf8_encode($row->sciname . ' ' . $row->author);
	}
}
$result->free();
if(!($con === false)) {
	$con->close();
}

echo json_encode($retArr);

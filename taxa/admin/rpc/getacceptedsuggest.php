<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$q = $con->real_escape_string($_REQUEST['term']);

$retArr = array();
$sql = 'SELECT t.tid, t.sciname, t.author FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
	'WHERE (ts.tid = ts.tidaccepted) AND (t.sciname LIKE "'.$q.'%") ORDER BY t.sciname LIMIT 10';
$result = $con->query($sql);
while($row = $result->fetch_object()){
	if($GLOBALS['CHARSET'] === 'UTF-8') {
		$retArr[] = $row->sciname . ' ' . $row->author;
	}
	else {
		$retArr[] = utf8_encode($row->sciname . ' ' . $row->author);
	}
}
$result->free();
$con->close();

echo json_encode($retArr);

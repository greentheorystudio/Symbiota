<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();

$retArrRow = array();
$retArr = array();
$queryString = $con->real_escape_string($_REQUEST['term']);
if($queryString) {
	$sql = 'SELECT DISTINCT SciName '.
		''.
		"WHERE RankId >= 210 AND SciName LIKE '".$queryString."%'";
	$sql .= 'LIMIT 10';
	//echo $sql;
	if($result = $con->query($sql)){
		while ($row = $result->fetch_object()) {
			$retArrRow['id'] = $row->SciName;
			$retArrRow['label'] = htmlentities($row->SciName);
			$retArrRow['value'] = $row->SciName;
			$retArr[] = $retArrRow;
		}
	}
}

$con->close();
echo json_encode($retArr);


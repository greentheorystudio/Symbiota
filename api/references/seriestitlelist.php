<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbConnectionService.php');

$connection = new DbConnectionService();
$con = $connection->getConnection();
$returnArr = array();
$retArrRow = array();
$queryString = $con->real_escape_string($_REQUEST['term']);
if($queryString) {
	$sql = 'SELECT o.refid, o.title, o.edition ' .
        'FROM referenceobject AS o  ' .
		"WHERE o.title LIKE '%".$queryString."%' AND o.ReferenceTypeId = 27 ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$titleLine = '';
		$titleLine .= $row->title;
		if($row->edition){
			$titleLine .= ' '.$row->edition.' Ed.';
		}
		$retArrRow['label'] = htmlentities($titleLine);
		$retArrRow['value'] = $row->refid;
		$returnArr[] = $retArrRow;
	}
}
$con->close();
echo json_encode($returnArr);

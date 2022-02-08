<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$returnArr = array();
$retArrRow = array();
$queryString = $con->real_escape_string($_REQUEST['term']);
if($queryString) {
	$sql = "SELECT DISTINCT refauthorid, CONCAT_WS(' ',firstname,middlename,lastname) AS authorName ".
        'FROM referenceauthors ' .
		"WHERE lastname LIKE '".$queryString."%' ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$retArrRow['label'] = htmlentities($row->authorName);
		$retArrRow['value'] = $row->refauthorid;
		$returnArr[] = $retArrRow;
	 }
}
$con->close();
echo json_encode($returnArr);

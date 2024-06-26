<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');

$connection = new DbService();
$con = $connection->getConnection();
$returnArr = array();
$retArrRow = array();
$queryString = $con->real_escape_string($_REQUEST['term']);
if($queryString) {
	$sql = 'SELECT o.refid, o.secondarytitle, o.volume, o.title, o.edition ' .
        'FROM referenceobject AS o LEFT JOIN referencetype AS t ON o.ReferenceTypeId = t.ReferenceTypeId ' .
		"WHERE (o.title LIKE '%".$queryString."%' OR o.secondarytitle LIKE '%".$queryString."%') AND o.ReferenceTypeId <> 27 AND t.IsParent = 1 ";
	$sql .= 'LIMIT 10';
	$result = $con->query($sql);
	while ($row = $result->fetch_object()) {
		$titleLine = '';
		if($row->secondarytitle){
			$titleLine .= $row->secondarytitle.' ';
			if($row->volume){
				$titleLine .= 'Vol. '.$row->volume.' ';
			}
		}
		if($row->title !== $row->secondarytitle){
			$titleLine .= $row->title;
		}
		if(!$row->secondarytitle && $row->volume){
			$titleLine .= ' Vol. '.$row->volume.' ';
		}
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

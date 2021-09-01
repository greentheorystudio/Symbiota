<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$occid = (int)$_POST['occid'];
if($occid){
	$sql = 'SELECT recordedby, recordnumber, eventdate ' .
        'FROM omoccurrences ' .
        'WHERE occid = ' .$occid;
	//echo $sql;
	$rs = $con->query($sql);
	if($row = $rs->fetch_object()){
		$retArr['recordedby'] = $row->recordedby;
		$retArr['recordnumber'] = $row->recordnumber;
		$retArr['eventdate'] = $row->eventdate;
	}
	$rs->free();
	$con->close();
}
echo json_encode($retArr, JSON_THROW_ON_ERROR);

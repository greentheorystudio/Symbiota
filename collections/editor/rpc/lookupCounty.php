<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$queryString = $con->real_escape_string($_REQUEST['term']);
$stateStr = array_key_exists('state',$_REQUEST)?$con->real_escape_string($_REQUEST['state']):'';

$sql = 'SELECT DISTINCT c.countyname FROM lkupcounty c ';
$sqlWhere = 'WHERE c.countyname LIKE "'.$queryString.'%" ';
if($stateStr){
	$sql .= 'INNER JOIN lkupstateprovince s ON c.stateid = s.stateid ';
	$sqlWhere .= 'AND s.statename = "'.$stateStr.'" ';
}
$sql .= $sqlWhere.'ORDER BY c.countyname';
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
	$countyStr = $row->countyname;
	if(($CHARSET === 'ISO-8859-1') && mb_detect_encoding($countyStr, 'UTF-8,ISO-8859-1', true) === 'ISO-8859-1') {
		$countyStr = utf8_encode($countyStr);
	}
	$retArr[] = $countyStr;
}
$result->free();
$con->close();
if($retArr){
	echo '["'.implode('","',($retArr)).'"]';
}
else{
	echo '';
}


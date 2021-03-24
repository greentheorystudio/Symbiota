<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$queryString = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT DISTINCT countryname FROM lkupcountry '.
	'WHERE countryname LIKE "'.$queryString.'%" ';
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
	$countryStr = $row->countryname;
	if(($GLOBALS['CHARSET'] === 'ISO-8859-1') && mb_detect_encoding($countryStr, 'UTF-8,ISO-8859-1', true) === 'ISO-8859-1') {
        $countryStr = utf8_encode($countryStr);
    }
	$retArr[] = $countryStr;
}
$result->free();
$con->close();
sort($retArr);
if($retArr){
	echo '["'.implode('","',($retArr)).'"]';
}
else{
	echo '';
}

<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$connection = new DbConnection();
$con = $connection->getConnection();
$sqlFrag = $con->real_escape_string($_REQUEST['sql']);
$clid = $con->real_escape_string($_REQUEST['clid']); 

$responseStr = '-1';
if($sqlFrag && $clid && ($IS_ADMIN || (array_key_exists('ClAdmin',$USER_RIGHTS) && in_array($clid, $USER_RIGHTS['ClAdmin'], true)))){
	$responseStr = '0';
	$sql = 'SELECT * FROM omoccurrences o WHERE '.$sqlFrag.' LIMIT 1';
	$result = $con->query($sql);
	if($result){
		$responseStr = '1';
	}
	if($result) {
		$result->close();
	}
}
if(!($con === false)) {
	$con->close();
}
echo $responseStr;

<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$retColArr = array();
$retArr = array();
$term = $_REQUEST['term'];

$colData = file_get_contents('http://www.catalogueoflife.org/col/webservice?name='.$term.'*&format=php&response=terse');
$colData = unserialize($colData, '');
if(array_key_exists('results',$colData)){
	$retColArr = $colData['results'];
}

if($retColArr){
	foreach($retColArr as $k => $vArr){
		$retArr[$vArr['name']]['id'] = $vArr['name'];
		$retArr[$vArr['name']]['value'] = $vArr['name'];
	}
	ksort($retArr);
	echo json_encode($retArr);
}
else{
	echo 'null';
}

<?php
include_once(__DIR__ . '/../../classes/OccurrenceAccessStats.php');

$occidStr = array_key_exists('occidstr',$_REQUEST)?$_REQUEST['occidstr']:'';
$sql = array_key_exists('sql',$_REQUEST)?$_REQUEST['sql']:'';
$accessType = $_REQUEST['accesstype'];

$statManager = new OccurrenceAccessStats();

if($occidStr){
	$statManager->recordAccessEventByArr($occidStr,$accessType);
}
elseif($sql){
	$statManager->batchRecordEventsBySql($sql,$accessType);
}

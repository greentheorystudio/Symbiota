<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDuplicate.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$recordedBy = $_REQUEST['recordedby'];
$eventDate = $_REQUEST['eventdate'];
$locality = $_REQUEST['locality'];

$dupManager = new OccurrenceDuplicate();
$retArr = $dupManager->getDupeLocality($recordedBy, $eventDate, $locality);

if($retArr){
	if($GLOBALS['CHARSET'] === 'UTF-8'){
		echo json_encode($retArr);
	}
	else{
		$str = '[';
		foreach($retArr as $k => $vArr){
			$str .= '{"id":"'.$vArr['id'].'","value":"'.str_replace('"',"''",$vArr['value']).'"},';
		}
		echo trim($str,',').']';
	}
}
else{
	echo 'null';
}

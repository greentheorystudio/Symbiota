<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDuplicate.php');
header('Content-Type: application/json; charset=UTF-8' );

$recordedBy = $_REQUEST['recordedby'];
$eventDate = $_REQUEST['eventdate'];
$locality = $_REQUEST['locality'];

$dupManager = new OccurrenceDuplicate();
$retArr = $dupManager->getDupeLocality($recordedBy, $eventDate, $locality);

if($retArr){
    echo json_encode($retArr);
}
else{
	echo 'null';
}

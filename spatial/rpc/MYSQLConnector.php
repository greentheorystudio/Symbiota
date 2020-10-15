<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');
header('Content-Type: application/json; charset=' .$CHARSET);
ini_set('max_execution_time', 300);

$stArrJson = $_REQUEST['starr'];
$occIndex = $_REQUEST['start'];
$recordCnt = $_REQUEST['rows'];
$type = $_REQUEST['type'];

$spatialManager = new SpatialModuleManager();
$occManager = new OccurrenceManager();

$retArr = array();

$stArr = json_decode($stArrJson, true);

$occManager->setSearchTermsArr($stArr);
$spatialManager->setSearchTermsArr($stArr);
$mapWhere = $occManager->getSqlWhere();
$spatialManager->setSqlWhere($mapWhere);
if($type === 'reccnt'){
    $spatialManager->setRecordCnt();
    echo $spatialManager->getRecordCnt();
}
if($type === 'geoquery'){
    echo $spatialManager->getOccPointMapGeoJson($occIndex,$recordCnt);
}

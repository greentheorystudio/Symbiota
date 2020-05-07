<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
header('Content-Type: application/json; charset=' .$CHARSET);
ini_set('max_execution_time', 300);

$stArrJson = $_REQUEST['starr'];
$occIndex = $_REQUEST['start'];
$recordCnt = $_REQUEST['rows'];
$type = $_REQUEST['type'];

$spatialManager = new SpatialModuleManager();

$retArr = array();

$stArr = json_decode($stArrJson, true);

if($stArr){
    $spatialManager->setSearchTermsArr($stArr);
    $mapWhere = $spatialManager->getSqlWhere();
    if($type === 'reccnt'){
        $spatialManager->setRecordCnt($mapWhere);
        echo $spatialManager->getRecordCnt();
    }
    if($type === 'geoquery'){
        echo $spatialManager->getOccPointMapGeoJson($mapWhere,$occIndex,1000);
    }
}

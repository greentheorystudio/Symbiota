<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpatialModuleManager.php');
header('Content-Type: application/json; charset=' .$CHARSET);

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

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && Sanitizer::validateInternalRequest()){
    if($action === 'getQueryRecCnt' && array_key_exists('starr',$_POST)){
        $spatialManager = new SpatialModuleManager();
        $stArrJson = $_POST['starr'];
        $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
        if($stArr){
            $searchManager->setSearchTermsArr($stArr);
            $mapWhere = $searchManager->getSqlWhere();
            $searchManager->setRecordCnt($mapWhere);
            echo $searchManager->getRecordCnt();
        }
    }
    elseif($action === 'getQueryResultsGeoJson' && array_key_exists('starr',$_POST) && array_key_exists('start',$_POST) && array_key_exists('rows',$_POST)){
        $spatialManager = new SpatialModuleManager();
        $occManager = new OccurrenceManager();
        $stArrJson = $_POST['starr'];
        $startIndex = $_POST['start'];
        $recordCnt = $_POST['rows'];
        $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
        if($stArr){
            $occManager->setSearchTermsArr($stArr);
            $spatialManager->setSearchTermsArr($stArr);
            $mapWhere = $occManager->getSqlWhere();
            $spatialManager->setSqlWhere($mapWhere);
            echo $spatialManager->getOccPointMapGeoJson($startIndex, $recordCnt);
        }
    }
    elseif($action === 'getQueryResultsRecordData' && array_key_exists('starr',$_POST) && array_key_exists('cntperpage',$_POST) && array_key_exists('index',$_POST)){
        $spatialManager = new SpatialModuleManager();
        $stArrJson = $_POST['starr'];
        $cntPerPage = array_key_exists('cntperpage',$_POST) ? $_POST['cntperpage'] : 100;
        $pageNumber = array_key_exists('index',$_POST) ? $_POST['index'] : 1;
        $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
        if($stArr){
            $searchManager->setSearchTermsArr($stArr);
            $mapWhere = $searchManager->getSqlWhere();
            echo json_encode($spatialManager->getMapRecordPageArr($pageNumber,$cntPerPage,$mapWhere));
        }
    }
}

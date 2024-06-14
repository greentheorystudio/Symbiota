<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && SanitizerService::validateInternalRequest()){
    $spatialManager = new SpatialModuleManager();
    $occManager = new OccurrenceManager();
    $stArr = array_key_exists('starr',$_POST) ? json_decode(str_replace('%squot;', "'", $_POST['starr']), true) : null;
    if($action === 'getQueryRecCnt' && array_key_exists('starr',$_POST)){
        if($stArr){
            $occManager->setSearchTermsArr($stArr);
            $spatialManager->setSearchTermsArr($stArr);
            $mapWhere = $occManager->getSqlWhere();
            $spatialManager->setSqlWhere($mapWhere);
            $spatialManager->setRecordCnt();
            echo $spatialManager->getRecordCnt();
        }
    }
    elseif($action === 'getQueryResultsGeoJson' && array_key_exists('starr',$_POST) && array_key_exists('start',$_POST) && array_key_exists('rows',$_POST)){
        $occIndex = (int)$_REQUEST['start'];
        $recordCnt = (int)$_REQUEST['rows'];
        if($stArr){
            $occManager->setSearchTermsArr($stArr);
            $spatialManager->setSearchTermsArr($stArr);
            $mapWhere = $occManager->getSqlWhere();
            $spatialManager->setSqlWhere($mapWhere);
            echo $spatialManager->getOccPointMapGeoJson($occIndex, $recordCnt);
        }
    }
    elseif($action === 'getQueryResultsRecordData' && array_key_exists('starr',$_POST) && array_key_exists('cntperpage',$_POST) && array_key_exists('index',$_POST)){
        $spatialManager = new SpatialModuleManager();
        $cntPerPage = array_key_exists('cntperpage',$_POST) ? $_POST['cntperpage'] : 100;
        $pageNumber = array_key_exists('index',$_POST) ? $_POST['index'] : 1;
        if($stArr){
            $searchManager->setSearchTermsArr($stArr);
            $mapWhere = $searchManager->getSqlWhere();
            echo json_encode($spatialManager->getMapRecordPageArr($pageNumber,$cntPerPage,$mapWhere));
        }
    }
}

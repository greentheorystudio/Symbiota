<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Collections.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $collections = new Collections();
    if($action === 'getCollectionArr'){
        echo json_encode($collections->getCollectionArr());
    }
    elseif($action === 'createCollectionRecord' && $isEditor && array_key_exists('collection', $_POST)){
        echo $collections->createCollectionRecord(json_decode($_POST['collection'], true));
    }
    elseif($action === 'deleteCollectionRecord' && $collid && $isEditor){
        echo $collections->deleteCollectionRecord($collid);
    }
    elseif($action === 'updateCollectionRecord' && $collid && $isEditor && array_key_exists('collectionData', $_POST)){
        echo $collections->updateCollectionRecord($collid, json_decode($_POST['collectionData'], true));
    }
    elseif($action === 'getCollectionInfoArr' && $collid){
        echo json_encode($collections->getCollectionInfoArr($collid));
    }
    elseif($action === 'updateCollectionStatistics' && $isEditor && array_key_exists('collidStr', $_POST)){
        echo $collections->updateCollectionStatistics($_POST['collidStr']);
    }
    elseif($action === 'cleanSOLRIndex' && $isEditor && array_key_exists('collidStr', $_POST)){
        echo $collections->cleanSOLRIndex($_POST['collidStr']);
    }
    elseif($action === 'getSpeciesListDownloadData' && $collid){
        echo json_encode($collections->getSpeciesListDownloadData($collid));
    }
    elseif($action === 'getGeographicDistributionData' && $collid){
        $country = array_key_exists('country',$_POST) ? $_POST['country'] : null;
        $state = array_key_exists('state',$_POST) ? $_POST['state'] : null;
        echo json_encode($collections->getGeographicDistributionData($collid, $country, $state));
    }
    elseif($action === 'getTaxonomicDistributionData' && $collid){
        echo json_encode($collections->getTaxonomicDistributionData($collid));
    }
    elseif($action === 'getCollectionListByUserRights'){
        echo json_encode($collections->getCollectionListByUserRights());
    }
}

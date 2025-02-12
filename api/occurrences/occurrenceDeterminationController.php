<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/OccurrenceDeterminations.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$occid = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$detid = array_key_exists('detid', $_REQUEST) ? (int)$_REQUEST['detid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}

if($action && SanitizerService::validateInternalRequest()){
    $occurrenceDeterminations = new OccurrenceDeterminations();
    if($action === 'getOccurrenceDeterminationArr' && $occid){
        echo json_encode($occurrenceDeterminations->getOccurrenceDeterminationData($occid));
    }
    elseif($action === 'createOccurrenceDeterminationRecord' && $isEditor && array_key_exists('determination',$_POST)){
        echo $occurrenceDeterminations->createOccurrenceDeterminationRecord(json_decode($_POST['determination'], true));
    }
    elseif($action === 'updateDeterminationRecord' && $detid && $isEditor){
        echo $occurrenceDeterminations->updateDeterminationRecord($detid, json_decode($_POST['determinationData'], true));
    }
    elseif($action === 'deleteDeterminationRecord' && $detid && $isEditor){
        echo $occurrenceDeterminations->deleteDeterminationRecord($detid);
    }
    elseif($action === 'makeDeterminationCurrent' && $detid && $isEditor){
        echo $occurrenceDeterminations->makeDeterminationCurrent($detid);
    }
    elseif($action === 'batchPopulateOccurrenceDeterminationGUIDs' && $isEditor && $collid){
        echo $occurrenceDeterminations->batchCreateOccurrenceDeterminationRecordGUIDs($collid);
    }
    elseif($action === 'updateDetThesaurusLinkages' && $isEditor && $collid && array_key_exists('kingdomid', $_POST)){
        $kingdomid = (int)$_POST['kingdomid'];
        echo $occurrenceDeterminations->updateDetTaxonomicThesaurusLinkages($collid, $kingdomid);
    }
}

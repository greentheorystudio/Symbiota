<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDataManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');

$occid = array_key_exists('occid',$_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

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

if($action && Sanitizer::validateInternalRequest()){
    $occManager = new OccurrenceDataManager();
    if($action === 'getOccurrenceDataLock' && $isEditor && $occid){
        echo json_encode($occManager->getLock($occid));
    }
    elseif($action === 'createOccurrenceRecord' && $isEditor){
        echo $occManager->createOccurrenceRecord(json_decode($_POST['occurrence'], true));
    }
    elseif($action === 'updateOccurrenceRecord' && $occid && $isEditor){
        echo $occManager->updateOccurrenceRecord($occid, json_decode($_POST['occurrenceData'], true));
    }
    elseif($action === 'getOccurrenceFields'){
        echo json_encode($occManager->getOccurrenceFields());
    }
    elseif($action === 'getOccurrenceDataArr' && $occid){
        echo json_encode($occManager->getOccurrenceData($occid));
    }
    elseif($action === 'getOccurrenceDeterminationArr' && $occid){
        echo json_encode($occManager->getOccurrenceDeterminationData($occid));
    }
    elseif($action === 'getOccurrenceImageArr' && $occid){
        echo json_encode($occManager->getOccurrenceImageData($occid));
    }
    elseif($action === 'getOccurrenceMediaArr' && $occid){
        echo json_encode($occManager->getOccurrenceMediaData($occid));
    }
    elseif($action === 'getOccurrenceChecklistArr' && $occid){
        echo json_encode($occManager->getOccurrenceChecklistData($occid));
    }
    elseif($action === 'getOccurrenceDuplicateArr' && $occid){
        echo json_encode($occManager->getOccurrenceDuplicateData($occid));
    }
    elseif($action === 'getOccurrenceGeneticLinkArr' && $occid){
        echo json_encode($occManager->getOccurrenceGeneticLinkData($occid));
    }
    elseif($action === 'evaluateOccurrenceForDeletion' && $occid && $isEditor){
        echo json_encode($occManager->evaluateOccurrenceForDeletion($occid));
    }
    elseif($action === 'deleteOccurrenceRecord' && $occid && $isEditor){
        echo $occManager->deleteOccurrenceRecord($occid);
    }
}

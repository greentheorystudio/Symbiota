<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Occurrences.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$occid = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
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
elseif($action === 'updateLocalitySecurity' && array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $occurrences = new Occurrences();
    if($action === 'getOccurrenceDataLock' && $isEditor && $occid){
        echo json_encode($occurrences->getLock($occid));
    }
    elseif($action === 'createOccurrenceRecord' && $isEditor){
        echo $occurrences->createOccurrenceRecord(json_decode($_POST['occurrence'], true));
    }
    elseif($action === 'updateOccurrenceRecord' && $occid && $isEditor && array_key_exists('occurrenceData', $_POST)){
        echo $occurrences->updateOccurrenceRecord($occid, json_decode($_POST['occurrenceData'], true));
    }
    elseif($action === 'getOccurrenceFields'){
        echo json_encode($occurrences->getOccurrenceFields());
    }
    elseif($action === 'getOccurrenceDataArr' && $occid){
        echo json_encode($occurrences->getOccurrenceData($occid));
    }
    elseif($action === 'getOccurrenceEditArr' && $occid){
        echo json_encode($occurrences->getOccurrenceEditData($occid));
    }
    elseif($action === 'evaluateOccurrenceForDeletion' && $occid && $isEditor){
        echo json_encode($occurrences->evaluateOccurrenceForDeletion($occid));
    }
    elseif($action === 'deleteOccurrenceRecord' && $occid && $isEditor){
        echo $occurrences->deleteOccurrenceRecord('occid', $occid);
    }
    elseif($action === 'getOccurrencesByCatalogNumber' && array_key_exists('catalognumber', $_POST)){
        $collId = array_key_exists('collid',$_POST) ? (int)$_POST['collid'] : null;
        echo json_encode($occurrences->getOccurrencesByCatalogNumber($_POST['catalognumber'], $collId));
    }
    elseif($action === 'transferOccurrenceRecord' && $isEditor && array_key_exists('transferToCollid', $_POST)){
        $collId = array_key_exists('transferToCollid',$_POST) ? (int)$_POST['transferToCollid'] : null;
        echo $occurrences->transferOccurrenceRecord($occid, $collId);
    }
    elseif($action === 'getOccurrenceIdDataFromIdentifierArr' && $collid && array_key_exists('identifierField',$_POST) && array_key_exists('identifiers',$_POST)){
        echo json_encode($occurrences->getOccurrenceIdDataFromIdentifierArr($collid, $_POST['identifierField'], json_decode($_POST['identifiers'], true)));
    }
    elseif($action === 'batchPopulateOccurrenceGUIDs' && $isEditor && $collid){
        echo $occurrences->batchCreateOccurrenceRecordGUIDs($collid);
    }
    elseif($action === 'getOccurrenceDuplicateIdentifierRecordArr' && $isEditor && $collid){
        $identifierField = $_POST['identifierField'] ?? null;
        $identifier = $_POST['identifier'] ?? null;
        echo json_encode($occurrences->getOccurrenceDuplicateIdentifierRecordArr($collid, $occid, $identifierField, $identifier));
    }
    elseif($action === 'undoOccScinameChange' && $isEditor && $collid){
        $oldName = str_replace(array('%squot;', '%dquot;'), array("'", '"'), $_POST['oldsciname']);
        $newName = str_replace(array('%squot;', '%dquot;'), array("'", '"'), $_POST['newsciname']);
        echo $occurrences->undoOccRecordsCleanedScinameChange($collid, $oldName, $newName);
    }
    elseif($action === 'updateOccWithCleanedName' && $isEditor && $collid){
        echo $occurrences->updateOccRecordsWithCleanedSciname($collid, $_POST['sciname'],$_POST['cleanedsciname'],$_POST['tid']);
    }
    elseif($action === 'updateOccWithNewSciname' && $isEditor && array_key_exists('sciname', $_POST) && array_key_exists('tid', $_POST) && $collid){
        $sciname = $_POST['sciname'];
        $tid = (int)$_POST['tid'];
        echo $occurrences->updateOccRecordsWithNewScinameTid($collid, $sciname, $tid);
    }
    elseif($action === 'getUnlinkedOccSciNames' && $isEditor && $collid){
        echo json_encode($occurrences->getUnlinkedSciNames($collid));
    }
    elseif($action === 'cleanDoubleSpaces' && $isEditor && $collid){
        echo $occurrences->cleanDoubleSpaceNames($collid);
    }
    elseif($action === 'cleanQualifierNames' && $isEditor && $collid){
        echo $occurrences->cleanQualifierNames($collid);
    }
    elseif($action === 'cleanInfra' && $isEditor && $collid){
        echo $occurrences->cleanInfraAbbrNames($collid);
    }
    elseif($action === 'cleanSpNames' && $isEditor && $collid){
        echo $occurrences->cleanSpNames($collid);
    }
    elseif($action === 'cleanTrimNames' && $isEditor && $collid){
        echo $occurrences->cleanTrimNames($collid);
    }
    elseif($action === 'cleanQuestionMarks' && $isEditor && $collid){
        echo $occurrences->cleanQuestionMarks($collid);
    }
    elseif($action === 'updateLocalitySecurity' && $isEditor){
        echo $occurrences->protectGlobalSpecies($collid);
    }
    elseif($action === 'updateOccThesaurusLinkages' && $isEditor && $collid && array_key_exists('kingdomid', $_POST)){
        $kingdomid = (int)$_POST['kingdomid'];
        echo $occurrences->updateOccTaxonomicThesaurusLinkages($collid, $kingdomid);
    }
    elseif($action === 'getUnlinkedScinameCounts' && $isEditor && $collid){
        $returnArr = array();
        $returnArr['taxaCnt'] = $occurrences->getBadTaxaCount($collid);
        $returnArr['occCnt'] = $occurrences->getBadSpecimenCount($collid);
        echo json_encode($returnArr);
    }
    elseif($action === 'updateOccurrenceLocation' && $isEditor && $occid && array_key_exists('locationid', $_POST)){
        $locationid = (int)$_POST['locationid'];
        $updateData = array_key_exists('updateData', $_POST) && (int)$_POST['updateData'] === 1;
        echo $occurrences->updateOccurrenceLocationId($occid, $locationid, $updateData);
    }
    elseif($action === 'updateOccurrenceEvent' && $isEditor && $occid && array_key_exists('eventid', $_POST)){
        $eventid = (int)$_POST['eventid'];
        $updateData = array_key_exists('updateData', $_POST) && (int)$_POST['updateData'] === 1;
        echo $occurrences->updateOccurrenceEventId($occid, $eventid, $updateData);
    }
}

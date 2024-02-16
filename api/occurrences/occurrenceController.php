<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/OccurrenceDataManager.php');

$occid = array_key_exists('occid',$_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && Sanitizer::validateInternalRequest()){
    $occManager = new OccurrenceDataManager();
    if($action === 'getOccurrenceDataLock' && $occid){
        echo json_encode($occManager->getLock($occid));
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
    elseif($action === 'getLocationDataArr' && array_key_exists('locationid', $_POST)){
        echo json_encode($occManager->getLocationData((int)$_POST['locationid']));
    }
    elseif($action === 'getCollectionEventDataArr' && array_key_exists('eventid', $_POST)){
        echo json_encode($occManager->getCollectionEventData((int)$_POST['eventid']));
    }
    elseif($action === 'getAdditionalDataArr' && array_key_exists('eventid', $_POST)){
        echo json_encode($occManager->getAdditionalData((int)$_POST['eventid']));
    }
}

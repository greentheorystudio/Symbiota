<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../models/OccurrenceCollectingEvents.php');

$eventid = array_key_exists('eventid',$_REQUEST) ? (int)$_REQUEST['eventid'] : 0;
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

if($action && SanitizerService::validateInternalRequest()){
    $evtManager = new OccurrenceCollectingEvents();
    if($action === 'getCollectingEventDataArr' && $eventid){
        echo json_encode($evtManager->getCollectingEventData($eventid));
    }
    elseif($action === 'createCollectingEventRecord' && $isEditor){
        echo $evtManager->createCollectingEventRecord(json_decode($_POST['event'], true));
    }
    elseif($action === 'updateCollectingEventRecord' && $eventid && $isEditor){
        echo $evtManager->updateCollectingEventRecord($eventid, json_decode($_POST['eventData'], true));
    }
    elseif($action === 'getCollectingEventFields'){
        echo json_encode($evtManager->getCollectingEventFields());
    }
    elseif($action === 'getConfiguredFieldDataArr' && $eventid){
        echo json_encode($evtManager->getConfiguredFieldData($eventid));
    }
    elseif($action === 'getLocationCollectingEventArr' && $collid && array_key_exists('locationid', $_POST)){
        $locationid = (int)$_POST['locationid'];
        echo json_encode($evtManager->getLocationCollectingEventArr($collid, $locationid));
    }
    elseif($action === 'getOccurrenceCollectingEventArr' && $collid){
        $occid = $_POST['occid'] ?? null;
        $varsArr = array();
        $varsArr['recordedby'] = $_POST['recordedby'] ?? null;
        $varsArr['recordnumber'] = $_POST['recordnumber'] ?? null;
        $varsArr['eventdate'] = $_POST['eventdate'] ?? null;
        $varsArr['lastname'] = $_POST['lastname'] ?? null;
        echo json_encode($evtManager->getOccurrenceCollectingEventArr($collid, $occid, $varsArr));
    }
    elseif($action === 'getCollectingEventBenthicData' && $eventid){
        echo json_encode($evtManager->getCollectingEventBenthicData($eventid));
    }
    elseif($action === 'getCollectingEventCollectionsArr' && $eventid){
        echo json_encode($evtManager->getCollectingEventCollectionsArr($eventid));
    }
    elseif($action === 'addConfiguredDataValue' && $eventid && array_key_exists('datakey', $_POST) && array_key_exists('datavalue', $_POST)){
        echo $evtManager->addConfiguredDataValue($eventid, $_POST['datakey'], $_POST['datavalue']);
    }
    elseif($action === 'deleteConfiguredDataValue' && $eventid && array_key_exists('datakey', $_POST)){
        echo $evtManager->deleteConfiguredDataValue($eventid, $_POST['datakey']);
    }
    elseif($action === 'updateConfiguredDataValue' && $eventid && array_key_exists('datakey', $_POST) && array_key_exists('datavalue', $_POST)){
        echo $evtManager->updateConfiguredDataValue($eventid, $_POST['datakey'], $_POST['datavalue']);
    }
}

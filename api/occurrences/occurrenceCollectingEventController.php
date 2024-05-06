<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectingEventManager.php');

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

if($action && Sanitizer::validateInternalRequest()){
    $evtManager = new OccurrenceCollectingEventManager();
    if($action === 'getCollectingEventDataArr' && $eventid){
        echo json_encode($evtManager->getCollectingEventData($eventid));
    }
    elseif($action === 'createCollectingEventRecord' && $isEditor){
        echo $evtManager->createCollectingEventRecord(json_decode($_POST['event'], true));
    }
    elseif($action === 'updateCollectingEventRecord' && $eventid && $isEditor){
        echo $evtManager->updateCollectingEventRecord($eventid, json_decode($_POST['eventData'], true));
    }
    elseif($action === 'getAdditionalDataArr' && $eventid){
        echo json_encode($evtManager->getAdditionalData($eventid));
    }
    elseif($action === 'getCollectingEventArr' && $collid){
        $occid = $_POST['occid'] ?? null;
        $varsArr = array();
        $varsArr['recordedby'] = $_POST['recordedby'] ?? null;
        $varsArr['recordnumber'] = $_POST['recordnumber'] ?? null;
        $varsArr['eventdate'] = $_POST['eventdate'] ?? null;
        $varsArr['lastname'] = $_POST['lastname'] ?? null;
        $varsArr['locationid'] = $_POST['locationid'] ?? null;
        echo json_encode($evtManager->getCollectingEventArr($collid, $occid, $varsArr));
    }
}

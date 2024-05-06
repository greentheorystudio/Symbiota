<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/OccurrenceLocationManager.php');

$locationid = array_key_exists('locationid',$_REQUEST) ? (int)$_REQUEST['locationid'] : 0;
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
    $locManager = new OccurrenceLocationManager();
    if($action === 'getLocationDataArr' && $isEditor && $locationid){
        echo json_encode($locManager->getLocationData($locationid));
    }
    elseif($action === 'createLocationRecord' && $isEditor){
        echo $locManager->createLocationRecord(json_decode($_POST['location'], true));
    }
    elseif($action === 'updateLocationRecord' && $locationid && $isEditor){
        echo $locManager->updateLocationRecord($locationid, json_decode($_POST['locationData'], true));
    }
}

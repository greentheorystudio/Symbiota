<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../models/OccurrenceLocations.php');

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

if($action && SanitizerService::validateInternalRequest()){
    $locManager = new OccurrenceLocations();
    if($action === 'getLocationDataArr' && $isEditor && $locationid){
        echo json_encode($locManager->getLocationData($locationid));
    }
    elseif($action === 'createLocationRecord' && $isEditor){
        echo $locManager->createLocationRecord(json_decode($_POST['location'], true));
    }
    elseif($action === 'updateLocationRecord' && $locationid && $isEditor){
        echo $locManager->updateLocationRecord($locationid, json_decode($_POST['locationData'], true));
    }
    elseif($action === 'getLocationFields'){
        echo json_encode($locManager->getLocationFields());
    }
    else if($action === 'getNameCodeAutocompleteLocationList'){
        echo json_encode($locManager->getAutocompleteLocationList($collid, $_POST['key'], $_POST['term']));
    }
    else if($action === 'getNearbyLocationArr' && array_key_exists('decimallatitude',$_POST) && array_key_exists('decimallongitude',$_POST)){
        echo json_encode($locManager->getNearbyLocationArr($collid, $locationid, $_POST['decimallatitude'], $_POST['decimallongitude']));
    }
}

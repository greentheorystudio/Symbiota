<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/OccurrenceLocations.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

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
    $occurrenceLocations = new OccurrenceLocations();
    if($action === 'getLocationDataArr' && $isEditor && $locationid){
        echo json_encode($occurrenceLocations->getLocationData($locationid));
    }
    elseif($action === 'createLocationRecord' && $isEditor && array_key_exists('location',$_POST)){
        echo $occurrenceLocations->createLocationRecord(json_decode($_POST['location'], true));
    }
    elseif($action === 'updateLocationRecord' && $locationid && $isEditor){
        echo $occurrenceLocations->updateLocationRecord($locationid, json_decode($_POST['locationData'], true));
    }
    elseif($action === 'getLocationFields'){
        echo json_encode($occurrenceLocations->getLocationFields());
    }
    elseif($action === 'getNameCodeAutocompleteLocationList'){
        echo json_encode($occurrenceLocations->getAutocompleteLocationList($collid, $_POST['key'], $_POST['term']));
    }
    elseif($action === 'getNearbyLocationArr' && array_key_exists('decimallatitude',$_POST) && array_key_exists('decimallongitude',$_POST)){
        echo json_encode($occurrenceLocations->getNearbyLocationArr($collid, $locationid, $_POST['decimallatitude'], $_POST['decimallongitude']));
    }
    elseif($action === 'searchLocations' && array_key_exists('criteria',$_POST)){
        echo json_encode($occurrenceLocations->searchLocations($collid, json_decode($_POST['criteria'], true)));
    }
    elseif($action === 'deleteLocationRecord' && $locationid && $isEditor){
        echo $occurrenceLocations->deleteLocationRecord($locationid);
    }
}

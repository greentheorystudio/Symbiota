<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/OccurrenceMeasurementsOrFacts.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$mofid = array_key_exists('mofid', $_REQUEST) ? (int)$_REQUEST['mofid'] : 0;
$dataType = $_REQUEST['type'] ?? null;
$id = array_key_exists('id', $_REQUEST) ? (int)$_REQUEST['id'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}

if($action && SanitizerService::validateInternalRequest()){
    $occurrenceMeasurementsOrFacts = new OccurrenceMeasurementsOrFacts();
    if($action === 'getMofDataByTypeAndId' && $dataType && $id){
        echo json_encode($occurrenceMeasurementsOrFacts->getMofDataByTypeAndId($dataType, $id));
    }
    elseif($action === 'processMofEdits' && $isEditor && $dataType && $id && array_key_exists('editData', $_POST)){
        echo $occurrenceMeasurementsOrFacts->processMofEdits($dataType, $id, json_decode($_POST['editData'], true));
    }
}

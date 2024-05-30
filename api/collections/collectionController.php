<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/CollectionManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');

$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && Sanitizer::validateInternalRequest()){
    $collManager = new CollectionManager();
    if($action === 'getCollectionArr'){
        echo json_encode($collManager->getCollectionArr());
    }
    elseif($action === 'getCollectionInfoArr' && $collid){
        echo json_encode($collManager->getCollectionInfoArr($collid));
    }
    elseif($action === 'updateCollectionStatistics' && $isEditor && array_key_exists('collidStr', $_POST)){
        echo $collManager->updateCollectionStatistics($_POST['collidStr']);
    }
    elseif($action === 'cleanSOLRIndex' && $isEditor && array_key_exists('collidStr', $_POST)){
        echo $collManager->cleanSOLRIndex($_POST['collidStr']);
    }
    elseif($action === 'getSpeciesListDownloadData' && $collid){
        echo json_encode($collManager->getSpeciesListDownloadData($collid));
    }
}

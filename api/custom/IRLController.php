<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/IRLManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;
$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && Sanitizer::validateInternalRequest()){
    $irlManager = new IRLManager();
    if($action === 'getNativeStatus' && $tId){
        echo $irlManager->getNativeStatus($tId);
    }
    elseif($action === 'getProjectEnvironmentalData' && $collid){
        echo json_encode($irlManager->getProjectEnvironmentalData($collid));
    }
    elseif($action === 'getProjectAmbiInfaunaData' && $collid){
        echo json_encode($irlManager->getProjectAmbiInfaunaData($collid));
    }
}

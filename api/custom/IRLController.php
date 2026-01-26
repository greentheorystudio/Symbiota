<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/IRLDataService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;
$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $irlManager = new IRLDataService();
    if($action === 'getNativeStatus' && $tId){
        echo $irlManager->getNativeStatus($tId);
    }
    elseif($action === 'getProjectEnvironmentalData' && $collid){
        echo json_encode($irlManager->getProjectEnvironmentalData($collid));
    }
    elseif($action === 'getProjectAmbiInfaunaData' && $collid){
        echo json_encode($irlManager->getProjectAmbiInfaunaData($collid));
    }
    elseif($action === 'getProjectRScriptData' && $collid){
        echo json_encode($irlManager->getProjectRScriptData($collid));
    }
    elseif($action === 'getOccurrencceTaxaCntsByKingdomDecade'){
        echo json_encode($irlManager->getOccurrencceTaxaCntsByKingdomDecade());
    }
}

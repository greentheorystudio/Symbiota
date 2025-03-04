<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Checklists.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $checklists = new Checklists();
    if($action === 'getChecklistListByUserRights'){
        echo json_encode($checklists->getChecklistListByUserRights());
    }
    elseif($action === 'createChecklistRecord' && $isEditor){
        echo $checklists->createChecklistRecord(json_decode($_POST['checklist'], true));
    }
    elseif($action === 'updateChecklistRecord' && $clid && $isEditor && array_key_exists('checklistData', $_POST)){
        echo $checklists->updateChecklistRecord($clid, json_decode($_POST['checklistData'], true));
    }
    elseif($action === 'getChecklistData' && $clid){
        echo json_encode($checklists->getChecklistData($clid));
    }
    elseif($action === 'deleteChecklistRecord' && $clid && $isEditor){
        echo $checklists->deleteChecklistRecord($clid);
    }
    elseif($action === 'createTemporaryChecklistFromTidArr' && array_key_exists('tidArr', $_POST)){
        echo $checklists->createTemporaryChecklistFromTidArr(json_decode($_POST['tidArr'], true));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../classes/ChecklistAdmin.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action){
    $checklistAdminManager = new ChecklistAdmin();
    if(SanitizerService::validateInternalRequest() && $action === 'getChecklistsProjectsByUid' && array_key_exists('uid',$_POST)){
        echo json_encode($checklistAdminManager->getManagementLists($_POST['uid']));
    }
}

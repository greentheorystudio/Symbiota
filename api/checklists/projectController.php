<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Projects.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$pid = array_key_exists('pid',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ProjAdmin', $GLOBALS['USER_RIGHTS']) && in_array($pid, $GLOBALS['USER_RIGHTS']['ProjAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $projects = new Projects();
    if($action === 'getProjectListByUserRights'){
        echo json_encode($projects->getProjectListByUserRights());
    }
    elseif($action === 'getProjectChecklists' && $pid){
        echo json_encode($projects->getProjectChecklists($pid));
    }
    elseif($action === 'createProjectRecord' && $isEditor){
        echo $projects->createProjectRecord(json_decode($_POST['project'], true));
    }
    elseif($action === 'updateProjectRecord' && $pid && $isEditor && array_key_exists('projectData', $_POST)){
        echo $projects->updateProjectRecord($pid, json_decode($_POST['projectData'], true));
    }
    elseif($action === 'getProjectData' && $pid){
        echo json_encode($projects->getProjectData($pid));
    }
    elseif($action === 'deleteProjectRecord' && $pid && $isEditor){
        echo $projects->deleteProjectRecord($pid);
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Projects.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action && SanitizerService::validateInternalRequest()){
    $projects = new Projects();
    if($action === 'getProjectListByUserRights'){
        echo json_encode($projects->getProjectListByUserRights());
    }
}

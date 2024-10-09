<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Permissions.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && SanitizerService::validateInternalRequest()){
    $permissions = new Permissions();
    if($action === 'validatePermission' && (array_key_exists('permission',$_POST) || array_key_exists('permissionJson',$_POST))){
        $key = array_key_exists('key',$_POST) ? (int)$_POST['key'] : null;
        if(array_key_exists('permissionJson',$_POST)){
            $permissionVal = json_decode($_POST['permissionJson']);
        }
        else{
            $permissionVal = $_POST['permission'];
        }
        echo json_encode($permissions->validatePermission($permissionVal, $key));
    }
}

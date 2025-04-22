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
    elseif($action === 'getCurrentUserRights'){
        echo json_encode($permissions->getCurrentUserRights());
    }
    elseif($action === 'getPermissionsByUid' && array_key_exists('uid', $_POST)){
        echo json_encode($permissions->getPermissionsByUid($_POST['uid']), JSON_FORCE_OBJECT);
    }
    elseif($action === 'deleteUserPermission' && array_key_exists('uid', $_POST) && array_key_exists('permission', $_POST) && array_key_exists('tablepk', $_POST)){
        $tablePk = $_POST['tablepk'] ?? null;
        echo $permissions->deletePermission($_POST['uid'], $_POST['permission'], $tablePk);
    }
    elseif($action === 'deleteAllUserPermissions' && array_key_exists('uid', $_POST)){
        echo $permissions->deleteAllPermissions($_POST['uid']);
    }
    elseif($action === 'addUserPermissions' && array_key_exists('uid', $_POST) && array_key_exists('permissionArr', $_POST)){
        echo $permissions->addPermissions(json_decode($_POST['permissionArr'], true), $_POST['uid']);
    }
}

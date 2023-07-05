<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/PermissionsManager.php');
include_once(__DIR__ . '/../../classes/ProfileManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action && Sanitizer::validateInternalRequest()){
    $permissionManager = new PermissionsManager();
    $profileHandler = new ProfileManager();
    if($action === 'login' && array_key_exists('username',$_POST) && array_key_exists('password',$_POST)){
        if($profileHandler->setUserName($_POST['username'])){
            if(array_key_exists('remember',$_POST) && (int)$_POST['remember'] === 1){
                $profileHandler->setRememberMe(true);
            }
            echo $profileHandler->authenticate($_POST['password']);
        }
        else{
            echo 0;
        }
    }
    elseif($action === 'logout'){
        $profileHandler->reset();
    }
    elseif($action === 'retrieveUsername' && array_key_exists('email',$_POST)){
        if($profileHandler->validateEmailAddress($_POST['email'])){
            echo $profileHandler->lookupUserName($_POST['email']);
        }
        else{
            echo 0;
        }
    }
    elseif($action === 'resetPassword' && array_key_exists('username',$_POST)){
        $admin = $GLOBALS['IS_ADMIN'] && array_key_exists('admin',$_POST) && (int)$_POST['admin'] === 1;
        echo $profileHandler->resetPassword($_POST['username'],$admin);
    }
    elseif($action === 'processConfirmationCode' && array_key_exists('uid',$_POST) && array_key_exists('confirmationCode',$_POST)){
        echo $profileHandler->validateFromConfirmationEmail($_POST['uid'],$_POST['confirmationCode']);
    }
    elseif($action === 'validatePermission' && array_key_exists('permission',$_POST)){
        $key = array_key_exists('key',$_POST) ? (int)$_POST['key'] : null;
        echo $profileHandler->validatePermission($_POST['permission'],$key);
    }
}

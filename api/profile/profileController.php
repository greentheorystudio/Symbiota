<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../classes/PermissionsManager.php');
include_once(__DIR__ . '/../../classes/ProfileManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action && SanitizerService::validateInternalRequest()){
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
        echo $profileHandler->lookupUserName($_POST['email']);
    }
    elseif($action === 'resetPassword' && array_key_exists('username',$_POST)){
        $admin = $GLOBALS['IS_ADMIN'] && array_key_exists('admin',$_POST) && (int)$_POST['admin'] === 1;
        echo $profileHandler->resetPassword($_POST['username'],$admin);
    }
    elseif($action === 'processConfirmationCode' && array_key_exists('uid',$_POST) && array_key_exists('confirmationCode',$_POST)){
        echo $profileHandler->validateFromConfirmationEmail($_POST['uid'],$_POST['confirmationCode']);
    }
    elseif($action === 'validatePermission' && (array_key_exists('permission',$_POST) || array_key_exists('permissionJson',$_POST))){
        $key = array_key_exists('key',$_POST) ? (int)$_POST['key'] : null;
        if(array_key_exists('permissionJson',$_POST)){
            $permissions = json_decode($_POST['permissionJson']);
        }
        else{
            $permissions = $_POST['permission'];
        }
        echo json_encode($permissionManager->validatePermission($permissions, $key));
    }
    elseif($action === 'getUidFromUsername' && array_key_exists('username',$_POST)){
        echo $profileHandler->getUidFromUsername($_POST['username']);
    }
    elseif($action === 'getUidFromEmail' && array_key_exists('email',$_POST)){
        echo $profileHandler->getUidFromEmail($_POST['email']);
    }
    elseif($action === 'createAccount' && array_key_exists('user',$_POST)){
        echo $profileHandler->register(json_decode($_POST['user'], true));
    }
    elseif($action === 'getAccountInfoByUid' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === (int)$GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo json_encode($profileHandler->getAccountInfoByUid($_POST['uid']));
    }
    elseif($action === 'sendConfirmationEmail' && array_key_exists('uid',$_POST)){
        echo $profileHandler->sendConfirmationEmail($_POST['uid']);
    }
    elseif($action === 'editAccount' && array_key_exists('user',$_POST)){
        echo $profileHandler->updateAccountInfo(json_decode($_POST['user'], true));
    }
    elseif($action === 'changePassword' && array_key_exists('uid',$_POST) && array_key_exists('pwd',$_POST)){
        echo $profileHandler->changePassword($_POST['uid'], $_POST['pwd']);
    }
    elseif($action === 'deleteAccount' && array_key_exists('uid',$_POST)){
        echo $profileHandler->deleteProfile($_POST['uid']);
    }
    elseif($action === 'getAccessTokenCnt' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === $GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo $profileHandler->getTokenCnt($_POST['uid']);
    }
    elseif($action === 'clearAccessTokens' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === $GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo $profileHandler->clearAccessTokens($_POST['uid']);
    }
    elseif($action === 'getAccountCollections'){
        echo json_encode($profileHandler->getAccountCollectionArr());
    }
    elseif($action === 'getPersonalOccurrencesCsvData' && array_key_exists('collid',$_POST)){
        echo json_encode($profileHandler->getPersonalOccurrencesCsvData($_POST['collid']));
    }
}

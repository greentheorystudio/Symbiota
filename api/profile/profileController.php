<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Users.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && SanitizerService::validateInternalRequest()){
    $users = new Users();
    if($action === 'login' && array_key_exists('username',$_POST) && array_key_exists('password',$_POST)){
        $rememberMe = array_key_exists('remember',$_POST) && (int)$_POST['remember'] === 1;
        echo $users->authenticateUserFromPassword($_POST['username'], $_POST['password'], $rememberMe);
    }
    elseif($action === 'logout'){
        $users->clearCookieSession();
    }
    elseif($action === 'retrieveUsername' && array_key_exists('email',$_POST)){
        echo $users->sendUsernameEmail($_POST['email']);
    }
    elseif($action === 'resetPassword' && array_key_exists('username',$_POST)){
        $admin = $GLOBALS['IS_ADMIN'] && array_key_exists('admin',$_POST) && (int)$_POST['admin'] === 1;
        echo $users->resetPassword($_POST['username'], $admin);
    }
    elseif($action === 'processConfirmationCode' && array_key_exists('uid',$_POST) && array_key_exists('confirmationCode',$_POST)){
        echo $users->validateFromConfirmationEmail($_POST['uid'], $_POST['confirmationCode']);
    }
    elseif($action === 'getUserFromUsername' && array_key_exists('username',$_POST)){
        echo json_encode($users->getUserByUsername($_POST['username']));
    }
    elseif($action === 'getUserFromEmail' && array_key_exists('email',$_POST)){
        echo json_encode($users->getUserByEmail($_POST['email']));
    }
    elseif($action === 'createAccount' && array_key_exists('user', $_POST)){
        echo $users->createUser(json_decode($_POST['user'], true));
    }
    elseif($action === 'getUserByUid' && array_key_exists('uid',$_POST)){
        echo json_encode($users->getUserByUid($_POST['uid']));
    }
    elseif($action === 'sendConfirmationEmail' && array_key_exists('uid',$_POST)){
        echo $users->sendConfirmationEmail($_POST['uid']);
    }
    elseif($action === 'editAccount' && array_key_exists('user',$_POST) && array_key_exists('uid',$_POST)){
        echo $users->updateUser($_POST['uid'], json_decode($_POST['user'], true));
    }
    elseif($action === 'changePassword' && array_key_exists('uid',$_POST) && array_key_exists('pwd',$_POST)){
        echo $users->changePassword($_POST['uid'], $_POST['pwd']);
    }
    elseif($action === 'deleteAccount' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === $GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo $users->deleteUser($_POST['uid']);
    }
    elseif($action === 'getAccessTokenCnt' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === $GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo $users->getTokenCnt($_POST['uid']);
    }
    elseif($action === 'clearAccessTokens' && array_key_exists('uid',$_POST) && ((int)$_POST['uid'] === $GLOBALS['SYMB_UID'] || $GLOBALS['IS_ADMIN'])){
        echo $users->clearAccessTokens($_POST['uid']);
    }
}

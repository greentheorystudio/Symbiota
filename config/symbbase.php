<?php
include_once(__DIR__ . '/../classes/Encryption.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
include_once(__DIR__ . '/../classes/Sanitizer.php');
Sanitizer::validateRequestPath();
ini_set('session.gc_maxlifetime',3600);
if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)){
    ini_set('session.cookie_secure',1);
}
session_start();

if(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'){
    $GLOBALS['CLIENT_ROOT'] = substr($GLOBALS['CLIENT_ROOT'],0, -1);
}
if(substr($GLOBALS['SERVER_ROOT'],-1) === '/'){
    $GLOBALS['SERVER_ROOT'] = substr($GLOBALS['SERVER_ROOT'],0, -1);
}

$GLOBALS['PARAMS_ARR'] = array();
$GLOBALS['USER_RIGHTS'] = array();
if(!isset($_SESSION['userparams'])){
    if((isset($_COOKIE['SymbiotaCrumb']) && (!isset($_REQUEST['submit']) || $_REQUEST['submit'] !== 'logout'))){
        $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true, 512, JSON_THROW_ON_ERROR);
        if($tokenArr){
            $pHandler = new ProfileManager();
            if($pHandler->setUserName($tokenArr[0])){
                $pHandler->setRememberMe(true);
                $pHandler->setToken($tokenArr[1]);
                $pHandler->setTokenAuthSql();
                if(!$pHandler->authenticate()){
                    $pHandler->reset();
                }
            }
            $pHandler->__destruct();
        }
    }

    if((isset($_COOKIE['SymbiotaCrumb']) && ((isset($_REQUEST['submit']) && $_REQUEST['submit'] === 'logout') || isset($_REQUEST['loginas'])))){
        $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true, 512, JSON_THROW_ON_ERROR);
        if($tokenArr){
            $pHandler = new ProfileManager();
            $uid = $pHandler->getUid($tokenArr[0]);
            $pHandler->deleteToken($uid,$tokenArr[1]);
            $pHandler->__destruct();
        }
    }
}

if(isset($_SESSION['userparams'])){
    $GLOBALS['PARAMS_ARR'] = $_SESSION['userparams'];
}

if(isset($_SESSION['userrights'])){
    $GLOBALS['USER_RIGHTS'] = $_SESSION['userrights'];
}

$GLOBALS['CSS_VERSION'] = '20210621';
if(isset($GLOBALS['CSS_VERSION_LOCAL']) && ($GLOBALS['CSS_VERSION_LOCAL'] > $GLOBALS['CSS_VERSION'])) {
    $GLOBALS['CSS_VERSION'] = $GLOBALS['CSS_VERSION_LOCAL'];
}
if(!isset($GLOBALS['EML_PROJECT_ADDITIONS'])) {
    $GLOBALS['EML_PROJECT_ADDITIONS'] = array();
}
$GLOBALS['USER_DISPLAY_NAME'] = (array_key_exists('dn',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['dn']: '');
$GLOBALS['USERNAME'] = (array_key_exists('un',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['un']:0);
$GLOBALS['SYMB_UID'] = (array_key_exists('uid',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['uid']:0);
$GLOBALS['IS_ADMIN'] = (array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS'])?1:0);
$GLOBALS['SOLR_MODE'] = (isset($GLOBALS['SOLR_URL']) && $GLOBALS['SOLR_URL']);
$GLOBALS['CHECKLIST_FG_EXPORT'] = (isset($GLOBALS['ACTIVATE_CHECKLIST_FG_EXPORT']) && $GLOBALS['ACTIVATE_CHECKLIST_FG_EXPORT']);
$GLOBALS['FIELDGUIDE_ACTIVE'] = (isset($GLOBALS['ACTIVATE_FIELDGUIDE']) && $GLOBALS['ACTIVATE_FIELDGUIDE']);
$GLOBALS['BROADGEOREFERENCE'] = (isset($GLOBALS['GEOREFERENCE_POLITICAL_DIVISIONS']) && $GLOBALS['GEOREFERENCE_POLITICAL_DIVISIONS']);

$LANG_TAG = 'en';
if(isset($_REQUEST['lang']) && $_REQUEST['lang']){
    $LANG_TAG = $_REQUEST['lang'];

    $_SESSION['lang'] = $LANG_TAG;
    setcookie('lang', $LANG_TAG, time() + (3600 * 24 * 30));
}
else if(isset($_SESSION['lang']) && $_SESSION['lang']){
    $LANG_TAG = $_SESSION['lang'];
}
else if(isset($_COOKIE['lang']) && $_COOKIE['lang']){
    $LANG_TAG = $_COOKIE['lang'];
}
else if(strlen($GLOBALS['DEFAULT_LANG']) === 2) {
    $LANG_TAG = $GLOBALS['DEFAULT_LANG'];
}
if(!$LANG_TAG || strlen($LANG_TAG) !== 2) {
    $LANG_TAG = 'en';
}

$GLOBALS['RIGHTS_TERMS_DEFS'] = array(
    'http://creativecommons.org/publicdomain/zero/1.0/' => array(
        'title' => 'CC0 1.0 (Public-domain)',
        'url' => 'https://creativecommons.org/publicdomain/zero/1.0/legalcode',
        'def' => 'Users can copy, modify, distribute and perform the work, even for commercial purposes, all without asking permission.'
    ),
    'http://creativecommons.org/licenses/by/3.0/' => array(
        'title' => 'CC BY (Attribution)',
        'url' => 'http://creativecommons.org/licenses/by/3.0/legalcode',
        'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material for any purpose, even commercially. The licensor cannot revoke these freedoms as long as you follow the license terms.'
    ),
    'http://creativecommons.org/licenses/by-nc/3.0/' => array(
        'title' => 'CC BY-NC (Attribution-Non-Commercial)',
        'url' => 'http://creativecommons.org/licenses/by-nc/3.0/legalcode',
        'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material. The licensor cannot revoke these freedoms as long as you follow the license terms.'
    ),
    'http://creativecommons.org/licenses/by/4.0/' => array(
        'title' => 'CC BY (Attribution)',
        'url' => 'http://creativecommons.org/licenses/by/4.0/legalcode',
        'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material for any purpose, even commercially. The licensor cannot revoke these freedoms as long as you follow the license terms.'
    ),
    'http://creativecommons.org/licenses/by-nc/4.0/' => array(
        'title' => 'CC BY-NC (Attribution-Non-Commercial)',
        'url' => 'http://creativecommons.org/licenses/by-nc/4.0/legalcode',
        'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material. The licensor cannot revoke these freedoms as long as you follow the license terms.'
    ),
    'http://creativecommons.org/licenses/by-nc-nd/4.0/' => array(
        'title' => 'CC BY-NC-ND 4.0 (Attribution-NonCommercial-NoDerivatives 4.0 International)',
        'url' => 'http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode',
        'def' => 'Users can copy and redistribute the material in any medium or format. The licensor cannot revoke these freedoms as long as you follow the license terms.'
    )
);

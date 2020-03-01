<?php
include_once(__DIR__ . '/../classes/Encryption.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
ini_set('session.gc_maxlifetime',3600);
session_start();

set_include_path(get_include_path() . PATH_SEPARATOR . $SERVER_ROOT . PATH_SEPARATOR . $SERVER_ROOT. '/config/' . PATH_SEPARATOR . $SERVER_ROOT. '/classes/');

if(substr($CLIENT_ROOT,-1) === '/'){
	$CLIENT_ROOT = substr($CLIENT_ROOT,0, -1);
}
if(substr($SERVER_ROOT,-1) === '/'){
	$SERVER_ROOT = substr($SERVER_ROOT,0, -1);
}

$PARAMS_ARR = array();
$USER_RIGHTS = array();
if(!isset($_SESSION['userparams'])){
    if((isset($_COOKIE['SymbiotaCrumb']) && (!isset($_REQUEST['submit']) || $_REQUEST['submit'] !== 'logout'))){
        $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true);
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
        $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true);
        if($tokenArr){
            $pHandler = new ProfileManager();
            $uid = $pHandler->getUid($tokenArr[0]);
            $pHandler->deleteToken($uid,$tokenArr[1]);
            $pHandler->__destruct();
        }
    }
}

if(isset($_SESSION['userparams'])){
    $PARAMS_ARR = $_SESSION['userparams'];
}

if(isset($_SESSION['userrights'])){
    $USER_RIGHTS = $_SESSION['userrights'];
}

$CSS_VERSION = '6';
if(!isset($CSS_VERSION_LOCAL)) {
    $CSS_VERSION_LOCAL = $CSS_VERSION;
}
if(!isset($EML_PROJECT_ADDITIONS)) {
    $EML_PROJECT_ADDITIONS = array();
}
$USER_DISPLAY_NAME = (array_key_exists('dn',$PARAMS_ARR)?$PARAMS_ARR['dn']: '');
$USERNAME = (array_key_exists('un',$PARAMS_ARR)?$PARAMS_ARR['un']:0);
$SYMB_UID = (array_key_exists('uid',$PARAMS_ARR)?$PARAMS_ARR['uid']:0);
$IS_ADMIN = (array_key_exists('SuperAdmin',$USER_RIGHTS)?1:0);
$SOLR_MODE = (isset($SOLR_URL) && $SOLR_URL);
$CHECKLIST_FG_EXPORT = (isset($ACTIVATE_CHECKLIST_FG_EXPORT) && $ACTIVATE_CHECKLIST_FG_EXPORT);
$FIELDGUIDE_ACTIVE = (isset($ACTIVATE_FIELDGUIDE) && $ACTIVATE_FIELDGUIDE);
$ETHNO_ACTIVE = (isset($ETHNOBIOLOGY_MOD_IS_ACTIVE) && $ETHNOBIOLOGY_MOD_IS_ACTIVE);
$GEOLOCATION = (isset($ACTIVATE_GEOLOCATION) && $ACTIVATE_GEOLOCATION);
$BROADGEOREFERENCE = (isset($GEOREFERENCE_POLITICAL_DIVISIONS) && $GEOREFERENCE_POLITICAL_DIVISIONS);

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
else if(strlen($DEFAULT_LANG) === 2) {
    $LANG_TAG = $DEFAULT_LANG;
}
if(!$LANG_TAG || strlen($LANG_TAG) !== 2) {
    $LANG_TAG = 'en';
}

$RIGHTS_TERMS_DEFS = array(
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

if(!isset($MAPPING_BOUNDARIES) || !$MAPPING_BOUNDARIES){
    $MAPPING_BOUNDARIES = '42.3;-100.5;18.0;-127';
}

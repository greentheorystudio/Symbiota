<?php
include_once(__DIR__ . '/../classes/Encryption.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
include_once(__DIR__ . '/../classes/Sanitizer.php');
Sanitizer::validateRequestPath();
ini_set('session.gc_maxlifetime',3600);
ini_set('session.cookie_httponly',1);
if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)){
    ini_set('session.cookie_secure',1);
}
session_start();

$confManager = new ConfigurationManager();
$confManager->setGlobalArr();
$confManager->setGlobalCssVersion();

if(!isset($_SESSION['PARAMS_ARR'])){
    $confManager->readClientCookies();
}

if(isset($_SESSION['PARAMS_ARR'])){
    $GLOBALS['PARAMS_ARR'] = $_SESSION['PARAMS_ARR'];
}

if(isset($_SESSION['USER_RIGHTS'])){
    $GLOBALS['USER_RIGHTS'] = $_SESSION['USER_RIGHTS'];
}

$GLOBALS['USER_DISPLAY_NAME'] = (array_key_exists('dn',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['dn']: '');
$GLOBALS['USERNAME'] = (array_key_exists('un',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['un']:0);
$GLOBALS['SYMB_UID'] = (array_key_exists('uid',$GLOBALS['PARAMS_ARR'])?$GLOBALS['PARAMS_ARR']['uid']:0);
$GLOBALS['IS_ADMIN'] = (array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS'])?1:0);
$GLOBALS['SOLR_MODE'] = (isset($GLOBALS['SOLR_URL']) && $GLOBALS['SOLR_URL']);
$GLOBALS['CHECKLIST_FG_EXPORT'] = (isset($GLOBALS['ACTIVATE_CHECKLIST_FG_EXPORT']) && $GLOBALS['ACTIVATE_CHECKLIST_FG_EXPORT']);
$GLOBALS['BROADGEOREFERENCE'] = (isset($GLOBALS['GEOREFERENCE_POLITICAL_DIVISIONS']) && $GLOBALS['GEOREFERENCE_POLITICAL_DIVISIONS']);

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

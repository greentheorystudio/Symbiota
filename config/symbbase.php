<?php
include_once(__DIR__ . '/../models/Configurations.php');
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../services/EncryptionService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
ini_set('session.gc_maxlifetime',3600);
ini_set('session.cookie_httponly',1);
if(SanitizerService::getConnectionProtocol() === 'https://') {
    ini_set('session.cookie_secure',1);
}
session_start();

$confManager = new Configurations();
$confManager->setGlobalArr();
$confManager->setGlobalCssVersion();
SanitizerService::validateRequestPath();

if(isset($_SESSION['PARAMS_ARR'])){
    $GLOBALS['PARAMS_ARR'] = $_SESSION['PARAMS_ARR'];
    (new Permissions)->setUserPermissions();
}
else{
    $confManager->readClientCookies();
}

$GLOBALS['USER_DISPLAY_NAME'] = (array_key_exists('dn',$GLOBALS['PARAMS_ARR']) ? $GLOBALS['PARAMS_ARR']['dn'] : '');
$GLOBALS['USERNAME'] = (array_key_exists('un',$GLOBALS['PARAMS_ARR']) ? $GLOBALS['PARAMS_ARR']['un'] : 0);
$GLOBALS['SYMB_UID'] = (array_key_exists('uid',$GLOBALS['PARAMS_ARR']) ? $GLOBALS['PARAMS_ARR']['uid'] : 0);
$GLOBALS['VALID_USER'] = (array_key_exists('valid', $GLOBALS['PARAMS_ARR']) && $GLOBALS['PARAMS_ARR']['valid'] === 1);
$GLOBALS['IS_ADMIN'] = (array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) ? 1 : 0);
$GLOBALS['PUBLIC_CHECKLIST'] = (
    array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('CollTaxon',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS']) ||
    array_key_exists('PublicChecklist',$GLOBALS['USER_RIGHTS'])
);
$GLOBALS['SOLR_MODE'] = (isset($GLOBALS['SOLR_URL']) && $GLOBALS['SOLR_URL']);

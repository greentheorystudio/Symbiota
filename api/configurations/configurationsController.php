<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$prop = array_key_exists('prop', $_REQUEST) ? $_REQUEST['prop'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    if($action === 'getGlobalConfigValue' && $prop){
        echo $GLOBALS[$prop];
    }
}

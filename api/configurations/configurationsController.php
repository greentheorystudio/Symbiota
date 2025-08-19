<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Configurations.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $configurations = new Configurations();
    if($action === 'getGlobalConfigValue' && array_key_exists('prop', $_POST)){
        $prop = array_key_exists('prop', $_REQUEST) ? $_REQUEST['prop'] : '';
        echo is_array($GLOBALS[$prop]) ? json_encode($GLOBALS[$prop]) : $GLOBALS[$prop];
    }
    elseif($action === 'addOotdConfigJson' && array_key_exists('jsonVal', $_POST)){
        echo $configurations->validateOotdConfigJson($_POST['jsonVal']) ? $configurations->addConfiguration('OOTD_CONFIG_JSON', $_POST['jsonVal']) : null;
    }
    elseif($action === 'updateOotdConfigJson' && array_key_exists('jsonVal', $_POST)){
        echo $configurations->validateOotdConfigJson($_POST['jsonVal']) ? $configurations->updateConfigurationValue('OOTD_CONFIG_JSON', $_POST['jsonVal']) : null;
    }
}

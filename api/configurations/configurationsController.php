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
    elseif($action === 'addConfigurationArr' && $isEditor && array_key_exists('data', $_POST)){
        echo $configurations->addConfigurationArr(json_decode($_POST['data'], true));
    }
    elseif($action === 'updateConfigurationValueArr' && $isEditor && array_key_exists('data', $_POST)){
        echo $configurations->updateConfigurationValueArr(json_decode($_POST['data'], true));
    }
    elseif($action === 'deleteConfigurationArr' && $isEditor && array_key_exists('data', $_POST)){
        echo $configurations->deleteConfigurationArr(json_decode($_POST['data'], true));
    }
    elseif($action === 'updateCss' && $isEditor){
        echo $configurations->updateCssVersion();
    }
    elseif($action === 'validateNameCore' && $isEditor && array_key_exists('value', $_POST)){
        echo $configurations->validateNewConfNameCore($_POST['value']);
    }
    elseif($action === 'validateNameExisting' && $isEditor && array_key_exists('value', $_POST)){
        echo $configurations->validateNewConfNameExisting($_POST['value']);
    }
    elseif($action === 'validateServerWritePath' && $isEditor && array_key_exists('value', $_POST)){
        echo $configurations->validatePathIsWritable($_POST['value']);
    }
    elseif($action === 'validateServerPath' && $isEditor && array_key_exists('value', $_POST)){
        echo $configurations->validateServerPath($_POST['value']);
    }
    elseif($action === 'validateClientPath' && $isEditor && array_key_exists('value', $_POST)){
        echo $configurations->validateClientPath($_POST['value']);
    }
    elseif($action === 'saveMapServerConfig' && $isEditor && array_key_exists('data', $_POST)){
        echo $configurations->updateConfigurationValue('SPATIAL_LAYER_CONFIG_JSON', json_decode($_POST['data'], true));
    }
    elseif($action === 'uploadMapDataFile' && $isEditor && array_key_exists('addLayerFile', $_FILES)){
        echo $configurations->uploadMapDataFile();
    }
    elseif($action === 'deleteMapDataFile' && $isEditor && array_key_exists('filename', $_POST)){
        echo $configurations->deleteMapDataFile($_POST['filename']);
    }
}

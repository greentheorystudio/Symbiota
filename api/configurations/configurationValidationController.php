<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Configurations.php');
header('Content-Type: text/html; charset=UTF-8' );

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$value = array_key_exists('value',$_REQUEST)?$_REQUEST['value']:'';

$confManager = new Configurations();

if($GLOBALS['IS_ADMIN']){
    if($action === 'validateNameCore'){
        echo $confManager->validateNewConfNameCore($value);
    }
    elseif($action === 'validateNameExisting'){
        echo (int)$confManager->validateNewConfNameExisting($value);
    }
    elseif($action === 'validateServerWritePath'){
        echo $confManager->validatePathIsWritable($value);
    }
    elseif($action === 'validateServerPath'){
        echo $confManager->validateServerPath($value);
    }
    elseif($action === 'validateClientPath'){
        echo $confManager->validateClientPath($value);
    }
}

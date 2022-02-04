<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$name = array_key_exists('name',$_REQUEST)?$_REQUEST['name']:'';
$value = array_key_exists('value',$_REQUEST)?$_REQUEST['value']:'';

$confManager = new ConfigurationManager();

if($GLOBALS['IS_ADMIN']){
    if($action === 'add'){
        $confManager->addConfiguration($name,$value);
    }
    elseif($action === 'update'){
        $confManager->updateConfigurationValue($name,$value);
    }
    elseif($action === 'delete'){
        $confManager->deleteConfiguration($name);
    }
    elseif($action === 'updateCss'){
        $confManager->updateCssVersion();
    }
}

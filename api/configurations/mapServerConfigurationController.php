<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=UTF-8' );

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$jsonData = array_key_exists('data',$_REQUEST)?$_REQUEST['data']:'';
$fileName = array_key_exists('filename',$_REQUEST)?$_REQUEST['filename']:'';
if($jsonData){
    $jsonData = str_replace('%<amp>%', '&', $jsonData);
}
if($fileName){
    $fileName = str_replace('%<amp>%', '&', $fileName);
}

$confManager = new ConfigurationManager();

if($GLOBALS['IS_ADMIN']){
    if($action === 'saveMapServerConfig' && $jsonData){
        echo $confManager->saveMapServerConfig($jsonData);
    }
    elseif($action === 'uploadMapDataFile'){
        echo $confManager->uploadMapDataFile();
    }
    elseif($action === 'deleteMapDataFile'){
        echo $confManager->deleteMapDataFile($fileName);
    }
}

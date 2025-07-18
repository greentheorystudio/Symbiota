<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Configurations.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$jsonData = array_key_exists('data',$_REQUEST)?$_REQUEST['data']:'';

if(SanitizerService::validateInternalRequest()){
    $dataArr = array();
    $confManager = new Configurations();

    if($jsonData){
        $dataArr = json_decode($jsonData, true);
    }

    if($GLOBALS['IS_ADMIN']){
        if($action === 'add'){
            foreach($dataArr as $key => $value){
                if($key){
                    $confManager->addConfiguration($key,$value);
                }
            }
        }
        elseif($action === 'update'){
            foreach($dataArr as $key => $value){
                if($key){
                    $confManager->updateConfigurationValue($key,$value);
                }
            }
        }
        elseif($action === 'delete'){
            foreach($dataArr as $key => $value){
                if($key){
                    $confManager->deleteConfiguration($key);
                }
            }
        }
        elseif($action === 'updateCss'){
            $confManager->updateCssVersion();
        }
    }
}

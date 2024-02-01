<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && Sanitizer::validateInternalRequest()){
    $spatialManager = new SpatialModuleManager();
    if($action === 'getLayersConfiguration'){
        echo $spatialManager->getLayersConfigJSON();
    }
}

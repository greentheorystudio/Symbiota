<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(SanitizerService::validateInternalRequest()){
    $spatialManager = new SpatialModuleManager();
    echo $GLOBALS['SPATIAL_LAYER_CONFIG_JSON'];
}

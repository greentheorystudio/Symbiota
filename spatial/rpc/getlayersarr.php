<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');

$spatialManager = new SpatialModuleManager();
$layersArr = array();
if(isset($GLOBALS['GEOSERVER_URL'], $GLOBALS['GEOSERVER_LAYER_WORKSPACE']) && $GLOBALS['GEOSERVER_URL'] && $GLOBALS['GEOSERVER_LAYER_WORKSPACE']){
    $layersArr = $spatialManager->getLayersArr();
}
echo json_encode($layersArr);

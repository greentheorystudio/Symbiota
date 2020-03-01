<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');

$spatialManager = new SpatialModuleManager();
$layersArr = array();
if(isset($GEOSERVER_URL, $GEOSERVER_LAYER_WORKSPACE) && $GEOSERVER_URL && $GEOSERVER_LAYER_WORKSPACE){
    $layersArr = $spatialManager->getLayersArr();
}
echo json_encode($layersArr);

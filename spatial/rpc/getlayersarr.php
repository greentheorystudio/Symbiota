<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpatialModuleManager.php');

$spatialManager = new SpatialModuleManager();
$layersArr = array();
if(isset($GEOSERVER_URL, $GEOSERVER_LAYER_WORKSPACE) && $GEOSERVER_URL && $GEOSERVER_LAYER_WORKSPACE){
    $layersArr = $spatialManager->getLayersArr();
}
echo json_encode($layersArr);

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');

$spatialManager = new SpatialModuleManager();
echo $spatialManager->getLayersConfigJSON();

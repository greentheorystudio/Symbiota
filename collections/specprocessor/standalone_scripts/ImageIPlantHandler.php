<?php
require_once(__DIR__ . '/../../../classes/ImageProcessor.php');

$imageProcessor = new ImageProcessor();

$imageProcessor->initProcessor();
$imageProcessor->processIPlantImages('', array());

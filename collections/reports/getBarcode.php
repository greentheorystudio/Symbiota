<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
header('Content-type: image/png');

$labelManager = new OccurrenceLabel();

$bcText = array_key_exists('bctext',$_REQUEST)?$_REQUEST['bctext']:'';
$bcCode = array_key_exists('bccode',$_REQUEST)?$_REQUEST['bccode']:'Code39';
$imgType = array_key_exists('imgtype',$_REQUEST)?$_REQUEST['imgtype']:'png';
$bcHeight = array_key_exists('bcheight',$_REQUEST)?(int)$_REQUEST['bcheight']:50;

if($bcText){
    $bcText = strtoupper($bcText);
    $bc = $labelManager->getBarcodePng($bcText, $bcHeight, 'code39');
    imagepng($bc);
    imagedestroy($bc);
}

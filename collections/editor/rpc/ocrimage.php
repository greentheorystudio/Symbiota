<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/SpecProcessorOcr.php');

$imgid = (int)$_REQUEST['imgid'];
$x = array_key_exists('x',$_REQUEST)?(int)$_REQUEST['x']:0;
$y = array_key_exists('y',$_REQUEST)?(int)$_REQUEST['y']:0;
$w = array_key_exists('w',$_REQUEST)?(int)$_REQUEST['w']:1;
$h = array_key_exists('h',$_REQUEST)?(int)$_REQUEST['h']:1;
$ocrBest = array_key_exists('ocrbest',$_REQUEST)?(int)$_REQUEST['ocrbest']:0;

$rawStr = '';
$ocrManager = new SpecProcessorOcr();
$ocrManager->setCropX($x);
$ocrManager->setCropY($y);
$ocrManager->setCropW($w);
$ocrManager->setCropH($h);
$rawStr = $ocrManager->ocrImageById($imgid,$ocrBest);

echo $rawStr;

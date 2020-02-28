<?php
include_once(__DIR__ . '/../../classes/SpecProcessorOcr.php');

$silent = 1;
$collStr = '';
if(array_key_exists(1,$argv)){
	$collStr = $argv[1];
} 
if(array_key_exists(2,$argv)){
	$silect = $argv[2];
} 

$ocrManager = new SpecProcessorOcr();
$ocrManager->batchOcrUnprocessed($collStr);

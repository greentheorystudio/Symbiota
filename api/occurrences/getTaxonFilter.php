<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceAttributes.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: application/json; charset=UTF-8' );

$exact = (isset($_REQUEST['exact']) && $_REQUEST['exact']);

if(SanitizerService::validateInternalRequest()){
    $attrManager = new OccurrenceAttributes();
    echo $attrManager->getTaxonFilterSuggest($_REQUEST['term'],$exact);
}

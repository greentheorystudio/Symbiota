<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDataset.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$term = $_REQUEST['term'];

if(SanitizerService::validateInternalRequest()){
    $datasetManager = new OccurrenceDataset();
    $retArr = $datasetManager->getUserList($term);

    echo json_encode($retArr);
}

<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/OccurrenceDataset.php');

$term = $_REQUEST['term'];

$datasetManager = new OccurrenceDataset();
$retArr = $datasetManager->getUserList($term);

echo json_encode($retArr);

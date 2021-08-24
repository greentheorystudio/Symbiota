<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/TaxonomyCleaner.php');

$term = $_REQUEST['term'];

$searchManager = new TaxonomyCleaner();
$nameArr = $searchManager->getTaxaSuggest($_REQUEST['term']);

echo json_encode($nameArr, JSON_THROW_ON_ERROR);

<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/OccurrenceCollectionProfile.php');

$collManager = new OccurrenceCollectionProfile();

$GBIFInstKey = $collManager->getGbifInstKey();

echo $GBIFInstKey;

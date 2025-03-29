<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(SanitizerService::validateInternalRequest()){
    $collManager = new OccurrenceCollectionProfile();

    $GBIFInstKey = $collManager->getGbifInstKey();

    echo $GBIFInstKey;
}

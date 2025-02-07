<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../services/SearchService.php');

$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';
$options = array_key_exists('options', $_POST) ? json_decode($_POST['options'], true) : null;
$stArr = array_key_exists('starr', $_POST) ? json_decode($_POST['starr'], true) : null;

if($action && $options && $stArr && SanitizerService::validateInternalRequest()){
    $searchService = new SearchService();
    if($action === 'getSearchOccidArr'){
        echo json_encode($searchService->getSearchOccidArr($stArr, $options));
    }
    elseif($action === 'processSearch'){
        echo json_encode($searchService->processSearch($stArr, $options));
    }
    elseif($action === 'processSearchDownload'){
        if($options['type'] === 'geojson' || $options['type'] === 'gpx' || $options['type'] === 'kml'){
            echo $searchService->processSearchSpatialDownload($stArr, $options);
        }
        else{
            $searchService->processSearchDownload($stArr, $options);
        }
    }
}

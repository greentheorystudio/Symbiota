<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../services/SearchService.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';
$options = array_key_exists('options', $_POST) ? json_decode($_POST['options'], true) : null;
$stArr = array_key_exists('starr', $_POST) ? json_decode($_POST['starr'], true) : null;

if($action && $options && $stArr && SanitizerService::validateInternalRequest()){
    $searchService = new SearchService();
    if($action === 'getSearchOccidArr'){
        $index = array_key_exists('index', $_POST) ? (int)$_POST['index'] : null;
        $recCnt = (array_key_exists('reccnt', $_POST) && (int)$_POST['reccnt'] > 0) ? (int)$_POST['reccnt'] : null;
        echo json_encode($searchService->getSearchOccidArr($stArr, $options, $index, $recCnt));
    }
    elseif($action === 'getSearchTidArr'){
        echo json_encode($searchService->getSearchTidArr($stArr, $options));
    }
    elseif($action === 'processSearch'){
        echo json_encode($searchService->processSearch($stArr, $options));
    }
    elseif($action === 'processSearchDownload'){
        if($options['type'] === 'geojson' || $options['type'] === 'gpx' || $options['type'] === 'kml'){
            $options['spatial'] = 1;
        }
        $searchService->processSearchDownload($stArr, $options);
    }
}

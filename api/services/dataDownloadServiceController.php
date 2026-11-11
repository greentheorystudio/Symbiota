<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DataDownloadService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';
$options = array_key_exists('options', $_POST) ? json_decode($_POST['options'], true) : null;
$stArr = array_key_exists('starr', $_POST) ? json_decode($_POST['starr'], true) : null;

if($action && $options && $stArr && SanitizerService::validateInternalRequest()){
    $dataDownloadService = new DataDownloadService();
    if($action === 'processSearchDownload'){
        if($options['type'] === 'geojson' || $options['type'] === 'gpx' || $options['type'] === 'kml'){
            $options['spatial'] = 1;
        }
        $dataDownloadService->processSearchDownload($stArr, $options);
    }
}

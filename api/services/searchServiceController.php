<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../services/SearchService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$options = array_key_exists('options', $_REQUEST) ? json_decode($_POST['options'], true) : null;
$stArr = array_key_exists('starr', $_REQUEST) ? json_decode($_POST['starr'], true) : null;

if($action && $options && $stArr && SanitizerService::validateInternalRequest()){
    $searchService = new SearchService();
    if($action === 'getSearchRecCnt('){
        echo $searchService->getSearchRecordCnt($stArr, $options);
    }
    elseif($action === 'processSearch'){
        echo json_encode($searchService->processSearch($stArr, $options));
    }
}
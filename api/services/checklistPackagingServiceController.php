<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/ChecklistPackagingService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';
$filename = array_key_exists('filename', $_POST) ? $_POST['filename'] : '';
$options = array_key_exists('options', $_POST) ? json_decode($_POST['options'], true) : null;
$clidArr = array_key_exists('clidArr', $_POST) ? json_decode($_POST['clidArr'], false) : null;
$clid = array_key_exists('clid', $_POST) ? (int)$_POST['clid'] : 0;

if($action && $filename && $options && $clidArr && SanitizerService::validateInternalRequest()){
    $checklistPackagingService = new ChecklistPackagingService();
    if($action === 'processCsvDownload'){
        $checklistPackagingService->processCsvDownload($clidArr, $options, $filename);
    }
    elseif($action === 'processDocxDownload'){
        $checklistPackagingService->processDocxDownload($clidArr, $clid, $options, $filename);
    }
}

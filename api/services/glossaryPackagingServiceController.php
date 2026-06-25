<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/GlossaryPackagingService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$filename = array_key_exists('filename', $_POST) ? $_POST['filename'] : '';
$options = array_key_exists('options', $_POST) ? json_decode($_POST['options'], true) : null;
$glossidArr = array_key_exists('glossidArr', $_POST) ? json_decode($_POST['glossidArr'], false) : null;

if($action){
    $glossaryPackagingService = new GlossaryPackagingService();
    if(SanitizerService::validateInternalRequest()){
        if($action === 'processDocxDownload' && $filename && $options && $glossidArr){
            $glossaryPackagingService->processDocxDownload($glossidArr, $options, $filename);
        }
    }
}

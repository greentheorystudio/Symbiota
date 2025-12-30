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

if($action && SanitizerService::validateInternalRequest()){
    $checklistPackagingService = new ChecklistPackagingService();
    if($action === 'processCsvDownload' && $filename && $options && $clidArr){
        $checklistPackagingService->processCsvDownload($clidArr, $options, $filename);
    }
    elseif($action === 'processDocxDownload' && $filename && $options && $clidArr && $clid){
        $checklistPackagingService->processDocxDownload($clidArr, $clid, $options, $filename);
    }
    elseif($action === 'deleteAppDataArchive' && $clid){
        echo $checklistPackagingService->deleteAppDataArchive($clid);
    }
    elseif($action === 'initializeAppDataArchive' && $clid){
        echo $checklistPackagingService->initializeAppDataArchive($clid);
    }
    elseif($action === 'packageChecklistTaggedImages' && $clidArr && array_key_exists('tidArr', $_POST) && array_key_exists('archiveFile', $_POST)){
        $tidArr = json_decode($_POST['tidArr'], false);
        echo json_encode($checklistPackagingService->packageChecklistTaggedImages($clidArr, $tidArr, $_POST['archiveFile']));
    }
}

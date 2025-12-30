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
    elseif($action === 'processCompletedImageDataPackaging' && array_key_exists('archiveFile', $_POST)){
        echo json_encode($checklistPackagingService->processCompletedImageDataPackaging($_POST['archiveFile']));
    }
    elseif($action === 'packageChecklistImages' && array_key_exists('tidArr', $_POST) && array_key_exists('imageMaxCnt', $_POST) && array_key_exists('archiveFile', $_POST)){
        $tidArr = json_decode($_POST['tidArr'], false);
        echo json_encode($checklistPackagingService->packageChecklistImages($tidArr, $_POST['imageMaxCnt'], $_POST['archiveFile']));
    }
    elseif($action === 'packageChecklistTaxaData' && $clidArr && array_key_exists('index', $_POST) && array_key_exists('reccnt', $_POST) && array_key_exists('archiveFile', $_POST)){
        $descTab = $_POST['descTab'] ?? null;
        echo json_encode($checklistPackagingService->packageChecklistTaxaData($clidArr, $_POST['index'], $_POST['reccnt'], $descTab, $_POST['archiveFile']));
    }
    elseif($action === 'processCompletedTaxaDataPackaging' && array_key_exists('archiveFile', $_POST)){
        echo json_encode($checklistPackagingService->processCompletedTaxaDataPackaging($_POST['archiveFile']));
    }
    elseif($action === 'processCompletedDataPackaging' && array_key_exists('archiveFile', $_POST)){
        echo json_encode($checklistPackagingService->processCompletedDataPackaging($_POST['archiveFile']));
    }
    elseif($action === 'packageChecklistCharacterData' && array_key_exists('csidArr', $_POST) && array_key_exists('archiveFile', $_POST)){
        $csidArr = json_decode($_POST['csidArr'], false);
        echo $checklistPackagingService->packageChecklistCharacterData($csidArr, $_POST['archiveFile']);
    }
}

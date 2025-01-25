<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DataUploadService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
ini_set('max_execution_time', 3600);

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && $isEditor && SanitizerService::validateInternalRequest()){
    $dataUploadService = new DataUploadService();
    if($action === 'getUploadTableFieldData' && array_key_exists('tableArr', $_POST)){
        echo json_encode($dataUploadService->getUploadTableFieldData(json_decode($_POST['tableArr'], false)));
    }
    elseif($action === 'processExternalDwcaTransfer' && array_key_exists('uploadType', $_POST) && array_key_exists('dwcaPath', $_POST)){
        echo json_encode($dataUploadService->processExternalDwcaTransfer($collid, $_POST['uploadType'], $_POST['dwcaPath']));
    }
    elseif($action === 'processExternalDwcaUnpack' && array_key_exists('targetPath', $_POST) && array_key_exists('archivePath', $_POST)){
        echo json_encode($dataUploadService->processExternalDwcaUnpack($_POST['targetPath'], $_POST['archivePath']));
    }
    elseif($action === 'processTransferredDwca' && array_key_exists('serverPath', $_POST) && array_key_exists('metaFile', $_POST)){
        echo json_encode($dataUploadService->processTransferredDwca($_POST['serverPath'], $_POST['metaFile']));
    }
    elseif($action === 'uploadDwcaFile' && array_key_exists('dwcaFile', $_FILES)){
        echo json_encode($dataUploadService->uploadDwcaFile($collid, $_FILES['dwcaFile']));
    }
    elseif($action === 'clearOccurrenceUploadTables'){
        echo $dataUploadService->clearOccurrenceUploadTables($collid);
    }
    elseif($action === 'processDwcaFileDataUpload' && array_key_exists('uploadConfig', $_POST)){
        echo $dataUploadService->processDwcaFileDataUpload($collid, json_decode($_POST['uploadConfig'], true));
    }
    elseif($action === 'processFlatFileDataUpload' && array_key_exists('uploadConfig', $_POST) && array_key_exists('data', $_POST)){
        echo $dataUploadService->processFlatFileDataUpload($collid, json_decode($_POST['uploadConfig'], true), json_decode($_POST['data'], true));
    }
    elseif($action === 'getUploadedMofDataFields'){
        echo json_encode($dataUploadService->getUploadedMofDataFields($collid));
    }
    elseif($action === 'removeExistingOccurrencesFromUpload'){
        echo $dataUploadService->removeExistingOccurrencesFromUpload($collid);
    }
    elseif($action === 'linkExistingOccurrencesToUpload'){
        $matchByCatalogNumber = array_key_exists('matchByCatalogNumber',$_POST) && (int)$_POST['matchByCatalogNumber'] === 1;
        $linkField = $_POST['linkField'] ?? null;
        echo $dataUploadService->linkExistingOccurrencesToUpload($collid, $matchByCatalogNumber, $linkField);
    }
    elseif($action === 'executeCleaningScriptArr' && array_key_exists('cleaningScriptArr', $_POST)){
        echo $dataUploadService->executeCleaningScriptArr($collid, json_decode($_POST['cleaningScriptArr'], true));
    }
    elseif($action === 'cleanUploadEventDates'){
        echo $dataUploadService->cleanUploadEventDates($collid);
    }
    elseif($action === 'cleanUploadCountryStateNames'){
        echo $dataUploadService->cleanUploadCountryStateNames($collid);
    }
    elseif($action === 'cleanUploadCoordinates'){
        echo $dataUploadService->cleanUploadCoordinates($collid);
    }
    elseif($action === 'cleanUploadTaxonomy'){
        echo $dataUploadService->cleanUploadTaxonomy($collid);
    }
    elseif($action === 'getUploadSummary'){
        echo json_encode($dataUploadService->getUploadSummary($collid));
    }
    elseif($action === 'getUploadData' && array_key_exists('dataType', $_POST)){
        $index = array_key_exists('index', $_POST) ? (int)$_POST['index'] : null;
        $limit = array_key_exists('limit', $_POST) ? (int)$_POST['limit'] : null;
        echo json_encode($dataUploadService->getUploadData($collid, $_POST['dataType'], $index, $limit));
    }
    elseif($action === 'processUploadDataDownload' && array_key_exists('filename', $_POST) && array_key_exists('dataType', $_POST)){
        $dataUploadService->processUploadDataDownload($collid, $_POST['filename'], $_POST['dataType']);
    }
}

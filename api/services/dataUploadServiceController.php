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
        echo json_encode($dataUploadService->processExternalDwcaTransfer($_POST['uploadType'], $_POST['dwcaPath']));
    }
    elseif($action === 'processExternalDwcaUnpack' && array_key_exists('targetPath', $_POST) && array_key_exists('archivePath', $_POST)){
        echo json_encode($dataUploadService->processExternalDwcaUnpack($_POST['targetPath'], $_POST['archivePath']));
    }
    elseif($action === 'processTransferredDwca' && array_key_exists('serverPath', $_POST) && array_key_exists('metaFile', $_POST)){
        echo json_encode($dataUploadService->processTransferredDwca($_POST['serverPath'], $_POST['metaFile']));
    }
    elseif($action === 'uploadDwcaFile' && array_key_exists('dwcaFile', $_FILES) && strtolower(substr($_FILES['dwcaFile']['name'], -4)) === '.zip'){
        echo json_encode($dataUploadService->uploadDwcaFile($_FILES['dwcaFile']));
    }
    elseif($action === 'clearOccurrenceUploadTables'){
        $optimizeTables = array_key_exists('optimizeTables', $_POST) && (int)$_POST['optimizeTables'] === 1;
        echo $dataUploadService->clearOccurrenceUploadTables($collid, $optimizeTables);
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
        $updateAssociatedData = array_key_exists('updateAssociatedData', $_POST) && (int)$_POST['updateAssociatedData'] === 1;
        $matchByCatalogNumber = array_key_exists('matchByCatalogNumber', $_POST) && (int)$_POST['matchByCatalogNumber'] === 1;
        $matchByRecordId = array_key_exists('matchByRecordId', $_POST) && (int)$_POST['matchByRecordId'] === 1;
        $linkField = $_POST['linkField'] ?? null;
        echo $dataUploadService->linkExistingOccurrencesToUpload($collid, $updateAssociatedData, $matchByRecordId, $matchByCatalogNumber, $linkField);
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
    elseif($action === 'finalTransferUpdateExistingOccurrences' && array_key_exists('mappedFields', $_POST)){
        echo $dataUploadService->finalTransferUpdateExistingOccurrences($collid, json_decode($_POST['mappedFields'], false));
    }
    elseif($action === 'finalTransferRemoveUnmatchedOccurrences'){
        echo $dataUploadService->finalTransferRemoveUnmatchedOccurrences($collid);
    }
    elseif($action === 'finalTransferAddNewOccurrences'){
        echo $dataUploadService->finalTransferAddNewOccurrences($collid);
    }
    elseif($action === 'finalTransferSetNewOccurrenceIds'){
        echo $dataUploadService->finalTransferSetNewOccurrenceIds($collid);
    }
    elseif($action === 'finalTransferClearPreviousDeterminations'){
        echo $dataUploadService->finalTransferClearPreviousDeterminations($collid);
    }
    elseif($action === 'finalTransferRemoveExistingDeterminationsFromUpload'){
        echo $dataUploadService->finalTransferRemoveExistingDeterminationsFromUpload($collid);
    }
    elseif($action === 'finalTransferAddNewDeterminations'){
        echo $dataUploadService->finalTransferAddNewDeterminations($collid);
    }
    elseif($action === 'finalTransferCleanMediaRecords'){
        echo $dataUploadService->finalTransferCleanMediaRecords($collid);
    }
    elseif($action === 'finalTransferRemoveExistingMediaRecordsFromUpload'){
        echo $dataUploadService->finalTransferRemoveExistingMediaRecordsFromUpload($collid);
    }
    elseif($action === 'finalTransferClearPreviousMediaRecords'){
        echo $dataUploadService->finalTransferClearPreviousMediaRecords($collid);
    }
    elseif($action === 'finalTransferAddNewMedia'){
        echo $dataUploadService->finalTransferAddNewMedia($collid);
    }
    elseif($action === 'finalTransferPopulateMofIdentifiers'){
        $eventMofDataFields = array_key_exists('eventMofDataFields', $_POST) ? json_decode($_POST['eventMofDataFields'], false) : array();
        $occurrenceMofDataFields = array_key_exists('occurrenceMofDataFields', $_POST) ? json_decode($_POST['occurrenceMofDataFields'], false) : array();
        echo $dataUploadService->finalTransferPopulateMofIdentifiers($collid, $eventMofDataFields, $occurrenceMofDataFields);
    }
    elseif($action === 'finalTransferRemoveExistingMofRecordsFromUpload'){
        echo $dataUploadService->finalTransferRemoveExistingMofRecordsFromUpload($collid);
    }
    elseif($action === 'finalTransferClearPreviousMofRecords'){
        echo $dataUploadService->finalTransferClearPreviousMofRecords($collid);
    }
    elseif($action === 'finalTransferClearPreviousMofRecordsForUpload'){
        echo $dataUploadService->finalTransferClearPreviousMofRecordsForUpload($collid);
    }
    elseif($action === 'finalTransferAddNewMof'){
        echo $dataUploadService->finalTransferAddNewMof($collid);
    }
    elseif($action === 'removeUploadFiles' && array_key_exists('serverPath', $_POST)){
        echo $dataUploadService->removeUploadFiles($_POST['serverPath']);
    }
    elseif($action === 'setUploadLocalitySecurity'){
        echo $dataUploadService->setUploadLocalitySecurity($collid);
    }
    elseif($action === 'removePrimaryIdentifiersFromUploadedOccurrences'){
        echo $dataUploadService->removePrimaryIdentifiersFromUploadedOccurrences($collid);
    }
    elseif($action === 'finalTransferRemoveDuplicateDbpkRecordsFromUpload'){
        echo $dataUploadService->finalTransferRemoveDuplicateDbpkRecordsFromUpload($collid);
    }
    elseif($action === 'finalTransferClearExistingMediaNotInUpload'){
        $clearDerivatives = array_key_exists('clearImageDerivatives',$_POST) && (int)$_POST['clearImageDerivatives'] === 1;
        echo $dataUploadService->finalTransferClearExistingMediaNotInUpload($collid, $clearDerivatives);
    }
    elseif($action === 'requestGbifDataDownload' && array_key_exists('predicateJson', $_POST)){
        echo $dataUploadService->requestGbifDataDownload(json_decode($_POST['predicateJson'], true));
    }
}

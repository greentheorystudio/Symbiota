<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DataUploadService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && $isEditor && SanitizerService::validateInternalRequest()){
    $dataUploadService = new DataUploadService();
    if($action === 'getUploadTableFieldData' && array_key_exists('tableArr',$_POST)){
        echo json_encode($dataUploadService->getUploadTableFieldData(json_decode($_POST['tableArr'], false)));
    }
    elseif($action === 'processExternalDwcaTransfer' && array_key_exists('uploadType',$_POST) && array_key_exists('dwcaPath',$_POST)){
        echo json_encode($dataUploadService->processExternalDwcaTransfer($collid, $_POST['uploadType'], $_POST['dwcaPath']));
    }
    elseif($action === 'processTransferredDwca' && array_key_exists('serverPath',$_POST) && array_key_exists('metaFile',$_POST)){
        echo json_encode($dataUploadService->processTransferredDwca($_POST['serverPath'], $_POST['metaFile']));
    }
}

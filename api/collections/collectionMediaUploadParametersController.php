<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/CollectionMediaUploadParameters.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$spprid = array_key_exists('spprid', $_REQUEST) ? (int)$_REQUEST['spprid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && $isEditor && SanitizerService::validateInternalRequest()){
    $collectionMediaUploadParameters = new CollectionMediaUploadParameters();
    if($action === 'getCollectionMediaUploadParametersByCollection'){
        echo json_encode($collectionMediaUploadParameters->getCollectionMediaUploadParametersByCollection($collid));
    }
    elseif($action === 'createCollectionMediaUploadParameterRecord' && array_key_exists('uploadParams',$_POST)){
        echo $collectionMediaUploadParameters->createCollectionMediaUploadParameterRecord(json_decode($_POST['uploadParams'], true));
    }
    elseif($action === 'updateCollectionMediaUploadParameterRecord' && $spprid){
        echo $collectionMediaUploadParameters->updateCollectionMediaUploadParameterRecord($spprid, json_decode($_POST['paramsData'], true));
    }
    elseif($action === 'deleteCollectionMediaUploadParameterRecord' && $spprid){
        echo $collectionMediaUploadParameters->deleteCollectionMediaUploadParameterRecord($spprid);
    }
}

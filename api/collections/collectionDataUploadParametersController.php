<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/CollectionDataUploadParameters.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$uspid = array_key_exists('uspid', $_REQUEST) ? (int)$_REQUEST['uspid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($action && $isEditor && SanitizerService::validateInternalRequest()){
    $collectionDataUploadParameters = new CollectionDataUploadParameters();
    if($action === 'getCollectionDataUploadParametersByCollection'){
        echo json_encode($collectionDataUploadParameters->getCollectionDataUploadParametersByCollection($collid));
    }
    elseif($action === 'createCollectionDataUploadParameterRecord' && array_key_exists('uploadParams',$_POST)){
        echo $collectionDataUploadParameters->createCollectionDataUploadParameterRecord(json_decode($_POST['uploadParams'], true));
    }
    elseif($action === 'updateCollectionDataUploadParameterRecord' && $uspid){
        echo $collectionDataUploadParameters->updateCollectionDataUploadParameterRecord($uspid, json_decode($_POST['paramsData'], true));
    }
    elseif($action === 'deleteCollectionDataUploadParameterRecord' && $uspid){
        echo $collectionDataUploadParameters->deleteCollectionDataUploadParameterRecord($uspid);
    }
    elseif($action === 'getUploadParametersFieldMapping' && $uspid){
        echo json_encode($collectionDataUploadParameters->getUploadParametersFieldMapping($uspid));
    }
}

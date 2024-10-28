<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/CollectionCategories.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$ccpk = array_key_exists('ccpk',$_REQUEST) ? (int)$_REQUEST['ccpk'] : 0;
$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $collectionCategories = new CollectionCategories();
    if($action === 'getCollectionCategoryArr'){
        echo json_encode($collectionCategories->getCollectionCategoryArr());
    }
}

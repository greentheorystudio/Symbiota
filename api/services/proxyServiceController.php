<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/ProxyService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && SanitizerService::validateInternalRequest()){
    if($action === 'getExternalData' && array_key_exists('url', $_POST) && array_key_exists('requestType', $_POST)){
        $postData = array_key_exists('postdata',$_POST) ? json_decode($_POST['postdata'], true) : null;
        echo ProxyService::getExternalData($_POST['url'], $_POST['requestType'], $postData);
    }
    elseif($action === 'getFileInfoFromUrl' && array_key_exists('url', $_POST)){
        $imageFile = array_key_exists('image',$_POST) && (int)$_POST['image'] === 1;
        echo json_encode(ProxyService::getFileInfoFromUrl($_POST['url'], $imageFile));
    }
    elseif($action === 'getFileContentsFromUrl' && array_key_exists('url', $_POST)){
        echo ProxyService::getFileContentsFromUrl($_POST['url']);
    }
}

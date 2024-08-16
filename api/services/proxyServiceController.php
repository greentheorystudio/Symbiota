<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/ProxyService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

if($action && SanitizerService::validateInternalRequest()){
    $proxyService = new ProxyService();
    if($action === 'getExternalData' && array_key_exists('url', $_POST) && array_key_exists('requestType', $_POST)){
        $postData = array_key_exists('postdata',$_POST) ? json_decode($_POST['postdata'], true) : null;
        echo $proxyService::getExternalData($_POST['url'], $_POST['requestType'], $postData);
    }
    elseif($action === 'getFileInfoFromUrl' && array_key_exists('url', $_POST)){
        $imageFile = array_key_exists('image',$_POST) && (int)$_POST['image'] === 1;
        echo json_encode($proxyService::getFileInfoFromUrl($_POST['url'], $imageFile));
    }
}

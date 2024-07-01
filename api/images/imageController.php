<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ImageShared.php');
include_once(__DIR__ . '/../../models/Images.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $images = new Images();
    if($action === 'addImageRecord' && $isEditor && array_key_exists('image',$_POST)){
        $imgUtilities = new ImageShared();
        echo json_encode($imgUtilities->addImageRecord(json_decode($_POST['image'], true)));
    }
    elseif($action === 'getImageArrByProperty' && array_key_exists('property',$_POST) && array_key_exists('value',$_POST)){
        echo json_encode($images->getImageArrByProperty($_POST['property'], $_POST['value']));
    }
}

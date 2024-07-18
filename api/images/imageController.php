<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ImageShared.php');
include_once(__DIR__ . '/../../models/Images.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}
elseif(array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
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
    elseif($action === 'addImage' && $isEditor && array_key_exists('image',$_POST)){
        $importExternalFiles = array_key_exists('copyToServer',$_POST) && (int)$_POST['copyToServer'] === 1;

        echo $images->getImageArrByProperty($_POST['property'], $_POST['value']);
    }
}

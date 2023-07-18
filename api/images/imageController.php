<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ImageShared.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($isEditor && $action){
    $imgUtilities = new ImageShared();
    if($action === 'addImageRecord' && array_key_exists('image',$_POST)){
        echo json_encode($imgUtilities->addImageRecord(json_decode($_POST['image'], true)));
    }
}

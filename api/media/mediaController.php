<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/MediaShared.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($isEditor && $action){
    $medUtilities = new MediaShared();
    if($action === 'addMediaRecord' && array_key_exists('media',$_POST)){
        echo json_encode($medUtilities->addMediaRecord(json_decode($_POST['media'], true)));
    }
}

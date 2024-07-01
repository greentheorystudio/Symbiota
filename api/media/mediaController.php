<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/MediaShared.php');
include_once(__DIR__ . '/../../models/Media.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $media = new Media();
    if($action === 'addMediaRecord' && $isEditor && array_key_exists('media',$_POST)){
        $medUtilities = new MediaShared();
        echo json_encode($medUtilities->addMediaRecord(json_decode($_POST['media'], true)));
    }
    elseif($action === 'getMediaArrByProperty' && array_key_exists('property',$_POST) && array_key_exists('value',$_POST)){
        $limitFormat = $_POST['limitFormat'] ?? null;
        echo json_encode($media->getMediaArrByProperty($_POST['property'], $_POST['value'], $limitFormat));
    }
}

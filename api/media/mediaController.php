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
    elseif($action === 'getTaxonVideos' && $tId){
        echo json_encode($media->getTaxonVideos($tId));
    }
    elseif($action === 'getTaxonAudios' && $tId){
        echo json_encode($media->getTaxonAudios($tId));
    }
}

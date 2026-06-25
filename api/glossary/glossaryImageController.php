<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/GlossaryImages.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$glimgid = array_key_exists('glimgid', $_REQUEST) ? (int)$_REQUEST['glimgid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $glossaryImages = new GlossaryImages();
    if($action === 'deleteGlossaryImageRecord' && $isEditor && $glimgid){
        echo $glossaryImages->deleteGlossaryImageRecord($glimgid);
    }
    elseif($action === 'createGlossaryImageRecord' && $isEditor && array_key_exists('glossaryImage', $_POST)){
        echo $glossaryImages->createGlossaryImageRecord(json_decode($_POST['glossaryImage'], true));
    }
    elseif($action === 'updateGlossaryImageRecord' && $isEditor && $glimgid && array_key_exists('glossaryImageData', $_POST)){
        echo $glossaryImages->updateGlossaryImageRecord($glimgid, json_decode($_POST['glossaryImageData'], true));
    }
    elseif($action === 'getGlossaryImageDataFromGlossidArr' && array_key_exists('glossIdArr', $_POST)){
        echo json_encode($glossaryImages->getGlossaryImageDataFromGlossidArr(json_decode($_POST['glossIdArr'], false)));
    }
    elseif($action === 'createGlossaryImageRecordFromFile' && $isEditor && array_key_exists('imageFile', $_FILES)  && array_key_exists('imageData', $_POST)){
        echo $glossaryImages->createGlossaryImageRecord($_FILES['imageFile'], null, json_decode($_POST['imageData'], true));
    }
    elseif($action === 'createGlossaryImageRecordFromUrl' && $isEditor && array_key_exists('imageUrl', $_POST) && array_key_exists('imageData', $_POST)){
        echo $glossaryImages->createGlossaryImageRecord(null, $_POST['imageUrl'], json_decode($_POST['imageData'], true));
    }
}

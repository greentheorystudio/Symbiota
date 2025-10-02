<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Images.php');
include_once(__DIR__ . '/../../services/FileSystemService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$collid = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$imgid = array_key_exists('imgid',$_REQUEST) ? (int)$_REQUEST['imgid'] : 0;

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
elseif(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $images = new Images();
    if($action === 'addImage' && $isEditor && array_key_exists('image',$_POST)){
        echo $images->createImageRecord(json_decode($_POST['image'], true));
    }
    elseif($action === 'getImageArrByTaxonomicGroup' && array_key_exists('parenttid',$_POST)){
        $includeOccurrence = array_key_exists('includeoccurrence',$_POST) && (int)$_POST['includeoccurrence'] === 1;
        $limit = array_key_exists('limit',$_POST) ? (int)$_POST['limit'] : null;
        echo json_encode($images->getImageArrByTaxonomicGroup((int)$_POST['parenttid'], $includeOccurrence, $limit));
    }
    elseif($action === 'getImageArrByProperty' && array_key_exists('property',$_POST) && array_key_exists('value',$_POST)){
        $limit = array_key_exists('limit',$_POST) ? (int)$_POST['limit'] : null;
        echo json_encode($images->getImageArrByProperty($_POST['property'], $_POST['value'], $limit));
    }
    elseif(($action === 'addImageFromFile' || $action === 'addImageFromUrl') && $isEditor && array_key_exists('image',$_POST) && array_key_exists('uploadpath',$_POST)){
        $imageData = json_decode($_POST['image'], true);
        if($action === 'addImageFromFile'){
            $imageData = FileSystemService::processUploadImageFromFile($imageData, $_POST['uploadpath']);
        }
        elseif($action === 'addImageFromUrl'){
            $imageData = FileSystemService::processUploadImageFromExternalUrl($imageData, $_POST['uploadpath']);
        }
        echo $imageData ? $images->createImageRecord($imageData) : 0;
    }
    elseif($action === 'deleteImageRecord' && $imgid && $isEditor){
        echo $images->deleteImageRecord($imgid);
    }
    elseif($action === 'getImageData' && $imgid){
        echo json_encode($images->getImageData($imgid));
    }
    elseif($action === 'updateImageRecord' && $imgid && $isEditor && array_key_exists('imageData', $_POST)){
        echo $images->updateImageRecord($imgid, json_decode($_POST['imageData'], true));
    }
    elseif($action === 'deleteImageTag' && $imgid && $isEditor && array_key_exists('tag', $_POST)){
        echo $images->deleteImageTag($imgid, $_POST['tag']);
    }
    elseif($action === 'batchPopulateOccurrenceImageGUIDs' && $collid){
        echo $images->batchCreateOccurrenceImageRecordGUIDs($collid);
    }
    elseif($action === 'getTaxonArrDisplayImageData' && array_key_exists('tidArr', $_POST)){
        $includeOccurrence = array_key_exists('includeoccurrence',$_POST) && (int)$_POST['includeoccurrence'] === 1;
        $limitToOccurrence = array_key_exists('limittooccurrence',$_POST) && (int)$_POST['limittooccurrence'] === 1;
        $limitPerTaxon = array_key_exists('limitPerTaxon',$_POST) ? (int)$_POST['limitPerTaxon'] : null;
        $sortsequenceLimit = array_key_exists('sortsequenceLimit',$_POST) ? (int)$_POST['sortsequenceLimit'] : null;
        echo json_encode($images->getTaxonArrDisplayImageData(json_decode($_POST['tidArr'], true), $includeOccurrence, $limitToOccurrence, $limitPerTaxon, $sortsequenceLimit));
    }
    elseif($action === 'getChecklistTaggedImageData' && array_key_exists('clidArr', $_POST) && array_key_exists('numberPerTaxon', $_POST)){
        $tidArr = (array_key_exists('tidArr',$_POST) && $_POST['tidArr']) ? json_decode($_POST['tidArr'], false) : null;
        echo json_encode($images->getChecklistTaggedImageData(json_decode($_POST['clidArr'], false), (int)$_POST['numberPerTaxon'], $tidArr));
    }
    elseif($action === 'getChecklistImageData' && array_key_exists('tidArr', $_POST) && array_key_exists('numberPerTaxon', $_POST)){
        echo json_encode($images->getChecklistImageData(json_decode($_POST['tidArr'], false), (int)$_POST['numberPerTaxon']));
    }
    elseif($action === 'getImageArrByTagValue' && array_key_exists('value', $_POST)){
        echo json_encode($images->getImageArrByTagValue($_POST['value']));
    }
    elseif($action === 'addImageTag' && $imgid && $isEditor && array_key_exists('tag', $_POST)){
        echo $images->addImageTag($imgid, $_POST['tag']);
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Media.php');
include_once(__DIR__ . '/../../services/FileSystemService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$mediaid = array_key_exists('mediaid', $_REQUEST) ? (int)$_REQUEST['mediaid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}
elseif(array_key_exists('TaxonProfile', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $media = new Media();
    if($action === 'addMedia' && $isEditor && array_key_exists('media', $_POST)){
        echo $media->createMediaRecord(json_decode($_POST['media'], true));
    }
    elseif($action === 'getMediaArrByProperty' && array_key_exists('property', $_POST) && array_key_exists('value', $_POST)){
        $limitFormat = $_POST['limitFormat'] ?? null;
        echo json_encode($media->getMediaArrByProperty($_POST['property'], $_POST['value'], $limitFormat));
    }
    elseif(($action === 'addMediaFromFile' || $action === 'addMediaFromUrl') && $isEditor && array_key_exists('media', $_POST) && array_key_exists('uploadpath', $_POST)){
        $mediaData = json_decode($_POST['media'], true);
        $targetPath = FileSystemService::getServerMediaUploadPath($_POST['uploadpath']);
        $origFilename = $action === 'addMediaFromFile' ? $_FILES['medfile']['name'] : $mediaData['filename'];
        if($targetPath && $origFilename && (strtolower(substr($origFilename, -3)) === '.zc' || strtolower(substr($origFilename, -4)) === '.mp4' || strtolower(substr($origFilename, -5)) === '.webm' || strtolower(substr($origFilename, -4)) === '.ogg' || strtolower(substr($origFilename, -4)) === '.wav' || strtolower(substr($origFilename, -4)) === '.mp3')){
            $targetFilename = FileSystemService::getServerUploadFilename($targetPath, $origFilename);
            if($action === 'addMediaFromFile' && $targetFilename && FileSystemService::moveUploadedFileToServer($_FILES['medfile'], $targetPath, $targetFilename)){
                $mediaData['accessuri'] = FileSystemService::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
            }
            elseif($action === 'addMediaFromUrl' && $targetFilename && FileSystemService::copyFileToTarget($mediaData['sourceurl'], $targetPath, $targetFilename)){
                $mediaData['accessuri'] = FileSystemService::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
            }
        }
        echo $mediaData['accessuri'] ? $media->createMediaRecord($mediaData) : 0;
    }
    elseif($action === 'deleteMediaRecord' && $mediaid && $isEditor){
        echo $media->deleteMediaRecord($mediaid);
    }
    elseif($action === 'getMediaData' && $mediaid){
        echo json_encode($media->getMediaData($mediaid));
    }
    elseif($action === 'updateMediaRecord' && $mediaid && $isEditor && array_key_exists('mediaData', $_POST)){
        echo $media->updateMediaRecord($mediaid, json_decode($_POST['mediaData'], true));
    }
    elseif($action === 'getTaxonArrDisplayMediaData'  && array_key_exists('tidArr', $_POST)){
        $includeOccurrence = array_key_exists('includeoccurrence',$_POST) && (int)$_POST['includeoccurrence'] === 1;
        $limitPerTaxon = array_key_exists('limitPerTaxon',$_POST) ? (int)$_POST['limitPerTaxon'] : null;
        $sortsequenceLimit = array_key_exists('sortsequenceLimit',$_POST) ? (int)$_POST['sortsequenceLimit'] : null;
        echo json_encode($media->getTaxonArrDisplayMediaData(json_decode($_POST['tidArr'], true), $includeOccurrence, $limitPerTaxon, $sortsequenceLimit));
    }
    elseif($action === 'deleteDescriptiveTranscriptFile' && $isEditor && array_key_exists('transcriptPath', $_POST)){
        echo $media->deleteDescriptiveTranscriptFile($_POST['transcriptPath']);
    }
    elseif($action === 'uploadDescriptiveTranscriptFromFile' && $isEditor && array_key_exists('transcriptfile', $_FILES) && array_key_exists('uploadpath', $_POST)){
        echo $media->uploadDescriptiveTranscriptFromFile($_FILES['transcriptfile'], $_POST['uploadpath']);
    }
    elseif($action === 'uploadDescriptiveTranscriptFromUrl' && $isEditor && array_key_exists('transcripturl', $_POST) && array_key_exists('uploadpath', $_POST)){
        echo $media->uploadDescriptiveTranscriptFromUrl($_POST['transcripturl'], $_POST['uploadpath']);
    }
}

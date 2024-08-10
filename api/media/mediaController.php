<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Media.php');
include_once(__DIR__ . '/../../services/FileSystemService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $media = new Media();
    if($action === 'addMedia' && $isEditor && array_key_exists('media',$_POST)){
        echo $media->createMediaRecord(json_decode($_POST['media'], true));
    }
    elseif($action === 'getMediaArrByProperty' && array_key_exists('property',$_POST) && array_key_exists('value',$_POST)){
        $limitFormat = $_POST['limitFormat'] ?? null;
        echo json_encode($media->getMediaArrByProperty($_POST['property'], $_POST['value'], $limitFormat));
    }
    elseif(($action === 'addMediaFromFile' || $action === 'addMediaFromUrl') && $isEditor && array_key_exists('media',$_POST) && array_key_exists('uploadpath',$_POST)){
        $mediaData = json_decode($_POST['media'], true);
        $targetPath = FileSystemService::getServerUploadPath($_POST['uploadpath']);
        $origFilename = $action === 'addMediaFromFile' ? $_FILES['medfile']['name'] : $mediaData['filename'];
        if($targetPath && $origFilename){
            $targetFilename = FileSystemService::getServerUploadFilename($targetPath, $origFilename);
            if($action === 'addMediaFromFile' && $targetFilename && FileSystemService::moveUploadedFileToServer($_FILES['medfile'], $targetPath, $targetFilename)){
                $mediaData['accessuri'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $targetFilename;
            }
            elseif($action === 'addMediaFromUrl' && $targetFilename && FileSystemService::copyFileToTarget($mediaData['sourceurl'], $targetPath, $targetFilename)){
                $mediaData['accessuri'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $targetFilename;
            }
        }
        echo $mediaData['accessuri'] ? $media->createMediaRecord($mediaData) : 0;
    }
}

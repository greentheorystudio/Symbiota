<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/KeyCharacterStates.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$csid = array_key_exists('csid',$_REQUEST) ? (int)$_REQUEST['csid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $keyCharacterStates = new KeyCharacterStates();
    if($action === 'getTaxaKeyCharacterStates' && array_key_exists('tidArr', $_POST)){
        echo json_encode($keyCharacterStates->getTaxaKeyCharacterStates(json_decode($_POST['tidArr'], false)));
    }
    elseif($action === 'createKeyCharacterStateRecord' && $isEditor){
        echo $keyCharacterStates->createKeyCharacterStateRecord(json_decode($_POST['characterState'], true));
    }
    elseif($action === 'updateKeyCharacterStateRecord' && $csid && $isEditor && array_key_exists('characterStateData', $_POST)){
        echo $keyCharacterStates->updateKeyCharacterStateRecord($csid, json_decode($_POST['characterStateData'], true));
    }
    elseif($action === 'getKeyCharacterStateData' && $csid && $isEditor){
        echo json_encode($keyCharacterStates->getKeyCharacterStateData($csid));
    }
    elseif($action === 'deleteKeyCharacterStateRecord' && $csid && $isEditor){
        echo $keyCharacterStates->deleteKeyCharacterStateRecord($csid);
    }
}
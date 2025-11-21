<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/KeyCharacters.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$cid = array_key_exists('cid',$_REQUEST) ? (int)$_REQUEST['cid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $keyCharacters = new KeyCharacters();
    if($action === 'getTaxaKeyCharacters' && array_key_exists('cidArr', $_POST)){
        echo json_encode($keyCharacters->getTaxaKeyCharacters(json_decode($_POST['cidArr'], false)));
    }
    elseif($action === 'createKeyCharacterRecord' && $isEditor){
        echo $keyCharacters->createKeyCharacterRecord(json_decode($_POST['character'], true));
    }
    elseif($action === 'updateKeyCharacterRecord' && $cid && $isEditor && array_key_exists('characterData', $_POST)){
        echo $keyCharacters->updateKeyCharacterRecord($cid, json_decode($_POST['characterData'], true));
    }
    elseif($action === 'getKeyCharacterData' && $cid && $isEditor){
        echo json_encode($keyCharacters->getKeyCharacterData($cid));
    }
    elseif($action === 'deleteKeyCharacterRecord' && $cid && $isEditor){
        echo $keyCharacters->deleteKeyCharacterRecord($cid);
    }
}

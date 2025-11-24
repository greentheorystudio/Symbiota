<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/KeyCharacters.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$cid = array_key_exists('cid', $_REQUEST) ? (int)$_REQUEST['cid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $keyCharacters = new KeyCharacters();
    if($action === 'getKeyCharactersArrByChidArr' && array_key_exists('chidArr', $_POST)){
        echo json_encode($keyCharacters->getKeyCharactersArrByChidArr(json_decode($_POST['chidArr'], false)));
    }
    elseif($action === 'createKeyCharacterRecord' && $isEditor && array_key_exists('character', $_POST)){
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
    elseif($action === 'getAutocompleteCharacterList'){
        echo json_encode($keyCharacters->getAutocompleteCharacterList($_POST['term']));
    }
}

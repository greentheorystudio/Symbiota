<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/KeyCharacterHeadings.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$chid = array_key_exists('chid',$_REQUEST) ? (int)$_REQUEST['chid'] : 0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $keyCharacterHeadings = new KeyCharacterHeadings();
    if($action === 'getKeyCharacterHeadingsArr'){
        $language = $_POST['language'] ?? null;
        echo json_encode($keyCharacterHeadings->getKeyCharacterHeadingsArr($language));
    }
    elseif($action === 'createKeyCharacterHeadingRecord' && $isEditor && array_key_exists('heading', $_POST)){
        echo $keyCharacterHeadings->createKeyCharacterHeadingRecord(json_decode($_POST['heading'], true));
    }
    elseif($action === 'updateKeyCharacterHeadingRecord' && $chid && $isEditor && array_key_exists('headingData', $_POST)){
        echo $keyCharacterHeadings->updateKeyCharacterHeadingRecord($chid, json_decode($_POST['headingData'], true));
    }
    elseif($action === 'getKeyCharacterHeadingData' && $chid && $isEditor){
        echo json_encode($keyCharacterHeadings->getKeyCharacterHeadingData($chid));
    }
    elseif($action === 'deleteKeyCharacterHeadingRecord' && $chid && $isEditor){
        echo $keyCharacterHeadings->deleteKeyCharacterHeadingRecord($chid);
    }
    elseif($action === 'getAutocompleteHeadingList'){
        echo json_encode($keyCharacterHeadings->getAutocompleteHeadingList($_POST['term']));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Glossary.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$glossid = array_key_exists('glossid', $_REQUEST) ? (int)$_REQUEST['glossid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $glossary = new Glossary();
    if($action === 'getTaxonGlossary' && array_key_exists('tid', $_POST)){
        echo json_encode($glossary->getTaxonGlossary($_POST['tid']));
    }
    elseif($action === 'deleteGlossaryRecord' && $isEditor && $glossid){
        echo $glossary->deleteGlossaryRecord($glossid);
    }
    elseif($action === 'createGlossaryRecord' && $isEditor && array_key_exists('glossary', $_POST)){
        echo $glossary->createGlossaryRecord(json_decode($_POST['glossary'], true));
    }
    elseif($action === 'getGlossaryData' && $glossid){
        echo json_encode($glossary->getGlossaryData($glossid));
    }
    elseif($action === 'updateGlossaryRecord' && $isEditor && $glossid && array_key_exists('glossaryData', $_POST)){
        echo $glossary->updateGlossaryRecord($glossid, json_decode($_POST['glossaryData'], true));
    }
    elseif($action === 'getGlossaryLanguageArr'){
        echo json_encode($glossary->getGlossaryLanguageArr());
    }
    elseif($action === 'getGlossaryTaxaArr'){
        echo json_encode($glossary->getGlossaryTaxaArr());
    }
    elseif($action === 'getGlossaryArr' && array_key_exists('numRows', $_POST) && array_key_exists('index', $_POST)){
        echo json_encode($glossary->getGlossaryArr((int)$_POST['numRows'], (int)$_POST['index']));
    }
    elseif($action === 'getGlossGroupIdStartIndex' && $isEditor){
        echo $glossary->getGlossGroupIdStartIndex();
    }
    elseif($action === 'addGlossaryTermRelationships' && $isEditor && array_key_exists('glossIdArr', $_POST) && array_key_exists('groupId', $_POST) && array_key_exists('relationType', $_POST)){
        echo $glossary->batchCreateGlossaryRelationshipRecordsFromGlossidArr($_POST['groupId'], $_POST['relationType'], json_decode($_POST['glossIdArr'], false));
    }
    elseif($action === 'addGlossaryTaxaRelationships' && $isEditor && array_key_exists('glossIdArr', $_POST) && array_key_exists('tid', $_POST)){
        echo $glossary->batchCreateGlossaryTaxonRelationshipRecordsFromGlossidArr($_POST['tid'], json_decode($_POST['glossIdArr'], false));
    }
    elseif($action === 'getGlossaryRelatedTermsDataFromGlossidArr' && array_key_exists('glossIdArr', $_POST)){
        $relationType = $_POST['relationtype'] ?? null;
        $languageArr = array_key_exists('languageArr', $_POST) ? json_decode($_POST['languageArr'], false) : null;
        echo json_encode($glossary->getGlossaryRelatedTermsDataFromGlossidArr(json_decode($_POST['glossIdArr'], false), $relationType, $languageArr));
    }
}

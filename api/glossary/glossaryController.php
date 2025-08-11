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
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/GlossarySources.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$tid = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $glossarySources = new GlossarySources();
    if($action === 'deleteGlossarySourceRecord' && $isEditor && $tid){
        echo $glossarySources->deleteGlossarySourceRecord($tid);
    }
    elseif($action === 'createGlossarySourceRecord' && $isEditor && array_key_exists('glossarySource', $_POST)){
        echo $glossarySources->createGlossarySourceRecord(json_decode($_POST['glossarySource'], true));
    }
    elseif($action === 'updateGlossarySourceRecord' && $isEditor && $tid && array_key_exists('glossarySourceData', $_POST)){
        echo $glossarySources->updateGlossarySourceRecord($tid, json_decode($_POST['glossarySourceData'], true));
    }
    elseif($action === 'getGlossarySourceRecord' && $tid){
        echo json_encode($glossarySources->getGlossarySourceData($tid), JSON_FORCE_OBJECT);
    }
}

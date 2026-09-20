<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonIdentifiers.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonIdentifiers = new TaxonIdentifiers();
    if($isEditor && $action === 'addTaxonIdentifier' && $tId && array_key_exists('idname', $_POST) && array_key_exists('id', $_POST)){
        echo $taxonIdentifiers->addTaxonIdentifier($tId, $_POST['idname'], $_POST['id']);
    }
    elseif($action === 'getIdentifiersForTaxonomicGroup' && $tId && array_key_exists('index', $_POST) && array_key_exists('source', $_POST)){
        echo json_encode($taxonIdentifiers->getIdentifiersForTaxonomicGroup($tId, (int)$_POST['index'], $_POST['source']));
    }
    elseif($action === 'getIdentifiersFromTidArr' && array_key_exists('tidArr', $_POST)){
        $tidArr = json_decode($_POST['tidArr'], false);
        echo json_encode($taxonIdentifiers->getIdentifiersFromTidArr($tidArr));
    }
    elseif($isEditor && $action === 'updateTaxonIdentifier' && $tId && array_key_exists('idname',$_POST) && array_key_exists('id',$_POST)){
        echo $taxonIdentifiers->updateTaxonIdentifier($tId, $_POST['idname'], $_POST['id']);
    }
    elseif($action === 'getValueIdentifierNameArr'){
        echo json_encode($taxonIdentifiers->getValueIdentifierNameArr());
    }
    elseif($action === 'getGroupIdentifierNameArr'){
        echo json_encode($taxonIdentifiers->getGroupIdentifierNameArr());
    }
}

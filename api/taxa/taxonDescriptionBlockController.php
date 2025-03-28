<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonDescriptionBlocks.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$tdbid = array_key_exists('tdbid', $_REQUEST) ? (int)$_REQUEST['tdbid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile', $GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonDescriptionBlocks = new TaxonDescriptionBlocks();
    if($action === 'getTaxonDescriptions' && array_key_exists('tid', $_POST)){
        echo json_encode($taxonDescriptionBlocks->getTaxonDescriptions($_POST['tid']));
    }
    elseif($action === 'deleteTaxonDescriptionBlockRecord' && $isEditor && $tdbid){
        echo $taxonDescriptionBlocks->deleteTaxonDescriptionBlockRecord($tdbid);
    }
    elseif($action === 'createTaxonDescriptionBlockRecord' && $isEditor && array_key_exists('description', $_POST)){
        echo $taxonDescriptionBlocks->createTaxonDescriptionBlockRecord(json_decode($_POST['description'], true));
    }
    elseif($action === 'getTaxonDescriptionBlockData' && $tdbid){
        echo json_encode($taxonDescriptionBlocks->getTaxonDescriptionBlockData($tdbid));
    }
    elseif($action === 'updateTaxonDescriptionBlockRecord' && $isEditor && $tdbid && array_key_exists('descriptionData', $_POST)){
        echo $taxonDescriptionBlocks->updateTaxonDescriptionBlockRecord($tdbid, json_decode($_POST['descriptionData'], true));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonDescriptionStatements.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$tdsid = array_key_exists('tdsid', $_REQUEST) ? (int)$_REQUEST['tdsid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile', $GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonDescriptionStatements = new TaxonDescriptionStatements();
    if($action === 'getTaxonDescriptionStatements' && array_key_exists('tid', $_POST)){
        echo json_encode($taxonDescriptionStatements->getTaxonDescriptionStatements($_POST['tid']));
    }
    elseif($action === 'deleteTaxonDescriptionStatementRecord' && $isEditor && $tdsid){
        echo $taxonDescriptionStatements->deleteTaxonDescriptionStatementRecord($tdsid);
    }
    elseif($action === 'createTaxonDescriptionStatementRecord' && $isEditor && array_key_exists('statement', $_POST)){
        echo $taxonDescriptionStatements->createTaxonDescriptionStatementRecord(json_decode($_POST['statement'], true));
    }
    elseif($action === 'updateTaxonDescriptionStatementRecord' && $isEditor && $tdsid && array_key_exists('statementData', $_POST)){
        echo $taxonDescriptionStatements->updateTaxonDescriptionStatementRecord($tdsid, json_decode($_POST['statementData'], true));
    }
}

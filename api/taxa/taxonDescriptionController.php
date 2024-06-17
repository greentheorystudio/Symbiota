<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonDescriptions.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$tId = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:null;
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonDescriptions = new TaxonDescriptions();
    if($action === 'getTaxonDescriptions' && $tId){
        echo json_encode($taxonDescriptions->getTaxonDescriptions($tId));
    }
    elseif($isEditor && $action === 'addTaxonDescriptionTab' && array_key_exists('description',$_POST)){
        echo json_encode($taxonDescriptions->addTaxonDescriptionTab(json_decode($_POST['description'], true)));
    }
    elseif($isEditor && $action === 'addTaxonDescriptionStatement' && array_key_exists('statement',$_POST)){
        echo json_encode($taxonDescriptions->addTaxonDescriptionStatement(json_decode($_POST['statement'], true)));
    }
}

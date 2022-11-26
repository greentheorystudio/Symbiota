<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($isEditor && $action){
    if($action === 'getRankNameArr'){
        $taxUtilities = new TaxonomyUtilities();
        echo json_encode($taxUtilities->getRankNameArr());
    }
    elseif($action === 'addTaxon'){
        $taxManager = new TaxonomyEditorManager();
        echo $taxManager->loadNewName(json_decode($_POST['taxon'], true));
    }
    elseif($action === 'primeHierarchyTable' && array_key_exists('tidarr',$_POST)){
        $taxUtilities = new TaxonomyUtilities();
        echo $taxUtilities->primeHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($action === 'populateHierarchyTable' && array_key_exists('tidarr',$_POST)){
        $taxUtilities = new TaxonomyUtilities();
        echo $taxUtilities->populateHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($action === 'parseSciName' && array_key_exists('sciname',$_POST)){
        $taxUtilities = new TaxonomyUtilities();
        echo json_encode($taxUtilities->parseScientificName($_POST['sciname']));
    }
    elseif($action === 'getSciNameFuzzyMatches' && array_key_exists('sciname',$_POST) && array_key_exists('lev',$_POST)){
        $taxUtilities = new TaxonomyUtilities();
        echo json_encode($taxUtilities->getCloseTaxaMatches($_POST['sciname'],$_POST['lev'],$kingdomid));
    }
}

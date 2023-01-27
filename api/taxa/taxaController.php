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
    $taxUtilities = new TaxonomyUtilities();
    $taxEditorManager = new TaxonomyEditorManager();
    if($action === 'getRankNameArr'){
        echo json_encode($taxUtilities->getRankNameArr());
    }
    elseif($action === 'getKingdomArr'){
        echo json_encode($taxUtilities->getKingdomArr());
    }
    elseif($action === 'addTaxon'){
        echo $taxEditorManager->loadNewName(json_decode($_POST['taxon'], true));
    }
    elseif($action === 'primeHierarchyTable' && array_key_exists('tidarr',$_POST)){
        echo $taxUtilities->primeHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($action === 'populateHierarchyTable' && array_key_exists('tidarr',$_POST)){
        echo $taxUtilities->populateHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($action === 'parseSciName' && array_key_exists('sciname',$_POST)){
        echo json_encode($taxUtilities->parseScientificName($_POST['sciname']));
    }
    elseif($action === 'getSciNameFuzzyMatches' && array_key_exists('sciname',$_POST) && array_key_exists('lev',$_POST)){
        echo json_encode($taxUtilities->getCloseTaxaMatches($_POST['sciname'],$_POST['lev'],$kingdomid));
    }
    elseif(($action === 'getAutocompleteSciNameList' || $action === 'getAutocompleteVernacularList') && $_POST['term']){
        $taxUtilities->setHideAuth(array_key_exists('hideauth',$_POST)?$_POST['hideauth']:false);
        $taxUtilities->setHideProtected(array_key_exists('hideprotected',$_POST)?$_POST['hideprotected']:false);
        $taxUtilities->setAcceptedOnly(array_key_exists('acceptedonly',$_POST)?$_POST['acceptedonly']:false);
        $taxUtilities->setRankLimit(array_key_exists('rlimit',$_POST)?(int)$_POST['rlimit']:0);
        $taxUtilities->setRankLow(array_key_exists('rlow',$_POST)?(int)$_POST['rlow']:0);
        $taxUtilities->setRankHigh(array_key_exists('rhigh',$_POST)?(int)$_POST['rhigh']:0);
        $taxUtilities->setLimit(array_key_exists('limit',$_POST)?(int)$_POST['limit']:0);
        if($action === 'getAutocompleteSciNameList'){
            echo json_encode($taxUtilities->getAutocompleteSciNameList($_POST['term']));
        }
        else{
            echo json_encode($taxUtilities->getAutocompleteVernacularList($_POST['term']));
        }
    }
}

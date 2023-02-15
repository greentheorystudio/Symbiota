<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$tId = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:null;
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action){
    $taxUtilities = new TaxonomyUtilities();
    $taxEditorManager = new TaxonomyEditorManager();
    if($action === 'getRankNameArr'){
        echo json_encode($taxUtilities->getRankNameArr());
    }
    elseif($action === 'getKingdomArr'){
        echo json_encode($taxUtilities->getKingdomArr());
    }
    elseif($action === 'getRankArr'){
        echo json_encode($taxUtilities->getRankArr($kingdomid));
    }
    elseif($isEditor && $action === 'addTaxon'){
        echo $taxEditorManager->loadNewName(json_decode($_POST['taxon'], true));
    }
    elseif($isEditor && $action === 'primeHierarchyTable' && array_key_exists('tidarr',$_POST)){
        echo $taxUtilities->primeHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($isEditor && $action === 'populateHierarchyTable' && array_key_exists('tidarr',$_POST)){
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
    elseif($action === 'getImageCountsForTaxonomicGroup' && array_key_exists('tid',$_POST) && array_key_exists('index',$_POST)){
        echo json_encode($taxUtilities->getImageCountsForTaxonomicGroup((int)$_POST['tid'],(int)$_POST['index']));
    }
    elseif($action === 'getVideoCountsForTaxonomicGroup' && array_key_exists('tid',$_POST) && array_key_exists('index',$_POST)){
        echo json_encode($taxUtilities->getVideoCountsForTaxonomicGroup((int)$_POST['tid'],(int)$_POST['index']));
    }
    elseif($action === 'getAudioCountsForTaxonomicGroup' && array_key_exists('tid',$_POST) && array_key_exists('index',$_POST)){
        echo json_encode($taxUtilities->getAudioCountsForTaxonomicGroup((int)$_POST['tid'],(int)$_POST['index']));
    }
    elseif($action === 'getDescriptionCountsForTaxonomicGroup' && array_key_exists('tid',$_POST) && array_key_exists('index',$_POST)){
        echo json_encode($taxUtilities->getDescriptionCountsForTaxonomicGroup((int)$_POST['tid'],(int)$_POST['index']));
    }
    elseif($action === 'getIdentifiersForTaxonomicGroup' && array_key_exists('tid',$_POST) && array_key_exists('index',$_POST) && array_key_exists('source',$_POST)){
        echo json_encode($taxUtilities->getIdentifiersForTaxonomicGroup((int)$_POST['tid'],(int)$_POST['index'],htmlspecialchars($_POST['source'])));
    }
    elseif($isEditor && $action === 'addTaxonIdentifier' && array_key_exists('tid',$_POST) && array_key_exists('idname',$_POST) && array_key_exists('id',$_POST)){
        echo $taxUtilities->addTaxonIdentifier((int)$_POST['tid'],htmlspecialchars($_POST['idname']),htmlspecialchars($_POST['id']));
    }
    elseif($action === 'getTaxonImages' && array_key_exists('tid',$_POST)){
        echo json_encode($taxUtilities->getTaxonImages((int)$_POST['tid']));
    }
    elseif($action === 'getTaxonVideos' && array_key_exists('tid',$_POST)){
        echo json_encode($taxUtilities->getTaxonVideos((int)$_POST['tid']));
    }
    elseif($action === 'getTaxonAudios' && array_key_exists('tid',$_POST)){
        echo json_encode($taxUtilities->getTaxonAudios((int)$_POST['tid']));
    }
    elseif($action === 'getTaxonDescriptions' && array_key_exists('tid',$_POST)){
        echo json_encode($taxUtilities->getTaxonDescriptions((int)$_POST['tid']));
    }
    if($isEditor && $action === 'addTaxonDescriptionTab' && array_key_exists('description',$_POST)){
        echo json_encode($taxUtilities->addTaxonDescriptionTab(json_decode($_POST['description'], true)));
    }
    if($isEditor && $action === 'addTaxonDescriptionStatement' && array_key_exists('statement',$_POST)){
        echo json_encode($taxUtilities->addTaxonDescriptionStatement(json_decode($_POST['statement'], true)));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Taxa.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : null;
$kingdomid = array_key_exists('kingdomid', $_REQUEST) ? (int)$_REQUEST['kingdomid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile', $GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxa = new Taxa();
    if($action === 'getTid' && array_key_exists('sciname',$_POST) && array_key_exists('kingdomid',$_POST)){
        $rankid = array_key_exists('rankid', $_POST) ? (int)$_POST['rankid'] : null;
        $author = array_key_exists('author', $_POST) ? $_POST['author'] : null;
        echo $taxa->getTid(htmlspecialchars($_POST['sciname']), (int)$_POST['kingdomid'], $rankid, $author);
    }
    elseif($isEditor && $action === 'addTaxon' && array_key_exists('taxon', $_POST)){
        echo $taxa->createTaxaRecord(json_decode($_POST['taxon'], true));
    }
    elseif($action === 'getSciNameFuzzyMatches' && array_key_exists('sciname', $_POST) && array_key_exists('lev', $_POST)){
        echo json_encode($taxa->getCloseTaxaMatches($_POST['sciname'],$_POST['lev'], $kingdomid));
    }
    elseif($action === 'getAutocompleteSciNameList' && $_POST['term']){
        echo json_encode($taxa->getAutocompleteSciNameList($_POST));
    }
    elseif($action === 'getImageCountsForTaxonomicGroup' && $tId && array_key_exists('index', $_POST)){
        echo json_encode($taxa->getImageCountsForTaxonomicGroup($tId, (int)$_POST['index']));
    }
    elseif($action === 'getVideoCountsForTaxonomicGroup' && $tId && array_key_exists('index', $_POST)){
        echo json_encode($taxa->getVideoCountsForTaxonomicGroup($tId, (int)$_POST['index']));
    }
    elseif($action === 'getAudioCountsForTaxonomicGroup' && $tId && array_key_exists('index', $_POST)){
        echo json_encode($taxa->getAudioCountsForTaxonomicGroup($tId, (int)$_POST['index']));
    }
    elseif($action === 'getDescriptionCountsForTaxonomicGroup' && $tId && array_key_exists('index', $_POST)){
        echo json_encode($taxa->getDescriptionCountsForTaxonomicGroup($tId, (int)$_POST['index']));
    }
    elseif($action === 'getIdentifiersForTaxonomicGroup' && $tId && array_key_exists('index', $_POST) && array_key_exists('source', $_POST)){
        echo json_encode($taxa->getIdentifiersForTaxonomicGroup($tId, (int)$_POST['index'], $_POST['source']));
    }
    elseif($isEditor && $action === 'addTaxonIdentifier' && $tId && array_key_exists('idname', $_POST) && array_key_exists('id', $_POST)){
        echo $taxa->addTaxonIdentifier($tId, $_POST['idname'], $_POST['id']);
    }
    elseif($action === 'getTaxonomicTreeKingdomNodes'){
        echo json_encode($taxa->getTaxonomicTreeKingdomNodes());
    }
    elseif($action === 'getTaxonomicTreeChildNodes' && $tId){
        $includeImage = array_key_exists('includeimage', $_POST) && (int)$_POST['includeimage'] === 1;
        $limitToAccepted = array_key_exists('limittoaccepted', $_POST) && (int)$_POST['limittoaccepted'] === 1;
        echo json_encode($taxa->getTaxonomicTreeChildNodes($tId, $limitToAccepted, $includeImage));
    }
    elseif($action === 'getTaxaIdDataFromNameArr' && array_key_exists('taxa', $_POST)){
        echo json_encode($taxa->getTaxaIdDataFromNameArr(json_decode($_POST['taxa'], true)));
    }
    elseif($action === 'getTaxonFromTid' && array_key_exists('tid', $_POST)){
        $fullData = !array_key_exists('full', $_POST) || (int)$_POST['full'] === 1;
        echo json_encode($taxa->getTaxonFromTid($_POST['tid'], $fullData));
    }
    elseif($isEditor && $action === 'updateTaxonTidAccepted' && $tId && array_key_exists('tidaccepted', $_POST) && (int)$_POST['tidaccepted']){
        $kingdom = array_key_exists('kingdom', $_POST) ? (int)$_POST['kingdom'] : 0;
        echo $taxa->submitChangeToNotAccepted($tId, $_POST['tidaccepted'], $kingdom);
    }
    elseif($isEditor && $action === 'editTaxon' && $tId && array_key_exists('taxonData', $_POST)){
        echo $taxa->updateTaxaRecord($tId, json_decode($_POST['taxonData'], true));
    }
    elseif($isEditor && $action === 'editTaxonParent' && $tId && array_key_exists('parenttid',$_POST) && (int)$_POST['parenttid']){
        echo $taxa->editTaxonParent((int)$_POST['parenttid'], $tId);
    }
    elseif($action === 'getTaxonFromSciname' && array_key_exists('sciname',$_POST)){
        $kingdomId = array_key_exists('kingdomid', $_POST) ? (int)$_POST['kingdomid'] : null;
        echo json_encode($taxa->getTaxonFromSciname($_POST['sciname'], $kingdomId));
    }
    elseif($isEditor && $action === 'setUpdateFamiliesAccepted' && array_key_exists('parenttid', $_POST)){
        echo $taxa->setUpdateFamiliesAccepted((int)$_POST['parenttid']);
    }
    elseif($isEditor && $action === 'setUpdateFamiliesUnaccepted' && array_key_exists('parenttid', $_POST)){
        echo $taxa->setUpdateFamiliesUnaccepted((int)$_POST['parenttid']);
    }
    elseif($action === 'getRankArrForTaxonomicGroup' && array_key_exists('parenttid', $_POST)){
        echo json_encode($taxa->getRankArrForTaxonomicGroup((int)$_POST['parenttid']));
    }
    elseif($action === 'getUnacceptedTaxaByTaxonomicGroup' && array_key_exists('index', $_POST) && array_key_exists('parenttid', $_POST)){
        $rankId = array_key_exists('rankid', $_POST) ? (int)$_POST['rankid'] : null;
        echo json_encode($taxa->getUnacceptedTaxaByTaxonomicGroup((int)$_POST['parenttid'], (int)$_POST['index'], $rankId));
    }
    elseif($action === 'getAcceptedTaxaByTaxonomicGroup' && array_key_exists('index', $_POST) && array_key_exists('parenttid', $_POST)){
        $rankId = array_key_exists('rankid', $_POST) ? (int)$_POST['rankid'] : null;
        echo json_encode($taxa->getAcceptedTaxaByTaxonomicGroup((int)$_POST['parenttid'], (int)$_POST['index'], $rankId));
    }
    elseif($action === 'evaluateTaxonForDeletion' && $tId){
        echo $taxa->evaluateTaxonForDeletion($tId);
    }
    elseif($isEditor && $action === 'deleteTaxonByTid' && $tId){
        echo $taxa->deleteTaxon($tId);
    }
    elseif($isEditor && $action === 'updateTaxonIdentifier' && $tId && array_key_exists('idname',$_POST) && array_key_exists('id',$_POST)){
        echo $taxa->updateTaxonIdentifier($tId, $_POST['idname'], $_POST['id']);
    }
    elseif($action === 'getProtectedTaxaArr'){
        echo json_encode($taxa->getProtectedTaxaArr());
    }
    elseif($action === 'setSecurityForTaxonOrTaxonomicGroup' && $isEditor && $tId){
        $includeSubtaxa = array_key_exists('includeSubtaxa', $_POST) && (int)$_POST['includeSubtaxa'] === 1;
        echo $taxa->setSecurityForTaxonOrTaxonomicGroup($tId, $includeSubtaxa);
    }
    elseif($action === 'removeSecurityForTaxon' && $isEditor && $tId){
        echo $taxa->removeSecurityForTaxon($tId);
    }
}

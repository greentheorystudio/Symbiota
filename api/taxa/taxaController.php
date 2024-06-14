<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Images.php');
include_once(__DIR__ . '/../../models/Media.php');
include_once(__DIR__ . '/../../models/Taxa.php');
include_once(__DIR__ . '/../../models/TaxonDescriptions.php');
include_once(__DIR__ . '/../../models/TaxonHierarchy.php');
include_once(__DIR__ . '/../../models/TaxonKingdoms.php');
include_once(__DIR__ . '/../../models/TaxonRanks.php');
include_once(__DIR__ . '/../../models/TaxonVernaculars.php');
include_once(__DIR__ . '/../../services/TaxonomyService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$tId = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:null;
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action){
    if($action === 'getRankNameArr'){
        echo json_encode((new TaxonRanks)->getRankNameArr());
    }
    elseif($action === 'getTid' && array_key_exists('sciname',$_POST) && array_key_exists('kingdomid',$_POST)){
        $rankid = array_key_exists('rankid',$_POST) ? (int)$_POST['rankid'] : null;
        $author = array_key_exists('author',$_POST) ? $_POST['author'] : null;
        echo (new Taxa)->getTid(htmlspecialchars($_POST['sciname']), (int)$_POST['kingdomid'], $rankid, $author);
    }
    elseif($action === 'getKingdomArr'){
        echo json_encode((new TaxonKingdoms)->getKingdomArr());
    }
    elseif($action === 'getRankArr'){
        echo json_encode((new TaxonRanks)->getRankArr($kingdomid));
    }
    elseif($isEditor && $action === 'addTaxon'){
        echo (new Taxa)->createTaxaRecord(json_decode($_POST['taxon'], true));
    }
    elseif($isEditor && $action === 'primeHierarchyTable'){
        if(array_key_exists('tidarr',$_POST)){
            echo (new TaxonHierarchy)->primeHierarchyTable(json_decode($_POST['tidarr'],false));
        }
        else{
            echo (new TaxonHierarchy)->primeHierarchyTable();
        }
    }
    elseif($isEditor && $action === 'populateHierarchyTable'){
        echo (new TaxonHierarchy)->populateHierarchyTable();
    }
    elseif($action === 'parseSciName' && array_key_exists('sciname',$_POST)){
        echo json_encode((new TaxonomyService)->parseScientificName($_POST['sciname']));
    }
    elseif($action === 'getSciNameFuzzyMatches' && array_key_exists('sciname',$_POST) && array_key_exists('lev',$_POST)){
        echo json_encode((new Taxa)->getCloseTaxaMatches($_POST['sciname'],$_POST['lev'],$kingdomid));
    }
    elseif(($action === 'getAutocompleteSciNameList' || $action === 'getAutocompleteVernacularList') && $_POST['term']){
        if($action === 'getAutocompleteSciNameList'){
            echo json_encode((new Taxa)->getAutocompleteSciNameList($_POST));
        }
        else{
            echo json_encode((new TaxonVernaculars)->getAutocompleteVernacularList($_POST['term']));
        }
    }
    elseif($action === 'getImageCountsForTaxonomicGroup' && $tId && array_key_exists('index',$_POST)){
        echo json_encode((new Taxa)->getImageCountsForTaxonomicGroup($tId,(int)$_POST['index']));
    }
    elseif($action === 'getVideoCountsForTaxonomicGroup' && $tId && array_key_exists('index',$_POST)){
        echo json_encode((new Taxa)->getVideoCountsForTaxonomicGroup($tId,(int)$_POST['index']));
    }
    elseif($action === 'getAudioCountsForTaxonomicGroup' && $tId && array_key_exists('index',$_POST)){
        echo json_encode((new Taxa)->getAudioCountsForTaxonomicGroup($tId,(int)$_POST['index']));
    }
    elseif($action === 'getDescriptionCountsForTaxonomicGroup' && $tId && array_key_exists('index',$_POST)){
        echo json_encode((new Taxa)->getDescriptionCountsForTaxonomicGroup($tId,(int)$_POST['index']));
    }
    elseif($action === 'getIdentifiersForTaxonomicGroup' && $tId && array_key_exists('index',$_POST) && array_key_exists('source',$_POST)){
        echo json_encode((new Taxa)->getIdentifiersForTaxonomicGroup($tId, (int)$_POST['index'], $_POST['source']));
    }
    elseif($isEditor && $action === 'addTaxonIdentifier' && $tId && array_key_exists('idname',$_POST) && array_key_exists('id',$_POST)){
        echo (new Taxa)->addTaxonIdentifier($tId, $_POST['idname'], $_POST['id']);
    }
    elseif($action === 'getTaxonImages' && $tId){
        echo json_encode((new Images)->getTaxonImages($tId));
    }
    elseif($action === 'getTaxonVideos' && $tId){
        echo json_encode((new Media)->getTaxonVideos($tId));
    }
    elseif($action === 'getTaxonAudios' && $tId){
        echo json_encode((new Media)->getTaxonAudios($tId));
    }
    elseif($action === 'getTaxonDescriptions' && $tId){
        echo json_encode((new TaxonDescriptions)->getTaxonDescriptions($tId));
    }
    elseif($isEditor && $action === 'addTaxonDescriptionTab' && array_key_exists('description',$_POST)){
        echo json_encode((new TaxonDescriptions)->addTaxonDescriptionTab(json_decode($_POST['description'], true)));
    }
    elseif($isEditor && $action === 'addTaxonDescriptionStatement' && array_key_exists('statement',$_POST)){
        echo json_encode((new TaxonDescriptions)->addTaxonDescriptionStatement(json_decode($_POST['statement'], true)));
    }
    elseif($action === 'getTaxonomicTreeKingdomNodes'){
        echo json_encode((new Taxa)->getTaxonomicTreeKingdomNodes());
    }
    elseif($action === 'getTaxonomicTreeChildNodes' && $tId){
        echo json_encode((new Taxa)->getTaxonomicTreeChildNodes($tId));
    }
    elseif($action === 'getTaxonomicTreeTaxonPath' && $tId){
        echo json_encode((new TaxonHierarchy)->getTaxonomicTreeTaxonPath($tId));
    }
    elseif($action === 'getTaxaArrFromNameArr' && array_key_exists('taxa',$_POST)){
        echo json_encode((new Taxa)->getTaxaArrFromNameArr(json_decode($_POST['taxa'], true)));
    }
    elseif($action === 'getTaxonFromTid' && array_key_exists('tid',$_POST)){
        $includeCommonNames = array_key_exists('includeCommonNames',$_POST) && $_POST['includeCommonNames'];
        $includeChildren = array_key_exists('includeChildren',$_POST) && $_POST['includeChildren'];
        echo json_encode((new Taxa)->getTaxonFromTid($_POST['tid'], $includeCommonNames, $includeChildren));
    }
    elseif($isEditor && $action === 'updateTaxonTidAccepted' && $tId && array_key_exists('tidaccepted',$_POST) && (int)$_POST['tidaccepted']){
        $kingdom = array_key_exists('kingdom',$_POST) ? (int)$_POST['kingdom'] : 0;
        echo (new Taxa)->submitChangeToNotAccepted($tId, $_POST['tidaccepted'], $kingdom);
    }
    elseif($isEditor && $action === 'editTaxon' && $tId && array_key_exists('taxonData',$_POST)){
        echo (new Taxa)->updateTaxaRecord($tId, json_decode($_POST['taxonData'], true));
    }
    elseif($isEditor && $action === 'editTaxonParent' && $tId && array_key_exists('parenttid',$_POST) && (int)$_POST['parenttid']){
        echo (new Taxa)->editTaxonParent((int)$_POST['parenttid'], $tId);
    }
    elseif($isEditor && $action === 'addTaxonCommonName' && $tId && array_key_exists('name',$_POST) && array_key_exists('langid',$_POST)){
        echo (new TaxonVernaculars)->addTaxonCommonName($tId,htmlspecialchars($_POST['name']),(int)$_POST['langid']);
    }
    elseif($isEditor && $action === 'clearHierarchyTable' && array_key_exists('tidarr',$_POST)){
        echo (new TaxonHierarchy)->deleteTidFromHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($action === 'getTaxonFromSciname' && array_key_exists('sciname',$_POST) && array_key_exists('kingdomid',$_POST)){
        $includeCommonNames = array_key_exists('includeCommonNames',$_POST) && $_POST['includeCommonNames'];
        $includeChildren = array_key_exists('includeChildren',$_POST) && $_POST['includeChildren'];
        echo json_encode((new Taxa)->getTaxonFromSciname($_POST['sciname'], (int)$_POST['kingdomid'], $includeCommonNames, $includeChildren));
    }
    elseif($isEditor && $action === 'setUpdateFamiliesAccepted' && array_key_exists('parenttid',$_POST)){
        echo (new Taxa)->setUpdateFamiliesAccepted((int)$_POST['parenttid']);
    }
    elseif($isEditor && $action === 'setUpdateFamiliesUnaccepted' && array_key_exists('parenttid',$_POST)){
        echo (new Taxa)->setUpdateFamiliesUnaccepted((int)$_POST['parenttid']);
    }
    elseif($action === 'getRankArrForTaxonomicGroup' && array_key_exists('parenttid',$_POST)){
        echo json_encode((new Taxa)->getRankArrForTaxonomicGroup((int)$_POST['parenttid']));
    }
    elseif($action === 'getUnacceptedTaxaByTaxonomicGroup' && array_key_exists('index',$_POST) && array_key_exists('parenttid',$_POST)){
        $rankId = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:null;
        echo json_encode((new Taxa)->getUnacceptedTaxaByTaxonomicGroup((int)$_POST['parenttid'],(int)$_POST['index'],$rankId));
    }
    elseif($action === 'getAcceptedTaxaByTaxonomicGroup' && array_key_exists('index',$_POST) && array_key_exists('parenttid',$_POST)){
        $rankId = array_key_exists('rankid',$_POST)?(int)$_POST['rankid']:null;
        echo json_encode((new Taxa)->getAcceptedTaxaByTaxonomicGroup((int)$_POST['parenttid'],(int)$_POST['index'],$rankId));
    }
    elseif($action === 'evaluateTaxonForDeletion' && $tId){
        echo (new Taxa)->evaluateTaxonForDeletion($tId);
    }
    elseif($isEditor && $action === 'deleteTaxonByTid' && $tId){
        echo (new Taxa)->deleteTaxon($tId);
    }
    elseif($isEditor && $action === 'removeTaxonFromTaxonomicHierarchy' && $tId && array_key_exists('parenttid',$_POST)){
        echo (new TaxonHierarchy)->removeTaxonFromTaxonomicHierarchy($tId,(int)$_POST['parenttid']);
    }
    elseif($action === 'getCommonNamesByTaxonomicGroup' && array_key_exists('index',$_POST) && array_key_exists('parenttid',$_POST)){
        echo json_encode((new TaxonVernaculars)->getCommonNamesByTaxonomicGroup((int)$_POST['parenttid'],(int)$_POST['index']));
    }
    elseif($isEditor && $action === 'editCommonName' && (int)$_POST['vid'] && array_key_exists('commonNameData',$_POST)){
        echo (new TaxonVernaculars)->updateVernacularRecord((int)$_POST['vid'], json_decode($_POST['commonNameData'], true));
    }
    elseif($isEditor && $action === 'removeCommonNamesInTaxonomicGroup' && array_key_exists('parenttid',$_POST)){
        echo (new TaxonVernaculars)->removeCommonNamesInTaxonomicGroup((int)$_POST['parenttid']);
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($collid && $action){
    if($action === 'getUnlinkedScinameCounts'){
        $cleanManager = new OccurrenceTaxonomyCleaner();
        $cleanManager->setCollId($collid);
        $returnArr = array();
        $returnArr['taxaCnt'] = $cleanManager->getBadTaxaCount();
        $returnArr['occCnt'] = $cleanManager->getBadSpecimenCount();
        echo json_encode($returnArr);
    }
    if($action === 'updateThesaurusLinkages'){
        $cleanManager = new OccurrenceTaxonomyCleaner();
        $cleanManager->setCollId($collid);
        echo $cleanManager->updateOccTaxonomicThesaurusLinkages();
    }
    if($action === 'updateLocalitySecurity'){
        $cleanManager = new OccurrenceTaxonomyCleaner();
        echo $cleanManager->protectGlobalSpecies($collid);
    }
}

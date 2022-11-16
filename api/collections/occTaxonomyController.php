<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$kingdomid = array_key_exists('kingdomid',$_REQUEST)?(int)$_REQUEST['kingdomid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = true;
}

if($collid && $action){
    $cleanManager = new OccurrenceTaxonomyCleaner();
    $cleanManager->setCollId($collid);
    if($action === 'getUnlinkedScinameCounts'){
        $returnArr = array();
        $returnArr['taxaCnt'] = $cleanManager->getBadTaxaCount();
        $returnArr['occCnt'] = $cleanManager->getBadSpecimenCount();
        echo json_encode($returnArr);
    }
    if($action === 'updateOccThesaurusLinkages'){
        echo $cleanManager->updateOccTaxonomicThesaurusLinkages($kingdomid);
    }
    if($action === 'updateDetThesaurusLinkages'){
        echo $cleanManager->updateDetTaxonomicThesaurusLinkages($kingdomid);
    }
    if($action === 'updateMediaThesaurusLinkages'){
        echo $cleanManager->updateMediaTaxonomicThesaurusLinkages();
    }
    if($action === 'updateLocalitySecurity'){
        echo $cleanManager->protectGlobalSpecies($collid);
    }
    if($action === 'cleanTrimNames'){
        echo $cleanManager->cleanTrimNames();
    }
    if($action === 'cleanSpNames'){
        echo $cleanManager->cleanSpNames();
    }
    if($action === 'cleanQualifierNames'){
        echo $cleanManager->cleanQualifierNames();
    }
    if($action === 'cleanDoubleSpaces'){
        echo $cleanManager->cleanDoubleSpaceNames();
    }
}

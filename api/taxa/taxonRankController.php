<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonRanks.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$kingdomid = array_key_exists('kingdomid',$_REQUEST) ? (int)$_REQUEST['kingdomid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonRanks = new TaxonRanks();
    if($action === 'getRankNameArr'){
        echo json_encode($taxonRanks->getRankNameArr());
    }
    elseif($action === 'getRankArr'){
        echo json_encode($taxonRanks->getRankArr($kingdomid));
    }
}

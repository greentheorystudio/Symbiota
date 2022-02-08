<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/OccurrenceChecklistManager.php');
include_once(__DIR__ . '/../classes/SOLRManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$solrManager = new SOLRManager();
$checklistManager = new OccurrenceChecklistManager();

$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?(int)$_REQUEST['taxonfilter']:1;
$interface = array_key_exists('interface',$_REQUEST)?$_REQUEST['interface']: 'checklist';
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';

if(!is_numeric($taxonFilter)) {
	$taxonFilter = 1;
}
$tidArr = array();
$stArr = array();

if($stArrJson){
    $stArr = json_decode($stArrJson, true);
	$checklistManager->setSearchTermsArr($stArr);
}

if($GLOBALS['SOLR_MODE']){
    $solrManager->setSearchTermsArr($stArr);
    $solrArr = $solrManager->getTaxaArr();
    $tidArr = $solrManager->getSOLRTidList($solrArr);
}

$dynClid = $checklistManager->buildSymbiotaChecklist($taxonFilter,$tidArr);
if($interface === 'key'){
	header('Location: ../ident/key.php?dynclid=' .$dynClid. '&taxon=All Species');
}
else{
	header('Location: ../checklists/checklist.php?dynclid=' .$dynClid);
}

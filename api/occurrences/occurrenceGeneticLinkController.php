<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/OccurrenceGeneticLinks.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$occid = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$idoccurgenetic = array_key_exists('idoccurgenetic', $_REQUEST) ? (int)$_REQUEST['idoccurgenetic'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}

if($action && SanitizerService::validateInternalRequest()){
    $occurrenceGeneticLinks = new OccurrenceGeneticLinks();
    if($action === 'getOccurrenceGeneticLinkArr' && $occid){
        echo json_encode($occurrenceGeneticLinks->getOccurrenceGeneticLinkData($occid));
    }
    elseif($action === 'createOccurrenceGeneticLinkageRecord' && $isEditor && array_key_exists('linkage',$_POST)){
        echo $occurrenceGeneticLinks->createOccurrenceGeneticLinkageRecord(json_decode($_POST['linkage'], true));
    }
    elseif($action === 'updateGeneticLinkageRecord' && $idoccurgenetic && $isEditor){
        echo $occurrenceGeneticLinks->updateGeneticLinkageRecord($idoccurgenetic, json_decode($_POST['linkageData'], true));
    }
    elseif($action === 'deleteGeneticLinkageRecord' && $idoccurgenetic && $isEditor){
        echo $occurrenceGeneticLinks->deleteGeneticLinkageRecord($idoccurgenetic);
    }
}

<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceDataset.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$action = array_key_exists('action',$_POST)?$_POST['action']:'';
$datasetID = array_key_exists('targetdatasetid',$_POST)?$_POST['targetdatasetid']:0;
$stArrJson = array_key_exists('dsstarrjson',$_POST)?$_POST['dsstarrjson']:'';
$sourcePage = array_key_exists('sourcepage',$_POST)?$_POST['sourcepage']:'datasetmanager';
$occid = array_key_exists('occid',$_POST)?$_POST['occid']:0;
$occArrJson = array_key_exists('occarrjson',$_POST)?$_POST['occarrjson']:'';

if(!is_numeric($datasetID)) {
    $datasetID = 0;
}

$stArr= array();
if($stArrJson){
    $stArr= json_decode($stArrJson, true);
}
if($occArrJson){
    $occid= json_decode($occArrJson, true);
}

if($SYMB_UID && $action){
    $datasetManager = new OccurrenceDataset();
    if($datasetID === '--newDataset'){
        $name = 'newDataset ('.date('Y-m-d H:i:s').')';
        $datasetManager->createDataset($name,'',$SYMB_UID);
        $datasetID = $datasetManager->getDatasetId();
    }
    $targetLink = 'datasetmanager.php?datasetid='.$datasetID;
    if($sourcePage === 'individual') {
        $targetLink = '../individual/index.php?occid=' . $occid;
    }
    if($action === 'addSelectedToDataset'){
        if($occid){
            if($datasetManager->addSelectedOccurrences($datasetID, $occid)){
                header('Location: '.$targetLink);
            }
            else {
                echo $datasetManager->getErrorMessage();
            }
        }
    }
    elseif($action === 'addAllToDataset'){
        $occurManager = new OccurrenceManager();
        $occurManager->setSearchTermsArr($stArr);
        $mapWhere = $occurManager->getSqlWhere();
        if($occurManager->addOccurrencesToDataset($datasetID,$mapWhere)) {
            header('Location: ' . $targetLink);
        }
    }
}
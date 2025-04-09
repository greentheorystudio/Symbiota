<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$catNum = array_key_exists('catalognumber',$_REQUEST)?$_REQUEST['catalognumber']:'';
$sciName = array_key_exists('sciname',$_REQUEST)?$_REQUEST['sciname']:'';

if(SanitizerService::validateInternalRequest()){
    $occManager = new OccurrenceEditorDeterminations();

    $recordListHtml = '';
    if($collid){
        $recordListHtml = $occManager->getBulkDetRows($collid,$catNum,$sciName,'');
    }

    echo $recordListHtml;
}

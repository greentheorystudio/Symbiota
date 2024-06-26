<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/GlossaryManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$tId = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:null;

if($action && SanitizerService::validateInternalRequest()){
    $glossUtilities = new GlossaryManager();
    if($action === 'getTaxonGlossary' && $tId){
        echo json_encode($glossUtilities->getTaxonGlossary($tId));
    }
}

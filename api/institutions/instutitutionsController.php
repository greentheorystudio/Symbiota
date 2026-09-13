<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Institutions.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$iid = array_key_exists('iid', $_REQUEST) ? (int)$_REQUEST['iid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif(array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
elseif(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $institutions = new Institutions();
    if($action === 'deleteInstitutionRecord' && $isEditor && $iid){
        echo $institutions->deleteInstitutionRecord($iid);
    }
    elseif($action === 'createInstitutionRecord' && $isEditor && array_key_exists('institution', $_POST)){
        echo $institutions->createInstitutionRecord(json_decode($_POST['institution'], true));
    }
    elseif($action === 'getInstitutionData' && $iid){
        echo json_encode($institutions->getInstitutionData($iid));
    }
    elseif($action === 'getInstitutionsArr'){
        echo json_encode($institutions->getInstitutionsArr());
    }
    elseif($action === 'updateInstitutionRecord' && $isEditor && $iid && array_key_exists('institutionData', $_POST)){
        echo $institutions->updateInstitutionRecord($iid, json_decode($_POST['institutionData'], true));
    }
    elseif($action === 'getAutocompleteLocationList'){
        echo json_encode($institutions->getAutocompleteLocationList($_POST['term']));
    }
}

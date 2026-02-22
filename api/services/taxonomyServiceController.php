<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../services/TaxonomyService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonomyService = new TaxonomyService();
    if($action === 'parseSciName' && array_key_exists('sciname',$_POST)){
        echo json_encode($taxonomyService->parseScientificName($_POST['sciname']));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Geography.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action && SanitizerService::validateInternalRequest()){
    $geoManager = new Geography();
    if($action === 'getAutocompleteCountryList'){
        echo json_encode($geoManager->getAutocompleteCountryList($_POST['term']));
    }
    elseif($action === 'getAutocompleteStateProvinceList'){
        $country = $_POST['country'] ?? null;
        echo json_encode($geoManager->getAutocompleteStateProvinceList($_POST['term'], $country));
    }
    elseif($action === 'getAutocompleteCountyList'){
        $stateProvince = $_POST['stateprovince'] ?? null;
        echo json_encode($geoManager->getAutocompleteCountyList($_POST['term'], $stateProvince));
    }
}

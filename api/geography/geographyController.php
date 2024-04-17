<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/GeographyManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action){
    $geoManager = new GeographyManager();
    if($action === 'getAutocompleteCountryList'){
        echo json_encode($geoManager->getAutocompleteCountryList($_POST['term']));
    }
    else if($action === 'getAutocompleteStateProvinceList'){
        $country = $_POST['country'] ?? null;
        echo json_encode($geoManager->getAutocompleteStateProvinceList($_POST['term'], $country));
    }
    else if($action === 'getAutocompleteCountyList'){
        $stateProvince = $_POST['stateprovince'] ?? null;
        echo json_encode($geoManager->getAutocompleteCountyList($_POST['term'], $stateProvince));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/Languages.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$iso = array_key_exists('iso',$_REQUEST)?htmlspecialchars($_REQUEST['iso']):'';
$name = array_key_exists('name',$_REQUEST)?htmlspecialchars($_REQUEST['name']):'';

if($action && SanitizerService::validateInternalRequest()){
    $languages = new Languages();
    if($action === 'getLanguageByIso'){
        echo json_encode($languages->getLanguageByIso($iso));
    }
    elseif($action === 'getLanguageByName'){
        echo json_encode($languages->getLanguageByName($name));
    }
    elseif(($action === 'getAutocompleteLanguageList') && $_POST['term']){
        echo json_encode($languages->getAutocompleteLanguageList(htmlspecialchars($_POST['term'])));
    }
    elseif($action === 'getLanguages'){
        echo json_encode($languages->getLanguageArr());
    }
}

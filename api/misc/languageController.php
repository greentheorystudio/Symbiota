<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/LanguageManager.php');
header('Content-Type: text/html; charset=UTF-8');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$iso = array_key_exists('iso',$_REQUEST)?htmlspecialchars($_REQUEST['iso']):'';
$name = array_key_exists('name',$_REQUEST)?htmlspecialchars($_REQUEST['name']):'';

if($action){
    $langManager = new LanguageManager();
    if($action === 'getLanguageByIso'){
        echo json_encode($langManager->getLanguageByIso($iso));
    }
    elseif($action === 'getLanguageByName'){
        echo json_encode($langManager->getLanguageByName($name));
    }
    elseif(($action === 'getAutocompleteLanguageList') && $_POST['term']){
        echo json_encode($langManager->getAutocompleteLanguageList(htmlspecialchars($_POST['term'])));
    }
    elseif($action === 'getLanguages'){
        echo json_encode($langManager->getLanguageArr());
    }
}

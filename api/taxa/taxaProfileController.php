<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonProfileManager.php');
include_once(__DIR__ . '/../../classes/TPImageEditorManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action){
    $tpManager = new TaxonProfileManager();
    $tImageEditor = new TPImageEditorManager();
    if($action === 'setTaxon' && array_key_exists('taxonStr',$_POST) && array_key_exists('clid',$_POST)){
        echo json_encode($tpManager->setTaxon(htmlspecialchars($_POST['taxonStr']),htmlspecialchars($_POST['clid'])));
    }
    elseif($isEditor && $action === 'uploadTaxonImage' && $_FILES && array_key_exists('tid',$_POST)){
        echo $tImageEditor->loadImage($_POST);
    }
    elseif($isEditor && $action === 'uploadTaxonMedia' && $_FILES && array_key_exists('tid',$_POST)){
        echo $tImageEditor->loadMedia($_POST);
    }
    elseif($action === 'getTaxonMedia' && array_key_exists('tid',$_POST)){
        $mediaType = array_key_exists('mediatypa',$_POST)?$_POST['mediatypa']:'';
        $limit = array_key_exists('limit',$_POST)?(int)$_POST['limit']:0;
        $includeAV = (array_key_exists('includeav',$_POST) && (int)$_POST['includeav'] === 1);
        echo json_encode($tpManager->getTaxaMedia((int)$_POST['tid'], $mediaType, $limit, $includeAV));
    }
}

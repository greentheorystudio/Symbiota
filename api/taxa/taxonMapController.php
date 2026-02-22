<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonMaps.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
$mid = array_key_exists('mid', $_REQUEST) ? (int)$_REQUEST['mid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile', $GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonMaps = new TaxonMaps();
    if($action === 'getTaxonMaps' && array_key_exists('tid', $_POST)){
        $includeSubtaxa = array_key_exists('includeSubtaxa',$_POST) && (int)$_POST['includeSubtaxa'] === 1;
        echo json_encode($taxonMaps->getTaxonMaps($_POST['tid'], $includeSubtaxa));
    }
    elseif($action === 'deleteTaxonMapRecord' && $isEditor  && array_key_exists('idType', $_POST) && ($mid || array_key_exists('tid', $_POST))){
        $id = $_POST['idType'] === 'mid' ? $mid : (int)$_POST['tid'];
        echo $taxonMaps->deleteTaxonMapRecord($_POST['idType'], $id);
    }
    elseif($action === 'createTaxonMapRecord' && $isEditor && array_key_exists('mapImageFile', $_FILES)  && array_key_exists('uploadPath', $_POST) && array_key_exists('mapData', $_POST)){
        echo $taxonMaps->createTaxonMapRecord($_FILES['mapImageFile'], $_POST['uploadPath'], json_decode($_POST['mapData'], true));
    }
    elseif($action === 'updateTaxonMapRecord' && $isEditor && $mid && array_key_exists('mapData', $_POST)){
        echo $taxonMaps->updateTaxonMapRecord($mid, json_decode($_POST['mapData'], true));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonVernaculars.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonVernaculars = new TaxonVernaculars();
    if($action === 'getAutocompleteVernacularList' && $_POST['term']){
        echo json_encode($taxonVernaculars->getAutocompleteVernacularList($_POST['term']));
    }
    elseif($isEditor && $action === 'addTaxonCommonName' && $tId && array_key_exists('name',$_POST) && array_key_exists('langid',$_POST)){
        echo $taxonVernaculars->addTaxonCommonName($tId,htmlspecialchars($_POST['name']),(int)$_POST['langid']);
    }
    elseif($action === 'getCommonNamesByTaxonomicGroup' && array_key_exists('index',$_POST) && array_key_exists('parenttid',$_POST)){
        echo json_encode($taxonVernaculars->getCommonNamesByTaxonomicGroup((int)$_POST['parenttid'],(int)$_POST['index']));
    }
    elseif($isEditor && $action === 'editCommonName' && (int)$_POST['vid'] && array_key_exists('commonNameData',$_POST)){
        echo $taxonVernaculars->updateVernacularRecord((int)$_POST['vid'], json_decode($_POST['commonNameData'], true));
    }
    elseif($isEditor && $action === 'removeCommonNamesInTaxonomicGroup' && array_key_exists('parenttid',$_POST)){
        echo $taxonVernaculars->removeCommonNamesInTaxonomicGroup((int)$_POST['parenttid']);
    }
}

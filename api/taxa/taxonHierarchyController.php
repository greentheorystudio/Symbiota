<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/TaxonHierarchy.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST) ? $_REQUEST['action'] : '';
$tId = array_key_exists('tid',$_REQUEST) ? (int)$_REQUEST['tid'] : null;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || isset($GLOBALS['USER_RIGHTS']['CollAdmin'])  || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS']) || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $taxonHierarchy = new TaxonHierarchy();
    if($isEditor && $action === 'primeHierarchyTable'){
        if(array_key_exists('tidarr',$_POST)){
            echo $taxonHierarchy->primeHierarchyTable(json_decode($_POST['tidarr'],false));
        }
        else{
            echo $taxonHierarchy->primeHierarchyTable();
        }
    }
    elseif($isEditor && $action === 'populateHierarchyTable'){
        echo $taxonHierarchy->populateHierarchyTable();
    }
    elseif($action === 'getTaxonomicTreeTaxonPath' && $tId){
        echo json_encode($taxonHierarchy->getTaxonomicTreeTaxonPath($tId));
    }
    elseif($isEditor && $action === 'clearHierarchyTable' && array_key_exists('tidarr',$_POST)){
        echo $taxonHierarchy->deleteTidFromHierarchyTable(json_decode($_POST['tidarr'],false));
    }
    elseif($isEditor && $action === 'removeTaxonFromTaxonomicHierarchy' && $tId && array_key_exists('parenttid',$_POST)){
        echo $taxonHierarchy->removeTaxonFromTaxonomicHierarchy($tId, (int)$_POST['parenttid']);
    }
    elseif($action === 'getTaxonomicTreeKingdomNodes'){
        echo json_encode($taxonHierarchy->getTaxonomicTreeKingdomNodes());
    }
    elseif($action === 'getTaxonomicTreeChildNodes' && $tId){
        $includeImage = array_key_exists('includeimage', $_POST) && (int)$_POST['includeimage'] === 1;
        $limitToAccepted = array_key_exists('limittoaccepted', $_POST) && (int)$_POST['limittoaccepted'] === 1;
        echo json_encode($taxonHierarchy->getTaxonomicTreeChildNodes($tId, $limitToAccepted, $includeImage));
    }
    elseif($isEditor && $action === 'populateTaxonHierarchyData' && $tId){
        echo $taxonHierarchy->populateTaxonHierarchyData($tId);
    }
    elseif($isEditor && $action === 'updateTaxonHierarchyData' && $tId){
        echo $taxonHierarchy->updateHierarchyTable($tId);
    }
    elseif($action === 'getParentTidDataFromTidArr' && array_key_exists('tidArr', $_POST)){
        $tidArr = json_decode($_POST['tidArr'], false);
        echo json_encode($taxonHierarchy->getParentTidDataFromTidArr($tidArr));
    }
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/ChecklistTaxa.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin', $GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $checklistTaxa = new ChecklistTaxa();
    if($action === 'getChecklistTaxa' && array_key_exists('clidArr', $_POST)){
        $includeKeyData = array_key_exists('includeKeyData', $_POST) && (int)$_POST['includeKeyData'] === 1;
        $includeSynonymyData = array_key_exists('includeSynonymyData', $_POST) && (int)$_POST['includeSynonymyData'] === 1;
        $includeVernacularData = array_key_exists('includeVernacularData', $_POST) && (int)$_POST['includeVernacularData'] === 1;
        $useAcceptedNames = array_key_exists('useAcceptedNames', $_POST) && (int)$_POST['useAcceptedNames'] === 1;
        $taxonSort = (array_key_exists('taxonSort', $_POST) && $_POST['taxonSort']) ? $_POST['taxonSort'] : null;
        $index = array_key_exists('index', $_POST) ? (int)$_POST['index'] : null;
        $recCnt = (array_key_exists('reccnt', $_POST) && (int)$_POST['reccnt'] > 0) ? (int)$_POST['reccnt'] : null;
        echo json_encode($checklistTaxa->getChecklistTaxa(json_decode($_POST['clidArr'], false), $includeKeyData, $includeSynonymyData, $includeVernacularData, $useAcceptedNames, $taxonSort, $index, $recCnt));
    }
    elseif($action === 'deleteChecklistTaxonRecord' && $isEditor && $clid && array_key_exists('cltlid', $_POST)){
        echo $checklistTaxa->deleteChecklistTaxonRecord($_POST['cltlid']);
    }
    elseif($action === 'createChecklistTaxonRecord' && $isEditor && $clid && array_key_exists('checklistTaxon', $_POST)){
        echo $checklistTaxa->createChecklistTaxonRecord($clid, json_decode($_POST['checklistTaxon'], true));
    }
    elseif($action === 'getChecklistTaxonData' && $clid && array_key_exists('tid', $_POST)){
        echo json_encode($checklistTaxa->getChecklistTaxonData($clid, $_POST['tid']));
    }
    elseif($action === 'updateChecklistTaxonRecord' && $isEditor && array_key_exists('cltlid', $_POST) && array_key_exists('checklistTaxonData', $_POST)){
        echo $checklistTaxa->updateChecklistTaxonRecord($_POST['cltlid'], json_decode($_POST['checklistTaxonData'], true));
    }
}

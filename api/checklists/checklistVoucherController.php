<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/ChecklistVouchers.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$occid = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
    $isEditor = true;
}

if($action && SanitizerService::validateInternalRequest()){
    $checklistVouchers = new ChecklistVouchers();
    if($action === 'getChecklistListByOccurrenceVoucher' && $occid){
        echo json_encode($checklistVouchers->getChecklistListByOccurrenceVoucher($_POST['occid']));
    }
    elseif($action === 'deleteChecklistVoucherRecord' && $isEditor && $clid && $occid){
        echo $checklistVouchers->deleteChecklistVoucherRecord($clid, $occid);
    }
    elseif($action === 'createChecklistVoucherRecord' && $isEditor && $clid && $occid && array_key_exists('tid', $_POST)){
        echo $checklistVouchers->createChecklistVoucherRecord($clid, $occid, (int)$_POST['tid']);
    }
    elseif($action === 'createChecklistVoucherRecords' && $isEditor && $clid && array_key_exists('occidArr', $_POST) && array_key_exists('tid', $_POST)){
        echo $checklistVouchers->createChecklistVoucherRecords($clid, json_decode($_POST['occidArr'], false), (int)$_POST['tid']);
    }
    elseif($action === 'getChecklistVouchers' && array_key_exists('clidArr', $_POST) && array_key_exists('tidArr', $_POST)){
        echo json_encode($checklistVouchers->getChecklistVouchers(json_decode($_POST['clidArr'], false), json_decode($_POST['tidArr'], false)));
    }
    elseif($action === 'getChecklistTaxonVouchers' && $clid && array_key_exists('tid', $_POST)){
        echo json_encode($checklistVouchers->getChecklistTaxaVouchers($clid, $_POST['tid']));
    }
}

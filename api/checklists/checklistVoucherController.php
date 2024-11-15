<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/ChecklistVouchers.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$occid = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
    $isEditor = true;
}
elseif($collid){
    if(array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
        $isEditor = true;
    }
    elseif(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
        $isEditor = true;
    }
}
elseif($clid){
    if(array_key_exists('ClAdmin', $GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true)){
        $isEditor = true;
    }
}

if($action && SanitizerService::validateInternalRequest()){
    $checklistVouchers = new ChecklistVouchers();
    if($action === 'getChecklistListByOccurrenceVoucher' && $occid){
        echo json_encode($checklistVouchers->getChecklistListByOccurrenceVoucher($_POST['occid']));
    }
    elseif($action === 'removeOccurrenceVoucherLinkage' && $isEditor && $clid && $occid){
        echo $checklistVouchers->removeOccurrenceVoucherLinkage($clid, $occid);
    }
    elseif($action === 'addOccurrenceVoucherLinkage' && $isEditor && $clid && $occid){
        $tid = array_key_exists('tid',$_POST) ? (int)$_POST['tid'] : null;
        echo $checklistVouchers->addOccurrenceVoucherLinkage($clid, $occid, $tid);
    }
}

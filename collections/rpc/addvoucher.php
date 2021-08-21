<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ChecklistVoucherAdmin.php');

$clid = (int)$_REQUEST['clid'];
$occid = (int)$_REQUEST['occid'];
$tid = (int)$_REQUEST['tid'];

if(!$clid){
	echo 'ERROR: Checklist ID is null';
}
elseif(!$occid){
	echo 'ERROR: Occurrence ID is null';
}
elseif(!$tid){
	echo 'ERROR: Problem with taxon name (null tid), contact administrator';
}
elseif($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin', $GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))) {
	$clManager = new ChecklistVoucherAdmin();
	$clManager->setClid($clid);
	echo $clManager->linkVoucher($tid,$occid);
}
else {
	echo 'ERROR: Permissions Error';
}

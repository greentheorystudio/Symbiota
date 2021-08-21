<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ChecklistVoucherAdmin.php');

$clid = (int)$_REQUEST['clid'];
$occArr = $_REQUEST['jsonOccArr'];
$tid = (int)$_REQUEST['tid'];

if(!$clid){
	echo 'ERROR: Checklist ID is null';
}
elseif(!$occArr){
	echo 'ERROR: Specimen identifiers are missing';
}
elseif(!$tid){
	echo 'ERROR: Problem with taxon name (null tid), contact administrator';
}
elseif($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin', $GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))) {
	$clManager = new ChecklistVoucherAdmin();
	$clManager->setClid($clid);
	$result = 0;
	foreach($occArr as $occId){
		if($clManager->linkVoucher($tid,$occId) !== 1){
			$result = 0;
			break;
		}

		$result = 1;
	}
	if($result){
		echo 1;
	}
	else{
		echo 'ERROR: Problem adding vouchers to checklist';
	}
}
else {
	echo 'ERROR: Permissions Error';
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecUpload.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$searchVar = array_key_exists('searchvar',$_REQUEST)?htmlspecialchars($_REQUEST['searchvar']):'';

$uploadManager = new SpecUpload();
$uploadManager->setCollId($collid);

if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
		$recArr = $uploadManager->exportPendingImport($searchVar);
	}
}

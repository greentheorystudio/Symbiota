<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecUpload.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$searchVar = array_key_exists('searchvar',$_REQUEST)?$_REQUEST['searchvar']:'';

$uploadManager = new SpecUpload();
$uploadManager->setCollId($collid);

if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'], true))){
		$recArr = $uploadManager->exportPendingImport($searchVar);
	}
}

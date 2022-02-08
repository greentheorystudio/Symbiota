<?php
include_once(__DIR__ . '/../../config/symbbase.php');
require_once(__DIR__ . '/../../classes/SpecUploadBase.php');
require_once(__DIR__ . '/../../classes/SpecUploadFile.php');
require_once(__DIR__ . '/../../classes/SpecUploadDwca.php');


$uploadType = array_key_exists('uploadtype',$_REQUEST)?(int)$_REQUEST['uploadtype']:0;
$securityKey = preg_replace("/[^A-Za-z0-9\-]/", '',$_REQUEST['key']);
$filePath = array_key_exists('filepath',$_REQUEST)?$_REQUEST['filepath']:'';
$importIdent = array_key_exists('importident',$_REQUEST)?$_REQUEST['importident']:false;
$importImage = array_key_exists('importimage',$_REQUEST)?$_REQUEST['importimage']:false;
$sourceType = array_key_exists('sourcetype',$_REQUEST)?$_REQUEST['sourcetype']:'';
$action = array_key_exists('action',$_REQUEST)?preg_replace('/[^a-z]/', '',$_REQUEST['action']):'';

if(!$securityKey){
	exit('ERROR: security key is required and is null ');
}
if(!$uploadType){
	exit('ERROR: uploadtype is required and is null ');
}

$FILEUPLOAD = 3;
$DWCAUPLOAD = 6;
if($uploadType === $FILEUPLOAD){
	$duManager = new SpecUploadFile();
}
elseif($uploadType === $DWCAUPLOAD){
	$duManager = new SpecUploadDwca();
	$duManager->setIncludeIdentificationHistory($importIdent);
	$duManager->setIncludeImages($importImage);
	if($filePath) {
		$duManager->setPath($filePath);
	}
	if(!$sourceType) {
		$sourceType = 'specify';
	}
}
else{
	exit('ERROR: illegal upload type = '.$uploadType.' (should be 3 = File Upload, 6 = DWCA upload)');
}
if(!$duManager->validateSecurityKey($securityKey)){
	exit('ERROR: security key validation failed!');
}
if($sourceType) {
	$duManager->setSourceDatabaseType($sourceType);
}
$duManager->setVerboseMode(2);

$duManager->loadFieldMap(true);
$ulPath = $duManager->uploadFile();
if(!$ulPath){
	exit('ERROR uploading file: '.$duManager->getErrorStr());
}

if(!$duManager->analyzeUpload()){
	exit('ERROR analyzing upload file: '.$duManager->getErrorStr());
}
$duManager->uploadData(false);
$transferCnt = $duManager->getTransferCount();
$duManager->finalTransfer();

if($transferCnt > 0){
	echo 'Transfer successful: '.$transferCnt.' records transferred';
}
else{
	echo 'FAILED: 0 records uploaded';
}

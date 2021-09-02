<?php 
include_once(__DIR__ . '/../../config/symbini.php');
require_once(__DIR__ . '/../../classes/SpecUploadBase.php');
require_once(__DIR__ . '/../../classes/SpecUploadDwca.php');

$uspid = array_key_exists('uspid',$_REQUEST)?(int)$_REQUEST['uspid']:0;

$importIdent = true;
$importImage = true;

if(!$uspid || !preg_match('/^[0-9,]+$/',$uspid)) {
	exit('ERROR: illegal upload profile identifier ');
}

$duManager = new SpecUploadDwca();
$duManager->setVerboseMode(2, 'batchDwcaUpload');

$duManager->setIncludeIdentificationHistory($importIdent);
$duManager->setIncludeImages($importImage);

$uspidArr = explode(',',$uspid);
foreach($uspidArr as $uploadId){
	$duManager->setUspid($uploadId);
	$duManager->readUploadParameters();
	$duManager->setSourceDatabaseType('batchDwcaUpload');
	
	if($duManager->getTitle() === '') {
		exit('ERROR: unable to set upload profile data (uspid: ' . $uploadId . ')');
	}
	if($duManager->getCollInfo('managementtype') !== 'Snapshot') {
		exit('ERROR: automatic updates only allowed for Snapshot collections');
	}
	
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
		$reportArr = $duManager->getTransferReport();
	}
	else{
		echo 'FAILED: 0 records uploaded';
	}
}

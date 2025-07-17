<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');
ini_set('max_execution_time', 300);

$schema = array_key_exists('schema',$_POST)?htmlspecialchars($_POST['schema']): 'native';
$cSet = array_key_exists('cset',$_POST)?htmlspecialchars($_POST['cset']):'';
$zip = array_key_exists('zip',$_POST)?(int)$_POST['zip']:0;
$format = htmlspecialchars($_POST['format']);
$clid = (int)$_POST['clid'];

$dwcaHandler = new DwcArchiverCore();

$redactLocalities = 1;
$rareReaderArr = array();
if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS'])){
	$redactLocalities = 0;
}
elseif(array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
	$redactLocalities = 0;
}
else{
	if(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS'])){
		$rareReaderArr = $GLOBALS['USER_RIGHTS']['CollEditor'];
	}
	if(array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS'])){
		$rareReaderArr = array_unique(array_merge($rareReaderArr,$GLOBALS['USER_RIGHTS']['RareSppReader']));
	}
}

$dwcaHandler->setSchemaType($schema);
$dwcaHandler->setRedactLocalities($redactLocalities);
if($rareReaderArr) {
    $dwcaHandler->setRareReaderArr($rareReaderArr);
}

$dwcaHandler->setCustomWhereSql('v.clid='.$clid);

$outputFile = null;
if($zip){
	$includeIdent = (array_key_exists('identifications',$_POST)?1:0);
	$dwcaHandler->setIncludeDets($includeIdent);
	$includeImages = (array_key_exists('images',$_POST)?1:0);
	$dwcaHandler->setIncludeImgs($includeImages);
	$outputFile = $dwcaHandler->createDwcArchive('specVouchers');
}
else{
	$outputFile = $dwcaHandler->getOccurrenceFile();
}
if($outputFile){
	$contentDesc = '';
	if($schema === 'dwc'){
		$contentDesc = 'Darwin Core ';
	}
	else{
		$contentDesc = 'Native ';
	}
	$contentDesc .= 'Occurrence ';
	if($zip){
		$contentDesc .= 'Archive ';
	}
	$contentDesc .= 'File';
	header('Content-Description: '.$contentDesc);

	if($zip){
		header('Content-Type: application/zip');
	}
	elseif($format === 'csv'){
		header('Content-Type: text/csv; charset=UTF-8');
	}
	else{
		header('Content-Type: text/html; charset=UTF-8' );
	}

	header('Content-Disposition: attachment; filename='.basename($outputFile));
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($outputFile));
	ob_clean();
	flush();
	readfile($outputFile);
	unlink($outputFile);
}
else{
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename=NoData.txt');
	echo 'The query failed to return records. Please modify query criteria and try again.';
}

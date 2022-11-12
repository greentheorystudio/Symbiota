<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = (int)$_REQUEST['collid'];
$oldSciname = $_REQUEST['oldsciname'];
$tid = (int)$_REQUEST['tid'];
$idQualifier = ($_REQUEST['idq'] ?? '');

$status = '0';
if($collid && $oldSciname && $tid){
	$cleanerManager = new OccurrenceTaxonomyCleaner();
	if($cleanerManager->remapOccurrenceTaxon($collid, $oldSciname, $tid, $idQualifier)){
		$status = '1';
	}
}
echo $status;

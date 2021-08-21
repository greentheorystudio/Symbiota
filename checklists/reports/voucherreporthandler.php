<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ChecklistVoucherPensoftExcel.php');

$clid = (int)$_REQUEST['clid'];
$rType = $_REQUEST['rtype'];

if($rType === 'pensoftxlsx'){
	$vManager = null;
	if(PHP_VERSION_ID < 50600) {
		$vManager = new ChecklistVoucherPensoftExcel();
	}
	else{
		$vManager = new ChecklistVoucherPensoft();
	}
	$vManager->setClid($clid);
	$vManager->setCollectionVariables();
	$vManager->downloadPensoftXlsx();
}
else{
	$vManager = new ChecklistVoucherAdmin();
	$vManager->setClid($clid);
	$vManager->setCollectionVariables();
	if($rType === 'fullcsv'){
		$vManager->downloadChecklistCsv();
	}
	elseif($rType === 'fullvoucherscsv'){
		$vManager->downloadVoucherCsv();
	}
	elseif($rType === 'missingoccurcsv'){
		$vManager->exportMissingOccurCsv();
	}
	elseif($rType === 'problemtaxacsv'){
		$vManager->exportProblemTaxaCsv();
	}
}

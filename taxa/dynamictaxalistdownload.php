<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
header('Cache-Control: no-cache, must-revalidate, max-age=0');

$descLimit = array_key_exists('desclimit',$_REQUEST)?(int)$_REQUEST['desclimit']:0;
$orderInput = array_key_exists('orderinput',$_REQUEST)?$_REQUEST['orderinput']:'';
$familyInput = array_key_exists('familyinput',$_REQUEST)?$_REQUEST['familyinput']:'';
$commonInput = array_key_exists('commoninput',$_REQUEST)?$_REQUEST['commoninput']:'';
$sortSelect = array_key_exists('sortSelect',$_REQUEST)?$_REQUEST['sortSelect']:'kingdom';
$targetTaxon = array_key_exists('targetname',$_REQUEST)?$_REQUEST['targetname']:'';
$targetTid = array_key_exists('targettid',$_REQUEST)?(int)$_REQUEST['targettid']:0;
$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$statusStr = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

$listManager = new TaxonomyDynamicListManager();
$tableArr = array();
$vernacularArr = array();

if(!$targetTid && $targetTaxon){
    $targetTid = $listManager->setTidFromSciname($targetTaxon);
}

if($collId){
    $listManager->setCollId($collId);
}

$listManager->setDescLimit($descLimit);

if($targetTid || $collId){
    $listManager->setTid($targetTid);
    $listManager->setSortField($sortSelect);
    $tableArr = $listManager->getTableArr();
    $vernacularArr = $listManager->getVernacularArr();
}
$fileName = 'TaxonomyDownload.csv';
header ('Content-Type: text/csv');
header ("Content-Disposition: attachment; filename=\"$fileName\"");
if($tableArr){
    $outstream = fopen('php://output', 'wb');
    fputcsv($outstream, array('Kingdom','Phylum','Class','Order','Family','Scientific Name','Common Name'));
    foreach($tableArr as $id => $taxArr){
        $rowArr = array();
        $rowArr['Kingdom'] = $taxArr['kingdomName'] ?: '';
        $rowArr['Phylum'] = $taxArr['phylumName'] ?: '';
        $rowArr['Class'] = $taxArr['className'] ?: '';
        $rowArr['Order'] = $taxArr['orderName'] ?: '';
        $rowArr['Family'] = $taxArr['familyName'] ?: '';
        $rowArr['Scientific Name'] = $taxArr['tid'] ? $taxArr['SciName'] : '';
        $rowArr['Common Name'] = array_key_exists($taxArr['tid'],$vernacularArr) ? implode(', ', $vernacularArr[$taxArr['tid']]) : '';
        fputcsv($outstream, $rowArr);
    }
    fclose($outstream);
}
else{
    echo 'Recordset is empty.\n';
}

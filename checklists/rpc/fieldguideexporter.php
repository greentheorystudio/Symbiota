<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ChecklistFGExportManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$clValue = array_key_exists('cl',$_REQUEST)?$_REQUEST['cl']:0;
$dynClid = array_key_exists('dynclid',$_REQUEST)?(int)$_REQUEST['dynclid']:0;
$pid = array_key_exists('pid',$_REQUEST)?(int)$_REQUEST['pid']: '';
$index = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$recLimit = array_key_exists('rows',$_REQUEST)?(int)$_REQUEST['rows']:0;
$photogJson = array_key_exists('photogArr',$_REQUEST)?$_REQUEST['photogArr']:'';
$photoNum = array_key_exists('photoNum',$_REQUEST)?$_REQUEST['photoNum']:0;

$dataArr = array();

$fgManager = new ChecklistFGExportManager();
if($clValue){
    $fgManager->setClValue($clValue);
}
elseif($dynClid){
    $fgManager->setDynClid($dynClid);
}
$fgManager->setSqlVars();
$fgManager->setRecIndex($index);
$fgManager->setRecLimit($recLimit);
$fgManager->setMaxPhoto($photoNum);
$fgManager->setPhotogJson($photogJson);

if($clValue || $dynClid){
    $fgManager->primeDataArr();
    $fgManager->primeOrderData();
    $fgManager->primeDescData();
    $fgManager->primeVernaculars();
    $fgManager->primeImages();
    $dataArr = $fgManager->getDataArr();
}
echo json_encode($dataArr);

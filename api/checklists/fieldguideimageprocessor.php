<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ChecklistFGExportManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$imgID = array_key_exists('imgid',$_REQUEST)?(int)$_REQUEST['imgid']:0;

$dataArr = array();

$fgManager = new ChecklistFGExportManager();
$returnStr = '';

if($imgID){
    $imgIDArr = json_decode($imgID, true);
    foreach($imgIDArr as $imId){
        $tempStr = '';
        $url = $fgManager->getImageUrl($imId);
        $dataUrl = $fgManager->getImageDataUrl($url);
        if($dataUrl){
            $tempStr = $imId.'-||-'.$dataUrl;
            $returnStr .= $tempStr.'-****-';
        }
    }
}
echo $returnStr;

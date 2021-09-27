<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
ini_set('max_execution_time', 180);

$collid = (int)$_POST['collid'];
$labelformatindex = htmlspecialchars($_POST['labelformatindex']);

$scope = $labelformatindex[0];
$labelIndex = substr($labelformatindex,2);
if(!is_numeric($labelIndex)) {
    $labelIndex = '';
}

use PhpOffice\PhpWord\PhpWord;
$labelManager = new OccurrenceLabel();
$phpWord = new PhpWord();
$ses_id = time();
$labelManager->setCollid($collid);
$formatArr = ($scope && is_numeric($labelIndex)) ? $labelManager->getLabelFormatByID($scope,$labelIndex) : array();
if($formatArr){
    $defaultFont = $formatArr['defaultFont'] ?? 'Arial';
    $defaultFontSize = isset($formatArr['defaultFontSize']) ? (int)$formatArr['defaultFontSize'] : 12;
    $formatFields = $formatArr['labelBlocks'];
    $lineWidth = 0;
    $columnCount = $formatArr['pageLayout'];
    if(!in_array($columnCount, array('1', '2', '3', '4', 'packet'), true)) {
        $columnCount = 2;
    }
    if($columnCount === 'packet'){
        $sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>2370,'marginRight'=>2370,'marginTop'=>375,'marginBottom'=>375,'headerHeight'=>0,'footerHeight'=>0);
        $sectionStyle['colsNum'] = 1;
        $lineWidth = 500;
    }
    elseif((int)$columnCount === 1){
        $sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>870,'marginRight'=>870,'marginTop'=>375,'marginBottom'=>375,'headerHeight'=>0,'footerHeight'=>0);
        $sectionStyle['colsNum'] = 1;
        $lineWidth = 700;
    }
    else{
        $sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>375,'marginRight'=>375,'marginTop'=>375,'marginBottom'=>375,'headerHeight'=>0,'footerHeight'=>0);
        if((int)$columnCount === 2){
            $sectionStyle['colsNum'] = 2;
            $lineWidth = 328;
        }
        if((int)$columnCount === 3){
            $sectionStyle['colsNum'] = 3;
            $lineWidth = 162;
        }
        if((int)$columnCount === 4){
            $sectionStyle['colsNum'] = 4;
            $lineWidth = 64;
        }
    }
    $sectionStyle['colsSpace'] = 450;
    $sectionStyle['breakType'] = 'continuous';

    if($GLOBALS['SYMB_UID']){
        $section = $phpWord->addSection($sectionStyle);
        $labelArr = $labelManager->getLabelArray($_POST['occid']);
        foreach($labelArr as $occid => $occArr){
            $dupCnt = $_POST['q-'.$occid];
            for($i = 0;$i < $dupCnt;$i++){
                if($columnCount === 'packet'){
                    $textrun = $section->addTextRun(array('keepLines'=>true,'keepNext'=>true));
                    $textrun->addTextBreak(1,array('size'=>285));
                    $textrun->addLine(array('weight'=>1,'width'=>500,'height'=>0,'dash'=>'rounddot'));
                    $textrun->addTextBreak(1,array('size'=>355));
                    $textrun->addLine(array('weight'=>1,'width'=>500,'height'=>0,'dash'=>'rounddot'));
                }

                if(isset($formatArr['headerPrefix']) || isset($formatArr['headerMidText']) || isset($formatArr['headerSuffix'])){
                    $headerMidVal = isset($formatArr['headerMidText']) ? (int)$formatArr['headerMidText'] : 0;
                    $headerStr = '';
                    $pStyleArr = array('keepLines'=>true,'keepNext'=>true);
                    $fStyleArr = array();
                    $headerStr .= $formatArr['headerPrefix'] ?? '';
                    if($headerMidVal === 1){
                        $headerStr .= $occArr['country'] ?? '';
                    }
                    if($headerMidVal === 2){
                        $headerStr .= $occArr['stateprovince'] ?? '';
                    }
                    if($headerMidVal === 3){
                        $headerStr .= $occArr['county'] ?? '';
                    }
                    if($headerMidVal === 4){
                        $headerStr .= $occArr['family'] ?? '';
                    }
                    $headerStr .= $formatArr['headerSuffix'] ?? '';
                    if(isset($formatArr['headerBold'])){
                        $fStyleArr['bold'] = true;
                    }
                    if(isset($formatArr['headerItalic'])){
                        $fStyleArr['italic'] = true;
                    }
                    if(isset($formatArr['headerUnderline'])){
                        $fStyleArr['underline'] = 'single';
                    }
                    if(isset($formatArr['headerUppercase'])){
                        $fStyleArr['allCaps'] = true;
                    }
                    $pStyleArr['align'] = $formatArr['headerTextAlign'];
                    $fStyleArr['name'] = $formatArr['headerFont'] ?? $defaultFont;
                    $fStyleArr['size'] = $formatArr['headerFontSize'] ?? $defaultFontSize;
                    $textrun = $section->addTextRun($pStyleArr);
                    $textrun->addText(htmlspecialchars($headerStr),$fStyleArr);
                    if(isset($formatArr['headerBottomMargin'])){
                        $textrun = $section->addTextRun();
                        $textrun->addTextBreak(1,array('size'=>$formatArr['headerBottomMargin']));
                    }
                }
                foreach($formatFields as $k => $labelFieldBlock){
                    $pStyleArr = array('keepLines'=>true,'keepNext'=>true);
                    if(isset($labelFieldBlock['blockTextAlign'])){
                        $pStyleArr['align'] = $labelFieldBlock['blockTextAlign'];
                    }
                    if(isset($labelFieldBlock['blockLineHeight'])){
                        $pStyleArr['size'] = $labelFieldBlock['blockLineHeight'];
                    }
                    if(isset($labelFieldBlock['blockSpaceBefore'])){
                        $pStyleArr['spaceBefore'] = ($labelFieldBlock['blockSpaceBefore'] * 15);
                    }
                    if(isset($labelFieldBlock['blockSpaceAfter'])){
                        $pStyleArr['spaceBefore'] = ($labelFieldBlock['blockSpaceAfter'] * 15);
                    }
                    $textrun = $section->addTextRun($pStyleArr);
                    if(isset($labelFieldBlock['blockDisplayLine'])){
                        $lineStyleArr = array('keepLines'=>true,'keepNext'=>true,'width'=>$lineWidth,'height'=>0,'weight'=>1);
                        if(isset($labelFieldBlock['blockDisplayLineHeight'])){
                            $lineStyleArr['weight'] = $labelFieldBlock['blockDisplayLineHeight'];
                        }
                        if(isset($labelFieldBlock['blockDisplayLineStyle']) && $labelFieldBlock['blockDisplayLineStyle'] === 'dash'){
                            $lineStyleArr['dash'] = 'dash';
                        }
                        if(isset($labelFieldBlock['blockDisplayLineStyle']) && $labelFieldBlock['blockDisplayLineStyle'] === 'dot'){
                            $lineStyleArr['dash'] = 'rounddot';
                        }
                        $textrun->addLine($lineStyleArr);
                    }
                    elseif(isset($labelFieldBlock['fields'])) {
                        $fieldsArr = $labelFieldBlock['fields'];
                        foreach($fieldsArr as $f => $fArr){
                            $field = $fArr['field'];
                            if(strncmp($field, 'barcode-', 8) === 0){
                                $idArr = explode('-', $field);
                                if($idArr){
                                    $bcField = $idArr[1];
                                    if(isset($occArr[$bcField])){
                                        ob_start();
                                        $bc = $labelManager->getBarcodePng(strtoupper($occArr[$bcField]), ($labelFieldBlock['barcodeHeight'] ?? 40), 'code39');
                                        imagepng($bc,$GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png');
                                        $textrun->addImage($GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png');
                                        imagedestroy($bc);
                                    }
                                }
                            }
                            elseif(strncmp($field, 'qr-', 3) === 0){
                                $qr = $labelManager->getQRCodePng($occid, ($labelFieldBlock['qrcodeSize'] ?? 100));
                                if($qr){
                                    file_put_contents($GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occid.'.png', $qr);
                                    $textrun->addImage($GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occid.'.png');
                                }
                            }
                            elseif(isset($occArr[$field]) && $occArr[$field]){
                                $textrun->addText(htmlspecialchars($field));
                                if(isset($labelFieldBlock['fieldPrefix']) && $labelFieldBlock['fieldPrefix']){
                                    $fPrefixStyleArr = array();
                                    if(isset($labelFieldBlock['fieldPrefixBold'])){
                                        $fPrefixStyleArr['bold'] = true;
                                    }
                                    if(isset($labelFieldBlock['fieldPrefixItalic'])){
                                        $fPrefixStyleArr['italic'] = true;
                                    }
                                    if(isset($labelFieldBlock['fieldPrefixUnderline'])){
                                        $fPrefixStyleArr['underline'] = 'single';
                                    }
                                    if(isset($labelFieldBlock['fieldPrefixUppercase'])){
                                        $fPrefixStyleArr['allCaps'] = true;
                                    }
                                    $fPrefixStyleArr['name'] = $labelFieldBlock['fieldPrefixFont'] ?? $defaultFont;
                                    $fPrefixStyleArr['size'] = $labelFieldBlock['fieldPrefixFontSize'] ?? $defaultFontSize;
                                    $textrun->addText(htmlspecialchars($labelFieldBlock['fieldPrefix']),$fPrefixStyleArr);
                                }
                                $fStyleArr = array();
                                if(isset($labelFieldBlock['fieldBold'])){
                                    $fStyleArr['bold'] = true;
                                }
                                if(isset($labelFieldBlock['fieldItalic'])){
                                    $fStyleArr['italic'] = true;
                                }
                                if(isset($labelFieldBlock['fieldUnderline'])){
                                    $fStyleArr['underline'] = 'single';
                                }
                                if(isset($labelFieldBlock['fieldUppercase'])){
                                    $fStyleArr['allCaps'] = true;
                                }
                                $fStyleArr['name'] = $labelFieldBlock['fieldFont'] ?? $defaultFont;
                                $fStyleArr['size'] = $labelFieldBlock['fieldFontSize'] ?? $defaultFontSize;
                                $textrun->addText(htmlspecialchars($occArr[$field]),$fStyleArr);
                                if(isset($labelFieldBlock['fieldSuffix']) && $labelFieldBlock['fieldSuffix']){
                                    $fSuffixStyleArr = array();
                                    if(isset($labelFieldBlock['fieldSuffixBold'])){
                                        $fSuffixStyleArr['bold'] = true;
                                    }
                                    if(isset($labelFieldBlock['fieldSuffixItalic'])){
                                        $fSuffixStyleArr['italic'] = true;
                                    }
                                    if(isset($labelFieldBlock['fieldSuffixUnderline'])){
                                        $fSuffixStyleArr['underline'] = 'single';
                                    }
                                    if(isset($labelFieldBlock['fieldSuffixUppercase'])){
                                        $fSuffixStyleArr['allCaps'] = true;
                                    }
                                    $fSuffixStyleArr['name'] = $labelFieldBlock['fieldSuffixFont'] ?? $defaultFont;
                                    $fSuffixStyleArr['size'] = $labelFieldBlock['fieldSuffixFontSize'] ?? $defaultFontSize;
                                    $textrun->addText(htmlspecialchars($labelFieldBlock['fieldSuffix']),$fSuffixStyleArr);
                                }
                            }
                        }
                    }
                    else {
                        $pStyleArr['size'] = ($labelFieldBlock['blockLineHeight'] ?? $defaultFontSize);
                        $textrun = $section->addTextRun($pStyleArr);
                        $textrun->addTextBreak();
                    }
                }
                if(isset($formatArr['footerText'])){
                    if(isset($formatArr['footerTopMargin'])){
                        $textrun = $section->addTextRun();
                        $textrun->addTextBreak(1,array('size'=>$formatArr['footerTopMargin']));
                    }
                    $pStyleArr = array('keepLines'=>true,'keepNext'=>true);
                    $fStyleArr = array();
                    if(isset($formatArr['footerBold'])){
                        $fStyleArr['bold'] = true;
                    }
                    if(isset($formatArr['footerItalic'])){
                        $fStyleArr['italic'] = true;
                    }
                    if(isset($formatArr['footerUnderline'])){
                        $fStyleArr['underline'] = 'single';
                    }
                    if(isset($formatArr['footerUppercase'])){
                        $fStyleArr['allCaps'] = true;
                    }
                    $pStyleArr['align'] = $formatArr['footerTextAlign'];
                    $fStyleArr['name'] = $formatArr['footerFont'] ?? $defaultFont;
                    $fStyleArr['size'] = $formatArr['footerFontSize'] ?? $defaultFontSize;
                    $textrun = $section->addTextRun($pStyleArr);
                    $textrun->addText(htmlspecialchars($formatArr['footerText']),$fStyleArr);
                }
                $textrun = $section->addTextRun();
                $textrun->addTextBreak(1,array('size'=>450));
            }
        }
    }

    $targetFile = $GLOBALS['SERVER_ROOT'].'/temp/report/'.$GLOBALS['PARAMS_ARR']['un'].'_'.date('Ymd').'_labels_'.$ses_id.'.docx';
    $phpWord->save($targetFile);

    header('Content-Description: File Transfer');
    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename($targetFile));
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize($targetFile));
    readfile($targetFile);
    $files = glob($GLOBALS['SERVER_ROOT'].'/temp/report/*');
    foreach($files as $file){
        if(strpos($file, (string)$ses_id) !== false) {
            unlink($file);
        }
    }
}

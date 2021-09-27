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
                    $textrun->addText($headerStr,$fStyleArr);
                    if(isset($formatArr['headerBottomMargin'])){
                        $textrun = $section->addTextRun();
                        $textrun->addTextBreak(1,array('size'=>($formatArr['headerBottomMargin'] * 15)));
                    }
                }


                /*if($hMid !== 4) {
                    $section->addText(htmlspecialchars($occArr['family']), 'familyFont', 'family');
                }
                $textrun = $section->addTextRun('scientificname');
                if($occArr['identificationqualifier']) {
                    $textrun->addText(htmlspecialchars($occArr['identificationqualifier']) . ' ', 'scientificnameauthFont');
                }
                $scinameStr = $occArr['scientificname'];
                $parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
                if(strpos($scinameStr,' sp.') !== false){
                    $scinameArr = explode(' sp. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('sp.','scientificnameinterFont');
                    }
                }
                elseif(strpos($scinameStr,'subsp.') !== false){
                    $scinameArr = explode(' subsp. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('subsp. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'ssp.') !== false){
                    $scinameArr = explode(' ssp. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('ssp. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'var.') !== false){
                    $scinameArr = explode(' var. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('var. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'variety') !== false){
                    $scinameArr = explode(' variety ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('var. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'Variety') !== false){
                    $scinameArr = explode(' Variety ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('var. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'v.') !== false){
                    $scinameArr = explode(' v. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('var. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,' f.') !== false){
                    $scinameArr = explode(' f. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('f. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'cf.') !== false){
                    $scinameArr = explode(' cf. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('cf. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                elseif(strpos($scinameStr,'aff.') !== false){
                    $scinameArr = explode(' aff. ',$scinameStr);
                    if($scinameArr){
                        $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
                        if($parentAuthor) {
                            $textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
                        }
                        $textrun->addText('aff. ','scientificnameinterFont');
                        $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
                    }
                }
                else{
                    $textrun->addText(htmlspecialchars($scinameStr).' ','scientificnameFont');
                }
                $textrun->addText(htmlspecialchars($occArr['scientificnameauthorship']),'scientificnameauthFont');
                if($occArr['identifiedby']){
                    $textrun = $section->addTextRun('identified');
                    $textrun->addText('Det by: '.htmlspecialchars($occArr['identifiedby']).' ','identifiedFont');
                    $textrun->addText(htmlspecialchars($occArr['dateidentified']),'identifiedFont');
                    if($occArr['identificationreferences'] || $occArr['identificationremarks'] || $occArr['taxonremarks']){
                        $section->addText(htmlspecialchars($occArr['identificationreferences']),'identifiedFont','identified');
                        $section->addText(htmlspecialchars($occArr['identificationremarks']),'identifiedFont','identified');
                        $section->addText(htmlspecialchars($occArr['taxonremarks']),'identifiedFont','identified');
                    }
                }
                $textrun = $section->addTextRun('loc1');
                $textrun->addText(htmlspecialchars($occArr['country'].($occArr['country']?', ':'')),'countrystateFont');
                $textrun->addText(htmlspecialchars($occArr['stateprovince'].($occArr['stateprovince']?', ':'')),'countrystateFont');
                $countyStr = trim($occArr['county']);
                if($countyStr){
                    if(!stripos($occArr['county'],' County') && !stripos($occArr['county'],' Parish')) {
                        $countyStr .= ' County';
                    }
                    $countyStr .= ', ';
                }
                $textrun->addText(htmlspecialchars($countyStr),'countrystateFont');
                $textrun->addText(htmlspecialchars($occArr['municipality'].($occArr['municipality']?', ':'')),'localityFont');
                $locStr = trim($occArr['locality']);
                if(substr($locStr,-1) !== '.'){
                    $locStr .= '.';
                }
                $textrun->addText(htmlspecialchars($locStr),'localityFont');
                if($occArr['decimallatitude'] || $occArr['verbatimcoordinates']){
                    $textrun = $section->addTextRun('other');
                    if($occArr['verbatimcoordinates']){
                        $textrun->addText(htmlspecialchars($occArr['verbatimcoordinates']),'otherFont');
                    }
                    else{
                        $textrun->addText(htmlspecialchars($occArr['decimallatitude']).($occArr['decimallatitude']>0?'N, ':'S, '),'otherFont');
                        $textrun->addText(htmlspecialchars($occArr['decimallongitude']).($occArr['decimallongitude']>0?'E':'W'),'otherFont');
                    }
                    if($occArr['coordinateuncertaintyinmeters']) {
                        $textrun->addText(htmlspecialchars(' +-' . $occArr['coordinateuncertaintyinmeters'] . ' meters'), 'otherFont');
                    }
                    if($occArr['geodeticdatum']) {
                        $textrun->addText(htmlspecialchars(' ' . $occArr['geodeticdatum']), 'otherFont');
                    }
                }
                if($occArr['elevationinmeters']){
                    $textrun = $section->addTextRun('other');
                    $textrun->addText(htmlspecialchars('Elev: '.$occArr['elevationinmeters'].'m. '),'otherFont');
                    if($occArr['verbatimelevation']) {
                        $textrun->addText(htmlspecialchars(' (' . $occArr['verbatimelevation'] . ')'), 'otherFont');
                    }
                }
                if($occArr['habitat']){
                    $textrun = $section->addTextRun('other');
                    $habStr = trim($occArr['habitat']);
                    if(substr($habStr,-1) !== '.'){
                        $habStr .= '.';
                    }
                    $textrun->addText(htmlspecialchars($habStr),'otherFont');
                }
                if($occArr['substrate']){
                    $textrun = $section->addTextRun('other');
                    $substrateStr = trim($occArr['substrate']);
                    if(substr($substrateStr,-1) !== '.'){
                        $substrateStr .= '.';
                    }
                    $textrun->addText(htmlspecialchars($substrateStr),'otherFont');
                }
                if($occArr['verbatimattributes'] || $occArr['establishmentmeans']){
                    $textrun = $section->addTextRun('other');
                    $textrun->addText(htmlspecialchars($occArr['verbatimattributes']),'otherFont');
                    if($occArr['verbatimattributes'] && $occArr['establishmentmeans']) {
                        $textrun->addText(htmlspecialchars('; '), 'otherFont');
                    }
                    $textrun->addText(htmlspecialchars($occArr['establishmentmeans']),'otherFont');
                }
                if($occArr['associatedtaxa']){
                    $textrun = $section->addTextRun('other');
                    $textrun->addText(htmlspecialchars('Associated species: '),'otherFont');
                    $textrun->addText(htmlspecialchars($occArr['associatedtaxa']),'associatedtaxaFont');
                }
                if($occArr['occurrenceremarks']){
                    $section->addText(htmlspecialchars($occArr['occurrenceremarks']),'otherFont','other');
                }
                if($occArr['typestatus']){
                    $section->addText(htmlspecialchars($occArr['typestatus']),'otherFont','other');
                }
                $textrun = $section->addTextRun('collector');
                $textrun->addText(htmlspecialchars($occArr['recordedby']),'otherFont');
                $textrun->addText(htmlspecialchars(' '.$occArr['recordnumber']),'otherFont');
                $section->addText(htmlspecialchars($occArr['eventdate']),'otherFont','other');
                if($occArr['associatedcollectors']){
                    $section->addText(htmlspecialchars('With: '.$occArr['associatedcollectors']),'otherFont','identified');
                }
                if($useBarcode && $occArr['catalognumber']){
                    $textrun = $section->addTextRun('cnbarcode');
                    $bc = $labelManager->getBarcodePng(strtoupper($occArr['catalognumber']), 40, 'code39');
                    imagepng($bc,$GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png');
                    $textrun->addImage($GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png', array('align'=>'center','marginTop'=>0.15625));
                    $textrun->addTextBreak();
                    $textrun->addText(htmlspecialchars($occArr['catalognumber']),'otherFont');
                    imagedestroy($bc);
                }
                elseif($showcatalognumbers){
                    $textrun = $section->addTextRun('cnbarcode');
                    if($occArr['catalognumber']){
                        $textrun->addText(htmlspecialchars($occArr['catalognumber']),'otherFont');
                    }
                    if($occArr['othercatalognumbers']){
                        if($occArr['catalognumber']){
                            $textrun->addTextBreak(1);
                        }
                        $textrun->addText(htmlspecialchars($occArr['othercatalognumbers']),'otherFont');
                    }
                }
                if($lFooter){
                    $section->addText(htmlspecialchars($lFooter),'lfooterFont','lfooter');
                }
                $section->addText(htmlspecialchars(' '),'dividerFont','lastLine');*/


                foreach($formatFields as $k => $labelFieldBlock){
                    $pStyleArr = array('keepLines'=>true,'keepNext'=>true);
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
                        $textrun = $section->addTextRun(array('keepLines'=>true,'keepNext'=>true));
                        $textrun->addLine($lineStyleArr);
                    }
                    elseif(isset($labelFieldBlock['fields'])) {
                        $fieldsArr = $labelFieldBlock['fields'];
                        $pStyleArr['align'] = $labelFieldBlock['blockTextAlign'];
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
                            else{

                            }
                        }
                    }
                    else {
                        $textrun = $section->addTextRun();
                        $textrun->addTextBreak(1,array('size'=>(isset($formatArr['blockLineHeight']) ? ($formatArr['blockLineHeight'] * 15) : ($defaultFontSize * 15))));
                    }
                }
                if(isset($formatArr['footerText'])){
                    if(isset($formatArr['footerTopMargin'])){
                        $textrun = $section->addTextRun();
                        $textrun->addTextBreak(1,array('size'=>($formatArr['footerTopMargin'] * 15)));
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
                    $textrun->addText($formatArr['footerText'],$fStyleArr);
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

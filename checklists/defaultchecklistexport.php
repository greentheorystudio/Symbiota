<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: text/html; charset=UTF-8' );
ini_set('max_execution_time', 240);

use PhpOffice\PhpWord\PhpWord;

$ses_id = session_id();

$clManager = new ChecklistManager();

$clValue = array_key_exists('cl',$_REQUEST)?(int)$_REQUEST['cl']:0;
$dynClid = array_key_exists('dynclid',$_REQUEST)?(int)$_REQUEST['dynclid']:0;
$pageNumber = array_key_exists('pagenumber',$_REQUEST)?(int)$_REQUEST['pagenumber']:1;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?htmlspecialchars($_REQUEST['taxonfilter']): '';
$thesFilter = array_key_exists('thesfilter',$_REQUEST)?(int)$_REQUEST['thesfilter']:0;
$showSynonyms = array_key_exists('showsynonyms',$_REQUEST)?(int)$_REQUEST['showsynonyms']:0;
$showAuthors = array_key_exists('showauthors',$_REQUEST)?(int)$_REQUEST['showauthors']:0;
$showCommon = array_key_exists('showcommon',$_REQUEST)?(int)$_REQUEST['showcommon']:0;
$showImages = array_key_exists('showimages',$_REQUEST)?(int)$_REQUEST['showimages']:0;
$showVouchers = array_key_exists('showvouchers',$_REQUEST)?(int)$_REQUEST['showvouchers']:0;
$showAlphaTaxa = array_key_exists('showalphataxa',$_REQUEST)?(int)$_REQUEST['showalphataxa']:0;
$searchCommon = array_key_exists('searchcommon',$_REQUEST)?(int)$_REQUEST['searchcommon']:0;
$searchSynonyms = array_key_exists('searchsynonyms',$_REQUEST)?(int)$_REQUEST['searchsynonyms']:0;

$exportEngine = '';
$exportExtension = '';
$locStr = '';
$exportEngine = 'Word2007';
$exportExtension = 'docx';

if($clValue){
	$statusStr = $clManager->setClValue($clValue);
}
elseif($dynClid){
	$clManager->setDynClid($dynClid);
}
$clArray = array();
if($clValue || $dynClid){
	$clArray = $clManager->getClMetaData();
}
$showDetails = 0;
if($pid) {
	$clManager->setProj($pid);
}
elseif(array_key_exists('proj',$_REQUEST)) {
	$pid = $clManager->setProj($_REQUEST['proj']);
}
if($taxonFilter) {
	$clManager->setTaxonFilter($taxonFilter);
}
if($searchCommon){
	$showCommon = 1;
	$clManager->setSearchCommon();
}
if($searchSynonyms) {
	$clManager->setSearchSynonyms();
}
if($thesFilter) {
    $clManager->setThesFilter();
}
if($showSynonyms) {
    $clManager->setShowSynonyms();
}
if($showAuthors) {
	$clManager->setShowAuthors();
}
if($showCommon) {
	$clManager->setShowCommon();
}
if($showImages) {
	$clManager->setShowImages();
}
if($showVouchers) {
	$clManager->setShowVouchers();
}
if($showAlphaTaxa) {
	$clManager->setShowAlphaTaxa();
}
$clid = $clManager->getClid();
$pid = $clManager->getPid();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = true;
}
$taxaArray = array();
if($clValue || $dynClid){
	$taxaArray = $clManager->getTaxaList($pageNumber,0);
}

$phpWord = new PhpWord();
$phpWord->addParagraphStyle('defaultPara', array('align'=>'left','lineHeight'=>1.0,'spaceBefore'=>0,'spaceAfter'=>0,'keepNext'=>true));
$phpWord->addFontStyle('titleFont', array('bold'=>true,'size'=>20,'name'=>'Arial'));
$phpWord->addFontStyle('topicFont', array('bold'=>true,'size'=>12,'name'=>'Arial'));
$phpWord->addFontStyle('textFont', array('size'=>12,'name'=>'Arial'));
$phpWord->addParagraphStyle('linePara', array('align'=>'left','lineHeight'=>1.0,'spaceBefore'=>0,'spaceAfter'=>0,'keepNext'=>true));
$phpWord->addParagraphStyle('familyPara', array('align'=>'left','lineHeight'=>1.0,'spaceBefore'=>225,'spaceAfter'=>75,'keepNext'=>true));
$phpWord->addFontStyle('familyFont', array('bold'=>true,'size'=>16,'name'=>'Arial'));
$phpWord->addParagraphStyle('scinamePara', array('align'=>'left','lineHeight'=>1.0,'indent'=>0.3125,'spaceBefore'=>0,'spaceAfter'=>45,'keepNext'=>true));
$phpWord->addFontStyle('scientificnameFont', array('bold'=>true,'italic'=>true,'size'=>12,'name'=>'Arial'));
$phpWord->addParagraphStyle('synonymPara', array('align'=>'left','lineHeight'=>1.0,'indent'=>0.78125,'spaceBefore'=>0,'spaceAfter'=>45));
$phpWord->addFontStyle('synonymFont', array('bold'=>false,'italic'=>true,'size'=>12,'name'=>'Arial'));
$phpWord->addParagraphStyle('notesvouchersPara', array('align'=>'left','lineHeight'=>1.0,'indent'=>0.78125,'spaceBefore'=>0,'spaceAfter'=>45));
$phpWord->addParagraphStyle('imagePara', array('align'=>'center','lineHeight'=>1.0,'spaceBefore'=>0,'spaceAfter'=>0));
$tableStyle = array('width'=>100);
$colRowStyle = array('cantSplit'=>true,'exactHeight'=>3750);
$phpWord->addTableStyle('imageTable',$tableStyle,$colRowStyle);
$imageCellStyle = array('valign'=>'center','width'=>2475,'borderSize'=>15,'borderColor'=>'808080');
$blankCellStyle = array('valign'=>'center','width'=>2475,'borderSize'=>15,'borderColor'=>'000000');

$section = $phpWord->addSection(array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>1080,'marginRight'=>1080,'marginTop'=>1080,'marginBottom'=>1080,'headerHeight'=>0,'footerHeight'=>0));
$title = str_replace(array('&quot;', '&apos;'), array('"', "'"), $clManager->getClName());
$textrun = $section->addTextRun('defaultPara');
$textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/checklists/checklist.php?clid='.$clValue. '&proj=' .$pid. '&dynclid=' .$dynClid,htmlspecialchars($title),'titleFont');
$textrun->addTextBreak();
if($clArray){
	if($clArray['type'] === 'rarespp'){
		$locality = str_replace(array('&quot;', '&apos;'), array('"', "'"), $clArray['locality']);
		$textrun->addText(htmlspecialchars('Sensitive species checklist for: '),'topicFont');
		$textrun->addText(htmlspecialchars($locality),'textFont');
		$textrun->addTextBreak();
	}
	$authors = str_replace(array('&quot;', '&apos;'), array('"', "'"), $clArray['authors']);
	$textrun->addText(htmlspecialchars('Authors: '),'topicFont');
	$textrun->addText(htmlspecialchars($authors),'textFont');
	$textrun->addTextBreak();
	if($clArray['publication']){
		$publication = str_replace(array('&quot;', '&apos;'), array('"', "'"), preg_replace('/\s+/', ' ', $clArray['publication']));
		$textrun->addText(htmlspecialchars('Publication: '),'topicFont');
		$textrun->addText(htmlspecialchars($publication),'textFont');
		$textrun->addTextBreak();
	}
    if($clArray['locality']){
        $locStr = str_replace(array('&quot;', '&apos;'), array('"', "'"), $clArray['locality']);
    }
    if($clArray['latcentroid']) {
        $locStr .= ' (' . $clArray['latcentroid'] . ', ' . $clArray['longcentroid'] . ')';
    }
    if($locStr){
        $textrun->addText(htmlspecialchars('Locality: '),'topicFont');
        $textrun->addText(htmlspecialchars($locStr),'textFont');
        $textrun->addTextBreak();
    }
    if($clArray['abstract']){
        $abstract = str_replace(array('&quot;', '&apos;'), array('"', "'"), preg_replace('/\s+/', ' ', $clArray['abstract']));
        $textrun->addText(htmlspecialchars('Abstract: '),'topicFont');
        $textrun->addText(htmlspecialchars($abstract),'textFont');
        $textrun->addTextBreak();
    }
    if($clValue && $clArray['notes']){
        $notes = str_replace(array('&quot;', '&apos;'), array('"', "'"), preg_replace('/\s+/', ' ', $clArray['notes']));
        $textrun->addText(htmlspecialchars('Notes: '),'topicFont');
        $textrun->addText(htmlspecialchars($notes),'textFont');
        $textrun->addTextBreak();
    }
}
$textrun = $section->addTextRun('linePara');
$textrun->addLine(array('weight'=>1,'width'=>670,'height'=>0));
$textrun = $section->addTextRun('defaultPara');
$textrun->addText(htmlspecialchars('Families: '),'topicFont');
$textrun->addText(htmlspecialchars($clManager->getFamilyCount()),'textFont');
$textrun->addTextBreak();
$textrun->addText(htmlspecialchars('Genera: '),'topicFont');
$textrun->addText(htmlspecialchars($clManager->getGenusCount()),'textFont');
$textrun->addTextBreak();
$textrun->addText(htmlspecialchars('Species: '),'topicFont');
$textrun->addText(htmlspecialchars($clManager->getSpeciesCount().' (species rank)'),'textFont');
$textrun->addTextBreak();
$textrun->addText(htmlspecialchars('Total Taxa: '),'topicFont');
$textrun->addText(htmlspecialchars($clManager->getTaxaCount().' (including subsp. and var.)'),'textFont');
$textrun->addTextBreak();
$prevfam = '';
if($showImages){
    $imageCnt = 0;
    $table = $section->addTable('imageTable');
    foreach($taxaArray as $tid => $sppArr){
        $imageCnt++;
        $family = $sppArr['family'];
        $tu = (array_key_exists('tnurl',$sppArr)?$sppArr['tnurl']:'');
        $u = (array_key_exists('url',$sppArr)?$sppArr['url']:'');
        $imgSrc = ($tu?:$u);
        if($imageCnt % 4 === 1) {
            $table->addRow();
        }
        if($imgSrc && $imgSrc[0] === '/'){
            $cell = $table->addCell(null,$imageCellStyle);
            $textrun = $cell->addTextRun('imagePara');
            $textrun->addImage(($GLOBALS['SERVER_ROOT'] . $imgSrc),array('width'=>160,'height'=>160));
            $textrun->addTextBreak();
            $textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/taxa/index.php?taxon='.$tid.'&cl='.$clid,htmlspecialchars($sppArr['sciname']),'topicFont');
            $textrun->addTextBreak();
            if(array_key_exists('vern',$sppArr)){
                $vern = str_replace(array('&quot;', '&apos;'), array('"', "'"), $sppArr['vern']);
                $textrun->addText(htmlspecialchars($vern),'topicFont');
                $textrun->addTextBreak();
            }
            if(!$showAlphaTaxa && $family !== $prevfam) {
                $textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/taxa/index.php?taxon='.$family.'&cl='.$clid,htmlspecialchars('['.$family.']'),'textFont');
                $prevfam = $family;
            }
        }
        else{
            $cell = $table->addCell(null,$blankCellStyle);
            $textrun = $cell->addTextRun('imagePara');
            $textrun->addText(htmlspecialchars('Image'),'topicFont');
            $textrun->addTextBreak();
            $textrun->addText(htmlspecialchars('not yet'),'topicFont');
            $textrun->addTextBreak();
            $textrun->addText(htmlspecialchars('available'),'topicFont');
        }
    }
}
else{
    $voucherArr = $clManager->getVoucherArr();
    foreach($taxaArray as $tid => $sppArr){
        if(!$showAlphaTaxa){
            $family = $sppArr['family'];
            if($family !== $prevfam){
                $textrun = $section->addTextRun('familyPara');
                $textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/taxa/index.php?taxon='.$family.'&cl='.$clid,htmlspecialchars($family),'familyFont');
                $prevfam = $family;
            }
        }
        $textrun = $section->addTextRun('scinamePara');
        $textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/taxa/index.php?taxon='.$tid.'&cl='.$clid,htmlspecialchars($sppArr['sciname']),'scientificnameFont');
        if(array_key_exists('author',$sppArr)){
            $sciAuthor = str_replace(array('&quot;', '&apos;'), array('"', "'"), $sppArr['author']);
            $textrun->addText(htmlspecialchars(' '.$sciAuthor),'textFont');
        }
        if(array_key_exists('vern',$sppArr)){
            $vern = str_replace(array('&quot;', '&apos;'), array('"', "'"), $sppArr['vern']);
            $textrun->addText(htmlspecialchars(' - '.$vern),'topicFont');
        }
        if(isset($sppArr['syn']) && $sppArr['syn']){
            $textrun = $section->addTextRun('synonymPara');
            $textrun->addText('[','textFont');
            $textrun->addText(htmlspecialchars(strip_tags($sppArr['syn'])),'synonymFont');
            $textrun->addText(']','textFont');
        }
        if($showVouchers){
            if(array_key_exists('notes',$sppArr) || array_key_exists($tid,$voucherArr)){
                $textrun = $section->addTextRun('notesvouchersPara');
            }
            if(array_key_exists('notes',$sppArr)){
                $noteStr = str_replace(array('&quot;', '&apos;'), array('"', "'"), trim($sppArr['notes']));
                $textrun->addText(htmlspecialchars($noteStr.($noteStr && array_key_exists($tid,$voucherArr)?'; ':'')),'textFont');
            }
            if(array_key_exists($tid,$voucherArr)){
                $i = 0;
                foreach($voucherArr[$tid] as $occid => $collName){
                    if($i > 0) {
                        $textrun->addText(htmlspecialchars(', '), 'textFont');
                    }
                    $voucStr = str_replace(array('&quot;', '&apos;'), array('"', "'"), $collName);
                    $textrun->addLink((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https':'http') . '://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$occid,htmlspecialchars($voucStr),'textFont');
                    $i++;
                }
            }
        }
    }
}

$fileName = str_replace(array(' ', '/'), '_', $clManager->getClName());
$targetFile = $GLOBALS['SERVER_ROOT'].'/temp/report/'.$fileName.'.'.$exportExtension;
$phpWord->save($targetFile, $exportEngine);

header('Content-Description: File Transfer');
header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename='.basename($targetFile));
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($targetFile));
readfile($targetFile);
unlink($targetFile);

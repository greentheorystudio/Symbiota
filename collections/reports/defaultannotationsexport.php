<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: text/html; charset=UTF-8' );
ini_set('max_execution_time', 180);

use PhpOffice\PhpWord\PhpWord;

$ses_id = session_id();

$labelManager = new OccurrenceLabel();

$collid = (int)$_POST['collid'];
$lHeader = $_POST['lheading'];
$lFooter = $_POST['lfooter'];
$detIdArr = $_POST['detid'];
$speciesAuthors = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
$clearQueue = ((array_key_exists('clearqueue',$_POST) && $_POST['clearqueue'])?1:0);
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$rowsPerPage = 3;

$exportEngine = '';
$exportExtension = '';
$labelArr = array();
if($action === 'Export to DOCX'){
	$exportEngine = 'Word2007';
	$exportExtension = 'docx';
}

$sectionStyle = array();
if($rowsPerPage === 1){
	$lineWidth = 740;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0);
}
if($rowsPerPage === 2){
	$lineWidth = 350;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0,'colsNum'=>2,'colsSpace'=>180,'breakType'=>'continuous');
}
if($rowsPerPage === 3){
	$lineWidth = 220;
	$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0,'colsNum'=>3,'colsSpace'=>180,'breakType'=>'continuous');
}

$labelManager->setCollid($collid);

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
		$isEditor = 1;
	}
}

if($isEditor && $action){
	$labelArr = $labelManager->getAnnoArray($_POST['detid'], $speciesAuthors);
	if($clearQueue){
		$labelManager->clearAnnoQueue($_POST['detid']);
	}
}

$phpWord = new PhpWord();
$phpWord->addParagraphStyle('firstLine', array('lineHeight'=>.1,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('lastLine', array('spaceAfter'=>50,'lineHeight'=>.1));
$phpWord->addFontStyle('dividerFont', array('size'=>1));
$phpWord->addParagraphStyle('header', array('align'=>'center','lineHeight'=>1.0,'spaceAfter'=>40,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('footer', array('align'=>'center','lineHeight'=>1.0,'spaceBefore'=>40,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addFontStyle('headerfooterFont', array('bold'=>true,'size'=>9,'name'=>'Arial'));
$phpWord->addParagraphStyle('other', array('align'=>'left','lineHeight'=>1.0,'spaceBefore'=>30,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addParagraphStyle('scientificname', array('align'=>'left','lineHeight'=>1.0,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
$phpWord->addFontStyle('scientificnameFont', array('bold'=>true,'italic'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameinterFont', array('bold'=>true,'size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('scientificnameauthFont', array('size'=>10,'name'=>'Arial'));
$phpWord->addFontStyle('identifiedFont', array('size'=>8,'name'=>'Arial'));
$tableStyle = array('width'=>100,'borderColor'=>'000000','borderSize'=>2,'cellMargin'=>75);
$colRowStyle = array('cantSplit'=>true);
$phpWord->addTableStyle('defaultTable',$tableStyle,$colRowStyle);
$cellStyle = array('valign'=>'top');

$section = $phpWord->addSection($sectionStyle);

foreach($labelArr as $occid => $occArr){
	$headerStr = trim($lHeader);
	$footerStr = trim($lFooter);
	
	$dupCnt = $_POST['q-'.$occid];
	for($i = 0;$i < $dupCnt;$i++){
		$section->addText(htmlspecialchars(' '),'dividerFont','firstLine');
		$table = $section->addTable('defaultTable');
		$table->addRow();
		$cell = $table->addCell(5000,$cellStyle);
		if($headerStr){
			$textrun = $cell->addTextRun('header');
			$textrun->addText(htmlspecialchars($headerStr),'headerfooterFont');
		}
		$textrun = $cell->addTextRun('scientificname');
		if($occArr['identificationqualifier']) {
			$textrun->addText(htmlspecialchars($occArr['identificationqualifier']) . ' ', 'scientificnameauthFont');
		}
		$scinameStr = $occArr['sciname'];
		$parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
		if(strpos($scinameStr,' sp.') !== false){
			$scinameArr = explode(' sp. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('sp.','scientificnameinterFont');
		}
		elseif(strpos($scinameStr,'subsp.') !== false){
			$scinameArr = explode(' subsp. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('subsp. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'ssp.') !== false){
			$scinameArr = explode(' ssp. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('ssp. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'var.') !== false){
			$scinameArr = explode(' var. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('var. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'variety') !== false){
			$scinameArr = explode(' variety ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('var. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'Variety') !== false){
			$scinameArr = explode(' Variety ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('var. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'v.') !== false){
			$scinameArr = explode(' v. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('var. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,' f.') !== false){
			$scinameArr = explode(' f. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('f. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'cf.') !== false){
			$scinameArr = explode(' cf. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('cf. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		elseif(strpos($scinameStr,'aff.') !== false){
			$scinameArr = explode(' aff. ',$scinameStr);
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[0]).' ','scientificnameFont');
            }
			if($parentAuthor) {
				$textrun->addText(htmlspecialchars($parentAuthor) . ' ', 'scientificnameauthFont');
			}
			$textrun->addText('aff. ','scientificnameinterFont');
			if($scinameArr){
                $textrun->addText(htmlspecialchars($scinameArr[1]).' ','scientificnameFont');
            }
		}
		else{
			$textrun->addText(htmlspecialchars($scinameStr).' ','scientificnameFont');
		}
		$textrun->addText(htmlspecialchars($occArr['scientificnameauthorship']),'scientificnameauthFont');
		if($occArr['identificationremarks']){
			$textrun = $cell->addTextRun('other');
			$textrun->addText(htmlspecialchars($occArr['identificationremarks']).' ','identifiedFont');
		}
		if($occArr['identificationreferences']){
			$textrun = $cell->addTextRun('other');
			$textrun->addText(htmlspecialchars($occArr['identificationreferences']).' ','identifiedFont');
		}
		if($occArr['identifiedby']){
            $textrun = $cell->addTextRun('other');
            $textrun->addText('Determiner: '.htmlspecialchars($occArr['identifiedby']),'identifiedFont');
        }
        if($occArr['dateidentified']){
            if($occArr['identifiedby']) {
                $textrun->addTextBreak();
            }
            else {
                $textrun = $cell->addTextRun('other');
            }
            $textrun->addText('Date: '.htmlspecialchars($occArr['dateidentified']).' ','identifiedFont');
        }
		if($footerStr){
			$textrun = $cell->addTextRun('footer');
			$textrun->addText(htmlspecialchars($footerStr),'headerfooterFont');
		}
		$section->addText(htmlspecialchars(' '),'dividerFont','lastLine');
	}
}

$targetFile = $GLOBALS['SERVER_ROOT'].'/temp/report/'.$GLOBALS['PARAMS_ARR']['un'].'_'.date('Ymd').'_annotations_'.$ses_id.'.'.$exportExtension;
$phpWord->save($targetFile, $exportEngine);

header('Content-Description: File Transfer');
header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename='.basename($targetFile));
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($targetFile));
readfile($targetFile);
unlink($targetFile);

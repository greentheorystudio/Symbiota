<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
ini_set('max_execution_time', 180);

$collid = (int)$_POST['collid'];
$labelformatindex = htmlspecialchars($_POST['labelformatindex']);
$columnCount = htmlspecialchars($_POST['labeltype']);

$scope = $labelformatindex[0];
$labelIndex = substr($labelformatindex,2);
if(!is_numeric($labelIndex)) {
    $labelIndex = '';
}
if(!is_numeric($columnCount) && $columnCount !== 'packet') {
    $columnCount = 2;
}

use PhpOffice\PhpWord\PhpWord;
$labelManager = new OccurrenceLabel();
$phpWord = new PhpWord();
$labelManager->setCollid($collid);
$formatArr = $labelManager->getLabelFormatByID($scope,$labelIndex);

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN']) {
        $isEditor = 1;
    }
    elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) {
        $isEditor = 1;
    }
    elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)) {
        $isEditor = 1;
    }
}

$hPrefix = $_POST['hprefix'];
$hMid = (int)$_POST['hmid'];
$hSuffix = $_POST['hsuffix'];
$lFooter = $_POST['lfooter'];
$includeSpeciesAuthor = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
$showcatalognumbers = ((array_key_exists('catalognumbers',$_POST) && $_POST['catalognumbers'])?1:0);
$useBarcode = array_key_exists('bc',$_POST)?(int)$_POST['bc']:0;
$barcodeOnly = array_key_exists('bconly',$_POST)?(int)$_POST['bconly']:0;

$ses_id = time();
$lineWidth = 0;

$hPrefix = filter_var($hPrefix, FILTER_SANITIZE_STRING);
$hSuffix = filter_var($hSuffix, FILTER_SANITIZE_STRING);
$lFooter = filter_var($lFooter, FILTER_SANITIZE_STRING);

$sectionStyle = array('pageSizeW'=>12240,'pageSizeH'=>15840,'marginLeft'=>360,'marginRight'=>360,'marginTop'=>360,'marginBottom'=>360,'headerHeight'=>0,'footerHeight'=>0);
if((int)$columnCount === 1){
	$lineWidth = 740;
}
elseif((int)$columnCount === 2){
	$lineWidth = 350;
	$sectionStyle['colsNum'] = 2;
	$sectionStyle['colsSpace'] = 690;
	$sectionStyle['breakType'] = 'continuous';
}
elseif((int)$columnCount === 3){
	$lineWidth = 220;
	$sectionStyle['colsNum'] = 3;
	$sectionStyle['colsSpace'] = 690;
	$sectionStyle['breakType'] = 'continuous';
}
/*elseif($columnCount === 'packet'){
	$lineWidth = 540;
}*/
if($isEditor){
    $phpWord->addParagraphStyle('foldMarks1', array('lineHeight'=>1.0,'spaceBefore'=>1200,'marginLeft'=>1200));
	$phpWord->addParagraphStyle('foldMarks2', array('lineHeight'=>1.0,'spaceBefore'=>1200,'spaceAfter'=>200,'marginLeft'=>400,'marginRight'=>400));
	$phpWord->addFontStyle('foldMarksFont', array('size'=>11));
	$phpWord->addParagraphStyle('firstLine', array('lineHeight'=>.1,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addParagraphStyle('lastLine', array('spaceAfter'=>300,'lineHeight'=>.1));
	$phpWord->addFontStyle('dividerFont', array('size'=>1));
	$phpWord->addParagraphStyle('barcodeonly', array('alignment'=>'center','lineHeight'=>1.0,'spaceAfter'=>300,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addParagraphStyle('lheader', array('alignment'=>'center','lineHeight'=>1.0,'spaceAfter'=>150,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('lheaderFont', array('bold'=>true,'size'=>14,'name'=>'Arial'));
	$phpWord->addParagraphStyle('family', array('alignment'=>'right','lineHeight'=>1.0,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('familyFont', array('size'=>10,'name'=>'Arial'));
	$phpWord->addParagraphStyle('scientificname', array('alignment'=>'left','lineHeight'=>1.0,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('scientificnameFont', array('bold'=>true,'italic'=>true,'size'=>11,'name'=>'Arial'));
	$phpWord->addFontStyle('scientificnameinterFont', array('bold'=>true,'size'=>11,'name'=>'Arial'));
	$phpWord->addFontStyle('scientificnameauthFont', array('size'=>11,'name'=>'Arial'));
	$phpWord->addParagraphStyle('identified', array('alignment'=>'left','lineHeight'=>1.0,'spaceAfter'=>0,'indent'=>0.3125,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('identifiedFont', array('size'=>10,'name'=>'Arial'));
	$phpWord->addParagraphStyle('loc1', array('spaceBefore'=>150,'lineHeight'=>1.0,'spaceAfter'=>0,'alignment'=>'left','keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('countrystateFont', array('size'=>11,'bold'=>true,'name'=>'Arial'));
	$phpWord->addFontStyle('localityFont', array('size'=>11,'name'=>'Arial'));
	$phpWord->addParagraphStyle('other', array('alignment'=>'left','lineHeight'=>1.0,'spaceAfter'=>0,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('otherFont', array('size'=>10,'name'=>'Arial'));
	$phpWord->addFontStyle('associatedtaxaFont', array('size'=>10,'italic'=>true,'name'=>'Arial'));
	$phpWord->addParagraphStyle('collector', array('spaceBefore'=>150,'lineHeight'=>1.0,'spaceAfter'=>0,'alignment'=>'left','keepNext'=>true,'keepLines'=>true));
	$phpWord->addParagraphStyle('cnbarcode', array('alignment'=>'center','lineHeight'=>1.0,'spaceAfter'=>0,'spaceBefore'=>150,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addParagraphStyle('lfooter', array('alignment'=>'center','lineHeight'=>1.0,'spaceAfter'=>0,'spaceBefore'=>150,'keepNext'=>true,'keepLines'=>true));
	$phpWord->addFontStyle('lfooterFont', array('bold'=>true,'size'=>12,'name'=>'Arial'));

	$section = $phpWord->addSection($sectionStyle);

	$labelArr = $labelManager->getLabelArray($_POST['occid'], $includeSpeciesAuthor);

	foreach($labelArr as $occid => $occArr){
		if($columnCount === 'packet'){
			$textrun = $section->addTextRun('foldMarks1');
			$textrun->addText('++','foldMarksFont');
			$textrun = $section->addTextRun('foldMarks2');
			$textrun->addText('+','foldMarksFont');

			$table = $section->addTable();
			$table->addRow();
			$table->addCell(1750)->addText('+');
			$table->addCell(1750)->addText('+');
		}

		if($barcodeOnly){
			if($occArr['catalognumber']){
                $textrun = $section->addTextRun('cnbarcode');
                $bc = $labelManager->getBarcodePng(strtoupper($occArr['catalognumber']), 40, 'code39');
                imagepng($bc,$GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png');
                $textrun->addImage($GLOBALS['SERVER_ROOT'].'/temp/report/'.$ses_id.$occArr['catalognumber'].'.png', array('align'=>'center'));
                $textrun->addTextBreak();
                $textrun->addText(htmlspecialchars($occArr['catalognumber']),'otherFont');
                imagedestroy($bc);
			}
		}
		else{
			$midStr = '';
			if($hMid === 1) {
                $midStr = $occArr['country'];
            }
			elseif($hMid === 2) {
                $midStr = $occArr['stateprovince'];
            }
			elseif($hMid === 3) {
                $midStr = $occArr['county'];
            }
			elseif($hMid === 4) {
                $midStr = $occArr['family'];
            }
			$headerStr = '';
			if($hPrefix || $midStr || $hSuffix){
				$headerStrArr = array();
				$headerStrArr[] = trim($hPrefix);
				$headerStrArr[] = trim($midStr);
				$headerStrArr[] = trim($hSuffix);
				$headerStr = implode(' ',$headerStrArr);
			}
			$dupCnt = $_POST['q-'.$occid];
			for($i = 0;$i < $dupCnt;$i++){
				$section->addText(htmlspecialchars(' '),'dividerFont','firstLine');
				if($headerStr){
					$section->addText(htmlspecialchars($headerStr),'lheaderFont','lheader');
				}
				if($hMid !== 4) {
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
				$section->addText(htmlspecialchars(' '),'dividerFont','lastLine');
			}
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
	if(is_file($file) && strpos($file, $ses_id) !== false) {
        unlink($file);
    }
}

<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DwcArchiverCore.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = (int)$_REQUEST['collid'];
$archiveFile = '';
$retArr = array();
if($collid){
	$isEditor = false;
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	 	$isEditor = true;
	}
	
	if($isEditor){
		$processingStatus = array_key_exists('ps',$_REQUEST)?$_REQUEST['ps']:'';
		$customField1 = array_key_exists('cf1',$_POST)?$_POST['cf1']:'';
		$customField2 = array_key_exists('cf2',$_POST)?$_POST['cf2']:'';
		
		$dwcaHandler = new DwcArchiverCore();
	
		$dwcaHandler->setCollArr($collid);
		$dwcaHandler->setCharSetOut('UTF-8');
		$dwcaHandler->setSchemaType('coge');
		$dwcaHandler->setExtended(false);
		$dwcaHandler->setDelimiter('csv');
		$dwcaHandler->setVerboseMode(0);
		$dwcaHandler->setRedactLocalities(0);
		$dwcaHandler->setIncludeDets(0);
		$dwcaHandler->setIncludeImgs(0);
		$dwcaHandler->setIncludeAttributes(0);
		$dwcaHandler->addCondition('decimallatitude','NULL');
		$dwcaHandler->addCondition('decimallongitude','NULL');
		$dwcaHandler->addCondition('catalognumber','NOTNULL');
		$dwcaHandler->addCondition('locality','NOTNULL');
		if($processingStatus) {
			$dwcaHandler->addCondition('processingstatus', 'EQUALS', $processingStatus);
		}
		if($customField1) {
			$dwcaHandler->addCondition($customField1, $_POST['ct1'], $_POST['cv1']);
		}
		if($customField2) {
			$dwcaHandler->addCondition($customField2, $_POST['ct2'], $_POST['cv2']);
		}

		$dwcaHandler->setGeolocateVariables(array('cogecomm'=>$_POST['cogecomm'],'cogename'=>$_POST['cogename'],'cogedescr'=>$_POST['cogedescr'],));

		$tPath = $GLOBALS['SERVER_ROOT'];
		$tPathSub = substr($tPath,-1);
		if($tPathSub !== '/' && $tPathSub !== '\\'){
			$tPath .= '/';
		}
		$pathFrag = 'temp/data/geolocate/';
		$tPath .= $pathFrag;
		$dwcaHandler->setTargetPath($tPath);
		$cnt = $dwcaHandler->getOccurrenceCnt();
		$fileName = 'CoGe'.'_'.time();
		$path = $dwcaHandler->createDwcArchive($fileName);

		$urlPrefix = 'http://';
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
			$urlPrefix = 'https://';
		}
		$urlPrefix .= $_SERVER['HTTP_HOST'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
			$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
		}
		$urlPath = $urlPrefix.$GLOBALS['CLIENT_ROOT'];
		$urlPathSub = substr($urlPath,-1);
		if($urlPathSub !== '/' && $urlPathSub !== '\\'){
			$urlPath .= '/';
		}
		$urlPath .= $pathFrag.$fileName.'_DwC-A.zip';
		
		if($cnt){
			if((@fclose(@fopen($urlPath, 'rb')))){
				$retArr['result']['cnt'] = $cnt;
				$retArr['result']['path'] = $urlPath;
			}
			else{
				$retArr['result'] = 'ERROR: File does not exist';
			}
		}
		else{
			$retArr['result'] = 'ERROR: Zero records returned';
		}
	}
}
echo json_encode($retArr);

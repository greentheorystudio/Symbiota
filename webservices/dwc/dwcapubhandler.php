<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$cond = array_key_exists('cond',$_REQUEST)?$_REQUEST['cond']:'';
$collType = array_key_exists('colltype',$_REQUEST)?$_REQUEST['colltype']:'specimens';
$schemaType = array_key_exists('schema',$_REQUEST)?$_REQUEST['schema']:'dwc';
$extended = array_key_exists('extended',$_REQUEST)?(int)$_REQUEST['extended']:0;
$includeDets = array_key_exists('dets',$_REQUEST)?(int)$_REQUEST['dets']:1;
$includeImgs = array_key_exists('imgs',$_REQUEST)?(int)$_REQUEST['imgs']:1;
$includeAttributes = array_key_exists('attr',$_REQUEST)?(int)$_REQUEST['attr']:1;

$dwcaHandler = new DwcArchiverCore();

$dwcaHandler->setVerboseMode(0);
$dwcaHandler->setCollArr($collid,$collType);
if($cond){
	$cArr = explode(';',$cond);
	foreach($cArr as $rawV){
		$tok = explode(':',$rawV);
		if($tok){
			$field = $tok[0];
			$cond = 'EQUALS';
			$valueArr = array();
			if($p = strpos($tok[0],'-')){
				$field = substr($tok[0],0,$p);
				$cond = substr($tok[0],$p+1);
			}
			if(isset($tok[1]) && $tok[1]){
				$valueArr = explode(',',$tok[1]);
			}
			if($valueArr){
				foreach($valueArr as $v){
					$dwcaHandler->addCondition($field, $cond, $v);
				}
			}
			else{
				$dwcaHandler->addCondition($field, $cond);
			}
		}
	}
}
$dwcaHandler->setSchemaType($schemaType);
$dwcaHandler->setExtended($extended);
$dwcaHandler->setIncludeDets($includeDets);
$dwcaHandler->setIncludeImgs($includeImgs);
$dwcaHandler->setIncludeAttributes($includeAttributes);

$archiveFile = $dwcaHandler->createDwcArchive();
if($archiveFile){
	header('Content-Description: DwC-A File Transfer');
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename='.basename($archiveFile));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($archiveFile));
	ob_clean();
	flush();
	readfile($archiveFile);
	unlink($archiveFile);
	exit;
}

header('Content-Description: DwC-A File Transfer Error');
header('Content-Type: text/plain');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo 'Error: unable to create archive';

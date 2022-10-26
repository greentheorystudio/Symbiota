<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');

$collId = (int)$_REQUEST['collid'];
$fieldName = $_REQUEST['fieldname'];
$oldValue = $_REQUEST['oldvalue'];
$buMatch = array_key_exists('bumatch',$_REQUEST)?(int)$_REQUEST['bumatch']:0;
$ouid = array_key_exists('ouid',$_REQUEST)?(int)$_REQUEST['ouid']:0;
$retCnt = '';

if($fieldName){
	$occManager = new OccurrenceEditorManager();
	$occManager->setCollId($collId);
	if($ouid){
		$occManager->setQueryVariables(array('ouid' => $ouid));
	}
	else{
		$occManager->setQueryVariables();
	}
	$occManager->setSqlWhere();
	
	$retCnt = $occManager->getBatchUpdateCount($fieldName,$oldValue, $buMatch);
}
echo $retCnt;

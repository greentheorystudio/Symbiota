<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/SpecLoans.php');
$retMsg = 0;

$loanId = $_REQUEST['loanid'];
$catalogNumber = $_REQUEST['catalognumber'];
$collId = $_REQUEST['collid'];

if($loanId && $collId && $catalogNumber && is_numeric($loanId) && is_numeric($collId)){
	if($GLOBALS['IS_ADMIN']
	|| ((array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))
	|| (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true)))){
		$loanManager = new SpecLoans();
		$retMsg = $loanManager->addSpecimen($loanId,$collId,$catalogNumber);
	}
}
echo $retMsg;


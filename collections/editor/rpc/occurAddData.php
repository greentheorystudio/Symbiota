<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceSkeletal.php');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$responseArr = array();
$isEditor = 0;
if($collid){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
		$isEditor = 1;
	}
	if($isEditor){
		$skelHandler = new OccurrenceSkeletal();
		$skelHandler->setCollid($_REQUEST['collid']);
		if(array_key_exists('catalognumber',$_REQUEST) && $skelHandler->catalogNumberExists($_REQUEST['catalognumber'])){
			$responseArr['occid'] = implode(',', $skelHandler->getOccidArr());
			if((int)$_REQUEST['addaction'] === 1){
				$responseArr['action'] = 'none';
				$responseArr['status'] = 'false';
				$responseArr['error'] = 'dupeCatalogNumber';
			}
			elseif((int)$_REQUEST['addaction'] === 2){
				$responseArr['action'] = 'update';
				$responseArr['status'] = 'true';
				if(!$skelHandler->updateOccurrence($_REQUEST)){
					$responseArr['status'] = 'false';
					$responseArr['error'] = $skelHandler->getErrorStr();
				}
			}
		}
		else{
			$responseArr['action'] = 'add';
			if($skelHandler->addOccurrence($_REQUEST)){
				$responseArr['status'] = 'true';
				$responseArr['occid'] = implode(',', $skelHandler->getOccidArr());
			}
			else{
				$responseArr['status'] = 'false';
				$responseArr['error'] = $skelHandler->getErrorStr();
			}
		}
	}
}
echo json_encode($responseArr);

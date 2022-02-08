<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceDuplicate.php');

$dupid = array_key_exists('dupid',$_REQUEST)?(int)$_REQUEST['dupid']:0;
$occid = array_key_exists('occid',$_REQUEST)?(int)$_REQUEST['occid']:0;

$isEditor = false;
if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])) {
	$isEditor = true;
}
elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])) {
	$isEditor = true;
}
if($GLOBALS['IS_ADMIN'] || $isEditor){
	if(is_numeric($occid) && is_numeric($dupid)){
		$dupeManager = new OccurrenceDuplicate();
		if($dupeManager->deleteOccurFromCluster($dupid, $occid)){
			echo '1';
		}
		else{
			echo $dupeManager->getErrorStr();
		}
	}
	else{
		echo 'ERROR unknown [1]';
	}
}
else{
	echo 'ERROR unknown [2]';
}

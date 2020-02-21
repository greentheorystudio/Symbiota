<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverCore.php');

$collid = $_REQUEST["collid"];

if($collid && is_numeric($collid)){
	$dwcaManager = new DwcArchiverCore();
	$dwcaManager->setCollArr($collid);
	$collArr = $dwcaManager->getCollArr();
	
	header('Content-Description: '.$collArr[$collid]['collname'].' EML');
	header('Content-Type: text/xml; charset=utf-8');

	$xmlDom = $dwcaManager->getEmlDom();
	echo $xmlDom->saveXML();
}
?>

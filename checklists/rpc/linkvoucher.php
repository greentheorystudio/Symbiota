<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ChecklistVoucherAdmin.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$sciname = $_POST['sciname'];
$occid = $_POST['occid'];
$clid = $_POST['clid'];

$status = 0;
if($sciname && is_numeric($occid) && is_numeric($clid)){
	$clManager = new ChecklistVoucherAdmin();
	$clManager->setClid($clid);
	$status = $clManager->linkVoucher($sciname,$occid);
}
echo $status;

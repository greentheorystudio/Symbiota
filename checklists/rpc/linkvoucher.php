<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ChecklistVoucherAdmin.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$sciname = $_POST['sciname'];
$occid = (int)$_POST['occid'];
$clid = (int)$_POST['clid'];

$status = 0;
if($sciname && $occid && $clid){
	$clManager = new ChecklistVoucherAdmin();
	$clManager->setClid($clid);
	$status = $clManager->linkVoucher($sciname,$occid);
}
echo $status;

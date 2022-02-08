<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceDuplicate.php');

$otherCatNum = array_key_exists('othercatnum',$_POST)?trim($_POST['othercatnum']):'';
$collid = array_key_exists('collid',$_POST)?(int)$_POST['collid']:0;
$currentOccid = array_key_exists('occid',$_POST)?(int)$_POST['occid']:0;

$dupeManager = new OccurrenceDuplicate();
$retStr = $dupeManager->getDupesOtherCatalogNumbers($otherCatNum, $collid, $currentOccid);
echo 'ocnum:'.implode(',',$retStr);

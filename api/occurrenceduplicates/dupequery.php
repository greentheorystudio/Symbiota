<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceDuplicate.php');

$collName = array_key_exists('cname',$_POST)?trim($_POST['cname']):'';
$collNum = array_key_exists('cnum',$_POST)?trim($_POST['cnum']):'';
$collDate = array_key_exists('cdate',$_POST)?trim($_POST['cdate']):'';
$ometid = array_key_exists('ometid',$_POST)?(int)$_POST['ometid']:0;
$exsNumber = array_key_exists('exsnumber',$_POST)?trim($_POST['exsnumber']):'';
$currentOccid = array_key_exists('curoccid',$_POST)?(int)$_POST['curoccid']:0;

$dupeManager = new OccurrenceDuplicate();
$retStr = $dupeManager->getDupes($collName, $collNum, $collDate, $ometid, $exsNumber, $currentOccid);
echo $retStr;
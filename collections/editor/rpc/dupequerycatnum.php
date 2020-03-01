<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/OccurrenceDuplicate.php');

$catNum = array_key_exists('catnum',$_POST)?$_POST['catnum']:'';
$collid = array_key_exists('collid',$_POST)?$_POST['collid']:'';
$occid = array_key_exists('occid',$_POST)?$_POST['occid']:'';

$dupeManager = new OccurrenceDuplicate();
$retArr = $dupeManager->getDupesCatalogNumber($catNum,$collid,$occid);
echo implode(',',$retArr);

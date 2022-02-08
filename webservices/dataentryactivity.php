<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/OccurrenceDownload.php');

$format = array_key_exists('format',$_REQUEST)?$_REQUEST['format']:'rss';
$days = $_REQUEST['days'] ?? 0;
$limit = $_REQUEST['limit'] ?? 0;

$activityManager = new OccurrenceDownload();

header('Content-Description: '.$GLOBALS['DEFAULT_TITLE'].' Data Entry Activity');
header('Content-Type: '.($format === 'json'?'application/json':'text/xml'));

echo $activityManager->getDataEntryActivity($format,$days,$limit);

<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/OccurrenceDownload.php');

$format = isset($_REQUEST['format']) && ($_REQUEST['format'] ?: 'rss');
$days = $_REQUEST['days'] ?? 0;
$limit = $_REQUEST['limit'] ?? 0;

$activityManager = new OccurrenceDownload();

header('Content-Description: '.$DEFAULT_TITLE.' Data Entry Activity');
header('Content-Type: '.($format === 'json'?'application/json':'text/xml'));

echo $activityManager->getDataEntryActivity($format,$days,$limit);

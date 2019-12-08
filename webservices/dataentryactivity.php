<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDownload.php');

$format = isset($_REQUEST['format'])&&$_REQUEST['format']?$_REQUEST['format']:'rss';
$days = isset($_REQUEST['days'])?$_REQUEST['days']:0;
$limit = isset($_REQUEST['limit'])?$_REQUEST['limit']:0;

$activityManager = new OccurrenceDownload();

header('Content-Description: '.$GLOBALS['DEFAULT_TITLE'].' Data Entry Activity');
header('Content-Type: '.($format=='json'?'application/json':'text/xml'));

echo $activityManager->getDataEntryActivity($format,$days,$limit);
?>

<?php
include_once(__DIR__ . '/../config/symbbase.php');

$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';

$url = $GLOBALS['CLIENT_ROOT'] . '/collections/occurrenceNavigator.php?interface=list';
if($queryId > 0){
    $url .= '&queryId=' . $queryId;
}
if($stArrJson){
    $url .= '&starr=' . $stArrJson;
}
header('Location: ' . $url);

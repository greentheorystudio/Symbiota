<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = (array_key_exists('starr', $_REQUEST) && $_REQUEST['starr'] && SanitizerService::validateJsonStr($_REQUEST['starr'])) ? $_REQUEST['starr'] : '';

$url = $GLOBALS['CLIENT_ROOT'] . '/collections/occurrenceNavigator.php?interface=list';
if($queryId > 0){
    $url .= '&queryId=' . $queryId;
}
if($stArrJson){
    $url .= '&starr=' . $stArrJson;
}
header('Location: ' . $url);

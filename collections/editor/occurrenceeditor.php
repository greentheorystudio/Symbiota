<?php
include_once(__DIR__ . '/../../config/symbbase.php');

$occId = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collId = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$displayMode = array_key_exists('mode', $_REQUEST) ? (int)$_REQUEST['mode'] : 1;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';

$url = $GLOBALS['CLIENT_ROOT'] . '/collections/occurrenceNavigator.php?interface=occurrence';
if($collId > 0){
    $url .= '&collid=' . $collId;
}
if($occId > 0){
    $url .= '&occid=' . $occId;
}
if($displayMode > 0){
    $url .= '&mode=' . $displayMode;
}
if($queryId > 0){
    $url .= '&queryId=' . $queryId;
}
if($stArrJson){
    $url .= '&starr=' . $stArrJson;
}
header('Location: ' . $url);

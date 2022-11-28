<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyAPIManager.php');

$queryString = $_REQUEST['term'];
$hideAuth = array_key_exists('hideauth',$_REQUEST)?$_REQUEST['hideauth']:false;
$hideProtected = array_key_exists('hideprotected',$_REQUEST)?$_REQUEST['hideprotected']:false;
$acceptedOnly = array_key_exists('acceptedonly',$_REQUEST)?$_REQUEST['acceptedonly']:false;
$rankLimit = array_key_exists('rlimit',$_REQUEST)?(int)$_REQUEST['rlimit']:0;
$rankLow = array_key_exists('rlow',$_REQUEST)?(int)$_REQUEST['rlow']:0;
$rankHigh = array_key_exists('rhigh',$_REQUEST)?(int)$_REQUEST['rhigh']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:0;

$qHandler = new TaxonomyAPIManager();
$listArr = array();

if($queryString){
    $qHandler->setHideAuth($hideAuth);
    $qHandler->setHideProtected($hideProtected);
    $qHandler->setRankLimit($rankLimit);
    $qHandler->setRankLow($rankLow);
    $qHandler->setRankHigh($rankHigh);
    $qHandler->setLimit($limit);

    $listArr = $qHandler->generateSciNameList($queryString);
    echo json_encode($listArr);
}

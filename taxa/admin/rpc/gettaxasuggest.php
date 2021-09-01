<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();

$q = $con->real_escape_string($_REQUEST['term']);
$hideAuth = array_key_exists('hideauth',$_REQUEST)?$con->real_escape_string($_REQUEST['hideauth']):false;
$taxAuthId = array_key_exists('taid',$_REQUEST)?(int)$_REQUEST['taid']:0;
$rankLimit = array_key_exists('rlimit',$_REQUEST)?(int)$_REQUEST['rlimit']:0;
$rankLow = array_key_exists('rlow',$_REQUEST)?(int)$_REQUEST['rlow']:0;
$rankHigh = array_key_exists('rhigh',$_REQUEST)?(int)$_REQUEST['rhigh']:0;

$returnArr = array();

$sqlWhere = '';
$sql = 'SELECT DISTINCT t.tid, t.sciname'.(!$hideAuth?',t.author':'').' FROM taxa t ';
if($taxAuthId){
    $sql .= 'INNER JOIN taxstatus ts ON t.tid = ts.tid ';
    $sqlWhere .= 'AND ts.taxauthid = '.$taxAuthId.' ';
}
if($q){
    $sqlWhere .= 'AND t.sciname LIKE "'.$q.'%" ';
}
if($rankLimit){
    $sqlWhere .= 'AND (t.rankid = '.$rankLimit.') ';
}
else{
    if($rankLow){
        $sqlWhere .= 'AND (t.rankid > '.$rankLow.' OR t.rankid IS NULL) ';
    }
    if($rankHigh){
        $sqlWhere .= 'AND (t.rankid < '.$rankHigh.' OR t.rankid IS NULL) ';
    }
}
if($sqlWhere){
    $sql .= 'WHERE '.substr($sqlWhere,4);
}
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
    $returnArr[] = $row->sciname.(!$hideAuth?' '.$row->author:'');
}
$result->free();
$con->close();
echo json_encode($returnArr, JSON_THROW_ON_ERROR);

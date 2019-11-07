<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$returnArr = Array();
$retArrRow = Array();
$queryString = $con->real_escape_string($_REQUEST['term']);
if($queryString) {
    $sql = 'SELECT DISTINCT CLID, Name ' .
        'FROM fmchecklists ' .
        "WHERE Name LIKE '%".$queryString."%' ";
    $sql .= 'LIMIT 10';
    $result = $con->query($sql);
    while ($row = $result->fetch_object()) {
        $retArrRow['label'] = htmlentities($row->Name);
        $retArrRow['value'] = $row->CLID;
        $returnArr[] = $retArrRow;
     }
}
$con->close();
echo json_encode($returnArr);

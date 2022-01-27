<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$returnArr = array();
$queryString = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT tid, sciname '.
    'FROM taxa '.
    'WHERE sciname LIKE "'.$queryString.'%" ';
//echo $sql;
$rs = $con->query($sql);
while ($row = $rs->fetch_object()) {
    $returnArr[] = $row->sciname;
}
$rs->free();
$con->close();
echo json_encode($returnArr);

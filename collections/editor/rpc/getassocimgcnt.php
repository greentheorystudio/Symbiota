<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$retCnt = 0;
$occId = (int)$_REQUEST['occid'];

$sql = 'SELECT count(*) AS imgcnt FROM images WHERE occid = '.$occId;
//echo $sql;
$result = $con->query($sql);
while($row = $result->fetch_object()) {
    $retCnt = $row->imgcnt;
}
$result->close();
$con->close();
echo $retCnt;

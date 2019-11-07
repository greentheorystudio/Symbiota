<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retCnt = 0;
$occId = $con->real_escape_string($_REQUEST['occid']);

$sql = 'SELECT count(*) AS imgcnt FROM images WHERE occid = '.$occId;
//echo $sql;
$result = $con->query($sql);
while($row = $result->fetch_object()) {
    $retCnt = $row->imgcnt;
}
$result->close();
$con->close();
echo $retCnt;

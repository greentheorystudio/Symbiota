<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$occId = $con->real_escape_string($_REQUEST['occid']);

$sql = 'SELECT cl.clid, cl.name '.
    'FROM fmvouchers v INNER JOIN fmchecklists cl ON v.clid = cl.clid '.
    'WHERE v.occid = '.$occId;
//echo $sql;
$result = $con->query($sql);
while($row = $result->fetch_object()) {
    $retArr[$row->clid] = $row->name;
}
$result->close();
$con->close();
echo json_encode($retArr);

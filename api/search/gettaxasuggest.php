<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$q = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT t.tid, t.sciname FROM taxa AS t '.
    'WHERE t.sciname LIKE "'.$q.'%" ORDER BY t.sciname ';
//echo $sql;
$result = $con->query($sql);
while ($r = $result->fetch_object()) {
    $retArr[] = array(
        'id' => $r->sciname,
        'value' => $r->tid,
        'label' => $r->sciname);
}
$con->close();
echo json_encode($retArr);

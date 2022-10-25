<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$q = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT COUNT(t.tid) AS ct, t.tid, t.sciname FROM taxa AS t '.
    'LEFT JOIN images AS i ON t.tid = i.tid ' .
	'WHERE i.tid IS NOT NULL AND (i.sortsequence < 500 OR t.rankid < 220) AND t.sciname LIKE "'.$q.'%" GROUP BY t.tid, t.sciname ';
//echo $sql;
$result = $con->query($sql);
while ($r = $result->fetch_object()) {
    $retArr[] = (object)array(
        'id' => $r->sciname,
		'value' => $r->tid,
        'label' => $r->sciname . ' ('. $r->ct . ')');
}
$con->close();
echo json_encode($retArr);

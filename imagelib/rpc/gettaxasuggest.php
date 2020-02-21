<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$q = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT count(t.tid) as ct, t.tid, t.sciname FROM taxa t '.
    ' left join images i on t.tid = i.tid ' .
	'WHERE i.tid is not null and (i.sortsequence < 500 or t.rankid < 220) and t.sciname LIKE "'.$q.'%" group by t.tid, t.sciname ';
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

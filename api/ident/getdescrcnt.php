<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../services/DbConnection.php');

$cid = (int)$_POST['cidinput'];
$cs = (array_key_exists('csinput',$_POST)?(int)$_POST['csinput']:0);

$retCnt = 0;
if($cid && $cs){
    $connection = new DbConnectionService();
    $con = $connection->getConnection();
	$sql = 'SELECT count(*) AS cnt FROM kmdescr WHERE cid = '.$cid;
	if($cs) {
        $sql .= ' AND cs = ' . $cs;
    }
	//echo $sql;
	$rs = $con->query($sql);
	while($r = $rs->fetch_object()) {
		$retCnt = $r->cnt;
	}
	$rs->free();
	$con->close();
}
echo $retCnt;

<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$cid = (int)$_POST['cidinput'];
$cs = (int)$_POST['csinput'];

$retCnt = 0;
if($cid && $cs){
    $connection = new DbConnection();
    $con = $connection->getConnection();
	$sql = 'SELECT count(*) AS cnt FROM kmcslang WHERE cid = '.$cid.' AND cs = '.$cs;
	//echo $sql;
	$rs = $con->query($sql);
	while($r = $rs->fetch_object()) {
		$retCnt = $r->cnt;
	}
	$rs->free();
	$con->close();
}
echo $retCnt;

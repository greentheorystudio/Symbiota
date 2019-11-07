<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');

$cid = $_POST['cidinput'];
$cs = $_POST['csinput'];

$retCnt = 0;
if(is_numeric($cid) && is_numeric($cs)){
    $connection = new DbConnection();
    $con = $connection->getConnection();
	$sql = 'SELECT count(*) AS cnt FROM kmcsimages WHERE cid = '.$cid.' AND cs = '.$cs;
	//echo $sql;
	$rs = $con->query($sql);
	while($r = $rs->fetch_object()) {
		$retCnt = $r->cnt;
	}
	$rs->free();
	$con->close();
}
echo $retCnt;

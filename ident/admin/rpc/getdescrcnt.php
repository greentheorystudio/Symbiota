<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');

$cid = $_POST['cidinput'];
$cs = (array_key_exists('csinput',$_POST)?$_POST['csinput']:0);

$retCnt = 0;
if(is_numeric($cid) && is_numeric($cs)){
    $connection = new DbConnection();
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

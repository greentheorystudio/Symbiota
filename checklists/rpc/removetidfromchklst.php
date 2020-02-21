<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$clid = $_REQUEST['clid'];
$tid = $_REQUEST['tid'];

if(is_numeric($clid) && is_numeric($tid)){
	if($IS_ADMIN || (array_key_exists('ClAdmin',$USER_RIGHTS) && in_array($clid, $USER_RIGHTS['ClAdmin'], true))){
		$connection = new DbConnection();
		$conn = $connection->getConnection();
		$tid = $conn->real_escape_string($tid);
		$clid = $conn->real_escape_string($clid);
		$delStatus = 'false';
		$sql = 'DELETE FROM fmchklsttaxalink WHERE chklsttaxalink.CLID = '.$clid.' AND chklsttaxalink.TID = '.$tid;
		//echo $sql;
		if($conn->query($sql)){
			echo $tid;
		}
	}
}
$conn->close();

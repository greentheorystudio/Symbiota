<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$clid = (int)$_REQUEST['clid'];
$tid = (int)$_REQUEST['tid'];

if(is_numeric($clid) && is_numeric($tid)){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
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
        $conn->close();
	}
}

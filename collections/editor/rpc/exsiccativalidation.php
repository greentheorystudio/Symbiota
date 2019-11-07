<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
$connection = new DbConnection();
$con = $connection->getConnection();
$queryTerm = $con->real_escape_string($_REQUEST['term']);
$queryTerm = str_replace('"',"''",$queryTerm);

$retStr = '';
$sql = 'SELECT ometid FROM omexsiccatititles '.
	'WHERE CONCAT_WS("",title,CONCAT(" [",abbreviation,"]")) = "'.$queryTerm.'"';
//echo $sql;
$rs = $con->query($sql);
if($r = $rs->fetch_object()) {
	$retStr = $r->ometid;
}
$rs->free();
$con->close();

echo $retStr;

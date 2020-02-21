<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$connection = new DbConnection();
$con = $connection->getConnection();
$ident = $con->real_escape_string($_REQUEST['ident']);
$collId = $con->real_escape_string($_REQUEST['collid']);

$responseStr = '';
$sql = 'SELECT loanid ' .
	'FROM omoccurloans ' .
	'WHERE loanIdentifierBorr = "'.$ident.'" AND collidBorr = '.$collId;
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
	$returnArr[] = $row->loanid;
}
$result->close();
if(!($con === false)) {
	$con->close();
}

echo json_encode($returnArr);

<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$ident = $con->real_escape_string($_REQUEST['ident']);
$collId = (int)$_REQUEST['collid'];

$returnVal = '';
$sql = 'SELECT loanid ' .
	'FROM omoccurloans ' .
	'WHERE loanIdentifierOwn = "'.$ident.'" AND collidOwn = '.$collId.' '.
    'LIMIT 1';
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
    $returnVal = $row->loanid;
}
$result->close();
if($con) {
    $con->close();
}

echo $returnVal;

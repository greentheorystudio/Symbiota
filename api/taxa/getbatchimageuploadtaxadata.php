<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();

$taxaJsonStr = $con->real_escape_string($_REQUEST['taxa']);

$returnArr = array();

$replaceChars  = array('\\', '[', ']');
$fixedTaxaStr = str_replace($replaceChars, '', $taxaJsonStr);

$sqlWhere = '';
$sql = 'SELECT DISTINCT sciname, tidaccepted FROM taxa '.
    'WHERE sciname IN('.$fixedTaxaStr.')';
//echo $sql;
$result = $con->query($sql);
while ($row = $result->fetch_object()) {
    $returnArr[$row->sciname] = (int)$row->tidaccepted;
}
$con->close();
echo json_encode($returnArr);

<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$queryString = $con->real_escape_string($_REQUEST['term']);

$sql = 'SELECT DISTINCT ometid, title, abbreviation FROM omexsiccatititles '.
    'WHERE title LIKE "%'.$queryString.'%" OR abbreviation LIKE "%'.$queryString.'%" ORDER BY title';
//echo $sql;
$result = $con->query($sql);
while ($r = $result->fetch_object()) {
    $retArr[] = '"id": '.$r->ometid.', "value":"'.str_replace('"',"''",$r->title.($r->abbreviation?' ['.$r->abbreviation.']':'')).'"';
}
$con->close();
echo '[{'.implode('},{',$retArr).'}]';

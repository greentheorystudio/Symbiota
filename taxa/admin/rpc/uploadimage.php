<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();

$q = $con->real_escape_string($_REQUEST['term']);
$rankHigh = array_key_exists('rhigh',$_REQUEST)?(int)$_REQUEST['rhigh']:0;

$returnArr = array();
$returnArr['files'] = array();
$returnArr['files'][0]['name'] = 'jpeg_1617133394_web.jpg';
$returnArr['files'][0]['error'] = json_encode(array_keys($_REQUEST));
//$returnArr['files'][0]['thumbnailUrl'] = 'https://storage.idigbio.org/ny/mycology/01926/NY-F-01926523.jpg';
$returnArr['files'][0]['jpeg_1617133394_web.jpg'] = true;
echo json_encode($returnArr);

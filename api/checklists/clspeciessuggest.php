<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );

$clid = (int)$_REQUEST['cl'];

if(SanitizerService::validateInternalRequest()){
    $returnArr = array();
    if(is_numeric($clid)){
        $connection = new DbService();
        $conn = $connection->getConnection();
        $clid = $conn->real_escape_string($clid);
        $queryString = $conn->real_escape_string($_REQUEST['term']);

        $sql = 'SELECT t.sciname '.
            'FROM taxa t INNER JOIN fmchklsttaxalink cl ON t.tid = cl.tid '.
            'WHERE sciname LIKE "'.$queryString.'%" AND cl.clid = '.$clid.' ORDER BY sciname';
        //echo $sql;
        $result = $conn->query($sql);
        while ($r = $result->fetch_object()) {
            $returnArr[] = $r->sciname;
        }
        $conn->close();
    }
    echo json_encode($returnArr);
}

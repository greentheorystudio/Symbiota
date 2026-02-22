<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );

if(SanitizerService::validateInternalRequest()){
    $connection = new DbService();
    $con = $connection->getConnection();
    $ident = $con->real_escape_string($_REQUEST['ident']);
    $collId = (int)$_REQUEST['collid'];

    $returnVal = '';
    $sql = 'SELECT exchangeid ' .
        'FROM omoccurexchange ' .
        'WHERE identifier = "'.$ident.'" AND collid = '.$collId.' '.
        'LIMIT 1';
    //echo $sql;
    $result = $con->query($sql);
    while ($row = $result->fetch_object()) {
        $returnVal = $row->exchangeid;
    }
    $result->close();
    if($con) {
        $con->close();
    }

    echo $returnVal;
}

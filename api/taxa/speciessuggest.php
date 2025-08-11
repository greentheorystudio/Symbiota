<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );

if(SanitizerService::validateInternalRequest()){
    $retArr = array();
    $connection = new DbService();
    $con = $connection->getConnection();

    $queryString = $con->real_escape_string($_REQUEST['term']);
    $taxLevel = (isset($_REQUEST['level'])?$con->real_escape_string($_REQUEST['level']):'low');

    $sql = 'SELECT tid, sciname FROM taxa WHERE sciname LIKE "'.$queryString.'%" ';
    if($taxLevel === 'species'){
        $sql .= 'AND rankid < 220';
    }
    elseif($taxLevel === 'low'){
        $sql .= 'AND rankid > 179';
    }
    else{
        $sql .= 'AND rankid < 180';
    }
    //echo $sql;
    $result = $con->query($sql);
    while ($r = $result->fetch_object()) {
        $retArr[$r->tid]['id'] = $r->tid;
        $retArr[$r->tid]['value'] = $r->sciname;
    }
    $result->free();
    $con->close();

    echo json_encode($retArr);
}

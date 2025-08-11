<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(SanitizerService::validateInternalRequest()){
    $connection = new DbService();
    $con = $connection->getConnection();
    $retCnt = 0;
    $occId = (int)$_REQUEST['occid'];

    $sql = 'SELECT count(*) AS imgcnt FROM images WHERE occid = '.$occId;
    //echo $sql;
    $result = $con->query($sql);
    while($row = $result->fetch_object()) {
        $retCnt = $row->imgcnt;
    }
    $result->close();
    $con->close();
    echo $retCnt;
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
header('Content-Type: application/json; charset=UTF-8' );

$connection = new DbService();
$con = $connection->getConnection();
$retArr = array();
$term = trim($con->real_escape_string($_REQUEST['term']));
if($term){
	if(is_numeric($term)){
        $sql = 'SELECT DISTINCT tid, author, family, securitystatus '.
            'FROM taxa WHERE tid = '.$term.' ';
    }
    else{
        $sql = 'SELECT DISTINCT tid, author, family, securitystatus '.
            'FROM taxa WHERE sciname = "'.$term.'" ';
    }
    //echo $sql;
    $rs = $con->query($sql);
    while ($r = $rs->fetch_object()) {
        $retArr['tid'] = $r->tid;
        $retArr['family'] = $r->family;
        $retArr['author'] = $r->author;
        $retArr['status'] = $r->securitystatus;
    }
    $rs->free();
    $con->close();
}

if($retArr){
    echo json_encode($retArr);
}

<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$term = trim($con->real_escape_string($_REQUEST['term']));
if($term){
	if(is_numeric($term)){
        $sql = 'SELECT DISTINCT tidaccepted, author, family, securitystatus '.
            'FROM taxa WHERE tid = '.$term.' ';
    }
    else{
        $sql = 'SELECT DISTINCT tidaccepted, author, family, securitystatus '.
            'FROM taxa WHERE sciname = "'.$term.'" ';
    }
    //echo $sql;
    $rs = $con->query($sql);
    while ($r = $rs->fetch_object()) {
        $retArr['tid'] = $r->tidaccepted;
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

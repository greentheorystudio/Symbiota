<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/DbConnection.php');
header('Content-Type: application/json; charset=' .$GLOBALS['CHARSET']);

$connection = new DbConnection();
$con = $connection->getConnection();
$retArr = array();
$term = trim($con->real_escape_string($_REQUEST['term']));
if($term){
	if(is_numeric($term)){
        $sql = 'SELECT DISTINCT ts.tidaccepted, t.author, ts.family, t.securitystatus '.
            'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
            'WHERE t.tid = '.$term.' ';
    }
    else{
        $sql = 'SELECT DISTINCT ts.tidaccepted, t.author, ts.family, t.securitystatus '.
            'FROM taxa AS t INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
            'WHERE t.sciname = "'.$term.'" ';
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

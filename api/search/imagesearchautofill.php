<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(SanitizerService::validateInternalRequest()){
    $connection = new DbService();
    $con = $connection->getConnection();
    $returnArr = array();
    $queryString = $con->real_escape_string($_REQUEST['q']);
    $type = $con->real_escape_string($_REQUEST['t']);
    if($queryString && $type){
        $sql = '';
        if($type === 'taxa'){
            $sql = 'SELECT DISTINCT sciname ' .
                'FROM taxa ' .
                "WHERE sciname LIKE '".$queryString."%' ".
                'LIMIT 10 ';
            $result = $con->query($sql);
            $i = 0;
            while ($row = $result->fetch_object()) {
                $returnArr[$i]['name'] = htmlentities($row->sciname);
                $i++;
            }
        }
        if($type === 'common'){
            $sql = 'SELECT DISTINCT VernacularName ' .
                'FROM taxavernaculars ' .
                "WHERE VernacularName LIKE '".$queryString."%' ".
                'LIMIT 10 ';
            $result = $con->query($sql);
            $i = 0;
            while ($row = $result->fetch_object()) {
                $returnArr[$i]['name'] = htmlentities($row->VernacularName);
                $i++;
            }
        }
        if($type === 'country'){
            $sql = 'SELECT DISTINCT country ' .
                'FROM omoccurrences ' .
                "WHERE country LIKE '".$queryString."%' ".
                'LIMIT 10 ';
            $result = $con->query($sql);
            $i = 0;
            while ($row = $result->fetch_object()) {
                $returnArr[$i]['name'] = htmlentities($row->country);
                $i++;
            }
        }
        if($type === 'state'){
            $sql = 'SELECT DISTINCT stateProvince ' .
                'FROM omoccurrences ' .
                "WHERE stateProvince LIKE '".$queryString."%' ".
                'LIMIT 10 ';
            $result = $con->query($sql);
            $i = 0;
            while ($row = $result->fetch_object()) {
                $returnArr[$i]['name'] = htmlentities($row->stateProvince);
                $i++;
            }
        }
        if($type === 'photographer'){
            $retArrRow = array();
            $sql = "SELECT DISTINCT u.uid, CONCAT_WS(' ',u.firstname,u.lastname) AS fullname ".
                'FROM images AS i LEFT JOIN users AS u ON i.photographeruid = u.uid ' .
                "WHERE u.firstname LIKE '".$queryString."%' OR u.lastname LIKE '".$queryString."%' ".
                'ORDER BY fullname ' .
                'LIMIT 10 ';
            $result = $con->query($sql);
            $i = 0;
            while ($row = $result->fetch_object()) {
                $returnArr[$i]['name'] = htmlentities($row->fullname);
                $returnArr[$i]['id'] = htmlentities($row->uid);
                $i++;
            }
        }
    }
    $con->close();
    echo json_encode($returnArr);
}

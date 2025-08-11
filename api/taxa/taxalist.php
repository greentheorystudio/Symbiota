<?php
include_once(__DIR__ . '/../../services/DbService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(SanitizerService::validateInternalRequest()){
    $connection = new DbService();

    $con = $connection->getConnection();
    $returnArr = array();
    $queryString = array_key_exists('term',$_REQUEST) ? $con->real_escape_string($_REQUEST['term']) : $con->real_escape_string($_REQUEST['q']);
    $type = array_key_exists('t',$_REQUEST)?$_REQUEST['t']:'';
    if($queryString) {
        if(is_numeric($type)){
            $type = (int)$type;
            $sql = '';
            if($type === 5){
                $sql = 'SELECT DISTINCT v.vernacularname AS sciname FROM taxavernaculars AS v ' .
                    "WHERE v.vernacularname LIKE '%".$queryString."%' ".
                    'limit 50 ';
            }
            elseif($type === 4){
                $sql = 'SELECT sciname FROM taxa ' .
                    "WHERE rankid > 20 AND rankid < 140 AND sciname LIKE '".$queryString."%' ".
                    'LIMIT 20';
            }
            elseif($type === 2){
                $sql = 'SELECT DISTINCT family AS sciname FROM taxa ' .
                    "WHERE family LIKE '".$queryString."%' ".
                    'LIMIT 20 ';
            }
            else{
                $sql = 'SELECT DISTINCT sciname FROM taxa ' .
                    "WHERE sciname LIKE '".$queryString."%' ";
                if($type === 3){
                    $sql .= 'AND rankid > 140 ';
                }
                else{
                    $sql .= 'AND rankid >= 140 ';
                }
                $sql .= 'LIMIT 20';
            }
        }
        else{
            $sql = 'SELECT DISTINCT t.tidaccepted, t.SciName, v.VernacularName '.
                'FROM taxa AS t LEFT JOIN taxavernaculars AS v ON t.TID = v.TID '.
                'WHERE (t.SciName LIKE "'.$queryString.'%" OR v.VernacularName LIKE "'.$queryString.'%") AND t.RankId < 185 '.
                'LIMIT 10 ';
        }
        $result = $con->query($sql);
        if($type === 'single'){
            while ($row = $result->fetch_object()) {
                $sciName = $row->SciName;
                if($row->VernacularName){
                    $sciName .= ' ('.$row->VernacularName.')';
                }
                $retArrRow['label'] = htmlentities($sciName);
                $retArrRow['value'] = $row->tidaccepted;
                $returnArr[] = $retArrRow;
            }
        }
        elseif($type === 'batch'){
            $i = 0;
            while ($row = $result->fetch_object()) {
                $sciName = $row->SciName;
                if($row->VernacularName){
                    $sciName .= ' ('.$row->VernacularName.')';
                }
                $returnArr[$i]['name'] = htmlentities($sciName);
                $returnArr[$i]['id'] = htmlentities($row->tidaccepted);
                $i++;
            }
        }
        else{
            while ($row = $result->fetch_object()) {
                $returnArr[] = htmlentities($row->sciname);
            }
        }
    }
    $con->close();
    echo json_encode($returnArr);
}

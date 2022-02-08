<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');

$connection = new DbConnection();
$con = $connection->getConnection();
$returnArr = array();
$queryString = array_key_exists('term',$_REQUEST)?$con->real_escape_string($_REQUEST['term']):$con->real_escape_string($_REQUEST['q']);
$type = $con->real_escape_string($_REQUEST['t']);
if($queryString) {
	$sql = 'SELECT DISTINCT ts.tidaccepted, t.SciName, v.VernacularName '.
		'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
		'LEFT JOIN taxavernaculars AS v ON t.TID = v.TID '.
		'WHERE (t.SciName LIKE "'.$queryString.'%" OR v.VernacularName LIKE "'.$queryString.'%") AND t.RankId < 185 '.
		'LIMIT 10 ';
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
	if($type === 'batch'){
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
}
$con->close();
echo json_encode($returnArr);

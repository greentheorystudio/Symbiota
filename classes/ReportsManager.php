<?php
include_once(__DIR__ . '/DbConnection.php');

class ReportsManager{

	private $conn;

	public function __construct(){
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}
 
	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

	public function getNewIdentByDeterminerReport(): array
    {
        $retArr = array();
        $sql = 'SELECT COUNT(*) as numberOfDet, identifiedby FROM omoccurdeterminations WHERE ((dateIdentified '.
         'like "%2013%") OR (dateIdentified like "%2014%") OR (dateIdentified like "%2015%")) AND sciname like "% %" GROUP BY identifiedby;';

        $rs = $this->conn->query($sql);
        if($rs){
            while($r = $rs->fetch_assoc()){
                $retArr[] = $r;
            }
            $rs->free();
        }

        return $retArr;
    }

	public function getNewIdentBySpecialistReport(): array
    {
		$retArr = array();
		$sql = 'SELECT CONCAT_WS(" ", firstname, lastname) as fullname, t.sciname AS family, c.numberOfDet FROM usertaxonomy ut ' .
            'INNER JOIN users u ON ut.uid = u.uid INNER JOIN taxa t ' .
            'ON ut.tid = t.tid INNER JOIN taxstatus ts ON t.tid = ts.tid INNER JOIN (SELECT ts.family, count(*) '.
            'as numberOfDet FROM omoccurdeterminations d INNER JOIN taxa t ON d.sciname = t.sciname INNER JOIN '.
            'taxstatus ts ON t.tid = ts.tid WHERE (t.rankid IN(220,230,240,260)) AND ((dateIdentified LIKE "%2013%") OR (dateIdentified '.
            'LIKE "%2014%") OR (dateIdentified LIKE "%2015%")) GROUP BY ts.family) c ON c.family = t.sciname GROUP BY ut.idusertaxonomy ORDER BY '.
            'u.lastname, u.firstname, t.sciname;';

		$rs = $this->conn->query($sql);
		if($rs){
            while($r = $rs->fetch_assoc()){
                $retArr[] = $r;
            }
			$rs->free();
        }

        return $retArr;
	}

    public function getNewIdentByFamilyReport(): array
    {
        $retArr = array();
        $sql = 'SELECT ts.family, count(*) as numberOfDet FROM omoccurdeterminations d INNER JOIN taxa t '.
            'ON d.sciname = t.sciname INNER JOIN taxstatus ts ON t.tid = ts.tid WHERE (t.rankid IN(220,230,240,260)) '.
            'AND (dateIdentified LIKE "%2013%" OR dateIdentified LIKE "%2014%" OR dateIdentified LIKE "%2015%") AND family '.
            'IS NOT NULL GROUP BY ts.family;';

        $rs = $this->conn->query($sql);
        if($rs){
            while($r = $rs->fetch_assoc()){
                $retArr[] = $r;
            }
            $rs->free();
        }

        return $retArr;
    }
}

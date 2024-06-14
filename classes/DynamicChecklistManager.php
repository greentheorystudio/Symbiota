<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');

class DynamicChecklistManager {

	private $conn;

	public function __construct(){
        $connection = new DbConnectionService();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function createChecklist($lat, $lng, $radius, $groundRadius, $radiusUnits, $tidFilter): int
    {
		if($radiusUnits === 'mi') {
			$radius = round($radius * 1.6);
		}
		$dynPk = 0;
        $whereRadius = $groundRadius * 0.621371192;
		$sql = 'INSERT INTO fmdynamicchecklists(name,details,expiration,uid) '.
			'VALUES ("'.round($lat,5).' '.round($lng,5).' within '.round($radius,1).' km","'.$lat.' '.$lng.' within '.$radius.' km","'.
			date('Y-m-d',mktime(0, 0, 0, date('m'), date('d') + 7, date('Y'))).'",'.($GLOBALS['SYMB_UID']?:'NULL').')';
		//echo $sql;
		if($this->conn->query($sql)){
			$dynPk = $this->conn->insert_id;
			$sql2 = 'INSERT INTO fmdyncltaxalink (dynclid, tid) '.
				'SELECT DISTINCT '.$dynPk.' AS dynpk, IF(t2.rankid=220,t2.tid,t2.parenttid) AS tid '.
				'FROM omoccurrences AS o INNER JOIN taxa AS t ON o.tid = t.tid '.
				'INNER JOIN taxa AS t2 ON t.tidaccepted = t2.tid ';
			if($tidFilter){
				$sql2 .= 'INNER JOIN taxaenumtree AS e ON t2.tid = e.tid ';
			}
			$sql2 .= 'WHERE o.tid IS NOT NULL AND o.decimalLatitude IS NOT NULL AND o.decimalLongitude IS NOT NULL AND (t2.rankid IN(220,230,240,260)) '.
				'AND ((3959 * ACOS(COS(RADIANS(o.decimalLatitude)) * COS(RADIANS('.$lat.')) * COS(RADIANS('.$lng.') - RADIANS(o.decimalLongitude)) + SIN(RADIANS(o.decimalLatitude)) * SIN(RADIANS('.$lat.')))) <= '.$whereRadius.') ';
            if($tidFilter){
				$sql2 .= 'AND e.parentTid = '.$tidFilter;
			}
			//echo $sql2; exit;
			$this->conn->query($sql2);
		}

		return $dynPk;
	}
	
	public function removeOldChecklists(): void
	{
		$sql1 = 'DELETE dcl.* '.
			'FROM fmdyncltaxalink AS dcl INNER JOIN fmdynamicchecklists AS dc ON dcl.dynclid = dc.dynclid '.
			'WHERE dc.expiration < NOW()';
		$this->conn->query($sql1);
		$sql2 = 'DELETE FROM fmdynamicchecklists WHERE expiration < NOW()';
		$this->conn->query($sql2);
	} 
}

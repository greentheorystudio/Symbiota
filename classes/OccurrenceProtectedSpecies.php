<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');

class OccurrenceProtectedSpecies extends OccurrenceMaintenance {

 	private $taxaArr = array();

 	public function __construct(){
		parent::__construct();
    }

    public function getProtectedSpeciesList(): array
    {
 		$returnArr = array();
		$sql = 'SELECT DISTINCT t.tid, ts.Family, t.SciName, t.Author, t.SecurityStatus FROM taxa AS t INNER JOIN taxstatus AS ts ON t.TID = ts.tid ';
		if($this->taxaArr) {
            $sql .= 'INNER JOIN taxaenumtree e ON t.tid = e.tid ';
        }
		$sql .= 'WHERE (ts.taxauthid = 1) AND (t.SecurityStatus > 0) ';
		if($this->taxaArr) {
            $sql .= 'AND (e.parenttid IN(' . implode(',', $this->taxaArr) . ') OR t.tid IN(' . implode(',', $this->taxaArr) . ')) ';
        }
		$sql .= 'ORDER BY ts.Family, t.SciName';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$returnArr[$row->Family][$row->tid]['sciname'] = $row->SciName;
			$returnArr[$row->Family][$row->tid]['author'] = $row->Author;
			$returnArr[$row->Family][$row->tid]['status'] = $row->SecurityStatus;
		}
		$rs->free();
		return $returnArr;
	}

	public function addSpecies($tid){
		$protectCnt = 0;
		if(is_numeric($tid)){
	 		$sql = 'UPDATE taxa t SET t.SecurityStatus = 1 WHERE (t.tid = '.$tid.')';
	 		//echo $sql;
			$this->conn->query($sql);
			$protectCnt = $this->protectGlobalSpecies();
		}
		return $protectCnt;
	}

	public function deleteSpecies($tid){
		$protectCnt = 0;
		if(is_numeric($tid)){
			$sql = 'UPDATE taxa t SET t.SecurityStatus = 0 WHERE (t.tid = '.$tid.')';
	 		//echo $sql;
			$this->conn->query($sql);
			$sql2 = 'UPDATE omoccurrences o INNER JOIN taxstatus ts1 ON o.tidinterpreted = ts1.tid '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
				'INNER JOIN taxa t ON ts2.tid = t.tid '.
				'SET o.LocalitySecurity = 0 '.
				'WHERE (t.tid = '.$tid.') AND (o.localitySecurityReason IS NULL) ';
			//echo $sql2; exit;
			$this->conn->query($sql2);
			$protectCnt = $this->protectGlobalSpecies();
		}
		return $protectCnt;
	}

	public function getStateList(): array
    {
		$retArr = array();
		$sql = 'SELECT DISTINCT c.clid, c.name, c.locality, c.authors, c.access '.
			'FROM fmchecklists c INNER JOIN fmchklsttaxalink l ON c.clid = l.clid '.
			'WHERE c.type = "rarespp" ';
		if($this->taxaArr){
			$sql .= 'AND l.tid IN('.implode(',', $this->taxaArr).') ';
		}
		$sql .= 'ORDER BY c.locality';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->clid]['name'] = $r->name;
			$retArr[$r->clid]['locality'] = $r->locality;
			$retArr[$r->clid]['authors'] = $r->authors;
			$retArr[$r->clid]['access'] = $r->access;
		}
		$rs->free();
		return $retArr;
	}

	public function setTaxonFilter($searchTaxon): void
    {
		$sql = 'SELECT ts.tidaccepted FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid WHERE t.sciname LIKE "'.$searchTaxon.'%" AND ts.taxauthid = 1';
		$rs = $this->conn->query($sql);
		if($rs) {
			while($r = $rs->fetch_object()){
				$this->taxaArr[] = $r->tidaccepted;
			}
		}
		$rs->free();

		if($this->taxaArr){
			$sql = 'SELECT tid  FROM taxstatus WHERE tidaccepted IN('.implode(',',$this->taxaArr). ')';
			$rs = $this->conn->query($sql);
			if($rs) {
				while($r = $rs->fetch_object()){
					$this->taxaArr[] = $r->tid;
				}
			}
			$rs->free();
		}
		else{
            $this->taxaArr[] = 0;
        }
	}

	public function getSpecimenCnt(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(*) AS cnt FROM omoccurrences WHERE (LocalitySecurity > 0)';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}
}
?>

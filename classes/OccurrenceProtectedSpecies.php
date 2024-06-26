<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');
include_once(__DIR__ . '/OccurrenceTaxonomyCleaner.php');

class OccurrenceProtectedSpecies extends OccurrenceMaintenance {

 	private $taxaArr = array();

 	public function __construct(){
		parent::__construct();
    }

    public function getProtectedSpeciesList(): array
    {
 		$returnArr = array();
		$sql = 'SELECT DISTINCT t.tid, t.family, t.SciName, t.Author, t.SecurityStatus FROM taxa AS t ';
		if($this->taxaArr) {
            $sql .= 'INNER JOIN taxaenumtree AS e ON t.tid = e.tid ';
        }
		$sql .= 'WHERE (t.SecurityStatus > 0) ';
		if($this->taxaArr) {
            $sql .= 'AND (e.parenttid IN(' . implode(',', $this->taxaArr) . ') OR t.tid IN(' . implode(',', $this->taxaArr) . ')) ';
        }
		$sql .= 'ORDER BY t.family, t.SciName';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$returnArr[$row->family][$row->tid]['sciname'] = $row->SciName;
			$returnArr[$row->family][$row->tid]['author'] = $row->Author;
			$returnArr[$row->family][$row->tid]['status'] = $row->SecurityStatus;
		}
		$rs->free();
		return $returnArr;
	}

	public function addSpecies($tid){
		$protectCnt = 0;
		if(is_numeric($tid)){
	 		$sql = 'UPDATE taxa AS t SET t.SecurityStatus = 1 WHERE (t.tid = '.$tid.')';
	 		//echo $sql;
			$this->conn->query($sql);
			$protectCnt = (new OccurrenceTaxonomyCleaner)->protectGlobalSpecies();
		}
		return $protectCnt;
	}

	public function deleteSpecies($tid){
		$protectCnt = 0;
		if(is_numeric($tid)){
			$sql = 'UPDATE taxa AS t SET t.SecurityStatus = 0 WHERE t.tid = '.$tid.' ';
	 		//echo $sql;
			$this->conn->query($sql);
			$sql2 = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.tid '.
				'SET o.LocalitySecurity = 0 '.
				'WHERE t.tidaccepted = '.$tid.' AND ISNULL(o.localitySecurityReason) ';
			//echo $sql2; exit;
			$this->conn->query($sql2);
			$protectCnt = (new OccurrenceTaxonomyCleaner)->protectGlobalSpecies();
		}
		return $protectCnt;
	}

	public function getStateList(): array
    {
		$retArr = array();
		$sql = 'SELECT DISTINCT c.clid, c.name, c.locality, c.authors, c.access '.
			'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS l ON c.clid = l.clid '.
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
		$sql = 'SELECT tidaccepted FROM taxa WHERE sciname LIKE "'.$searchTaxon.'%"';
		$rs = $this->conn->query($sql);
		if($rs) {
			while($r = $rs->fetch_object()){
				$this->taxaArr[] = $r->tidaccepted;
			}
		}
		$rs->free();

		if($this->taxaArr){
			$sql = 'SELECT tid FROM taxa WHERE tidaccepted IN('.implode(',',$this->taxaArr). ') ';
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

	public function getOccRecordCnt(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(occid) AS cnt FROM omoccurrences WHERE LocalitySecurity > 0 ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}
}

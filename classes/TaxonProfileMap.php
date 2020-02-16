<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');

class TaxonProfileMap {
	
	private $tid;
	private $sciName;
	private $taxaMap = array();
	private $taxArr = array();
	private $synMap = array();
	private $childLoopCnt = 0;
	private $sqlWhere = '';

    public function __construct(){
		$connection = new DbConnection();
    	$this->conn = $connection->getConnection();
    }

	public function __destruct(){
 		if(!($this->conn === false)) {
			$this->conn->close();
		}
	}
	
	public function setTaxon($tValue): void
	{
		if($tValue){
			$taxonValue = $this->conn->real_escape_string($tValue);
			$sql = 'SELECT t.tid, t.sciname FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tidaccepted ';
			if(is_numeric($taxonValue)){
				$sql .= 'WHERE (ts.tid = '.$taxonValue.') AND (ts.taxauthid = 1)';
			}
			else{
				$sql .= 'INNER JOIN taxa t2 ON ts.tid = t2.tid WHERE (t2.sciname = "'.$taxonValue.'") AND (ts.taxauthid = 1)';
			}
			//echo '<div>'.$sql.'</div>';
			$result = $this->conn->query($sql);
			while($r = $result->fetch_object()){
				$this->tid = $r->tid;
				$this->sciName = $r->sciname;
			}
			$result->close();
			if($this->tid){
				$this->taxArr[$this->tid] = $this->sciName;
				$this->taxArr += $this->getChildren(array($this->tid));
				$taxaKeys = array_keys($this->taxArr);
				$this->synMap = array_combine($taxaKeys,$taxaKeys);
				$this->setTaxaSynonyms($taxaKeys);
			}
		}
	}

    private function getChildren($inArr): array
	{
		$retArr = array();
		if($inArr){
			$sql = 'SELECT t.tid, t.sciname FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.tid '.
				'WHERE ts.taxauthid = 1 AND ts.parenttid IN('.implode(',',$inArr).') AND (ts.tid = ts.tidaccepted)';
			//echo '<div>SQL: '.$sql.'</div>';
	        $rs = $this->conn->query($sql);
	        while($r = $rs->fetch_object()){
	        	$retArr[$r->tid] = $r->sciname;
	        }
			$rs->close();
			if($retArr && $this->childLoopCnt < 5 && count(array_intersect($retArr,$inArr)) < count($retArr)){
				$retArr += $this->getChildren(array_keys($retArr));
			}
			$this->childLoopCnt++;
		}
		return $retArr;
	}

	private function setTaxaSynonyms($inArray): void
	{
		if($inArray){
			$sql = 'SELECT s.tid, s.tidaccepted, t.SciName FROM taxa t LEFT JOIN taxstatus s on t.TID = s.tid '.
				'WHERE s.taxauthid = 1 AND s.tidaccepted IN('.implode(',',$inArray).') AND (s.tid <> s.tidaccepted)';
			//echo '<div>SQL: '.$sql.'</div>';
	        $rs = $this->conn->query($sql);
	        while($r = $rs->fetch_object()){
	        	$this->synMap[$r->tid] = $r->tidaccepted;
				$this->taxArr[$r->tid] = $r->SciName;
	        }
			$rs->close();
		}
	}
	
	private function getTaxaWhere(): string
	{
		global $USER_RIGHTS;
		$sql = '';
		$sql .= 'WHERE (o.tidinterpreted IN('.implode(',',array_keys($this->synMap)).')) '.
			'AND (o.decimallatitude IS NOT NULL AND o.decimallongitude IS NOT NULL) ';
		if(!array_key_exists('SuperAdmin',$USER_RIGHTS) && !array_key_exists('CollAdmin',$USER_RIGHTS) &&
			!array_key_exists('RareSppAdmin',$USER_RIGHTS) && !array_key_exists('RareSppReadAll',$USER_RIGHTS) && array_key_exists('RareSppReader',$USER_RIGHTS)){
			$sql .= 'AND ((o.CollId IN ('.implode(',',$USER_RIGHTS['RareSppReader']).')) OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
		}
		return $sql;
	}
	
	public function getTaxaMap(): array
	{
		foreach($this->taxArr as $key => $taxonName){
        	$this->taxArr[$taxonName] = array();
		}
		return $this->taxaMap;
	}

	public function getSynMap(): array
	{
		return $this->synMap;
	}

	public function getTaxaSqlWhere(): string
	{
		$this->sqlWhere = $this->getTaxaWhere();
		return $this->sqlWhere;
	}

	public function getTaxaArr(): array
	{
    	return $this->taxArr;
    }
}

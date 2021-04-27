<?php
include_once(__DIR__ . '/KeyManager.php');

class KeyEditorManager extends KeyManager{

	private $tid;
	private $taxonName;
	private $chars = array();
	private $charStates = array();
	private $selectedStates = array();
	private $parentTid;
	private $rankId;
	private $charDepArray = array();

	public function setTid($t): void
	{
		if(is_numeric($t)){
			$this->tid = $t;
			$sql = 'SELECT t.SciName, ts.ParentTID, t.RankId ' .
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid WHERE ts.taxauthid = 1 AND (t.TID = ' .$this->tid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->taxonName = $row->SciName;
				$this->parentTid = $row->ParentTID;
				$this->rankId = $row->RankId;
		    }
			$result->free();
		}
	}

	public function getTaxonName(){
		return $this->taxonName;
	}
	
	public function getTid(){
		return $this->tid;
	}
	
	public function getParentTid(){
		return $this->parentTid;
	}
	
	public function getRankId(){
		return $this->rankId;
	}

	public function isSelected($str): bool
	{
		return $this->selectedStates && array_key_exists($str, $this->selectedStates);
	}

	public function getInheritedStr($str){
		if($this->selectedStates && array_key_exists($str, $this->selectedStates)){
			return $this->selectedStates[$str];
		}

		return '';
	}

	public function getCharList(): array
	{
		$this->setCharList();
		$this->setSelectedStates();
		return $this->chars;
	}
	
	private function setCharList(): void
	{
		$cidArray = array();
		$parentStr = implode(',',$this->getParentArr($this->tid));
		$sql = 'SELECT c.CharName, c.CID, ch.headingname, dep.CIDDependance, dep.CSDependance '.
			'FROM ((kmcharacters c INNER JOIN kmchartaxalink ctl ON c.CID = ctl.CID) '.
			'INNER JOIN kmcharheading ch ON c.hid = ch.hid) LEFT JOIN kmchardependence dep ON c.CID = dep.CID '.
			'WHERE ((ch.language = "'.$this->language.'") AND (c.CID Not In (SELECT DISTINCT chartl.CID FROM kmchartaxalink chartl '.
			'WHERE (chartl.TID In ('.$parentStr.')) AND (chartl.Relation="exclude"))) '.
			'AND (c.chartype = "UM" Or c.chartype="OM") AND (ctl.TID In ('.$parentStr.')) AND '.
			'(c.defaultlang="'.$this->language.'") AND (ctl.Relation="include")) '.
			'ORDER BY c.SortSequence';
		//echo $sql;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$charKey = $row->CID;
			$cidArray[$charKey] = '';
			$charValue = $row->CharName;
			$charHeading = $row->headingname;
			$pos = strpos($charHeading, ':');
			if($pos) {
				$charHeading = substr($charHeading, $pos + 2);
			}
			if($charHeading){
                $this->chars[$charHeading][$charKey] = $charValue;
            }

			$cidDepValue = $row->CIDDependance;
			$csDepValue = $row->CSDependance;
			if($cidDepValue) {
				$this->charDepArray[$charKey] = $cidDepValue . ':' . $csDepValue;
			}
			
		}
		if($cidArray) {
			$this->setCharStates(implode(',', array_keys($cidArray)));
		}
		$result->free();
	}
	
	public function getCharDepArray(): array
	{
		return $this->charDepArray;
	}

	private function setCharStates($cidStr): void
	{
		$sql = 'SELECT cid, cs, charstatename FROM kmcs WHERE (cid In ('.$cidStr.')) ORDER BY sortSequence, cs';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$this->charStates[$row->cid][$row->cs] = $row->charstatename;
	    }
		$result->free();
	}
	
	public function getCharStates(): array
	{
		return $this->charStates;
	}

	private function setSelectedStates(): void
	{
		$sql = 'SELECT d.CID, d.CS, d.Inherited FROM kmdescr d WHERE (d.TID = '.$this->tid.')';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$cid = $row->CID;
			$cs = $row->CS;
			$this->selectedStates[$cid. '_' .$cs] = ($row->Inherited? ' (i)' : '');
		}
		$result->free();
	}

	public function processTaxa($addStates,$removeStates): void
	{
		$aStates = $this->processStateArr($addStates);
		$rStates = $this->processStateArr($removeStates);
		$charUsedStr = implode(',',array_unique(array_merge(array_keys($aStates),array_keys($rStates))));

		if($charUsedStr) {
			$this->deleteInheritance($this->tid, $charUsedStr);
		}
		
		if($rStates){
			foreach($rStates as $cid => $csArr){
				foreach($csArr as $cs){
					$this->deleteDescr($this->tid, $cid, $cs);
				}
			}
		}

		if($aStates){
			foreach($aStates as $cid => $csArr){
				foreach($csArr as $cs){
					$this->insertDescr($this->tid, $cid, $cs);
				}
			}
		}

		$this->resetInheritance($this->tid, $charUsedStr);
	}

	private function processStateArr($stateArr): array
	{
		$retArr = array();
		if($stateArr){
			foreach($stateArr as $value){
				$tok = explode('_',$value);
				if($tok){
                    $cid = $tok[0];
                    $cs = $tok[1];
                    $retArr[$cid][] = $cs;
                }
			}
		}
		return $retArr;
	} 
}

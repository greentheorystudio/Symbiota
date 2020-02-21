<?php

class TaxonomyCleanerOccurrences extends TaxonomyCleaner{

	private $collId;

	public function linkSciNames($collId): void
	{
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE o.tidinterpreted IS NULL ';
		if($collId && is_numeric($collId)) {
			$sql .= 'AND (o.collid = ' . $collId . ')';
		}
		$this->conn->query($sql);
	}

	public function verifyCollectionTaxa($collId){
		$numGood = 0;
		$numBad = 0;
		$sql = 'SELECT DISTINCT o.sciname FROM omoccurrences o '.
			'WHERE o.tidinterpreted IS NULL AND o.sciname IS NOT NULL ';
		if($collId && is_numeric($collId)) {
			$sql .= 'AND (o.collid = ' . $collId . ') ';
		}
		$sql .= 'ORDER BY o.sciname LIMIT 1';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->sciname){
				$externalTaxonObj = $this->getTaxonObjSpecies2000($r->sciname);
				if($externalTaxonObj){
					$numGood++;
				}
				else{
					$numBad++;
					$sql = 'UPDATE omoccurrences SET taxonstatus = 1 WHERE (sciname = "'.$r->sciname.'") AND tidinterpreted IS NULL ';
					$this->conn->query($sql);
				}
			}
		}
		$rs->close();
		$retArr['good'] = $numGood;
		$retArr['bad'] = $numBad;
		return $retArr;
	}

	private function getHierarchy($tid){
		$parentArr = Array($tid);
		$parCnt = 0;
		$targetTid = $tid;
		do{
			$sqlParents = 'SELECT IFNULL(ts.parenttid,0) AS parenttid FROM taxstatus ts WHERE (ts.tid = '.$targetTid.')';
			//echo "<div>".$sqlParents."</div>";
			$resultParent = $this->conn->query($sqlParents);
			if($rowParent = $resultParent->fetch_object()){
				$parentTid = $rowParent->parenttid;
				if($parentTid) {
					$parentArr[$parentTid] = $parentTid;
				}
			}
			else{
				break;
			}
			$resultParent->close();
			$parCnt++;
			if($targetTid === $parentTid) {
				break;
			}
			$targetTid = $parentTid;
		}
		while($targetTid && $parCnt < 16);
		
		return implode(',',array_reverse($parentArr));
	}

	public function getCollectionName(){
		$retStr = '';
		$sql = 'SELECT institutioncode, collectioncode, collectionname '.
			'FROM omcollections WHERE (collid = '.$this->collId.') ';
		if($rs = $this->conn->query($sql)){
			if($row = $rs->fetch_object()){
				$retStr = $row->collectionname;
				if($row->institutioncode) {
					$retStr .= ' (' . $row->institutioncode . ($row->collectioncode ? ':' . $row->collectioncode : '') . ')';
				}
			}
			$rs->close();
		}
		return $retStr;
	}
	
	public function getTaxaList($index = 0): array
	{
		$retArr = array();
		$sql = 'SELECT sciname '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collId.') AND tidinterpreted IS NULL '.
			'ORDER BY sciname '.
			'LIMIT '.$index.',500 ';
		if($rs = $this->conn->query($sql)){
			if($row = $rs->fetch_object()){
				$retArr[] = $row->sciname;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function analyzeTaxa($startIndex = 0, $limit = 10){
		$retArr = array();
		$sql = 'SELECT sciname '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collId.') AND tidinterpreted IS NULL '.
			'ORDER BY sciname '.
			'LIMIT '.$startIndex.','.$limit;
		if($rs = $this->conn->query($sql)){
			if($row = $rs->fetch_object()){
				$sn = $row->sciname;
				$sxArr[$sn] = $sn;
				$sxArr = $this->getSoundexMatch($sn);
				if($sxArr) {
					$retArr[$sn]['soundex'] = $sxArr;
				}
				
			}
			$rs->close();
		}

		return $retArr;
	}

	public function getTaxaCount(){
		$retStr = '';
		$sql = 'SELECT count(DISTINCT sciname) AS taxacnt '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collId.') AND tidinterpreted IS NULL ';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			if($row = $rs->fetch_object()){
				$retStr = $row->taxacnt;
			}
			$rs->close();
		}
		return $retStr;
	}
	
	public function setCollId($id): void
	{
		if(is_numeric($id)){
			$this->collId = $id;
		}
	}
}

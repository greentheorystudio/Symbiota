<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceAttributes extends Manager {

	private $collidStr = '';
	private $tidFilter;
	private $traitArr = array();
	private $targetOccid = 0;
	private $attrNotes = '';
	private $sqlBody = '';

	public function __construct(){
		parent::__construct();
	}

	public function saveAttributes($postArr,$notes,$uid): bool
	{
		if(!is_numeric($uid)){
			$this->errorMessage = 'ERROR saving occurrence attribute: bad input values; ';
			return false;
		}
		$status = true;
		$stateArr = array();
		foreach($postArr as $postKey => $postValue){
			if(strncmp($postKey, 'stateid-', 8) === 0){
				if(is_array($postValue)){
					foreach($postValue as $val){
						$stateArr[] = $val;
					}
				}
				else{
					$stateArr[] = $postValue;
				}
			}
		}
		foreach($stateArr as $stateId){
			if(is_numeric($stateId)){
				$sql = 'INSERT INTO tmattributes(stateid,occid,notes,createduid) VALUES('.$stateId.','.$this->targetOccid.',"'.SanitizerService::cleanInStr($this->conn,$notes).'",'.$uid.') ';
				if(!$this->conn->query($sql)){
					$this->errorMessage .= 'ERROR saving occurrence attribute.';
					$status = false;
				}
			}
			else{
				$this->errorMessage .= 'ERROR saving occurrence attribute: bad input values ('.$stateId.'); ';
				$status = false;
			}
		}
		return $status;
	}

	public function getImageUrls(): array
	{
		$retArr = array();
		if($this->collidStr){
			if(!$this->sqlBody) {
				$this->setSqlBody();
			}
			$sql = 'SELECT i.occid, IFNULL(o.catalognumber, o.othercatalognumbers) AS catnum '.
				$this->sqlBody.
				'ORDER BY RAND() LIMIT 1';
			if($this->tidFilter){
				$sql = 'SELECT i.occid, IFNULL(o.catalognumber, o.othercatalognumbers) AS catnum '.
					$this->sqlBody.
					'ORDER BY RAND() LIMIT 1';
			}
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr[$r->occid]['catnum'] = $r->catnum;
				$sql2 = 'SELECT i.imgid, i.url, i.originalurl, i.occid '.
					'FROM images i '.
					'WHERE (i.occid = '.$r->occid.') ';
				$rs2 = $this->conn->query($sql2);
				$cnt = 1;
				while($r2 = $rs2->fetch_object()){
					$retArr[$r2->occid][$cnt]['web'] = ($r2->url && $GLOBALS['CLIENT_ROOT'] && strncmp($r2->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r2->url) : $r2->url;
					$retArr[$r2->occid][$cnt]['lg'] = ($r2->originalurl && $GLOBALS['CLIENT_ROOT'] && strncmp($r2->originalurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r2->originalurl) : $r2->originalurl;
					$cnt++;
				}
				$rs2->free();
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function getSpecimenCount(): int
    {
		$retCnt = 0;
		if($this->collidStr){
			if(!$this->sqlBody) {
				$this->setSqlBody();
			}
			$sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt '.$this->sqlBody;
			if($this->tidFilter){
				$sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt '.$this->sqlBody;
			}
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retCnt = $r->cnt;
			}
			$rs->free();
		}
		return $retCnt;
	}
	
	private function setSqlBody(): void
	{
		$this->sqlBody = 'FROM omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
			'LEFT JOIN tmattributes AS a ON i.occid = a.occid '.
			'WHERE ISNULL(a.occid) AND o.collid = '.$this->collidStr.' ';
		if($this->tidFilter){
			$tidArr = array();
			$sql = 'SELECT tidaccepted FROM taxa WHERE tid = '.$this->tidFilter.' ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$tidArr[] = $r->tidaccepted;
			}
			$rs->free();
			$this->sqlBody = 'FROM omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
				'INNER JOIN taxaenumtree AS e ON i.tid = e.tid '.
				'LEFT JOIN tmattributes AS a ON i.occid = a.occid '.
				'WHERE e.parenttid IN('.$this->tidFilter.') OR e.tid IN('.implode(',',$tidArr).') '.
				'AND ISNULL(a.occid) AND o.collid = '.$this->collidStr.' ';
		}
	}

	public function getTraitNames(): array
	{
		$retArr = array();
		$sql = 'SELECT t.traitid, t.traitname '.
			'FROM tmtraits t LEFT JOIN tmtraitdependencies d ON t.traitid = d.traitid '.
			'WHERE t.traittype IN("UM","OM") AND d.traitid IS NULL';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->traitid] = $r->traitname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	public function getTraitArr($traitID, $setAttributes = null): array
	{
		unset($this->traitArr);
		$this->traitArr = array();
		$this->setTraitArr($traitID);
		$this->setTraitStates();
		if($setAttributes) {
			$this->setCodedAttribute();
		}
		return $this->traitArr;
	}

	private function setTraitArr($traitStr): array
	{
		if(preg_match('/^[\d,]+$/', $traitStr)){
			$sql = 'SELECT traitid, traitname, traittype, units, description, refurl, notes, dynamicproperties '.
				'FROM tmtraits '. 
				'WHERE (traitid IN('.$traitStr.'))';
			//echo $sql.'<br/>';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->traitArr[$r->traitid]['name'] = $r->traitname;
				$this->traitArr[$r->traitid]['type'] = $r->traittype;
				$this->traitArr[$r->traitid]['units'] = $r->units;
				$this->traitArr[$r->traitid]['description'] = $r->description;
				$this->traitArr[$r->traitid]['refurl'] = $r->refurl;
				$this->traitArr[$r->traitid]['notes'] = $r->notes;
				$this->traitArr[$r->traitid]['props'] = $r->dynamicproperties;
			}
			$rs->free();
			$this->setDependentTraits($traitStr);
		}
		return $this->traitArr;
	}

	private function setDependentTraits($traitStr): void
	{
		$traitIdArr = array();
		$sql = 'SELECT DISTINCT s.traitid AS parenttraitid, d.parentstateid, d.traitid AS depTraitID '.
			'FROM tmstates s INNER JOIN tmtraitdependencies d ON s.stateid = d.parentstateid '.
			'WHERE (s.traitid IN('.$traitStr.'))';
		//echo $sql.'<br/>'; 
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->traitArr[$r->parenttraitid]['states'][$r->parentstateid]['dependTraitID'] = $r->depTraitID;
			$traitIdArr[] = $r->depTraitID;
		}
		$rs->free();
		if($traitIdArr){
			$this->setTraitArr(implode(',', $traitIdArr));
		}
	}

	private function setTraitStates(): void
	{
		$sql = 'SELECT traitid, stateid, statename, description, notes, refurl '.
			'FROM tmstates '.
			'WHERE traitid IN('.implode(',',array_keys($this->traitArr)).') '.
			'ORDER BY traitid, sortseq, statecode ';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->traitArr[$r->traitid]['states'][$r->stateid]['name'] = $r->statename;
			$this->traitArr[$r->traitid]['states'][$r->stateid]['description'] = $r->description;
			$this->traitArr[$r->traitid]['states'][$r->stateid]['notes'] = $r->notes;
			$this->traitArr[$r->traitid]['states'][$r->stateid]['refurl'] = $r->refurl;
		}
		$rs->free();
	}

	private function setCodedAttribute(): array
	{
		$retArr = array();
		$sql = 'SELECT s.traitid, a.stateid, a.notes '.
			'FROM tmattributes a INNER JOIN tmstates s ON a.stateid = s.stateid '.
			'WHERE a.occid = '.$this->targetOccid.' AND s.traitid IN('.implode(',',array_keys($this->traitArr)).')';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->traitArr[$r->traitid]['states'][$r->stateid]['coded'] = $r->notes;
			$retArr[] = $r->stateid;
		}
		$rs->free();
		return $retArr;
	}
	
	public function echoFormTraits($traitID): void
	{
		echo $this->getTraitUnitString($traitID,true);
		echo '<div style="margin:10px 5px;">';
		echo 'Notes: <input name="notes" type="text" style="width:200px" value="'.$this->attrNotes.'" />';
		echo '</div>';
	}

	private function getTraitUnitString($traitID,$dispaly,$classStr=null): string
	{
		$controlType = 'checkbox';
		if($this->traitArr[$traitID]['props']){
			$propArr = json_decode($this->traitArr[$traitID]['props'], true);
			if(isset($propArr[0]['controlType'])) {
				$controlType = $propArr[0]['controlType'];
			}
		}
		$attrStateArr = $this->traitArr[$traitID]['states'];
		$innerStr = '';
		foreach($attrStateArr as $sid => $sArr){
			$isCoded = false;
			if(array_key_exists('coded',$sArr)){
				$isCoded = true;
				$this->attrNotes = $sArr['coded'];
			}
			$depTraitId = false;
			if(isset($sArr['dependTraitID']) && $sArr['dependTraitID']) {
				$depTraitId = $sArr['dependTraitID'];
			}
			if($controlType === 'checkbox' || $controlType === 'radio'){
				$innerStr .= '<div title="'.$sArr['description'].'"><input id="stateid-'.$traitID.'" name="stateid-'.$traitID.'[]" class="'.$classStr.'" type="'.$controlType.'" value="'.$sid.'" '.($isCoded?'checked':'').' onchange="traitChanged('.$traitID.')" /> '.$sArr['name'].'</div>';
			}
			elseif($controlType === 'select'){
				$innerStr .= '<option value="'.$sid.'" '.($isCoded?'selected':'').'>'.$sArr['name'].'</option>';
			}
			if($depTraitId){
				$innerStr .= $this->getTraitUnitString($depTraitId,$isCoded,trim($classStr.' child-'.$sid));
			}
		}
		$divClass = '';
		if($classStr){
			$classArr = explode(' ',$classStr);
			$divClass = array_pop($classArr);
		}
		$outStr = '<div class="'.$divClass.'" style="margin-left:'.($classStr?'10':'').'px; display:'.($dispaly?'block':'none').';">';
		if($controlType === 'select'){
			$outStr .= '<select name="stateid">';
			$outStr .= '<option value="">Select State</option>';
			$outStr .= '<option value="">------------------------------</option>';
			$outStr .= $innerStr;
			$outStr .= '</select>';
		}
		else{
			$outStr .= $innerStr;
		}
		$outStr .= '</div>';
		return $outStr;
	}

	public function getTaxonFilterSuggest($str,$exactMatch=null){
		$retArr = array();
		if($str){
			$sql = 'SELECT tid, sciname FROM taxa ';
			if($exactMatch){
				$sql .= 'WHERE sciname = "'.$str.'"';
			}
			else{
				$sql .= 'WHERE sciname LIKE "'.$str.'%"';
			}
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = array('id' => $r->tid, 'value' => $r->sciname);
			}
			$rs->free();
		}
		return json_encode($retArr);
	}

	public function getReviewUrls($traitID, $reviewUid, $reviewDate, $reviewStatus, $start): array
    {
		$retArr = array();
		if($reviewStatus && is_numeric($reviewStatus) && $reviewUid && is_numeric($reviewUid) && is_numeric($traitID) && $this->collidStr && $reviewDate && preg_match('/^\d{4}-\d{2}-\d{2}$/',$reviewDate)){
			$targetOccid = 0;
			$sql1 = 'SELECT DISTINCT o.occid, IFNULL(o.catalognumber, o.othercatalognumbers) AS catnum '.
				$this->getReviewSqlBase($traitID, $reviewUid, $reviewDate, $reviewStatus).' LIMIT '.$start.',1';
			//echo $sql1;
			$rs1 = $this->conn->query($sql1);
			while($r1 = $rs1->fetch_object()){
				$targetOccid = $r1->occid;
				$retArr[$r1->occid]['catnum'] = $r1->catnum;
			}
			$rs1->free();
			$sql = 'SELECT imgid, url, originalurl, occid FROM images WHERE (occid = '.$targetOccid.')';
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			$cnt = 1;
			while($r = $rs->fetch_object()){
				$retArr[$r->occid][$cnt]['web'] = ($r->url && $GLOBALS['CLIENT_ROOT'] && strncmp($r->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->url) : $r->url;
				$retArr[$r->occid][$cnt]['lg'] = ($r->originalurl && $GLOBALS['CLIENT_ROOT'] && strncmp($r->originalurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->originalurl) : $r->originalurl;
				$cnt++;
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function getReviewCount($traitID, $reviewUid, $reviewDate, $reviewStatus): int
    {
		$cnt = 0;
		if($reviewDate && $reviewStatus && is_numeric($reviewStatus) && $reviewUid && is_numeric($reviewUid) && is_numeric($traitID) && $this->collidStr  && preg_match('/^\d{4}-\d{2}-\d{2}$/',$reviewDate)){
			$sql = 'SELECT COUNT(DISTINCT o.occid) as cnt '.
				$this->getReviewSqlBase($traitID, $reviewUid, $reviewDate, $reviewStatus);
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$cnt = $r->cnt;
			}
			$rs->free();
		}
		return $cnt;
	}

	private function getReviewSqlBase($traitID, $reviewUid, $reviewDate, $reviewStatus): string
	{
		$sqlFrag = 'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
			'INNER JOIN tmattributes a ON i.occid = a.occid '.
			'INNER JOIN tmstates s ON a.stateid = s.stateid '. 
			'WHERE (s.traitid = '.$traitID.') AND (o.collid = '.$this->collidStr.') ';
		if($reviewUid){
			$sqlFrag .= 'AND (a.createduid = '.$reviewUid.') ';
		}
		if($reviewDate){
			$sqlFrag .= 'AND (date(a.initialtimestamp) = "'.$reviewDate.'") ';
		}
		if($reviewStatus){
			$sqlFrag .= 'AND (a.statuscode = '.$reviewStatus.') ';
		}
		else{
			$sqlFrag .= 'AND (a.statuscode IS NULL OR a.statuscode = 0) ';
		}
		return $sqlFrag;
	}

	public function saveReviewStatus($traitID, $postArr): bool
	{
		$status = false;
		$stateArr = array();
		foreach($postArr as $postKey => $postValue){
			if(strncmp($postKey, 'stateid-', 8) === 0){
				if(is_array($postValue)){
					foreach($postValue as $val){
						$stateArr[] = $val;
					}
				}
				else{
					$stateArr[] = $postValue;
				}
			}
		}
		$setStatus = $postArr['setstatus'];
		if(is_numeric($traitID) && is_numeric($setStatus)){
			$this->setTraitArr($traitID);
			$attrArr = $this->setCodedAttribute();
			$addArr = array_diff($stateArr,$attrArr);
			$delArr = array_diff($attrArr,$stateArr);
			if($addArr){
				foreach($addArr as $id){
					if(is_numeric($id)){
						$sql = 'INSERT INTO tmattributes(stateid,occid,createduid) VALUES('.$id.','.$this->targetOccid.','.$GLOBALS['SYMB_UID'].') ';
						//echo $sql.'<br/>';
						if(!$this->conn->query($sql)){
							$this->errorMessage = 'ERROR addin occurrence attribute.';
							$status = false;
						}
					}
				}
			} 
			if($delArr){
				foreach($delArr as $id){
					if(is_numeric($id)){
						$sql = 'DELETE FROM tmattributes WHERE stateid = '.$id.' AND occid = '.$this->targetOccid;
						//echo $sql.'<br/>';
						if(!$this->conn->query($sql)){
							$this->errorMessage = 'ERROR removing occurrence attribute.';
							$status = false;
						}
					}
				}
			} 
			
			$sql = 'UPDATE tmattributes a INNER JOIN tmstates s ON a.stateid = s.stateid '.
				'SET a.statuscode = '.$setStatus.', a.notes = "'.SanitizerService::cleanInStr($this->conn,$postArr['notes']).'" '.
				'WHERE a.occid = '.$this->targetOccid.' AND s.traitid IN('.implode(',',array_keys($this->traitArr)).')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR updating occurrence attribute review status.';
				$status = false;
			}
		}
		return $status;
	}

	public function getEditorArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT u.uid, u.lastname, u.firstname, u.username '.
			'FROM tmattributes a INNER JOIN users u ON a.createduid = u.uid '.
			'INNER JOIN omoccurrences o ON a.occid = o.occid '.
			'WHERE o.collid = '.$this->collidStr.' ORDER BY u.lastname, u.firstname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->lastname.($r->firstname?', '.$r->firstname:'').' ('.$r->username.')';
		}
		$rs->free();
		return $retArr;
	}
	
	public function getEditDates(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT DATE(a.initialtimestamp) as d '.
			'FROM tmattributes a INNER JOIN omoccurrences o ON a.occid = o.occid '.
			'WHERE o.collid = '.$this->collidStr.' ORDER BY a.initialtimestamp DESC';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->d;
		}
		$rs->free();
		return $retArr;
	}

	public function getFieldValueArr($traitID, $fieldName, $tidFilter, $stringFilter): array
	{
		$retArr = array();
		if(is_numeric($traitID)){
			$sql = 'SELECT o.'.$fieldName.', count(DISTINCT o.occid) AS cnt FROM omoccurrences o '.
				$this->getMiningSqlFrag($traitID, $fieldName, $tidFilter, $stringFilter).
				'GROUP BY o.'.$fieldName;
			//echo $sql; 
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_assoc()){
				if($r[$fieldName]) {
					$retArr[] = strtolower($r[$fieldName]) . ' - [' . $r['cnt'] . ']';
				}
			}
			$rs->free();
			sort($retArr);
		}
		return $retArr;
	}

	public function submitBatchAttributes($traitID, $fieldName, $tidFilter, $stateIDArr, $fieldValueArr, $notes, $reviewStatus): bool
	{
		set_time_limit(1800);
		$status = true;
		$fieldArr = array();
		foreach($fieldValueArr as $fieldValue){
			if(preg_match('/(.+) - \[\d+]$/',$fieldValue,$m)){
				$fieldValue = $m[1];
			}
			$fieldArr[] = $this->conn->real_escape_string($fieldValue);
		}
		if($fieldArr){
			$occArr = array();
			$sql = 'SELECT DISTINCT occid FROM omoccurrences o '.
				$this->getMiningSqlFrag($traitID, $fieldName, $tidFilter).
				'AND ('.SanitizerService::cleanInStr($this->conn,$fieldName).' IN("'.implode('","',$fieldArr).'")) ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$occArr[] = $r->occid;
			}
			$rs->free();
			$occidChuckArr = array_chunk($occArr, '100000');
			foreach($stateIDArr as $stateID){
				if(is_numeric($stateID)){
					foreach($occidChuckArr as $oArr){
						$sql = '';
						foreach($oArr as $occid){
							$sql .= ',('.$stateID.','.$occid.')';
						}
						if($sql){
							$sql = 'INSERT INTO tmattributes(stateid,occid) VALUES'.substr($sql,1);
							if(!$this->conn->query($sql)){
								$this->errorMessage .= 'ERROR saving batch occurrence attributes.';
								$status = false;
							}
						}
					}
				}
			}
			$occidChuckArr = array_chunk($occArr, '200000');
			foreach($occidChuckArr as $oArr){
				$sqlUpdate = 'UPDATE tmattributes '.
					'SET source = "Field mining: '.SanitizerService::cleanInStr($this->conn,$fieldName).'", createduid = '.$GLOBALS['SYMB_UID'];
				if($notes) {
					$sqlUpdate .= ', notes = "' . SanitizerService::cleanInStr($this->conn,$notes) . '"';
				}
				if(is_numeric($reviewStatus)) {
					$sqlUpdate .= ', statuscode = "' . SanitizerService::cleanInStr($this->conn,$reviewStatus) . '"';
				}
				$sqlUpdate .= ' WHERE stateid IN('.implode(',',$stateIDArr).') AND occid IN('.implode(',',$oArr).')';
				if(!$this->conn->query($sqlUpdate)){
					$this->errorMessage .= 'ERROR saving batch occurrence attributes(2).';
					$status = false;
				}
			}
		}
		return $status;
	}
	
	private function getMiningSqlFrag($traitID, $fieldName, $tidFilter, $stringFilter = null): string
	{
		$sql = '';
		if($tidFilter){
			$sql = 'INNER JOIN taxaenumtree AS e ON o.tid = e.tid ';
		}
		$sql .= 'WHERE (o.'.$fieldName.' IS NOT NULL) '.
			'AND (o.occid NOT IN(SELECT t.occid FROM tmattributes AS t INNER JOIN tmstates AS s ON t.stateid = s.stateid WHERE s.traitid = '.$traitID.')) ';
		if($tidFilter){
			$sql .= 'AND (e.parenttid = '.$tidFilter.' OR o.tid = '.$tidFilter.') ';
		}
		if($this->collidStr !== 'all'){
			$sql .= 'AND (o.collid IN('.$this->collidStr.')) ';
		}
		if($stringFilter){
			$sql .= 'AND (o.'.$fieldName.' LIKE "%'.SanitizerService::cleanInStr($this->conn,$stringFilter).'%") ';
		}
		return $sql;
	}

	public function getCollectionList($collArr): array
	{
		$retArr = array();
		$sql = 'SELECT collid, collectionname, CONCAT_WS("-",institutioncode,collectioncode) as instcode FROM omcollections ';
		if($collArr) {
			$sql .= 'WHERE collid IN(' . implode(',', $collArr) . ')';
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->collectionname.($r->instcode?' ('.$r->instcode.')':'');
		}
		$rs->free();
		return $retArr;
	}

	public function setCollid($idStr): void
	{
		if(preg_match('/^[0-9,al]+$/', $idStr)){
			$this->collidStr = $idStr;
		}
	}

	public function setTidFilter($tid): void
	{
        $this->tidFilter = $tid;
	}

	public function setTargetOccid($occid): void
	{
        $this->targetOccid = $occid;
	}
}

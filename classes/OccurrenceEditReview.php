<?php
include_once(__DIR__ . '/Manager.php');

class OccurrenceEditReview extends Manager{

	private $collid;
	private $collAcronym;
	private $obsUid = 0;

	private $display = 1;
	private $appliedStatusFilter = '';
	private $reviewStatusFilter;
	private $editorFilter;
	private $queryOccidFilter;
	private $startDateFilter;
	private $endDateFilter;
	private $pageNumber = 0;
	private $limitNumber;

	public function __construct(){
		parent::__construct(null);
	}

    public function setCollId($id): string
	{
        global $SYMB_UID;
	    $collName = '';
	    if(is_numeric($id)){
			$this->collid = $id;
			$sql = 'SELECT collectionname, institutioncode, collectioncode, colltype '.
				'FROM omcollections WHERE (collid = '.$id.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$collName = $r->collectionname.' (';
				$this->collAcronym = $r->institutioncode;
				$collName .= $r->institutioncode;
				if($r->collectioncode){
					$collName .= ':'.$r->collectioncode;
					$this->collAcronym .= ':'.$r->collectioncode;
				}
				$collName .= ')';
				if($r->colltype === 'General Observations') {
					$this->obsUid = $SYMB_UID;
				}
			}
			$rs->free();
		}
		return $collName;
	}

	public function getEditCnt(){
		if ($this->display === 1) {
			return $this->getOccurEditCnt();
		}

		if($this->display === 2) {
			return $this->getRevisionCnt();
		}
		return 0;
	}
	
	public function getEditArr(){
		if ($this->display === 1) {
			return $this->getOccurEditArr();
		}

		if($this->display === 2) {
			return $this->getRevisionArr();
		}
		return null;
	}
	
	private function getOccurEditCnt(){
		$recCnt = 0;
		$sql = 'SELECT COUNT(e.ocedid) AS fullcnt '.$this->getEditSqlBase();
		//echo $sql; exit;
		$rsCnt = $this->conn->query($sql);
		if($rCnt = $rsCnt->fetch_object()){
			$recCnt = $rCnt->fullcnt;
		}
		$rsCnt->free();
		return $recCnt;
	}

	private function getOccurEditArr(): array
	{
		$retArr = array();
		$sql = 'SELECT e.ocedid,e.occid,o.catalognumber,e.fieldname,e.fieldvaluenew,e.fieldvalueold,e.reviewstatus,e.appliedstatus,'.
			'CONCAT_WS(", ",u.lastname,u.firstname) AS username, e.initialtimestamp '.
			$this->getEditSqlBase().' ORDER BY e.initialtimestamp DESC, e.fieldname ASC '.
			'LIMIT '.($this->pageNumber*$this->limitNumber).','.($this->limitNumber+1);
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['ts'] = $r->initialtimestamp;
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['catnum'] = $r->catalognumber;
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['rstatus'] = $r->reviewstatus;
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['editor'] = $r->username;
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['f'][$r->fieldname]['old'] = $r->fieldvalueold;
			$retArr[$r->occid][$r->ocedid][$r->appliedstatus]['f'][$r->fieldname]['new'] = $r->fieldvaluenew;
		}
		$rs->free();
		return $retArr;
	}
	
	private function getEditSqlBase(): string
	{
		$sqlBase = '';
		if($this->collid){
			$sqlBase = 'FROM omoccuredits e INNER JOIN omoccurrences o ON e.occid = o.occid '.
				'INNER JOIN users u ON e.uid = u.uid '.
				'WHERE (o.collid = '.$this->collid.') ';
			if($this->appliedStatusFilter !== ''){
				$sqlBase .= 'AND (e.appliedstatus = '.$this->appliedStatusFilter.') ';
			}
			if($this->reviewStatusFilter){
				$sqlBase .= 'AND (e.reviewstatus IN('.$this->reviewStatusFilter.')) ';
			}
			if($this->editorFilter){
				$sqlBase .= 'AND (e.uid = '.$this->editorFilter.') ';
			}
			if($this->queryOccidFilter){
				$sqlBase .= 'AND (e.occid = '.$this->queryOccidFilter.') ';
			}
			if($this->startDateFilter){
				$sqlBase .= 'AND (e.initialtimestamp >= "'.$this->startDateFilter.'") ';
			}
			if($this->endDateFilter){
				$sqlBase .= 'AND (e.initialtimestamp <= "'.$this->endDateFilter.'") ';
			}
			if($this->obsUid){
				$sqlBase .= 'AND (o.observeruid = '.$this->obsUid.') ';
			}
		}
		return $sqlBase;
	}
	
	private function getRevisionCnt(){
		$recCnt = 0;
		$sql = 'SELECT COUNT(r.orid) AS fullcnt '.$this->getRevisionSqlBase();
		//echo $sql; exit;
		$rsCnt = $this->conn->query($sql);
		if($rCnt = $rsCnt->fetch_object()){
			$recCnt = $rCnt->fullcnt;
		}
		$rsCnt->free();
		return $recCnt;
	}

	private function getRevisionArr(): array
	{
		$retArr = array();
		$sql = 'SELECT r.orid, r.occid, o.catalognumber, r.oldvalues, r.newvalues, r.externalsource, r.externaleditor, r.reviewstatus, r.appliedstatus, r.errormessage, '.
			'CONCAT_WS(", ",u.lastname,u.firstname) AS username, r.externaltimestamp, r.initialtimestamp '.
			$this->getRevisionSqlBase().' ORDER BY r.initialtimestamp DESC '.
			'LIMIT '.($this->pageNumber*$this->limitNumber).','.($this->limitNumber+1);
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){

			$retArr[$r->occid][$r->orid][$r->appliedstatus]['catnum'] = $r->catalognumber;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['exsource'] = $r->externalsource;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['exeditor'] = $r->externaleditor;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['rstatus'] = $r->reviewstatus;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['errmsg'] = $r->errormessage;
			$editor = $r->externaleditor;
			if($r->username) {
				$editor .= ' (' . $r->username . ')';
			}
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['editor'] = $editor;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['extstamp'] = $r->externaltimestamp;
			$retArr[$r->occid][$r->orid][$r->appliedstatus]['ts'] = $r->initialtimestamp;
				
			$oldValues = json_decode($r->oldvalues,true);
			$newValues = json_decode($r->newvalues,true);
			foreach($oldValues as $fieldName => $value){
				if($fieldName !== 'georeferencesources' && $fieldName !== 'georeferencedby'){
					$retArr[$r->occid][$r->orid][$r->appliedstatus]['f'][$fieldName]['old'] = $value;
					$retArr[$r->occid][$r->orid][$r->appliedstatus]['f'][$fieldName]['new'] = ($newValues[$fieldName] ?? 'ERROR');
				}
			}
		}
		$rs->free();
		return $retArr;
	}

	private function getRevisionSqlBase(): string
	{
		$sqlBase = '';
		if($this->collid){
			$sqlBase = 'FROM omoccurrevisions r INNER JOIN omoccurrences o ON r.occid = o.occid '.
					'LEFT JOIN users u ON r.uid = u.uid '.
					'WHERE (o.collid = '.$this->collid.') ';
			if($this->appliedStatusFilter !== ''){
				$sqlBase .= 'AND (r.appliedstatus = '.$this->appliedStatusFilter.') ';
			}
			if($this->reviewStatusFilter){
				$sqlBase .= 'AND (r.reviewstatus IN('.$this->reviewStatusFilter.')) ';
			}
			if($this->editorFilter){
				if(is_numeric($this->editorFilter)){
					$sqlBase .= 'AND (u.uid = '.$this->editorFilter.') ';
				}
				else{
					$sqlBase .= 'AND (r.externaleditor = "'.$this->editorFilter.'") ';
				}
			}
			if($this->startDateFilter){
				$sqlBase .= 'AND (r.initialtimestamp >= "'.$this->startDateFilter.'") ';
			}
			if($this->endDateFilter){
				$sqlBase .= 'AND (r.initialtimestamp <= "'.$this->endDateFilter.'") ';
			}
			if($this->queryOccidFilter){
				$sqlBase .= 'AND (r.occid = '.$this->queryOccidFilter.') ';
			}
			if($this->obsUid){
				$sqlBase .= 'AND (o.observeruid = '.$this->obsUid.') ';
			}
		}
		return $sqlBase;
	}
	
	public function updateRecords($postArr){
		if ($this->display === 1) {
			return $this->updateOccurEditRecords($postArr);
		}

		if($this->display === 2) {
			return $this->updateRevisionRecords($postArr);
		}
		return null;
	}

	private function updateOccurEditRecords($postArr): bool
	{
		if(!array_key_exists('id',$postArr)) {
			return false;
		}
		$status = true;
		$idStr = implode(',',$postArr['id']);
		if($idStr){
			$ocedidStr = $this->getFullOcedidStr($idStr);
			//Apply edits
			$applyTask = $postArr['applytask'];
			//Apply edits with applied status = 0
			$sql = 'SELECT occid, fieldname, fieldvalueold, fieldvaluenew '.
				'FROM omoccuredits '.
				'WHERE appliedstatus = '.($applyTask === 'apply'?'0':'1').' AND (ocedid IN('.$ocedidStr.')) ORDER BY initialtimestamp';
			//echo '<div>'.$sql.'</div>'; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($applyTask === 'apply') {
					$value = $r->fieldvaluenew;
				}
				else {
					$value = $r->fieldvalueold;
				}
				$uSql = 'UPDATE omoccurrences '.
					'SET '.$r->fieldname.' = '.($value?'"'.$value.'"':'NULL').' '.
					'WHERE (occid = '.$r->occid.')';
				//echo '<div>'.$uSql.'</div>';
				if(!$this->conn->query($uSql)){
					$this->warningArr[] = 'ERROR '.($applyTask === 'apply'?'applying':'reverting').' edits: '.$this->conn->error;
					$status = false;
				}
			}
			$rs->free();
			$sql = 'UPDATE omoccuredits SET appliedstatus = '.($applyTask==='apply'?1:0);
			if($postArr['rstatus']){
				$sql .= ',reviewstatus = '.$postArr['rstatus'];
			}
			$sql .= ' WHERE (ocedid IN('.$ocedidStr.'))';
			//echo '<div>'.$sql.'</div>'; exit;
			$this->conn->query($sql);
		}
		return $status;
	}

	private function updateRevisionRecords($postArr): bool
	{
		if(!array_key_exists('id',$postArr)) {
			return false;
		}
		$status = true;
		$idStr = implode(',',$postArr['id']);
		if($idStr){
			$applyTask = $postArr['applytask'];
			$sql = 'SELECT occid, newvalues, oldvalues '.
				'FROM omoccurrevisions '.
				'WHERE appliedstatus = '.($applyTask === 'apply'?'0':'1').' AND (orid IN('.$idStr.')) ORDER BY initialtimestamp';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$dwcArr = json_decode(($applyTask === 'apply') ? $r->newvalues : $r->oldvalues, true);
				$sqlFrag = '';
				foreach($dwcArr as $fieldName => $fieldValue){
					$sqlFrag .= ','.$fieldName.' = '.($fieldValue?'"'.$fieldValue.'"':'NULL').' ';
				}
				$uSql = 'UPDATE omoccurrences SET '.trim($sqlFrag,', ').' WHERE (occid = '.$r->occid.')';
				//echo '<div>'.$uSql.'</div>'; exit;
				if(!$this->conn->query($uSql)){
					$this->warningArr[] = 'ERROR '.($applyTask === 'apply'?'applying':'reverting').' revisions: '.$this->conn->error;
					$status = false;
				}
			}
			$rs->free();
			$sql = 'UPDATE omoccurrevisions SET appliedstatus = '.($applyTask==='apply'?1:0);
			if($postArr['rstatus']){
				$sql .= ',reviewstatus = '.$postArr['rstatus'];
			}
			$sql .= ' WHERE (orid IN('.$idStr.'))';
			//echo '<div>'.$sql.'</div>'; exit;
			$this->conn->query($sql);
		}
		return $status;
	}

	public function deleteEdits($idStr){
		if ($this->display === 1) {
			return $this->deleteOccurEdits($idStr);
		}

		if($this->display === 2) {
			return $this->deleteRevisionsEdits($idStr);
		}
		return null;
	}

	private function deleteOccurEdits($idStr): bool
	{
		$status = true;
		if(!preg_match('/^[\d,]+$/', $idStr)) {
			return false;
		}
		$ocedidStr = $this->getFullOcedidStr($idStr);
		$sql = 'DELETE FROM omoccuredits WHERE (ocedid IN('.$ocedidStr.'))';
		//echo '<div>'.$sql.'</div>'; exit;
		if(!$this->conn->query($sql)){
			$this->errorMessage = 'ERROR deleting edits: '.$this->conn->error;
			$status = false;
		}
		return $status;
	}

	private function deleteRevisionsEdits($idStr): bool
	{
		$status = true;
		if(!preg_match('/^[\d,]+$/', $idStr)) {
			return false;
		}
		$sql = 'DELETE FROM omoccurrevisions WHERE (orid IN('.$idStr.'))';
		//echo '<div>'.$sql.'</div>';
		if($this->conn->query($sql)){
			$this->errorMessage = 'ERROR deleting revisions: '.$this->conn->error;
			$status = false;
		}
		return $status;
	}
	
	public function exportCsvFile($idStr, $exportAll = false): bool
	{
		$status = true;
		if($this->display === 1) {
			$idStr = $this->getFullOcedidStr($idStr);
		}
		if($this->display === 1){
			$sql = 'SELECT e.ocedid AS id, o.occid, o.catalognumber, o.dbpk, e.fieldname, e.fieldvaluenew, e.fieldvalueold, e.reviewstatus, e.appliedstatus, '.
				'CONCAT_WS(", ",u.lastname,u.firstname) AS username, e.initialtimestamp ';
			if($exportAll){
				$sql .= $this->getEditSqlBase();
			}
			else{
				$sql .= 'FROM omoccuredits e INNER JOIN omoccurrences o ON e.occid = o.occid '.
				'INNER JOIN users u ON e.uid = u.uid '.
				'WHERE (o.collid = '.$this->collid.') AND (ocedid IN('.$idStr.')) ';
				if($this->obsUid){
					$sql .= 'AND (o.observeruid = '.$this->obsUid.') ';
				}
			}
			$sql .= 'ORDER BY e.fieldname ASC, e.initialtimestamp DESC';
		}
		else{
			$sql = 'SELECT r.orid AS id, o.occid, o.catalognumber, o.dbpk, r.oldvalues, r.newvalues, r.reviewstatus, r.appliedstatus, '.
				'r.externaleditor, CONCAT_WS(", ",u.lastname,u.firstname) AS username, r.externaltimestamp, r.initialtimestamp ';
			if($exportAll){
				$sql .= $this->getRevisionSqlBase();
			}
			else{
				$sql .= 'FROM omoccurrevisions r INNER JOIN omoccurrences o ON r.occid = o.occid '.
				'LEFT JOIN users u ON r.uid = u.uid '.
				'WHERE (o.collid = '.$this->collid.') AND (r.orid IN('.$idStr.')) ';
				if($this->obsUid){
					$sql .= 'AND (o.observeruid = '.$this->obsUid.') ';
				}
			}
			$sql .= 'ORDER BY r.initialtimestamp DESC';
		}
		//echo '<div>'.$sql.'</div>'; exit;
		if($sql){
			$rs = $this->conn->query($sql);
			if($rs->num_rows){
				//Initiate file
				$fileName = $this->collAcronym.'SpecimenEdits_'.time(). '.csv';
				header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header ('Content-Type: text/csv');
				header ("Content-Disposition: attachment; filename=\"$fileName\"");
				$outFH = fopen('php://output', 'wb');
				$headerArr = array('EditId', 'occid', 'CatalogNumber', 'dbpk', 'ReviewStatus', 'AppliedStatus', 'Editor', 'Timestamp', 'FieldName', 'OldValue', 'NewValue');
				fputcsv($outFH, $headerArr);
				while($r = $rs->fetch_object()){
					$outArr = array(0 => $r->id, 1 => $r->occid, 2 => $r->catalognumber, 3 => $r->dbpk);
					if($r->reviewstatus === 1){
						$outArr[4] = 'OPEN';
					}
					elseif($r->reviewstatus === 2){
						$outArr[4] = 'PENDING';
					}
					elseif($r->reviewstatus === 3){
						$outArr[4] = 'CLOSED';
					}
					$outArr[5] = ($r->appliedstatus? 'APPLIED' : 'NOT APPLIED');
					if($this->display === 1) {
						$outArr[6] = $r->username;
					}
					else {
						$outArr[6] = $r->externaleditor . ($r->username ? ' (' . $r->username . ')' : '');
					}
					if($this->display === 1){
						$outArr[7] = $r->initialtimestamp;
						if($r->fieldname === 'footprintwkt') {
							continue;
						}
						$outArr[8] = $r->fieldname;
						$outArr[9] = $r->fieldvalueold;
						$outArr[10] = $r->fieldvaluenew;
						fputcsv($outFH, $outArr);
					}
					else{
						$outArr[7] = $r->initialtimestamp.($r->externaltimestamp?' ('.$r->externaltimestamp.')':'');
						$oldValueArr = json_decode($r->oldvalues,true);
						$newValueArr = json_decode($r->newvalues,true);
						foreach($oldValueArr as $fieldName => $oldValue){
							$outArr[8] = $fieldName;
							$outArr[9] = $oldValue;
							$outArr[10] = $newValueArr[$fieldName];
							fputcsv($outFH, $outArr);
						}
					}
				}
				$rs->free();
				fclose($outFH);
			}
			else{
				$status = false;
				$this->errorMessage = 'Recordset is empty';
			}
		}
		return $status;
	}

	private function getFullOcedidStr($idStr): string
	{
		$ocedidArr = array();
		if($idStr){
			$sql = 'SELECT e.ocedid '.
				'FROM omoccuredits e INNER JOIN omoccuredits e2 ON e.occid = e2.occid AND e.initialtimestamp = e2.initialtimestamp '.
				'WHERE e2.ocedid IN('.$idStr.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$ocedidArr[] = $r->ocedid;
			}
			$rs->free();
		}
		return implode(',',$ocedidArr);
	}
	
	public function setDisplay($d): void
	{
		if(is_numeric($d)){
			$this->display = $d;
		}
	}

	public function setAppliedStatusFilter($status): void
	{
		if(is_numeric($status)){
			$this->appliedStatusFilter = $status;
		}
	}

	public function setReviewStatusFilter($status): void
	{
		if(preg_match('/^[,\d]+$/', $status)){
			$this->reviewStatusFilter = $status;
		}
	}

	public function setEditorFilter($f): void
    {
		$this->editorFilter = $this->cleanInStr($f);
	}
	
	public function setQueryOccidFilter($num): void
	{
		if(is_numeric($num)){
			$this->queryOccidFilter = $num;
		}
	}

	public function setStartDateFilter($d): void
	{
		if(preg_match('/^[\d-]+$/', $d)){
			$this->startDateFilter = $d;
		}
	}

	public function setEndDateFilter($d): void
	{
		if(preg_match('/^[\d-]+$/', $d)){
			$this->endDateFilter = $d;
		}
	}

	public function setPageNumber($num): void
	{
		if(is_numeric($num)){
			$this->pageNumber = $num;
		}
	}

	public function setLimitNumber($limit): void
	{
		if(is_numeric($limit)){
			$this->limitNumber = $limit;
		}
	}
	
	public function getObsUid(): int
	{
		return $this->obsUid;
	}

	public function getEditorList(): array
	{
		$retArr = array();
		if($this->display === 1){
			$sql = 'SELECT DISTINCT u.uid AS id, CONCAT_WS(", ",u.lastname,u.firstname) AS name '.
				'FROM omoccuredits e INNER JOIN omoccurrences o ON e.occid = o.occid '.
				'INNER JOIN users u ON e.uid = u.uid ';
		}
		else{
			$sql = 'SELECT DISTINCT IFNULL(l.uid,r.externaleditor) as id, IFNULL(l.username,r.externaleditor) AS name '.
				'FROM omoccurrevisions r INNER JOIN omoccurrences o ON r.occid = o.occid '.
				'LEFT JOIN users l ON r.uid = l.uid ';
		}
		$sql .= 'WHERE (o.collid = '.$this->collid.') ';
		if($this->obsUid){
			$sql .= 'AND (o.observeruid = '.$this->obsUid.') ';
		}
		//echo $sql;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$retArr[$row->id] = $row->name;
		}
		$result->free();
		asort($retArr);
		return $retArr;
	}
	
	public function hasRevisionRecords(): bool
	{
		$status = false;
		$sql = 'SELECT orid FROM omoccurrevisions LIMIT 1';
		$result = $this->conn->query($sql);
		if($row = $result->fetch_object()){
			$status = true;
		}
		$result->free();
		return $status;
	}
}

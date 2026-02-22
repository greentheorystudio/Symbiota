<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceEditReview extends Manager{

	private $collid;
	private $collAcronym;
	private $obsUid = 0;

	private $appliedStatusFilter = '';
	private $reviewStatusFilter;
	private $editorFilter;
	private $queryOccidFilter;
	private $startDateFilter;
	private $endDateFilter;
	private $pageNumber = 0;
	private $limitNumber;

	public function __construct(){
		parent::__construct();
	}

    public function setCollId($id): string
	{
        $collName = '';
	    if(is_numeric($id)){
			$this->collid = $id;
			$sql = 'SELECT collectionname, institutioncode, collectioncode, colltype '.
				'FROM omcollections WHERE (collid = '.$id.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$code = '';
                $collName = $r->collectionname;
				if($r->institutioncode){
                    $code .= $r->institutioncode;
                }
				if($r->collectioncode){
                    $code .= ($code?':':'') . $r->collectioncode;
				}
                $this->collAcronym = $code;
				if($code){
                    $collName .= ' (' . $code . ')';
                }
				if($r->colltype === 'HumanObservation') {
					$this->obsUid = $GLOBALS['SYMB_UID'];
				}
			}
			$rs->free();
		}
		return $collName;
	}

	public function getEditCnt(): ?int
    {
        return $this->getOccurEditCnt();
	}
	
	public function getEditArr(): ?array
    {
        return $this->getOccurEditArr();
	}
	
	private function getOccurEditCnt(): int
    {
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

    public function updateRecords($postArr): ?bool
    {
        return $this->updateOccurEditRecords($postArr);
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
					$this->warningArr[] = 'ERROR '.($applyTask === 'apply'?'applying':'reverting').' edits.';
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

    public function deleteEdits($idStr): ?bool
    {
        return $this->deleteOccurEdits($idStr);
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
			$this->errorMessage = 'ERROR deleting edits.';
			$status = false;
		}
		return $status;
	}

    public function exportCsvFile($idStr, $exportAll = null): bool
	{
		$status = true;
        $idStr = $this->getFullOcedidStr($idStr);
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
                    $outArr[6] = $r->username;
                    $outArr[7] = $r->initialtimestamp;
                    if($r->fieldname === 'footprintwkt') {
                        continue;
                    }
                    $outArr[8] = $r->fieldname;
                    $outArr[9] = $r->fieldvalueold;
                    $outArr[10] = $r->fieldvaluenew;
                    fputcsv($outFH, $outArr);
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
		$this->editorFilter = SanitizerService::cleanInStr($this->conn,$f);
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
        $sql = 'SELECT DISTINCT u.uid AS id, CONCAT_WS(", ",u.lastname,u.firstname) AS name '.
            'FROM omoccuredits e INNER JOIN omoccurrences o ON e.occid = o.occid '.
            'INNER JOIN users u ON e.uid = u.uid ';
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

}

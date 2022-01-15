<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class TaxonomyEditorManager{

	private $conn;
	private $taxAuthId = 1;
	private $tid = 0;
	private $family;
	private $sciName;
	private $kingdomName;
    private $kingdomId = 0;
	private $rankid = 0;
	private $rankName;
	private $unitInd1;
	private $unitName1;
	private $unitInd2;
	private $unitName2;
	private $unitInd3;
	private $unitName3;
	private $author;
	private $parentTid = 0;
	private $parentName;
	private $parentNameFull;
	private $source;
	private $notes;
	private $hierarchyArr;
	private $securityStatus;
	private $isAccepted = -1;
	private $acceptedArr = array();
	private $synonymArr = array();

	private $errorStr = '';
	
	public function __construct() {
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}
	
	public function setTaxon(): void
	{
		$sqlTaxon = 'SELECT tid, kingdomId, rankid, sciname, unitind1, unitname1, '.
			'unitind2, unitname2, unitind3, unitname3, author, source, notes, securitystatus, initialtimestamp '.
			'FROM taxa '.
			'WHERE (tid = '.$this->tid.')';
		//echo $sqlTaxon;
		$rs = $this->conn->query($sqlTaxon);
		if($r = $rs->fetch_object()){
			$this->sciName = $r->sciname;
			$this->rankid = (int)$r->rankid;
            $this->kingdomId = (int)$r->kingdomId;
            $this->unitInd1 = $r->unitind1;
			$this->unitName1 = $r->unitname1;
			$this->unitInd2 = $r->unitind2;
			$this->unitName2 = $r->unitname2;
			$this->unitInd3 = $r->unitind3;
			$this->unitName3 = $r->unitname3;
			$this->author = $r->author;
			$this->source = $r->source;
			$this->notes = $r->notes;
			$this->securityStatus = (int)$r->securitystatus;
		}
		$rs->free();
		
		if($this->sciName){
			$this->setRankName();
			$this->setHierarchy();
			
			$sqlTs = 'SELECT ts.parenttid, ts.tidaccepted, ts.unacceptabilityreason, ' .
				'ts.family, t.sciname, t.author, t.notes, ts.sortsequence ' .
				'FROM taxstatus ts INNER JOIN taxa t ON ts.tidaccepted = t.tid ' .
				'WHERE (ts.taxauthid = ' .$this->taxAuthId. ') AND (ts.tid = ' .$this->tid.')';
			//echo $sqlTs;
			if($rsTs = $this->conn->query($sqlTs)){
                while($row = $rsTs->fetch_object()){
                    $this->parentTid = $row->parenttid;
                    $this->family = $row->family;
                    $tidAccepted = (int)$row->tidaccepted;
                    if($this->tid === $tidAccepted){
                        if($this->isAccepted === -1 || $this->isAccepted === 1){
                            $this->isAccepted = 1;
                        }
                        else{
                            $this->isAccepted = -2;
                        }
                    }
                    else{
                        if($this->isAccepted === -1 || $this->isAccepted === 0){
                            $this->isAccepted = 0;
                        }
                        else{
                            $this->isAccepted = -2;
                        }
                        $this->acceptedArr[$tidAccepted]['unacceptabilityreason'] = $row->unacceptabilityreason;
                        $this->acceptedArr[$tidAccepted]['sciname'] = $row->sciname;
                        $this->acceptedArr[$tidAccepted]['author'] = $row->author;
                        $this->acceptedArr[$tidAccepted]['usagenotes'] = $row->notes;
                        $this->acceptedArr[$tidAccepted]['sortsequence'] = $row->sortsequence;
                    }
                }
            }
            else{
                $sqlPar = 'SELECT t.tid, ts.family '.
                    'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
                    'WHERE ts.taxauthid = '.$this->taxAuthId.' AND ';
                if($this->rankid > 220){
                    $sqlPar .= 't.rankid = 220 AND t.unitName1 = "'.$this->unitName1.'" AND t.unitName2 = "'.$this->unitName2.'" ';
                }
                elseif($this->rankid > 180){
                    $sqlPar .= 't.rankid = 180 AND t.unitName1 = "'.$this->unitName1.'" ';
                }
                elseif($this->kingdomName){
                    $sqlPar .= 't.rankid = 10 AND t.unitName1 = "'.$this->kingdomName.'"';
                }
                else{
                    $sqlPar .= 't.rankid = 1 ';
                }
                $rsPar = $this->conn->query($sqlPar);
                if($rPar = $rsPar->fetch_object()){
                    $sqlIns = 'INSERT INTO taxstatus(tid, tidaccepted, taxauthid, parenttid, family) '.
                        'VALUES('.$this->tid.','.$this->tid.','.$this->taxAuthId.','.$rPar->tid.','.
                        ($rPar->family?'"'.$rPar->family.'"':'NULL').')';
                    if($this->conn->query($sqlIns)){
                        $this->parentTid = $rPar->tid;
                        $this->family = $rPar->family;
                        $this->isAccepted = 1;
                    }
                }
                $rsPar->free();
            }
			$rsTs->free();

			if($this->isAccepted === 1) {
				$this->setSynonyms();
			}
			if($this->parentTid) {
				$this->setParentName();
			}
		}
	}

	private function setRankName(): void
	{
		if($this->rankid){
			$sql = 'SELECT rankname '.
				'FROM taxonunits '.
				'WHERE (rankid = '.$this->rankid.') AND (kingdomId = '.$this->kingdomId.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->rankName = $r->rankname;
			}
			$rs->free();
		}
	}
	
	private function setHierarchy(): void
	{
		unset($this->hierarchyArr);
		$this->hierarchyArr = array();
		$sql = 'SELECT parenttid '.
			'FROM taxaenumtree '.
			'WHERE (tid = '.$this->tid.') AND (taxauthid = '.$this->taxAuthId.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->hierarchyArr[] = $r->parenttid;
		}
		$rs->free();
		if($this->hierarchyArr){
			$sql2 = 'SELECT sciname '.
				'FROM taxa '.
				'WHERE (tid IN('.implode(',',$this->hierarchyArr).')) AND (rankid = 10)';
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$this->kingdomName = $r2->sciname;
			}
			$rs2->free();
		}
	}

	private function setSynonyms(): void
	{
		$sql = 'SELECT t.tid, t.sciname, t.author, ts.unacceptabilityreason, ts.notes, ts.sortsequence ' .
			'FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.tid ' .
			'WHERE (ts.taxauthid = ' .$this->taxAuthId. ') AND (ts.tid <> ts.tidaccepted) AND (ts.tidaccepted = ' .$this->tid. ') ' .
			'ORDER BY ts.sortsequence,t.sciname';
		//echo $sql."<br>";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->synonymArr[$r->tid]['sciname'] = $r->sciname;
			$this->synonymArr[$r->tid]['author'] = $r->author;
			$this->synonymArr[$r->tid]['unacceptabilityreason'] = $r->unacceptabilityreason;
			$this->synonymArr[$r->tid]['notes'] = $r->notes;
			$this->synonymArr[$r->tid]['sortsequence'] = $r->sortsequence;
		}
		$rs->free();
	}

	private function setParentName(): void
	{
		$sql = 'SELECT t.sciname, t.author ' .
			'FROM taxa t ' .
			'WHERE (t.tid = ' .$this->parentTid. ')';
		//echo $sql."<br>";
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->parentNameFull = '<i>'.$r->sciname.'</i> '.$r->author;
			$this->parentName = $r->sciname;
		}
		$rs->free();
	}

	public function submitTaxonEdits($postArr): string
	{
		$statusStr = '';
		$sql = 'UPDATE taxa SET '.
			'unitind1 = '.($postArr['unitind1']?'"'.Sanitizer::cleanInStr($postArr['unitind1']).'"':'NULL').', '.
			'unitname1 = "'.Sanitizer::cleanInStr($postArr['unitname1']).'",'.
			'unitind2 = '.($postArr['unitind2']?'"'.Sanitizer::cleanInStr($postArr['unitind2']).'"':'NULL').', '.
			'unitname2 = '.($postArr['unitname2']?'"'.Sanitizer::cleanInStr($postArr['unitname2']).'"':'NULL').', '.
			'unitind3 = '.($postArr['unitind3']?'"'.Sanitizer::cleanInStr($postArr['unitind3']).'"':'NULL').', '.
			'unitname3 = '.($postArr['unitname3']?'"'.Sanitizer::cleanInStr($postArr['unitname3']).'"':'NULL').', '.
			'author = '.($postArr['author']?'"'.Sanitizer::cleanInStr($postArr['author']).'"':'NULL').', '.
			'rankid = '.(is_numeric($postArr['rankid'])?$postArr['rankid']:'NULL').', '.
			'source = '.($postArr['source']?'"'.Sanitizer::cleanInStr($postArr['source']).'"':'NULL').', '.
			'notes = '.($postArr['notes']?'"'.Sanitizer::cleanInStr($postArr['notes']).'"':'NULL').', '.
			'securitystatus = '.(is_numeric($postArr['securitystatus'])?$postArr['securitystatus']:'0').', '.
			'modifiedUid = '.$GLOBALS['SYMB_UID'].', '.
			'modifiedTimeStamp = "'.date('Y-m-d H:i:s').'",'.
			'sciname = "'.Sanitizer::cleanInStr(($postArr['unitind1']?$postArr['unitind1']. ' ' : '').
			$postArr['unitname1'].($postArr['unitind2']? ' ' .$postArr['unitind2']: '').
			($postArr['unitname2']? ' ' .$postArr['unitname2']: '').
			($postArr['unitind3']? ' ' .$postArr['unitind3']: '').
			($postArr['unitname3']? ' ' .$postArr['unitname3']: '')).'" '.
			'WHERE (tid = '.$this->tid.')';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR editing taxon.';
		}
		
		if(($postArr['securitystatus'] !== $_REQUEST['securitystatusstart']) && is_numeric($postArr['securitystatus'])) {
			$sql2 = 'UPDATE omoccurrences SET localitysecurity = '.$postArr['securitystatus'].' WHERE (tidinterpreted = '.$this->tid.') AND ISNULL(localitySecurityReason)';
			$this->conn->query($sql2);
		}
		return $statusStr;
	}
	
	public function submitTaxStatusEdits($parentTid,$tidAccepted): string
	{
		$status = '';
		if(is_numeric($parentTid) && is_numeric($tidAccepted)){
			$this->setTaxon();
			$sql = 'UPDATE taxstatus '.
				'SET parenttid = '.$parentTid.' '.
				'WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.$this->tid.') AND (tidaccepted = '.$tidAccepted.')';
			if($this->conn->query($sql)){
				$this->rebuildHierarchy();
			}
			else{
				$status = 'Unable to edit taxonomic placement. SQL: '.$sql; 
			}
		}
		return $status;
	}

	public function submitSynonymEdits($targetTid, $tidAccepted, $unacceptabilityReason, $notes, $sortSeq): string
	{
		$statusStr = '';
		if(is_numeric($tidAccepted)){
			$sql = 'UPDATE taxstatus SET unacceptabilityReason = '.($unacceptabilityReason?'"'.Sanitizer::cleanInStr($unacceptabilityReason).'"':'NULL').', '.
				' notes = '.($notes?'"'.Sanitizer::cleanInStr($notes).'"':'NULL').', sortsequence = '.(is_numeric($sortSeq)?$sortSeq:'NULL').
				' WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.$targetTid.') AND (tidaccepted = '.$tidAccepted.')';
			//echo $sql; exit();
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR submitting synonym edits.';
			}
		}
		return $statusStr;
	}

	public function submitAddAcceptedLink($tidAcc, $deleteOther): string
	{
		$family = '';
		$parentTid = 0;
		$statusStr = '';
		if(is_numeric($tidAcc)){
			$sqlFam = 'SELECT ts.family, ts.parenttid '.
				'FROM taxstatus ts WHERE (ts.tid = '.$this->tid.') AND (ts.taxauthid = '.$this->taxAuthId.')';
			$rs = $this->conn->query($sqlFam);
			if($row = $rs->fetch_object()){
				$family = $row->family;
				$parentTid = $row->parenttid;
			}
			$rs->free();
			
			if($deleteOther){
				$sqlDel = 'DELETE FROM taxstatus WHERE (tid = '.$this->tid.') AND (taxauthid = '.$this->taxAuthId.')';
				$this->conn->query($sqlDel);
			}
			$sql = 'INSERT INTO taxstatus (tid,tidaccepted,taxauthid,family,parenttid) '.
				'VALUES ('.$this->tid.', '.$tidAcc.', '.$this->taxAuthId.','.
				($family?'"'.$family.'"': 'NULL').','.
				$parentTid.') ';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR adding accepted link.';
			}
		}
		return $statusStr;
	}
	
	public function removeAcceptedLink($tidAccepted): string
	{
		$statusStr = '';
		if(is_numeric($tidAccepted)){
			$sql = 'DELETE FROM taxstatus WHERE (tid = '.$this->tid.') AND (tidaccepted = '.$tidAccepted.') AND (taxauthid = '.$this->taxAuthId.')';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR removing tidAccepted link.';
			}
		}
		return $statusStr;
	}

	public function submitChangeToAccepted($tid,$tidAccepted,$switchAcceptance): string
	{
		$statusStr = '';
		if(is_numeric($tid)){
			$sql = 'UPDATE taxstatus SET tidaccepted = '.$tid.
				' WHERE (tid = '.$tid.') AND (taxauthid = '.$this->taxAuthId.')';
			$this->conn->query($sql);
	
			if($switchAcceptance){
				$sqlSwitch = 'UPDATE taxstatus SET tidaccepted = '.$tid.
					' WHERE (tidaccepted = '.$tidAccepted.') AND (taxauthid = '.$this->taxAuthId.')';
				if(!$this->conn->query($sqlSwitch)){
					$statusStr = 'ERROR changing to accepted.';
				}
				
				$this->updateDependentData($tidAccepted,$tid);
			}
		}
		return $statusStr;
	}
	
	public function submitChangeToNotAccepted($tid,$tidAccepted,$reason,$notes): string
	{
		$status = '';
		if(is_numeric($tid)){
			$sql = 'UPDATE taxstatus '.
				'SET tidaccepted = '.$tidAccepted.', unacceptabilityreason = '.($reason?'"'.$reason.'"':'NULL').
				', notes = '.($notes?'"'.$notes.'"':'NULL').' '.
				'WHERE (tid = '.$tid.') AND (taxauthid = '.$this->taxAuthId.')';
			//echo $sql;
			if($this->conn->query($sql)) {
				$sqlSyns = 'UPDATE taxstatus SET tidaccepted = '.$tidAccepted.' WHERE (tidaccepted = '.$tid.') AND (taxauthid = '.$this->taxAuthId.')';
				if(!$this->conn->query($sqlSyns)){
					$status = 'ERROR: unable to transfer linked synonyms to accepted taxon.';
				}
				
				$this->updateDependentData($tid,$tidAccepted);
			}
			else {
				$status = 'ERROR: unable to switch acceptance.';
			}
		}
		return $status;
	}
	
	private function updateDependentData($tid, $tidNew): void
	{
		if(is_numeric($tid) && is_numeric($tidNew)){
			$this->conn->query('DELETE FROM kmdescr WHERE inherited IS NOT NULL AND (tid = '.$tid.')');
			$this->conn->query('UPDATE IGNORE kmdescr SET tid = '.$tidNew.' WHERE (tid = '.$tid.')');
			$this->conn->query('DELETE FROM kmdescr WHERE (tid = '.$tid.')');
			$this->resetCharStateInheritance($tidNew);
			
			$sqlVerns = 'UPDATE taxavernaculars SET tid = '.$tidNew.' WHERE (tid = '.$tid.')';
			$this->conn->query($sqlVerns);
			
			$sqltl = 'UPDATE taxalinks SET tid = '.$tidNew.' WHERE (tid = '.$tid.')';
			$this->conn->query($sqltl);
		}		
	}
	
	private function resetCharStateInheritance($tid): void
	{
		$sqlAdd1 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
			'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
			'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
			'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
			'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
			'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
			'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
			'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
			'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
			'AND (t2.tid = '.$tid.') AND ISNULL(d2.CID)';
		$this->conn->query($sqlAdd1);

		if($this->rankid === 140){
			$sqlAdd2a = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
				'AND (t2.RankId = 180) AND (t1.tid = '.$tid.') AND ISNULL(d2.CID)';
			//echo $sqlAdd2a;
			$this->conn->query($sqlAdd2a);
			$sqlAdd2b = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				"WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.family = '".
				$this->sciName."') AND (ts2.tid = ts2.tidaccepted) ".
				'AND (t2.RankId = 220) AND ISNULL(d2.CID)';
			$this->conn->query($sqlAdd2b);
		}

		if($this->rankid > 140 && $this->rankid < 220){
			$sqlAdd3 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
				'AND (t2.RankId = 220) AND (t1.tid = '.$tid.') AND ISNULL(d2.CID)';
			//echo $sqlAdd2b;
			$this->conn->query($sqlAdd3);
		}
	}

	public function rebuildHierarchy($tid = null): void
	{
		if(!$tid) {
			$tid = $this->tid;
		}
		if(!$this->rankid) {
			$this->setTaxon();
		}
		$parentArr = array();
		$parCnt = 0;
		$targetTid = $tid;
		do{
			$sql1 = 'SELECT DISTINCT ts.parenttid '.
				'FROM taxstatus ts '.
				'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts.tid = '.$targetTid.')';
			//echo $sqlParents;
			$targetTid = 0;
			$rs1 = $this->conn->query($sql1);
			if(($r1 = $rs1->fetch_object()) && $r1->parenttid) {
				if(in_array($r1->parenttid, $parentArr, true)) {
					break;
				}
				$parentArr[] = $r1->parenttid;
				$targetTid = $r1->parenttid;
			}
			$rs1->free();
			$parCnt++;
		}while($targetTid && $parCnt < 16);

        /** @noinspection UnNecessaryDoubleQuotesInspection */
        $trueHierarchyStr = implode(',',array_reverse($parentArr));
		if($parentArr !== $this->hierarchyArr){
			$branchTidArr = array($tid);
			$sql2 = 'SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = '.$tid;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$branchTidArr[] = $r2->tid;
			}
			$rs2->free();
			if($this->hierarchyArr){
				$sql2a = 'DELETE FROM taxaenumtree '.
					'WHERE parenttid IN('.implode(',',$this->hierarchyArr).') AND (tid IN ('.implode(',',$branchTidArr).')) '.
					'AND (taxauthid = '.$this->taxAuthId.') ';
				//echo $sql2a; exit;
				$this->conn->query($sql2a);
			}

			$sql3 = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid,taxauthid) ';
			foreach($parentArr as $pid){
				$sql3a = $sql3.'SELECT DISTINCT tid,'.$pid.','.$this->taxAuthId.' FROM taxaenumtree WHERE parenttid = '.$tid;
				$this->conn->query($sql3a);
				//echo $sql3a.'<br/>';
				$sql3b = $sql3.'VALUES('.$tid.','.$pid.','.$this->taxAuthId.')';
				$this->conn->query($sql3b);
				//echo $sql3b.'<br/>';
			}
			$this->setHierarchy();
		}

		if($this->rankid > 140){
			$newFam = '';
			$sqlFam1 = 'SELECT sciname FROM taxa WHERE (tid IN('.$trueHierarchyStr.')) AND rankid = 140';
			$rsFam1 = $this->conn->query($sqlFam1);
			if($r1 = $rsFam1->fetch_object()){
				$newFam = $r1->sciname;
			}
			$rsFam1->free();
			
			$sqlFam2 = 'SELECT family FROM taxstatus WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.$tid.')';
			$rsFam2 = $this->conn->query($sqlFam2);
			if(($rFam2 = $rsFam2->fetch_object()) && $newFam !== $rFam2->family) {
				$sql = 'UPDATE taxstatus ts INNER JOIN taxaenumtree e ON ts.tid = e.tid '.
					'SET ts.family = '.($newFam?'"'.Sanitizer::cleanInStr($newFam).'"':'Not assigned').' '.
					'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (e.taxauthid = '.$this->taxAuthId.') AND '.
					'((ts.tid = '.$tid.') OR (e.parenttid = '.$tid.'))';
				//echo $sql;
				$this->conn->query($sql);
			}
			$rsFam2->free();
		}
	}

	public function loadNewName($dataArr){
		$retStr = '';
        $tid = 0;
	    $sqlTaxa = 'INSERT INTO taxa(sciname, author, rankid, unitind1, unitname1, unitind2, unitname2, unitind3, unitname3, '.
			'source, notes, securitystatus, modifiedUid, modifiedTimeStamp) '.
			'VALUES ("'.Sanitizer::cleanInStr($dataArr['sciname']).'",'.
			($dataArr['author']?'"'.Sanitizer::cleanInStr($dataArr['author']).'"':'NULL').','.
			($dataArr['rankid']?:'NULL').','.
			($dataArr['unitind1']?'"'.Sanitizer::cleanInStr($dataArr['unitind1']).'"':'NULL').',"'.
			Sanitizer::cleanInStr($dataArr['unitname1']).'",'.
			($dataArr['unitind2']?'"'.Sanitizer::cleanInStr($dataArr['unitind2']).'"':'NULL').','.
			($dataArr['unitname2']?'"'.Sanitizer::cleanInStr($dataArr['unitname2']).'"':'NULL').','.
			($dataArr['unitind3']?'"'.Sanitizer::cleanInStr($dataArr['unitind3']).'"':'NULL').','.
			($dataArr['unitname3']?'"'.Sanitizer::cleanInStr($dataArr['unitname3']).'"':'NULL').','.
			($dataArr['source']?'"'.Sanitizer::cleanInStr($dataArr['source']).'"':'NULL').','.
			($dataArr['notes']?'"'.Sanitizer::cleanInStr($dataArr['notes']).'"':'NULL').','.
			Sanitizer::cleanInStr($dataArr['securitystatus']).','.
			$GLOBALS['SYMB_UID'].',"'.date('Y-m-d H:i:s').'")';
		//echo "sqlTaxa: ".$sqlTaxa;
		if($this->conn->query($sqlTaxa)){
			$tid = $this->conn->insert_id;
		 	$tidAccepted = ($dataArr['acceptstatus']?$tid:$dataArr['tidaccepted']);
			$parTid = Sanitizer::cleanInStr($dataArr['parenttid']);
			if(!$parTid && $dataArr['rankid'] <= 10) {
				$parTid = $tid;
			}
			if(!$parTid && $dataArr['parentname']){
				$sqlPar = 'SELECT tid FROM taxa WHERE sciname = "'.$dataArr['parentname'].'"';
				$rsPar = $this->conn->query($sqlPar);
				if($rPar = $rsPar->fetch_object()){
					$parTid = $rPar->tid;
				}
				$rsPar->free();
			}
			if($parTid){ 
				$family = '';
				if($dataArr['rankid'] > 140){
					$sqlFam = 'SELECT t.sciname '.
						'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.parenttid '.
						'WHERE (t.tid = '.$parTid.' OR e.tid = '.$parTid.') AND t.rankid = 140 ';
					//echo $sqlFam; exit;
					$rsFam = $this->conn->query($sqlFam);
					if($r = $rsFam->fetch_object()){
						$family = $r->sciname;
					}
					$rsFam->free();
				}
				
				$sqlTaxStatus = 'INSERT INTO taxstatus(tid, tidaccepted, taxauthid, family, parenttid, unacceptabilityreason) '.
					'VALUES ('.$tid.','.$tidAccepted.','.$this->taxAuthId.','.($family?'"'.Sanitizer::cleanInStr($family).'"':'NULL').','.
					$parTid.','.($dataArr['unacceptabilityreason']?'"'.Sanitizer::cleanInStr($dataArr['unacceptabilityreason']).'"':'NULL').') ';
				//echo "sqlTaxStatus: ".$sqlTaxStatus;
				if($this->conn->query($sqlTaxStatus)) {
                    $sqlEnumTree = 'INSERT INTO taxaenumtree(tid,parenttid,taxauthid) '.
                        'SELECT '.$tid.' as tid, parenttid, taxauthid FROM taxaenumtree WHERE tid = '.$parTid;
                    if($this->conn->query($sqlEnumTree)){
                        $sqlEnumTree2 = 'INSERT INTO taxaenumtree(tid,parenttid,taxauthid) '.
                            'VALUES ('.$tid.','.$parTid.','.$this->taxAuthId.')';
                        if(!$this->conn->query($sqlEnumTree2)){
                            echo 'WARNING: Taxon loaded into taxa, but failed to populate taxaenumtree(2).';
                        }
                    }
                    else{
                        echo 'WARNING: Taxon loaded into taxa, but failed to populate taxaenumtree.';
                    }

                    $sql1 = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname SET o.TidInterpreted = t.tid ';
                    if($dataArr['securitystatus'] === 1) {
                        $sql1 .= ',o.localitysecurity = 1 ';
                    }
                    $sql1 .= 'WHERE (o.sciname = "'.Sanitizer::cleanInStr($dataArr['sciname']).'") ';
                    if(!$this->conn->query($sql1)){
                        echo 'WARNING: Taxon loaded into taxa, but update occurrences with matching name.';
                    }

                    $sql2 = 'UPDATE omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
                        'SET i.tid = o.tidinterpreted '.
                        'WHERE i.tid IS NULL AND o.tidinterpreted IS NOT NULL';
                    $this->conn->query($sql2);
                    if(!$this->conn->query($sql2)){
                        echo 'WARNING: Taxon loaded into taxa, but update occurrence images with matching name.';
                    }

                    $sql3 = 'INSERT IGNORE INTO omoccurgeoindex(tid,decimallatitude,decimallongitude) '.
                        'SELECT DISTINCT o.tidinterpreted, round(o.decimallatitude,3), round(o.decimallongitude,3) '.
                        'FROM omoccurrences o '.
                        'WHERE (o.tidinterpreted = '.$tid.') AND (ISNULL(o.cultivationStatus) OR o.cultivationStatus <> 1) AND o.decimallatitude IS NOT NULL AND o.decimallongitude IS NOT NULL';
                    $this->conn->query($sql3);
                }
				else {
                    $retStr = 'ERROR: Taxon loaded into taxa, but failed to load taxstatus.';
				}
			}
			else{
                $retStr = 'ERROR loading taxon due to missing parentTid';
			}
		}
		else{
            $retStr = 'ERROR inserting new taxon.';
		}
		if($tid){
            $retStr = $tid;
        }
		return $retStr;
	}

	public function verifyDeleteTaxon(): array
	{
		$retArr = array();

		$sql ='SELECT t.tid, t.sciname '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '. 
			'WHERE ts.parenttid = '.$this->tid.' ORDER BY t.sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['child'][$r->tid] = $r->sciname;
		}
		$rs->free();
		
		$sql ='SELECT t.tid, t.sciname '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '. 
			'WHERE ts.tidaccepted = '.$this->tid.' AND ts.tid <> ts.tidaccepted ORDER BY t.sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['syn'][$r->tid] = $r->sciname;
		}
		$rs->free();
		
		$sql ='SELECT COUNT(imgid) AS cnt FROM images WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['img'] = $r->cnt;
		}
		$rs->free();
		
		$sql ='SELECT vernacularname FROM taxavernaculars WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['vern'][] = $r->vernacularname;
		}
		$rs->free();

		$sql ='SELECT tdbid,caption FROM taxadescrblock WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['tdesc'][$r->tdbid] = $r->caption;
		}
		$rs->free();
		
		$sql ='SELECT occid FROM omoccurrences WHERE tidinterpreted = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['occur'][] = $r->occid;
		}
		$rs->free();
		
		$sql ='SELECT occid FROM omoccurdeterminations WHERE tidinterpreted = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['dets'][] = $r->occid;
		}
		$rs->free();
		
		$sql ='SELECT c.clid, c.name '.
			'FROM fmchecklists c INNER JOIN fmchklsttaxalink cl ON c.clid = cl.clid '.
			'WHERE cl.tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['cl'][$r->clid] = $r->name;
		}
		$rs->free();
		
		$sql ='SELECT COUNT(*) AS cnt FROM kmdescr WHERE inherited IS NULL AND tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['kmdesc'] = $r->cnt;
		}
		$rs->free();
		
		$sql ='SELECT title FROM taxalinks WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['link'][] = $r->title;
		}
		$rs->free();
		
		return $retArr;
	}
	
	public function transferResources($targetTid): ?string
	{
		$statusStr = '';
		$delStatusStr = '';
		if(is_numeric($targetTid)){
			$sql ='UPDATE omoccurrences SET tidinterpreted = '.$targetTid.' WHERE tidinterpreted = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring occurrence records<br/>';
			}
			$sql ='UPDATE omoccurdeterminations SET tidinterpreted = '.$targetTid.' WHERE tidinterpreted = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring occurrence determination records<br/>';
			}

			$sql ='UPDATE IGNORE images SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring image links<br/>';
			}
			
			$sql ='UPDATE IGNORE taxavernaculars SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring vernaculars<br/>';
			}
			
			$sql ='UPDATE IGNORE taxadescrblock SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring taxadescblocks<br/>';
			}
			
			$sql ='UPDATE IGNORE fmchklsttaxalink SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring checklist links<br/>';
			}
			$sql ='UPDATE IGNORE fmvouchers SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring vouchers<br/>';
			}
			$sql ='DELETE FROM fmvouchers WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR deleting leftover vouchers<br/>';
			}
			$sql ='DELETE FROM fmchklsttaxalink WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR deleting leftover checklist links<br/>';
			}
				
			$sql ='UPDATE IGNORE kmdescr SET tid = '.$targetTid.' WHERE inherited IS NULL AND tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring morphology for ID key<br/>';
			}
			
			$sql ='UPDATE IGNORE taxalinks SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring taxa links<br/>';
			}

			$delStatusStr = $this->deleteTaxon(); 
			if($statusStr) {
				$delStatusStr .= $statusStr;
			}
		}
		return $delStatusStr;
	}

	public function deleteTaxon(): string
	{
		$statusStr = '';
		$sql ='UPDATE images SET tid = NULL WHERE occid IS NOT NULL AND tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR setting tid to NULL for occurrence images in deleteTaxon method<br/>';
		}
		$sql ='DELETE FROM images WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting remaining links in deleteTaxon method<br/>';
		}
		
		$sql ='DELETE FROM taxavernaculars WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting vernaculars in deleteTaxon method<br/>';
		}
		
		$sql ='DELETE FROM taxadescrblock WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting taxa description blocks in deleteTaxon method<br/>';
		}

		$sql = 'UPDATE omoccurrences SET tidinterpreted = NULL WHERE tidinterpreted = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR setting tidinterpreted to NULL in deleteTaxon method<br/>';
		}
		
		$sql ='DELETE FROM fmvouchers WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting voucher links in deleteTaxon method<br/>';
		}
		
		$sql ='DELETE FROM fmchklsttaxalink WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting checklist links in deleteTaxon method<br/>';
		}
		
		$sql ='DELETE FROM kmdescr WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting morphology for ID Key in deleteTaxon method<br/>';
		}

		$sql ='DELETE FROM taxalinks WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR deleting taxa links in deleteTaxon method<br/>';
		}

		$taxStatusArr = array();
		$sqlTS = 'SELECT tidaccepted, parenttid, family, unacceptabilityreason, notes, sortsequence '.
			'FROM taxstatus WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sqlTS);
		if($r = $rs->fetch_object()){
			$taxStatusArr[0]['tidaccepted'] = $r->tidaccepted;
			$taxStatusArr[0]['parenttid'] = $r->parenttid;
			$taxStatusArr[0]['family'] = $r->family;
			$taxStatusArr[0]['unacceptabilityreason'] = $r->unacceptabilityreason;
			$taxStatusArr[0]['notes'] = $r->notes;
			$taxStatusArr[0]['sortsequence'] = $r->sortsequence;
		}
		$rs->free();

		$statusStrFinal = 'SUCCESS: taxon deleted!<br/>';
		$sql ='DELETE FROM taxstatus WHERE (tid = '.$this->tid.') OR (tidaccepted = '.$this->tid.')';
		if($this->conn->query($sql)){
			$sql ='DELETE FROM taxa WHERE (tid = '.$this->tid.')';
			if(!$this->conn->query($sql)){
				$statusStrFinal = 'ERROR attempting to delete taxon.<br/>';
				$tsNewSql = 'INSERT INTO taxstatus(tid,taxauthid,tidaccepted, parenttid, family, unacceptabilityreason, notes, sortsequence) '.
					'VALUES('.$this->tid.','.$this->taxAuthId.','.$taxStatusArr[0]['tidaccepted'].','.$taxStatusArr[0]['parenttid'].',"'.
					$taxStatusArr[0]['family'].'","'.$taxStatusArr[0]['unacceptabilityreason'].'","'.
					$taxStatusArr[0]['unacceptabilityreason'].'",'.$taxStatusArr[0]['sortsequence'].')';
				$this->conn->query($tsNewSql);
			}
		}
		else{
			$statusStrFinal = 'ERROR attempting to delete taxon status<br/>';
		}

		if($statusStr){
			$statusStrFinal .= $statusStr;
		}
		return $statusStrFinal;
	}

	public function setTid($tid): void
	{
		if(is_numeric($tid)){
			$this->tid = $tid;
		}
	}
	
	public function getTid(): int
	{
		return $this->tid;
	}
	
	public function setTaxAuthId($taid): void
	{
		if(is_numeric($taid)){
			$this->taxAuthId = $taid;
		}
	}

	public function getFamily(){
		return $this->family;
	}

	public function getSciName(){
		return $this->sciName;
	}

	public function getKingdomName(){
		return $this->kingdomName;
	}

	public function getRankId(): int
	{
		return $this->rankid;
	}
	
	public function getRankName(){
		return $this->rankName;
	}

	public function getUnitInd1(){
		return $this->unitInd1;
	}

	public function getUnitName1(){
		return $this->unitName1;
	}

	public function getUnitInd2(){
		return $this->unitInd2;
	}

	public function getUnitName2(){
		return $this->unitName2;
	}

	public function getUnitInd3(){
		return $this->unitInd3;
	}

	public function getUnitName3(){
		return $this->unitName3;
	}

	public function getAuthor(){
		return $this->author;
	}

	public function getParentTid(): int
	{
		return $this->parentTid;
	}

	public function getParentName(){
		return $this->parentName;
	}

	public function getParentNameFull(){
		return $this->parentNameFull;
	}

	public function getSource(){
		return $this->source;
	}

	public function getNotes(){
		return $this->notes;
	}
	
	public function getErrorStr(): string
	{
		return $this->errorStr;
	}

	public function getSecurityStatus(){
		return $this->securityStatus;
	}

	public function getIsAccepted(): int
	{
		return $this->isAccepted;
	}

	public function getAcceptedArr(): array
	{
		return $this->acceptedArr;
	}
	
	public function getSynonyms(): array
	{
		return $this->synonymArr;
	}

	public function getTaxonomicThesaurusIds(): array
	{
		$retArr = array();
		if($this->tid){
			$sql = 'SELECT ta.taxauthid, ta.name FROM taxauthority ta INNER JOIN taxstatus ts ON ta.taxauthid = ts.taxauthid '.
				'WHERE ta.isactive = 1 AND (ts.tid = '.$this->tid.') ORDER BY ta.taxauthid ';
			$rs = $this->conn->query($sql); 
			while($row = $rs->fetch_object()){
				$retArr[$row->taxauthid] = $row->name;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getRankArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits ';
		if($this->kingdomId){
            $sql .= 'WHERE kingdomid = '.$this->kingdomId.' ';
        }
        $sql .= 'ORDER BY rankid ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->rankid] = $r->rankname;
		}
		$rs->free();
		return $retArr;
	}  

	public function getHierarchyArr(): array
	{
		$retArr = array();
		if($this->hierarchyArr){
			$sql = 'SELECT t.tid, t.sciname, ts.parenttid, t.rankid '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (t.tid IN('.implode(',',$this->hierarchyArr).')) '.
				'ORDER BY t.rankid, t.sciname ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			$nonRanked = array();
			while($r = $rs->fetch_object()){
				if($r->rankid){
					$retArr[$r->tid] = $r->sciname;
				}
				else{
					$nonRanked[$r->parenttid]['name'] = $r->sciname;
					$nonRanked[$r->parenttid]['tid'] = $r->tid;
				}
				if($nonRanked && array_key_exists($r->tid,$nonRanked)){
					$retArr[$nonRanked[$r->tid]['tid']] = $nonRanked[$r->tid]['name'];
				}
			}
			$rs->free();
		}
		return $retArr;
	}
}

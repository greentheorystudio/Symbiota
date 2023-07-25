<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');

class TaxonomyEditorManager{

	private $conn;
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
		$sqlTaxon = 'SELECT t.tid, t.kingdomId, t.rankid, t.sciname, t.unitind1, t.unitname1, t.parenttid, t.tidaccepted, t.family, '.
			't.unitind2, t.unitname2, t.unitind3, t.unitname3, t.author, t.source, t.notes, t.securitystatus, t.initialtimestamp, '.
            't2.sciname AS scinameaccepted, t2.author AS authoraccepted, t2.notes AS notesaccepted '.
			'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.tid '.
            'WHERE t.tid = '.$this->tid.' ';
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
            $this->parentTid = (int)$r->parenttid;
            $this->family = $r->family;
            $tidAccepted = (int)$r->tidaccepted;
            if($this->tid === $tidAccepted){
                $this->isAccepted = 1;
            }
            else{
                $this->isAccepted = 0;
                $this->acceptedArr[$tidAccepted]['sciname'] = $r->scinameaccepted;
                $this->acceptedArr[$tidAccepted]['author'] = $r->authoraccepted;
                $this->acceptedArr[$tidAccepted]['usagenotes'] = $r->notesaccepted;
            }
		}
		$rs->free();
		
		if($this->sciName){
			$this->setRankName();
			$this->setHierarchy();
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
			'WHERE (tid = '.$this->tid.')';
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
		$sql = 'SELECT tid, sciname, author FROM taxa ' .
			'WHERE tid <> tidaccepted AND tidaccepted = ' .$this->tid. ' ' .
			'ORDER BY sciname';
		//echo $sql."<br>";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->synonymArr[$r->tid]['sciname'] = $r->sciname;
			$this->synonymArr[$r->tid]['author'] = $r->author;
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

    public function editTaxon($postArr,$tId = null): string
    {
        if(!$tId){
            $tId = $this->tid;
        }
        $statusStr = '';
        if($tId){
            $sql = 'UPDATE taxa SET ';
            if(array_key_exists('kingdomid',$postArr) && (int)$postArr['kingdomid']){
                $sql .= 'kingdomId = '.(int)$postArr['kingdomid'].', ';
            }
            if(array_key_exists('unitind1',$postArr)){
                $sql .= 'unitind1 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitind1'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitind1']).'"':'NULL').', ';
            }
            if(array_key_exists('unitname1',$postArr) && $postArr['unitname1']){
                $sql .= 'unitname1 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitname1'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitname1']).'"':'NULL').', ';
            }
            if(array_key_exists('unitind2',$postArr)){
                $sql .= 'unitind2 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitind2'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitind2']).'"':'NULL').', ';
            }
            if(array_key_exists('unitname2',$postArr)){
                $sql .= 'unitname2 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitname2'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitname2']).'"':'NULL').', ';
            }
            if(array_key_exists('unitind3',$postArr)){
                $sql .= 'unitind3 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitind3'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitind3']).'"':'NULL').', ';
            }
            if(array_key_exists('unitname3',$postArr)){
                $sql .= 'unitname3 = '.(Sanitizer::cleanInStr($this->conn,$postArr['unitname3'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['unitname3']).'"':'NULL').', ';
            }
            if(array_key_exists('author',$postArr)){
                $sql .= 'author = '.(Sanitizer::cleanInStr($this->conn,$postArr['author'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['author']).'"':'NULL').', ';
            }
            if(array_key_exists('family',$postArr)){
                $sql .= 'family = '.(Sanitizer::cleanInStr($this->conn,$postArr['family'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['family']).'"':'NULL').', ';
            }
            if(array_key_exists('rankid',$postArr) && (int)$postArr['rankid']){
                $sql .= 'rankid = '.(int)$postArr['rankid'].', ';
            }
            if(array_key_exists('source',$postArr)){
                $sql .= '`source` = '.(Sanitizer::cleanInStr($this->conn,$postArr['source'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['source']).'"':'NULL').', ';
            }
            if(array_key_exists('notes',$postArr)){
                $sql .= 'notes = '.(Sanitizer::cleanInStr($this->conn,$postArr['notes'])?'"'.Sanitizer::cleanInStr($this->conn,$postArr['notes']).'"':'NULL').', ';
            }
            if(array_key_exists('securitystatus',$postArr)){
                $sql .= 'securitystatus = '.(int)$postArr['securitystatus'].', ';
            }
            if(array_key_exists('sciname',$postArr) && $postArr['sciname']){
                $sql .= 'sciname = "'.Sanitizer::cleanInStr($this->conn,$postArr['sciname']).'", ';
            }
            elseif(array_key_exists('unitname1',$postArr) && $postArr['unitname1']){
                $sql .= 'sciname = "'.Sanitizer::cleanInStr($this->conn,($postArr['unitind1']?$postArr['unitind1']. ' ' : '').
                        $postArr['unitname1'].($postArr['unitind2']? ' ' .$postArr['unitind2']: '').
                        ($postArr['unitname2']? ' ' .$postArr['unitname2']: '').
                        ($postArr['unitind3']? ' ' .$postArr['unitind3']: '').
                        ($postArr['unitname3']? ' ' .$postArr['unitname3']: '')).'", ';
            }
            $sql .= 'modifiedUid = '.$GLOBALS['SYMB_UID'].', ';
            $sql .= 'modifiedTimeStamp = "'.date('Y-m-d H:i:s').'" ';
            $sql .= 'WHERE TID = '.$tId.' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $statusStr = 'ERROR editing taxon.';
            }
            if(array_key_exists('securitystatus',$postArr) && array_key_exists('securitystatusstart',$postArr) && $postArr['securitystatus'] !== $_REQUEST['securitystatusstart'] && is_numeric($postArr['securitystatus'])) {
                $sql2 = 'UPDATE omoccurrences SET localitysecurity = '.$postArr['securitystatus'].' WHERE tid = '.$this->tid.' AND ISNULL(localitySecurityReason) ';
                $this->conn->query($sql2);
            }
        }
        return $statusStr;
    }
	
	public function editTaxonParent($parentTid,$tId = null): string
	{
		if(!$tId){
            $tId = $this->tid;
        }
        $status = '';
		if(is_numeric($parentTid) && $parentTid){
			$this->setTaxon();
			$sql = 'UPDATE taxa '.
				'SET parenttid = '.$parentTid.' '.
				'WHERE tid = '.$tId.' ';
			if(!$this->conn->query($sql)){
                $status = 'Unable to edit taxonomic placement.';
			}
		}
		return $status;
	}

	public function submitAddAcceptedLink($tidAcc): string
	{
		$family = '';
		$parentTid = 0;
		$statusStr = '';
		if(is_numeric($tidAcc)){
			$sql = 'UPDATE taxa SET tidaccepted = '.$tidAcc.' WHERE tid = '.$this->tid;
			//echo $sql;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR adding accepted link.';
			}
		}
		return $statusStr;
	}
	
	public function submitChangeToAccepted($tid,$tidAccepted): string
	{
		$statusStr = '';
		if(is_numeric($tid)){
			$sql = 'UPDATE taxa SET tidaccepted = '.$tid.
				' WHERE tid = '.$tid.' ';
			$this->conn->query($sql);
	        $this->updateDependentData($tidAccepted,$tid);
		}
		return $statusStr;
	}
	
	public function submitChangeToNotAccepted($tid,$tidAccepted,$kingdom = false): string
	{
		$status = '';
		if(is_numeric($tid)){
			$sql = 'UPDATE taxa SET tidaccepted = '.$tidAccepted.', parenttid = (SELECT parenttid FROM taxa WHERE TID = '.$tidAccepted.'), kingdomId = (SELECT kingdomId FROM taxa WHERE TID = '.$tidAccepted.') WHERE tid = '.$tid.' ';
			//echo $sql;
			if($this->conn->query($sql)) {
				$sqlSyns = 'UPDATE taxa SET tidaccepted = '.$tidAccepted.', parenttid = (SELECT parenttid FROM taxa WHERE TID = '.$tidAccepted.'), kingdomId = (SELECT kingdomId FROM taxa WHERE TID = '.$tidAccepted.') WHERE tidaccepted = '.$tid.' ';
				if(!$this->conn->query($sqlSyns)){
					$status = 'ERROR: unable to transfer linked synonyms to accepted taxon.';
				}
                $sqlParent = 'UPDATE taxa SET parenttid = '.$tidAccepted.', kingdomId = (SELECT kingdomId FROM taxa WHERE TID = '.$tidAccepted.') WHERE parenttid = '.$tid.' ';
                if(!$this->conn->query($sqlParent)){
                    $status = 'ERROR: unable to transfer children taxa to accepted taxon.';
                }
                $sqlHierarchy = 'UPDATE taxaenumtree SET parenttid = '.$tidAccepted.', kingdomId = (SELECT kingdomId FROM taxa WHERE TID = '.$tidAccepted.') WHERE parenttid = '.$tid.' ';
                if(!$this->conn->query($sqlHierarchy)){
                    $status = 'ERROR: unable to update taxonomic hierarchy with accepted taxon.';
                }
                if((int)$tid !== (int)$tidAccepted){
                    $sqlHierarchy = 'DELETE FROM taxaenumtree WHERE tid = '.$tid.' ';
                    if(!$this->conn->query($sqlHierarchy)){
                        $status = 'ERROR: unable to remove taxonomic hierarchy for unaccepted taxon.';
                    }
                }
				if($kingdom){
                    $this->updateKingdomAcceptance($tid,$tidAccepted);
                }
				$this->updateDependentData($tid,$tidAccepted);
			}
			else {
				$status = 'ERROR: unable to switch acceptance.';
			}
		}
		return $status;
	}

    private function updateKingdomAcceptance($tid, $tidNew): void
    {
        if(is_numeric($tid) && is_numeric($tidNew)){
            $oldKingdomId = 0;
            $newKingdomId = 0;
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = '.$tid.' ';
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $oldKingdomId = $r->kingdom_id;
            }
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = '.$tidNew.' ';
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $newKingdomId = $r->kingdom_id;
            }
            if($oldKingdomId && $newKingdomId){
                $sql = 'UPDATE taxa SET kingdomId = '.$newKingdomId.' WHERE kingdomId = '.$oldKingdomId.' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonkingdoms WHERE kingdom_id = '.$oldKingdomId.' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonunits WHERE kingdomid = '.$oldKingdomId.' ';
                $this->conn->query($sql);
            }
        }
    }
	
	private function updateDependentData($tid, $tidNew): void
	{
		if(is_numeric($tid) && is_numeric($tidNew)){
			$this->conn->query('DELETE FROM kmdescr WHERE inherited IS NOT NULL AND tid = '.$tid.' ');
			$this->conn->query('UPDATE IGNORE kmdescr SET tid = '.$tidNew.' WHERE tid = '.$tid.' ');
			$this->conn->query('DELETE FROM kmdescr WHERE tid = '.$tid.' ');
			$this->resetCharStateInheritance($tidNew);

            $sqlVerns = 'DELETE v2.* '.
                'FROM taxavernaculars AS v1 LEFT JOIN taxavernaculars AS v2 ON v1.VernacularName = v2.VernacularName AND v1.langid = v2.langid '.
                'WHERE v1.TID = '.$tidNew.' AND v2.TID = '.$tid.' AND v2.VID IS NOT NULL ';
            $this->conn->query($sqlVerns);
			
			$sqlVerns = 'UPDATE taxavernaculars SET tid = '.$tidNew.' WHERE tid = '.$tid.' ';
			$this->conn->query($sqlVerns);
		}
	}
	
	private function resetCharStateInheritance($tid): void
	{
		$sqlAdd1 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
			'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
			'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
			'FROM ((taxa AS t1 INNER JOIN kmdescr AS d1 ON t1.TID = d1.TID) '.
			'INNER JOIN taxa AS t2 ON t1.tidaccepted = t2.parenttid) '.
			'LEFT JOIN kmdescr AS d2 ON d1.CID = d2.CID AND t2.TID = d2.TID '.
			'WHERE t2.tid = t2.tidaccepted AND t2.tid = '.$tid.' AND ISNULL(d2.CID) ';
		$this->conn->query($sqlAdd1);

		if($this->rankid === 140){
			$sqlAdd2a = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((taxa AS t1 INNER JOIN kmdescr AS d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxa AS t2 ON t1.tidaccepted = t2.parenttid) '.
				'LEFT JOIN kmdescr AS d2 ON d1.CID = d2.CID AND t2.TID = d2.TID '.
				'WHERE t2.tid = t2.tidaccepted '.
				'AND t2.RankId = 180 AND t1.tid = '.$tid.' AND ISNULL(d2.CID) ';
			//echo $sqlAdd2a;
			$this->conn->query($sqlAdd2a);
			$sqlAdd2b = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((taxa AS t1 INNER JOIN kmdescr AS d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxa AS t2 ON t1.tidaccepted = t2.parenttid) '.
				'LEFT JOIN kmdescr AS d2 ON d1.CID = d2.CID AND t2.TID = d2.TID '.
				"WHERE t2.family = '".$this->sciName."' AND t2.tid = t2.tidaccepted ".
				'AND t2.RankId = 220 AND ISNULL(d2.CID) ';
			$this->conn->query($sqlAdd2b);
		}

		if($this->rankid > 140 && $this->rankid < 220){
			$sqlAdd3 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((taxa AS t1 INNER JOIN kmdescr AS d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxa AS t2 ON t1.tidaccepted = t2.parenttid) '.
				'LEFT JOIN kmdescr AS d2 ON d1.CID = d2.CID AND t2.TID = d2.TID '.
				'WHERE t2.tid = t2.tidaccepted '.
				'AND t2.RankId = 220 AND t1.tid = '.$tid.' AND ISNULL(d2.CID) ';
			//echo $sqlAdd2b;
			$this->conn->query($sqlAdd3);
		}
	}

	public function loadNewName($dataArr): int
    {
		$tid = 0;
        $dataArr = $this->validateNewTaxonArr($dataArr);
	    $sqlTaxa = 'INSERT IGNORE INTO taxa(sciname,author,kingdomId,rankid,unitind1,unitname1,unitind2,unitname2,unitind3,unitname3,'.
			'tidaccepted,parenttid,family,`source`,notes,securitystatus,modifiedUid,modifiedTimeStamp) '.
			'VALUES ("'.Sanitizer::cleanInStr($this->conn,$dataArr['sciname']).'",'.
			($dataArr['author']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['author']).'"':'NULL').','.
            ($dataArr['kingdomid']?(int)$dataArr['kingdomid']:'NULL').','.
            ($dataArr['rankid']?(int)$dataArr['rankid']:'NULL').','.
			($dataArr['unitind1']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['unitind1']).'"':'NULL').',"'.
			Sanitizer::cleanInStr($this->conn,$dataArr['unitname1']).'",'.
			($dataArr['unitind2']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['unitind2']).'"':'NULL').','.
			($dataArr['unitname2']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['unitname2']).'"':'NULL').','.
			($dataArr['unitind3']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['unitind3']).'"':'NULL').','.
			($dataArr['unitname3']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['unitname3']).'"':'NULL').','.
			($dataArr['tidaccepted']?(int)$dataArr['tidaccepted']:'NULL').','.
            ($dataArr['parenttid']?(int)$dataArr['parenttid']:'NULL').','.
            ((array_key_exists('family',$dataArr) && $dataArr['family'])?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['family']).'"':'NULL').','.
            ($dataArr['source']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['source']).'"':'NULL').','.
			($dataArr['notes']?'"'.Sanitizer::cleanInStr($this->conn,$dataArr['notes']).'"':'NULL').','.
            (int)$dataArr['securitystatus'].','.
			$GLOBALS['SYMB_UID'].',"'.date('Y-m-d H:i:s').'")';
		//echo "sqlTaxa: ".$sqlTaxa;
		if($this->conn->query($sqlTaxa)){
			$tid = $this->conn->insert_id;
		 	if($tid){
                if((int)$dataArr['acceptstatus'] === 1){
                    $sqlNewTaxUpdate = 'UPDATE taxa SET tidaccepted = '.$tid.' WHERE tid = '.$tid.' ';
                    //echo "sqlNewTaxUpdate: ".$sqlNewTaxUpdate;
                    $this->conn->query($sqlNewTaxUpdate);
                }
                if(array_key_exists('source-name',$dataArr) && array_key_exists('source-id',$dataArr) && $dataArr['source-name'] && $dataArr['source-id']){
                    $sqlId = 'INSERT IGNORE INTO taxaidentifiers(tid,`name`,identifier) VALUES('.
                        $tid.',"'.Sanitizer::cleanInStr($this->conn,$dataArr['source-name']).'","'.Sanitizer::cleanInStr($this->conn,$dataArr['source-id']).'")';
                    //echo $sqlId; exit;
                    $this->conn->query($sqlId);
                }
            }
		}
		return $tid;
	}

    public function validateNewTaxonArr($dataArr): array
    {
        $dataArr['kingdomid'] = 0;
        $dataArr['family'] = '';
        if(array_key_exists('rankid',$dataArr) && (int)$dataArr['rankid'] === 10 && Sanitizer::cleanInStr($this->conn,$dataArr['sciname'])){
            $dataArr['kingdomid'] = $this->addNewTaxonomicKingdom($dataArr['sciname']);
        }
        elseif((array_key_exists('parenttid',$dataArr) && $dataArr['parenttid']) && (!array_key_exists('kingdomid',$dataArr) || !$dataArr['kingdomid'] || !array_key_exists('family',$dataArr) || !$dataArr['family'])){
            $sqlKg = 'SELECT kingdomId, family FROM taxa WHERE tid = '.(int)$dataArr['parenttid'].' ';
            //echo $sqlKg; exit;
            $rsKg = $this->conn->query($sqlKg);
            if($r = $rsKg->fetch_object()){
                $dataArr['kingdomid'] = $r->kingdomId;
                $dataArr['family'] = $r->family;
            }
            $rsKg->free();
            if(!$dataArr['family'] && (int)$dataArr['rankid'] === 140){
                $dataArr['family'] = $dataArr['sciname'];
            }
        }
        if(!array_key_exists('unitname1',$dataArr) || !$dataArr['unitname1']){
            $sciNameArr = (new TaxonomyUtilities)->parseScientificName($dataArr['sciname'],$dataArr['rankid']);
            $dataArr['unitind1'] = array_key_exists('unitind1',$sciNameArr) ? $sciNameArr['unitind1'] : '';
            $dataArr['unitname1'] = array_key_exists('unitname1',$sciNameArr) ? $sciNameArr['unitname1'] : '';
            $dataArr['unitind2'] = array_key_exists('unitind2',$sciNameArr) ? $sciNameArr['unitind2'] : '';
            $dataArr['unitname2'] = array_key_exists('unitname2',$sciNameArr) ? $sciNameArr['unitname2'] : '';
            $dataArr['unitind3'] = array_key_exists('unitind3',$sciNameArr) ? $sciNameArr['unitind3'] : '';
            $dataArr['unitname3'] = array_key_exists('unitname3',$sciNameArr) ? $sciNameArr['unitname3'] : '';
        }
        if(!array_key_exists('source',$dataArr)){
            $dataArr['source'] = '';
        }
        if(!array_key_exists('notes',$dataArr)){
            $dataArr['notes'] = '';
        }
        if(!array_key_exists('securitystatus',$dataArr)){
            $dataArr['securitystatus'] = 0;
        }
        return $dataArr;
    }

    public function addNewTaxonomicKingdom($name): int
    {
        $retVal = 0;
        $sql = 'INSERT INTO taxonkingdoms(`kingdom_name`) VALUES("'.Sanitizer::cleanInStr($this->conn,$name).'")';
        if($this->conn->query($sql)){
            $retVal = $this->conn->insert_id;
            $sql = 'INSERT INTO taxonunits(kingdomid,rankid,rankname,dirparentrankid,reqparentrankid) '.
                'SELECT '.$retVal.',rankid,rankname,dirparentrankid,reqparentrankid '.
                'FROM taxonunits WHERE kingdomid = 100 ';
            $this->conn->query($sql);
        }
        return $retVal;
    }

    public function updateOccurrencesNewTaxon($dataArr): void
    {
        if($dataArr){
            $sql1 = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.sciname = t.sciname SET o.tid = t.tid ';
            if($dataArr['securitystatus'] === 1) {
                $sql1 .= ',o.localitysecurity = 1 ';
            }
            $sql1 .= 'WHERE o.sciname = "'.Sanitizer::cleanInStr($this->conn,$dataArr['sciname']).'" ';
            $this->conn->query($sql1);

            $sql2 = 'UPDATE omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
                'SET i.tid = o.tid '.
                'WHERE ISNULL(i.tid) AND o.tid IS NOT NULL';
            $this->conn->query($sql2);

            $sql3 = 'UPDATE omoccurrences AS o INNER JOIN media AS m ON o.occid = m.occid '.
                'SET m.tid = o.tid '.
                'WHERE ISNULL(m.tid) AND o.tid IS NOT NULL';
            $this->conn->query($sql3);
        }
    }

	public function verifyDeleteTaxon(): array
	{
		$retArr = array();

		$sql ='SELECT tid, sciname FROM taxa '. 
			'WHERE parenttid = '.$this->tid.' ORDER BY sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['child'][$r->tid] = $r->sciname;
		}
		$rs->free();
		
		$sql ='SELECT tid, sciname FROM taxa '. 
			'WHERE tidaccepted = '.$this->tid.' AND tid <> tidaccepted ORDER BY sciname';
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
		
		$sql ='SELECT occid FROM omoccurrences WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['occur'][] = $r->occid;
		}
		$rs->free();
		
		$sql ='SELECT occid FROM omoccurdeterminations WHERE tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['dets'][] = $r->occid;
		}
		$rs->free();
		
		$sql ='SELECT c.clid, c.name '.
			'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid '.
			'WHERE cl.tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['cl'][$r->clid] = $r->name;
		}
		$rs->free();
		
		$sql ='SELECT COUNT(*) AS cnt FROM kmdescr WHERE ISNULL(inherited) AND tid = '.$this->tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['kmdesc'] = $r->cnt;
		}
		$rs->free();
		
		return $retArr;
	}
	
	public function transferResources($targetTid): ?string
	{
		$statusStr = '';
		$delStatusStr = '';
		if(is_numeric($targetTid)){
			$sql ='UPDATE omoccurrences SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
			if(!$this->conn->query($sql)){
				$statusStr .= 'ERROR transferring occurrence records<br/>';
			}
			$sql ='UPDATE omoccurdeterminations SET tid = '.$targetTid.' WHERE tid = '.$this->tid;
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

		$sql = 'UPDATE omoccurrences SET tid = NULL WHERE tid = '.$this->tid;
		if(!$this->conn->query($sql)){
			$statusStr .= 'ERROR setting tid to NULL in deleteTaxon method<br/>';
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

		$sql ='DELETE FROM taxa WHERE (tid = '.$this->tid.')';
        if($this->conn->query($sql)){
            $statusStrFinal = 'SUCCESS: taxon deleted!<br/>';
        }
        else{
            $statusStrFinal = 'ERROR attempting to delete taxon.<br/>';
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
			$sql = 'SELECT tid, sciname, parenttid, rankid FROM taxa '.
				'WHERE tid IN('.implode(',',$this->hierarchyArr).') '.
				'ORDER BY rankid, sciname ';
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

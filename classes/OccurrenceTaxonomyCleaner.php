<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceTaxonomyCleaner extends Manager{

	private $collid = 0;
	private $targetKingdom;
	private $autoClean = 0;

	public function __construct(){
		parent::__construct();
	}

	public function getBadTaxaCount(): int
    {
		$retCnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(DISTINCT sciname) AS taxacnt FROM omoccurrences '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname IS NOT NULL ';
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($row = $rs->fetch_object()){
					$retCnt = $row->taxacnt;
				}
				$rs->free();
			}
		}
		return $retCnt;
	}

	public function getBadSpecimenCount(): int
    {
		$retCnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(occid) AS cnt FROM omoccurrences '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname IS NOT NULL ';
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($row = $rs->fetch_object()){
					$retCnt = $row->cnt;
				}
				$rs->free();
			}
		}
		return $retCnt;
	}

    public function updateOccRecordsWithNewScinameTid($sciname,$tid): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql = 'UPDATE omoccurrences SET tid = '.$tid.' '.
                'WHERE collid = '.$this->collid.' AND sciname = "' . $sciname . '" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
                $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                    'SET d.tid = '.$tid.' '.
                    'WHERE o.collid = '.$this->collid.' AND d.sciname = "' . $sciname . '" ';
                //echo $sql2;
                $this->conn->query($sql2);

                $sql3 = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                    'SET i.tid = o.tid '.
                    'WHERE o.collid = '.$this->collid.' AND o.sciname = "' . $sciname . '" ';
                //echo $sql3;
                $this->conn->query($sql3);

                $sql4 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                    'SET m.tid = o.tid '.
                    'WHERE o.collid = '.$this->collid.' AND o.sciname = "' . $sciname . '" ';
                //echo $sql4;
                $this->conn->query($sql4);
            }
        }
        return $retCnt;
    }

    public function updateOccTaxonomicThesaurusLinkages($kingdomId): int
    {
        $retCnt = 0;
        if($this->collid && $kingdomId){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                'SET o.tid = t.tid '.
                'WHERE o.collid = '.$this->collid.' AND ISNULL(o.tid) AND t.kingdomId = ' . $kingdomId . ' ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function updateDetTaxonomicThesaurusLinkages($kingdomId): int
    {
        $retCnt = 0;
        if($this->collid && $kingdomId){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                'LEFT JOIN taxa AS t ON d.sciname = t.SciName '.
                'SET d.tid = t.tid '.
                'WHERE o.collid = '.$this->collid.' AND ISNULL(d.tid) AND t.kingdomId = ' . $kingdomId . ' ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function updateMediaTaxonomicThesaurusLinkages(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                'SET i.tid = o.tid '.
                'WHERE o.collid = '.$this->collid.' AND i.imgid IS NOT NULL ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                'SET m.tid = o.tid '.
                'WHERE o.collid = '.$this->collid.' AND m.mediaid IS NOT NULL ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

	public function getUnlinkedSciNames(): string
    {
        $retArr = array();
        if($this->collid){
            $sql = 'SELECT DISTINCT sciname '.
                'FROM omoccurrences '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname IS NOT NULL '.
                'ORDER BY sciname ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[] = $r->sciname;
            }
            $rs->free();
        }
        return json_encode($retArr);
    }

    public function cleanTrimNames(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql = 'UPDATE omoccurrences '.
                'SET sciname = TRIM(sciname) '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND (sciname LIKE " %" OR sciname LIKE "% ") ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanSpNames(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND (sciname LIKE "% sp." OR sciname LIKE "% sp" OR '.
                'sciname LIKE "% sp. nov." OR sciname LIKE "% sp. nov" OR sciname LIKE "% sp nov." OR sciname LIKE "% sp nov" OR '.
                'sciname LIKE "% spp." OR sciname LIKE "% spp") ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp.","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp." ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp" ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov.","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp. nov." ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp. nov" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov.","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp nov." ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% sp nov" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp.","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% spp." ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp","") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% spp" ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanInfraAbbrNames(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND (sciname LIKE "% ssp. %" OR sciname LIKE "% ssp %" OR '.
                'sciname LIKE "% subspec. %" OR sciname LIKE "% subspec %" OR sciname LIKE "% subspecies %" OR sciname LIKE "% subsp %" OR '.
                'sciname LIKE "% var %" OR sciname LIKE "% variety %" OR sciname LIKE "% forma %" OR sciname LIKE "% form %" OR '.
                'sciname LIKE "% fo. %" OR sciname LIKE "% fo %") ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp. "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% ssp. %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% ssp %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec. "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% subspec. %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% subspec %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspecies "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% subspecies %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subsp "," subsp. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% subsp %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," var "," var. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% var %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," variety "," var. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% variety %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," forma "," f. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% forma %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," form "," f. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% form %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo. "," f. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% fo. %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo "," f. ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% fo %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanQualifierNames(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf. "," "), identificationQualifier = "cf." '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% cf. %" ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf "," "), identificationQualifier = "cf." '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% cf %" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff. "," "), identificationQualifier = "aff." '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% aff. %" ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff "," "), identificationQualifier = "aff." '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "% aff %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanDoubleSpaceNames(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname, "  ", " ") '.
                'WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname LIKE "%  %" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

	private function indexOccurrenceTaxa(): void
	{
		$this->logOrEcho('Populating null kingdom name tags...');
		$sql = 'UPDATE taxa AS t INNER JOIN taxaenumtree AS e ON t.tid = e.tid '.
			'INNER JOIN taxa AS t2 ON e.parenttid = t2.tid '.
			'SET t.kingdomname = t2.sciname '.
			'WHERE t2.rankid = 10 AND ISNULL(t.kingdomName) ';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' taxon records updated',1);
		}
		else{
			$this->logOrEcho('ERROR updating kingdoms.');
		}
		flush();

		$this->logOrEcho('Populating null family tags...');
		$sql = 'UPDATE taxa AS t INNER JOIN taxaenumtree AS e ON t.tid = e.tid '.
			'INNER JOIN taxa AS t2 ON e.parenttid = t2.tid '.
			'SET t.family = t2.sciname '.
			'WHERE t2.rankid = 140 AND ISNULL(t.family) ';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' taxon records updated',1);
		}
		else{
			$this->logOrEcho('ERROR family tags.');
		}
		flush();

		$this->logOrEcho('Indexing names based on exact matches...');
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON o.sciname = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND ISNULL(o.tid) ';
		if($this->targetKingdom) {
			$sql .= 'AND t.kingdomname = "' . $this->targetKingdom . '" ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		else{
			$this->logOrEcho('ERROR linking new data to occurrences.');
		}
		flush();
	}

	public function remapOccurrenceTaxon($collid, $oldSciname, $tid, $idQualifierIn = null): int
	{
		$affectedRows = 0;
		$idQualifier = '';
		if(is_numeric($collid) && $oldSciname && is_numeric($tid)){
			$hasEditType = false;
			$rsTest = $this->conn->query('SHOW COLUMNS FROM omoccuredits WHERE field = "editType"');
			if($rsTest->num_rows) {
				$hasEditType = true;
			}
			$rsTest->free();

			$newSciname = '';
			$newAuthor= '';
			$sql = 'SELECT sciname, author FROM taxa WHERE (tid = '.$tid.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$newSciname = $r->sciname;
				$newAuthor = $r->author;
			}
			$rs->free();

			$oldSciname = Sanitizer::cleanInStr($this->conn,$oldSciname);
			if($idQualifierIn) {
				$idQualifier = Sanitizer::cleanInStr($this->conn,$idQualifierIn);
			}
			$sqlWhere = 'WHERE collid IN('.$collid.') AND sciname = "'.$oldSciname.'" AND ISNULL(tid) ';
			$sql1 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
				'SELECT occid, "sciname", "'.$newSciname.'", sciname, '.$GLOBALS['SYMB_UID'].', 1, 1'.($hasEditType?',1':'').' FROM omoccurrences '.$sqlWhere;
			if($this->conn->query($sql1)){
				if($newAuthor){
					$sql2 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
						'SELECT occid, "scientificNameAuthorship" AS fieldname, "'.$newAuthor.'", IFNULL(scientificNameAuthorship,""), '.$GLOBALS['SYMB_UID'].', 1, 1 '.($hasEditType?',1 ':'').
						'FROM omoccurrences '.$sqlWhere.'AND (scientificNameAuthorship != "'.$newAuthor.'")';
					if(!$this->conn->query($sql2)){
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (author).',1);
					}
				}
				if($idQualifier){
					$sql3 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
						'SELECT occid, "identificationQualifier" AS fieldname, CONCAT_WS("; ",identificationQualifier,"'.$idQualifier.'") AS idqual, '.
						'IFNULL(identificationQualifier,""), '.$GLOBALS['SYMB_UID'].', 1, 1 '.($hasEditType?',1 ':'').
						'FROM omoccurrences '.$sqlWhere;
					if(!$this->conn->query($sql3)){
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (idQual).',1);
					}
				}
				$sqlFinal = 'UPDATE omoccurrences '.
					'SET tid = '.$tid.', sciname = "'.$newSciname.'" ';
				if($newAuthor){
					$sqlFinal .= ', scientificNameAuthorship = "'.$newAuthor.'" ';
				}
				if($idQualifier){
					$sqlFinal .= ', identificationQualifier = CONCAT_WS("; ",identificationQualifier,"'.$idQualifier.'") ';
				}
				$sqlFinal .= $sqlWhere;
				if($this->conn->query($sqlFinal)){
					$affectedRows = $this->conn->affected_rows;
				}
				else{
					$this->logOrEcho('ERROR thrown remapping occurrence taxon.',1);
				}
			}
			else{
				$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (E1).',1);
			}
		}
		return $affectedRows;
	}

	public function getVerificationCounts(): array
	{
		return [];
	}

	public function getCollMap(): array
	{
		$retArr = array();
		$collArr = $GLOBALS['USER_RIGHTS']['CollAdmin'] ?? array();
		if($GLOBALS['IS_ADMIN']) {
			$collArr = array_merge($collArr, explode(',', $this->collid));
		}
		$sql = 'SELECT collid, CONCAT_WS("-",institutioncode, collectioncode) AS code, collectionname, icon, colltype, managementtype FROM omcollections '.
			'WHERE colltype IN("Preserved Specimens","Observations") AND collid IN('.implode(',', $collArr).') '.
			'ORDER BY collectionname, collectioncode ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['code'] = $r->code;
			$retArr[$r->collid]['collectionname'] = $r->collectionname;
			$retArr[$r->collid]['icon'] = $r->icon;
			$retArr[$r->collid]['colltype'] = $r->colltype;
			$retArr[$r->collid]['managementtype'] = $r->managementtype;
		}
		$rs->free();
		return $retArr;
	}

	public function protectGlobalSpecies($collid = null): int
    {
        $status = 0;
        $sensitiveArr = (new TaxonomyUtilities)->getSensitiveTaxa();

        if($sensitiveArr){
            $sql = 'UPDATE omoccurrences '.
                'SET localitySecurity = 1 '.
                'WHERE (ISNULL(localitySecurity) OR localitySecurity = 0) AND ISNULL(localitySecurityReason) AND tid IN('.implode(',',$sensitiveArr).') ';
            if($collid) {
                $sql .= 'AND collid = ' . $collid . ' ';
            }
            if($this->conn->query($sql)){
                $status += $this->conn->affected_rows;
            }
        }
        $sql2 = 'UPDATE omoccurrences '.
            'SET localitySecurity = 0 '.
            'WHERE localitySecurity = 1 AND ISNULL(localitySecurityReason) AND tid NOT IN('.implode(',',$sensitiveArr).') ';
        if($collid) {
            $sql2 .= 'AND collid = ' . $collid . ' ';
        }
        if($this->conn->query($sql2)){
            $status += $this->conn->affected_rows;
        }
        return $status;
    }

	public function setTargetKingdom($k): void
	{
		$this->targetKingdom = $k;
	}

	public function setAutoClean($v): void
	{
		if(is_numeric($v)) {
			$this->autoClean = $v;
		}
	}

	public function setCollId($collid): void
	{
		if(is_numeric($collid)){
			$this->collid = $collid;
		}
	}
}

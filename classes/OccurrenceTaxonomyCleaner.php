<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/TaxonomyHarvester.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceTaxonomyCleaner extends Manager{

	private $collid = 0;
	private $targetKingdom;
	private $autoClean = 0;
	private $testValidity = 1;
	private $testTaxonomy = 1;
	private $checkAuthor = 1;
	private $verificationMode = 0;

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

    public function updateOccTaxonomicThesaurusLinkages(): int
    {
        $retCnt = 0;
        if($this->collid){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                'SET o.tid = t.tid '.
                'WHERE o.collid = '.$this->collid.' AND o.tid IS NOT NULL AND t.TID IS NOT NULL ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

	public function analyzeTaxa($taxResource, $startIndex, $limit = null){
		set_time_limit(1800);
		$isTaxonomyEditor = false;
		if($GLOBALS['USER_RIGHTS'] && array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])) {
			$isTaxonomyEditor = true;
		}
		$endIndex = 0;
		echo '<li>Starting taxa check</li>';
		$sql = 'SELECT sciname, family, scientificnameauthorship, count(*) AS cnt '.$this->getSqlFragment();
		if($startIndex) {
			$sql .= 'AND (sciname > "' . Sanitizer::cleanInStr($this->conn,$startIndex) . '") ';
		}
		$sql .= 'GROUP BY sciname, family ORDER BY sciname LIMIT '.($limit ?: 50);
		//echo $sql; exit;
		if($rs = $this->conn->query($sql)){
			$taxonHarvester = new  TaxonomyHarvester();
			if($this->targetKingdom){
				$kingArr = explode(':',$this->targetKingdom);
				if($kingArr){
                    $taxonHarvester->setKingdomTid($kingArr[0]);
                    if(isset($kingArr[1])){
                        $taxonHarvester->setKingdomName($kingArr[1]);
                    }
                }
            }
            $taxonHarvester->setTaxonomicResources($taxResource);
            $taxaAdded = false;
            $taxaCnt = 1;
            $itemCnt = 0;
            while($r = $rs->fetch_object()){
                $editLink = '[<a href="#" onclick="openPopup(\''.$GLOBALS['CLIENT_ROOT'].
                    '/collections/editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=sciname&q_customtype1=EQUALS&q_customvalue1='.urlencode($r->sciname).'&collid='.
                    $this->collid.'\'); return false;">'.$r->cnt.' '.((int)$r->cnt === 1?'occurrence':'occurrences').' <i style="height:15px;width:15px;" class="far fa-edit"></i></a>]';
                echo '<li><div style="margin-top:5px">Resolving #'.$taxaCnt.': <b><i>'.$r->sciname.'</i></b>'.($r->family?' ('.$r->family.')':'').'</b> '.$editLink.'</div></li>';
                if($r->family) {
                    $taxonHarvester->setDefaultFamily($r->family);
                }
                if($r->scientificnameauthorship) {
                    $taxonHarvester->setDefaultAuthor($r->scientificnameauthorship);
                }
                $sciname = $r->sciname;
                $tid = 0;
                $manualCheck = true;
                $taxonArr = (new TaxonomyUtilities)->parseScientificName($r->sciname,$this->conn);
                if($taxonArr && $taxonArr['sciname']){
                    $sciname = $taxonArr['sciname'];
                    if($sciname !== $r->sciname){
                        echo '<li style="margin-left:15px;">Interpreted base name: <b>'.$sciname.'</b></li>';
                    }
                    $tid = $taxonHarvester->getTid($taxonArr);
                }
                if(!$tid && $taxonHarvester->processSciname($sciname)) {
                    $taxaAdded= true;
                    if($taxonHarvester->isFullyResolved()){
                        $manualCheck = false;
                    }
                    else{
                        echo '<li style="margin-left:15px;">Taxon not fully resolved...</li>';
                    }
                    $taxonArr = (new TaxonomyUtilities)->parseScientificName($sciname,$this->conn);
                    $tid = $taxonHarvester->getTid($taxonArr);
                }
                if($taxonArr && $taxonArr['sciname'] && $tid && $this->autoClean) {
                    $this->remapOccurrenceTaxon($this->collid, $r->sciname, $tid, ($taxonArr['identificationqualifier'] ?? ''));
                    echo '<li style="margin-left:15px;">Occurrences mapped to '.($taxonHarvester->getTidAccepted()?'taxon #'.$taxonHarvester->getTidAccepted():'<b>'.$sciname.'</b>').'</li>';
                    $manualCheck = false;
                }
                if($manualCheck){
                    $thesLink = '';
                    if($isTaxonomyEditor){
                        $thesLink = ' <a href="#" onclick="openPopup(\'../../taxa/taxonomy/index.php\'); return false;" title="Open New Taxon Form"><i style="height:15px;width:15px;" class="far fa-edit"></i><b style="font-size:70%;">T</b></a>';
                    }
                    echo '<li style="margin-left:15px;">Checking close matches in thesaurus'.$thesLink.'...</li>';
                    if($matchArr = $taxonHarvester->getCloseMatch($sciname)){
                        $strTestArr = array();
                        for($x=1; $x <= 3; $x++){
                            $indexStr = 'unitname'.$x;
                            if(isset($taxonArr[$indexStr]) && $taxonArr['unitname'.$x]) {
                                $strTestArr[] = $taxonArr['unitname' . $x];
                            }
                        }
                        foreach($matchArr as $tid => $scinameMatch){
                            $snTokens = explode(' ',$scinameMatch);
                            if($snTokens){
                                foreach($snTokens as $k => $v){
                                    if(in_array($v, $strTestArr, true)) {
                                        $snTokens[$k] = '<b>' . $v . '</b>';
                                    }
                                }
                                $idQual = (isset($taxonArr['identificationqualifier'])?str_replace("'", '', $taxonArr['identificationqualifier']):'');
                                $echoStr = '<i>'.implode(' ',$snTokens).'</i> =&gt; <span class="hideOnLoad">wait for page to finish loading...</span><span class="displayOnLoad" style="display:none">'.
                                    '<a href="#" onclick="return remappTaxon(\''.urlencode($r->sciname).'\','.$tid.',\''.$idQual.'\','.$itemCnt.')" style="color:blue"> remap to this taxon</a>'.
                                    '<span id="remapSpan-'.$itemCnt.'"></span></span>';
                                echo '<li style="margin-left:30px;">'.$echoStr.'</li>';
                                $itemCnt++;
                            }
						}
					}
					else{
						echo '<li style="margin-left:30px;">No close matches found</li>';
					}
					$manStr = 'Manual search: ';
					$manStr .= '<form onsubmit="return false" style="display:inline;">';
					$manStr .= '<input class="taxon" name="taxon" type="text" value="" />';
					$manStr .= '<input class="tid" name="tid" type="hidden" value="" />';
					$manStr .= '<button onclick="batchUpdate(this.form,\''.$r->sciname.'\','.$taxaCnt.')">Remap</button>';
					$manStr .= '<span id="remapSpan-'.$taxaCnt.'-c"></span>';
					$manStr .= '</form>';
					echo '<li style="margin-left:30px;">'.$manStr.'</li>';
				}
				$taxaCnt++;
				$endIndex = preg_replace("/[^A-Za-z\-. ]/", '', $r->sciname );
				flush();
			}
			$rs->free();
			if($taxaAdded) {
				$this->indexOccurrenceTaxa();
			}
		}

		echo '<li><b>Done with taxa check </b></li>';
		return $endIndex;
	}

	private function getSqlFragment(): string
	{
		return 'FROM omoccurrences WHERE collid = '.$this->collid.' AND ISNULL(tid) AND sciname IS NOT NULL AND sciname NOT LIKE "% x %" AND sciname NOT LIKE "% × %" ';
	}

	public function deepIndexTaxa(): void
	{
		$this->setVerboseMode(2);
		$kingdomName = '';
		if($this->targetKingdom) {
			$targetKingdomStr = explode(':', $this->targetKingdom);
			$kingdomName = array_pop($targetKingdomStr);
		}

		$this->logOrEcho('Cleaning leading and trailing spaces');
		$sql = 'UPDATE omoccurrences '.
			'SET sciname = TRIM(sciname) '.
			'WHERE collid IN('.$this->collid.') AND ISNULL(tid) AND (sciname LIKE " %" OR sciname LIKE "% ")';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records cleaned',1);
		}
		flush();

		$this->logOrEcho('Cleaning double spaces embedded within name');
		$sql = 'UPDATE omoccurrences '.
			'SET sciname = REPLACE(sciname, "  ", " ") '.
			'WHERE collid IN('.$this->collid.') AND ISNULL(tid) AND sciname LIKE "%  %" ';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records cleaned',1);
		}
		flush();

		$this->indexOccurrenceTaxa();

		$this->logOrEcho('Indexing names based on matching trinomials without taxonRank designation');
		$triCnt = 0;
		$sql = 'SELECT DISTINCT o.sciname, t.tid '.
			'FROM omoccurrences AS o INNER JOIN taxa AS t ON o.sciname = CONCAT_WS(" ",t.unitname1,t.unitname2,t.unitname3) '.
			'WHERE o.collid IN('.$this->collid.') AND t.rankid IN(230,240) AND o.sciname LIKE "% % %" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		$sql .= 'ORDER BY t.rankid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$triCnt += $this->remapOccurrenceTaxon($this->collid, $r->sciname, $r->tid);
		}
		$rs->free();
		$this->logOrEcho($triCnt.' occurrence records remapped',1);
		flush();

		$this->logOrEcho('Indexing names ending in sp.');
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON SUBSTRING(o.sciname,1, CHAR_LENGTH(o.sciname) - 4) = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% sp." AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing spp.');
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON REPLACE(o.sciname," spp.","") = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% spp.%" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing cf.');
		$cnt = 0;
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON REPLACE(o.sciname," cf. "," ") = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% cf. %" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$cnt = $this->conn->affected_rows;
		}
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON REPLACE(o.sciname," cf "," ") = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% cf %" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$cnt += $this->conn->affected_rows;
			$this->logOrEcho($cnt.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing aff.');
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON REPLACE(o.sciname," aff. "," ") = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% aff. %" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing group statements');
		$sql = 'UPDATE omoccurrences AS o INNER JOIN taxa AS t ON REPLACE(o.sciname," group"," ") = t.sciname '.
			'SET o.tid = t.tid '.
			'WHERE o.collid IN('.$this->collid.') AND o.sciname LIKE "% group%" AND ISNULL(o.tid) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();
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

	public function getTaxaSuggest($queryString): array
	{
		$retArr = array();
		$sql = 'SELECT tid, sciname FROM taxa ';
		$queryString = preg_replace('/[()\'"+\-=@$%]+/', '', $queryString);
		if($queryString){
			$tokenArr = explode(' ',Sanitizer::cleanInStr($this->conn,$queryString));
			$token = array_shift($tokenArr);
			if($token === 'x') {
				$token = array_shift($tokenArr);
			}
			if($token) {
				$sql .= 'WHERE unitname1 LIKE "' . $token . '%" ';
			}
			if($tokenArr){
				$token = array_shift($tokenArr);
				if($token === 'x') {
					$token = array_shift($tokenArr);
				}
				if($token) {
					$sql .= 'AND unitname2 LIKE "' . $token . '%" ';
				}
				if($tokenArr){
					$token = array_shift($tokenArr);
					if($tokenArr){
						$sql .= 'AND unitind3 LIKE "'.$token.'%" AND unitname3 LIKE "'.array_shift($tokenArr).'%" ';
					}
					else{
						$sql .= 'AND (unitind3 LIKE "'.$token.'%" OR unitname3 LIKE "'.$token.'%") ';
					}
				}
			}
			if($this->targetKingdom){
				$kingdomStr = explode(':',$this->targetKingdom);
				$kingdomName = array_pop($kingdomStr);
				$sql .= 'AND (ISNULL(kingdomname) OR kingdomname = "'.$kingdomName.'") ';
			}
			$sql .= 'LIMIT 30';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = array('id'=>$r->tid,'value'=>$r->sciname);
			}
			$rs->free();
		}
		return $retArr;
	}

    public function protectGlobalSpecies($collid = null): int
    {
        $status = 0;
        if($this->verbose) {
            $this->outputMsg('Protecting globally rare species... ', 1);
        }
        $sensitiveArr = $this->getSensitiveTaxa();

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
            else{
                $errStr = 'WARNING: unable to protect globally rare species; '.$this->conn->error;
                $this->errorArr[] = $errStr;
                if($this->verbose) {
                    $this->outputMsg($errStr, 2);
                }
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
